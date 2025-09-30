<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crop Production Management - Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Required field styling */
        .required-field label::after {
            content: " *";
            color: #ef4444;
            font-weight: bold;
        }
        
        /* Error state styling */
        .border-red-500 {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
        
        .error-message {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Focus states for better UX */
        input:focus, select:focus, textarea:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Success state styling */
        .border-green-500 {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        /* Required asterisk styling */
        .text-red-500 {
            color: #ef4444;
            font-weight: bold;
        }
        
        /* Force modals to be on top */
        #addCropModal, #batchUploadModal, #confirmDeleteModal, #editCropModal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            z-index: 99999 !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
        }
        
        #addCropModal.hidden, #batchUploadModal.hidden, #confirmDeleteModal.hidden, #editCropModal.hidden {
            display: none !important;
        }
        
        #addCropModal:not(.hidden), #batchUploadModal:not(.hidden), #confirmDeleteModal:not(.hidden), #editCropModal:not(.hidden) {
            display: block !important;
        }
        
        /* Enhanced Search Styling */
        .search-highlight {
            background-color: #fef08a !important;
            padding: 1px 3px;
            border-radius: 3px;
            font-weight: 600;
        }
        
        .search-active {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }
        
        .search-result-info {
            animation: slideInDown 0.3s ease-out;
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Search hints styling */
        #searchHints {
            animation: fadeIn 0.2s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Improved search input focus state */
        #searchInput:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            transition: all 0.2s ease;
        }
        
        /* Loading state for search */
        .search-loading {
            background-image: url("data:image/svg+xml,%3csvg width='20' height='20' xmlns='http://www.w3.org/2000/svg'%3e%3cg fill='none' fill-rule='evenodd'%3e%3cg fill='%239fa6b2' fill-opacity='0.4'%3e%3cpath d='M10 3v3l4-4-4-4v3a8 8 0 1 0 8 8 1 1 0 0 1 2 0A10 10 0 1 1 10 0z'/%3e%3c/g%3e%3c/g%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 16px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
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
                        <h1 class="text-2xl font-bold text-gray-900">Crop Production Management</h1>
                        <p class="text-sm text-gray-600">Manage crop production data for all farmers</p>
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
                        <form method="GET" action="{{ route('admin.crops.index') }}" class="flex flex-wrap items-center gap-3 flex-1" id="searchForm">
                            <div class="flex-1 min-w-64 relative">
                                <input type="text" 
                                       name="search" 
                                       id="searchInput" 
                                       value="{{ request('search') }}" 
                                       placeholder="Search crops, municipalities, farmers, yields, dates..." 
                                       class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                                       autocomplete="off">
                                <div class="absolute right-2 top-1/2 transform -translate-y-1/2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <div id="searchHints" class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-md shadow-lg z-10 hidden mt-1">
                                    <div class="p-2 text-xs text-gray-600">
                                        <div class="font-medium mb-1">Search in:</div>
                                        <div class="grid grid-cols-2 gap-1 text-gray-500">
                                            <div>• Crop names & varieties</div>
                                            <div>• Municipalities & locations</div>
                                            <div>• Farmer names & contacts</div>
                                            <div>• Production data & yields</div>
                                            <div>• Farm types & categories</div>
                                            <div>• Years & dates</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="min-w-28">
                                <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                                    <option value="">View</option>
                                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Descending</option>
                                </select>
                            </div>
                            <div class="min-w-36 relative">
                                <select name="municipality" 
                                        id="municipalityFilter"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                        onchange="this.form.submit()">
                                    <option value="">Municipality</option>
                                    @if($allMunicipalities->count() > 0)
                                        @foreach($allMunicipalities as $municipality)
                                            <option value="{{ $municipality }}" {{ request('municipality') == $municipality ? 'selected' : '' }}>{{ $municipality }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div id="municipalityHints" class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-md shadow-lg z-10 hidden mt-1">
                                    <div class="p-2 text-xs text-gray-600">
                                        <div class="font-medium mb-1">Searches in:</div>
                                        <div class="text-gray-500 space-y-1">
                                            <div>• Municipality names</div>
                                            <div>• Farmer locations</div>
                                            <div>• Farmer addresses</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="min-w-28 relative">
                                <select name="crop" 
                                        id="cropFilter"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                        onchange="this.form.submit()">
                                    <option value="">Crop</option>
                                    @foreach($allCropNames as $crop)
                                        <option value="{{ $crop }}" {{ request('crop') == $crop ? 'selected' : '' }}>{{ $crop }}</option>
                                    @endforeach
                                </select>
                                <div id="cropHints" class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-md shadow-lg z-10 hidden mt-1">
                                    <div class="p-2 text-xs text-gray-600">
                                        <div class="font-medium mb-1">Searches in:</div>
                                        <div class="text-gray-500 space-y-1">
                                            <div>• Crop names</div>
                                            <div>• Crop varieties</div>
                                            <div>• Categories</div>
                                            <div>• Descriptions</div>
                                        </div>
                                    </div>
                                </div>
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
                        <div class="flex-shrink-0 relative">
                            <!-- Delete Options Dropdown -->
                            <div class="relative inline-block text-left">
                                <button type="button" onclick="toggleDeleteDropdown()" class="px-4 py-2 border border-red-300 text-red-600 hover:bg-red-50 rounded-md text-sm flex items-center space-x-2" id="delete-menu-button">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    <span>Delete Options</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <!-- Dropdown Menu -->
                                <div id="delete-dropdown-menu" class="hidden absolute right-0 z-10 mt-2 w-56 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <div class="py-1">
                                        <button onclick="deleteSelectedItems()" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                            <svg class="mr-3 h-4 w-4 text-red-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Delete Selected
                                            <span class="ml-auto text-xs text-gray-400">(Selected items)</span>
                                        </button>
                                        <button onclick="deleteCurrentPage()" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                            <svg class="mr-3 h-4 w-4 text-orange-400 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Delete Page
                                            <span class="ml-auto text-xs text-gray-400">(Current page)</span>
                                        </button>
                                        <button onclick="deleteAllItems()" class="group flex w-full items-center px-4 py-2 text-sm text-red-700 hover:bg-red-50 hover:text-red-900">
                                            <svg class="mr-3 h-4 w-4 text-red-500 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Delete All
                                            <span class="ml-auto text-xs text-red-400">(All crops)</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
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
                                        <th class="px-4 py-4 text-left text-sm font-medium text-gray-600 cursor-pointer hover:text-gray-800">
                                            Category
                                            <svg class="w-3 h-3 inline ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                            </svg>
                                        </th>
                                        <th class="px-4 py-4 text-left text-sm font-medium text-gray-600 cursor-pointer hover:text-gray-800">
                                            Days to Maturity
                                            <svg class="w-3 h-3 inline ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                            </svg>
                                        </th>
                                        <th class="px-4 py-4 text-left text-sm font-medium text-gray-600 cursor-pointer hover:text-gray-800">
                                            Production Month
                                            <svg class="w-3 h-3 inline ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                            </svg>
                                        </th>
                                        <th class="px-4 py-4 text-left text-sm font-medium text-gray-600 cursor-pointer hover:text-gray-800">
                                            Production Farm Type
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
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ $crop->cropCategory ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ $crop->cropDaysToMaturity ? $crop->cropDaysToMaturity . ' days' : 'N/A' }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ $crop->productionMonth ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700">
                                                {{ $crop->productionFarmType ?? 'N/A' }}
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
                                                            <a href="{{ route('admin.crops.show', $crop->id) }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                <i class="fas fa-eye mr-2"></i>View Details
                                                            </a>
                                                            <button onclick="editCrop({{ $crop->id }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                <i class="fas fa-edit mr-2"></i>Edit
                                                            </button>
                                                            <button onclick="deleteSingleItem({{ $crop->id }})" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                                                <i class="fas fa-trash mr-2"></i>Delete
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="px-6 py-12 text-center text-gray-500">
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
                    <p class="text-sm text-gray-600 mb-4">All required fields are marked with <span class="text-red-500 font-bold">*</span></p>
                    
                    <form id="addCropForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Municipality -->
                            <div>
                                <label for="municipality" class="block text-sm font-medium text-gray-700 mb-1">Municipality<span class="text-red-500">*</span></label>
                                <select id="municipality" name="municipality" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select municipality</option>
                                    @foreach($allMunicipalities as $municipality)
                                        <option value="{{ $municipality }}">{{ $municipality }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Farm Type -->
                            <div>
                                <label for="farm_type" class="block text-sm font-medium text-gray-700 mb-1">Farm Type<span class="text-red-500">*</span></label>
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
                                <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year<span class="text-red-500">*</span></label>
                                <input type="number" id="year" name="year" placeholder="Year" min="2000" max="2030" value="{{ date('Y') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Crop -->
                            <div>
                                <label for="crop_name" class="block text-sm font-medium text-gray-700 mb-1">Crop<span class="text-red-500">*</span></label>
                                <select id="crop_name" name="crop_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select highland crop</option>
                                    @foreach($allCropNames as $crop)
                                        <option value="{{ $crop }}">{{ $crop }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Area Planted -->
                            <div>
                                <label for="area_planted" class="block text-sm font-medium text-gray-700 mb-1">Area Planted (ha)<span class="text-red-500">*</span></label>
                                <input type="number" id="area_planted" name="area_planted" placeholder="Enter a number" min="0" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Area Harvested -->
                            <div>
                                <label for="area_harvested" class="block text-sm font-medium text-gray-700 mb-1">Area Harvested (ha)<span class="text-red-500">*</span></label>
                                <input type="number" id="area_harvested" name="area_harvested" placeholder="Enter a number" min="0" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <div id="area_validation_error" class="text-red-500 text-xs mt-1 hidden">Area harvested cannot be greater than area planted.</div>
                            </div>

                            <!-- Production -->
                            <div>
                                <label for="production" class="block text-sm font-medium text-gray-700 mb-1">Production (mt)<span class="text-red-500">*</span></label>
                                <input type="number" id="production" name="production" placeholder="Enter a number" min="0" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Productivity -->
                            <div>
                                <label for="productivity" class="block text-sm font-medium text-gray-700 mb-1">Productivity (mt/ha)</label>
                                <input type="number" id="productivity" name="productivity" placeholder="Enter a number" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Crop ID -->
                            <div>
                                <label for="cropID" class="block text-sm font-medium text-gray-700 mb-1">Crop ID</label>
                                <input type="text" id="cropID" name="cropID" placeholder="Enter crop identifier (optional)" maxlength="50" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Crop Category -->
                            <div>
                                <label for="cropCategory" class="block text-sm font-medium text-gray-700 mb-1">Crop Category</label>
                                <select id="cropCategory" name="cropCategory" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select category (auto-filled based on crop)</option>
                                    @foreach($allCropCategories as $category)
                                        <option value="{{ $category }}">{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Days to Maturity -->
                            <div>
                                <label for="cropDaysToMaturity" class="block text-sm font-medium text-gray-700 mb-1">Days to Maturity</label>
                                <input type="number" id="cropDaysToMaturity" name="cropDaysToMaturity" placeholder="Days (auto-filled based on crop)" min="1" max="365" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Production Month -->
                            <div>
                                <label for="productionMonth" class="block text-sm font-medium text-gray-700 mb-1">Production Month</label>
                                <select id="productionMonth" name="productionMonth" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select month</option>
                                    @foreach($allProductionMonths as $month)
                                        <option value="{{ $month }}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Production Farm Type -->
                            <div>
                                <label for="productionFarmType" class="block text-sm font-medium text-gray-700 mb-1">Production Farm Type</label>
                                <select id="productionFarmType" name="productionFarmType" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select farm type</option>
                                    @foreach($allProductionFarmTypes as $farmType)
                                        <option value="{{ $farmType }}">{{ $farmType }}</option>
                                    @endforeach
                                </select>
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload File (CSV/Excel)<span class="text-red-500">*</span></label>
                            
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

    <!-- Delete Selected Items Modal -->
    <div id="confirmDeleteModal" class="fixed inset-0 overflow-y-auto h-full w-full hidden modal-overlay" style="z-index: 99999; background-color: rgba(0, 0, 0, 0.6);">
        <div class="relative top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-11/12 max-w-lg modal-content bg-white rounded-xl" style="z-index: 100000;">
            <div class="p-8">
                <!-- Modal Header -->
                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                        <svg class="h-8 w-8 text-red-600 delete-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Selected Items</h3>
                    <div class="text-sm text-gray-600">
                        <span id="delete-count-text">You are about to delete the selected crop records.</span>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="text-center mb-8">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-center">
                            <svg class="h-5 w-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium text-yellow-800">This action cannot be undone</span>
                        </div>
                    </div>
                    <p class="text-gray-600">The selected crop records will be permanently removed from the database. Make sure you have backed up any important data.</p>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-center space-x-4">
                    <button onclick="closeConfirmDeleteModal()" class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-all duration-200 focus:ring-4 focus:ring-gray-300">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </span>
                    </button>
                    <button onclick="confirmDelete()" class="px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg delete-btn transition-all duration-200 focus:ring-4 focus:ring-red-300">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Items
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete All Items Modal -->
    <div id="confirmDeleteAllModal" class="fixed inset-0 overflow-y-auto h-full w-full hidden modal-overlay" style="z-index: 99999; background-color: rgba(0, 0, 0, 0.6);">
        <div class="relative top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-11/12 max-w-lg modal-content bg-white rounded-xl" style="z-index: 100000;">
            <div class="p-8">
                <!-- Modal Header -->
                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                        <svg class="h-8 w-8 text-red-600 danger-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-red-600 mb-2">⚠️ DANGER ZONE ⚠️</h3>
                    <div class="text-lg font-semibold text-gray-900">
                        Delete ALL Crop Records
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="text-center mb-8">
                    <div class="bg-red-50 border-2 border-red-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center justify-center mb-3">
                            <svg class="h-6 w-6 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-lg font-bold text-red-800">CRITICAL WARNING</span>
                        </div>
                        <p class="text-sm text-red-700 font-medium">This will permanently delete <strong>ALL</strong> crop records from the entire database!</p>
                    </div>
                    <div class="space-y-3 text-sm text-gray-600">
                        <p class="flex items-center justify-center">
                            <span class="text-red-500 mr-2">✗</span>
                            All crop production data will be lost forever
                        </p>
                        <p class="flex items-center justify-center">
                            <span class="text-red-500 mr-2">✗</span>
                            This action cannot be undone or recovered
                        </p>
                        <p class="flex items-center justify-center">
                            <span class="text-red-500 mr-2">✗</span>
                            All reports and analytics will be reset
                        </p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-center space-x-4">
                    <button onclick="closeConfirmDeleteAllModal()" class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-all duration-200 focus:ring-4 focus:ring-gray-300">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Keep Data Safe
                        </span>
                    </button>
                    <button onclick="showFinalConfirmation()" class="px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg delete-btn transition-all duration-200 focus:ring-4 focus:ring-red-300">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            I Understand, Continue
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Current Page Modal -->
    <div id="confirmDeletePageModal" class="fixed inset-0 overflow-y-auto h-full w-full hidden modal-overlay" style="z-index: 99999; background-color: rgba(0, 0, 0, 0.6);">
        <div class="relative top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-11/12 max-w-lg modal-content bg-white rounded-xl" style="z-index: 100000;">
            <div class="p-8">
                <!-- Modal Header -->
                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-orange-100 mb-4">
                        <svg class="h-8 w-8 text-orange-600 warning-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Current Page</h3>
                    <div class="text-sm text-gray-600">
                        <span id="page-info-text">Delete all items on the current page</span>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="text-center mb-8">
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="h-5 w-5 text-orange-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium text-orange-800">Page-level deletion</span>
                        </div>
                        <p class="text-sm text-orange-700" id="delete-page-count">This will delete all items currently visible on this page</p>
                    </div>
                    <p class="text-gray-600">Items on other pages will remain untouched. This action cannot be undone.</p>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-center space-x-4">
                    <button onclick="closeConfirmDeletePageModal()" class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-all duration-200 focus:ring-4 focus:ring-gray-300">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </span>
                    </button>
                    <button onclick="confirmDeletePage()" class="px-8 py-3 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg delete-btn transition-all duration-200 focus:ring-4 focus:ring-orange-300">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Page
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Crop Modal -->
    <div id="editCropModal" class="fixed inset-0 overflow-y-auto h-full w-full hidden" style="z-index: 99999; background-color: rgba(0, 0, 0, 0.5);">
        <div class="relative top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-11/12 max-w-2xl shadow-lg rounded-md bg-white" style="z-index: 100000;">
            <div class="p-6">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Crop</h3>
                    <button onclick="closeEditCropModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form id="editCropForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Municipality -->
                        <div>
                            <label for="edit_municipality" class="block text-sm font-medium text-gray-700 mb-1">Municipality<span class="text-red-500">*</span></label>
                            <select id="edit_municipality" name="municipality" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select municipality</option>
                                @foreach($allMunicipalities as $municipality)
                                    <option value="{{ $municipality }}">{{ $municipality }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Farm Type -->
                        <div>
                            <label for="edit_farm_type" class="block text-sm font-medium text-gray-700 mb-1">Farm Type<span class="text-red-500">*</span></label>
                            <select id="edit_farm_type" name="farm_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select farm type</option>
                                <option value="irrigated">Irrigated</option>
                                <option value="rainfed">Rainfed</option>
                                <option value="upland">Upland</option>
                                <option value="lowland">Lowland</option>
                            </select>
                        </div>

                        <!-- Year -->
                        <div>
                            <label for="edit_year" class="block text-sm font-medium text-gray-700 mb-1">Year<span class="text-red-500">*</span></label>
                            <input type="number" id="edit_year" name="year" min="2000" max="2030" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Crop -->
                        <div>
                            <label for="edit_crop_name" class="block text-sm font-medium text-gray-700 mb-1">Crop<span class="text-red-500">*</span></label>
                            <select id="edit_crop_name" name="crop_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select highland crop</option>
                                @foreach($allCropNames as $crop)
                                    <option value="{{ $crop }}">{{ $crop }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Area Planted -->
                        <div>
                            <label for="edit_area_planted" class="block text-sm font-medium text-gray-700 mb-1">Area Planted (ha)<span class="text-red-500">*</span></label>
                            <input type="number" id="edit_area_planted" name="area_planted" min="0" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Area Harvested -->
                        <div>
                            <label for="edit_area_harvested" class="block text-sm font-medium text-gray-700 mb-1">Area Harvested (ha)<span class="text-red-500">*</span></label>
                            <input type="number" id="edit_area_harvested" name="area_harvested" min="0" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <div id="edit_area_validation_error" class="text-red-500 text-xs mt-1 hidden">Area harvested cannot be greater than area planted.</div>
                        </div>

                        <!-- Production -->
                        <div>
                            <label for="edit_production" class="block text-sm font-medium text-gray-700 mb-1">Production (mt)<span class="text-red-500">*</span></label>
                            <input type="number" id="edit_production" name="production" min="0" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Productivity -->
                        <div>
                            <label for="edit_productivity" class="block text-sm font-medium text-gray-700 mb-1">Productivity (mt/ha)</label>
                            <input type="number" id="edit_productivity" name="productivity" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Crop ID -->
                        <div>
                            <label for="edit_cropID" class="block text-sm font-medium text-gray-700 mb-1">Crop ID</label>
                            <input type="text" id="edit_cropID" name="cropID" placeholder="Crop identifier (optional)" maxlength="50" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Crop Category -->
                        <div>
                            <label for="edit_cropCategory" class="block text-sm font-medium text-gray-700 mb-1">Crop Category</label>
                            <select id="edit_cropCategory" name="cropCategory" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select category</option>
                                @foreach($allCropCategories as $category)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Days to Maturity -->
                        <div>
                            <label for="edit_cropDaysToMaturity" class="block text-sm font-medium text-gray-700 mb-1">Days to Maturity</label>
                            <input type="number" id="edit_cropDaysToMaturity" name="cropDaysToMaturity" placeholder="Days to maturity" min="1" max="365" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Production Month -->
                        <div>
                            <label for="edit_productionMonth" class="block text-sm font-medium text-gray-700 mb-1">Production Month</label>
                            <select id="edit_productionMonth" name="productionMonth" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select month</option>
                                @foreach($allProductionMonths as $month)
                                    <option value="{{ $month }}">{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Production Farm Type -->
                        <div>
                            <label for="edit_productionFarmType" class="block text-sm font-medium text-gray-700 mb-1">Production Farm Type</label>
                            <select id="edit_productionFarmType" name="productionFarmType" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select farm type</option>
                                @foreach($allProductionFarmTypes as $farmType)
                                    <option value="{{ $farmType }}">{{ $farmType }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeEditCropModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                            Update Crop
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        console.log('Crop production management JavaScript loaded');
        
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

        function closeEditCropModal() {
            const modal = document.getElementById('editCropModal');
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.getElementById('editCropForm').reset();
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
            
            // Update modal content with selection information
            const countText = document.getElementById('delete-count-text');
            if (countText) {
                const itemCount = cropIds.length;
                if (itemCount === 1) {
                    countText.textContent = 'You are about to delete 1 selected crop record.';
                } else {
                    countText.textContent = `You are about to delete ${itemCount} selected crop records.`;
                }
            }
            
            const modal = document.getElementById('confirmDeleteModal');
            modal.classList.remove('hidden');
            modal.style.display = 'block';
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100%';
            modal.style.height = '100%';
            modal.style.zIndex = '99999';
            modal.style.backgroundColor = 'rgba(0, 0, 0, 0.6)';
            document.body.style.overflow = 'hidden';
        }

        function closeConfirmDeleteModal() {
            const modal = document.getElementById('confirmDeleteModal');
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            itemsToDelete = [];
        }

        // Delete All Modal Functions
        function openConfirmDeleteAllModal() {
            const modal = document.getElementById('confirmDeleteAllModal');
            modal.classList.remove('hidden');
            modal.style.display = 'block';
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100%';
            modal.style.height = '100%';
            modal.style.zIndex = '99999';
            modal.style.backgroundColor = 'rgba(0, 0, 0, 0.6)';
            document.body.style.overflow = 'hidden';
        }

        function closeConfirmDeleteAllModal() {
            const modal = document.getElementById('confirmDeleteAllModal');
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function showFinalConfirmation() {
            closeConfirmDeleteAllModal();
            
            // Create a final confirmation modal overlay
            const finalModal = document.createElement('div');
            finalModal.className = 'fixed inset-0 z-50 bg-black bg-opacity-75 flex items-center justify-center';
            finalModal.innerHTML = `
                <div class="bg-white rounded-xl p-8 max-w-md mx-4 text-center">
                    <div class="mb-6">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                            <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-red-600 mb-2">🚨 FINAL CONFIRMATION 🚨</h3>
                        <p class="text-gray-700 mb-4">This is your last chance to cancel.</p>
                        <p class="text-sm text-red-600 font-bold">Are you 100% certain you want to delete ALL crops?</p>
                    </div>
                    <div class="flex justify-center space-x-4">
                        <button onclick="closeFinalConfirmation()" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg">
                            Cancel & Keep Data
                        </button>
                        <button onclick="proceedWithDeleteAll()" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg">
                            Yes, Delete Everything
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(finalModal);
            document.body.style.overflow = 'hidden';
        }

        function closeFinalConfirmation() {
            const finalModal = document.querySelector('.fixed.inset-0.z-50');
            if (finalModal) {
                finalModal.remove();
            }
            document.body.style.overflow = 'auto';
        }

        function proceedWithDeleteAll() {
            closeFinalConfirmation();
            performDeleteAll();
        }

        // Delete Page Modal Functions
        function openConfirmDeletePageModal() {
            const modal = document.getElementById('confirmDeletePageModal');
            modal.classList.remove('hidden');
            modal.style.display = 'block';
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100%';
            modal.style.height = '100%';
            modal.style.zIndex = '99999';
            modal.style.backgroundColor = 'rgba(0, 0, 0, 0.6)';
            document.body.style.overflow = 'hidden';
        }

        function closeConfirmDeletePageModal() {
            const modal = document.getElementById('confirmDeletePageModal');
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function confirmDeletePage() {
            closeConfirmDeletePageModal();
            
            const currentPage = {{ $crops->currentPage() }};
            const perPage = {{ $crops->perPage() }};
            
            performDeletePage(currentPage, perPage);
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
                    
                    // Ensure itemsToDelete is an array
                    const idsArray = Array.isArray(itemsToDelete) ? itemsToDelete : [itemsToDelete];
                    console.log('Processing IDs array:', idsArray);
                    
                    const archivedCount = data.debug && data.debug.archived_count ? data.debug.archived_count : idsArray.length;
                    showSuccessMessage(data.message || `Successfully archived ${archivedCount} item(s)!`);
                    closeConfirmDeleteModal();
                    
                    // Remove deleted rows from table immediately
                    console.log('Removing rows for IDs:', itemsToDelete);
                    console.log('Items to delete array:', JSON.stringify(itemsToDelete));
                    let removedCount = 0;
                    
                    idsArray.forEach(cropId => {
                        const cropIdStr = String(cropId);
                        console.log(`Looking for row with data-crop-id="${cropIdStr}"`);
                        
                        // Find the row using data-crop-id attribute
                        let row = document.querySelector(`tr[data-crop-id="${cropIdStr}"]`);
                        console.log(`Found row with selector:`, row);
                        
                        if (!row) {
                            // Alternative: look through all rows manually
                            const allRows = document.querySelectorAll('.crop-row');
                            console.log(`Searching through ${allRows.length} rows manually`);
                            for (let r of allRows) {
                                const rowId = r.getAttribute('data-crop-id');
                                console.log(`Checking row with data-crop-id="${rowId}"`);
                                if (rowId === cropIdStr) {
                                    row = r;
                                    console.log(`Found matching row manually:`, row);
                                    break;
                                }
                            }
                        }
                        
                        if (row) {
                            console.log(`Removing row for crop ID: ${cropIdStr}`, row);
                            row.style.transition = 'opacity 0.3s ease';
                            row.style.opacity = '0.3';
                            row.style.backgroundColor = '#fee2e2'; // Light red background
                            
                            setTimeout(() => {
                                console.log(`Actually removing row ${cropIdStr} from DOM`);
                                row.remove();
                                removedCount++;
                                console.log(`Row ${cropIdStr} removed successfully`);
                                
                                // Update counts after each removal
                                updateTableCounts();
                                
                                // Check if we've removed all expected rows  
                                if (removedCount === idsArray.length) {
                                    checkEmptyTableState();
                                }
                            }, 300);
                        } else {
                            console.error(`Could not find row for crop ID: ${cropIdStr}`);
                            console.log('Available rows with data-crop-id:');
                            document.querySelectorAll('[data-crop-id]').forEach(r => {
                                console.log(`- data-crop-id="${r.getAttribute('data-crop-id')}"`);
                            });
                            removedCount++; // Count as processed even if not found
                            if (removedCount === idsArray.length) {
                                checkEmptyTableState();
                            }
                        }
                    });

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
                                    <td colspan="11" class="px-6 py-12 text-center">
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
                        throw new Error(data.message || 'Failed to delete items');
                    }
                }
            })
            .catch(error => {
                console.error('Delete request failed:', error);
                showErrorMessage(error.message || 'Failed to delete items. Please try again.');
                closeConfirmDeleteModal();
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
            
            // Fetch crop data and populate edit form
            fetch(`/admin/crops/${cropId}`)
                .then(response => response.json())
                .catch(error => {
                    console.error('Error fetching crop data:', error);
                    // If fetch fails, try to get data from table row
                    const row = document.querySelector(`tr[data-crop-id="${cropId}"]`);
                    if (row) {
                        const cells = row.querySelectorAll('td');
                        return {
                            id: cropId,
                            municipality: cells[1]?.textContent?.trim() || '',
                            crop_name: cells[2]?.textContent?.trim() || '',
                            farm_type: 'upland', // default
                            year: cells[3]?.textContent?.trim() || new Date().getFullYear(),
                            area_planted: cells[4]?.textContent?.trim() || '',
                            area_harvested: cells[5]?.textContent?.trim() || '',
                            production_mt: cells[6]?.textContent?.trim() || '',
                            productivity_mt_ha: cells[7]?.textContent?.trim() || ''
                        };
                    }
                    throw error;
                })
                .then(crop => {
                    // Populate form fields
                    document.getElementById('edit_municipality').value = crop.municipality || '';
                    document.getElementById('edit_farm_type').value = crop.farm_type || 'upland';
                    document.getElementById('edit_year').value = crop.year || new Date().getFullYear();
                    document.getElementById('edit_crop_name').value = crop.crop_name || '';
                    document.getElementById('edit_area_planted').value = crop.area_planted || '';
                    document.getElementById('edit_area_harvested').value = crop.area_harvested || '';
                    document.getElementById('edit_production').value = crop.production_mt || '';
                    document.getElementById('edit_productivity').value = crop.productivity_mt_ha || '';
                    document.getElementById('edit_cropID').value = crop.cropID || '';
                    document.getElementById('edit_cropCategory').value = crop.cropCategory || '';
                    document.getElementById('edit_cropDaysToMaturity').value = crop.cropDaysToMaturity || '';
                    document.getElementById('edit_productionMonth').value = crop.productionMonth || '';
                    document.getElementById('edit_productionFarmType').value = crop.productionFarmType || '';
                    
                    // Set form action
                    document.getElementById('editCropForm').action = `/admin/crops/${cropId}`;
                    
                    // Show modal
                    document.getElementById('editCropModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error loading crop data:', error);
                    alert('Error loading crop data. Please try again.');
                });
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
            console.log('Found checked boxes:', checkedBoxes.length);
            
            const selectedIds = Array.from(checkedBoxes).map(checkbox => {
                const row = checkbox.closest('tr');
                const cropId = row ? row.getAttribute('data-crop-id') : null;
                console.log('Checkbox row crop ID:', cropId, 'Row element:', row); // Debug log
                return cropId;
            }).filter(id => id); // Remove any null/undefined IDs
            
            console.log('Selected IDs for deletion:', selectedIds); // Debug log

            if (selectedIds.length === 0) {
                showErrorMessage('Please select items to delete.');
                return;
            }

            openConfirmDeleteModal(selectedIds);
        }

        // Function to toggle delete dropdown menu
        function toggleDeleteDropdown() {
            const dropdown = document.getElementById('delete-dropdown-menu');
            dropdown.classList.toggle('hidden');
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function closeDropdown(e) {
                const button = document.getElementById('delete-menu-button');
                if (!button.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                    document.removeEventListener('click', closeDropdown);
                }
            });
        }

        // Function to delete all items
        function deleteAllItems() {
            // Hide dropdown
            document.getElementById('delete-dropdown-menu').classList.add('hidden');
            
            // Show confirmation modal
            openConfirmDeleteAllModal();
        }

        // Function to delete current page items
        function deleteCurrentPage() {
            // Hide dropdown
            document.getElementById('delete-dropdown-menu').classList.add('hidden');
            
            const currentPage = {{ $crops->currentPage() }};
            const perPage = {{ $crops->perPage() }};
            const itemsOnPage = {{ $crops->count() }};
            
            if (itemsOnPage === 0) {
                showErrorMessage('No items found on current page to delete.');
                return;
            }
            
            // Update modal content with page-specific information
            document.getElementById('page-info-text').textContent = `Delete all ${itemsOnPage} items on page ${currentPage}`;
            document.getElementById('delete-page-count').textContent = `This will delete ${itemsOnPage} crop records from page ${currentPage}`;
            
            // Show confirmation modal
            openConfirmDeletePageModal();
        }

        // Perform delete all operation
        function performDeleteAll() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                showErrorMessage('Security token not found. Please refresh the page.');
                return;
            }

            // Show loading state
            showSuccessMessage('Deleting all crops... Please wait.');

            fetch('{{ route("admin.crops.delete-all") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessMessage(data.message);
                    // Reload page after short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showErrorMessage(data.message || 'Failed to delete all crops.');
                }
            })
            .catch(error => {
                console.error('Delete all error:', error);
                showErrorMessage('An error occurred while deleting all crops.');
            });
        }

        // Perform delete page operation
        function performDeletePage(page, perPage) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                showErrorMessage('Security token not found. Please refresh the page.');
                return;
            }

            // Show loading state
            showSuccessMessage(`Deleting crops from page ${page}... Please wait.`);

            fetch('{{ route("admin.crops.delete-page") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    page: page,
                    per_page: perPage
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessMessage(data.message);
                    // Reload page after short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showErrorMessage(data.message || 'Failed to delete crops from this page.');
                }
            })
            .catch(error => {
                console.error('Delete page error:', error);
                showErrorMessage('An error occurred while deleting crops from this page.');
            });
        }

        // Area validation helper function
        function validateAreaFields(areaPlanted, areaHarvested) {
            const planted = parseFloat(areaPlanted);
            const harvested = parseFloat(areaHarvested);
            
            if (isNaN(planted) || isNaN(harvested)) {
                return { valid: true }; // Skip validation if values are not numbers
            }
            
            if (harvested > planted) {
                return {
                    valid: false,
                    message: 'Area harvested cannot be greater than area planted.'
                };
            }
            
            return { valid: true };
        }

        // AJAX form submission for Add Crop
        const addCropForm = document.getElementById('addCropForm');
        if (addCropForm) {
            addCropForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate area fields before submission
                const areaPlanted = this.querySelector('#area_planted').value;
                const areaHarvested = this.querySelector('#area_harvested').value;
                const validation = validateAreaFields(areaPlanted, areaHarvested);
                
                if (!validation.valid) {
                    showErrorMessage(validation.message);
                    return;
                }
                
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

        // AJAX form submission for Edit Crop
        const editCropForm = document.getElementById('editCropForm');
        if (editCropForm) {
            editCropForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Edit crop form submitted!');
                
                // Validate area fields before submission
                const areaPlanted = this.querySelector('#edit_area_planted').value;
                const areaHarvested = this.querySelector('#edit_area_harvested').value;
                const validation = validateAreaFields(areaPlanted, areaHarvested);
                
                if (!validation.valid) {
                    showErrorMessage(validation.message);
                    return;
                }
                
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                
                // Disable submit button and show loading state
                submitBtn.disabled = true;
                submitBtn.textContent = 'Updating...';
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    console.log('Edit crop response status:', response.status);
                    console.log('Edit crop response headers:', [...response.headers.entries()]);
                    
                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('Edit crop error response:', text.substring(0, 1000));
                            try {
                                const data = JSON.parse(text);
                                throw new Error(data.message || 'Server error');
                            } catch (parseError) {
                                console.error('Edit crop JSON parse error:', parseError);
                                throw new Error(`Server returned HTML error. Status: ${response.status}`);
                            }
                        });
                    }
                    
                    // Check if response is actually JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        return response.text().then(text => {
                            console.error('Edit crop non-JSON response:', text.substring(0, 1000));
                            throw new Error('Server returned non-JSON response');
                        });
                    }
                    
                    return response.json();
                })
                .then(data => {
                    console.log('Edit crop response data:', data);
                    if (data.success) {
                        showSuccessMessage('Crop updated successfully!');
                        closeEditCropModal();
                        
                        // Update the table row with new data
                        const crop = data.crop;
                        const row = document.querySelector(`tr[data-crop-id="${crop.id}"]`);
                        if (row) {
                            updateTableRow(row, crop);
                        }
                        
                        // Refresh the page after a short delay to ensure data consistency
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        throw new Error(data.message || 'Failed to update crop');
                    }
                })
                .catch(error => {
                    console.error('Edit crop error:', error);
                    showErrorMessage(error.message || 'Failed to update crop. Please try again.');
                })
                .finally(() => {
                    // Restore button state
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                });
            });
        }

        // Function to update table row with new crop data
        function updateTableRow(row, crop) {
            const cells = row.querySelectorAll('td');
            if (cells.length >= 11) {
                cells[1].textContent = crop.crop_name || '';
                cells[2].textContent = crop.municipality || '';
                cells[3].textContent = crop.year || '';
                cells[4].textContent = crop.area_planted ? parseFloat(crop.area_planted).toFixed(1) : 'N/A';
                cells[5].textContent = crop.productivity_mt_ha ? parseFloat(crop.productivity_mt_ha).toFixed(4) : 'N/A';
                cells[6].textContent = crop.cropCategory || 'N/A';
                cells[7].textContent = crop.cropDaysToMaturity ? crop.cropDaysToMaturity + ' days' : 'N/A';
                cells[8].textContent = crop.productionMonth || 'N/A';
                cells[9].textContent = crop.productionFarmType || 'N/A';
            }
        }

        // Function to add new crop row to table dynamically
        function addCropToTable(crop) {
            const tableBody = document.getElementById('cropTableBody');
            
            // Remove empty state if it exists
            const emptyRow = tableBody.querySelector('tr td[colspan="11"]');
            if (emptyRow) {
                emptyRow.closest('tr').remove();
            }
            
            // Create new row
            const newRow = document.createElement('tr');
            newRow.className = 'border-b border-gray-100 hover:bg-gray-50 crop-row';
            newRow.setAttribute('data-crop-id', crop.id); // CRITICAL: Add the crop ID for deletion
            newRow.setAttribute('data-crop', (crop.crop_name || crop.name || '').toLowerCase());
            newRow.setAttribute('data-municipality', (crop.municipality || '').toLowerCase());
            newRow.setAttribute('data-year', crop.year || '');
            
            newRow.innerHTML = `
                <td class="px-4 py-4">
                    <input type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 row-checkbox">
                </td>
                <td class="px-4 py-4 text-sm font-medium text-gray-900">
                    ${crop.crop_name || crop.name || 'N/A'}
                </td>
                <td class="px-4 py-4 text-sm text-gray-700">
                    ${crop.municipality || 'N/A'}
                </td>
                <td class="px-4 py-4 text-sm text-gray-700">
                    ${crop.year || 'N/A'}
                </td>
                <td class="px-4 py-4 text-sm text-gray-700">
                    ${crop.area_planted ? parseFloat(crop.area_planted).toFixed(1) : (crop.area_hectares ? parseFloat(crop.area_hectares).toFixed(1) : 'N/A')}
                </td>
                <td class="px-4 py-4 text-sm text-gray-700">
                    ${crop.productivity_mt_ha ? parseFloat(crop.productivity_mt_ha).toFixed(4) : 'N/A'}
                </td>
                <td class="px-4 py-4 text-sm text-gray-700">
                    ${crop.cropCategory || 'N/A'}
                </td>
                <td class="px-4 py-4 text-sm text-gray-700">
                    ${crop.cropDaysToMaturity ? crop.cropDaysToMaturity + ' days' : 'N/A'}
                </td>
                <td class="px-4 py-4 text-sm text-gray-700">
                    ${crop.productionMonth || 'N/A'}
                </td>
                <td class="px-4 py-4 text-sm text-gray-700">
                    ${crop.productionFarmType || 'N/A'}
                </td>
                <td class="px-4 py-4 text-right">
                    <div class="relative inline-block">
                        <button onclick="toggleDropdown(${crop.id})" class="p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" title="More actions">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                            </svg>
                        </button>
                        <div id="dropdown-${crop.id}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                            <div class="py-1">
                                <a href="/admin/crops/${crop.id}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-eye mr-2"></i>View Details
                                </a>
                                <button onclick="editCrop(${crop.id})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </button>
                                <button onclick="deleteSingleItem(${crop.id})" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-trash mr-2"></i>Delete
                                </button>
                            </div>
                        </div>
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
        
        // Helper functions for delete operations
        function updateTableCounts() {
            updateTotalCount();
            updateSelectedCount();
        }
        
        function checkEmptyTableState() {
            setTimeout(() => {
                const tableBody = document.getElementById('cropTableBody');
                const remainingRows = tableBody.querySelectorAll('.crop-row');
                
                if (remainingRows.length === 0) {
                    // Add empty state
                    const emptyRow = document.createElement('tr');
                    emptyRow.innerHTML = `
                        <td colspan="11" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <div class="text-lg font-medium mb-2">All items deleted</div>
                                <p class="text-gray-500 mb-4">The page will refresh to show updated data</p>
                                <button onclick="window.location.reload()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Refresh Page
                                </button>
                            </div>
                        </td>
                    `;
                    tableBody.appendChild(emptyRow);
                    
                    // Also disable the select all checkbox since there are no items
                    const selectAllCheckbox = document.getElementById('selectAll');
                    if (selectAllCheckbox) {
                        selectAllCheckbox.disabled = true;
                        selectAllCheckbox.checked = false;
                    }
                }
            }, 400);
        }

        // Form validation for Add Crop Form
        document.getElementById('addCropForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            clearFormErrors('addCropForm');
            
            let hasErrors = false;
            const form = this;
            
            // Required field validation
            const requiredFields = [
                { name: 'municipality', label: 'Municipality' },
                { name: 'farm_type', label: 'Farm Type' },
                { name: 'year', label: 'Year' },
                { name: 'crop_name', label: 'Crop' },
                { name: 'area_planted', label: 'Area Planted' },
                { name: 'area_harvested', label: 'Area Harvested' },
                { name: 'production', label: 'Production' }
            ];
            
            requiredFields.forEach(field => {
                const input = form.querySelector(`[name="${field.name}"]`);
                if (!input.value.trim()) {
                    showFieldError(input, `${field.label} is required`);
                    hasErrors = true;
                }
            });
            
            // Numeric field validation
            const numericFields = [
                { name: 'year', label: 'Year', min: 2000, max: 2030 },
                { name: 'area_planted', label: 'Area Planted', min: 0 },
                { name: 'area_harvested', label: 'Area Harvested', min: 0 },
                { name: 'production', label: 'Production', min: 0 },
                { name: 'productivity', label: 'Productivity', min: 0, required: false }
            ];
            
            numericFields.forEach(field => {
                const input = form.querySelector(`[name="${field.name}"]`);
                const value = parseFloat(input.value);
                
                if (input.value.trim() && (isNaN(value) || value < field.min)) {
                    showFieldError(input, `${field.label} must be a valid number${field.min > 0 ? ` greater than ${field.min}` : ''}`);
                    hasErrors = true;
                }
                
                if (field.max && value > field.max) {
                    showFieldError(input, `${field.label} cannot exceed ${field.max}`);
                    hasErrors = true;
                }
            });
            
            // Area validation: area_harvested should not exceed area_planted
            const areaPlanted = parseFloat(form.querySelector('[name="area_planted"]').value);
            const areaHarvested = parseFloat(form.querySelector('[name="area_harvested"]').value);
            
            if (!isNaN(areaPlanted) && !isNaN(areaHarvested) && areaHarvested > areaPlanted) {
                showFieldError(form.querySelector('[name="area_harvested"]'), 'Area harvested cannot exceed area planted');
                hasErrors = true;
            }
            
            if (!hasErrors) {
                // Submit form via AJAX (existing functionality)
                addNewCrop();
            }
        });

        // Form validation for Batch Upload Form
        document.getElementById('batchUploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            clearFormErrors('batchUploadForm');
            
            let hasErrors = false;
            const fileInput = this.querySelector('[name="csv_file"]');
            
            if (!fileInput.files || fileInput.files.length === 0) {
                showFieldError(fileInput.parentElement, 'Please select a file to upload');
                hasErrors = true;
            } else {
                const file = fileInput.files[0];
                const allowedTypes = ['text/csv', 'application/csv', 'text/plain', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
                const allowedExtensions = ['.csv', '.txt', '.xlsx', '.xls'];
                
                const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                
                if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
                    showFieldError(fileInput.parentElement, 'Please select a valid CSV or Excel file');
                    hasErrors = true;
                }
                
                // File size validation (max 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    showFieldError(fileInput.parentElement, 'File size cannot exceed 10MB');
                    hasErrors = true;
                }
            }
            
            if (!hasErrors) {
                // Submit form (existing functionality)
                this.submit();
            }
        });

        // Utility functions for form validation
        function showFieldError(field, message) {
            field.classList.add('border-red-500');
            
            // Remove existing error message
            const existingError = field.parentElement.querySelector('.error-message');
            if (existingError) existingError.remove();
            
            // Add new error message
            const errorDiv = document.createElement('p');
            errorDiv.className = 'mt-1 text-sm text-red-600 error-message';
            errorDiv.textContent = message;
            field.parentElement.appendChild(errorDiv);
        }

        function clearFieldError(field) {
            field.classList.remove('border-red-500');
            const errorMsg = field.parentElement.querySelector('.error-message');
            if (errorMsg) errorMsg.remove();
        }

        function clearFormErrors(formId) {
            const form = document.getElementById(formId);
            const fields = form.querySelectorAll('input, select, textarea');
            fields.forEach(field => clearFieldError(field));
        }

        // Real-time validation for crop forms
        document.addEventListener('input', function(e) {
            if (e.target.matches('#addCropForm input[required], #addCropForm select[required]')) {
                if (e.target.value.trim()) {
                    clearFieldError(e.target);
                }
            }
            
            // Real-time area validation
            if (e.target.name === 'area_planted' || e.target.name === 'area_harvested') {
                const form = e.target.closest('form');
                const areaPlanted = parseFloat(form.querySelector('[name="area_planted"]').value);
                const areaHarvested = parseFloat(form.querySelector('[name="area_harvested"]').value);
                
                if (!isNaN(areaPlanted) && !isNaN(areaHarvested) && areaHarvested <= areaPlanted) {
                    clearFieldError(form.querySelector('[name="area_harvested"]'));
                }
            }
        });

        document.addEventListener('blur', function(e) {
            if (e.target.matches('#addCropForm input[required], #addCropForm select[required]')) {
                if (!e.target.value.trim()) {
                    const label = e.target.parentElement.querySelector('label').textContent.replace('*', '').trim();
                    showFieldError(e.target, `${label} is required`);
                }
            }
        }, true);

        // Real-time area validation for Add Crop form
        function setupAreaValidation(areaPlantedId, areaHarvestedId, errorId) {
            const areaPlantedField = document.getElementById(areaPlantedId);
            const areaHarvestedField = document.getElementById(areaHarvestedId);
            const errorDiv = document.getElementById(errorId);
            
            function validateArea() {
                const planted = parseFloat(areaPlantedField.value);
                const harvested = parseFloat(areaHarvestedField.value);
                
                if (!isNaN(planted) && !isNaN(harvested) && harvested > planted) {
                    errorDiv.classList.remove('hidden');
                    areaHarvestedField.classList.add('border-red-500');
                    return false;
                } else {
                    errorDiv.classList.add('hidden');
                    areaHarvestedField.classList.remove('border-red-500');
                    return true;
                }
            }
            
            if (areaPlantedField && areaHarvestedField && errorDiv) {
                areaPlantedField.addEventListener('input', validateArea);
                areaHarvestedField.addEventListener('input', validateArea);
                areaPlantedField.addEventListener('blur', validateArea);
                areaHarvestedField.addEventListener('blur', validateArea);
            }
        }
        
        // Setup validation for both forms
        setupAreaValidation('area_planted', 'area_harvested', 'area_validation_error');
        setupAreaValidation('edit_area_planted', 'edit_area_harvested', 'edit_area_validation_error');
        
        // Add keyboard event listeners for better UX
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                // Close any open modals when Escape is pressed
                const modals = [
                    'confirmDeleteModal',
                    'confirmDeleteAllModal', 
                    'confirmDeletePageModal',
                    'addCropModal',
                    'editCropModal',
                    'batchUploadModal'
                ];
                
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (modal && !modal.classList.contains('hidden')) {
                        if (modalId === 'confirmDeleteModal') closeConfirmDeleteModal();
                        else if (modalId === 'confirmDeleteAllModal') closeConfirmDeleteAllModal();
                        else if (modalId === 'confirmDeletePageModal') closeConfirmDeletePageModal();
                        else if (modalId === 'addCropModal') closeAddCropModal();
                        else if (modalId === 'editCropModal') closeEditCropModal();
                        else if (modalId === 'batchUploadModal') closeBatchUploadModal();
                    }
                });
                
                // Also close final confirmation if open
                const finalModal = document.querySelector('.fixed.inset-0.z-50');
                if (finalModal) {
                    closeFinalConfirmation();
                }
            }
        });
        
        // Close modals when clicking outside
        document.addEventListener('click', function(event) {
            const modals = [
                'confirmDeleteModal',
                'confirmDeleteAllModal', 
                'confirmDeletePageModal'
            ];
            
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && !modal.classList.contains('hidden')) {
                    // Check if click was on the modal backdrop (not the modal content)
                    if (event.target === modal) {
                        if (modalId === 'confirmDeleteModal') closeConfirmDeleteModal();
                        else if (modalId === 'confirmDeleteAllModal') closeConfirmDeleteAllModal();
                        else if (modalId === 'confirmDeletePageModal') closeConfirmDeletePageModal();
                    }
                }
            });
        });

        // Enhanced Search Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');
            const searchHints = document.getElementById('searchHints');
            let searchTimeout;

            if (searchInput) {
                // Show search hints on focus
                searchInput.addEventListener('focus', function() {
                    if (this.value.length === 0) {
                        searchHints.classList.remove('hidden');
                    }
                });

                // Hide search hints on blur (with small delay to allow clicking)
                searchInput.addEventListener('blur', function() {
                    setTimeout(() => {
                        searchHints.classList.add('hidden');
                    }, 150);
                });

                // Hide hints when typing
                searchInput.addEventListener('input', function() {
                    searchHints.classList.add('hidden');
                    
                    // Clear existing timeout
                    if (searchTimeout) {
                        clearTimeout(searchTimeout);
                    }
                    
                    // If search is cleared, submit immediately
                    if (this.value.length === 0) {
                        searchForm.submit();
                        return;
                    }
                    
                    // Auto-submit after 1 second of no typing (real-time search)
                    searchTimeout = setTimeout(() => {
                        if (this.value.length >= 2) { // Only search if 2+ characters
                            searchForm.submit();
                        }
                    }, 1000);
                });

                // Submit on Enter key
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchForm.submit();
                    }
                });

                // Add visual feedback for active search
                if (searchInput.value.length > 0) {
                    searchInput.classList.add('border-blue-500', 'ring-2', 'ring-blue-200');
                    highlightSearchResults(searchInput.value);
                }
            }
        });

        // Function to highlight search terms in the table
        function highlightSearchResults(searchTerm) {
            if (!searchTerm || searchTerm.length < 2) return;
            
            const tableRows = document.querySelectorAll('.crop-row');
            const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            
            tableRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                cells.forEach(cell => {
                    // Skip cells with checkboxes or buttons
                    if (cell.querySelector('input[type="checkbox"]') || cell.querySelector('button')) {
                        return;
                    }
                    
                    const originalText = cell.textContent;
                    if (originalText && regex.test(originalText)) {
                        const highlightedText = originalText.replace(regex, '<mark class="bg-yellow-200 px-1 rounded">$1</mark>');
                        cell.innerHTML = highlightedText;
                    }
                });
            });
        }

        // Enhanced search statistics display
        function updateSearchStats() {
            const searchInput = document.getElementById('searchInput');
            const searchTerm = searchInput ? searchInput.value : '';
            
            if (searchTerm.length >= 2) {
                console.log(`Search performed for: "${searchTerm}"`);
                console.log(`Results found: {{ $crops->total() }} total items`);
                
                // Add search result indicator
                const resultInfo = document.querySelector('.search-result-info');
                if (!resultInfo && {{ $crops->total() }} > 0) {
                    const infoDiv = document.createElement('div');
                    infoDiv.className = 'search-result-info text-sm text-blue-600 mb-2';
                    infoDiv.innerHTML = `<i class="fas fa-search mr-1"></i> Found {{ $crops->total() }} results for "${searchTerm}"`;
                    
                    const table = document.querySelector('table');
                    if (table) {
                        table.parentNode.insertBefore(infoDiv, table);
                    }
                }
            }
        }

        // Enhanced real-time search functionality
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        const searchHints = document.getElementById('searchHints');
        const searchForm = document.getElementById('searchForm');
        const municipalityFilter = document.getElementById('municipalityFilter');
        const municipalityHints = document.getElementById('municipalityHints');
        const cropFilter = document.getElementById('cropFilter');
        const cropHints = document.getElementById('cropHints');

        // Show/hide search hints on focus/blur
        if (searchInput && searchHints) {
            searchInput.addEventListener('focus', function() {
                if (this.value.length === 0) {
                    searchHints.classList.remove('hidden');
                }
            });

            searchInput.addEventListener('blur', function() {
                // Delay hiding to allow for clicks
                setTimeout(() => {
                    searchHints.classList.add('hidden');
                }, 150);
            });

            // Hide hints when typing
            searchInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    searchHints.classList.add('hidden');
                } else {
                    searchHints.classList.remove('hidden');
                }
                
                // Clear existing timeout
                clearTimeout(searchTimeout);
                
                // Set new timeout for auto-search
                searchTimeout = setTimeout(() => {
                    if (this.value.length >= 3 || this.value.length === 0) {
                        searchForm.submit();
                    }
                }, 1000); // Wait 1 second after user stops typing
            });
        }

        // Enhanced filter hints functionality
        if (municipalityFilter && municipalityHints) {
            municipalityFilter.addEventListener('mouseenter', function() {
                municipalityHints.classList.remove('hidden');
            });

            municipalityFilter.addEventListener('mouseleave', function() {
                setTimeout(() => {
                    municipalityHints.classList.add('hidden');
                }, 300);
            });

            municipalityFilter.addEventListener('focus', function() {
                municipalityHints.classList.remove('hidden');
            });

            municipalityFilter.addEventListener('blur', function() {
                setTimeout(() => {
                    municipalityHints.classList.add('hidden');
                }, 150);
            });
        }

        if (cropFilter && cropHints) {
            cropFilter.addEventListener('mouseenter', function() {
                cropHints.classList.remove('hidden');
            });

            cropFilter.addEventListener('mouseleave', function() {
                setTimeout(() => {
                    cropHints.classList.add('hidden');
                }, 300);
            });

            cropFilter.addEventListener('focus', function() {
                cropHints.classList.remove('hidden');
            });

            cropFilter.addEventListener('blur', function() {
                setTimeout(() => {
                    cropHints.classList.add('hidden');
                }, 150);
            });
        }

        // Handle form submission with visual feedback
        if (searchForm) {
            searchForm.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalText = submitBtn.textContent;
                    
                    submitBtn.textContent = 'Filtering...';
                    submitBtn.disabled = true;
                    
                    // Re-enable after a short delay (in case of client-side validation failure)
                    setTimeout(() => {
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                    }, 3000);
                }
            });
        }

        // Enhanced filter status indication
        function updateFilterStatus() {
            const activeFilters = [];
            
            // Check for active search
            if (searchInput && searchInput.value.trim() !== '') {
                activeFilters.push(`Search: "${searchInput.value}"`);
            }
            
            // Check for active municipality filter
            if (municipalityFilter && municipalityFilter.value !== '') {
                activeFilters.push(`Municipality: ${municipalityFilter.value}`);
            }
            
            // Check for active crop filter  
            if (cropFilter && cropFilter.value !== '') {
                activeFilters.push(`Crop: ${cropFilter.value}`);
            }
            
            // Check for sort
            const sortSelect = document.querySelector('select[name="sort"]');
            if (sortSelect && sortSelect.value !== '') {
                const sortText = sortSelect.value === 'asc' ? 'Ascending' : 'Descending';
                activeFilters.push(`Sort: ${sortText}`);
            }
            
            // Show filter status
            showFilterStatus(activeFilters);
        }

        function showFilterStatus(activeFilters) {
            // Remove existing filter status
            const existingStatus = document.getElementById('filterStatus');
            if (existingStatus) {
                existingStatus.remove();
            }
            
            if (activeFilters.length > 0) {
                const statusDiv = document.createElement('div');
                statusDiv.id = 'filterStatus';
                statusDiv.className = 'bg-blue-50 border-l-4 border-blue-400 p-3 mb-4 rounded-r-md';
                statusDiv.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-700 font-medium">Active Filters (${activeFilters.length}):</p>
                            <p class="text-xs text-blue-600">${activeFilters.join(' • ')}</p>
                        </div>
                        <button onclick="clearAllFilters()" class="text-blue-500 hover:text-blue-700 text-xs font-medium">
                            Clear All
                        </button>
                    </div>
                `;
                
                // Insert after the search form
                const searchContainer = document.querySelector('.bg-white.rounded-lg.shadow-sm.p-4.mb-6');
                if (searchContainer) {
                    searchContainer.parentNode.insertBefore(statusDiv, searchContainer.nextSibling);
                }
            }
        }

        // Clear all filters function
        function clearAllFilters() {
            if (searchInput) searchInput.value = '';
            if (municipalityFilter) municipalityFilter.value = '';
            if (cropFilter) cropFilter.value = '';
            const sortSelect = document.querySelector('select[name="sort"]');
            if (sortSelect) sortSelect.value = '';
            
            // Submit the form to refresh with no filters
            if (searchForm) {
                searchForm.submit();
            }
        }

        // Call search stats update after page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSearchStats();
            updateFilterStatus();
            
            // Highlight search results if there's an active search
            if (searchInput && searchInput.value.length >= 2) {
                setTimeout(() => {
                    highlightSearchResults(searchInput.value);
                }, 100);
            }
            
            // Add visual indicators for active filters
            if (municipalityFilter && municipalityFilter.value !== '') {
                municipalityFilter.classList.add('border-blue-500', 'bg-blue-50');
            }
            
            if (cropFilter && cropFilter.value !== '') {
                cropFilter.classList.add('border-blue-500', 'bg-blue-50');
            }
        });

    </script>
</body>
</html>