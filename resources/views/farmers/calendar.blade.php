<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - PASYA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Sidebar Layout Fix */
        @media (min-width: 1024px) {
            #mobileSidebar {
                transform: translateX(0) !important;
                display: flex !important;
            }
        }
        
        /* Mobile sidebar overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 35;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease-in-out;
        }
        
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        @media (min-width: 1024px) {
            .sidebar-overlay {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="min-h-screen">
        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-35 hidden lg:hidden" onclick="closeMobileSidebar()"></div>
        
        @include('farmers.partials.sidebar', ['active' => 'calendar'])

        <!-- Main Content Area -->
        <div class="pl-64">
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
                    <h2 class="text-2xl font-bold text-gray-900">Calendar</h2>
                    <p class="text-gray-600 mt-1">Manage your farming schedule and events</p>
                </div>

                <!-- Main Content Area (Empty for now) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <div class="text-center py-16">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Calendar Content</h3>
                        <p class="text-gray-500">Calendar and scheduling features will be added here.</p>
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

        // Mobile sidebar toggle functionality
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('active');
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.add('-translate-x-full');
            overlay.classList.remove('active');
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
