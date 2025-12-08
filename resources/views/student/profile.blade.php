@extends('layouts.app')

@section('title', 'Profil Siswa')

@section('content')
<script src="{{ asset('js/profile-upload.js') }}"></script>

<div class="bg-white">
    <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-orange-500 text-white px-6 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6 flex items-center gap-3">
                <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition-all backdrop-blur">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
            <h1 class="text-4xl font-bold mb-2">Profil Saya</h1>
            <p class="text-blue-50 text-lg">Kelola informasi pribadi dan preferensi Anda dengan mudah</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-6 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-1">
                <div class="sticky top-6">
                    <div class="bg-white rounded-lg border border-gray-200 p-6 text-center mb-4 shadow-sm">
                        <div class="mb-4">
                            @php
                                $hasProfilePicture = $user->profile_picture && Storage::disk('public')->exists($user->profile_picture);
                            @endphp
                            @if($hasProfilePicture)
                                <img src="{{ Storage::url($user->profile_picture) }}" alt="{{ $user->name }}" 
                                     class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-purple-200 shadow-md">
                            @else
                                <div class="w-32 h-32 rounded-full mx-auto bg-gradient-to-br from-purple-400 to-blue-400 flex items-center justify-center border-4 border-purple-200 shadow-md">
                                    <span class="text-white text-4xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <h3 class="font-semibold text-gray-800 text-lg">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $user->email }}</p>
                        <p class="text-xs text-gray-500 mt-2 bg-gray-100 inline-block px-2 py-1 rounded">Siswa</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-lg border border-purple-200 p-4 shadow-sm">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600"><i class="fas fa-book-reader mr-2 text-purple-400"></i>Courses</span>
                                <span class="font-bold text-purple-600">{{ $user->courseRegistrations()->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600"><i class="fas fa-check-circle mr-2 text-green-400"></i>Selesai</span>
                                <span class="font-bold text-green-600">{{ $user->courseRegistrations()->where('status', 'completed')->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600"><i class="fas fa-tasks mr-2 text-blue-400"></i>Assignments</span>
                                <span class="font-bold text-blue-600">{{ $user->assignmentSubmissions()->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
                        <i class="fas fa-user-edit text-purple-500"></i> Edit Profil
                    </h3>

                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                            <p class="text-red-800 font-semibold mb-2">Terjadi Kesalahan:</p>
                            <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            <p class="text-green-800">{{ session('success') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="border-b border-gray-200 pb-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pribadi</h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor HP</label>
                                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                           placeholder="+62812345678">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Domisili</label>
                                    <input type="text" name="location" value="{{ old('location', $user->location) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                           placeholder="Kota, Provinsi">
                                </div>
                            </div>
                        </div>

                        <div class="border-b border-gray-200 pb-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Pendidikan</h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tingkat Pendidikan</label>
                                    <select name="education_level"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                                        <option value="">-- Pilih --</option>
                                        <option value="SMA" {{ old('education_level', $user->education_level) === 'SMA' ? 'selected' : '' }}>SMA/Sederajat</option>
                                        <option value="Diploma" {{ old('education_level', $user->education_level) === 'Diploma' ? 'selected' : '' }}>Diploma</option>
                                        <option value="Bachelor" {{ old('education_level', $user->education_level) === 'Bachelor' ? 'selected' : '' }}>Sarjana (S1)</option>
                                        <option value="Master" {{ old('education_level', $user->education_level) === 'Master' ? 'selected' : '' }}>Master (S2)</option>
                                        <option value="Doctorate" {{ old('education_level', $user->education_level) === 'Doctorate' ? 'selected' : '' }}>Doktor (S3)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Sekolah/Universitas</label>
                                    <input type="text" name="education_name" value="{{ old('education_name', $user->education_name) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                           placeholder="Contoh: SMK Telkom Jakarta">
                                </div>
                            </div>
                        </div>

                        <div class="border-b border-gray-200 pb-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Foto Profil</h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Foto Baru (JPG, PNG - Max 5MB)</label>
                                    <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 hover:border-purple-400 transition-all cursor-pointer bg-gray-50 hover:bg-white" id="dropZone">
                                        <input type="file" name="profile_picture" accept="image/*" id="profilePictureInput"
                                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        
                                        <div class="text-center pointer-events-none">
                                            <div class="w-12 h-12 bg-purple-100 text-purple-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                                <i class="fas fa-cloud-upload-alt text-2xl"></i>
                                            </div>
                                            <p class="text-sm font-medium text-gray-700">Klik atau Drag & Drop foto di sini</p>
                                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (Maks 5MB)</p>
                                        </div>
                                    </div>
                                    <p id="fileName" class="text-sm text-gray-600 mt-2 min-h-[20px]"></p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Informasi Tambahan</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bio/Tentang Diri Saya</label>
                                <textarea name="bio" rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                          placeholder="Ceritakan tentang diri Anda...">{{ old('bio', $user->bio) }}</textarea>
                            </div>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button type="submit" 
                                    class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('student.dashboard') }}" 
                               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection