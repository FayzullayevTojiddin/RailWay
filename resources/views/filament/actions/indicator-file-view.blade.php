@php
    $fileUrl = asset('storage/' . $record->file);
    $ext = strtolower(pathinfo($record->file, PATHINFO_EXTENSION));
@endphp

@if(in_array($ext, ['jpg','jpeg','png','webp']))
    <img src="{{ $fileUrl }}" class="w-full h-auto object-contain" alt="Image preview">
@elseif($ext === 'pdf')
    <iframe src="{{ $fileUrl }}" class="w-full h-[80vh] border-none"></iframe>
@else
    <div class="text-center py-8">
        <a href="{{ $fileUrl }}" class="text-xl font-semibold text-primary-600 underline" download>
            Faylni yuklab olish
        </a>
    </div>
@endif