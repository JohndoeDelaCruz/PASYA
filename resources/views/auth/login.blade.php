<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Benguet Agriculture Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="w-full max-w-6xl bg-white rounded-3xl shadow-2xl overflow-hidden">
        <div class="flex min-h-[600px]">
            <!-- Left Side - Login Form -->
            <div class="w-1/2 bg-green-600 p-12 flex flex-col justify-center">
                <div class="max-w-md mx-auto w-full">
                    <!-- Title -->
                    <h1 class="text-4xl font-bold text-yellow-400 mb-8 text-center">Log In</h1>
                    
                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Username Field -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-yellow-400 mb-2">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                value="{{ old('username') }}"
                                class="w-full px-4 py-3 rounded-lg border border-green-300 focus:ring-2 focus:ring-yellow-400 focus:border-transparent bg-white text-gray-900 placeholder-gray-500"
                                placeholder="Enter your username"
                                required
                            >
                            @error('username')
                                <p class="mt-1 text-sm text-yellow-200">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-yellow-400 mb-2">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password"
                                class="w-full px-4 py-3 rounded-lg border border-green-300 focus:ring-2 focus:ring-yellow-400 focus:border-transparent bg-white text-gray-900 placeholder-gray-500"
                                placeholder="Enter your password"
                                required
                            >
                            @error('password')
                                <p class="mt-1 text-sm text-yellow-200">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- User Type Field -->
                        <div>
                            <label for="user_type" class="block text-sm font-medium text-yellow-400 mb-2">Login As</label>
                            <select 
                                id="user_type" 
                                name="user_type"
                                class="w-full px-4 py-3 rounded-lg border border-green-300 focus:ring-2 focus:ring-yellow-400 focus:border-transparent bg-white text-gray-900"
                                required
                            >
                                <option value="">Select user type</option>
                                <option value="admin" {{ old('user_type') == 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="farmer" {{ old('user_type') == 'farmer' ? 'selected' : '' }}>Farmer</option>
                            </select>
                            @error('user_type')
                                <p class="mt-1 text-sm text-yellow-200">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Forgot Password Link -->
                        <div class="text-center">
                            <a href="#" class="text-yellow-400 hover:text-yellow-300 text-sm font-medium transition-colors duration-200">
                                Forget your password?
                            </a>
                        </div>
                        
                        <!-- Register Link -->
                        <div class="text-center">
                            <p class="text-yellow-400 text-sm">
                                Don't have an account? 
                                <a href="{{ route('register') }}" class="text-yellow-300 hover:text-yellow-200 font-medium transition-colors duration-200">
                                    Register here
                                </a>
                            </p>
                        </div>
                        
                        <!-- Login Button -->
                        <button 
                            type="submit"
                            class="w-full bg-yellow-400 hover:bg-yellow-500 text-green-800 font-bold py-4 px-6 rounded-2xl text-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200"
                        >
                            Log in
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Right Side - Logo and Caption -->
            <div class="w-1/2 bg-white p-12 flex flex-col items-center justify-center">
                <div class="text-center">
                    <!-- Logo -->
                    <div class="flex justify-center mb-8">
                        <img src="{{ asset('images/PASYA.png') }}" alt="Benguet Agriculture Platform Logo" class="w-32 h-32 object-contain">
                    </div>
                    
                    <!-- Caption -->
                    <h2 class="text-2xl font-semibold text-green-800 leading-relaxed max-w-md">
                        Empowering Benguet Agriculture with Data-Driven Insights and Smart Decision Support
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const usernameField = document.getElementById('username');
            const passwordField = document.getElementById('password');

            // Form validation
            form.addEventListener('submit', function(e) {
                let hasErrors = false;
                
                // Remove existing error styling
                [usernameField, passwordField].forEach(field => {
                    field.classList.remove('border-red-500');
                    const errorMsg = field.parentElement.querySelector('.error-message');
                    if (errorMsg) errorMsg.remove();
                });

                // Validate username
                if (!usernameField.value.trim()) {
                    showFieldError(usernameField, 'Username is required');
                    hasErrors = true;
                }

                // Validate password
                if (!passwordField.value.trim()) {
                    showFieldError(passwordField, 'Password is required');
                    hasErrors = true;
                }

                if (hasErrors) {
                    e.preventDefault();
                }
            });

            // Real-time validation
            [usernameField, passwordField].forEach(field => {
                field.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        showFieldError(this, this.name.charAt(0).toUpperCase() + this.name.slice(1) + ' is required');
                    } else {
                        clearFieldError(this);
                    }
                });

                field.addEventListener('input', function() {
                    if (this.value.trim()) {
                        clearFieldError(this);
                    }
                });
            });

            function showFieldError(field, message) {
                field.classList.add('border-red-500');
                
                // Remove existing error message
                const existingError = field.parentElement.querySelector('.error-message');
                if (existingError) existingError.remove();

                // Add new error message
                const errorDiv = document.createElement('p');
                errorDiv.className = 'mt-1 text-sm text-yellow-200 error-message';
                errorDiv.textContent = message;
                field.parentElement.appendChild(errorDiv);
            }

            function clearFieldError(field) {
                field.classList.remove('border-red-500');
                const errorMsg = field.parentElement.querySelector('.error-message');
                if (errorMsg) errorMsg.remove();
            }
        });
    </script>
</body>
</html>