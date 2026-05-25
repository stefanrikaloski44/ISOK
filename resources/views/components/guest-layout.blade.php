<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Grand Horizon') }}</title>
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
<body class="h-full bg-gray-50">
<div class="min-h-screen flex">
    <!-- Left panel -->
    <div class="hidden lg:flex lg:w-96 bg-gray-900 flex-col items-center justify-center px-10 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20"
             style="background: radial-gradient(ellipse at 30% 50%, #b86b12 0%, transparent 60%)"></div>
        <div class="relative z-10 text-center">
            <div class="w-16 h-16 rounded-2xl bg-hotel-600 flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h1 class="font-display text-3xl font-bold text-white mb-2">Grand Horizon</h1>
            <p class="text-hotel-300 text-sm tracking-widest uppercase mb-8">Hotel & Resorts</p>
            <p class="text-gray-400 text-sm leading-relaxed">
                Experience luxury and comfort.<br>Book your perfect stay today.
            </p>
        </div>
        <div class="absolute bottom-8 text-center relative z-10">
            <a href="{{ url('/rooms') }}"
               class="text-hotel-400 hover:text-hotel-300 text-sm transition-colors">
                Browse available rooms →
            </a>
        </div>
    </div>

    <!-- Right panel -->
    <div class="flex-1 flex flex-col items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">
            <!-- Mobile logo -->
            <div class="lg:hidden flex items-center gap-3 mb-8 justify-center">
                <div class="w-10 h-10 rounded-xl bg-hotel-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                    </svg>
                </div>
                <span class="font-display font-bold text-gray-900">Grand Horizon</span>
            </div>

            {{ $slot }}
(
            <p class="text-center text-xs text-gray-400 mt-8">
                <a href="{{ url('/rooms') }}" class="hover:text-hotel-600 transition-colors">
                    ← Back to room listings
                </a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
