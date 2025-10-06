<!-- Sidebar -->
<aside class="fixed left-0 top-0 w-64 h-full bg-white flex flex-col shadow-lg z-40" style="background-color: #28310D;">
    <div class="p-6 border-b" style="border-color: rgba(255, 255, 255, 0.2);">
        <div class="flex items-center space-x-3">
            <img src="{{ asset('images/PASYA.png') }}" alt="PASYA Logo" class="w-10 h-10 object-contain">
            <div>
                <h1 class="text-xl font-bold text-yellow-400">PASYA</h1>
                <p class="text-xs" style="color: rgba(255, 255, 255, 0.7);">Admin Panel</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2">
        <!-- Dashboard Section -->
        <div class="mb-6">
            <p class="text-xs uppercase tracking-wide mb-3" style="color: rgba(255, 255, 255, 0.8);">Dashboard</p>
            <a href="{{ route('admin.data-analytics') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-all duration-200 {{ $active === 'data-analytics' ? 'text-yellow-400' : 'text-white' }}" style="{{ $active === 'data-analytics' ? 'background-color: rgba(255, 255, 255, 0.1);' : '' }}" onmouseover="if (!this.classList.contains('{{ $active === 'data-analytics' ? 'active' : '' }}')) this.style.backgroundColor='rgba(255, 255, 255, 0.1)'; this.style.color='#fbbf24';" onmouseout="if (!this.classList.contains('{{ $active === 'data-analytics' ? 'active' : '' }}')) { this.style.backgroundColor=''; this.style.color='white'; }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span>Data & Analytics</span>
            </a>
            <a href="{{ route('admin.crop-trends') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-all duration-200 mt-1 {{ $active === 'crop-trends' ? 'text-yellow-400' : 'text-white' }}" style="{{ $active === 'crop-trends' ? 'background-color: rgba(255, 255, 255, 0.1);' : '' }}" onmouseover="if (!this.classList.contains('{{ $active === 'crop-trends' ? 'active' : '' }}')) this.style.backgroundColor='rgba(255, 255, 255, 0.1)'; this.style.color='#fbbf24';" onmouseout="if (!this.classList.contains('{{ $active === 'crop-trends' ? 'active' : '' }}')) { this.style.backgroundColor=''; this.style.color='white'; }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <span>Crop Trends & Patterns</span>
            </a>
        </div>

        <!-- Management Section -->
        <div>
            <p class="text-xs uppercase tracking-wide mb-3" style="color: rgba(255, 255, 255, 0.8);">Management</p>
            <a href="{{ route('admin.create-account') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-all duration-200 {{ $active === 'create-account' ? 'text-yellow-400' : 'text-white' }}" style="{{ $active === 'create-account' ? 'background-color: rgba(255, 255, 255, 0.1);' : '' }}" onmouseover="if (!this.classList.contains('{{ $active === 'create-account' ? 'active' : '' }}')) this.style.backgroundColor='rgba(255, 255, 255, 0.1)'; this.style.color='#fbbf24';" onmouseout="if (!this.classList.contains('{{ $active === 'create-account' ? 'active' : '' }}')) { this.style.backgroundColor=''; this.style.color='white'; }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Create Account</span>
            </a>
            <a href="{{ route('admin.crops.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-all duration-200 mt-1 {{ $active === 'crops' ? 'text-yellow-400' : 'text-white' }}" style="{{ $active === 'crops' ? 'background-color: rgba(255, 255, 255, 0.1);' : '' }}" onmouseover="if (!this.classList.contains('{{ $active === 'crops' ? 'active' : '' }}')) this.style.backgroundColor='rgba(255, 255, 255, 0.1)'; this.style.color='#fbbf24';" onmouseout="if (!this.classList.contains('{{ $active === 'crops' ? 'active' : '' }}')) { this.style.backgroundColor=''; this.style.color='white'; }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span>Crop Production Management</span>
            </a>
            <a href="{{ route('admin.crop-management.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-all duration-200 mt-1 {{ $active === 'crop-management' ? 'text-yellow-400' : 'text-white' }}" style="{{ $active === 'crop-management' ? 'background-color: rgba(255, 255, 255, 0.1);' : '' }}" onmouseover="if (!this.classList.contains('{{ $active === 'crop-management' ? 'active' : '' }}')) this.style.backgroundColor='rgba(255, 255, 255, 0.1)'; this.style.color='#fbbf24';" onmouseout="if (!this.classList.contains('{{ $active === 'crop-management' ? 'active' : '' }}')) { this.style.backgroundColor=''; this.style.color='white'; }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                </svg>
                <span>Crop Management System</span>
            </a>
            <a href="{{ route('admin.recommendations') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-all duration-200 mt-1 {{ $active === 'recommendations' ? 'text-yellow-400' : 'text-white' }}" style="{{ $active === 'recommendations' ? 'background-color: rgba(255, 255, 255, 0.1);' : '' }}" onmouseover="if (!this.classList.contains('{{ $active === 'recommendations' ? 'active' : '' }}')) this.style.backgroundColor='rgba(255, 255, 255, 0.1)'; this.style.color='#fbbf24';" onmouseout="if (!this.classList.contains('{{ $active === 'recommendations' ? 'active' : '' }}')) { this.style.backgroundColor=''; this.style.color='white'; }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                <span>Recommendations</span>
            </a>
        </div>
    </nav>

    <div class="p-4 border-t" style="border-color: rgba(255, 255, 255, 0.2);">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center">
                    <span class="text-sm font-bold" style="color: #28310D;">{{ substr(Auth::guard('admin')->user()->name, 0, 1) }}</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-white">{{ Auth::guard('admin')->user()->name }}</p>
                    <p class="text-xs" style="color: rgba(255, 255, 255, 0.7);">Administrator</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="transition-colors duration-200" style="color: rgba(255, 255, 255, 0.7);" onmouseover="this.style.color='white';" onmouseout="this.style.color='rgba(255, 255, 255, 0.7)';">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>