<!-- Farmer Sidebar -->
<aside id="mobileSidebar" class="fixed left-0 top-0 h-full w-64 bg-green-600 text-white flex flex-col shadow-lg z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out lg:block">
    <!-- User Profile Section -->
    <div class="p-6">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                <span class="text-green-600 font-bold text-lg">
                    {{ substr(Auth::guard('farmer')->user()->farmerName ?? 'A', 0, 1) }}
                </span>
            </div>
            <div>
                <h2 class="font-bold text-white">{{ Auth::guard('farmer')->user()->farmerName ?? 'Anna Quitaleg' }}</h2>
                <p class="text-sm text-green-200">farmer@gmail.com</p>
            </div>
        </div>
    </div>

    <!-- Navigation Section -->
    <nav class="flex-1 px-4">
        <!-- Dashboard Section -->
        <div class="mb-6">
            <p class="text-sm font-semibold text-green-200 mb-3">Dashboard</p>
            <div class="space-y-2">
                <a href="{{ route('farmer.dashboard') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ $active === 'dashboard' ? 'bg-green-500' : 'hover:bg-green-500' }} transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2v4zm0 0V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v2M7 13h10v-2H7v2z"/>
                    </svg>
                    <span class="font-medium">Home</span>
                </a>

                <a href="{{ route('farmer.pricelist-watch') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ $active === 'pricelist-watch' ? 'bg-green-500' : 'hover:bg-green-500' }} transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="font-medium">Price Watch</span>
                </a>

                <a href="{{ route('farmer.calendar') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ $active === 'calendar' ? 'bg-green-500' : 'hover:bg-green-500' }} transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-medium">Calendar</span>
                </a>
            </div>
        </div>

        <!-- Crop Management Section -->
        <div class="mb-6">
            <p class="text-sm font-semibold text-green-200 mb-3">Crop Management</p>
            <div class="space-y-2">
                <a href="{{ route('farmer.harvest-history') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ $active === 'harvest-history' ? 'bg-green-500' : 'hover:bg-green-500' }} transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="font-medium">Harvest History & Crop List</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Help Section -->
    <div class="p-4 border-t border-green-500">
        <a href="#" class="flex items-center space-x-2 px-4 py-2 text-green-200 hover:text-white hover:bg-green-500 rounded-lg transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-medium">Help</span>
        </a>
    </div>

</aside>