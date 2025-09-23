<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'Current crop count: ' . App\Models\Crop::count() . PHP_EOL;
echo 'Recent crops:' . PHP_EOL;
foreach(App\Models\Crop::latest()->take(5)->get() as $crop) {
    echo '- ' . ($crop->crop_name ?? $crop->name) . ' (' . ($crop->municipality ?? 'No municipality') . ')' . PHP_EOL;
}