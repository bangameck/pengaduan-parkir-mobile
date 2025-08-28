{{-- Komponen ini menerima satu variabel: $media --}}

<a href="{{ Storage::url($media->file_path) }}" data-lity
    class="block w-full h-full bg-black rounded-md hover:opacity-80 transition-opacity">
    @if ($media->file_type == 'video')
        {{-- Jika file adalah video, gunakan tag <video> --}}
        <video src="{{ Storage::url($media->file_path) }}" class="w-full h-full object-cover" preload="metadata" muted loop
            playsinline></video>
    @else
        {{-- Jika file adalah gambar, gunakan tag <img> --}}
        <img src="{{ Storage::url($media->file_path) }}" alt="Bukti Laporan" class="w-full h-full object-cover">
    @endif
</a>
