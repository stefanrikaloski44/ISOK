<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Grand Horizon Hotel')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        hotel: {
                            50:'#fdf8f0',100:'#faefd9',200:'#f4daa8',
                            300:'#ecc06b',400:'#e4a030',500:'#d4891a',
                            600:'#b86b12',700:'#964f12',800:'#7a3e15',900:'#663415',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=playfair-display:400,600,700|inter:300,400,500,600" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

<!-- Navbar -->
<nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-hotel-600 flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                </svg>
            </div>
            <span class="font-display font-bold text-gray-900">Grand Horizon</span>
        </a>
        <div class="flex items-center gap-6">
            <a href="{{ route('public.rooms') }}" class="text-sm text-gray-600 hover:text-hotel-700 transition-colors">Rooms</a>
            @auth
                <a href="{{ route('my.reservations') }}" class="text-sm text-gray-600 hover:text-hotel-700 transition-colors">My Reservations</a>
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="text-sm text-hotel-600 font-medium hover:underline">Admin Panel</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button class="text-sm text-gray-500 hover:text-red-600 transition-colors">Sign out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-hotel-700">Sign in</a>
                <a href="{{ route('register') }}" class="text-sm bg-hotel-600 hover:bg-hotel-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Register
                </a>
            @endauth
        </div>
    </div>
</nav>

<!-- Flash messages -->
@if(session('success'))
    <div class="max-w-6xl mx-auto w-full px-6 pt-4">
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    </div>
@endif
@if(session('error'))
    <div class="max-w-6xl mx-auto w-full px-6 pt-4">
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    </div>
@endif

<!-- Page content grows to fill space, pushing footer down -->
<main class="flex-1">
    @yield('content')
</main>

<footer class="bg-gray-900 text-gray-400 text-sm py-10">
    <div class="max-w-6xl mx-auto px-6 text-center">
        <p class="font-display text-white text-lg mb-1">Grand Horizon</p>
        <p class="text-xs">© {{ date('Y') }} All rights reserved.</p>
    </div>
</footer>

</body>
</html>
