<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>View Crop - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="flex">
        @include('admin.partials.sidebar', ['active' => 'crops'])
        
        <main class="flex-1 ml-0 lg:ml-64">
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Crop Details</h1>
                        <p class="text-sm text-gray-600">View detailed information about this crop entry</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.crops.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                            <i class="fas fa-arrow-left text-sm"></i>
                            <span>Back to List</span>
                        </a>
                        <a href="{{ route('admin.crops.edit', $crop->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                            <i class="fas fa-edit text-sm"></i>
                            <span>Edit Crop</span>
                        </a>
                    </div>
                </div>

                <!-- Crop Information Card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">
                            {{ $crop->crop_name ?? $crop->name }}
                        </h2>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            {{ $crop->status === 'planted' ? 'bg-green-100 text-green-800' : 
                               ($crop->status === 'harvested' ? 'bg-blue-100 text-blue-800' : 
                                'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($crop->status ?? 'Active') }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Basic Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-700 border-b border-gray-200 pb-2">Basic Information</h3>
                            
                            @if($crop->crop_name)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Crop Name</label>
                                    <p class="text-gray-900">{{ $crop->crop_name }}</p>
                                </div>
                            @endif

                            @if($crop->municipality)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Municipality</label>
                                    <p class="text-gray-900">{{ $crop->municipality }}</p>
                                </div>
                            @endif

                            @if($crop->farm_type)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Farm Type</label>
                                    <p class="text-gray-900">{{ ucfirst($crop->farm_type) }}</p>
                                </div>
                            @endif

                            @if($crop->year)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Year</label>
                                    <p class="text-gray-900">{{ $crop->year }}</p>
                                </div>
                            @endif

                            @if($crop->variety)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Variety</label>
                                    <p class="text-gray-900">{{ $crop->variety }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Area & Production Data -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-700 border-b border-gray-200 pb-2">Area & Production</h3>
                            
                            @if($crop->area_planted)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Area Planted (ha)</label>
                                    <p class="text-gray-900">{{ number_format($crop->area_planted, 2) }}</p>
                                </div>
                            @endif

                            @if($crop->area_harvested)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Area Harvested (ha)</label>
                                    <p class="text-gray-900">{{ number_format($crop->area_harvested, 2) }}</p>
                                </div>
                            @endif

                            @if($crop->area_hectares)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Total Area (ha)</label>
                                    <p class="text-gray-900">{{ number_format($crop->area_hectares, 2) }}</p>
                                </div>
                            @endif

                            @if($crop->production_mt)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Production (mt)</label>
                                    <p class="text-gray-900">{{ number_format($crop->production_mt, 2) }}</p>
                                </div>
                            @endif

                            @if($crop->productivity_mt_ha)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Productivity (mt/ha)</label>
                                    <p class="text-gray-900">{{ number_format($crop->productivity_mt_ha, 2) }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Dates & Timeline -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-700 border-b border-gray-200 pb-2">Timeline</h3>
                            
                            @if($crop->planting_date)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Planting Date</label>
                                    <p class="text-gray-900">{{ $crop->planting_date->format('M d, Y') }}</p>
                                </div>
                            @endif

                            @if($crop->expected_harvest_date)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Expected Harvest Date</label>
                                    <p class="text-gray-900">{{ $crop->expected_harvest_date->format('M d, Y') }}</p>
                                </div>
                            @endif

                            @if($crop->actual_harvest_date)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Actual Harvest Date</label>
                                    <p class="text-gray-900">{{ $crop->actual_harvest_date->format('M d, Y') }}</p>
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-500">Created At</label>
                                <p class="text-gray-900">{{ $crop->created_at->format('M d, Y h:i A') }}</p>
                            </div>

                            @if($crop->updated_at && $crop->updated_at != $crop->created_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                                    <p class="text-gray-900">{{ $crop->updated_at->format('M d, Y h:i A') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($crop->description)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-medium text-gray-700 mb-3">Description</h3>
                            <p class="text-gray-600 leading-relaxed">{{ $crop->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Farmer Information -->
                @if($crop->farmer)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Farmer Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Farmer Name</label>
                            <p class="text-gray-900">{{ $crop->farmer->farmerName }}</p>
                        </div>
                        @if($crop->farmer->farmerLocation)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Location</label>
                                <p class="text-gray-900">{{ $crop->farmer->farmerLocation }}</p>
                            </div>
                        @endif
                        @if($crop->farmer->farmerPhone)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Phone</label>
                                <p class="text-gray-900">{{ $crop->farmer->farmerPhone }}</p>
                            </div>
                        @endif
                        @if($crop->farmer->farmerEmail)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Email</label>
                                <p class="text-gray-900">{{ $crop->farmer->farmerEmail }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 mt-6">
                    <form action="{{ route('admin.crops.destroy', $crop->id) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this crop entry?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                            <i class="fas fa-trash text-sm"></i>
                            <span>Delete Crop</span>
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>