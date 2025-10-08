<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CSV IMPORT REJECTION ANALYZER ===\n\n";

// Look for CSV files
$csvFiles = [];
$searchDirs = ['.', 'storage/app', 'storage/app/public', 'public'];

foreach ($searchDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*.csv');
        $csvFiles = array_merge($csvFiles, $files);
    }
}

if (empty($csvFiles)) {
    echo "❌ No CSV files found in common directories.\n";
    echo "Please place your CSV file in the project root directory.\n";
    exit(1);
}

echo "Found CSV files:\n";
foreach ($csvFiles as $i => $file) {
    echo "  [" . ($i + 1) . "] {$file} (" . round(filesize($file) / 1024, 2) . " KB)\n";
}

// Use the first CSV file or let user choose
$csvFile = $csvFiles[0];
echo "\nAnalyzing: {$csvFile}\n";
echo str_repeat("=", 60) . "\n\n";

$handle = fopen($csvFile, 'r');
if (!$handle) {
    echo "❌ Cannot open CSV file!\n";
    exit(1);
}

// Read headers
$headers = fgetcsv($handle);
echo "CSV HEADERS:\n";
echo "  " . implode(', ', $headers) . "\n\n";

// Normalize headers (same logic as import service)
$normalizedHeaders = array_map(function($header) {
    $normalized = strtolower(str_replace([' ', '-'], '_', trim($header)));
    if ($normalized === 'crop') {
        $normalized = 'crop_name';
    }
    return $normalized;
}, $headers);

echo "NORMALIZED HEADERS:\n";
echo "  " . implode(', ', $normalizedHeaders) . "\n\n";

// Check if required headers exist
$requiredHeaders = ['municipality', 'crop_name', 'farm_type', 'year', 'area_planted', 'area_harvested', 'production'];
$missingHeaders = array_diff($requiredHeaders, $normalizedHeaders);

if (!empty($missingHeaders)) {
    echo "❌ MISSING REQUIRED HEADERS:\n";
    foreach ($missingHeaders as $missing) {
        echo "  - {$missing}\n";
    }
    echo "\nRequired headers: " . implode(', ', $requiredHeaders) . "\n";
    exit(1);
}

echo "✅ All required headers present\n\n";
echo str_repeat("=", 60) . "\n";
echo "ANALYZING ROWS...\n\n";

$rowNumber = 1;
$validRows = 0;
$invalidRows = 0;
$rejectionReasons = [];
$yearDistribution = [];
$sampleRejections = [];

while (($row = fgetcsv($handle)) !== false) {
    $rowNumber++;
    
    if (empty($row) || count($row) !== count($normalizedHeaders)) {
        $invalidRows++;
        $rejectionReasons['Malformed row (column count mismatch)'] = ($rejectionReasons['Malformed row (column count mismatch)'] ?? 0) + 1;
        
        if (count($sampleRejections) < 5) {
            $sampleRejections[] = [
                'row' => $rowNumber,
                'reason' => 'Malformed row - expected ' . count($normalizedHeaders) . ' columns, got ' . count($row),
                'data' => implode('|', array_slice($row, 0, 5)) . '...'
            ];
        }
        continue;
    }
    
    $rowData = array_combine($normalizedHeaders, $row);
    
    // Track year distribution
    $year = trim($rowData['year'] ?? '');
    if (!empty($year) && is_numeric($year)) {
        $yearInt = intval($year);
        $yearDistribution[$yearInt] = ($yearDistribution[$yearInt] ?? 0) + 1;
    }
    
    // Check required fields (ProcessCropImport validation)
    if (empty($rowData['crop_name'] ?? null) || trim($rowData['crop_name']) === '') {
        $invalidRows++;
        $rejectionReasons['Missing crop_name'] = ($rejectionReasons['Missing crop_name'] ?? 0) + 1;
        
        if (count($sampleRejections) < 5) {
            $sampleRejections[] = [
                'row' => $rowNumber,
                'reason' => 'Missing crop_name',
                'data' => "Municipality: " . ($rowData['municipality'] ?? 'N/A') . ", Year: " . ($rowData['year'] ?? 'N/A')
            ];
        }
        continue;
    }
    
    if (empty($rowData['municipality'] ?? null) || trim($rowData['municipality']) === '') {
        $invalidRows++;
        $rejectionReasons['Missing municipality'] = ($rejectionReasons['Missing municipality'] ?? 0) + 1;
        
        if (count($sampleRejections) < 5) {
            $sampleRejections[] = [
                'row' => $rowNumber,
                'reason' => 'Missing municipality',
                'data' => "Crop: " . ($rowData['crop_name'] ?? 'N/A') . ", Year: " . ($rowData['year'] ?? 'N/A')
            ];
        }
        continue;
    }
    
    // Check farm_type validation (CropImportExportService validation)
    $farmType = strtolower(trim($rowData['farm_type'] ?? ''));
    $validFarmTypes = ['irrigated', 'rainfed', 'upland', 'lowland'];
    
    if (empty($farmType)) {
        // Farm type is missing but will default to 'rainfed'
        // This is OK
    } elseif (!in_array($farmType, $validFarmTypes)) {
        $invalidRows++;
        $rejectionReasons["Invalid farm_type: '{$farmType}'"] = ($rejectionReasons["Invalid farm_type: '{$farmType}'"] ?? 0) + 1;
        
        if (count($sampleRejections) < 5) {
            $sampleRejections[] = [
                'row' => $rowNumber,
                'reason' => "Invalid farm_type: '{$farmType}' (must be: irrigated, rainfed, upland, or lowland)",
                'data' => "Crop: {$rowData['crop_name']}, Municipality: {$rowData['municipality']}, Year: {$rowData['year']}"
            ];
        }
        continue;
    }
    
    // Check year validation
    $yearValue = trim($rowData['year'] ?? '');
    if (empty($yearValue) || !is_numeric($yearValue)) {
        $invalidRows++;
        $rejectionReasons['Invalid or missing year'] = ($rejectionReasons['Invalid or missing year'] ?? 0) + 1;
        
        if (count($sampleRejections) < 5) {
            $sampleRejections[] = [
                'row' => $rowNumber,
                'reason' => "Invalid year: '{$yearValue}'",
                'data' => "Crop: {$rowData['crop_name']}, Municipality: {$rowData['municipality']}"
            ];
        }
        continue;
    }
    
    $yearInt = intval($yearValue);
    if ($yearInt < 2000 || $yearInt > 2030) {
        $invalidRows++;
        $rejectionReasons["Year out of range: {$yearInt}"] = ($rejectionReasons["Year out of range: {$yearInt}"] ?? 0) + 1;
        
        if (count($sampleRejections) < 5) {
            $sampleRejections[] = [
                'row' => $rowNumber,
                'reason' => "Year out of range: {$yearInt} (must be 2000-2030)",
                'data' => "Crop: {$rowData['crop_name']}, Municipality: {$rowData['municipality']}"
            ];
        }
        continue;
    }
    
    // If we got here, row is valid
    $validRows++;
}

