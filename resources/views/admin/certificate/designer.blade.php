<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Certificate Designer - {{ $course->title }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .canvas-container {
            width: 100%;
            aspect-ratio: 297/210; /* A4 Landscape */
            position: relative;
            background-color: #e5e7eb;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .draggable-el {
            position: absolute;
            cursor: move;
            user-select: none;
            border: 1px dashed transparent;
            padding: 2px;
            white-space: nowrap;
        }
        .draggable-el:hover, .draggable-el.active {
            border-color: #3b82f6;
            background-color: rgba(59, 130, 246, 0.1);
            z-index: 50;
        }
        /* Font Choices */
        .font-helvetica { font-family: 'Helvetica', sans-serif; }
        .font-courier { font-family: 'Courier', monospace; }
        .font-times { font-family: 'Times New Roman', serif; }
    </style>
</head>
<body class="bg-gray-100 h-screen flex flex-col">

    <div class="bg-white border-b px-6 py-3 flex justify-between items-center z-50 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.courses.manage') }}" class="text-gray-500 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="font-bold text-gray-800">Certificate Designer</h1>
        </div>
        <button onclick="submitForm()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold text-sm shadow flex items-center gap-2">
            <i class="fas fa-save"></i> SIMPAN DESAIN
        </button>
    </div>

    <div class="flex-1 flex overflow-hidden">
        
        <div class="w-80 bg-white border-r flex flex-col overflow-y-auto">
            
            <div class="p-5 border-b">
                <h3 class="font-bold text-gray-700 text-xs uppercase mb-3">1. Background Utama</h3>
                <form id="mainForm" action="{{ route('admin.courses.certificate.update', $course->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="elements_json" id="elementsJsonInput">
                    <input type="file" name="certificate_template" class="text-xs w-full border rounded p-1 mb-2">
                    <p class="text-[10px] text-gray-400">Upload JPG/PNG kosong (Tanpa teks)</p>
                </form>
            </div>

            <div class="p-5 border-b">
                <h3 class="font-bold text-gray-700 text-xs uppercase mb-3">2. Tambah Elemen</h3>
                <div class="grid grid-cols-2 gap-2">
                    <button onclick="addText('text')" class="flex items-center justify-center gap-2 p-2 bg-gray-50 hover:bg-gray-100 border rounded text-sm text-gray-600">
                        <i class="fas fa-font"></i> Teks Biasa
                    </button>
                    <button onclick="document.getElementById('assetUpload').click()" class="flex items-center justify-center gap-2 p-2 bg-gray-50 hover:bg-gray-100 border rounded text-sm text-gray-600">
                        <i class="fas fa-image"></i> Gambar/Logo
                    </button>
                </div>
                <input type="file" id="assetUpload" class="hidden" accept="image/*" onchange="uploadAsset(this)">
                
                <div class="mt-3">
                    <label class="text-[10px] font-bold text-gray-500 block mb-1">Variabel Dinamis:</label>
                    <select id="dynamicVarSelect" class="w-full text-sm border rounded p-1.5 mb-2" onchange="addText('dynamic', this.value, this.options[this.selectedIndex].text)">
                        <option value="">+ Pilih Data Siswa...</option>
                        <option value="student_name">Nama Siswa</option>
                        <option value="course_title">Judul Kursus</option>
                        <option value="date">Tanggal Lulus</option>
                        <option value="code">Nomor Sertifikat</option>
                    </select>
                </div>
            </div>

            <div id="propertiesPanel" class="p-5 hidden bg-blue-50/50 flex-1">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-blue-800 text-xs uppercase">Edit Properti</h3>
                    <button onclick="deleteActiveElement()" class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-trash"></i> Hapus</button>
                </div>

                <div id="propTextGroup" class="mb-3">
                    <label class="text-[10px] text-gray-500 font-bold">Konten Teks</label>
                    <input type="text" id="propText" class="w-full border rounded p-1 text-sm" oninput="updateActiveElement('text', this.value)">
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="text-[10px] text-gray-500 font-bold">Font</label>
                        <select id="propFont" class="w-full border rounded p-1 text-sm" onchange="updateActiveElement('font', this.value)">
                            <option value="Helvetica">Helvetica</option>
                            <option value="Courier">Courier</option>
                            <option value="Times New Roman">Times New Roman</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-500 font-bold">Size (pt)</label>
                        <input type="number" id="propSize" class="w-full border rounded p-1 text-sm" oninput="updateActiveElement('size', this.value)">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="text-[10px] text-gray-500 font-bold">Warna</label>
                        <input type="color" id="propColor" class="w-full h-8 border rounded p-0 cursor-pointer" oninput="updateActiveElement('color', this.value)">
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-500 font-bold">Alignment</label>
                        <div class="flex border rounded bg-white">
                            <button class="flex-1 p-1 hover:bg-gray-100" onclick="updateActiveElement('align', 'left')"><i class="fas fa-align-left"></i></button>
                            <button class="flex-1 p-1 hover:bg-gray-100" onclick="updateActiveElement('align', 'center')"><i class="fas fa-align-center"></i></button>
                            <button class="flex-1 p-1 hover:bg-gray-100" onclick="updateActiveElement('align', 'right')"><i class="fas fa-align-right"></i></button>
                        </div>
                    </div>
                </div>

                 <div id="propImageGroup" class="grid grid-cols-2 gap-2 mb-3 hidden">
                    <div>
                        <label class="text-[10px] text-gray-500 font-bold">Lebar (px)</label>
                        <input type="number" id="propWidth" class="w-full border rounded p-1 text-sm" oninput="updateActiveElement('w', this.value)">
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-500 font-bold">Tinggi (px)</label>
                        <input type="number" id="propHeight" class="w-full border rounded p-1 text-sm" oninput="updateActiveElement('h', this.value)">
                    </div>
                </div>
            </div>
            <div id="emptyState" class="p-10 text-center text-gray-400 text-sm italic">
                Klik elemen di canvas untuk mengedit.
            </div>
        </div>

        <div class="flex-1 bg-gray-200 overflow-auto flex justify-center items-center p-8">
            <div id="canvas" class="canvas-container bg-white shadow-lg relative">
                
                {{-- Background Image --}}
                @if($course->certificate_template)
                    <img src="{{ Storage::url($course->certificate_template) }}" class="absolute inset-0 w-full h-full object-cover pointer-events-none">
                @else
                    <div class="absolute inset-0 flex items-center justify-center text-gray-300 font-bold text-2xl select-none">
                        CANVAS AREA (A4)
                    </div>
                @endif

                {{-- Elements Container --}}
                <div id="elementsLayer" class="absolute inset-0 w-full h-full"></div>

            </div>
        </div>
    </div>

    <script>
        // Init Data from PHP
        let elements = @json($elements);
        let activeElId = null;
        
        // Render Initial Elements
        const layer = document.getElementById('elementsLayer');
        
        function render() {
            layer.innerHTML = '';
            elements.forEach(el => {
                const div = document.createElement('div');
                div.id = el.id;
                div.className = `draggable-el ${el.id === activeElId ? 'active' : ''}`;
                
                // Style Positioning (Convert % to visual)
                div.style.left = el.x + '%';
                div.style.top = el.y + '%';
                div.style.transform = 'translate(-50%, -50%)'; // Center pivot
                
                if (el.type === 'image') {
                    div.innerHTML = `<img src="${el.src}" style="width: ${el.w}px; height: ${el.h}px; object-fit: contain;">`;
                } else {
                    div.innerText = el.text;
                    div.style.fontFamily = el.font;
                    div.style.fontSize = (el.size / 2) + 'px'; // Scale down for preview roughly
                    div.style.color = el.color;
                    div.style.textAlign = el.align || 'center';
                }

                // Event Listeners
                div.onmousedown = (e) => startDrag(e, el.id);
                div.onclick = (e) => selectElement(el.id); // Stop propagation handled in logic
                
                layer.appendChild(div);
            });
        }

        // --- Drag Logic ---
        let isDragging = false;
        function startDrag(e, id) {
            e.stopPropagation();
            selectElement(id);
            isDragging = true;
            
            const el = elements.find(x => x.id === id);
            const canvas = document.getElementById('canvas');
            const rect = canvas.getBoundingClientRect();

            function onMouseMove(e) {
                if (!isDragging) return;
                let clientX = e.clientX;
                let clientY = e.clientY;

                // Calculate % position relative to canvas
                let x = ((clientX - rect.left) / rect.width) * 100;
                let y = ((clientY - rect.top) / rect.height) * 100;

                // Clamp
                x = Math.max(0, Math.min(100, x));
                y = Math.max(0, Math.min(100, y));

                el.x = x;
                el.y = y;
                render();
            }

            function onMouseUp() {
                isDragging = false;
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
            }

            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        }

        // --- Element Management ---
        function addText(type, contentKey = null, contentText = 'Teks Baru') {
            const id = 'el_' + Date.now();
            elements.push({
                id: id,
                type: type, // 'text' or 'dynamic'
                content: contentKey, // 'student_name', etc
                text: contentText,
                x: 50, y: 50,
                font: 'Helvetica',
                color: '#000000',
                size: 20,
                align: 'center'
            });
            render();
            selectElement(id);
        }

        function uploadAsset(input) {
            if (!input.files || !input.files[0]) return;

            const formData = new FormData();
            formData.append('image', input.files[0]);
            // CSRF
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            // Show Loading?
            
            fetch("{{ route('admin.courses.certificate.upload-asset') }}", {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const id = 'el_' + Date.now();
                    elements.push({
                        id: id,
                        type: 'image',
                        src: data.url, // URL untuk preview
                        path: data.path, // Path untuk DomPDF
                        x: 50, y: 50,
                        w: 100, h: 100
                    });
                    render();
                    selectElement(id);
                } else {
                    alert('Gagal upload gambar');
                }
            })
            .catch(err => console.error(err));
            
            input.value = ''; // reset
        }

        function selectElement(id) {
            activeElId = id;
            const el = elements.find(x => x.id === id);
            
            document.getElementById('propertiesPanel').classList.remove('hidden');
            document.getElementById('emptyState').classList.add('hidden');

            if (el.type === 'image') {
                document.getElementById('propTextGroup').classList.add('hidden');
                document.getElementById('propImageGroup').classList.remove('hidden');
                document.getElementById('propWidth').value = el.w;
                document.getElementById('propHeight').value = el.h;
            } else {
                document.getElementById('propTextGroup').classList.remove('hidden');
                document.getElementById('propImageGroup').classList.add('hidden');
                
                // Disable text input if dynamic variable (optional, but keep simple)
                const textInput = document.getElementById('propText');
                textInput.value = el.text;
                textInput.disabled = (el.type === 'dynamic');
                
                document.getElementById('propFont').value = el.font;
                document.getElementById('propSize').value = el.size;
                document.getElementById('propColor').value = el.color;
            }
            render(); // to highlight active
        }

        function updateActiveElement(key, value) {
            if (!activeElId) return;
            const el = elements.find(x => x.id === activeElId);
            if (!el) return;
            
            el[key] = value;
            render();
        }

        function deleteActiveElement() {
            if (!activeElId) return;
            elements = elements.filter(x => x.id !== activeElId);
            activeElId = null;
            document.getElementById('propertiesPanel').classList.add('hidden');
            document.getElementById('emptyState').classList.remove('hidden');
            render();
        }

        function submitForm() {
            document.getElementById('elementsJsonInput').value = JSON.stringify(elements);
            document.getElementById('mainForm').submit();
        }

        // Initial Render
        render();

        // Canvas Click to Deselect
        document.getElementById('canvas').addEventListener('click', (e) => {
            if (e.target.id === 'canvas' || e.target.id === 'elementsLayer') {
                activeElId = null;
                document.getElementById('propertiesPanel').classList.add('hidden');
                document.getElementById('emptyState').classList.remove('hidden');
                render();
            }
        });
    </script>
</body>
</html>