<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Store</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 to-purple-100 min-h-screen flex flex-col">

    <div class="flex-grow flex items-center justify-center text-center p-6">
        <!-- Main Content -->
        <div>
            <div class="flex justify-center space-x-4 mb-4 text-4xl opacity-90">
                <span style="animation: float 3s infinite;">👜</span>
                <span style="animation: float 3s infinite 0.3s;">👗</span>
                <span style="animation: float 3s infinite 0.6s;">👟</span>
                <span style="animation: float 3s infinite 0.9s;">⌚</span>
            </div>

            <h1 class="text-5xl font-extrabold text-gray-800 drop-shadow-sm">
                Welcome to <span class="text-purple-600">Your Store</span>
            </h1>

            <p class="mt-3 text-gray-600 text-lg italic">
                “Style, Comfort & Quality — Delivered to You.”
            </p>

            <div class="mt-8 flex justify-center gap-4">

                <a href="{{ route('products.index') }}"
                class="px-7 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl shadow-lg hover:scale-105 hover:shadow-2xl transition-all duration-200">
                    Browse Products
                </a>

                @guest
                <a href="{{ route('login') }}"
                class="px-7 py-3 border-2 border-purple-600 text-purple-700 rounded-xl hover:bg-purple-600 hover:text-white transition-all duration-200">
                    Login
                </a>

                <a href="{{ route('register') }}"
                class="px-7 py-3 border-2 border-blue-600 text-blue-700 rounded-xl hover:bg-blue-600 hover:text-white transition-all duration-200">
                    Register
                </a>
                @endguest

                @auth
                <a href="{{ route('dashboard') }}"
                class="px-7 py-3 bg-green-600 text-white rounded-xl shadow hover:bg-green-700 transition">
                    Dashboard
                </a>
                @endauth

            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 bg-white shadow-inner border-t">
        <p class="text-gray-700 text-sm">
            Designed & Developed with 💜 by 
            <span class="font-semibold text-purple-600">Neelima Gupta</span>
        </p>
    </footer>

</body>
</html>
