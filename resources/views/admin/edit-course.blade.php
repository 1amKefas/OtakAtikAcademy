<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course - OtakAtik Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

    @if(session('success'))
    <div id="alert-success" class="fixed top-6 right-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3 transition-opacity duration-500">
        <i class="fas fa-check-circle text-xl"></i>
        <div>
            <h4 class="font-bold">Berhasil!</h4>
            <p class="text-sm">{{ session('success') }}</p>
        </div>
        <button onclick="document.getElementById('alert-success').remove()" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
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

    <div class="min-h-screen p-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Edit Course</h1>
                    <a href="{{ route('admin.courses.manage') }}" class="text-blue-600 hover:text-blue-800">
                        ← Kembali ke Kelola Course
                    </a>
                </div>

                <form action="{{ route('admin.courses.update', $course->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <!-- Course Thumbnail -->
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail Course</label>
                            
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-2 text-center relative hover:bg-gray-50 transition-colors h-64 flex flex-col items-center justify-center overflow-hidden group">
                                
                                <input type="file" name="image" id="imageInput" 
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" 
                                       accept="image/*" 
                                       onchange="previewImage(this)">
                                
                                <img id="imagePreview" 
                                     src="{{ $course->image_url ?? '' }}" 
                                     class="{{ $course->image_url ? '' : 'hidden' }} absolute inset-0 w-full h-full object-cover rounded-lg z-10 transition-transform group-hover:scale-105 duration-300" 
                                     alt="Preview">

                                <div id="placeholder" class="{{ $course->image_url ? 'hidden' : '' }} flex flex-col items-center justify-center text-gray-400 z-0 pointer-events-none">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-cloud-upload-alt text-2xl text-blue-500"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-600">Upload Thumbnail</p>
                                    <p class="text-xs text-gray-400 mt-1">JPG/PNG, Max 2MB</p>
                                </div>

                                <div id="changeText" class="{{ $course->image_url ? '' : 'hidden' }} absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs py-2 z-20 opacity-0 group-hover:opacity-100 transition-opacity">
                                    Klik untuk ubah gambar
                                </div>
                            </div>
                        </div>
                        <!-- Course Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Judul Course</label>
                            <input type="text" name="title" value="{{ $course->title }}" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Course Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="description" rows="4" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $course->description }}</textarea>
                        </div>

                        <!-- Course Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Course</label>
                            <select name="type" required onchange="toggleInstructorField(this.value)"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="Full Online" {{ $course->type == 'Full Online' ? 'selected' : '' }}>Full Online</option>
                                <option value="Hybrid" {{ $course->type == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                                <option value="Tatap Muka" {{ $course->type == 'Tatap Muka' ? 'selected' : '' }}>Tatap Muka</option>
                            </select>
                        </div>

                        <!-- Instructor Field (Conditional) -->
                        <div id="instructorField" style="{{ in_array($course->type, ['Hybrid', 'Tatap Muka']) ? 'display: block;' : 'display: none;' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Instruktur</label>
                            <select name="instructor_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Pilih Instruktur</option>
                                @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}" {{ $course->instructor_id == $instructor->id ? 'selected' : '' }}>
                                    {{ $instructor->name }} ({{ $instructor->email }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Pricing -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Harga (Rp)</label>
                                <input type="number" name="price" value="{{ $course->price }}" required min="0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Diskon (%)</label>
                                <input type="number" name="discount_percent" value="{{ $course->discount_percent }}" min="0" max="100"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <!-- Quota -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kuota Minimal</label>
                                <input type="number" name="min_quota" value="{{ $course->min_quota }}" required min="1"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kuota Maksimal</label>
                                <input type="number" name="max_quota" value="{{ $course->max_quota }}" required min="1"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <!-- Duration & Schedule -->
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Durasi (Hari)</label>
                                <input type="number" name="duration_days" value="{{ $course->duration_days }}" min="1"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                                <input type="date" name="start_date" value="{{ $course->start_date?->format('Y-m-d') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                                <input type="date" name="end_date" value="{{ $course->end_date?->format('Y-m-d') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <!-- Reschedule Info -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reschedule (Jika ada perubahan jadwal)</label>
                            <textarea name="reschedule_reason" placeholder="Alasan perubahan jadwal (optional)" rows="2"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $course->reschedule_reason }}</textarea>
                        </div>

                        <!-- Active Status -->
                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   {{ $course->is_active ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <label for="is_active" class="text-sm font-medium text-gray-700">
                                Aktifkan Course (muncul di user)
                            </label>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex gap-4 pt-6">
                            <button type="submit" 
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-all">
                                <i class="fas fa-save mr-2"></i> Update Course
                            </button>
                            <a href="{{ route('admin.courses.manage') }}" 
                               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all">
                                Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const imgPreview = document.getElementById('imagePreview');
            const placeholder = document.getElementById('placeholder');
            const changeText = document.getElementById('changeText');

            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    // Update source gambar
                    imgPreview.src = e.target.result;
                    
                    // Tampilkan gambar, sembunyikan placeholder
                    imgPreview.classList.remove('hidden');
                    changeText.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }    

        function toggleInstructorField(type) {
            const instructorField = document.getElementById('instructorField');
            if (type === 'Hybrid' || type === 'Tatap Muka') {
                instructorField.style.display = 'block';
                instructorField.querySelector('select').required = true;
            } else {
                instructorField.style.display = 'none';
                instructorField.querySelector('select').required = false;
                instructorField.querySelector('select').value = '';
            }
        }

        // Auto hide success alert after 5 seconds
        setTimeout(() => {
            const alert = document.getElementById('alert-success');
            if(alert) {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    </script>
</body>
</html>