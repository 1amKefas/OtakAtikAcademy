<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat</title>
    <style>
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Helvetica', sans-serif; width: 100%; height: 100%; }
        
        .bg-image {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
        }

        .element {
            position: absolute;
            transform: translate(-50%, -50%); /* Pivot Center */
            white-space: nowrap;
        }
    </style>
</head>
<body>
    {{-- Background --}}
    <img src="{{ $background_image }}" class="bg-image">

    {{-- Render Elements --}}
    @foreach($elements as $el)
        <div class="element" style="
            left: {{ $el['x'] }}%; 
            top: {{ $el['y'] }}%; 
            
            @if($el['type'] !== 'image')
                font-family: '{{ $el['font'] ?? 'Helvetica' }}';
                font-size: {{ $el['size'] ?? 12 }}pt;
                color: {{ $el['color'] ?? '#000' }};
                text-align: {{ $el['align'] ?? 'center' }};
            @endif
        ">
            @if($el['type'] === 'image')
                {{-- Gunakan storage_path untuk PDF --}}
                <img src="{{ storage_path('app/public/' . $el['path']) }}" style="width: {{ $el['w'] }}px; height: {{ $el['h'] }}px; object-fit: contain;">
            @elseif($el['type'] === 'dynamic')
                {{-- Ambil value dari array variables --}}
                {{ $variables[$el['content']] ?? 'Error' }}
            @else
                {{-- Teks Statis --}}
                {{ $el['text'] }}
            @endif
        </div>
    @endforeach
</body>
</html>