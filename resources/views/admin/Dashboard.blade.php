<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 h-screen overflow-hidden">
    <div class="flex h-screen bg-gray-100">
        @include('admin.partials.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col lg:ml-64">
            @include('admin.partials.header')
            
            <!-- Page Title -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-4 sm:px-6 py-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                    <p class="text-sm text-gray-600">Welcome back! Here's what's happening with your agricultural system.</p>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                <!-- Empty content area - pages will be implemented as needed -->
                <div class="bg-white rounded-lg shadow p-6 sm:p-8 text-center">
                    <div class="max-w-md mx-auto">
                        <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 00-2 2v2m0 0V9a2 2 0 012-2h14a2 2 0 012 2v2M7 7V3a2 2 0 012-2h6a2 2 0 012 2v4M7 7h10"></path>
                        </svg>
                        <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">Admin Dashboard</h3>
                        <p class="text-sm text-gray-600">Select a section from the sidebar to get started.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>