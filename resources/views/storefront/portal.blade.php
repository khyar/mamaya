@extends('layouts.storefront')

@section('title', 'Mamaya Super App - Food, Tickets, & Jastip')

@section('content')
<style>
    /* Motion Background Keyframes */
    @keyframes blob-drift-1 {
        0% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(20vw, -15vh) scale(1.1); }
        66% { transform: translate(-15vw, 20vh) scale(0.9); }
        100% { transform: translate(0, 0) scale(1); }
    }
    @keyframes blob-drift-2 {
        0% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(-20vw, 15vh) scale(0.95); }
        66% { transform: translate(15vw, -20vh) scale(1.05); }
        100% { transform: translate(0, 0) scale(1); }
    }
    .motion-blob-1 { animation: blob-drift-1 8s infinite ease-in-out; }
    .motion-blob-2 { animation: blob-drift-2 10s infinite ease-in-out reverse; }
</style>

{{-- Container: scrolls naturally on mobile, locked & fixed on desktop --}}
<div class="relative w-full min-h-[calc(100vh-80px)] lg:h-screen lg:fixed lg:top-0 lg:left-0 lg:z-50 lg:overflow-hidden bg-canvas flex flex-col justify-center items-center py-[48px] lg:py-0">
    
    {{-- Close Button for Fixed Desktop View (optional navigation back/exit if needed, but usually portal is the root) --}}
    <div class="absolute top-[24px] left-[24px] lg:top-[40px] lg:left-[40px] z-50 hidden lg:block">
        <a href="/" class="text-[24px] font-bold tracking-tight text-ink">Mamaya.</a>
    </div>

    {{-- Motion Background Elements (Soft & Elegant) --}}
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="motion-blob-1 absolute top-[0%] left-[0%] w-[100vw] lg:w-[50vw] h-[100vw] lg:h-[50vw] rounded-full bg-blue-200 blur-[60px] lg:blur-[80px] mix-blend-multiply opacity-60"></div>
        <div class="motion-blob-2 absolute bottom-[10%] right-[10%] w-[80vw] lg:w-[40vw] h-[80vw] lg:h-[40vw] rounded-full bg-amber-200 blur-[60px] lg:blur-[80px] mix-blend-multiply opacity-60"></div>
        <div class="motion-blob-1 absolute top-[20%] right-[20%] w-[70vw] lg:w-[35vw] h-[70vw] lg:h-[35vw] rounded-full bg-purple-200 blur-[60px] lg:blur-[80px] mix-blend-multiply opacity-60" style="animation-delay: -3s;"></div>
        <div class="motion-blob-2 absolute bottom-[0%] left-[10%] w-[90vw] lg:w-[45vw] h-[90vw] lg:h-[45vw] rounded-full bg-pink-200 blur-[60px] lg:blur-[80px] mix-blend-multiply opacity-60" style="animation-delay: -5s;"></div>
    </div>

    {{-- Content Wrapper --}}
    <div class="relative z-10 w-full px-4 sm:px-6 lg:px-8 max-w-[1280px] mx-auto flex flex-col justify-center items-center h-full max-h-none lg:max-h-[900px] py-[24px]">
        
        <div class="text-center max-w-[800px] mx-auto mb-[32px] lg:mb-[48px] fade-in-up">
            <h1 class="text-[32px] sm:text-[40px] lg:text-[56px] font-bold tracking-tight text-ink mb-[12px] lg:mb-[16px] leading-[1.1]">
                Satu Aplikasi.<br>Semua Kebutuhan Anda.
            </h1>
            <p class="text-[16px] lg:text-[20px] text-muted font-normal max-w-[500px] mx-auto">
                Pilih layanan premium kami hari ini.
            </p>
        </div>

        {{-- Cards Grid (Vertical on mobile, Compact row on desktop) --}}
        <div class="w-full grid grid-cols-1 md:grid-cols-3 gap-[16px] lg:gap-[24px] fade-in-up flex-1 max-h-none lg:max-h-[500px]" style="animation-delay: 0.1s">
            
            {{-- Food Card --}}
            <a href="{{ route('food.home') }}" class="group block relative overflow-hidden rounded-[20px] lg:rounded-[24px] shadow-sm transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 min-h-[220px] lg:min-h-0 lg:h-full">
                {{-- Background Illustration --}}
                <img src="{{ asset('images/portal/food.jpg') }}" alt="Food" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                {{-- Gradient Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-black/10 z-10"></div>
                
                <div class="relative z-20 h-full flex flex-col justify-between p-[24px] lg:p-[32px]">
                    <div class="w-[48px] h-[48px] lg:w-[64px] lg:h-[64px] rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30 text-white">
                        <svg class="w-6 h-6 lg:w-8 lg:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div class="text-left mt-auto">
                        <h2 class="text-[20px] lg:text-[28px] font-bold text-white mb-[4px] lg:mb-[8px] group-hover:underline">Mamaya Food</h2>
                        <p class="text-[13px] lg:text-[15px] text-white/80 leading-snug line-clamp-2">Pre-order masakan rumahan otentik, asinan segar, dan dessert.</p>
                    </div>
                </div>
            </a>

            {{-- Tickets Card --}}
            <a href="{{ route('tickets.index') }}" class="group block relative overflow-hidden rounded-[20px] lg:rounded-[24px] shadow-sm transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 min-h-[220px] lg:min-h-0 lg:h-full">
                {{-- Background Illustration --}}
                <img src="{{ asset('images/portal/tickets.jpg') }}" alt="Tickets" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                {{-- Gradient Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-black/10 z-10"></div>
                
                <div class="relative z-20 h-full flex flex-col justify-between p-[24px] lg:p-[32px]">
                    <div class="w-[48px] h-[48px] lg:w-[64px] lg:h-[64px] rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30 text-white">
                        <svg class="w-6 h-6 lg:w-8 lg:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    </div>
                    <div class="text-left mt-auto">
                        <h2 class="text-[20px] lg:text-[28px] font-bold text-white mb-[4px] lg:mb-[8px] group-hover:underline">Tickets</h2>
                        <p class="text-[13px] lg:text-[15px] text-white/80 leading-snug line-clamp-2">Ikuti war tiket konser dan event artis tanpa ribet.</p>
                    </div>
                </div>
            </a>

            {{-- Jastip Card --}}
            <a href="{{ route('jastip.index') }}" class="group block relative overflow-hidden rounded-[20px] lg:rounded-[24px] shadow-sm transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 min-h-[220px] lg:min-h-0 lg:h-full">
                {{-- Background Illustration --}}
                <img src="{{ asset('images/portal/jastip.jpg') }}" alt="Jastip" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                {{-- Gradient Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-black/10 z-10"></div>
                
                <div class="relative z-20 h-full flex flex-col justify-between p-[24px] lg:p-[32px]">
                    <div class="w-[48px] h-[48px] lg:w-[64px] lg:h-[64px] rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30 text-white">
                        <svg class="w-6 h-6 lg:w-8 lg:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="text-left mt-auto">
                        <h2 class="text-[20px] lg:text-[28px] font-bold text-white mb-[4px] lg:mb-[8px] group-hover:underline">Mamaya Jastip</h2>
                        <p class="text-[13px] lg:text-[15px] text-white/80 leading-snug line-clamp-2">Titip beli barang luar negeri, original dan transparan.</p>
                    </div>
                </div>
            </a>

        </div>
    </div>
</div>
@endsection
