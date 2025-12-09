<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desain Sertifikat - {{ $course->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .draggable { position: absolute; cursor: move; user-select: none; }
        .draggable:hover { outline: 2px dashed #3b82f6; }
        .canvas-area { 
            position: relative; 
            width: 100%; 
            aspect-ratio: 297/210; /* A4 Landscape */
            background-color: #f3f4f6;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100">

    <div class="min-h-screen flex flex-col">
        {{-- Header Admin Simple --}}
        <div class="bg-white border-b px-6 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.courses.manage') }}" class="text-gray-500 hover:text-gray-800">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <h1 class="text-xl font-bold text-gray-800">Certificate Designer: <span class="text-blue-600">{{ $course->title }}</span></h1>
            </div>
            <button onclick="saveDesign()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold shadow-md transition flex items-center gap-2">
                <i class="fas fa-save"></i> SIMPAN PERUBAHAN
            </button>
        </div>

        <div class="flex-1 p-6 overflow-hidden flex gap-6">
            
            {{-- SIDEBAR KONTROL --}}
            <div class="w-80 bg-white rounded-xl shadow-sm border p-6 overflow-y-auto h-[calc(100vh-100px)]">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Pengaturan Elemen</h3>
                
                <form action="{{ route('admin.courses.certificate.update', $course->id) }}" method="POST" enctype="multipart/form-data" id="designForm">
                    @csrf
                    <input type="hidden" name="settings_json" id="settingsJson">

                    {{-- Uploads --}}
                    <div class="mb-6 space-y-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Background Template</label>
                            <input type="file" name="certificate_template" class="block w-full text-sm mt-1 border rounded p-1" accept="image/*">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Gambar Tanda Tangan</label>
                            <input type="file" name="signature_image" class="block w-full text-sm mt-1 border rounded p-1" accept="image/*">
                        </div>
                    </div>

                    {{-- Text Controls --}}
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Kata Pengantar</label>
                            <input type="text" class="w-full border rounded p-2 text-sm" 
                                   value="{{ $settings['message']['text'] ?? '' }}" 
                                   oninput="updateText('message', this.value)">
                        </div>
                        
                        <hr class="border-gray-100">

                        <div class="bg-blue-50 p-3 rounded text-xs text-blue-800">
                            <i class="fas fa-hand-pointer mr-1"></i> <strong>Tips:</strong> Geser langsung elemen pada preview sertifikat di sebelah kanan untuk mengatur posisi.
                        </div>
                    </div>
                </form>
            </div>

            {{-- CANVAS PREVIEW --}}
            <div class="flex-1 flex justify-center items-start overflow-auto">
                <div class="canvas-area rounded-lg" id="certificateCanvas">
                    
                    {{-- Background --}}
                    @if($course->certificate_template)
                        <img src="{{ Storage::url($course->certificate_template) }}" class="absolute inset-0 w-full h-full object-cover pointer-events-none">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                            <div class="text-center">
                                <i class="fas fa-image text-4xl mb-2"></i>
                                <p>Upload Template Background Dulu</p>
                            </div>
                        </div>
                    @endif

                    {{-- Draggable Elements --}}
                    {{-- 1. Message --}}
                    <div id="el-message" class="draggable text-center" 
                         style="top: {{ $settings['message']['y'] }}%; left: {{ $settings['message']['x'] }}%; transform: translate(-50%, -50%); color: {{ $settings['message']['color'] }}; font-size: 1.5vw;">
                        {{ $settings['message']['text'] }}
                    </div>

                    {{-- 2. Nama Siswa --}}
                    <div id="el-student_name" class="draggable font-bold text-center uppercase" 
                         style="top: {{ $settings['student_name']['y'] }}%; left: {{ $settings['student_name']['x'] }}%; transform: translate(-50%, -50%); color: {{ $settings['student_name']['color'] }}; font-size: 3vw;">
                        [NAMA SISWA]
                    </div>

                    {{-- 3. Judul Course --}}
                    <div id="el-course_name" class="draggable font-bold text-center" 
                         style="top: {{ $settings['course_name']['y'] }}%; left: {{ $settings['course_name']['x'] }}%; transform: translate(-50%, -50%); color: {{ $settings['course_name']['color'] }}; font-size: 2.2vw;">
                        {{ $course->title }}
                    </div>

                    {{-- 4. Tanggal --}}
                    <div id="el-date" class="draggable text-center" 
                         style="top: {{ $settings['date']['y'] }}%; left: {{ $settings['date']['x'] }}%; transform: translate(-50%, -50%); color: {{ $settings['date']['color'] }}; font-size: 1.1vw;">
                        {{ date('d F Y') }}
                    </div>

                    {{-- 5. Kode --}}
                    <div id="el-code" class="draggable text-center font-mono" 
                         style="top: {{ $settings['code']['y'] }}%; left: {{ $settings['code']['x'] }}%; transform: translate(-50%, -50%); color: {{ $settings['code']['color'] }}; font-size: 1vw;">
                        NO: CRT/{{ date('Y') }}/XXXX
                    </div>

                    {{-- 6. Tanda Tangan --}}
                    @if($course->signature_image)
                        <div id="el-signature" class="draggable" 
                             style="top: {{ $settings['signature']['y'] ?? 80 }}%; left: {{ $settings['signature']['x'] ?? 50 }}%; transform: translate(-50%, -50%);">
                            <img src="{{ Storage::url($course->signature_image) }}" style="width: 12vw; pointer-events: none;">
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Javascript Drag Logic --}}
    <script>
        let settings = @json($settings);
        const draggables = document.querySelectorAll('.draggable');
        let activeItem = null;
        let startX, startY, startLeft, startTop;

        draggables.forEach(item => {
            item.addEventListener('mousedown', startDrag);
        });

        function startDrag(e) {
            activeItem = this;
            e.preventDefault();
            const parent = activeItem.parentElement;
            
            startX = e.clientX;
            startY = e.clientY;
            
            startLeft = (activeItem.offsetLeft / parent.offsetWidth) * 100;
            startTop = (activeItem.offsetTop / parent.offsetHeight) * 100;

            document.addEventListener('mousemove', drag);
            document.addEventListener('mouseup', stopDrag);
        }

        function drag(e) {
            if (!activeItem) return;
            e.preventDefault();
            const parent = activeItem.parentElement;
            
            // Hitung delta dalam persen
            let deltaX = ((e.clientX - startX) / parent.offsetWidth) * 100;
            let deltaY = ((e.clientY - startY) / parent.offsetHeight) * 100;

            let newLeft = startLeft + deltaX;
            let newTop = startTop + deltaY;

            // Update CSS
            activeItem.style.left = newLeft + '%';
            activeItem.style.top = newTop + '%';

            // Update settings object
            let key = activeItem.id.replace('el-', '');
            if(settings[key]) {
                settings[key].x = newLeft;
                settings[key].y = newTop;
            }
        }

        function stopDrag() {
            activeItem = null;
            document.removeEventListener('mousemove', drag);
            document.removeEventListener('mouseup', stopDrag);
        }

        function updateText(key, val) {
            const el = document.getElementById('el-' + key);
            if(el) el.innerText = val;
            if(settings[key]) settings[key].text = val;
        }

        function saveDesign() {
            document.getElementById('settingsJson').value = JSON.stringify(settings);
            document.getElementById('designForm').submit();
        }
    </script>
</body>
</html>