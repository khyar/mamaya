<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dapur Mamaya - Pre-Order makanan rumahan, Tiket, dan Jastip.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <title>@yield('title', 'Dapur Mamaya - Super App')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Minimalist utility animations */
        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .slide-down { animation: slideDown 0.4s ease-out; }
        @keyframes fadeInUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .fade-in-up { animation: fadeInUp 0.5s ease-out; }
    </style>
</head>
<body class="bg-canvas text-body font-sans antialiased min-h-screen flex flex-col">

    {{-- Campaign Banners (Top Strip) --}}
    @if(isset($campaigns) && $campaigns->count() > 0)
    <div x-data="{ currentBanner: 0, banners: {{ $campaigns->count() }} }" x-init="setInterval(() => { currentBanner = (currentBanner + 1) % banners }, 4000)" class="relative h-10 overflow-hidden bg-surface-soft border-b border-hairline text-ink">
        @foreach($campaigns as $index => $campaign)
        <div x-show="currentBanner === {{ $index }}"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 w-full h-full flex items-center justify-center px-4 text-[13px] font-medium">
            <div class="max-w-[1280px] mx-auto flex items-center justify-center gap-2">
                <span class="truncate">{{ $campaign->content }}</span>
                @if($campaign->link_url)
                <a href="{{ e($campaign->link_url) }}" class="underline font-semibold hover:text-primary ml-1 shrink-0">Lihat detail</a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Top Navigation --}}
    <nav class="bg-canvas sticky top-0 z-50 border-b border-hairline" x-data="{ mobileMenu: false }">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-[80px]">
                {{-- Logo (Flush Left) --}}
                <a href="/" class="text-[24px] font-bold tracking-tight text-ink">Mamaya.</a>

                {{-- Desktop Nav (Center) --}}
                <div class="hidden md:flex items-center h-full">
                    <a href="{{ route('food.home') }}" class="flex items-center h-full px-4 text-[16px] font-medium transition-colors border-b-2 {{ request()->routeIs('food.*') ? 'text-ink border-ink' : 'text-muted border-transparent hover:text-ink hover:border-hairline' }}">
                        Food
                    </a>
                    <a href="{{ route('tickets.index') }}" class="flex items-center h-full px-4 text-[16px] font-medium transition-colors border-b-2 {{ request()->routeIs('tickets.*') ? 'text-ink border-ink' : 'text-muted border-transparent hover:text-ink hover:border-hairline' }}">
                        Tickets
                    </a>
                    <a href="{{ route('jastip.index') }}" class="flex items-center h-full px-4 text-[16px] font-medium transition-colors border-b-2 {{ request()->routeIs('jastip.*') ? 'text-ink border-ink' : 'text-muted border-transparent hover:text-ink hover:border-hairline' }}">
                        Jastip
                    </a>
                </div>

                {{-- Utilities (Right) --}}
                <div class="flex items-center gap-4">
                    <a href="{{ route('track.show') }}" class="text-[14px] font-medium text-ink hover:text-muted transition-colors hidden sm:block">Lacak Pesanan</a>
                    
                    {{-- Cart Button --}}
                    @php $cartCount = \App\Http\Controllers\CartController::getCartCount(); @endphp
                    <a href="{{ route('food.cart.index') }}" class="relative flex items-center justify-center w-10 h-10 rounded-full border border-hairline hover:shadow-md transition-shadow">
                        <svg class="w-5 h-5 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center ring-2 ring-white">{{ $cartCount }}</span>
                        @endif
                    </a>

                    {{-- Mobile menu toggle --}}
                    <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-full border border-hairline hover:shadow-md transition-shadow">
                        <svg x-show="!mobileMenu" class="w-5 h-5 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg x-show="mobileMenu" x-cloak class="w-5 h-5 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenu" x-cloak x-transition class="md:hidden border-t border-hairline bg-canvas">
            <div class="px-4 py-4 space-y-2">
                <a href="{{ route('food.home') }}" class="block px-4 py-3 rounded-lg text-[16px] font-medium text-ink hover:bg-surface-soft">Mamaya Food</a>
                <a href="{{ route('tickets.index') }}" class="block px-4 py-3 rounded-lg text-[16px] font-medium text-ink hover:bg-surface-soft">Mamaya Tickets</a>
                <a href="{{ route('jastip.index') }}" class="block px-4 py-3 rounded-lg text-[16px] font-medium text-ink hover:bg-surface-soft">Mamaya Jastip</a>
                <hr class="border-hairline my-2">
                <a href="{{ route('track.show') }}" class="block px-4 py-3 rounded-lg text-[16px] font-medium text-ink hover:bg-surface-soft">Lacak Pesanan</a>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition class="slide-down fixed top-24 left-1/2 -translate-x-1/2 z-50 max-w-sm w-full mx-4">
        <div class="bg-ink text-white px-5 py-4 rounded-md shadow-lg flex items-start gap-3">
            <svg class="w-5 h-5 text-green-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="text-[14px] font-medium leading-relaxed">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition class="slide-down fixed top-24 left-1/2 -translate-x-1/2 z-50 max-w-sm w-full mx-4">
        <div class="bg-ink text-white px-5 py-4 rounded-md shadow-lg flex items-start gap-3">
            <svg class="w-5 h-5 text-primary-error-text shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span class="text-[14px] font-medium leading-relaxed">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    {{-- Main Content --}}
    <main class="flex-1 relative">
        {{-- Dynamic elegant gradient from the navbar downwards --}}
        @hasSection('top-gradient')
            @yield('top-gradient')
        @else
            <div class="absolute inset-x-0 top-0 h-[400px] bg-gradient-to-b from-surface-soft to-transparent pointer-events-none -z-10"></div>
        @endif
        
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-surface-soft border-t border-hairline mt-auto">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-[48px]">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-[24px]">
                <div>
                    <h3 class="text-ink font-medium text-[16px] mb-4">Layanan Mamaya</h3>
                    <ul class="space-y-3 text-[14px]">
                        <li><a href="{{ route('food.home') }}" class="text-muted hover:text-ink transition-colors">Food & Catering</a></li>
                        <li><a href="{{ route('tickets.index') }}" class="text-muted hover:text-ink transition-colors">Event Tickets</a></li>
                        <li><a href="{{ route('jastip.index') }}" class="text-muted hover:text-ink transition-colors">Jasa Titip (Jastip)</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-ink font-medium text-[16px] mb-4">Dukungan</h3>
                    <ul class="space-y-3 text-[14px]">
                        <li><a href="{{ route('track.show') }}" class="text-muted hover:text-ink transition-colors">Lacak Pesanan</a></li>
                        <li><a href="#" class="text-muted hover:text-ink transition-colors">Cara Pemesanan</a></li>
                        <li><a href="#" class="text-muted hover:text-ink transition-colors">Pusat Bantuan</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-ink font-medium text-[16px] mb-4">Hubungi Kami</h3>
                    <ul class="space-y-3 text-[14px]">
                        <li><span class="text-muted">WhatsApp: {{ config('app.whatsapp_number', '6281234567890') }}</span></li>
                        <li><span class="text-muted">Instagram: @dapurmamaya</span></li>
                        <li><span class="text-muted">Email: hello@mamaya.id</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        {{-- Legal Band --}}
        <div class="border-t border-hairline">
            <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="text-[14px] text-muted font-medium">
                    &copy; {{ date('Y') }} Dapur Mamaya. Hak Cipta Dilindungi.
                </div>
                <div class="flex gap-4 text-[14px] text-muted font-medium">
                    <a href="#" class="hover:text-ink">Privasi</a>
                    <span>·</span>
                    <a href="#" class="hover:text-ink">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
