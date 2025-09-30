<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Farmer Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Sidebar Layout Fix */
        @media (min-width: 1024px) {
            #mobileSidebar {
                transform: translateX(0) !important;
                display: flex !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="min-h-screen">
        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-35 hidden lg:hidden" onclick="closeMobileSidebar()"></div>
        
        @include('farmers.partials.sidebar', ['active' => 'profile'])

        <!-- Main Content Area -->
        <div class="pl-64">
        <!-- Page Banner with Controls -->
        <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-6">
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
                        <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-10">
                            <div class="py-1">
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profile
                                </a>
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Settings
                                </a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-6 space-y-4 bg-gray-50 min-h-screen">
            <!-- Page Title -->
            <div class="mb-4">
                <h2 class="text-2xl font-bold text-gray-900">Profile</h2>
                <p class="text-gray-600 mt-1">Manage your personal information and account settings</p>
            </div>

            <!-- Profile Content -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="max-w-2xl">
                    <!-- Profile Header -->
                    <div class="flex items-center space-x-6 mb-8">
                        <div class="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-2xl font-bold">
                                {{ substr(Auth::guard('farmer')->user()->name ?? 'U', 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">{{ Auth::guard('farmer')->user()->name ?? 'User' }}</h3>
                            <p class="text-gray-600">{{ Auth::guard('farmer')->user()->email ?? 'No email' }}</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                                Farmer
                            </span>
                        </div>
                    </div>

                    <!-- Profile Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                            <p class="text-gray-900 bg-gray-50 rounded-lg px-3 py-2">{{ Auth::guard('farmer')->user()->username ?? 'Not set' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <p class="text-gray-900 bg-gray-50 rounded-lg px-3 py-2">{{ Auth::guard('farmer')->user()->email ?? 'Not set' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Municipality</label>
                            <p class="text-gray-900 bg-gray-50 rounded-lg px-3 py-2">{{ Auth::guard('farmer')->user()->municipality ?? 'Not specified' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cooperative</label>
                            <p class="text-gray-900 bg-gray-50 rounded-lg px-3 py-2">{{ Auth::guard('farmer')->user()->cooperative ?? 'Not specified' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                            <p class="text-gray-900 bg-gray-50 rounded-lg px-3 py-2">{{ Auth::guard('farmer')->user()->contact_number ?? 'Not specified' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Member Since</label>
                            <p class="text-gray-900 bg-gray-50 rounded-lg px-3 py-2">{{ Auth::guard('farmer')->user()->created_at ? Auth::guard('farmer')->user()->created_at->format('F d, Y') : 'Unknown' }}</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 flex items-center space-x-4">
                        <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            Edit Profile
                        </button>
                        <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            Change Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for dropdown -->
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profileDropdown');
            const button = event.target.closest('button');
            
            if (!button || !button.getAttribute('onclick')) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>