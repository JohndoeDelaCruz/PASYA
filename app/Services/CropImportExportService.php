<?php

namespace App\Services;

use App\Models\Crop;
use App\Models\Farmer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CropImportExportService
{
    /**
     * Export crops to CSV format
     */
    public function exportToCsv(Collection $crops = null): string
    {
        if ($crops === null) {
            $crops = Crop::with('farmer')->orderBy('created_at', 'desc')->get();
        }

        $csvData = [];
        
        // CSV Headers
        $csvData[] = [
            'ID',
            'Farmer Name',
            'Farmer Location',
            'Crop Name',
            'Variety',
            'Planting Date',
            'Expected Harvest Date',
            'Actual Harvest Date',
            'Area (Hectares)',
            'Status',
            'Expected Yield (kg)',
            'Actual Yield (kg)',
            'Description',
            'Created At',
            'Updated At'
        ];

        // Add crop data
        foreach ($crops as $crop) {
            $csvData[] = [
                $crop->id,
                $crop->farmer->farmerName ?? '',
                $crop->farmer->farmerLocation ?? '',
                $crop->name,
                $crop->variety ?? '',
                $crop->planting_date->format('Y-m-d'),
                $crop->expected_harvest_date ? $crop->expected_harvest_date->format('Y-m-d') : '',
                $crop->actual_harvest_date ? $crop->actual_harvest_date->format('Y-m-d') : '',
                $crop->area_hectares,
                $crop->status,
                $crop->expected_yield_kg ?? '',
                $crop->actual_yield_kg ?? '',
                $crop->description ?? '',
                $crop->created_at->format('Y-m-d H:i:s'),
                $crop->updated_at->format('Y-m-d H:i:s')
            ];
        }

        return $this->arrayToCsv($csvData);
    }

    /**
     * Calculate median values for numeric fields from existing crop data
     */
    private function calculateMedianValues(): array
    {
        $medianValues = [];
        
        // Get all existing crop data for median calculation (exclude zero/null values)
        $existingCrops = Crop::whereNotNull('area_planted')
            ->whereNotNull('area_harvested')
            ->whereNotNull('production_mt')
            ->whereNotNull('productivity_mt_ha')
            ->where('area_planted', '>', 0)
            ->where('area_harvested', '>', 0)
            ->where('production_mt', '>', 0)
            ->where('productivity_mt_ha', '>', 0)
            ->get();

        if ($existingCrops->count() > 0) {
            // Calculate median for area_planted
            $areaPlantedValues = $existingCrops->pluck('area_planted')->sort()->values();
            $medianValues['area_planted'] = $this->calculateMedian($areaPlantedValues->toArray());

            // Calculate median for area_harvested
            $areaHarvestedValues = $existingCrops->pluck('area_harvested')->sort()->values();
            $medianValues['area_harvested'] = $this->calculateMedian($areaHarvestedValues->toArray());

            // Calculate median for production
            $productionValues = $existingCrops->pluck('production_mt')->sort()->values();
            $medianValues['production'] = $this->calculateMedian($productionValues->toArray());

            // Calculate median for productivity
            $productivityValues = $existingCrops->pluck('productivity_mt_ha')->sort()->values();
            $medianValues['productivity'] = $this->calculateMedian($productivityValues->toArray());
        } else {
            // Fallback default values if no existing data (based on typical Philippine agriculture)
            $medianValues = [
                'area_planted' => 1.5,   // 1.5 hectares (typical small farm size)
                'area_harvested' => 1.4,  // Slightly less than planted due to losses
                'production' => 12.0,     // 12 metric tons (reasonable vegetable production)
                'productivity' => 8.5     // 8.5 mt/ha (typical vegetable yield)
            ];
        }

        return $medianValues;
    }

    /**
     * Calculate median from an array of values
     */
    private function calculateMedian(array $values): float
    {
        $count = count($values);
        if ($count === 0) return 0.0;
        
        sort($values, SORT_NUMERIC);
        
        if ($count % 2 === 0) {
            // Even number of values - average of two middle values
            return ($values[$count / 2 - 1] + $values[$count / 2]) / 2;
        } else {
            // Odd number of values - middle value
            return $values[floor($count / 2)];
        }
    }

    /**
     * Apply median imputation to row data for missing numeric values
     */
    private function applyMedianImputation(array $rowData, array $medianValues): array
    {
        // Numeric fields that should use median imputation
        $numericFields = [
            'area_planted' => 'area_planted',
            'area_harvested' => 'area_harvested', 
            'production' => 'production',
            'productivity' => 'productivity'
        ];
        
        foreach ($numericFields as $field => $medianKey) {
            $value = trim($rowData[$field] ?? '');
            
            // Check if value is empty, null, or invalid
            if (empty($value) || 
                $value === 'null' || 
                $value === 'n/a' || 
                $value === 'N/A' ||
                !is_numeric($value) || 
                floatval($value) <= 0) {
                
                $rowData[$field] = $medianValues[$medianKey];
            } else {
                $rowData[$field] = floatval($value);
            }
        }
        
        return $rowData;
    }

    /**
     * Handle year value with appropriate default
     */
    private function handleYearValue($year): int
    {
        $year = trim($year ?? '');
        
        if (empty($year) || $year === 'null' || !is_numeric($year)) {
            return (int) date('Y'); // Current year
        }
        
        $yearInt = (int) $year;
        
        // Validate year range (reasonable agricultural data range)
        if ($yearInt < 2000 || $yearInt > 2030) {
            return (int) date('Y');
        }
        
        return $yearInt;
    }

    /**
     * Import crops from CSV file
     */
    public function importFromCsv(UploadedFile $file): array
    {
        // Calculate median values at the start of import
        $medianValues = $this->calculateMedianValues();
        
        $results = [
            'success' => 0,
            'errors' => 0,
            'messages' => [],
            'median_values_used' => $medianValues
        ];

        if (!$file->isValid()) {
            $results['messages'][] = 'Invalid file uploaded.';
            $results['errors']++;
            return $results;
        }

        if (!in_array($file->getClientOriginalExtension(), ['csv', 'txt'])) {
            $results['messages'][] = 'File must be a CSV file.';
            $results['errors']++;
            return $results;
        }

        // Open file handle for streaming (memory efficient for large files)
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            $results['messages'][] = 'Could not open CSV file for reading.';
            $results['errors']++;
            return $results;
        }
        
        // Read header row
        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            $results['messages'][] = 'CSV file appears to be empty or invalid.';
            $results['errors']++;
            return $results;
        }
        
        // Normalize headers for comparison (lowercase and trim)
        $normalizedHeaders = array_map(function($header) {
            $normalized = strtolower(trim($header));
            // Remove common suffixes and normalize common patterns
            $normalized = preg_replace('/\s*\([^)]*\)/', '', $normalized); // Remove (ha), (mt), etc.
            $normalized = str_replace([' ', '-'], '_', $normalized); // Replace spaces and hyphens with underscores
            // Map specific header variations
            if ($normalized === 'crop') {
                $normalized = 'crop_name';
            }
            return $normalized;
        }, $headers);
        
        // Check if we have required headers for agricultural statistics format
        $requiredHeaders = ['municipality', 'crop_name', 'farm_type', 'year', 'area_planted', 'area_harvested', 'production'];
        $intersectingHeaders = array_intersect($requiredHeaders, $normalizedHeaders);
        $hasNewFormat = count($intersectingHeaders) >= 5;
        
        \Log::info('CSV import format detection', [
            'original_headers' => $headers,
            'normalized_headers' => $normalizedHeaders,
            'required_headers' => $requiredHeaders,
            'intersecting_headers' => $intersectingHeaders,
            'intersection_count' => count($intersectingHeaders),
            'has_new_format' => $hasNewFormat
        ]);
        
        // Process in chunks for better performance and memory usage
        $chunkSize = 5000; // Process 5000 rows at a time
        $chunk = [];
        $rowNumber = 2; // Start from row 2 (after headers)
        
        while (($row = fgetcsv($handle)) !== false) {
            try {
                if ($hasNewFormat) {
                    // Convert row to associative array using headers
                    $rowData = [];
                    foreach ($normalizedHeaders as $colIndex => $header) {
                        $rowData[$header] = trim($row[$colIndex] ?? '');
                    }
                    
                    // Apply median imputation for missing numeric values
                    $rowData = $this->applyMedianImputation($rowData, $medianValues);
                    
                    $processedData = $this->prepareCsvRowNewFormat($rowData, $rowNumber);
                } else {
                    $processedData = $this->prepareCsvRowOldFormat($row, $rowNumber);
                }
                
                if ($processedData) {
                    $chunk[] = $processedData;
                }
                
                // Process chunk when it reaches the desired size
                if (count($chunk) >= $chunkSize) {
                    $chunkResults = $this->processBulkInsert($chunk, $hasNewFormat);
                    $results['success'] += $chunkResults['success'];
                    $results['errors'] += $chunkResults['errors'];
                    $results['messages'] = array_merge($results['messages'], $chunkResults['messages']);
                    $chunk = []; // Reset chunk
                }
                
            } catch (\Exception $e) {
                $results['errors']++;
                $results['messages'][] = "Row {$rowNumber}: " . $e->getMessage();
            }
            
            $rowNumber++;
        }
        
        // Process any remaining data in the last chunk
        if (!empty($chunk)) {
            $chunkResults = $this->processBulkInsert($chunk, $hasNewFormat);
            $results['success'] += $chunkResults['success'];
            $results['errors'] += $chunkResults['errors'];
            $results['messages'] = array_merge($results['messages'], $chunkResults['messages']);
        }
        
        fclose($handle);
        return $results;
    }

    /**
     * Import crops from Excel file
     */
    public function importFromExcel(UploadedFile $file): array
    {
        $results = [
            'success' => 0,
            'errors' => 0,
            'messages' => []
        ];

        if (!$file->isValid()) {
            $results['messages'][] = 'Invalid file uploaded.';
            $results['errors']++;
            return $results;
        }

        if (!in_array($file->getClientOriginalExtension(), ['xlsx', 'xls'])) {
            $results['messages'][] = 'File must be an Excel file (.xlsx or .xls).';
            $results['errors']++;
            return $results;
        }

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            // Get headers from first row
            $headers = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $headers[] = strtolower(trim($worksheet->getCell($col . '1')->getValue()));
            }

            // Check if we have required headers for agricultural statistics format
            $requiredHeaders = ['municipality', 'crop_name', 'farm_type', 'year', 'area_planted', 'area_harvested', 'production'];
            $hasNewFormat = count(array_intersect($requiredHeaders, $headers)) >= 5;

            \Log::info('Excel import format detection', [
                'headers' => $headers,
                'has_new_format' => $hasNewFormat,
                'required_headers' => $requiredHeaders
            ]);

            // Process data rows
            for ($row = 2; $row <= $highestRow; $row++) {
                try {
                    $rowData = [];
                    for ($col = 'A'; $col <= $highestColumn; $col++) {
                        $colIndex = ord($col) - ord('A');
                        $rowData[$headers[$colIndex]] = trim($worksheet->getCell($col . $row)->getValue());
                    }

                    if ($hasNewFormat) {
                        $this->processExcelRowNewFormat($rowData, $row);
                    } else {
                        $this->processExcelRowOldFormat($rowData, $row);
                    }
                    $results['success']++;
                } catch (\Exception $e) {
                    $results['errors']++;
                    $results['messages'][] = "Row {$row}: " . $e->getMessage();
                }
            }

        } catch (\Exception $e) {
            $results['messages'][] = 'Error reading Excel file: ' . $e->getMessage();
            $results['errors']++;
        }

        return $results;
    }

    /**
     * Get CSV template for download (Agricultural Statistics Format)
     */
    public function getCsvTemplate(): string
    {
        $templateData = [
            [
                'Municipality',
                'Crop_Name',
                'Farm_Type',
                'Year',
                'Area_Planted',
                'Area_Harvested',
                'Production',
                'Productivity'
            ],
            [
                'Antipolo City',
                'Rice',
                'irrigated',
                '2025',
                '150.5',
                '148.2',
                '1250.75',
                '8.44'
            ],
            [
                'Rodriguez',
                'Corn',
                'rainfed',
                '2025',
                '75.3',
                '73.1',
                '421.8',
                '5.77'
            ],
            [
                'San Mateo',
                'Sweet Potato',
                'upland',
                '2025',
                '25.0',
                '24.5',
                '245.0',
                '10.0'
            ]
        ];

        return $this->arrayToCsv($templateData);
    }

    /**
     * Process a single CSV row
     */
    protected function processCsvRow(array $row, int $rowNumber): void
    {
        // Map CSV columns to expected format
        $data = [
            'farmer_name' => trim($row[0] ?? ''),
            'name' => trim($row[1] ?? ''),
            'variety' => trim($row[2] ?? '') ?: null,
            'planting_date' => trim($row[3] ?? ''),
            'expected_harvest_date' => trim($row[4] ?? '') ?: null,
            'area_hectares' => trim($row[5] ?? ''),
            'status' => trim($row[6] ?? '') ?: 'planted',
            'expected_yield_kg' => trim($row[7] ?? '') ?: null,
            'description' => trim($row[8] ?? '') ?: null,
        ];

        // Validate data
        $validator = Validator::make($data, [
            'farmer_name' => 'required|string',
            'name' => 'required|string|max:255',
            'variety' => 'nullable|string|max:255',
            'planting_date' => 'required|date',
            'expected_harvest_date' => 'nullable|date|after:planting_date',
            'area_hectares' => 'required|numeric|min:0.01',
            'status' => 'required|in:planted,growing,harvested,failed',
            'expected_yield_kg' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Validation failed: ' . implode(', ', $validator->errors()->all()));
        }

        // Find farmer by name
        $farmer = Farmer::where('farmerName', 'LIKE', '%' . $data['farmer_name'] . '%')->first();
        if (!$farmer) {
            throw new \Exception("Farmer '{$data['farmer_name']}' not found");
        }

        // Create crop
        Crop::create([
            'farmer_id' => $farmer->farmerID,
            'name' => $data['name'],
            'variety' => $data['variety'],
            'planting_date' => $data['planting_date'],
            'expected_harvest_date' => $data['expected_harvest_date'],
            'area_hectares' => $data['area_hectares'],
            'status' => $data['status'],
            'expected_yield_kg' => $data['expected_yield_kg'],
            'description' => $data['description'],
        ]);
    }

    /**
     * Process Excel row in new agricultural statistics format
     */
    protected function processExcelRowNewFormat(array $rowData, int $rowNumber): void
    {
        // Calculate median values for this processing
        $medianValues = $this->calculateMedianValues();
        
        // Apply median imputation for missing numeric values
        $rowData = $this->applyMedianImputation($rowData, $medianValues);
        
        // Handle farm_type with default
        $farmType = strtolower(trim($rowData['farm_type'] ?? $rowData['farmtype'] ?? ''));
        if (empty($farmType) || $farmType === 'null' || $farmType === 'n/a') {
            $farmType = 'rainfed'; // Default to rainfed as it's most common
        }
        
        // Helper function to handle empty text values
        $handleEmptyTextValue = function($value, $default = 'N/A') {
            $trimmed = trim($value ?? '');
            return (empty($trimmed) || $trimmed === 'null') ? $default : $trimmed;
        };
        
        $data = [
            'municipality' => $handleEmptyTextValue($rowData['municipality'] ?? ''),
            'crop_name' => $handleEmptyTextValue($rowData['crop_name'] ?? $rowData['crop'] ?? ''),
            'farm_type' => $farmType,
            'year' => $this->handleYearValue($rowData['year'] ?? ''),
            // Numeric values are already processed by median imputation
            'area_planted' => $rowData['area_planted'],
            'area_harvested' => $rowData['area_harvested'],
            'production' => $rowData['production'],
            'productivity' => $rowData['productivity'],
        ];

        // Validate data with updated rules to allow N/A and default values
        $validator = Validator::make($data, [
            'municipality' => 'required|string|max:255',
            'crop_name' => 'required|string|max:255',
            'farm_type' => 'required|string|in:irrigated,rainfed,upland,lowland',
            'year' => 'required|integer|min:2000|max:2030',
            'area_planted' => 'required|numeric|min:0|max:99999.99',
            'area_harvested' => 'required|numeric|min:0|max:99999.99',
            'production' => 'required|numeric|min:0|max:99999999.99',
            'productivity' => 'required|numeric|min:0|max:99999.99',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Validation failed: ' . implode(', ', $validator->errors()->all()));
        }

        // Create crop record
        Crop::create([
            'municipality' => $data['municipality'],
            'crop_name' => $data['crop_name'],
            'farm_type' => $data['farm_type'],
            'year' => $data['year'],
            'area_planted' => $data['area_planted'],
            'area_harvested' => $data['area_harvested'],
            'production_mt' => $data['production'],
            'productivity_mt_ha' => $data['productivity'],
            'status' => 'planted', // Default status
        ]);
    }

    /**
     * Process CSV row in new agricultural statistics format
     */
    protected function processCsvRowNewFormat(array $rowData, int $rowNumber): void
    {
        // Calculate median values for this processing
        $medianValues = $this->calculateMedianValues();
        
        // Apply median imputation for missing numeric values
        $rowData = $this->applyMedianImputation($rowData, $medianValues);
        
        // Handle farm_type with default
        $farmType = strtolower(trim($rowData['farm_type'] ?? $rowData['farmtype'] ?? ''));
        if (empty($farmType) || $farmType === 'null' || $farmType === 'n/a') {
            $farmType = 'rainfed'; // Default to rainfed as it's most common
        }
        
        // Helper function to handle empty text values
        $handleEmptyTextValue = function($value, $default = 'N/A') {
            $trimmed = trim($value ?? '');
            return (empty($trimmed) || $trimmed === 'null') ? $default : $trimmed;
        };
        
        $data = [
            'municipality' => $handleEmptyTextValue($rowData['municipality'] ?? ''),
            'crop_name' => $handleEmptyTextValue($rowData['crop_name'] ?? $rowData['crop'] ?? ''),
            'farm_type' => $farmType,
            'year' => $this->handleYearValue($rowData['year'] ?? ''),
            // Numeric values are already processed by median imputation
            'area_planted' => $rowData['area_planted'],
            'area_harvested' => $rowData['area_harvested'],
            'production' => $rowData['production'],
            'productivity' => $rowData['productivity'],
        ];

        // Validate data with updated rules to allow N/A and default values
        $validator = Validator::make($data, [
            'municipality' => 'required|string|max:255',
            'crop_name' => 'required|string|max:255',
            'farm_type' => 'required|string|in:irrigated,rainfed,upland,lowland',
            'year' => 'required|integer|min:2000|max:2030',
            'area_planted' => 'required|numeric|min:0|max:99999.99',
            'area_harvested' => 'required|numeric|min:0|max:99999.99',
            'production' => 'required|numeric|min:0|max:99999999.99',
            'productivity' => 'required|numeric|min:0|max:99999.99',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Validation failed: ' . implode(', ', $validator->errors()->all()));
        }

        // Create crop record
        Crop::create([
            'municipality' => $data['municipality'],
            'crop_name' => $data['crop_name'],
            'farm_type' => $data['farm_type'],
            'year' => $data['year'],
            'area_planted' => $data['area_planted'],
            'area_harvested' => $data['area_harvested'],
            'production_mt' => $data['production'],
            'productivity_mt_ha' => $data['productivity'],
            'status' => 'planted', // Default status
        ]);
    }

    /**
     * Process Excel row in old farmer-based format
     */
    protected function processExcelRowOldFormat(array $rowData, int $rowNumber): void
    {
        // Map Excel columns to expected format
        $data = [
            'farmer_name' => $rowData['farmer_name'] ?? $rowData['farmer'] ?? '',
            'name' => $rowData['crop_name'] ?? $rowData['name'] ?? '',
            'variety' => $rowData['variety'] ?? null,
            'planting_date' => $rowData['planting_date'] ?? '',
            'expected_harvest_date' => $rowData['expected_harvest_date'] ?? null,
            'area_hectares' => $rowData['area_hectares'] ?? $rowData['area'] ?? '',
            'status' => $rowData['status'] ?? 'planted',
            'expected_yield_kg' => $rowData['expected_yield_kg'] ?? $rowData['expected_yield'] ?? null,
            'description' => $rowData['description'] ?? null,
        ];

        // Validate data
        $validator = Validator::make($data, [
            'farmer_name' => 'required|string',
            'name' => 'required|string|max:255',
            'variety' => 'nullable|string|max:255',
            'planting_date' => 'required|date',
            'expected_harvest_date' => 'nullable|date|after:planting_date',
            'area_hectares' => 'required|numeric|min:0.01',
            'status' => 'required|in:planted,growing,harvested,failed',
            'expected_yield_kg' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Validation failed: ' . implode(', ', $validator->errors()->all()));
        }

        // Find farmer by name
        $farmer = Farmer::where('farmerName', 'LIKE', '%' . $data['farmer_name'] . '%')->first();
        if (!$farmer) {
            throw new \Exception("Farmer '{$data['farmer_name']}' not found");
        }

        // Create crop record
        Crop::create([
            'farmer_id' => $farmer->farmerID,
            'name' => $data['name'],
            'variety' => $data['variety'],
            'planting_date' => $data['planting_date'],
            'expected_harvest_date' => $data['expected_harvest_date'],
            'area_hectares' => $data['area_hectares'],
            'status' => $data['status'],
            'expected_yield_kg' => $data['expected_yield_kg'],
            'description' => $data['description'],
        ]);
    }

    /**
     * Convert array to CSV string
     */
    protected function arrayToCsv(array $data): string
    {
        $output = fopen('php://temp', 'r+');
        
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }

    /**
     * Convert CSV file to array
     */
    protected function csvToArray(string $filePath): array
    {
        $data = [];
        
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }
        
        return $data;
    }

    /**
     * Prepare CSV row data for new format (agricultural statistics) without inserting
     * Note: Numeric values are already handled by median imputation in applyMedianImputation()
     */
    protected function prepareCsvRowNewFormat(array $rowData, int $rowNumber): ?array
    {
        // Handle farm_type with default - normalize to lowercase
        $farmType = strtolower(trim($rowData['farm_type'] ?? $rowData['farmtype'] ?? ''));
        
        // Normalize common variations
        $farmTypeMap = [
            'irrigated' => 'irrigated',
            'rainfed' => 'rainfed',
            'upland' => 'upland',
            'lowland' => 'lowland',
            'rain fed' => 'rainfed',
            'rain-fed' => 'rainfed',
        ];
        
        if (isset($farmTypeMap[$farmType])) {
            $farmType = $farmTypeMap[$farmType];
        } elseif (empty($farmType) || $farmType === 'null' || $farmType === 'n/a') {
            $farmType = 'rainfed'; // Default to rainfed as it's most common
        }
        
        // Helper function to handle empty text values
        $handleEmptyTextValue = function($value, $default = 'N/A') {
            $trimmed = trim($value ?? '');
            return (empty($trimmed) || $trimmed === 'null') ? $default : $trimmed;
        };
        
        $data = [
            'municipality' => $handleEmptyTextValue($rowData['municipality'] ?? ''),
            'crop_name' => $handleEmptyTextValue($rowData['crop_name'] ?? $rowData['crop'] ?? ''),
            'farm_type' => $farmType,
            'year' => $this->handleYearValue($rowData['year'] ?? ''),
            // Numeric values are already processed by median imputation
            'area_planted' => $rowData['area_planted'],
            'area_harvested' => $rowData['area_harvested'],
            'production' => $rowData['production'],
            'productivity' => $rowData['productivity'],
        ];

        // Validate data with updated rules to allow N/A and default values
        $validator = Validator::make($data, [
            'municipality' => 'required|string|max:255',
            'crop_name' => 'required|string|max:255',
            'farm_type' => 'required|string|in:irrigated,rainfed,upland,lowland',
            'year' => 'required|integer|min:2000|max:2030',
            'area_planted' => 'required|numeric|min:0|max:99999.99',
            'area_harvested' => 'required|numeric|min:0|max:99999.99',
            'production' => 'required|numeric|min:0|max:99999999.99',
            'productivity' => 'required|numeric|min:0|max:99999.99',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Validation failed: ' . implode(', ', $validator->errors()->all()));
        }

        // Return prepared data for bulk insert
        return [
            'municipality' => $data['municipality'],
            'crop_name' => $data['crop_name'],
            'farm_type' => $data['farm_type'],
            'year' => $data['year'],
            'area_planted' => $data['area_planted'],
            'area_harvested' => $data['area_harvested'],
            'production_mt' => $data['production'],
            'productivity_mt_ha' => $data['productivity'],
            'status' => 'planted', // Default status
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Prepare CSV row data for old format (farmer-based) without inserting
     */
    protected function prepareCsvRowOldFormat(array $row, int $rowNumber): ?array
    {
        // Map CSV columns to expected format
        $data = [
            'farmer_name' => trim($row[0] ?? ''),
            'name' => trim($row[1] ?? ''),
            'variety' => trim($row[2] ?? '') ?: null,
            'planting_date' => trim($row[3] ?? ''),
            'expected_harvest_date' => trim($row[4] ?? '') ?: null,
            'area_hectares' => trim($row[5] ?? ''),
            'status' => trim($row[6] ?? '') ?: 'planted',
            'expected_yield_kg' => trim($row[7] ?? '') ?: null,
            'description' => trim($row[8] ?? '') ?: null,
        ];

        // Validate data
        $validator = Validator::make($data, [
            'farmer_name' => 'required|string',
            'name' => 'required|string|max:255',
            'variety' => 'nullable|string|max:255',
            'planting_date' => 'required|date',
            'expected_harvest_date' => 'nullable|date|after:planting_date',
            'area_hectares' => 'required|numeric|min:0.01',
            'status' => 'required|in:planted,growing,harvested,failed',
            'expected_yield_kg' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Validation failed: ' . implode(', ', $validator->errors()->all()));
        }

        // Find farmer by name
        $farmer = Farmer::where('farmerName', 'LIKE', '%' . $data['farmer_name'] . '%')->first();
        if (!$farmer) {
            throw new \Exception("Farmer '{$data['farmer_name']}' not found");
        }

        // Return prepared data for bulk insert
        return [
            'farmer_id' => $farmer->farmerID,
            'name' => $data['name'],
            'variety' => $data['variety'],
            'planting_date' => $data['planting_date'],
            'expected_harvest_date' => $data['expected_harvest_date'],
            'area_hectares' => $data['area_hectares'],
            'status' => $data['status'],
            'expected_yield_kg' => $data['expected_yield_kg'],
            'description' => $data['description'],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Process bulk insert for improved performance
     */
    protected function processBulkInsert(array $chunk, bool $isNewFormat): array
    {
        $results = ['success' => 0, 'errors' => 0, 'messages' => []];
        
        try {
            // Use bulk insert for better performance
            Crop::insert($chunk);
            $results['success'] = count($chunk);
        } catch (\Exception $e) {
            // If bulk insert fails, try individual inserts to identify problematic rows
            foreach ($chunk as $index => $rowData) {
                try {
                    Crop::create($rowData);
                    $results['success']++;
                } catch (\Exception $rowException) {
                    $results['errors']++;
                    $results['messages'][] = "Chunk row " . ($index + 1) . ": " . $rowException->getMessage();
                }
            }
        }
        
        return $results;
    }
}