@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Ajukan Refund</h2>

            <!-- Informasi Kursus -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-lg mb-2">Detail Pendaftaran</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kursus:</span>
                        <span class="font-medium">{{ $registration->course->title }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Harga:</span>
                        <span class="font-medium">Rp {{ number_format($registration->course->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal Pembayaran:</span>
                        <span class="font-medium">{{ $registration->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="px-2 py-1 rounded text-xs font-semibold
                            @if($registration->payment_status === 'paid') bg-green-100 text-green-800
                            @elseif($registration->payment_status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($registration->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Peringatan -->
            <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Perhatian:</strong> Setelah refund disetujui, Anda tidak akan bisa mengakses kursus ini lagi.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Refund -->
            <form action="{{ route('refund.store', $registration->id) }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Refund <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        name="reason" 
                        id="reason" 
                        rows="5" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('reason') border-red-500 @enderror"
                        placeholder="Jelaskan alasan Anda mengajukan refund..."
                        required>{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Minimal 20 karakter</p>
                </div>

                <div class="mb-6">
                    <label for="bank_account" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Rekening Bank <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="bank_account" 
                        id="bank_account" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('bank_account') border-red-500 @enderror"
                        placeholder="Contoh: BCA 1234567890"
                        value="{{ old('bank_account') }}"
                        required>
                    @error('bank_account')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Format: Nama Bank + Nomor Rekening</p>
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('student.dashboard') }}" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Ajukan Refund
                    </button>
                </div>
            </form>
        </div>

        <!-- Informasi Kebijakan Refund -->
        <div class="mt-6 bg-blue-50 rounded-lg p-4">
            <h4 class="font-semibold text-blue-900 mb-2">Kebijakan Refund:</h4>
            <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                <li>Refund hanya dapat diajukan dalam 30 hari setelah pembayaran</li>
                <li>Proses refund akan diproses dalam 7-14 hari kerja</li>
                <li>Dana akan dikembalikan ke rekening yang Anda daftarkan</li>
                <li>Setelah refund disetujui, akses ke kursus akan dicabut</li>
            </ul>
        </div>
    </div>
</div>
@endsection