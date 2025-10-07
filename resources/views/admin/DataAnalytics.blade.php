<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data & Analytics - Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .metric-card {
            transition: all 0.3s ease;
        }
        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
        .summary-item {
            transition: all 0.2s ease;
        }
        .summary-item:hover {
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex">
        @include('admin.partials.sidebar', ['active' => 'data-analytics'])
        
        <!-- Main Content Container -->
        <div class="flex-1 lg:ml-64 min-h-screen">
            @include('admin.partials.header')

            <!-- Main Content -->
            <main class="flex flex-col">
            <!-- Page Title Header -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-4 sm:px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Dashboard</h1>
                        <p class="text-sm text-gray-600">Comprehensive agricultural data analytics and insights</p>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                <!-- Action Buttons and Filters Section -->
                <div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <!-- Left side - Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                        <button class="w-full sm:w-auto px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors duration-200 font-medium text-sm sm:text-base">
                            Allocate Resource
                        </button>
                        <button class="w-full sm:w-auto px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors duration-200 font-medium text-sm sm:text-base">
                            Recommend
                        </button>
                    </div>

                    <!-- Right side - Filters -->
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                        <span class="text-sm font-medium text-gray-700">Filter by:</span>
                        
                        <!-- Crop Dropdown -->
                        <select name="crop" class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-500 bg-white">
                            <option value="">Crop</option>
                            <option value="broccoli">Broccoli</option>
                            <option value="cabbage">Cabbage</option>
                            <option value="carrots">Carrots</option>
                            <option value="cauliflower">Cauliflower</option>
                            <option value="chinese-cabbage">Chinese Cabbage</option>
                            <option value="garden-peas">Garden Peas</option>
                            <option value="lettuce">Lettuce</option>
                            <option value="onion">Onion</option>
                            <option value="snap-beans">Snap Beans</option>
                            <option value="sweet-pepper">Sweet Pepper</option>
                            <option value="white-potato">White Potato</option>
                        </select>

                        <!-- Municipality Dropdown -->
                        <select name="municipality" class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-500 bg-white">
                            <option value="">Municipality</option>
                            <option value="atok">Atok</option>
                            <option value="bakun">Bakun</option>
                            <option value="bokod">Bokod</option>
                            <option value="buguias">Buguias</option>
                            <option value="itogon">Itogon</option>
                            <option value="kabayan">Kabayan</option>
                            <option value="kapangan">Kapangan</option>
                            <option value="kibungan">Kibungan</option>
                            <option value="la-trinidad">La Trinidad</option>
                            <option value="mankayan">Mankayan</option>
                            <option value="sablan">Sablan</option>
                            <option value="tuba">Tuba</option>
                            <option value="tublay">Tublay</option>
                        </select>

                        <!-- Month Dropdown -->
                        <select name="month" class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-500 bg-white">
                            <option value="">Month</option>
                            <option value="january">January</option>
                            <option value="february">February</option>
                            <option value="march">March</option>
                            <option value="april">April</option>
                            <option value="may">May</option>
                            <option value="june">June</option>
                            <option value="july">July</option>
                            <option value="august">August</option>
                            <option value="september">September</option>
                            <option value="october">October</option>
                            <option value="november">November</option>
                            <option value="december">December</option>
                        </select>

                        <!-- Year Dropdown -->
                        <select name="year" class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-500 bg-white">
                            <option value="">Year</option>
                            <option value="2015">2015</option>
                            <option value="2016">2016</option>
                            <option value="2017">2017</option>
                            <option value="2018">2018</option>
                            <option value="2019">2019</option>
                            <option value="2020">2020</option>
                            <option value="2021">2021</option>
                            <option value="2022">2022</option>
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                        </select>

                        <!-- Reset Button -->
                                                <!-- Reset Button -->
                        <button onclick="resetFilters()" class="w-full sm:w-auto px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors duration-200 font-medium text-sm sm:text-base">
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Production Chart -->
                <div class="mb-6 bg-white rounded-lg shadow-sm p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 space-y-2 sm:space-y-0">
                        <div>
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900">Total Analysis Line Chart</h3>
                            <p class="text-xs sm:text-sm text-gray-600">Production analysis by crop type</p>
                        </div>
                        <div class="flex items-center space-x-2 text-xs sm:text-sm">
                            <span class="text-gray-600">Production up by 5.2% this month</span>
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="productionChart"></canvas>
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Key Metrics:</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        <!-- Number of Farmers -->
                        <div class="metric-card bg-white rounded-lg shadow-sm p-4 sm:p-6">
                            <div class="flex items-center">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 sm:ml-4 flex-1 min-w-0">
                                    <h4 class="text-xs sm:text-sm font-medium text-gray-600">Number of Farmers</h4>
                                    <p class="text-lg sm:text-2xl font-bold text-gray-900">156</p>
                                    <p class="text-xs text-gray-500">Updated July 2025</p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Area -->
                        <div class="metric-card bg-white rounded-lg shadow-sm p-4 sm:p-6">
                            <div class="flex items-center">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 sm:ml-4 flex-1 min-w-0">
                                    <h4 class="text-xs sm:text-sm font-medium text-gray-600">Total Area planted/harvested</h4>
                                    <p class="text-lg sm:text-2xl font-bold text-gray-900">1000 ha</p>
                                    <p class="text-xs text-gray-500">Updated July 2025</p>
                                </div>
                            </div>
                        </div>

                        <!-- Average Yield -->
                        <div class="metric-card bg-white rounded-lg shadow-sm p-4 sm:p-6">
                            <div class="flex items-center">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 sm:ml-4 flex-1 min-w-0">
                                    <h4 class="text-xs sm:text-sm font-medium text-gray-600">Average Yield</h4>
                                    <p class="text-lg sm:text-2xl font-bold text-gray-900">56</p>
                                    <p class="text-xs text-gray-500">Updated July 2025</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export Summary Data Button -->
                    <div class="mt-4 flex justify-stretch sm:justify-end">
                        <button class="w-full sm:w-auto px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors duration-200 text-sm sm:text-base">
                            Export Summary Data
                        </button>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Top 3 Crops -->
                    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900">Summary Cards</h3>
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        </div>
                        
                        <div class="space-y-3">
                            <h4 class="font-medium text-gray-800">Top 3 Crops</h4>
                            <div class="summary-item p-2 rounded">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">1. Broccoli - Area planted</span>
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                </div>
                            </div>
                            <div class="summary-item p-2 rounded">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">2. White Potato - Area Harvested</span>
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                </div>
                            </div>
                            <div class="summary-item p-2 rounded">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">3. Cabbage - Ave planted</span>
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Updated July 2025</p>

                            <div class="mt-6">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-gray-800">Most Productive Municipality</h4>
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                </div>
                                <p class="text-sm text-gray-700">Baguio</p>
                                <p class="text-xs text-gray-500">Updated June 2025</p>
                            </div>
                        </div>
                    </div>

                    <!-- Demand Chart -->
                    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="font-medium text-gray-800">Demand</h3>
                                <p class="text-sm text-gray-600">January - June 2024</p>
                            </div>
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        </div>
                        
                        <div class="chart-container" style="height: 200px;">
                            <canvas id="demandChart"></canvas>
                        </div>
                        
                        <div class="mt-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Demand down by 2.4% this month</span>
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Showing trend analysis for the last 6 months</p>
                        </div>
                    </div>
                </div>
            </div>
            </main>
        </div>
    </div>

    <script>
        // Production Line Chart
        const productionCtx = document.getElementById('productionChart').getContext('2d');
        const productionChart = new Chart(productionCtx, {
            type: 'line',
            data: {
                labels: ['Broccoli', 'Cabbage', 'Carrots', 'Cauliflower', 'Chinese Cabbage', 'Garden Peas', 'Lettuce', 'Snap Beans', 'Sweet Beans', 'White Potato'],
                datasets: [{
                    label: '2023',
                    data: [65, 45, 35, 50, 40, 55, 30, 45, 60, 70],
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4
                }, {
                    label: '2024',
                    data: [70, 50, 40, 55, 45, 60, 35, 50, 65, 75],
                    borderColor: 'rgb(59, 130, 246)', 
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }, {
                    label: '2025',
                    data: [75, 55, 45, 60, 50, 65, 40, 55, 70, 80],
                    borderColor: 'rgb(234, 179, 8)',
                    backgroundColor: 'rgba(234, 179, 8, 0.1)', 
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Demand Bar Chart
        const demandCtx = document.getElementById('demandChart').getContext('2d');
        const demandChart = new Chart(demandCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Demand',
                    data: [65, 45, 80, 35, 70, 55],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(163, 163, 163, 0.8)', 
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(163, 163, 163, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(34, 197, 94, 0.8)'
                    ],
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Account Dropdown Toggle
        function toggleAccountDropdown() {
            const dropdown = document.getElementById('accountDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('accountDropdown');
            const button = event.target.closest('button[onclick="toggleAccountDropdown()"]');
            
            if (!button && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Filter functionality
        function applyFilters() {
            const filters = {
                crop: document.querySelector('select[name="crop"]')?.value || '',
                month: document.querySelector('select[name="month"]')?.value || '',
                municipality: document.querySelector('select[name="municipality"]')?.value || '',
                year: document.querySelector('select[name="year"]')?.value || '',
                fileType: document.querySelector('select[name="file_type"]')?.value || '',
                status: document.querySelector('select[name="status"]')?.value || '',
                cooperative: document.querySelector('select[name="cooperative"]')?.value || ''
            };

            console.log('Filters applied:', filters);
            
            // Update charts based on filters
            updateChartsWithFilters(filters);
            
            // Show filter applied message
            showFilterMessage(filters);
        }

        function updateChartsWithFilters(filters) {
            // Update production chart data based on filters
            if (filters.crop || filters.municipality || filters.year) {
                // Simulate filtering data
                const filteredData = generateFilteredData(filters);
                
                // Update production chart
                productionChart.data.datasets.forEach((dataset, index) => {
                    dataset.data = filteredData.production[index];
                });
                productionChart.update();
                
                // Update demand chart
                demandChart.data.datasets[0].data = filteredData.demand;
                demandChart.update();
            }
        }

        function generateFilteredData(filters) {
            // Generate sample filtered data based on filters
            const baseProduction = [
                [65, 45, 35, 50, 40, 55, 30, 45, 60, 70],
                [70, 50, 40, 55, 45, 60, 35, 50, 65, 75],
                [75, 55, 45, 60, 50, 65, 40, 55, 70, 80]
            ];
            
            const baseDemand = [65, 45, 80, 35, 70, 55];
            
            // Apply filter modifications (sample logic)
            const modifier = Object.values(filters).filter(v => v).length * 0.1;
            
            return {
                production: baseProduction.map(dataset => 
                    dataset.map(value => Math.round(value * (1 + modifier)))
                ),
                demand: baseDemand.map(value => Math.round(value * (1 + modifier)))
            };
        }

        function showFilterMessage(filters) {
            const activeFilters = Object.entries(filters).filter(([key, value]) => value);
            if (activeFilters.length > 0) {
                const message = `Filters applied: ${activeFilters.map(([key, value]) => `${key}: ${value}`).join(', ')}`;
                
                // Create and show temporary message
                const messageDiv = document.createElement('div');
                messageDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
                messageDiv.textContent = message;
                document.body.appendChild(messageDiv);
                
                setTimeout(() => {
                    messageDiv.remove();
                }, 3000);
            }
        }

        // Reset filters functionality
        function resetFilters() {
            // Reset all dropdowns to empty values
            const selects = document.querySelectorAll('select[name="crop"], select[name="municipality"], select[name="month"], select[name="year"]');
            selects.forEach(select => {
                select.value = '';
            });
            
            // Reset charts to original data
            if (window.productionChart) {
                window.productionChart.data.datasets.forEach((dataset, index) => {
                    const originalData = [
                        [65, 45, 35, 50, 40, 55, 30, 45, 60, 70],
                        [70, 50, 40, 55, 45, 60, 35, 50, 65, 75],
                        [75, 55, 45, 60, 50, 65, 40, 55, 70, 80]
                    ];
                    dataset.data = originalData[index];
                });
                window.productionChart.update();
            }
            
            if (window.demandChart) {
                const originalDemandData = [65, 45, 80, 35, 70, 55];
                window.demandChart.data.datasets[0].data = originalDemandData;
                window.demandChart.update();
            }
            
            // Show reset message
            const messageDiv = document.createElement('div');
            messageDiv.className = 'fixed top-4 right-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded z-50';
            messageDiv.textContent = 'Filters have been reset to default values';
            document.body.appendChild(messageDiv);
            
            setTimeout(() => {
                messageDiv.remove();
            }, 2000);
        }

        // Add interactivity for filter buttons
        document.addEventListener('DOMContentLoaded', function() {
            // Add name attributes to selects for easier filtering
            const selects = document.querySelectorAll('select');
            const selectNames = ['crop', 'month', 'municipality', 'year', 'file_type', 'status', 'cooperative'];
            
            selects.forEach((select, index) => {
                if (selectNames[index]) {
                    select.name = selectNames[index];
                }
            });

            // Filter button functionality
            const filterButton = document.querySelector('button[onclick*="Filter"]') || 
                                document.querySelector('button:contains("Filter")');
            if (filterButton) {
                filterButton.addEventListener('click', applyFilters);
            }

            // Add hover effects to metric cards
            const metricCards = document.querySelectorAll('.metric-card');
            metricCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Auto-filter on dropdown change
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    // Optional: Apply filters automatically on change
                    // applyFilters();
                });
            });
        });
    </script>
</body>
</html>