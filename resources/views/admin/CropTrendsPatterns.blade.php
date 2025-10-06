<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crop Trends & Patterns - Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex">
        @include('admin.partials.sidebar', ['active' => 'crop-trends'])
        
        <!-- Main Content Container -->
                <div class="flex h-screen bg-gray-100">
            <!-- Sidebar -->
            @include('admin.partials.sidebar')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col ml-64">
                @include('admin.partials.header')
                
                <!-- Page Title -->
                <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Crop Trends & Patterns</h1>
                        <p class="text-sm text-gray-600">Analyze agricultural trends and seasonal patterns</p>
                    </div>
                </div>

            <div class="flex-1 overflow-y-auto p-6">
                <!-- Empty content area -->
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <div class="max-w-md mx-auto">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Crop Trends & Patterns</h3>
                        <p class="text-gray-600">This page is under construction. Crop analysis features will be added here.</p>
                    </div>
                </div>
            </div>
            </main>
        </div>
    </div>
</body>
</html>