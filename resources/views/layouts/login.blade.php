<x-guest-layout>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <h2 class="font-display text-2xl font-semibold text-gray-900 mb-1">Welcome back</h2>
        <p class="text-sm text-gray-500 mb-6">Sign in to your account</p>

        @if (session('status'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg mb-5">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400 @error('email') border-red-400 @enderror">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-hotel-600 hover:underline">Forgot password?</a>
                    @endif
                </div>
                <input type="password" name="password" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400 @error('password') border-red-400 @enderror">
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember"
                       class="w-4 h-4 text-hotel-600 rounded border-gray-300">
                <label for="remember" class="text-sm text-gray-600">Remember me</label>
            </div>
            <button type="submit"
                    class="w-full bg-hotel-600 hover:bg-hotel-700 text-white font-medium py-2.5 rounded-lg text-sm transition-colors mt-2">
                Sign in
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-100 text-center">
            <p class="text-sm text-gray-500">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-hotel-600 hover:underline font-medium">Create one</a>
            </p>
        </div>
    </div>
</x-guest-layout>
