<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\Validator;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ANALYZING YOUR CSV FILE ===\n\n";

$csvFile = 'c:\Users\Admin\Downloads\crop_data_cleaned.csv';

if (!file_exists($csvFile)) {
    echo "❌ File not found: $csvFile\n";
    exit(1);
}

$fileSize = filesize($csvFile);
echo "📁 File: " . basename($csvFile) . "\n";
echo "📊 Size: " . number_format($fileSize / 1024, 2) . " KB\n\n";

// Open and read the CSV
$handle = fopen($csvFile, 'r');
if (!$handle) {
    echo "❌ Cannot open file\n";
    exit(1);
}

// Read header
$headers = fgetcsv($handle);
if (!$headers) {
    echo "❌ Cannot read CSV headers\n";
    exit(1);
}

echo "CSV HEADERS:\n";
foreach ($headers as $index => $header) {
    echo "  [$index] $header\n";
}
echo "\n";

// Normalize headers
$normalizedHeaders = array_map(function($header) {
    $header = strtolower(trim($header));
    $header = str_replace(' ', '_', $header); // Replace spaces with underscores
    $header = preg_replace('/\(.*?\)/', '', $header); // Remove units in parentheses
    $header = trim($header, '_'); // Remove trailing underscores
    if ($header === 'crop') return 'crop_name';
    return $header;
}, $headers);

echo "NORMALIZED HEADERS:\n";
echo "  " . implode(', ', $normalizedHeaders) . "\n\n";

// Check required headers
$requiredHeaders = ['crop_name', 'municipality', 'farm_type', 'year'];
$missingHeaders = array_diff($requiredHeaders, $normalizedHeaders);

if (!empty($missingHeaders)) {
    echo "❌ MISSING REQUIRED HEADERS:\n";
    foreach ($missingHeaders as $missing) {
        echo "  - $missing\n";
    }
    echo "\nℹ️  Your CSV needs these columns: crop_name, municipality, farm_type, year, area_planted, area_harvested, production\n";
    echo "\n💡 TIP: If your CSV doesn't have 'farm_type', you can add a column with one of these values:\n";
    echo "   - irrigated\n";
    echo "   - rainfed\n";
    echo "   - upland\n";
    echo "   - lowland\n";
    fclose($handle);
    exit(1);
}

echo "✅ All required headers present\n\n";

// Create header mapping
$headerMap = array_flip($normalizedHeaders);

// Analyze the data
$totalRows = 0;
$validRows = 0;
$rejectedRows = 0;
$yearDistribution = [];
$rejectionReasons = [];
$sampleRejections = [];
$sampleValid = [];

echo "🔍 Analyzing rows...\n\n";

while (($row = fgetcsv($handle)) !== false) {
    $totalRows++;
    
    // Create data array
    $data = [];
    foreach ($normalizedHeaders as $index => $header) {
        $data[$header] = $row[$index] ?? '';
    }
    
    // Normalize farm_type to lowercase for consistency
    if (isset($data['farm_type'])) {
        $data['farm_type'] = strtolower(trim($data['farm_type']));
    }
    
    // Count year distribution
    $year = $data['year'] ?? '';
    if (!isset($yearDistribution[$year])) {
        $yearDistribution[$year] = 0;
    }
    $yearDistribution[$year]++;
    
    // Validate
    $validator = Validator::make($data, [
        'crop_name' => 'required|string',
        'municipality' => 'required|string',
        'farm_type' => 'required|in:irrigated,rainfed,upland,lowland',
        'year' => 'required|integer|min:2000|max:2030',
    ]);
    
    if ($validator->fails()) {
        $rejectedRows++;
        $errors = $validator->errors()->all();
        
        foreach ($errors as $error) {
            if (!isset($rejectionReasons[$error])) {
                $rejectionReasons[$error] = 0;
            }
            $rejectionReasons[$error]++;
        }
        
        if (count($sampleRejections) < 10) {
            $sampleRejections[] = [
                'row' => $totalRows + 1, // +1 for header
                'data' => $data,
                'errors' => $errors
            ];
        }
    } else {
        $validRows++;
        if (count($sampleValid) < 5) {
            $sampleValid[] = [
                'row' => $totalRows + 1,
                'data' => $data
            ];
        }
    }
    
    // Progress indicator
    if ($totalRows % 1000 == 0) {
        echo "  Processed $totalRows rows...\r";
    }
}

