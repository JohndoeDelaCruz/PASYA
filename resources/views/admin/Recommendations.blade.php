<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommendations - Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex">
        @include('admin.partials.sidebar', ['active' => 'recommendations'])
        
        <!-- Main Content Container -->
        <div class="flex-1 ml-64 min-h-screen">
            <!-- Main Content -->
            <main class="flex flex-col">
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Recommendations</h1>
                        <p class="text-sm text-gray-600">AI recommendations - coming soon</p>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-6">
                <!-- Empty content area -->
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <div class="max-w-md mx-auto">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Recommendations</h3>
                        <p class="text-gray-600">This page is under construction. AI recommendation features will be added here.</p>
                    </div>
                </div>
            </div>
            </main>
        </div>
    </div>
</body>
</html>