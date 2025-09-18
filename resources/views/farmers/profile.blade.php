<!DOCTYPE html><!DOCTYPE html><!DOCTYPE html>

<html lang="en">

<head><html lang="en"><html lang="en">

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0"><head><head>

    <title>Profile - Farmer Panel</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])    <meta charset="UTF-8">    <meta charset="UTF-8">

</head>

<body class="bg-gray-50 h-screen overflow-hidden">    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <div class="flex h-full">

        @include('farmers.partials.sidebar', ['active' => 'profile'])    <title>Profile - Farmer Panel</title>    <title>Profile - Farmer Dashboard</title>



        <!-- Main Content -->    @vite(['resources/css/app.css', 'resources/js/app.js'])    @vite(['resources/css/app.css', 'resources/js/app.js'])

        <main class="flex-1 flex flex-col overflow-hidden">

            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4"></head></head>

                <div class="flex items-center justify-between">

                    <div><body class="bg-gray-50 h-screen overflow-hidden"><body class="bg-gray-100">

                        <h1 class="text-2xl font-bold text-gray-900">Profile</h1>

                        <p class="text-sm text-gray-600">Manage your personal information and account settings</p>    <div class="flex h-full">    <!-- Navigation -->

                    </div>

                </div>        @include('farmers.partials.sidebar', ['active' => 'profile'])    <nav class="bg-green-600 shadow-lg">

            </header>

        <div class="max-w-7xl mx-auto px-4">

            <div class="flex-1 overflow-y-auto p-6">

                <!-- Profile Content -->        <!-- Main Content -->            <div class="flex justify-between h-16">

                <div class="bg-white rounded-lg shadow p-8">

                    <div class="max-w-md mx-auto">        <main class="flex-1 flex flex-col overflow-hidden">                <div class="flex items-center space-x-4">

                        <div class="text-center mb-6">

                            <div class="w-24 h-24 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">                    <a href="{{ route('farmer.dashboard') }}" class="text-yellow-400 hover:text-yellow-300">

                                <span class="text-white text-2xl font-bold">{{ substr($user->name, 0, 1) }}</span>

                            </div>                <div class="flex items-center justify-between">                        ← Back to Dashboard

                            <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>

                            <p class="text-gray-600">{{ $user->email }}</p>                    <div>                    </a>

                        </div>

                                                <h1 class="text-2xl font-bold text-gray-900">Profile</h1>                    <h1 class="text-xl font-bold text-yellow-400">My Profile</h1>

                        <div class="space-y-4">

                            <div>                        <p class="text-sm text-gray-600">User profile management - coming soon</p>                </div>

                                <label class="block text-sm font-medium text-gray-700">Username</label>

                                <p class="mt-1 text-sm text-gray-900">{{ $user->username }}</p>                    </div>                <div class="flex items-center space-x-4">

                            </div>

                                            </div>                    <span class="text-yellow-400">Welcome, {{ Auth::guard('farmer')->user()->name }}</span>

                            <div>

                                <label class="block text-sm font-medium text-gray-700">Municipality</label>            </header>                    <form method="POST" action="{{ route('logout') }}" class="inline">

                                <p class="mt-1 text-sm text-gray-900">{{ $user->municipality ?? 'Not specified' }}</p>

                            </div>                        @csrf

                            

                            <div>            <div class="flex-1 overflow-y-auto p-6">                        <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-green-800 px-4 py-2 rounded font-medium">

                                <label class="block text-sm font-medium text-gray-700">Cooperative</label>

                                <p class="mt-1 text-sm text-gray-900">{{ $user->cooperative ?? 'Not specified' }}</p>                <!-- Empty content area -->                            Logout

                            </div>

                                            <div class="bg-white rounded-lg shadow p-8 text-center">                        </button>

                            <div>

                                <label class="block text-sm font-medium text-gray-700">Contact Number</label>                    <div class="max-w-md mx-auto">                    </form>

                                <p class="mt-1 text-sm text-gray-900">{{ $user->contact_number ?? 'Not specified' }}</p>

                            </div>                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">                </div>

                        </div>

                    </div>                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>            </div>

                </div>

            </div>                        </svg>        </div>

        </main>

    </div>                        <h3 class="text-lg font-medium text-gray-900 mb-2">Profile</h3>    </nav>

</body>

</html>                        <p class="text-gray-600">This page is under construction. Profile management features will be added here.</p>

                    </div>    <!-- Main Content -->

                </div>    <div class="max-w-3xl mx-auto py-6 px-4">

            </div>        <div class="bg-white shadow rounded-lg">

        </main>            <div class="px-4 py-5 sm:p-6">

    </div>                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Profile Information</h3>

</body>                

</html>                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Full name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Username</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->username }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email address</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Role</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Farmer
                            </span>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Member since</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('F d, Y') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('F d, Y') }}</dd>
                    </div>
                </dl>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex justify-end">
                        <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-medium">
                            Edit Profile
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
