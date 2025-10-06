<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Account Management - PASYA Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex">
        <!-- Include Sidebar -->
        @include('admin.partials.sidebar', ['active' => 'create-account'])
        
        <!-- Main Content Container -->
        <div class="flex-1 ml-64 min-h-screen">
            <!-- Main Content -->
            <main class="flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Farmer Account Management</h1>
                            <p class="text-sm text-gray-600 mt-1">Manage and view farmer accounts in the system</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button onclick="openAddFarmerModal()" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Add Farmer Account</span>
                            </button>
                            <button onclick="openBatchImportModal()" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <span>Batch Import</span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Filters Section -->
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <form id="filterForm" class="flex items-center space-x-4">
                        <div class="flex-1">
                            <input type="text" id="searchInput" name="search" placeholder="Search by name..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="{{ request('search') }}">
                        </div>
                        <div class="w-48">
                            <select id="municipalityFilter" name="municipality" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Municipality</option>
                                @foreach($municipalities ?? [] as $municipality)
                                    <option value="{{ $municipality }}" {{ request('municipality') == $municipality ? 'selected' : '' }}>
                                        {{ $municipality }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-48">
                            <select id="cooperativeFilter" name="cooperative" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Cooperative</option>
                                @foreach($cooperatives ?? [] as $cooperative)
                                    <option value="{{ $cooperative }}" {{ request('cooperative') == $cooperative ? 'selected' : '' }}>
                                        {{ $cooperative }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" onclick="resetFilters()" class="px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            Reset ✕
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                            View
                        </button>
                    </form>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 p-6 overflow-y-auto">
                <!-- Data Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Table Actions -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Select All</span>
                                </label>
                                <span id="selectedCount" class="text-sm text-gray-600">0 of {{ $farmers->total() ?? 0 }} row(s) selected.</span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-600">
                                    Showing {{ $farmers->firstItem() ?? 0 }} to {{ $farmers->lastItem() ?? 0 }} of {{ $farmers->total() ?? 0 }} results
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="w-12 px-6 py-3 text-left">
                                        <span class="sr-only">Select</span>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                        <div class="flex items-center space-x-1">
                                            <span>Full Name</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                            </svg>
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                        <div class="flex items-center space-x-1">
                                            <span>Municipality</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                            </svg>
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                        <div class="flex items-center space-x-1">
                                            <span>Farmer's Cooperative</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                            </svg>
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                        <div class="flex items-center space-x-1">
                                            <span>Contact Number</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                            </svg>
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($farmers ?? [] as $farmer)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" class="farmer-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $farmer->farmerID }}">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $farmer->farmerName }}</div>
                                            <div class="text-xs text-gray-500">Username: {{ $farmer->username }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $farmer->farmerLocation ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $farmer->farmerCooperative ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $farmer->farmerContactInfo ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $farmer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $farmer->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button onclick="editFarmer({{ $farmer->farmerID }})" class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button onclick="toggleFarmerStatus({{ $farmer->farmerID }}, {{ $farmer->is_active ? 'false' : 'true' }})" class="text-{{ $farmer->is_active ? 'red' : 'green' }}-600 hover:text-{{ $farmer->is_active ? 'red' : 'green' }}-900 transition-colors duration-200">
                                                @if($farmer->is_active)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                @endif
                                            </button>
                                            <button onclick="deleteFarmer({{ $farmer->farmerID }})" class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No farmers found</h3>
                                            <p class="text-gray-500 mb-4">Get started by adding a new farmer account.</p>
                                            <button onclick="openAddFarmerModal()" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                                Add Farmer Account
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-700">Rows per page</span>
                                <select id="perPageSelect" onchange="changePerPage()" class="px-3 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                            
                            @if(isset($farmers) && $farmers->hasPages())
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-700">
                                    Page {{ $farmers->currentPage() }} of {{ $farmers->lastPage() }}
                                </span>
                                <div class="flex items-center space-x-1">
                                    <a href="{{ $farmers->previousPageUrl() }}" 
                                       class="p-2 text-gray-400 hover:text-gray-600 {{ !$farmers->previousPageUrl() ? 'cursor-not-allowed opacity-50' : '' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ $farmers->previousPageUrl() }}" 
                                       class="p-2 text-gray-400 hover:text-gray-600 {{ !$farmers->previousPageUrl() ? 'cursor-not-allowed opacity-50' : '' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ $farmers->nextPageUrl() }}" 
                                       class="p-2 text-gray-400 hover:text-gray-600 {{ !$farmers->nextPageUrl() ? 'cursor-not-allowed opacity-50' : '' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ $farmers->nextPageUrl() }}" 
                                       class="p-2 text-gray-400 hover:text-gray-600 {{ !$farmers->nextPageUrl() ? 'cursor-not-allowed opacity-50' : '' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            </main>
        </div>
    </div>

    <!-- Add Farmer Modal -->
    <div id="addFarmerModal" class="fixed inset-0 bg-transparent hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-screen overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Add New Farmer Account</h3>
                </div>
                <form id="addFarmerForm" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="username" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" onblur="generateUsernameIfEmpty()">
                        <p class="text-xs text-gray-500 mt-1">Username must be unique. Leave empty to auto-generate from full name.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Municipality <span class="text-red-500">*</span>
                        </label>
                        <select name="municipality" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Municipality</option>
                            <option value="Atok">Atok</option>
                            <option value="Bakun">Bakun</option>
                            <option value="Bokod">Bokod</option>
                            <option value="Buguias">Buguias</option>
                            <option value="Itogon">Itogon</option>
                            <option value="Kabayan">Kabayan</option>
                            <option value="Kapangan">Kapangan</option>
                            <option value="Kibungan">Kibungan</option>
                            <option value="La Trinidad">La Trinidad</option>
                            <option value="Mankayan">Mankayan</option>
                            <option value="Sablan">Sablan</option>
                            <option value="Tuba">Tuba</option>
                            <option value="Tublay">Tublay</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cooperative</label>
                        <select name="cooperative" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Cooperative (Optional)</option>
                            <option value="Benguet Highland Farmers Cooperative">Benguet Highland Farmers Cooperative</option>
                            <option value="La Trinidad Vegetable Growers Association">La Trinidad Vegetable Growers Association</option>
                            <option value="Northern Benguet Agri Cooperative">Northern Benguet Agri Cooperative</option>
                            <option value="Kabayan Organic Farmers Cooperative">Kabayan Organic Farmers Cooperative</option>
                            <option value="Tuba Agro-Enterprise Cooperative">Tuba Agro-Enterprise Cooperative</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                        <input type="text" name="contact_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="flex items-center justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeAddFarmerModal()" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors duration-200">
                            Add Farmer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Batch Import Modal -->
    <div id="batchImportModal" class="fixed inset-0 bg-transparent hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Batch Import Farmers</h3>
                </div>
                <form id="batchImportForm" class="p-6 space-y-4" enctype="multipart/form-data">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Upload CSV File <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="csv_file" accept=".csv" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Upload a CSV file with farmer data</p>
                    </div>
                    <div class="flex items-center justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeBatchImportModal()" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors duration-200">
                            Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Farmer Modal -->
    <div id="editFarmerModal" class="fixed inset-0 bg-transparent hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-screen overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Edit Farmer Account</h3>
                </div>
                <form id="editFarmerForm" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="farmer_id" id="editFarmerId">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="editName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="username" id="editUsername" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Username must be unique.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" id="editPassword" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Leave empty to keep current password.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="editPasswordConfirmation" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Municipality <span class="text-red-500">*</span>
                        </label>
                        <select name="municipality" id="editMunicipality" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Municipality</option>
                            <option value="Atok">Atok</option>
                            <option value="Bakun">Bakun</option>
                            <option value="Bokod">Bokod</option>
                            <option value="Buguias">Buguias</option>
                            <option value="Itogon">Itogon</option>
                            <option value="Kabayan">Kabayan</option>
                            <option value="Kapangan">Kapangan</option>
                            <option value="Kibungan">Kibungan</option>
                            <option value="La Trinidad">La Trinidad</option>
                            <option value="Mankayan">Mankayan</option>
                            <option value="Sablan">Sablan</option>
                            <option value="Tuba">Tuba</option>
                            <option value="Tublay">Tublay</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cooperative</label>
                        <select name="cooperative" id="editCooperative" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Cooperative (Optional)</option>
                            <option value="Benguet Highland Farmers Cooperative">Benguet Highland Farmers Cooperative</option>
                            <option value="La Trinidad Vegetable Growers Association">La Trinidad Vegetable Growers Association</option>
                            <option value="Northern Benguet Agri Cooperative">Northern Benguet Agri Cooperative</option>
                            <option value="Kabayan Organic Farmers Cooperative">Kabayan Organic Farmers Cooperative</option>
                            <option value="Tuba Agro-Enterprise Cooperative">Tuba Agro-Enterprise Cooperative</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                        <input type="text" name="contact_number" id="editContactNumber" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="flex items-center justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeEditFarmerModal()" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors duration-200">
                            Update Farmer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateSelectedCount();
            setupEventListeners();
        });

        function setupEventListeners() {
            // Select all checkbox
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.farmer-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateSelectedCount();
                });
            }

            // Individual checkboxes
            document.querySelectorAll('.farmer-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            // Filter form submission
            const filterForm = document.getElementById('filterForm');
            if (filterForm) {
                filterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    applyFilters();
                });
            }

            // Add farmer form submission
            const addFarmerForm = document.getElementById('addFarmerForm');
            if (addFarmerForm) {
                addFarmerForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    submitAddFarmer();
                });
            }

            // Batch import form submission
            const batchImportForm = document.getElementById('batchImportForm');
            if (batchImportForm) {
                batchImportForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    submitBatchImport();
                });
            }

            // Edit farmer form submission
            const editFarmerForm = document.getElementById('editFarmerForm');
            if (editFarmerForm) {
                editFarmerForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    submitEditFarmer();
                });
            }
        }

        function updateSelectedCount() {
            const checkboxes = document.querySelectorAll('.farmer-checkbox:checked');
            const total = document.querySelectorAll('.farmer-checkbox').length;
            const selectedCountElement = document.getElementById('selectedCount');
            if (selectedCountElement) {
                selectedCountElement.textContent = `${checkboxes.length} of ${total} row(s) selected.`;
            }
            
            const selectAll = document.getElementById('selectAll');
            if (selectAll) {
                selectAll.checked = checkboxes.length === total && total > 0;
                selectAll.indeterminate = checkboxes.length > 0 && checkboxes.length < total;
            }
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('municipalityFilter').value = '';
            document.getElementById('cooperativeFilter').value = '';
            applyFilters();
        }

        function applyFilters() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            window.location.href = `${window.location.pathname}?${params.toString()}`;
        }

        function changePerPage() {
            const perPage = document.getElementById('perPageSelect').value;
            const url = new URL(window.location);
            url.searchParams.set('per_page', perPage);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        }

        // Modal functions
        function openAddFarmerModal() {
            document.getElementById('addFarmerModal').classList.remove('hidden');
        }

        function closeAddFarmerModal() {
            document.getElementById('addFarmerModal').classList.add('hidden');
            document.getElementById('addFarmerForm').reset();
        }

        function openBatchImportModal() {
            document.getElementById('batchImportModal').classList.remove('hidden');
        }

        function closeBatchImportModal() {
            document.getElementById('batchImportModal').classList.add('hidden');
            document.getElementById('batchImportForm').reset();
        }

        function openEditFarmerModal() {
            document.getElementById('editFarmerModal').classList.remove('hidden');
        }

        function closeEditFarmerModal() {
            document.getElementById('editFarmerModal').classList.add('hidden');
            document.getElementById('editFarmerForm').reset();
        }

        function generateUsernameIfEmpty() {
            const usernameInput = document.querySelector('input[name="username"]');
            const nameInput = document.querySelector('input[name="name"]');
            
            if (!usernameInput.value && nameInput.value) {
                // Generate username from name: remove spaces, convert to lowercase, add timestamp
                let username = nameInput.value
                    .toLowerCase()
                    .replace(/[^a-z0-9]/g, '')
                    .substring(0, 10);
                
                // Add random number to ensure uniqueness
                username += Math.floor(Math.random() * 1000);
                
                usernameInput.value = username;
            }
        }

        function submitAddFarmer() {
            const form = document.getElementById('addFarmerForm');
            const formData = new FormData(form);

            fetch('{{ route("admin.create-account") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                // Check if the response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server returned non-JSON response. Please refresh the page and try again.');
                }
                
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Farmer added successfully!');
                    closeAddFarmerModal();
                    window.location.reload();
                } else {
                    if (data.redirect) {
                        alert('Session expired. Please log in again.');
                        window.location.href = data.redirect;
                    } else {
                        alert('Error: ' + (data.message || 'Failed to add farmer'));
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (error.message) {
                    alert('Error: ' + error.message);
                } else if (error.errors) {
                    // Handle validation errors
                    let errorMessages = [];
                    for (let field in error.errors) {
                        errorMessages.push(field + ': ' + error.errors[field].join(', '));
                    }
                    alert('Validation errors:\n' + errorMessages.join('\n'));
                } else {
                    alert('An error occurred while adding the farmer. Please refresh the page and try again.');
                }
            });
        }

        function submitBatchImport() {
            const form = document.getElementById('batchImportForm');
            const formData = new FormData(form);

            fetch('{{ route("admin.farmers.batch-import") ?? "#" }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Successfully imported ${data.count} farmers!`);
                    closeBatchImportModal();
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to import farmers'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during batch import');
            });
        }

        function submitEditFarmer() {
            const form = document.getElementById('editFarmerForm');
            const formData = new FormData(form);
            const farmerId = document.getElementById('editFarmerId').value;

            // Add the _method field for Laravel method spoofing
            formData.append('_method', 'PUT');

            fetch(`{{ url('/admin/farmers') }}/${farmerId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                // Check if the response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server returned non-JSON response. Please refresh the page and try again.');
                }
                
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Farmer updated successfully!');
                    closeEditFarmerModal();
                    window.location.reload();
                } else {
                    if (data.redirect) {
                        alert('Session expired. Please log in again.');
                        window.location.href = data.redirect;
                    } else {
                        alert('Error: ' + (data.message || 'Failed to update farmer'));
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (error.message) {
                    alert('Error: ' + error.message);
                } else if (error.errors) {
                    // Handle validation errors
                    let errorMessages = [];
                    for (let field in error.errors) {
                        errorMessages.push(field + ': ' + error.errors[field].join(', '));
                    }
                    alert('Validation errors:\n' + errorMessages.join('\n'));
                } else {
                    alert('An error occurred while updating the farmer. Please refresh the page and try again.');
                }
            });
        }

        function editFarmer(id) {
            // Fetch farmer data and populate the edit form
            fetch(`{{ url('/admin/farmers') }}/${id}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch farmer data');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.farmer) {
                    const farmer = data.farmer;
                    
                    // Populate the edit form
                    document.getElementById('editFarmerId').value = farmer.id;
                    document.getElementById('editName').value = farmer.name || '';
                    document.getElementById('editUsername').value = farmer.username || '';
                    document.getElementById('editMunicipality').value = farmer.municipality || '';
                    document.getElementById('editCooperative').value = farmer.cooperative || '';
                    document.getElementById('editContactNumber').value = farmer.contact_number || '';
                    
                    // Clear password fields
                    document.getElementById('editPassword').value = '';
                    document.getElementById('editPasswordConfirmation').value = '';
                    
                    // Open the edit modal
                    openEditFarmerModal();
                } else {
                    alert('Error: ' + (data.message || 'Failed to fetch farmer data'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while fetching farmer data');
            });
        }

        function toggleFarmerStatus(id, status) {
            fetch(`{{ url('/admin/farmers') }}/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ is_active: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error updating farmer status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating farmer status');
            });
        }

        function deleteFarmer(id) {
            if (confirm('Are you sure you want to delete this farmer account?')) {
                fetch(`{{ url('/admin/farmers') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Error deleting farmer');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting farmer');
                });
            }
        }

        // Form validation for Add Farmer Modal
        document.getElementById('addFarmerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            clearFormErrors('addFarmerForm');
            
            let hasErrors = false;
            const form = this;
            
            // Required field validation
            const requiredFields = [
                { name: 'name', label: 'Full Name' },
                { name: 'username', label: 'Username' },
                { name: 'password', label: 'Password' },
                { name: 'password_confirmation', label: 'Confirm Password' },
                { name: 'municipality', label: 'Municipality' }
            ];
            
            requiredFields.forEach(field => {
                const input = form.querySelector(`[name="${field.name}"]`);
                if (!input.value.trim()) {
                    showFieldError(input, `${field.label} is required`);
                    hasErrors = true;
                }
            });
            
            // Password confirmation validation
            const password = form.querySelector('[name="password"]').value;
            const passwordConfirmation = form.querySelector('[name="password_confirmation"]').value;
            
            if (password && passwordConfirmation && password !== passwordConfirmation) {
                showFieldError(form.querySelector('[name="password_confirmation"]'), 'Passwords do not match');
                hasErrors = true;
            }
            
            // Username format validation
            const username = form.querySelector('[name="username"]').value;
            if (username && !/^[a-zA-Z0-9_]+$/.test(username)) {
                showFieldError(form.querySelector('[name="username"]'), 'Username can only contain letters, numbers, and underscores');
                hasErrors = true;
            }
            
            if (!hasErrors) {
                // Submit form via AJAX (existing functionality)
                addFarmer();
            }
        });

        // Form validation for Edit Farmer Modal
        document.getElementById('editFarmerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            clearFormErrors('editFarmerForm');
            
            let hasErrors = false;
            const form = this;
            
            // Required field validation
            const requiredFields = [
                { name: 'name', label: 'Full Name' },
                { name: 'username', label: 'Username' },
                { name: 'municipality', label: 'Municipality' }
            ];
            
            requiredFields.forEach(field => {
                const input = form.querySelector(`[name="${field.name}"]`);
                if (!input.value.trim()) {
                    showFieldError(input, `${field.label} is required`);
                    hasErrors = true;
                }
            });
            
            // Password confirmation validation (only if password is provided)
            const password = form.querySelector('[name="password"]').value;
            const passwordConfirmation = form.querySelector('[name="password_confirmation"]').value;
            
            if (password && password !== passwordConfirmation) {
                showFieldError(form.querySelector('[name="password_confirmation"]'), 'Passwords do not match');
                hasErrors = true;
            }
            
            // Username format validation
            const username = form.querySelector('[name="username"]').value;
            if (username && !/^[a-zA-Z0-9_]+$/.test(username)) {
                showFieldError(form.querySelector('[name="username"]'), 'Username can only contain letters, numbers, and underscores');
                hasErrors = true;
            }
            
            if (!hasErrors) {
                // Submit form via AJAX (existing functionality)
                updateFarmer();
            }
        });

        // Form validation for Batch Import
        document.getElementById('batchImportForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            clearFormErrors('batchImportForm');
            
            let hasErrors = false;
            const fileInput = this.querySelector('[name="csv_file"]');
            
            if (!fileInput.files || fileInput.files.length === 0) {
                showFieldError(fileInput, 'Please select a CSV file to upload');
                hasErrors = true;
            } else {
                const file = fileInput.files[0];
                const allowedTypes = ['text/csv', 'application/csv', 'text/plain'];
                
                if (!allowedTypes.includes(file.type) && !file.name.toLowerCase().endsWith('.csv')) {
                    showFieldError(fileInput, 'Please select a valid CSV file');
                    hasErrors = true;
                }
            }
            
            if (!hasErrors) {
                // Submit form via AJAX (existing functionality)
                batchImportFarmers();
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

        // Real-time validation
        document.addEventListener('input', function(e) {
            if (e.target.matches('input[required], select[required], textarea[required]')) {
                if (e.target.value.trim()) {
                    clearFieldError(e.target);
                }
            }
        });

        document.addEventListener('blur', function(e) {
            if (e.target.matches('input[required], select[required], textarea[required]')) {
                if (!e.target.value.trim()) {
                    const label = e.target.parentElement.querySelector('label').textContent.replace('*', '').trim();
                    showFieldError(e.target, `${label} is required`);
                }
            }
        }, true);
    </script>
</body>
</html>