<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Benguet Agriculture Platform - Data-Driven Insights</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 50%, #bbf7d0 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .glow-effect {
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.3);
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="min-h-screen">

    <!-- Hero Section -->
    <section class="py-32 px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Floating Action Buttons -->
            <div class="fixed top-8 right-8 z-50 flex flex-col space-y-4">
                <a href="{{ route('login') }}" class="bg-white shadow-lg hover:shadow-xl text-green-700 font-semibold py-3 px-6 rounded-full transition-all duration-300 hover:scale-105 glow-effect">
                    Login
                </a>
                
            </div>

            <div class="flex flex-col lg:flex-row items-center justify-center">
                <!-- Left Content -->
                <div class="lg:w-1/2 lg:pr-12 mb-16 lg:mb-0 text-center lg:text-left">
                    <div class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-semibold mb-6">
                        🌱 Smart Agriculture Platform
                    </div>
                    <h1 class="text-6xl lg:text-7xl font-bold text-green-800 mb-8 leading-tight">
                        Revolutionizing 
                        <span class="bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">Benguet</span>
                        <br>Agriculture
                    </h1>
                    <p class="text-2xl text-green-700 mb-10 leading-relaxed max-w-2xl">
                        Harness the power of data analytics and AI to transform your farming operations. 
                        Make smarter decisions, increase yields, and build a sustainable future.
                    </p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-6 justify-center lg:justify-start">
                        <a href="{{ route('login') }}" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-5 px-10 rounded-2xl shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 text-center text-lg">
                            Start Your Journey
                        </a>
                        <a href="#features" class="border-2 border-green-600 text-green-600 hover:bg-green-600 hover:text-white font-bold py-5 px-10 rounded-2xl transition-all duration-300 text-center text-lg">
                            Explore Features
                        </a>
                    </div>
                </div>
                
                <!-- Right Content - Enhanced Hero Visual -->
                <div class="lg:w-1/2 relative">
                    <div class="relative floating-animation">
                        <div class="w-96 h-96 mx-auto bg-gradient-to-br from-green-400 via-green-500 to-green-600 rounded-3xl shadow-2xl flex items-center justify-center transform rotate-3 glow-effect">
                            <div class="text-center text-white transform -rotate-3">
                                <div class="text-9xl mb-6"></div>
                                <h3 class="text-3xl font-bold mb-2">Smart Farming</h3>
                                <p class="text-green-100 text-lg">AI-Powered Agriculture</p>
                            </div>
                        </div>
                        <!-- Enhanced Floating Elements -->
                        <div class="absolute -top-4 -left-8 bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-6 py-3 rounded-2xl shadow-lg font-semibold transform -rotate-12 floating-animation" style="animation-delay: 1s;">
                            📊 Real-time Analytics
                        </div>
                        <div class="absolute -bottom-4 -right-8 bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-3 rounded-2xl shadow-lg font-semibold transform rotate-12 floating-animation" style="animation-delay: 2s;">
                            🤖 AI Predictions
                        </div>
                        <div class="absolute top-1/2 -right-12 bg-gradient-to-r from-pink-500 to-red-500 text-white px-6 py-3 rounded-2xl shadow-lg font-semibold transform rotate-6 floating-animation" style="animation-delay: 3s;">
                            📈 Growth Insights
                        </div>
                        <div class="absolute top-8 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-indigo-500 to-blue-600 text-white px-6 py-3 rounded-2xl shadow-lg font-semibold floating-animation" style="animation-delay: 0.5s;">
                            🌡️ Weather Data
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-white/80 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <div class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-semibold mb-6">
                    ✨ Platform Capabilities
                </div>
                <h2 class="text-5xl font-bold bg-gradient-to-r from-green-800 to-green-600 bg-clip-text text-transparent mb-6">
                    Powerful Features for Modern Farmers
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Discover cutting-edge tools designed to elevate your agricultural operations to new heights
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Feature 1 -->
                <div class="bg-white p-10 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 card-hover border border-green-100">
                    <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-8 shadow-lg">
                        <span class="text-4xl">📊</span>
                    </div>
                    <h3 class="text-2xl font-bold text-green-800 mb-6">Advanced Analytics</h3>
                    <p class="text-gray-600 leading-relaxed text-lg mb-6">
                        Deep insights into crop performance, weather patterns, and market trends with interactive dashboards and predictive modeling.
                    </p>
                    <div class="flex items-center text-green-600 font-semibold">
                        Learn more <span class="ml-2">→</span>
                    </div>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-white p-10 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 card-hover border border-yellow-100">
                    <div class="w-20 h-20 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center mb-8 shadow-lg">
                        <span class="text-4xl">🤖</span>
                    </div>
                    <h3 class="text-2xl font-bold text-green-800 mb-6">AI-Powered Intelligence</h3>
                    <p class="text-gray-600 leading-relaxed text-lg mb-6">
                        Machine learning algorithms that adapt to your farm's unique conditions, providing personalized recommendations and forecasts.
                    </p>
                    <div class="flex items-center text-green-600 font-semibold">
                        Learn more <span class="ml-2">→</span>
                    </div>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-white p-10 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 card-hover border border-blue-100">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mb-8 shadow-lg">
                        <span class="text-4xl">📈</span>
                    </div>
                    <h3 class="text-2xl font-bold text-green-800 mb-6">Smart Decision Support</h3>
                    <p class="text-gray-600 leading-relaxed text-lg mb-6">
                        Intelligent tools that guide critical farming decisions, from planting schedules to resource allocation and harvest timing.
                    </p>
                    <div class="flex items-center text-green-600 font-semibold">
                        Learn more <span class="ml-2">→</span>
                    </div>
                </div>
            </div>

            <!-- Additional Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mt-16">
                <div class="text-center p-6 bg-green-50 rounded-2xl">
                    <div class="text-3xl mb-4">🌡️</div>
                    <h4 class="font-semibold text-green-800 mb-2">Weather Monitoring</h4>
                    <p class="text-sm text-gray-600">Real-time weather data</p>
                </div>
                <div class="text-center p-6 bg-blue-50 rounded-2xl">
                    <div class="text-3xl mb-4">💧</div>
                    <h4 class="font-semibold text-green-800 mb-2">Irrigation Control</h4>
                    <p class="text-sm text-gray-600">Smart water management</p>
                </div>
                <div class="text-center p-6 bg-yellow-50 rounded-2xl">
                    <div class="text-3xl mb-4">🚜</div>
                    <h4 class="font-semibold text-green-800 mb-2">Equipment Tracking</h4>
                    <p class="text-sm text-gray-600">Monitor farm machinery</p>
                </div>
                <div class="text-center p-6 bg-purple-50 rounded-2xl">
                    <div class="text-3xl mb-4">📱</div>
                    <h4 class="font-semibold text-green-800 mb-2">Mobile Access</h4>
                    <p class="text-sm text-gray-600">Farm management on-the-go</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-24 bg-gradient-to-br from-green-600 via-green-700 to-green-800 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-32 h-32 border border-white rounded-full"></div>
            <div class="absolute top-32 right-20 w-24 h-24 border border-white rounded-full"></div>
            <div class="absolute bottom-20 left-32 w-40 h-40 border border-white rounded-full"></div>
            <div class="absolute bottom-10 right-10 w-28 h-28 border border-white rounded-full"></div>
        </div>
        
        <div class="max-w-5xl mx-auto text-center px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="inline-block bg-white/20 text-white px-4 py-2 rounded-full text-sm font-semibold mb-8">
                🚀 Join the Revolution
            </div>
            <h2 class="text-5xl lg:text-6xl font-bold text-white mb-8 leading-tight">
                Transform Your Farm Into a 
                <span class="bg-gradient-to-r from-yellow-300 to-orange-400 bg-clip-text text-transparent">Smart Operation</span>
            </h2>
            <p class="text-xl text-green-100 mb-12 leading-relaxed max-w-3xl mx-auto">
                Join over 500+ progressive farmers in Benguet who are already leveraging our platform to achieve 
                <strong class="text-yellow-300">30% higher yields</strong> and <strong class="text-yellow-300">25% cost reduction</strong>.
            </p>
            
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <div class="text-center">
                    <div class="text-4xl font-bold text-yellow-300 mb-2">500+</div>
                    <div class="text-green-100">Active Farmers</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-yellow-300 mb-2">30%</div>
                    <div class="text-green-100">Yield Increase</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-yellow-300 mb-2">25%</div>
                    <div class="text-green-100">Cost Reduction</div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                
                <a href="{{ route('login') }}" class="border-2 border-white/50 text-white hover:bg-white hover:text-green-800 font-bold py-5 px-10 rounded-2xl transition-all duration-300 text-lg backdrop-blur-sm">
                    Sign In to Dashboard
                </a>
            </div>
            
            
        </div>
    </section>
</body>
</html>
