<?php

namespace App\Services;

use App\Models\Crop;
use App\Models\Farmer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;

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
     * Import crops from CSV file
     */
    public function importFromCsv(UploadedFile $file): array
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

        if (!in_array($file->getClientOriginalExtension(), ['csv', 'txt'])) {
            $results['messages'][] = 'File must be a CSV file.';
            $results['errors']++;
            return $results;
        }

        $path = $file->getRealPath();
        $csvData = $this->csvToArray($path);

        if (empty($csvData)) {
            $results['messages'][] = 'CSV file is empty or could not be read.';
            $results['errors']++;
            return $results;
        }

        // Remove header row
        $headers = array_shift($csvData);
        
        foreach ($csvData as $index => $row) {
            $rowNumber = $index + 2; // +2 because we removed header and arrays are 0-indexed
            
            try {
                $this->processCsvRow($row, $rowNumber);
                $results['success']++;
            } catch (\Exception $e) {
                $results['errors']++;
                $results['messages'][] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Get CSV template for download
     */
    public function getCsvTemplate(): string
    {
        $templateData = [
            [
                'Farmer Name',
                'Crop Name',
                'Variety',
                'Planting Date',
                'Expected Harvest Date',
                'Area (Hectares)',
                'Status',
                'Expected Yield (kg)',
                'Description'
            ],
            [
                'John Doe',
                'Rice',
                'IR64',
                '2025-01-15',
                '2025-05-15',
                '2.5',
                'planted',
                '5000',
                'First season rice crop'
            ],
            [
                'Jane Smith',
                'Corn',
                'Sweet Corn',
                '2025-02-01',
                '2025-06-01',
                '1.0',
                'growing',
                '2000',
                'Organic corn for local market'
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
}