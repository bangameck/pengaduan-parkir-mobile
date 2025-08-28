@extends('layouts.mobile')
@section('title', 'Laporan Saya')
@section('content')
    {{-- Header Halaman --}}
    <x-resident-header />
    <x-page-header>Laporan Saya</x-page-header>
    <div class="p-4 sm:p-6">
        {{-- Panggil component Livewire --}}
        <livewire:resident-report-list />
    </div>
@endsection
