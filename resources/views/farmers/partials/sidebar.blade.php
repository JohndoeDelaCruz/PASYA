<!-- Farmer Sidebar -->
<aside class="w-64 bg-green-600 text-white flex flex-col shadow-lg">
    <div class="p-6 border-b border-green-500">
        <div class="flex items-center space-x-3">
            <img src="{{ asset('images/PASYA.png') }}" alt="PASYA Logo" class="w-10 h-10 object-contain">
            <div>
                <h1 class="text-xl font-bold text-yellow-400">PASYA</h1>
                <p class="text-xs text-green-200">Agriculture Platform</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-6">
        <!-- Dashboard Section -->
        <div>
            <p class="text-xs text-green-300 uppercase tracking-wider mb-3 font-semibold">Dashboard</p>
            <div class="space-y-1">
                <a href="{{ route('farmer.pricelist-watch') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ $active === 'pricelist-watch' ? 'bg-yellow-500 text-green-800' : 'text-white hover:bg-green-700 hover:text-yellow-400' }} transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span class="font-medium">Pricelist Watch</span>
                </a>
                <a href="{{ route('farmer.calendar') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ $active === 'calendar' ? 'bg-yellow-500 text-green-800' : 'text-white hover:bg-green-700 hover:text-yellow-400' }} transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-medium">Calendar</span>
                </a>
            </div>
        </div>

        <!-- Crop Management Section -->
        <div>
            <p class="text-xs text-green-300 uppercase tracking-wider mb-3 font-semibold">Crop Management</p>
            <div class="space-y-1">
                <a href="{{ route('farmer.harvest-history') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ $active === 'harvest-history' ? 'bg-yellow-500 text-green-800' : 'text-white hover:bg-green-700 hover:text-yellow-400' }} transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-medium">Harvest History & Crop List</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- User Info Footer -->
    <div class="p-4 border-t border-green-500">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center">
                    <span class="text-green-600 text-sm font-bold">{{ substr(Auth::guard('farmer')->user()->name, 0, 1) }}</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-white">{{ Auth::guard('farmer')->user()->name }}</p>
                    <p class="text-xs text-green-200">Farmer</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-green-200 hover:text-white transition-colors duration-200" title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>