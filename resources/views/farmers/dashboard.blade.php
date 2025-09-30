<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PASYA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Custom styles for enhanced farmer dashboard */
        .calendar-day {
            min-height: 80px;
            transition: all 0.2s ease-in-out;
        }
        
        .calendar-day:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .weather-temp {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .price-item {
            transition: all 0.3s ease;
        }
        
        .price-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 8px;
        }
        
        /* Smooth fade-in animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Sidebar Layout Fix - Ensure sidebar is always visible on desktop */
        @media (min-width: 1024px) {
            #mobileSidebar {
                transform: translateX(0) !important;
                display: flex !important;
            }
        }
        
        /* Ensure main content has proper spacing - High Specificity Fix */
        div.pl-64 {
            padding-left: 16rem !important; /* 256px to match sidebar width */
            margin-left: 0 !important; /* Override any margin */
        }
        
        @media (max-width: 1023px) {
            div.pl-64 {
                padding-left: 0 !important;
                margin-left: 0 !important;
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
        
        /* Loading skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="min-h-screen">
        <!-- Mobile Sidebar Overlay -->
        <div id="sidebarOverlay" class="sidebar-overlay" onclick="closeMobileSidebar()"></div>
        
        @include('farmers.partials.sidebar', ['active' => 'dashboard'])

        <!-- Main Content Area -->
        <div class="pl-64">
            <!-- Header Banner -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Left: Logo and PASYA Text -->
                    <div class="flex items-center space-x-3">
                        <!-- Mobile Menu Toggle -->
                        <button onclick="toggleMobileSidebar()" class="lg:hidden p-2 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        
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
                            <button class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-green-50 transition-colors duration-200" onclick="toggleProfileDropdown()">
                                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">
                                        {{ substr(Auth::guard('farmer')->user()->farmerName ?? 'U', 0, 1) }}
                                    </span>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-medium text-gray-700">{{ Auth::guard('farmer')->user()->farmerName ?? 'User' }}</p>
                                    <p class="text-xs text-green-600">Farmer</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
                                <div class="py-2">
                                    <div class="px-4 py-2 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-700">{{ Auth::guard('farmer')->user()->farmerName ?? 'Farmer' }}</p>
                                        <p class="text-xs text-gray-500">{{ Auth::guard('farmer')->user()->farmerContact ?? 'farmer@gmail.com' }}</p>
                                    </div>
                                    <a href="{{ route('farmer.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors duration-200">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            Profile
                                        </div>
                                    </a>
                                    <a href="{{ route('farmer.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors duration-200">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2v4zm0 0V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v2M7 13h10v-2H7v2z"/>
                                            </svg>
                                            Dashboard
                                        </div>
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                                </svg>
                                                Sign Out
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Welcome Section -->
            <div class="mx-6 mt-4 bg-gradient-to-r from-green-300 to-green-400 px-6 py-4 text-white rounded-lg shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-semibold">Welcome, {{ Auth::guard('farmer')->user()->farmerName ?? 'Anna Quitaleg' }}</h1>
                        <div class="flex items-center mt-1 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ Auth::guard('farmer')->user()->farmerLocation ?? 'Buguias' }}</span>
                        </div>
                    </div>
                    <div class="text-right text-sm">
                        <p>{{ date('l') }}</p>
                        <p class="font-medium">{{ date('F j, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <main class="p-6 space-y-4 bg-gray-50 min-h-screen">
                <!-- Calendar and News Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Calendar Section (2/3 width) -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            @php
                                $displayMonth = request('month', date('n'));
                                $displayYear = request('year', date('Y'));
                                $today = date('j');
                                $todayMonth = date('n');
                                $todayYear = date('Y');
                                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $displayMonth, $displayYear);
                                $firstDayOfWeek = date('w', strtotime("$displayYear-$displayMonth-01"));
                                $monthName = date('F Y', strtotime("$displayYear-$displayMonth-01"));
                            @endphp

                            <!-- Calendar Header -->
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-2xl font-bold text-gray-800">{{ $monthName }}</h2>
                                <div class="flex space-x-2">
                                    <button onclick="previousMonth()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="Previous Month">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </button>
                                    <button onclick="nextMonth()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="Next Month">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                    <button onclick="goToToday()" class="px-3 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors" title="Go to Today">
                                        Today
                                    </button>
                                </div>
                            </div>

                            <!-- Calendar Grid -->
                            <div class="grid grid-cols-7 gap-2 mb-4">
                                <!-- Day Headers -->
                                <div class="text-center p-3 text-sm font-medium text-gray-500">Sun</div>
                                <div class="text-center p-3 text-sm font-medium text-gray-500">Mon</div>
                                <div class="text-center p-3 text-sm font-medium text-gray-500">Tue</div>
                                <div class="text-center p-3 text-sm font-medium text-gray-500">Wed</div>
                                <div class="text-center p-3 text-sm font-medium text-gray-500">Thu</div>
                                <div class="text-center p-3 text-sm font-medium text-gray-500">Fri</div>
                                <div class="text-center p-3 text-sm font-medium text-gray-500">Sat</div>

                                <!-- Empty cells for days before month starts -->
                                @for($i = 0; $i < $firstDayOfWeek; $i++)
                                    <div class="h-20 border border-gray-200 rounded-lg bg-gray-50"></div>
                                @endfor

                                <!-- Calendar Days -->
                                @for($day = 1; $day <= $daysInMonth; $day++)
                                    @php
                                        $isToday = ($day == $today && $displayMonth == $todayMonth && $displayYear == $todayYear);
                                        $isPast = ($displayYear < $todayYear) || 
                                                 ($displayYear == $todayYear && $displayMonth < $todayMonth) ||
                                                 ($displayYear == $todayYear && $displayMonth == $todayMonth && $day < $today);
                                    @endphp
                                    <div class="calendar-day relative h-20 border border-gray-200 rounded-lg p-2 hover:bg-green-50 transition-colors cursor-pointer
                                        {{ $isToday ? 'bg-orange-100 border-orange-300 ring-2 ring-orange-300' : 'bg-green-50' }}
                                        {{ $isPast ? 'opacity-60' : '' }}"
                                        data-day="{{ $day }}"
                                        data-month="{{ $displayMonth }}"
                                        data-year="{{ $displayYear }}"
                                        title="Click to view details for {{ $monthName }} {{ $day }}">
                                        
                                        <div class="font-medium text-sm {{ $isToday ? 'text-orange-600 font-bold' : 'text-gray-700' }}">
                                            {{ $day }}
                                        </div>
                                        
                                        <!-- Sample Events -->
                                        @if($day == 4 && $displayMonth == date('n'))
                                            <!-- Plant Carrots -->
                                            <div class="absolute bottom-1 left-1 w-3 h-3 bg-teal-400 rounded-full" title="Plant Carrots"></div>
                                        @elseif($day == 2 && $displayMonth == date('n'))
                                            <!-- Harvest Cabbage -->
                                            <div class="absolute bottom-1 left-1 w-3 h-3 bg-green-500 rounded-full" title="Harvest Cabbage"></div>
                                        @elseif($day == 28 && $displayMonth == date('n'))
                                            <!-- Claim fertilizer -->
                                            <div class="absolute bottom-1 left-1 w-3 h-3 bg-gray-700 rounded-full" title="Claim fertilizer"></div>
                                        @endif
                                    </div>
                                @endfor

                                <!-- Fill remaining cells if needed -->
                                @php
                                    $totalCells = $firstDayOfWeek + $daysInMonth;
                                    $remainingCells = 42 - $totalCells; // 6 rows * 7 days = 42
                                    if ($totalCells <= 35) $remainingCells = 35 - $totalCells; // Show only 5 rows if possible
                                @endphp
                                @for($i = 0; $i < $remainingCells && $totalCells + $i < 42; $i++)
                                    <div class="h-20 border border-gray-200 rounded-lg bg-gray-50"></div>
                                @endfor
                            </div>

                            <!-- Calendar Legend -->
                            <div class="flex flex-wrap gap-4 text-sm">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-teal-400 rounded-full mr-2"></div>
                                    <span class="text-gray-600">Plant Carrots in 6 days</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                                    <span class="text-gray-600">Harvest Cabbage Today</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-gray-700 rounded-full mr-2"></div>
                                    <span class="text-gray-600">Claim fertilizer in 2 days</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- News and Announcements (1/3 width) -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">News and Announcements</h3>
                        
                        <div class="text-center py-12">
                            <div class="mb-4">
                                <!-- Illustration placeholder -->
                                <div class="mx-auto w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-gray-500 text-sm">No News and Announcement yet!</p>
                        </div>
                    </div>
                </div>

                <!-- Weather and Price Watch Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Weather Widget -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-5 h-5 text-gray-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-gray-600">{{ Auth::guard('farmer')->user()->farmerLocation ?? 'Buguias, Benguet' }}</span>
                        </div>
                        
                        <div class="mb-4">
                            <div class="weather-temp text-4xl font-bold text-gray-800">31°C</div>
                            <div class="weather-high text-sm text-gray-500">H: 37°C</div>
                        </div>

                        <div class="mb-4">
                            <div class="text-lg font-semibold text-gray-800">{{ date('l') }}</div>
                            <div class="text-sm text-gray-500">{{ date('j, M, Y') }}</div>
                        </div>

                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <div class="font-medium text-gray-800">Mostly Sunny</div>
                                <div class="text-sm text-gray-500">Feels like 35°</div>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Price Watch -->
                    <div class="bg-gradient-to-br from-green-300 to-green-400 rounded-xl shadow-sm p-6 text-white">
                        <h3 class="text-lg font-bold mb-4">Daily Price Watch</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-green-100">Cabbage</span>
                                <div class="text-right">
                                    <div class="price-cabbage font-bold">Php 77.630</div>
                                    <div class="text-sm text-red-300">-Php 24.00</div>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-green-100">Chinese Cabbage</span>
                                <div class="text-right">
                                    <div class="price-chineseCabbage font-bold">Php 149.00</div>
                                    <div class="text-sm text-green-300">+Php 16.00</div>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-green-100">Carrots</span>
                                <div class="text-right">
                                    <div class="price-carrots font-bold">Php 80.00</div>
                                    <div class="text-sm text-red-300">-Php 3.00</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-3 border-t border-green-400">
                            <a href="{{ route('farmer.pricelist-watch') }}" class="text-green-100 hover:text-white text-sm font-medium flex items-center">
                                View Full Price List
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- JavaScript for Dashboard Functionality -->
    <script>
        let currentMonth = {{ request('month', date('n')) }};
        let currentYear = {{ request('year', date('Y')) }};

        // Profile dropdown functionality
        function toggleProfileDropdown() {
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

        // Close dropdown and sidebar when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profileDropdown');
            const dropdownButton = event.target.closest('button[onclick="toggleProfileDropdown()"]');
            
            // Close profile dropdown
            if (!dropdownButton && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Close mobile sidebar when clicking outside (handled by overlay)
        // ESC key to close sidebar
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeMobileSidebar();
            }
        });

        // Calendar navigation
        function previousMonth() {
            currentMonth--;
            if (currentMonth < 1) {
                currentMonth = 12;
                currentYear--;
            }
            updateCalendar();
        }

        function nextMonth() {
            currentMonth++;
            if (currentMonth > 12) {
                currentMonth = 1;
                currentYear++;
            }
            updateCalendar();
        }

        function updateCalendar() {
            // Reload the page with new month/year parameters
            const url = new URL(window.location);
            url.searchParams.set('month', currentMonth);
            url.searchParams.set('year', currentYear);
            window.location.href = url.toString();
        }

        function goToToday() {
            const today = new Date();
            currentMonth = today.getMonth() + 1;
            currentYear = today.getFullYear();
            updateCalendar();
        }

        // Add click functionality to calendar days
        document.addEventListener('DOMContentLoaded', function() {
            // Add click listeners to calendar days
            const calendarDays = document.querySelectorAll('.calendar-day');
            calendarDays.forEach(day => {
                day.addEventListener('click', function() {
                    const dayNumber = this.dataset.day;
                    if (dayNumber) {
                        showDayDetails(dayNumber, currentMonth, currentYear);
                    }
                });
            });

            // Update real-time data periodically
            updateWeatherData();
            updatePriceData();
            
            // Set up periodic updates (every 5 minutes for weather, 1 minute for prices)
            setInterval(updateWeatherData, 300000); // 5 minutes
            setInterval(updatePriceData, 60000);    // 1 minute
        });

        function showDayDetails(day, month, year) {
            // Show details for selected day
            const selectedDate = new Date(year, month - 1, day);
            const dateString = selectedDate.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            // Create a simple modal or notification for day details
            showNotification(`Selected: ${dateString}`, 'info');
            
            // You can extend this to show actual farming activities for the day
            // Example: fetch activities from API and display in a modal
        }

        function updateWeatherData() {
            // Simulate weather data update
            const temp = Math.floor(Math.random() * 10) + 28; // 28-37°C
            const high = temp + Math.floor(Math.random() * 5) + 3;
            
            // Update temperature displays if elements exist
            const tempElement = document.querySelector('.weather-temp');
            const highElement = document.querySelector('.weather-high');
            
            if (tempElement) tempElement.textContent = `${temp}°C`;
            if (highElement) highElement.textContent = `H: ${high}°C`;
        }

        function updatePriceData() {
            // Simulate price data update
            const prices = {
                cabbage: (Math.random() * 20 + 65).toFixed(2),
                chineseCabbage: (Math.random() * 30 + 130).toFixed(2),
                carrots: (Math.random() * 15 + 70).toFixed(2)
            };
            
            // Update price displays if elements exist
            Object.keys(prices).forEach(crop => {
                const priceElement = document.querySelector(`.price-${crop}`);
                if (priceElement) {
                    priceElement.textContent = `Php ${prices[crop]}`;
                }
            });
        }

        // Notification functionality
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
                type === 'error' ? 'bg-red-500 text-white' : 
                type === 'success' ? 'bg-green-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }

        // Add smooth transitions
        document.addEventListener('DOMContentLoaded', function() {
            // Add fade-in animation to main content
            const mainContent = document.querySelector('main');
            if (mainContent) {
                mainContent.style.opacity = '0';
                mainContent.style.transition = 'opacity 0.5s ease-in-out';
                
                setTimeout(() => {
                    mainContent.style.opacity = '1';
                }, 100);
            }
        });
    </script>
</body>
</html>
