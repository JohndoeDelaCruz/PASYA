<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crop Data Management - Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 h-screen overflow-hidden">
    <div class="flex h-full">
        @include('admin.partials.sidebar', ['active' => 'crops'])

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Crop Data Management</h1>
                        <p class="text-sm text-gray-600">Manage crop data for all farmers</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.crops.import-export') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            <span>Import/Export</span>
                        </a>
                        <button onclick="openBatchUploadModal()" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            <span>Batch Import - Data</span>
                        </button>
                        <button onclick="openAddCropModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>Add Single - Data</span>
                        </button>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-6">
                <!-- Success Message -->
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Crops Table -->
                @if($crops->count() > 0)
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Crop</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location/Farmer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type/Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Production</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year/Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($crops as $crop)
                                        <tr class="hover:bg-gray-50">
                                            <!-- Crop Name -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $crop->crop_name ?? $crop->name }}
                                                        </div>
                                                        @if($crop->variety)
                                                            <div class="text-sm text-gray-500">{{ $crop->variety }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Location/Farmer -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($crop->municipality)
                                                    <!-- Agricultural Statistics Data Format -->
                                                    <div class="text-sm text-gray-900">{{ $crop->municipality }}</div>
                                                    <div class="text-sm text-gray-500">Municipality</div>
                                                @else
                                                    <!-- Individual Farmer Format -->
                                                    <div class="text-sm text-gray-900">{{ $crop->farmer->farmerName ?? 'N/A' }}</div>
                                                    <div class="text-sm text-gray-500">{{ $crop->farmer->farmerLocation ?? '' }}</div>
                                                @endif
                                            </td>

                                            <!-- Type/Status -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($crop->farm_type)
                                                    <!-- Agricultural Statistics Data Format -->
                                                    <div class="text-sm font-medium text-gray-900">{{ ucfirst($crop->farm_type) }}</div>
                                                    <div class="text-sm text-gray-500">Farm Type</div>
                                                @else
                                                    <!-- Individual Farmer Format -->
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        @if($crop->status === 'planted') bg-blue-100 text-blue-800
                                                        @elseif($crop->status === 'growing') bg-green-100 text-green-800
                                                        @elseif($crop->status === 'harvested') bg-yellow-100 text-yellow-800
                                                        @else bg-red-100 text-red-800
                                                        @endif">
                                                        {{ ucfirst($crop->status) }}
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Area -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($crop->area_planted && $crop->area_harvested)
                                                    <!-- Agricultural Statistics Data Format -->
                                                    <div>Planted: {{ number_format($crop->area_planted, 2) }} ha</div>
                                                    <div class="text-xs text-gray-500">Harvested: {{ number_format($crop->area_harvested, 2) }} ha</div>
                                                @else
                                                    <!-- Individual Farmer Format -->
                                                    {{ $crop->area_hectares ? number_format($crop->area_hectares, 2) . ' ha' : 'N/A' }}
                                                @endif
                                            </td>

                                            <!-- Production -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($crop->production_mt)
                                                    <!-- Agricultural Statistics Data Format -->
                                                    <div>{{ number_format($crop->production_mt, 2) }} mt</div>
                                                    @if($crop->productivity_mt_ha)
                                                        <div class="text-xs text-gray-500">{{ number_format($crop->productivity_mt_ha, 2) }} mt/ha</div>
                                                    @endif
                                                @else
                                                    <!-- Individual Farmer Format -->
                                                    {{ $crop->expected_yield_kg ? number_format($crop->expected_yield_kg, 0) . ' kg' : 'N/A' }}
                                                @endif
                                            </td>

                                            <!-- Year/Date -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($crop->year)
                                                    <!-- Agricultural Statistics Data Format -->
                                                    <div class="font-medium">{{ $crop->year }}</div>
                                                    <div class="text-xs text-gray-500">Year</div>
                                                @else
                                                    <!-- Individual Farmer Format -->
                                                    <div>{{ $crop->planting_date ? $crop->planting_date->format('M d, Y') : 'N/A' }}</div>
                                                    @if($crop->expected_harvest_date)
                                                        <div class="text-xs text-gray-500">Harvest: {{ $crop->expected_harvest_date->format('M d, Y') }}</div>
                                                    @endif
                                                @endif
                                            </td>

                                            <!-- Actions -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('admin.crops.show', $crop) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                                    <span class="text-gray-300">|</span>
                                                    <a href="{{ route('admin.crops.edit', $crop) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                    <span class="text-gray-300">|</span>
                                                    <form action="{{ route('admin.crops.destroy', $crop) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this crop?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            {{ $crops->links() }}
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="bg-white rounded-lg shadow p-8 text-center">
                        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No crops found</h3>
                        <p class="text-gray-600 mb-6">Start by adding the first crop entry to the system.</p>
                        <a href="{{ route('admin.crops.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>Add First Crop</span>
                        </a>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Add Crop Modal -->
    <div id="addCropModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Add Single Data Entry</h3>
                    <button onclick="closeAddCropModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="mt-4">
                    <p class="text-sm text-gray-600 mb-4">All required fields are marked with *</p>
                    
                    <form id="addCropForm" method="POST" action="{{ route('admin.crops.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Municipality -->
                            <div>
                                <label for="municipality" class="block text-sm font-medium text-gray-700 mb-1">Municipality*</label>
                                <input type="text" id="municipality" name="municipality" placeholder="Municipality" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Farm Type -->
                            <div>
                                <label for="farm_type" class="block text-sm font-medium text-gray-700 mb-1">Farm Type*</label>
                                <select id="farm_type" name="farm_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select farm type</option>
                                    <option value="irrigated">Irrigated</option>
                                    <option value="rainfed">Rainfed</option>
                                    <option value="upland">Upland</option>
                                    <option value="lowland">Lowland</option>
                                </select>
                            </div>

                            <!-- Year -->
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year*</label>
                                <input type="number" id="year" name="year" placeholder="Year" min="2000" max="2030" value="{{ date('Y') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Crop -->
                            <div>
                                <label for="crop_name" class="block text-sm font-medium text-gray-700 mb-1">Crop*</label>
                                <input type="text" id="crop_name" name="crop_name" placeholder="Crop" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Area Planted -->
                            <div>
                                <label for="area_planted" class="block text-sm font-medium text-gray-700 mb-1">Area Planted (ha)*</label>
                                <input type="number" id="area_planted" name="area_planted" placeholder="Enter a number" min="0" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Area Harvested -->
                            <div>
                                <label for="area_harvested" class="block text-sm font-medium text-gray-700 mb-1">Area Harvested (ha)*</label>
                                <input type="number" id="area_harvested" name="area_harvested" placeholder="Enter a number" min="0" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Production -->
                            <div>
                                <label for="production" class="block text-sm font-medium text-gray-700 mb-1">Production (mt)*</label>
                                <input type="number" id="production" name="production" placeholder="Enter a number" min="0" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Productivity -->
                            <div>
                                <label for="productivity" class="block text-sm font-medium text-gray-700 mb-1">Productivity (mt/ha)</label>
                                <input type="number" id="productivity" name="productivity" placeholder="Enter a number" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Batch Upload Modal -->
    <div id="batchUploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Batch Upload Data</h3>
                    <button onclick="closeBatchUploadModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="mt-4">
                    <p class="text-sm text-gray-700 mb-4">Make sure that the uploaded CSV follows the columns, respectively:</p>
                    <p class="text-xs text-gray-600 mb-6 leading-relaxed">Municipality, Farm Type, Year, Crop, Area Planted (ha), Area Harvested (ha), Production (mt), and Productivity (mt/ha)</p>
                    
                    <form id="batchUploadForm" method="POST" action="{{ route('admin.crops.import') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Upload CSV Label -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload CSV*</label>
                            
                            <!-- File Input Button -->
                            <div class="mb-3">
                                <label for="csv_file" class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md cursor-pointer transition-colors duration-200 border border-gray-300">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                    </svg>
                                    Select file
                                </label>
                                <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" required class="hidden">
                                <span id="fileName" class="ml-3 text-sm text-gray-600"></span>
                            </div>
                            
                            <!-- Drag and Drop Area -->
                            <div id="dropZone" class="border-2 border-dashed border-yellow-300 rounded-lg p-8 text-center bg-yellow-50 hover:bg-yellow-100 transition-colors duration-200 cursor-pointer">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                </svg>
                                <p class="text-gray-500 text-sm">...or drag and drop file here!</p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openAddCropModal() {
            document.getElementById('addCropModal').classList.remove('hidden');
        }

        function closeAddCropModal() {
            document.getElementById('addCropModal').classList.add('hidden');
            document.getElementById('addCropForm').reset();
        }

        function openBatchUploadModal() {
            document.getElementById('batchUploadModal').classList.remove('hidden');
        }

        function closeBatchUploadModal() {
            document.getElementById('batchUploadModal').classList.add('hidden');
            document.getElementById('batchUploadForm').reset();
            document.getElementById('fileName').textContent = '';
            resetDropZone();
        }

        // Close modal when clicking outside
        document.getElementById('addCropModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddCropModal();
            }
        });

        // Auto-calculate productivity when production and area harvested change
        document.getElementById('production').addEventListener('input', calculateProductivity);
        document.getElementById('area_harvested').addEventListener('input', calculateProductivity);

        function calculateProductivity() {
            const production = parseFloat(document.getElementById('production').value) || 0;
            const areaHarvested = parseFloat(document.getElementById('area_harvested').value) || 0;
            
            if (areaHarvested > 0) {
                const productivity = (production / areaHarvested).toFixed(2);
                document.getElementById('productivity').value = productivity;
            } else {
                document.getElementById('productivity').value = '';
            }
        }

        // Batch Upload Modal Functions
        document.getElementById('batchUploadModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBatchUploadModal();
            }
        });

        // File input change handler
        document.getElementById('csv_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                document.getElementById('fileName').textContent = file.name;
                updateDropZoneSuccess();
            }
        });

        // Drag and Drop functionality
        const dropZone = document.getElementById('dropZone');

        dropZone.addEventListener('click', function() {
            document.getElementById('csv_file').click();
        });

        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropZone.classList.add('border-yellow-500', 'bg-yellow-100');
            dropZone.classList.remove('border-yellow-300', 'bg-yellow-50');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            resetDropZone();
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            resetDropZone();
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type === 'text/csv' || file.name.endsWith('.csv') || file.name.endsWith('.txt')) {
                    document.getElementById('csv_file').files = files;
                    document.getElementById('fileName').textContent = file.name;
                    updateDropZoneSuccess();
                } else {
                    alert('Please select a CSV file');
                }
            }
        });

        function resetDropZone() {
            dropZone.classList.remove('border-yellow-500', 'bg-yellow-100', 'border-green-500', 'bg-green-50');
            dropZone.classList.add('border-yellow-300', 'bg-yellow-50');
            dropZone.innerHTML = `
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                <p class="text-gray-500 text-sm">...or drag and drop file here!</p>
            `;
        }

        function updateDropZoneSuccess() {
            dropZone.classList.remove('border-yellow-300', 'bg-yellow-50');
            dropZone.classList.add('border-green-500', 'bg-green-50');
            dropZone.innerHTML = `
                <svg class="w-12 h-12 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-green-600 text-sm font-medium">File selected successfully!</p>
            `;
        }
    </script>
</body>
</html>