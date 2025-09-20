<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import/Export Crops - Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 h-screen overflow-hidden">
    <div class="flex h-full">
        @include('admin.partials.sidebar', ['active' => 'crops'])

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.crops.index') }}" class="text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Import/Export Crops</h1>
                            <p class="text-sm text-gray-600">Bulk import and export crop data</p>
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-6">
                <div class="max-w-4xl mx-auto space-y-6">
                    <!-- Success/Warning/Error Messages -->
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            <pre class="whitespace-pre-wrap">{{ session('success') }}</pre>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                            <pre class="whitespace-pre-wrap">{{ session('warning') }}</pre>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <pre class="whitespace-pre-wrap">{{ session('error') }}</pre>
                        </div>
                    @endif

                    <!-- Export Section -->
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Export Crops</h2>
                        <p class="text-gray-600 mb-6">Download crop data in CSV format for backup or analysis.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Export All Crops -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h3 class="font-medium text-gray-900 mb-2">Export All Crops</h3>
                                <p class="text-sm text-gray-600 mb-4">Download all crop data from all farmers.</p>
                                <a href="{{ route('admin.crops.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span>Export All</span>
                                </a>
                            </div>

                            <!-- Export by Farmer -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h3 class="font-medium text-gray-900 mb-2">Export by Farmer</h3>
                                <p class="text-sm text-gray-600 mb-4">Download crop data for a specific farmer.</p>
                                <form action="{{ route('admin.crops.export') }}" method="GET" class="space-y-3">
                                    <select name="farmer_id" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                        <option value="">Select a farmer...</option>
                                        @foreach(\App\Models\Farmer::active()->orderBy('farmerName')->get() as $farmer)
                                            <option value="{{ $farmer->farmerID }}">{{ $farmer->farmerName }} - {{ $farmer->farmerLocation }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center justify-center space-x-2 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span>Export Selected</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Import Section -->
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Import Crops</h2>
                        <p class="text-gray-600 mb-6">Upload a CSV file to bulk import crop data. Make sure to follow the required format.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Download Template -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h3 class="font-medium text-gray-900 mb-2">Download Template</h3>
                                <p class="text-sm text-gray-600 mb-4">Get the CSV template with the correct format and example data.</p>
                                <a href="{{ route('admin.crops.template') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span>Download Template</span>
                                </a>
                            </div>

                            <!-- Upload File -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h3 class="font-medium text-gray-900 mb-2">Upload CSV File</h3>
                                <p class="text-sm text-gray-600 mb-4">Select and upload your CSV file with crop data.</p>
                                <form action="{{ route('admin.crops.import') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                    @csrf
                                    <div>
                                        <input type="file" name="csv_file" accept=".csv,.txt" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                        @error('csv_file')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg inline-flex items-center justify-center space-x-2 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                        </svg>
                                        <span>Import CSV</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Import Instructions -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-blue-900 mb-3">Import Instructions</h3>
                        <div class="text-sm text-blue-800 space-y-2">
                            <p><strong>CSV Format Requirements:</strong></p>
                            <ul class="list-disc list-inside space-y-1 ml-4">
                                <li>File must be in CSV format (.csv or .txt)</li>
                                <li>Maximum file size: 2MB</li>
                                <li>First row should contain headers (download template for exact format)</li>
                                <li>Farmer names must match existing farmers in the system</li>
                                <li>Dates should be in YYYY-MM-DD format</li>
                                <li>Status must be one of: planted, growing, harvested, failed</li>
                                <li>Area must be a positive number (hectares)</li>
                                <li>Empty optional fields will be ignored</li>
                            </ul>
                            <p class="mt-3"><strong>Required Columns:</strong> Farmer Name, Crop Name, Planting Date, Area (Hectares), Status</p>
                            <p><strong>Optional Columns:</strong> Variety, Expected Harvest Date, Expected Yield (kg), Description</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>