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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-2 text-center relative hover:bg-gray-50 transition-colors h-48 flex flex-col items-center justify-center overflow-hidden">
                                <input type="file" name="image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*" onchange="previewImage(this)">
                                
                                @if($course->image_url)
                                    <img id="imagePreview" src="{{ $course->image_url }}" class="absolute inset-0 w-full h-full object-cover rounded-md" />
                                @else
                                    <img id="imagePreview" class="hidden absolute inset-0 w-full h-full object-cover rounded-md" />
                                    <div id="placeholder" class="text-gray-400">
                                        <i class="fas fa-image text-3xl mb-2"></i>
                                        <p class="text-xs">Ganti Gambar</p>
                                    </div>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-2 text-center">Klik gambar untuk mengubah</p>
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
    </script>
</body>
</html>