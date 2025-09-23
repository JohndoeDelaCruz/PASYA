<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crop Data Management - Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Force modals to be on top */
        #addCropModal, #batchUploadModal, #confirmDeleteModal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            z-index: 99999 !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
        }
        
        #addCropModal.hidden, #batchUploadModal.hidden, #confirmDeleteModal.hidden {
            display: none !important;
        }
        
        #addCropModal:not(.hidden), #batchUploadModal:not(.hidden), #confirmDeleteModal:not(.hidden) {
            display: block !important;
        }
    </style>
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

                <!-- Search and Filters -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <form method="GET" action="{{ route('admin.crops.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
                            <div class="flex-1 min-w-64">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Enter search item..." class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="min-w-28">
                                <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                                    <option value="">View</option>
                                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Descending</option>
                                </select>
                            </div>
                            <div class="min-w-36">
                                <select name="municipality" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                                    <option value="">Municipality</option>
                                    @if($allMunicipalities->count() > 0)
                                        @foreach($allMunicipalities as $municipality)
                                            <option value="{{ $municipality }}" {{ request('municipality') == $municipality ? 'selected' : '' }}>{{ $municipality }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="min-w-28">
                                <select name="crop" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                                    <option value="">Crop</option>
                                    @if($allCropNames->count() > 0)
                                        @foreach($allCropNames as $cropName)
                                            <option value="{{ $cropName }}" {{ request('crop') == $cropName ? 'selected' : '' }}>{{ $cropName }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                                    Search
                                </button>
                                <a href="{{ route('admin.crops.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                                    Reset
                                </a>
                            </div>
                            
                            @if(request('per_page'))
                                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                            @endif
                        </form>
                        <div class="flex-shrink-0">
                            <button onclick="deleteSelectedItems()" class="px-4 py-2 border border-red-300 text-red-600 hover:bg-red-50 rounded-md text-sm flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <span>Delete Selected</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Crops Table -->
                @if($crops->count() > 0)
                    <!-- Debug: Show crop count -->
                    <div class="mb-2 text-sm text-gray-600">
                        Showing {{ $crops->count() }} crops on this page ({{ $crops->total() }} total crops in database)
                    </div>
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="px-4 py-4 text-left w-12">
                                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                        </th>
                                        <th class="px-4 py-4 text-left text-sm font-medium text-gray-600 cursor-pointer hover:text-gray-800">
                                            Crop
                                            <svg class="w-3 h-3 inline ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                            </svg>
                                        </th>
                                        <th class="px-4 py-4 text-left text-sm font-medium text-gray-600 cursor-pointer hover:text-gray-800">
                                            Municipality
                                            <svg class="w-3 h-3 inline ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                            </svg>
                                        </th>
                                        <th class="px-4 py-4 text-left text-sm font-medium text-gray-600 cursor-pointer hover:text-gray-800">
                                            Year
                                            <svg class="w-3 h-3 inline ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                            </svg>
                                        </th>
                                        <th class="px-4 py-4 text-left text-sm font-medium text-gray-600 cursor-pointer hover:text-gray-800">
                                            Area Planted (ha)
                                            <svg class="w-3 h-3 inline ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                            </svg>
                                        </th>
                                        <th class="px-4 py-4 text-left text-sm font-medium text-gray-600 cursor-pointer hover:text-gray-800">
                                            Productivity (mt/ha)
                                            <svg class="w-3 h-3 inline ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                            </svg>
                                        </th>
                                        <th class="px-4 py-4 text-right text-sm font-medium text-gray-600 w-16">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white" id="cropTableBody">
                                    @forelse($crops as $crop)
                                        <tr class="border-b border-gray-100 hover:bg-gray-50 crop-row" 
                                            data-crop="{{ strtolower($crop->crop_name ?? $crop->name ?? '') }}"
                                            data-municipality="{{ strtolower($crop->municipality ?? '') }}"
                                            data-year="{{ $crop->year ?? '' }}"
                                            data-crop-id="{{ $crop->id }}">
                                            <td class="px-4 py-4">
                                                <input type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 row-checkbox">
                                            </td>
                                            <td class="px-4 py-4 text-sm font-medium text-gray-900">
                                                {{ $crop->crop_name ?? $crop->name }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ $crop->municipality ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ $crop->year ?? ($crop->planting_date ? $crop->planting_date->format('Y') : 'N/A') }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ $crop->area_planted ? number_format($crop->area_planted, 1) : ($crop->area_hectares ? number_format($crop->area_hectares, 1) : 'N/A') }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ $crop->productivity_mt_ha ? number_format($crop->productivity_mt_ha, 4) : 'N/A' }}
                                            </td>
                                            <td class="px-4 py-4 text-right">
                                                <div class="relative inline-block">
                                                    <button onclick="toggleDropdown({{ $crop->id }})" class="p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" title="More actions">
                                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                                        </svg>
                                                    </button>
                                                    <div id="dropdown-{{ $crop->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                                        <div class="py-1">
                                                            <button onclick="editCrop({{ $crop->id }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                Edit
                                                            </button>
                                                            <button onclick="deleteSingleItem({{ $crop->id }})" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                                                Delete
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                                @if(request()->hasAny(['search', 'municipality', 'crop']))
                                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                    </svg>
                                                    <div class="text-lg font-medium mb-2">No crops match your search criteria</div>
                                                    <div class="text-sm mb-4">
                                                        @if(request('search'))
                                                            No results for "<strong>{{ request('search') }}</strong>"
                                                        @endif
                                                        @if(request('municipality'))
                                                            in <strong>{{ request('municipality') }}</strong>
                                                        @endif
                                                        @if(request('crop'))
                                                            for <strong>{{ request('crop') }}</strong>
                                                        @endif
                                                    </div>
                                                    <a href="{{ route('admin.crops.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                                                        Clear Filters
                                                    </a>
                                                @else
                                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                    </svg>
                                                    <div class="text-lg font-medium mb-2">No crop data found</div>
                                                    <div class="text-sm">Start by adding crop data to the system.</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforelse


                                </tbody>
                            </table>
                        </div>

                        <!-- Table Footer -->
                        <div class="px-6 py-3 border-t bg-gray-50 flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                <span id="selectedCount">0</span> of <span id="totalCount">{{ $crops->count() }}</span> row(s) selected.
                                @if($crops->total() > 0)
                                    <span class="ml-4 text-gray-500">
                                        Showing {{ $crops->firstItem() }} to {{ $crops->lastItem() }} of {{ number_format($crops->total()) }} 
                                        @if(request()->hasAny(['search', 'municipality', 'crop']))
                                            filtered
                                        @endif
                                        results
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-sm text-gray-700">Rows per page</div>
                                <select id="rowsPerPage" class="px-2 py-1 border border-gray-300 rounded text-sm" onchange="changeRowsPerPage(this.value)">
                                    <option value="10" {{ request('per_page', 15) == '10' ? 'selected' : '' }}>10</option>
                                    <option value="15" {{ request('per_page', 15) == '15' || !request('per_page') ? 'selected' : '' }}>15</option>
                                    <option value="25" {{ request('per_page', 15) == '25' ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page', 15) == '50' ? 'selected' : '' }}>50</option>
                                </select>
                                <div class="text-sm text-gray-700">
                                    Page <span id="currentPage">{{ $crops->currentPage() }}</span> of <span id="totalPages">{{ $crops->hasPages() ? $crops->lastPage() : 1 }}</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <!-- First Page Button -->
                                    <button onclick="goToPage(1)" class="p-1 border border-gray-300 rounded hover:bg-gray-50 {{ $crops->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $crops->onFirstPage() ? 'disabled' : '' }}>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                                        </svg>
                                    </button>
                                    <!-- Previous Page Button -->
                                    <button onclick="goToPage({{ $crops->currentPage() - 1 }})" class="p-1 border border-gray-300 rounded hover:bg-gray-50 {{ $crops->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $crops->onFirstPage() ? 'disabled' : '' }}>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </button>
                                    <!-- Next Page Button -->
                                    <button onclick="goToPage({{ $crops->currentPage() + 1 }})" class="p-1 border border-gray-300 rounded hover:bg-gray-50 {{ !$crops->hasMorePages() ? 'opacity-50 cursor-not-allowed' : '' }}" {{ !$crops->hasMorePages() ? 'disabled' : '' }}>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                    <!-- Last Page Button -->
                                    <button onclick="goToPage({{ $crops->lastPage() }})" class="p-1 border border-gray-300 rounded hover:bg-gray-50 {{ !$crops->hasMorePages() ? 'opacity-50 cursor-not-allowed' : '' }}" {{ !$crops->hasMorePages() ? 'disabled' : '' }}>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="bg-white rounded-lg shadow p-8 text-center">
                        <!-- Debug: Show pagination info -->
                        <div class="mb-4 text-sm text-gray-500">
                            Debug: Current page {{ $crops->currentPage() }} of {{ $crops->lastPage() }} 
                            ({{ $crops->total() }} total crops in database)
                            <br>Per page: {{ $crops->perPage() }}
                        </div>
                        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No crops found on this page</h3>
                        <p class="text-gray-600 mb-6">Try going to page 1 or check if crops exist in the database.</p>
                        <div class="space-x-3">
                            <a href="{{ route('admin.crops.index', ['page' => 1]) }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5v14l11-7z"></path>
                                </svg>
                                <span>Go to First Page</span>
                            </a>
                            <button onclick="openAddCropModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>Add First Crop</span>
                        </button>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Add Crop Modal -->
    <div id="addCropModal" class="fixed inset-0 overflow-y-auto h-full w-full hidden" style="z-index: 99999; background-color: rgba(0, 0, 0, 0.5);">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white" style="z-index: 100000;">
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
                    
                    <form id="addCropForm">
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
    <div id="batchUploadModal" class="fixed inset-0 overflow-y-auto h-full w-full hidden" style="z-index: 99999; background-color: rgba(0, 0, 0, 0.5);">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white" style="z-index: 100000;">
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
                    <p class="text-sm text-gray-700 mb-4">Upload CSV or Excel files with agricultural statistics data:</p>
                    <p class="text-xs text-gray-600 mb-4 leading-relaxed"><strong>Required columns:</strong> Municipality, Crop_Name, Farm_Type, Year, Area_Planted (ha), Area_Harvested (ha), Production (mt), and Productivity (mt/ha)</p>
                    <p class="text-xs text-blue-600 mb-4"><strong>Supported file types:</strong> .csv, .txt, .xlsx, .xls</p>
                    

                    
                    <form id="batchUploadForm" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Upload CSV Label -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload File (CSV/Excel)*</label>
                            
                            <!-- File Input Button -->
                            <div class="mb-3">
                                <label for="csv_file" class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md cursor-pointer transition-colors duration-200 border border-gray-300">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                    </svg>
                                    Select file
                                </label>
                                <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt,.xlsx,.xls" required class="hidden">
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

    <!-- Confirmation Delete Modal -->
    <div id="confirmDeleteModal" class="fixed inset-0 overflow-y-auto h-full w-full hidden" style="z-index: 99999; background-color: rgba(0, 0, 0, 0.5);">
        <div class="relative top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-11/12 max-w-md shadow-lg rounded-md bg-white" style="z-index: 100000;">
            <div class="p-6">
                <!-- Modal Header -->
                <div class="text-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Confirm Data Deletion</h3>
                </div>

                <!-- Modal Body -->
                <div class="text-center mb-6">
                    <p class="text-sm text-gray-600 mb-2">Are you sure you want to archive the selected items?</p>
                    <p class="text-xs text-gray-500">This will be removed from the records.</p>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-center space-x-4">
                    <button onclick="closeConfirmDeleteModal()" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition-colors duration-200">
                        No
                    </button>
                    <button onclick="confirmDelete()" class="px-6 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition-colors duration-200">
                        Yes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        console.log('Crop management JavaScript loaded');
        
        function openAddCropModal() {
            console.log('openAddCropModal called');
            const modal = document.getElementById('addCropModal');
            console.log('Modal element:', modal);
            if (!modal) {
                console.error('addCropModal element not found');
                return;
            }
            modal.classList.remove('hidden');
            modal.style.display = 'block';
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100%';
            modal.style.height = '100%';
            modal.style.zIndex = '99999';
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
            console.log('Modal should be open now');
        }

        function closeAddCropModal() {
            const modal = document.getElementById('addCropModal');
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.getElementById('addCropForm').reset();
            document.body.style.overflow = 'auto'; // Restore scrolling
        }

        function openBatchUploadModal() {
            console.log('openBatchUploadModal called');
            const modal = document.getElementById('batchUploadModal');
            console.log('Batch modal element:', modal);
            if (!modal) {
                console.error('batchUploadModal element not found');
                return;
            }
            modal.classList.remove('hidden');
            modal.style.display = 'block';
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100%';
            modal.style.height = '100%';
            modal.style.zIndex = '99999';
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
            console.log('Batch modal should be open now');
        }

        function closeBatchUploadModal() {
            const modal = document.getElementById('batchUploadModal');
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.getElementById('batchUploadForm').reset();
            document.getElementById('fileName').textContent = '';
            resetDropZone();
            document.body.style.overflow = 'auto'; // Restore scrolling
        }

        // Confirmation modal functions
        let itemsToDelete = [];
        
        function openConfirmDeleteModal(cropIds = []) {
            itemsToDelete = cropIds;
            const modal = document.getElementById('confirmDeleteModal');
            modal.classList.remove('hidden');
            modal.style.display = 'block';
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100%';
            modal.style.height = '100%';
            modal.style.zIndex = '99999';
            modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            document.body.style.overflow = 'hidden';
        }

        function closeConfirmDeleteModal() {
            const modal = document.getElementById('confirmDeleteModal');
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            itemsToDelete = [];
        }

        function confirmDelete() {
            if (itemsToDelete.length === 0) {
                showErrorMessage('No items selected for deletion.');
                closeConfirmDeleteModal();
                return;
            }

            // Show loading state and prevent double-clicking
            const confirmBtn = document.querySelector('#confirmDeleteModal button[onclick="confirmDelete()"]');
            if (!confirmBtn) {
                console.error('Confirm button not found');
                alert('Delete button not found. Please refresh the page.');
                return;
            }
            if (confirmBtn.disabled) {
                return; // Already processing
            }
            
            const originalText = confirmBtn.textContent;
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Archiving...';

            // Send delete request
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token not found');
                showErrorMessage('Security token not found. Please refresh the page.');
                return;
            }
            
            fetch('{{ route("admin.crops.delete-multiple") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                },
                body: JSON.stringify({ crop_ids: itemsToDelete })
            })
            .then(response => {
                console.log('Response status:', response.status, response.statusText);
                console.log('Response headers:', response.headers.get('content-type'));
                
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Error response body:', text.substring(0, 1000));
                        try {
                            const data = JSON.parse(text);
                            throw new Error(data.message || `Server error: ${response.status}`);
                        } catch (parseError) {
                            console.error('JSON parse error:', parseError);
                            throw new Error(`Server returned HTML error page. Status: ${response.status}`);
                        }
                    });
                }
                
                // Check if response is actually JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        console.error('Non-JSON response received:', text.substring(0, 1000));
                        throw new Error('Server returned non-JSON response');
                    });
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Full response data:', data); // Debug full response
                if (data.success) {
                    console.log('Archive success:', data); // Debug log
                    const archivedCount = data.debug && data.debug.archived_count ? data.debug.archived_count : itemsToDelete.length;
                    showSuccessMessage(data.message || `Successfully archived ${archivedCount} item(s)!`);
                    closeConfirmDeleteModal();
                    
                    // Remove archived rows from table immediately
                    console.log('Removing rows for IDs:', itemsToDelete);
                    
                    itemsToDelete.forEach(cropId => {
                        const cropIdStr = String(cropId);
                        
                        // Try multiple selectors to find the row
                        let row = document.querySelector(`tr[data-crop-id="${cropIdStr}"]`);
                        
                        if (!row) {
                            // Alternative: look through all rows manually
                            const allRows = document.querySelectorAll('tbody tr');
                            for (let r of allRows) {
                                if (r.getAttribute('data-crop-id') === cropIdStr) {
                                    row = r;
                                    break;
                                }
                            }
                        }
                        
                        if (row) {
                            console.log(`Removing row for crop ID: ${cropIdStr}`);
                            row.style.opacity = '0.5'; // Visual feedback
                            setTimeout(() => {
                                row.remove();
                                console.log(`Row ${cropIdStr} removed successfully`);
                            }, 200);
                        } else {
                            console.error(`Could not find row for crop ID: ${cropIdStr}`);
                        }
                    });
                    
                    // Update counts and check if table is empty
                    updateTotalCount();
                    updateSelectedCount();
                    
                    // Check if table is now empty and show empty state
                    setTimeout(() => {
                        const tableBody = document.getElementById('cropTableBody');
                        const remainingRows = tableBody.querySelectorAll('tr.crop-row');
                        
                        if (remainingRows.length === 0) {
                            // Add empty state
                            const emptyRow = document.createElement('tr');
                            emptyRow.innerHTML = `
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div class="text-lg font-medium mb-2">No crop data found</div>
                                        <p class="text-gray-500 mb-4">Get started by adding your first crop data</p>
                                    </div>
                                </td>
                            `;
                            tableBody.appendChild(emptyRow);
                        }
                    }, 300);
                } else {
                    // Even if server says items don't exist, remove them from UI
                    // (they might have been archived in another tab/session)
                    if (data.message && data.message.includes('already archived')) {
                        console.log('Items already archived, removing from UI anyway');
                        
                        // Remove the rows since they're already archived
                        itemsToDelete.forEach(cropId => {
                            const cropIdStr = String(cropId);
                            let row = document.querySelector(`tr[data-crop-id="${cropIdStr}"]`);
                            if (!row) {
                                const allRows = document.querySelectorAll('tbody tr');
                                for (let r of allRows) {
                                    if (r.getAttribute('data-crop-id') === cropIdStr) {
                                        row = r;
                                        break;
                                    }
                                }
                            }
                            if (row) {
                                row.style.opacity = '0.5';
                                setTimeout(() => row.remove(), 200);
                            }
                        });
                        
                        showSuccessMessage('Items were already archived and have been removed from the list.');
                        updateTotalCount();
                        updateSelectedCount();
                        
                        // Check if table is now empty
                        setTimeout(() => {
                            const tableBody = document.getElementById('cropTableBody');
                            const remainingRows = tableBody.querySelectorAll('tr.crop-row');
                            if (remainingRows.length === 0) {
                                const emptyRow = document.createElement('tr');
                                emptyRow.innerHTML = `
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <div class="text-lg font-medium mb-2">No crop data found</div>
                                            <p class="text-gray-500 mb-4">Get started by adding your first crop data</p>
                                        </div>
                                    </td>
                                `;
                                tableBody.appendChild(emptyRow);
                            }
                        }, 300);
                        
                    } else {
                        throw new Error(data.message || 'Failed to archive items');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage(error.message || 'Failed to delete items. Please try again.');
            })
            .finally(() => {
                confirmBtn.disabled = false;
                confirmBtn.textContent = originalText;
            });
        }



        // Function to toggle dropdown menu
        function toggleDropdown(cropId) {
            // Close all other dropdowns first
            document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
                if (dropdown.id !== `dropdown-${cropId}`) {
                    dropdown.classList.add('hidden');
                }
            });
            
            // Toggle current dropdown
            const dropdown = document.getElementById(`dropdown-${cropId}`);
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }

        // Function to edit crop
        function editCrop(cropId) {
            // Close dropdown
            const dropdown = document.getElementById(`dropdown-${cropId}`);
            if (dropdown) {
                dropdown.classList.add('hidden');
            }
            
            // For now, just show an alert - you can implement edit modal later
            alert('Edit functionality will be implemented');
        }

        // Function to delete single item
        function deleteSingleItem(cropId) {
            // Close dropdown
            const dropdown = document.getElementById(`dropdown-${cropId}`);
            if (dropdown) {
                dropdown.classList.add('hidden');
            }
            
            openConfirmDeleteModal([cropId]);
        }

        // Function to delete selected items
        function deleteSelectedItems() {
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            const selectedIds = Array.from(checkedBoxes).map(checkbox => {
                const row = checkbox.closest('tr');
                const cropId = row ? row.getAttribute('data-crop-id') : null;
                console.log('Checkbox row crop ID:', cropId); // Debug log
                return cropId;
            }).filter(id => id); // Remove any null/undefined IDs
            
            console.log('Selected IDs for deletion:', selectedIds); // Debug log

            if (selectedIds.length === 0) {
                showErrorMessage('Please select items to delete.');
                return;
            }

            openConfirmDeleteModal(selectedIds);
        }

        // AJAX form submission for Add Crop
        const addCropForm = document.getElementById('addCropForm');
        if (addCropForm) {
            addCropForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                if (!submitBtn) {
                    console.error('Submit button not found in add crop form');
                    return;
                }
                const originalText = submitBtn.textContent;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
            
            const csrfTokenAdd = document.querySelector('meta[name="csrf-token"]');
            if (!csrfTokenAdd) {
                console.error('CSRF token not found for add crop');
                showErrorMessage('Security token not found. Please refresh the page.');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                return;
            }
            
            fetch('{{ route("admin.crops.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfTokenAdd.getAttribute('content')
                }
            })
            .then(response => {
                console.log('Add crop response status:', response.status, response.statusText);
                console.log('Add crop response headers:', response.headers.get('content-type'));
                
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Add crop error response:', text.substring(0, 1000));
                        try {
                            const data = JSON.parse(text);
                            throw new Error(data.message || 'Server error');
                        } catch (parseError) {
                            console.error('Add crop JSON parse error:', parseError);
                            throw new Error(`Server returned HTML error. Status: ${response.status}`);
                        }
                    });
                }
                
                // Check if response is actually JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        console.error('Add crop non-JSON response:', text.substring(0, 1000));
                        throw new Error('Server returned non-JSON response');
                    });
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data); // Debug log
                if (data.success) {
                    // Show success message
                    showSuccessMessage(data.message || 'Crop added successfully!');
                    closeAddCropModal();
                    // Add new crop to the table dynamically
                    if (data.crop) {
                        console.log('Adding crop to table:', data.crop); // Debug log
                        addCropToTable(data.crop);
                        updateTotalCount();
                    }
                } else {
                    throw new Error(data.message || 'Failed to add crop');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage(error.message || 'Failed to add crop. Please try again.');
            })
            .finally(() => {
                // Restore button state
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
            });
        }

        // AJAX form submission for Batch Upload
        const batchUploadForm = document.getElementById('batchUploadForm');
        if (batchUploadForm) {
            batchUploadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Batch upload form submitted!');
                
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                if (!submitBtn) {
                    console.error('Submit button not found in batch upload form');
                    return;
                }
                const originalText = submitBtn.textContent;
            
            // Check if file is selected
            const fileInput = document.getElementById('csv_file');
            if (!fileInput.files || fileInput.files.length === 0) {
                alert('Please select a file to upload');
                return;
            }
            
            console.log('File selected:', fileInput.files[0].name);
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.textContent = 'Uploading...';
            
            const csrfTokenBatch = document.querySelector('meta[name="csrf-token"]');
            if (!csrfTokenBatch) {
                console.error('CSRF token not found for batch upload');
                showErrorMessage('Security token not found. Please refresh the page.');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                return;
            }
            
            console.log('Submitting batch upload form...');
            console.log('FormData contents:');
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }
            
            fetch('{{ route("admin.crops.import") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfTokenBatch.getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Server error');
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Batch upload response:', data);
                if (data.success) {
                    // Show success message
                    showSuccessMessage(data.message || 'Data imported successfully!');
                    closeBatchUploadModal();
                    // Clear the file input
                    document.getElementById('csv_file').value = '';
                    document.getElementById('fileName').textContent = '';
                    // Reload page to show new imported data (batch import is complex to update dynamically)
                    setTimeout(() => {
                        console.log('Reloading page to show imported data...');
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Failed to import data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage(error.message || 'Failed to import data. Please try again.');
            })
            .finally(() => {
                // Restore button state
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
            });
        }

        // Function to add new crop row to table dynamically
        function addCropToTable(crop) {
            const tableBody = document.getElementById('cropTableBody');
            
            // Remove empty state if it exists
            const emptyRow = tableBody.querySelector('tr td[colspan="7"]');
            if (emptyRow) {
                emptyRow.closest('tr').remove();
            }
            
            // Create new row
            const newRow = document.createElement('tr');
            newRow.className = 'hover:bg-gray-50 crop-row';
            newRow.setAttribute('data-crop-id', crop.id); // CRITICAL: Add the crop ID for deletion
            newRow.setAttribute('data-crop', (crop.crop_name || crop.name || '').toLowerCase());
            newRow.setAttribute('data-municipality', (crop.municipality || '').toLowerCase());
            newRow.setAttribute('data-year', crop.year || '');
            
            newRow.innerHTML = `
                <td class="px-6 py-4">
                    <input type="checkbox" class="rounded row-checkbox">
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                    ${crop.crop_name || crop.name || 'N/A'}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    ${crop.municipality || 'N/A'}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    ${crop.year || 'N/A'}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    ${crop.area_planted ? parseFloat(crop.area_planted).toFixed(1) : (crop.area_hectares ? parseFloat(crop.area_hectares).toFixed(1) : 'N/A')}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    ${crop.productivity_mt_ha ? parseFloat(crop.productivity_mt_ha).toFixed(4) : 'N/A'}
                </td>
                <td class="px-6 py-4 text-sm">
                    <div class="flex space-x-2">
                        <button onclick="deleteSingleItem(${crop.id})" class="text-red-400 hover:text-red-600 transition-colors duration-200" title="Delete item">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </td>
            `;
            
            // Add at the beginning of the table
            tableBody.insertBefore(newRow, tableBody.firstChild);
            
            // Highlight the new row briefly
            newRow.classList.add('bg-green-50');
            setTimeout(() => {
                newRow.classList.remove('bg-green-50');
            }, 2000);
        }
        
        // Function to update total count display
        function updateTotalCount() {
            const totalCountElement = document.getElementById('totalCount');
            if (totalCountElement) {
                const visibleRows = document.querySelectorAll('.crop-row:not([style*="display: none"])').length;
                totalCountElement.textContent = visibleRows;
            }
        }

        // Helper functions for showing messages
        function showSuccessMessage(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg z-[10000] max-w-md';
            alertDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }

        function showErrorMessage(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg z-[10000] max-w-md';
            alertDiv.innerHTML = `
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="whitespace-pre-line">${message}</span>
                </div>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Close modal when clicking outside
        document.getElementById('addCropModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddCropModal();
            }
        });

        // Close modal when clicking outside
        document.getElementById('batchUploadModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBatchUploadModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (!document.getElementById('addCropModal').classList.contains('hidden')) {
                    closeAddCropModal();
                }
                if (!document.getElementById('batchUploadModal').classList.contains('hidden')) {
                    closeBatchUploadModal();
                }
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
        
        if (dropZone) {
            dropZone.addEventListener('click', function() {
                const fileInput = document.getElementById('csv_file');
                if (fileInput) {
                    fileInput.click();
                }
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
                        const fileInput = document.getElementById('csv_file');
                        const fileName = document.getElementById('fileName');
                        if (fileInput) fileInput.files = files;
                        if (fileName) fileName.textContent = file.name;
                        updateDropZoneSuccess();
                    } else {
                        alert('Please select a CSV file');
                    }
                }
            });
        }

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

        // Table functionality - Search, Filter, Selection
        const selectAllCheckbox = document.getElementById('selectAll');
        const selectedCountSpan = document.getElementById('selectedCount');

        // Only initialize table functionality if elements exist
        if (selectAllCheckbox) {

        // Checkbox functionality
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const visibleCheckboxes = document.querySelectorAll('.crop-row:not([style*="display: none"]) .row-checkbox');
                visibleCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelectedCount();
            });
        }

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('row-checkbox')) {
                updateSelectedCount();
            }
        });

        function updateSelectedCount() {
            const visibleCheckboxes = document.querySelectorAll('.crop-row:not([style*="display: none"]) .row-checkbox');
            const checkedBoxes = document.querySelectorAll('.crop-row:not([style*="display: none"]) .row-checkbox:checked');
            
            if (selectedCountSpan) {
                selectedCountSpan.textContent = checkedBoxes.length;
            }
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = visibleCheckboxes.length > 0 && checkedBoxes.length === visibleCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < visibleCheckboxes.length;
            }
        }

        // Initialize count on page load
        updateSelectedCount();
        
        } // End of table functionality conditional check
        
        // Pagination functions
        function changeRowsPerPage(perPage) {
            const url = new URL(window.location);
            url.searchParams.set('per_page', perPage);
            url.searchParams.set('page', 1); // Reset to first page when changing rows per page
            window.location.href = url.toString();
        }
        
        function goToPage(page) {
            const url = new URL(window.location);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.relative')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });
            }
        });

        // Add event listeners as backup for onclick handlers
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded');
            
            // Add Crop Modal Button
            const addCropButtons = document.querySelectorAll('button[onclick="openAddCropModal()"]');
            addCropButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    console.log('Add crop button clicked via event listener');
                    e.preventDefault();
                    openAddCropModal();
                });
            });
            
            // Batch Upload Modal Button  
            const batchUploadButtons = document.querySelectorAll('button[onclick="openBatchUploadModal()"]');
            batchUploadButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    console.log('Batch upload button clicked via event listener');
                    e.preventDefault();
                    openBatchUploadModal();
                });
            });
            
            console.log('Event listeners added');
        });
    </script>
</body>
</html>