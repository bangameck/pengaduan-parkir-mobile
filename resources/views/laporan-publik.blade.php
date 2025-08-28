@extends('layouts.public')

@section('title', 'Daftar Laporan Publik')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 my-8 sm:my-12">

        {{-- Header Halaman dengan Tombol Kembali --}}
        <div class="flex items-center mb-6">
            <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-800 p-2 -ml-2 rounded-full transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800 ml-2">
                Daftar Laporan Publik

                {{-- Menampilkan status filter jika ada, dengan format yang lebih cantik --}}
                @if ($status)
                    <span
                        class="text-lg font-medium text-blue-600">({{ Str::ucfirst(str_replace('_', ' ', $status)) }})</span>
                @endif
            </h1>
        </div>

        {{--
    |--------------------------------------------------------------------------
    | Memanggil Komponen Livewire
    |--------------------------------------------------------------------------
    |
    | Di sinilah semua "keajaiban" (daftar laporan, search, load more)
    | terjadi. Halaman ini hanya perlu memanggil component-nya dan
    | mengirimkan filter status jika ada.
    |
    --}}
        <livewire:public-report-list :status="$status" />

    </div>
@endsection