fclose($handle);

// Display results
echo "\n" . str_repeat("=", 60) . "\n";
echo "ANALYSIS RESULTS:\n";
echo str_repeat("=", 60) . "\n\n";

echo "📊 ROW STATISTICS:\n";
echo "  Total rows analyzed: " . ($rowNumber - 1) . "\n";
echo "  ✅ Valid rows: {$validRows}\n";
echo "  ❌ Invalid rows: {$invalidRows}\n";
echo "  Success rate: " . round(($validRows / ($rowNumber - 1)) * 100, 2) . "%\n\n";

if (!empty($yearDistribution)) {
    ksort($yearDistribution);
    echo "📅 YEAR DISTRIBUTION:\n";
    foreach ($yearDistribution as $year => $count) {
        $bar = str_repeat('█', min(50, round($count / 50)));
        echo sprintf("  %d: %4d rows %s\n", $year, $count, $bar);
    }
    echo "\n";
}

if (!empty($rejectionReasons)) {
    echo "❌ REJECTION REASONS:\n";
    arsort($rejectionReasons);
    foreach ($rejectionReasons as $reason => $count) {
        echo "  - {$reason}: {$count} rows\n";
    }
    echo "\n";
}

if (!empty($sampleRejections)) {
    echo "🔍 SAMPLE REJECTED ROWS:\n";
    foreach ($sampleRejections as $sample) {
        echo "\n  Row {$sample['row']}:\n";
        echo "    Reason: {$sample['reason']}\n";
        echo "    Data: {$sample['data']}\n";
    }
    echo "\n";
}

echo str_repeat("=", 60) . "\n";
echo "RECOMMENDATIONS:\n";
echo str_repeat("=", 60) . "\n\n";

if ($invalidRows > 0) {
    echo "⚠️  {$invalidRows} rows will be REJECTED during import\n\n";
    
    echo "TO FIX:\n";
    if (isset($rejectionReasons['Missing crop_name'])) {
        echo "  1. Fill in missing crop names\n";
    }
    if (isset($rejectionReasons['Missing municipality'])) {
        echo "  2. Fill in missing municipalities\n";
    }
    
    $invalidFarmTypes = array_filter(array_keys($rejectionReasons), function($key) {
        return strpos($key, 'Invalid farm_type') !== false;
    });
    
    if (!empty($invalidFarmTypes)) {
        echo "  3. Fix farm_type values - must be one of:\n";
        echo "     - irrigated\n";
        echo "     - rainfed\n";
        echo "     - upland\n";
        echo "     - lowland\n";
    }
    
    if (isset($rejectionReasons['Invalid or missing year'])) {
        echo "  4. Fill in missing or invalid years\n";
    }
    
    $yearOutOfRange = array_filter(array_keys($rejectionReasons), function($key) {
        return strpos($key, 'Year out of range') !== false;
    });
    
    if (!empty($yearOutOfRange)) {
        echo "  5. Correct years to be between 2000-2030\n";
    }
    
} else {
    echo "✅ All rows are valid and will be imported successfully!\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";