<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Farmer Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 h-screen overflow-hidden">
    <div class="flex h-full">
        @include('farmers.partials.sidebar', ['active' => 'dashboard'])

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Left: Logo/Text -->
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('images/PASYA.png') }}" alt="PASYA Logo" class="w-8 h-8 object-contain">
                        <div>
                            <h1 class="text-lg font-semibold text-green-800">PASYA</h1>
                            <p class="text-xs text-green-600">Agriculture Platform</p>
                        </div>
                    </div>

                    <!-- Right: Notifications and Profile -->
                    <div class="flex items-center space-x-4">
                        <!-- Notification Bell -->
                        <button class="relative p-2 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7C18 6.279 15.458 4 12.25 4H11.75C8.542 4 6 6.279 6 9.05v.7a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"></path>
                            </svg>
                            <!-- Notification Badge -->
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                        </button>

                        <!-- Profile Dropdown -->
                        <div class="relative">
                            <button class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-green-50 transition-colors duration-200" onclick="toggleDropdown()">
                                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">
                                        {{ substr(Auth::guard('farmer')->user()->name ?? 'U', 0, 1) }}
                                    </span>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-medium text-gray-700">{{ Auth::guard('farmer')->user()->name ?? 'User' }}</p>
                                    <p class="text-xs text-green-600">Farmer</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
                                <div class="py-2">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors duration-200">
                                        Profile Settings
                                    </a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors duration-200">
                                        Account Settings
                                    </a>
                                    <div class="border-t border-gray-100 my-2"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                            Sign Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                <!-- Page Title -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Welcome, {{ $user->name }}!</h2>
                    <p class="text-gray-600 mt-1">Welcome to your farming management system</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Active Crops Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Active Crops</p>
                                <p class="text-2xl font-bold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Events Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Upcoming Events</p>
                                <p class="text-2xl font-bold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Harvests Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Harvests</p>
                                <p class="text-2xl font-bold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-green-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2v4zm0 0V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v2M7 13h10v-2H7v2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Get Started</h3>
                        <p class="text-gray-600 mb-6">Explore the different sections of your farming dashboard</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-md mx-auto">
                            <a href="{{ route('farmer.pricelist-watch') }}" class="block w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors duration-200 text-center">
                                View Price Watch
                            </a>
                            <a href="{{ route('farmer.calendar') }}" class="block w-full bg-yellow-500 text-white py-3 px-4 rounded-lg hover:bg-yellow-600 transition-colors duration-200 text-center">
                                Check Calendar
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- JavaScript for Dropdown Toggle -->
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profileDropdown');
            const button = event.target.closest('button');
            
            if (!button || !button.onclick) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
