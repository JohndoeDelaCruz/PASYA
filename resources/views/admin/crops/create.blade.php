<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Crop - Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 h-screen overflow-hidden">
    <div class="flex h-full">
        @include('admin.partials.sidebar', ['active' => 'crops'])

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.crops.index') }}" class="text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Add New Crop</h1>
                            <p class="text-sm text-gray-600">Create a crop entry for a farmer</p>
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-6">
                <div class="max-w-2xl mx-auto">
                    <!-- Form -->
                    <div class="bg-white rounded-lg shadow-md border border-gray-200">
                        <form action="{{ route('admin.crops.store') }}" method="POST" class="p-6 space-y-6">
                            @csrf

                            <!-- Farmer Selection -->
                            <div>
                                <label for="farmer_id" class="block text-sm font-medium text-gray-700 mb-2">Farmer *</label>
                                <select name="farmer_id" id="farmer_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('farmer_id') border-red-500 @enderror" required>
                                    <option value="">Select a farmer...</option>
                                    @foreach($farmers as $farmer)
                                        <option value="{{ $farmer->farmerID }}" {{ old('farmer_id') == $farmer->farmerID ? 'selected' : '' }}>
                                            {{ $farmer->farmerName }} - {{ $farmer->farmerLocation }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('farmer_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Crop Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Crop Name *</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" 
                                       placeholder="e.g., Rice, Corn, Tomato" required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Variety -->
                            <div>
                                <label for="variety" class="block text-sm font-medium text-gray-700 mb-2">Variety</label>
                                <input type="text" name="variety" id="variety" value="{{ old('variety') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('variety') border-red-500 @enderror" 
                                       placeholder="e.g., IR64, Sweet Corn">
                                @error('variety')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Planting Date -->
                            <div>
                                <label for="planting_date" class="block text-sm font-medium text-gray-700 mb-2">Planting Date *</label>
                                <input type="date" name="planting_date" id="planting_date" value="{{ old('planting_date') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('planting_date') border-red-500 @enderror" required>
                                @error('planting_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Expected Harvest Date -->
                            <div>
                                <label for="expected_harvest_date" class="block text-sm font-medium text-gray-700 mb-2">Expected Harvest Date</label>
                                <input type="date" name="expected_harvest_date" id="expected_harvest_date" value="{{ old('expected_harvest_date') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('expected_harvest_date') border-red-500 @enderror">
                                @error('expected_harvest_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Area -->
                            <div>
                                <label for="area_hectares" class="block text-sm font-medium text-gray-700 mb-2">Area (Hectares) *</label>
                                <input type="number" step="0.01" min="0.01" name="area_hectares" id="area_hectares" value="{{ old('area_hectares') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('area_hectares') border-red-500 @enderror" 
                                       placeholder="0.00" required>
                                @error('area_hectares')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Expected Yield -->
                            <div>
                                <label for="expected_yield_kg" class="block text-sm font-medium text-gray-700 mb-2">Expected Yield (kg)</label>
                                <input type="number" step="0.01" min="0" name="expected_yield_kg" id="expected_yield_kg" value="{{ old('expected_yield_kg') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('expected_yield_kg') border-red-500 @enderror" 
                                       placeholder="0.00">
                                @error('expected_yield_kg')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea name="description" id="description" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror" 
                                          placeholder="Additional notes about this crop...">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Form Actions -->
                            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                                <a href="{{ route('admin.crops.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Cancel
                                </a>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Add Crop
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>