<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <title>Login Admin - Dapur Mamaya</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-warm-50 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center shadow-lg">
                <span class="text-white font-bold text-2xl">DM</span>
            </div>
            <h1 class="text-2xl font-bold text-warm-900">Dapur Mamaya</h1>
            <p class="text-warm-500 text-sm mt-1">Admin Panel</p>
        </div>

        {{-- Login Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-warm-100 p-8">
            <h2 class="text-lg font-semibold text-warm-900 mb-6 text-center">Masuk ke Dashboard</h2>

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="login-email" class="block text-sm font-medium text-warm-700 mb-1.5">Email</label>
                    <input type="email" id="login-email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email"
                           class="w-full border border-warm-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                           placeholder="admin@dapurmamaya.com">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="login-password" class="block text-sm font-medium text-warm-700 mb-1.5">Password</label>
                    <div x-data="{ show: false }" class="relative">
                        <input :type="show ? 'text' : 'password'" id="login-password" name="password" required autocomplete="current-password"
                               class="w-full border border-warm-300 rounded-xl px-4 py-3 pr-12 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                               placeholder="••••••••">
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-warm-400 hover:text-warm-600">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="login-remember" name="remember" class="w-4 h-4 rounded border-warm-300 text-brand-600 focus:ring-brand-500">
                    <label for="login-remember" class="ml-2 text-sm text-warm-600">Ingat saya</label>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-brand-500 to-brand-600 text-white py-3.5 rounded-xl font-semibold text-lg hover:from-brand-600 hover:to-brand-700 transition-all shadow-lg hover:shadow-xl">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-warm-400 mt-6">&copy; {{ date('Y') }} Dapur Mamaya</p>
    </div>
</body>
</html>
