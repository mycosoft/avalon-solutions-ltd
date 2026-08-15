<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Avalon Solutions - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#e6f3f8',
                            100: '#cce7f1',
                            200: '#99cfe3',
                            300: '#66b7d5',
                            400: '#339fc7',
                            500: '#0a6699',
                            600: '#085980',
                            700: '#064d66',
                            800: '#04304d',
                            900: '#021333',
                        },
                        secondary: {
                            50: '#f5f5f5',
                            100: '#e5e5e5',
                            200: '#cccccc',
                            300: '#b3b3b3',
                            400: '#999999',
                            500: '#808080',
                            600: '#666666',
                            700: '#4d4d4d',
                            800: '#333333',
                            900: '#1a1a1a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-gray-400 via-gray-500 to-gray-600">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-[#0a6699] p-8 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Avalon Solutions" class="h-24 mx-auto mb-4 object-contain">
                <h1 class="text-2xl font-bold text-white">Avalon Solutions</h1>
                <p class="text-blue-200 text-sm mt-1">Management Portal</p>
            </div>

            <div class="p-8">
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-5">
                        <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email Address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0a6699] focus:border-[#0a6699] transition duration-200"
                                placeholder="Enter your email" required autofocus>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" id="password" name="password"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0a6699] focus:border-[#0a6699] transition duration-200"
                                placeholder="Enter your password" required>
                        </div>
                    </div>

                    <div class="mb-6 flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" id="remember"
                                class="w-4 h-4 text-[#0a6699] border-gray-300 rounded focus:ring-[#0a6699]">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm text-[#0a6699] hover:text-[#085980]">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit"
                        class="w-full bg-[#0a6699] text-white py-3 rounded-lg font-semibold hover:bg-[#085980] focus:outline-none focus:ring-2 focus:ring-[#0a6699] focus:ring-offset-2 transition duration-200">
                        <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                    </button>
                </form>
            </div>

            <div class="bg-gray-50 px-8 py-4 text-center">
                <p class="text-gray-500 text-sm">
                    &copy; {{ date('Y') }} Avalon Solutions Ltd. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