fclose($handle);

echo "\n\n";
echo "=== ANALYSIS RESULTS ===\n\n";
echo "📊 TOTAL ROWS: " . number_format($totalRows) . "\n";
echo "✅ VALID ROWS: " . number_format($validRows) . " (" . number_format($validRows/$totalRows*100, 1) . "%)\n";
echo "❌ REJECTED ROWS: " . number_format($rejectedRows) . " (" . number_format($rejectedRows/$totalRows*100, 1) . "%)\n\n";

if ($validRows > 0) {
    echo "✅ SAMPLE VALID ROWS:\n";
    foreach ($sampleValid as $sample) {
        echo "  Row {$sample['row']}: {$sample['data']['crop_name']} | {$sample['data']['municipality']} | {$sample['data']['farm_type']} | Year {$sample['data']['year']}\n";
    }
    echo "\n";
}

if ($rejectedRows > 0) {
    echo "❌ REJECTION REASONS:\n";
    arsort($rejectionReasons);
    foreach ($rejectionReasons as $reason => $count) {
        echo "  [$count rows] $reason\n";
    }
    echo "\n";
    
    echo "❌ SAMPLE REJECTED ROWS:\n";
    foreach ($sampleRejections as $sample) {
        echo "\n  Row {$sample['row']}:\n";
        echo "    Crop: {$sample['data']['crop_name']}\n";
        echo "    Municipality: {$sample['data']['municipality']}\n";
        echo "    Farm Type: {$sample['data']['farm_type']}\n";
        echo "    Year: {$sample['data']['year']}\n";
        echo "    Errors:\n";
        foreach ($sample['errors'] as $error) {
            echo "      • $error\n";
        }
    }
    echo "\n";
}

echo "📅 YEAR DISTRIBUTION:\n";
ksort($yearDistribution);
foreach ($yearDistribution as $year => $count) {
    $bar = str_repeat('█', min(50, (int)($count / max($yearDistribution) * 50)));
    echo sprintf("  %4s: %s %s\n", $year, $bar, number_format($count));
}
echo "\n";

// Compare with database
$dbCount = DB::table('crops')->count();
$dbYears = DB::table('crops')
    ->select('year', DB::raw('count(*) as count'))
    ->groupBy('year')
    ->orderBy('year')
    ->get();

echo "💾 DATABASE COMPARISON:\n";
echo "  Records in database: " . number_format($dbCount) . "\n";
echo "  Valid rows in CSV: " . number_format($validRows) . "\n";
if ($dbCount < $validRows) {
    echo "  ⚠️  Database has " . number_format($validRows - $dbCount) . " fewer records than valid CSV rows\n";
} elseif ($dbCount > $validRows) {
    echo "  ⚠️  Database has " . number_format($dbCount - $validRows) . " more records than valid CSV rows\n";
} else {
    echo "  ✅ Database matches valid CSV rows!\n";
}
echo "\n";

echo "  Years in database:\n";
foreach ($dbYears as $yearData) {
    $csvCount = $yearDistribution[$yearData->year] ?? 0;
    $match = $yearData->count == $csvCount ? '✅' : '⚠️';
    echo "    {$yearData->year}: " . number_format($yearData->count) . " records (CSV: $csvCount) $match\n";
}
echo "\n";

if ($rejectedRows > 0) {
    echo "🔧 RECOMMENDATIONS:\n";
    echo "  1. Fix the rejected rows in your CSV based on the error messages above\n";
    echo "  2. Make sure farm_type is one of: irrigated, rainfed, upland, lowland\n";
    echo "  3. Ensure year is between 2000 and 2030\n";
    echo "  4. Check that all required fields have values\n";
    echo "  5. Re-import the corrected CSV file\n";
} else {
    echo "🎉 All rows are valid! The CSV should import completely.\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";
