<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PASYA - Predictive Analytics for Sustainable Agriculture and Yield Advancement</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white">
    <!-- Header Navigation -->
    <nav class="bg-gray-900 dark:bg-gray-900 fixed w-full z-20 top-0 start-0 border-b border-gray-700 dark:border-gray-600">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="{{ url('/') }}" class="flex items-center rtl:space-x-reverse">
                <img src="{{ asset('images/pasya.png') }}" class="h-12" alt="PASYA Logo"/>
                <img src="{{ asset('images/titleh.png') }}" class="h-12" alt="PASYA Title"/>
            </a>
            <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                <a href="{{ route('login') }}" class="text-white bg-green-500 hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-green-300 font-semibold rounded-lg text-sm px-6 py-2.5 text-center transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    Get started
                </a>
                <button data-collapse-toggle="navbar-sticky" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-400 rounded-lg md:hidden hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-600 transition-colors duration-200" aria-controls="navbar-sticky" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                    </svg>
                </button>
            </div>
            <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
                <ul class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-700 rounded-lg bg-gray-800 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-gray-900 dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                    <li>
                        <a href="#home" class="block py-2 px-3 text-white rounded-sm md:bg-transparent md:text-blue-500 md:p-0 hover:text-blue-400 transition-colors duration-200" aria-current="page">Home</a>
                    </li>
                    <li>
                        <a href="#about" class="block py-2 px-3 text-gray-300 rounded-sm hover:bg-gray-700 md:hover:bg-transparent md:hover:text-white md:p-0 transition-colors duration-200">About us</a>
                    </li>
                    <li>
                        <a href="#features" class="block py-2 px-3 text-gray-300 rounded-sm hover:bg-gray-700 md:hover:bg-transparent md:hover:text-white md:p-0 transition-colors duration-200">Work with us</a>
                    </li>
                    <li>
                        <a href="#contact" class="block py-2 px-3 text-gray-300 rounded-sm hover:bg-gray-700 md:hover:bg-transparent md:hover:text-white md:p-0 transition-colors duration-200">Blog</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="bg-cover bg-center bg-no-repeat bg-gray-700 bg-blend-multiply" style="background-image: url('{{ asset('images/rice-terraces.jpg') }}');">
        <div class="px-4 mx-auto max-w-screen-xl text-center py-24 lg:py-56">
            <img class="h-auto max-w-sm mx-auto" src="{{ asset('images/pasya.png') }}" alt="PASYA Logo"/>
            <h1 class="mb-4 text-2xl font-extrabold tracking-tight leading-none text-green-500 md:text-2xl lg:text-3xl dark:text-white">
                PASYA: Predictive Analytics for Sustainable Agriculture and Yield Advancement
            </h1>
            <p class="mb-8 text-md font-normal text-gray-300 lg:text-xl sm:px-16 lg:px-42 dark:text-gray-400">
                A Decision Support System for Highland Vegetable Agriculture in Benguet
            </p>
            <div class="flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-y-0">
                <a href="{{ route('login') }}" class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-white rounded-lg bg-green-500 hover:bg-green-600 focus:ring-4 focus:ring-green-300 dark:focus:ring-green-900">
                    Get started
                    <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                    </svg>
                </a>
                <a href="#features" class="inline-flex justify-center hover:text-gray-900 items-center py-3 px-5 sm:ms-4 text-base font-medium text-center text-white rounded-lg border border-white hover:bg-gray-100 focus:ring-4 focus:ring-gray-400">
                    Learn how it works
                </a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="bg-white dark:bg-gray-900">
        <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-4">
            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 md:p-12 mb-8">
                <h1 class="text-gray-900 dark:text-white text-3xl md:text-4xl font-extrabold mb-2">
                    Where Tradition Meets Technology: Data-Driven Farming for Benguet's Future
                </h1>
            </div>
            
            <!-- Features Grid -->
            <div id="features" class="grid md:grid-cols-2 gap-8">
                <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-8 md:p-12">
                    <h2 class="text-gray-900 dark:text-white text-3xl font-extrabold mb-2">Crop Prediction Dashboard</h2>
                    <p class="text-lg font-normal text-gray-500 dark:text-gray-400 mb-4">
                        Visual forecasts of crop yields based on historical and seasonal data
                    </p>
                    <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-500 hover:underline font-medium text-lg inline-flex items-center">
                        Read more
                        <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                        </svg>
                    </a>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-8 md:p-12">
                    <h2 class="text-gray-900 dark:text-white text-3xl font-extrabold mb-2">Seasonal Trend Analysis</h2>
                    <p class="text-lg font-normal text-gray-500 dark:text-gray-400 mb-4">
                        Insights on optimal planting and harvesting periods
                    </p>
                    <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-500 hover:underline font-medium text-lg inline-flex items-center">
                        Read more
                        <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="bg-white dark:bg-gray-900">
        <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-4">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-green-200 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-8 md:p-12">
                    <h2 class="text-gray-900 dark:text-white text-3xl font-extrabold mb-2">10,000+ Hectares monitored</h2>
                </div>
                <div class="bg-green-200 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-8 md:p-12">
                    <h2 class="text-gray-900 dark:text-white text-3xl font-extrabold mb-2">+20% income</h2>
                </div>
                <div class="bg-green-200 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-8 md:p-12">
                    <h2 class="text-gray-900 dark:text-white text-3xl font-extrabold mb-2">15% reduced food waste</h2>
                </div>
            </div>
            
            <!-- Info Card -->
            <a href="{{ route('login') }}" class="flex flex-col items-center bg-white border border-gray-200 rounded-lg shadow-sm md:flex-row md:max-screen-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 mt-4">
                <img class="object-cover w-full rounded-t-lg h-96 md:h-auto md:w-48 md:rounded-none md:rounded-s-lg" src="{{ asset('images/irrigation-stock.jpg') }}" alt="Agriculture"/>
                <div class="flex flex-col justify-between p-4 leading-normal">
                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">
                        Our models are trained on 10+ years of regional data, achieving over 90% accuracy in trend forecasting for key highland vegetables
                    </p>
                </div>
            </a>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section id="contact" class="bg-cover bg-center bg-no-repeat bg-gray-400 bg-blend-multiply" style="background-image: url('{{ asset('images/farming.jpg') }}');">
        <div class="py-8 px-4 mx-auto max-w-screen-xl text-center lg:py-16 z-10 relative">
            <p class="mb-8 text-xl font-extrabold text-white lg:text-xl sm:px-32 lg:px-60 dark:text-gray-200">
                Ready Cultivate the Future of Farming in Benguet?
            </p>
            <h1 class="mb-4 text-4xl font-extrabold tracking-tight leading-none text-white md:text-5xl lg:text-6xl dark:text-white">
                Join hundreds of farmers and<br>experts already using PASYA to<br>make smarter decisions
            </h1>
            <form class="w-full max-w-md mx-auto" action="{{ route('login') }}" method="GET">
                <label for="default-email" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Email sign-up</label>
                <div class="relative">
                    <div class="absolute inset-y-0 rtl:inset-x-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 16">
                            <path d="m10.036 8.278 9.258-7.79A1.979 1.979 0 0 0 18 0H2A1.987 1.987 0 0 0 .641.541l9.395 7.737Z"/>
                            <path d="M11.241 9.817c-.36.275-.801.425-1.255.427-.428 0-.845-.138-1.187-.395L0 2.6V14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2.5l-8.759 7.317Z"/>
                        </svg>
                    </div>
                    <input type="email" id="default-email" name="email" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your email here..." required />
                    <button type="submit" class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Sign up</button>
                </div>
            </form>
        </div>
        <div class="bg-gradient-to-b from-blue-50 to-transparent dark:from-blue-900 w-full h-full absolute top-0 left-0 z-0"></div>
    </section>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-900">
        <div class="mx-auto w-full max-w-screen-xl p-4 py-6 lg:py-8">
            <div class="md:flex md:justify-between">
                <div class="mb-6 md:mb-0">
                    <a href="{{ url('/') }}" class="flex items-center">
                        <img src="{{ asset('images/pasya.png') }}" class="h-16 me-3" alt="PASYA Logo" />
                        <img src="{{ asset('images/titleh.png') }}" class="h-16 me-3" alt="PASYA" />
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-8 sm:gap-6 sm:grid-cols-3">
                    <div>
                        <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Navigation:</h2>
                        <ul class="text-gray-500 dark:text-gray-400 font-medium">
                            <li class="mb-4">
                                <a href="#home" class="hover:underline">Home</a>
                            </li>
                            <li>
                                <a href="#about" class="hover:underline">About us</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <ul class="text-gray-500 dark:text-gray-400 font-medium">
                            <li class="mb-4">
                                <a href="#features" class="hover:underline">Work with us</a>
                            </li>
                            <li>
                                <a href="#contact" class="hover:underline">Blog</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Legal</h2>
                        <ul class="text-gray-500 dark:text-gray-400 font-medium">
                            <li class="mb-4">
                                <a href="#" class="hover:underline">Privacy Policy</a>
                            </li>
                            <li>
                                <a href="#" class="hover:underline">Terms &amp; Conditions</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <hr class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8" />
            <div class="sm:flex sm:items-center sm:justify-between">
                <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">
                    © 2025 <a href="{{ url('/') }}" class="hover:underline">PASYA</a>. All Rights Reserved.
                </span>
            </div>
        </div>
    </footer>

    <!-- Flowbite JavaScript for mobile menu toggle -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
</body>
</html>
