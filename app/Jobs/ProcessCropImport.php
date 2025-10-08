<?php

namespace App\Jobs;

use App\Models\Crop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProcessCropImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour timeout for large files
    public $tries = 3; // Retry 3 times if failed

    protected $filePath;
    protected $userId;
    protected $jobId;
    protected $medianValues;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, int $userId, string $jobId, array $medianValues)
    {
        $this->filePath = $filePath;
        $this->userId = $userId;
        $this->jobId = $jobId;
        $this->medianValues = $medianValues;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting crop import job: {$this->jobId}");

        try {
            // Initialize progress tracking
            $this->updateProgress(0, 'Starting import...');

            $extension = pathinfo($this->filePath, PATHINFO_EXTENSION);
            
            if (in_array($extension, ['xlsx', 'xls'])) {
                $this->processExcelFile();
            } else {
                $this->processCsvFile();
            }

            // Mark as completed
            $this->updateProgress(100, 'Import completed successfully!');
            
            Log::info("Completed crop import job: {$this->jobId}");

        } catch (\Exception $e) {
            Log::error("Error in crop import job: {$this->jobId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->updateProgress(-1, 'Error: ' . $e->getMessage());
            throw $e; // Re-throw to trigger retry
        } finally {
            // Clean up temporary file
            if (file_exists($this->filePath)) {
                @unlink($this->filePath);
            }
        }
    }

    /**
     * Process CSV file in chunks
     */
    protected function processCsvFile(): void
    {
        $handle = fopen($this->filePath, 'r');
        
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        // Read header row
        $headers = fgetcsv($handle);
        if (!$headers) {
            throw new \Exception('Invalid CSV file - no headers found');
        }

        $headers = array_map('trim', $headers);
        $totalRows = $this->countCsvRows($this->filePath) - 1; // Exclude header
        $processedRows = 0;
        $successCount = 0;
        $errorCount = 0;
        $batch = [];
        $batchSize = 5000; // Process 5000 rows at a time

        $this->updateProgress(1, "Processing {$totalRows} rows...");

        while (($row = fgetcsv($handle)) !== false) {
            $rowData = array_combine($headers, $row);
            
            if ($this->validateRowData($rowData)) {
                $batch[] = $this->prepareRowForInsert($rowData);
                
                // Insert batch when it reaches the batch size
                if (count($batch) >= $batchSize) {
                    $inserted = $this->insertBatch($batch);
                    $successCount += $inserted;
                    $errorCount += (count($batch) - $inserted);
                    $batch = [];
                }
            } else {
                $errorCount++;
            }

            $processedRows++;
            
            // Update progress every 2500 rows
            if ($processedRows % 2500 === 0) {
                $progress = min(95, ($processedRows / $totalRows) * 100);
                $this->updateProgress(
                    $progress, 
                    "Processed {$processedRows}/{$totalRows} rows. Success: {$successCount}, Errors: {$errorCount}"
                );
            }
        }

        // Insert remaining rows in batch
        if (!empty($batch)) {
            $inserted = $this->insertBatch($batch);
            $successCount += $inserted;
            $errorCount += (count($batch) - $inserted);
        }

        fclose($handle);

        $this->updateProgress(
            100, 
            "Import complete! Success: {$successCount}, Errors: {$errorCount}"
        );
    }

    /**
     * Process Excel file in chunks
     */
    protected function processExcelFile(): void
    {
        $spreadsheet = IOFactory::load($this->filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        if (empty($rows)) {
            throw new \Exception('Excel file is empty');
        }

        $headers = array_shift($rows); // Remove header row
        $headers = array_map('trim', $headers);
        
        $totalRows = count($rows);
        $processedRows = 0;
        $successCount = 0;
        $errorCount = 0;
        $batch = [];
        $batchSize = 5000;

        $this->updateProgress(1, "Processing {$totalRows} rows...");

        foreach ($rows as $row) {
            $rowData = array_combine($headers, $row);
            
            if ($this->validateRowData($rowData)) {
                $batch[] = $this->prepareRowForInsert($rowData);
                
                if (count($batch) >= $batchSize) {
                    $inserted = $this->insertBatch($batch);
                    $successCount += $inserted;
                    $errorCount += (count($batch) - $inserted);
                    $batch = [];
                }
            } else {
                $errorCount++;
            }

            $processedRows++;
            
            if ($processedRows % 2500 === 0) {
                $progress = min(95, ($processedRows / $totalRows) * 100);
                $this->updateProgress(
                    $progress, 
                    "Processed {$processedRows}/{$totalRows} rows. Success: {$successCount}, Errors: {$errorCount}"
                );
            }
        }

        // Insert remaining batch
        if (!empty($batch)) {
            $inserted = $this->insertBatch($batch);
            $successCount += $inserted;
            $errorCount += (count($batch) - $inserted);
        }

        $this->updateProgress(
            100, 
            "Import complete! Success: {$successCount}, Errors: {$errorCount}"
        );
    }

    /**
     * Validate row data
     */
    protected function validateRowData(array $rowData): bool
    {
        // Required fields
        $requiredFields = ['crop', 'municipality'];
        
        foreach ($requiredFields as $field) {
            if (empty($rowData[$field] ?? null)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Prepare row data for database insert
     */
    protected function prepareRowForInsert(array $rowData): array
    {
        // Apply median imputation for missing numeric values
        $areaPlanted = $this->getNumericValue($rowData['area_planted'] ?? null, $this->medianValues['area_planted']);
        $areaHarvested = $this->getNumericValue($rowData['area_harvested'] ?? null, $this->medianValues['area_harvested']);
        $production = $this->getNumericValue($rowData['production'] ?? null, $this->medianValues['production']);
        $productivity = $this->getNumericValue($rowData['productivity'] ?? null, $this->medianValues['productivity']);

        $year = $this->getYearValue($rowData['year'] ?? null);
        $month = trim($rowData['month'] ?? date('F'));

        return [
            'crop' => trim($rowData['crop']),
            'variety' => trim($rowData['variety'] ?? ''),
            'municipality' => trim($rowData['municipality']),
            'barangay' => trim($rowData['barangay'] ?? ''),
            'farm_type' => trim($rowData['farm_type'] ?? 'rainfed'),
            'month' => $month,
            'year' => $year,
            'area_planted' => $areaPlanted,
            'area_harvested' => $areaHarvested,
            'production_mt' => $production,
            'productivity_mt_ha' => $productivity,
            'status' => trim($rowData['status'] ?? 'Active'),
            'cooperative' => trim($rowData['cooperative'] ?? ''),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Get numeric value with fallback to median
     */
    protected function getNumericValue($value, float $median): float
    {
        $value = trim($value ?? '');
        
        if (empty($value) || !is_numeric($value) || floatval($value) <= 0) {
            return $median;
        }
        
        return floatval($value);
    }

    /**
     * Get year value with validation
     */
    protected function getYearValue($year): int
    {
        $year = trim($year ?? '');
        
        if (empty($year) || !is_numeric($year)) {
            return (int) date('Y');
        }
        
        $yearInt = (int) $year;
        
        if ($yearInt < 2000 || $yearInt > 2030) {
            return (int) date('Y');
        }
        
        return $yearInt;
    }

    /**
     * Insert batch of records using transaction
     */
    protected function insertBatch(array $batch): int
    {
        try {
            DB::beginTransaction();
            
            // Use chunk insert for better performance
            Crop::insert($batch);
            
            DB::commit();
            
            return count($batch);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Batch insert failed', [
                'error' => $e->getMessage(),
                'batch_size' => count($batch)
            ]);
            return 0;
        }
    }

    /**
     * Count CSV rows
     */
    protected function countCsvRows(string $filePath): int
    {
        $lineCount = 0;
        $handle = fopen($filePath, 'r');
        
        while (fgets($handle) !== false) {
            $lineCount++;
        }
        
        fclose($handle);
        
        return $lineCount;
    }

    /**
     * Update progress in cache
     */
    protected function updateProgress(float $percentage, string $message): void
    {
        Cache::put("import_progress_{$this->jobId}", [
            'percentage' => round($percentage, 2),
            'message' => $message,
            'updated_at' => now()->toIso8601String()
        ], 3600); // Cache for 1 hour
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Crop import job failed: {$this->jobId}", [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        $this->updateProgress(-1, 'Import failed: ' . $exception->getMessage());
    }
}
