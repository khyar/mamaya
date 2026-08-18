@extends('layouts.storefront')
@section('title', '404 - Halaman Tidak Ditemukan | Dapur Mamaya')

@section('content')
<div class="min-h-[60vh] flex flex-col items-center justify-center px-4 py-16 text-center">
    <div class="mb-8 relative">
        <div class="w-32 h-32 bg-brand-100 rounded-full flex items-center justify-center mx-auto shadow-inner">
            <span class="text-6xl">🍽️</span>
        </div>
        <div class="absolute -bottom-2 -right-2 bg-white rounded-full p-2 shadow-md">
            <span class="text-2xl">❓</span>
        </div>
    </div>
    
    <h1 class="text-4xl md:text-5xl font-extrabold text-warm-900 tracking-tight mb-4">Waduh, Kesasar Ya?</h1>
    <p class="text-lg text-warm-500 max-w-md mx-auto mb-8 leading-relaxed">
        Halaman yang Anda cari sepertinya tidak ada di menu Dapur Mamaya. Mungkin sudah dihapus atau URL-nya salah.
    </p>
    
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('home') }}" class="btn-primary inline-flex items-center justify-center px-8 py-3.5 rounded-xl text-white font-semibold shadow-md gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Beranda
        </a>
        <a href="{{ route('food.products') }}" class="inline-flex items-center justify-center px-8 py-3.5 rounded-xl bg-white text-warm-700 font-semibold shadow-sm border border-warm-200 hover:bg-warm-50 hover:text-brand-600 transition-colors gap-2">
            Lihat Menu PO
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
        </a>
    </div>
</div>
@endsection
