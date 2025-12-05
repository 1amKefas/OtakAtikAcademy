<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course - OtakAtik Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* [ADD] Handle Style */
        .handle { cursor: grab; }
        .handle:active { cursor: grabbing; }
        .sortable-ghost { opacity: 0.4; background-color: #eff6ff; border: 2px dashed #3b82f6; }
    </style>
</head>
<body class="bg-gray-50">

    @if(session('success'))
    <div id="alert-success" class="fixed top-6 right-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3 animate-bounce">
        <i class="fas fa-check-circle text-xl"></i>
        <div>
            <h4 class="font-bold">Berhasil!</h4>
            <p class="text-sm">{{ session('success') }}</p>
        </div>
    </div>
    <script>setTimeout(() => document.getElementById('alert-success').remove(), 4000);</script>
    @endif

    @if ($errors->any())
    <div id="alert-error" class="fixed top-6 right-6 bg-red-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-xl"></i>
        <div>
            <h4 class="font-bold">Gagal Menyimpan</h4>
            <ul class="text-sm list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button onclick="document.getElementById('alert-error').remove()" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <div class="min-h-screen p-6 flex flex-col">
        <div class="max-w-4xl mx-auto w-full flex-1">
            
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.courses.manage') }}" class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center text-gray-600 hover:text-blue-600 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-800">Edit Course</h1>
                </div>
                <div class="text-sm text-gray-500">ID: #{{ $course->id }}</div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 relative overflow-hidden">
                
                <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                        
                        <div class="md:col-span-4 space-y-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Thumbnail Course</label>
                            
                            <div class="border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 hover:bg-blue-50 transition-colors relative h-56 flex flex-col items-center justify-center overflow-hidden group cursor-pointer">
                                
                                <input type="file" name="image" id="imageInput" 
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-50" 
                                       accept="image/*">
                                
                                <div id="imagePlaceholder" class="{{ $course->image_url ? 'hidden' : '' }} text-center p-4">
                                    <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center mx-auto mb-3 text-blue-500">
                                        <i class="fas fa-cloud-upload-alt text-3xl"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-600">Upload Thumbnail Baru</p>
                                    <p class="text-xs text-gray-400 mt-1">JPG/PNG, Max 2MB</p>
                                </div>

                                <img id="imagePreview" 
                                     src="{{ $course->image_url ?? '' }}" 
                                     class="{{ $course->image_url ? '' : 'hidden' }} absolute inset-0 w-full h-full object-cover z-10 bg-white" 
                                     alt="Preview">

                                <div id="imageOverlay" class="{{ $course->image_url ? '' : 'hidden' }} absolute inset-0 bg-black/60 z-40 flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                                    <i class="fas fa-crop-alt text-3xl mb-2"></i>
                                    <span class="font-medium text-sm">Klik untuk Ganti & Crop</span>
                                </div>
                            </div>
                            
                            <p class="text-xs text-gray-500 text-center">
                                *Rasio gambar akan otomatis di-crop ke <strong>16:9</strong>
                            </p>
                        </div>

                        <div class="md:col-span-8 space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Judul Course</label>
                                <input type="text" name="title" value="{{ $course->title }}" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-shadow shadow-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi</label>
                                <textarea name="description" rows="4" required
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-shadow shadow-sm resize-none">{{ $course->description }}</textarea>
                            </div>

                            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">Manajemen Modul</h3>
                                        <p class="text-sm text-gray-500">Atur struktur bab pembelajaran. Drag icon untuk urutkan.</p>
                                    </div>
                                    <button type="button" onclick="addEditModuleInput()" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
                                        <i class="fas fa-plus mr-2"></i> Tambah Baru
                                    </button>
                                </div>

                                <div id="admin-modules-list" class="space-y-3" data-course-id="{{ $course->id }}">
                                    @forelse($course->modules->sortBy('sort_order') as $index => $module)
                                        <div class="flex items-center gap-2 group module-item" id="edit-module-row-old-{{ $index }}" data-id="{{ $module->id }}">
                                            <input type="hidden" name="modules[{{ $index }}][id]" value="{{ $module->id }}">
                                            
                                            <div class="flex-1 bg-white p-3 rounded-lg border border-gray-300 flex items-center gap-3 shadow-sm">
                                                <span class="text-gray-400 font-bold px-2 cursor-grab handle hover:text-gray-600"><i class="fas fa-grip-vertical"></i></span>
                                                <input type="text" name="modules[{{ $index }}][title]" value="{{ $module->title }}"
                                                    class="flex-1 bg-transparent border-none focus:ring-0 text-sm font-medium text-gray-800" required>
                                            </div>
                                            <button type="button" onclick="removeEditModuleRow('old-{{ $index }}')" class="p-2 text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors" title="Hapus Modul">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    @empty
                                        <div id="no-modules-msg" class="text-center py-4 text-gray-400 text-sm italic">
                                            Belum ada modul. Silakan tambah modul.
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Tipe Course</label>
                                    <select name="type" required onchange="toggleInstructorField(this.value)" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white shadow-sm">
                                        <option value="Full Online" {{ $course->type == 'Full Online' ? 'selected' : '' }}>Full Online</option>
                                        <option value="Hybrid" {{ $course->type == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                                        <option value="Tatap Muka" {{ $course->type == 'Tatap Muka' ? 'selected' : '' }}>Tatap Muka</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Instruktur Utama</label>
                                    <select name="instructor_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white shadow-sm">
                                        <option value="">Pilih Instruktur Utama</option>
                                        @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->id }}" {{ $course->instructor_id == $instructor->id ? 'selected' : '' }}>
                                            {{ $instructor->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Wajib untuk Hybrid & Tatap Muka. Opsional untuk Online.</p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Instruktur Tambahan</label>
                                <select name="assistants[]" multiple class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white shadow-sm h-32">
                                    @foreach($instructors as $instructor)
                                    <option value="{{ $instructor->id }}" {{ $course->assistants->contains($instructor->id) ? 'selected' : '' }}>
                                        {{ $instructor->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Tekan CTRL (Windows) atau CMD (Mac) untuk memilih lebih dari satu.</p>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Harga (Rp)</label>
                                    <input type="number" name="price" value="{{ $course->price }}" required 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Diskon (%)</label>
                                    <input type="number" name="discount_percent" value="{{ $course->discount_percent }}" min="0" max="100" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Kode Promo</label>
                                    <input type="text" name="discount_code" value="{{ $course->discount_code }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm" placeholder="Opsional">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Durasi (Hari)</label>
                                    <input type="number" name="duration_days" value="{{ $course->duration_days }}" min="1"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Min Kuota</label>
                                    <input type="number" name="min_quota" value="{{ $course->min_quota }}" required 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Max Kuota</label>
                                    <input type="number" name="max_quota" value="{{ $course->max_quota }}" required 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Mulai</label>
                                    <input type="date" name="start_date" value="{{ $course->start_date?->format('Y-m-d') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Selesai</label>
                                    <input type="date" name="end_date" value="{{ $course->end_date?->format('Y-m-d') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Info Reschedule</label>
                                <textarea name="reschedule_reason" rows="2" placeholder="Opsional..."
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm">{{ $course->reschedule_reason }}</textarea>
                            </div>

                            <div class="flex items-center gap-3 p-4 bg-blue-50 rounded-lg border border-blue-100">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       {{ $course->is_active ? 'checked' : '' }}
                                       class="w-5 h-5 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500">
                                <label for="is_active" class="text-sm font-bold text-blue-900 cursor-pointer">
                                    Aktifkan Course (Tampilkan di halaman user)
                                </label>
                            </div>

                            <div class="flex gap-4 pt-6 border-t border-gray-100 mt-6">
                                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-6 rounded-xl transition-all shadow-lg hover:shadow-blue-200 flex justify-center items-center gap-2">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('admin.courses.manage') }}" class="px-8 py-3.5 border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-all">
                                    Batal
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="cropModal" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-xl w-full max-w-2xl overflow-hidden shadow-2xl transform transition-all">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Sesuaikan Area Gambar (16:9)</h3>
                <button type="button" onclick="closeCropper()" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="bg-gray-900 h-[400px] flex justify-center items-center relative">
                <img id="imageToCrop" class="max-w-full max-h-full block">
            </div>

            <div class="p-4 border-t border-gray-200 flex justify-end gap-3 bg-white">
                <button type="button" onclick="closeCropper()" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                    Batal
                </button>
                <button type="button" onclick="cropImage()" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow-lg transition-colors flex items-center gap-2">
                    <i class="fas fa-crop-simple"></i> Potong & Simpan
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        function toggleInstructorField(type) {
            // Logic handled backend/view state
        }

        const imageInput = document.getElementById('imageInput');
        const cropModal = document.getElementById('cropModal');
        const imageToCrop = document.getElementById('imageToCrop');
        const imagePreview = document.getElementById('imagePreview');
        const imagePlaceholder = document.getElementById('imagePlaceholder');
        const imageOverlay = document.getElementById('imageOverlay');
        
        let cropper = null;

        imageInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const file = files[0];
                
                if(!file.type.startsWith('image/')) {
                    alert('Mohon upload file gambar (JPG/PNG).');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    imageToCrop.src = event.target.result;
                    cropModal.classList.remove('hidden');
                    
                    if (cropper) cropper.destroy();
                    
                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: 16 / 9,
                        viewMode: 1,
                        autoCropArea: 1,
                        background: false,
                        responsive: true,
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        function cropImage() {
            if (!cropper) return;
            
            cropper.getCroppedCanvas({
                width: 800, 
                height: 450,
                fillColor: '#fff'
            }).toBlob((blob) => {
                const newFile = new File([blob], "thumbnail_edited.jpg", { type: "image/jpeg" });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(newFile);
                imageInput.files = dataTransfer.files;

                const url = URL.createObjectURL(blob);
                imagePreview.src = url;
                imagePreview.classList.remove('hidden');
                imagePlaceholder.classList.add('hidden');
                imageOverlay.classList.remove('hidden');
                
                closeCropper();
            }, 'image/jpeg', 0.85);
        }

        function closeCropper() {
            cropModal.classList.add('hidden');
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        }
    </script>
    <script>
    let editModuleCount = {{ $course->modules->count() + 100 }}; 

    function addEditModuleInput() {
        const emptyMsg = document.getElementById('no-modules-msg');
        if(emptyMsg) emptyMsg.remove();

        const container = document.getElementById('admin-modules-list');
        const index = editModuleCount;

        const html = `
            <div class="flex items-center gap-2 group module-item" id="edit-module-row-new-${index}" data-id="new-${index}">
                <div class="flex-1 bg-blue-50 p-3 rounded-lg border border-blue-200 flex items-center gap-3">
                    <span class="text-blue-400 font-bold px-2 cursor-grab handle"><i class="fas fa-grip-vertical"></i></span>
                    <input type="text" name="modules[new_${index}][title]" placeholder="Judul Modul Baru..." 
                           class="flex-1 bg-transparent border-none focus:ring-0 text-sm font-medium text-gray-800 placeholder-blue-300" required>
                </div>
                <button type="button" onclick="removeEditModuleRow('new-${index}')" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', html);
        editModuleCount++;
    }

    function removeEditModuleRow(idSuffix) {
        document.getElementById(`edit-module-row-${idSuffix}`).remove();
    }

    // [ADD] Script SortableJS for Admin
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('admin-modules-list');
        var courseId = el ? el.getAttribute('data-course-id') : null;

        if(el) {
            Sortable.create(el, {
                handle: '.handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function (evt) {
                    var orderedIds = this.toArray();
                    
                    // AJAX Reorder Call
                    fetch("{{ route('admin.modules.reorder', $course->id) }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ ordered_ids: orderedIds })
                    }).then(res => res.json())
                      .then(data => console.log('Reorder berhasil'));
                }
            });
        }
    });
</script>
</body>
</html>