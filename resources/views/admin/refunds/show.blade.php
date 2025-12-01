@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800">Detail Refund #{{ $refund->id }}</h1>
            <a href="{{ route('admin.refunds.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Kembali
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Status Badge -->
            <div class="p-6 border-b">
                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full
                    @if($refund->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($refund->status === 'approved') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($refund->status) }}
                </span>
            </div>

            <!-- Student Information -->
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold mb-4">Informasi Student</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Nama</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $refund->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Email</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $refund->user->email }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Course Information -->
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold mb-4">Informasi Kursus</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Nama Kursus</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $refund->registration->course->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Harga</dt>
                        <dd class="text-sm font-medium text-gray-900">Rp {{ number_format($refund->registration->course->price, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Tanggal Pembayaran</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $refund->registration->created_at->format('d M Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Refund Details -->
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold mb-4">Detail Refund</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm text-gray-500">Jumlah Refund</dt>
                        <dd class="text-2xl font-bold text-gray-900">Rp {{ number_format($refund->amount, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 mb-1">Alasan</dt>
                        <dd class="text-sm text-gray-900 bg-gray-50 p-3 rounded">{{ $refund->reason }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Nomor Rekening</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $refund->bank_account }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Tanggal Pengajuan</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $refund->created_at->format('d M Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            @if($refund->status !== 'pending')
            <!-- Approval Information -->
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold mb-4">Informasi Pemrosesan</h3>
                <dl class="space-y-4">
                    @if($refund->approvedBy)
                    <div>
                        <dt class="text-sm text-gray-500">Diproses oleh</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $refund->approvedBy->name }}</dd>
                    </div>
                    @endif
                    @if($refund->approved_at)
                    <div>
                        <dt class="text-sm text-gray-500">Tanggal Diproses</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $refund->approved_at->format('d M Y H:i') }}</dd>
                    </div>
                    @endif
                    @if($refund->rejection_reason)
                    <div>
                        <dt class="text-sm text-gray-500 mb-1">Alasan Penolakan</dt>
                        <dd class="text-sm text-gray-900 bg-red-50 p-3 rounded">{{ $refund->rejection_reason }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif

            <!-- Action Buttons -->
            @if($refund->status === 'pending')
            <div class="p-6 bg-gray-50">
                <div class="flex space-x-4">
                    <form action="{{ route('admin.refunds.approve', $refund) }}" method="POST" class="flex-1"
                          onsubmit="return confirm('Apakah Anda yakin ingin menyetujui refund ini?')">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                            Setujui Refund
                        </button>
                    </form>

                    <button onclick="document.getElementById('rejectModal').classList.remove('hidden')" 
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                        Tolak Refund
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tolak Refund</h3>
            <form action="{{ route('admin.refunds.reject', $refund) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Penolakan *
                    </label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                              placeholder="Jelaskan alasan penolakan..." required></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="button" 
                            onclick="document.getElementById('rejectModal').classList.add('hidden')"
                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Tolak Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection