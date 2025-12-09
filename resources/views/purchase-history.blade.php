@extends('layouts.app')

<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Purchase History - OtakAtik Academy</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
        <style>
            .status-badge {
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
            }
            .status-pending { background: #fef3c7; color: #d97706; }
            .status-paid { background: #d1fae5; color: #065f46; }
            .status-cancelled { background: #fee2e2; color: #dc2626; }
        </style>
    </head>
    <body class="bg-gray-50">
        

        <!-- Purchase History Section -->
        <section class="pt-32 pb-20 px-6">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-gray-800 mb-4">Purchase History</h1>
                    <p class="text-gray-600 text-lg">Riwayat pembelian dan pendaftaran course Anda</p>
                </div>

                @if($allRegistrations->count() > 0)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($allRegistrations as $registration)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-book text-white"></i>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $registration->course->title }}</div>
                                                    <div class="text-sm text-gray-500">{{ $registration->course->instructor->name ?? 'No Instructor' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium 
                                                @if($registration->course->type === 'online') bg-blue-100 text-blue-800
                                                @elseif($registration->course->type === 'hybrid') bg-yellow-100 text-yellow-800
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ ucfirst($registration->course->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if($registration->discount_code)
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-green-600">{{ $registration->formatted_final_price }}</span>
                                                    <span class="text-xs text-gray-500 line-through">{{ $registration->formatted_price }}</span>
                                                </div>
                                                <div class="text-xs text-green-600">Diskon: {{ $registration->discount_code }}</div>
                                                @else
                                                <span class="font-bold text-gray-900">{{ $registration->formatted_price }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="status-badge status-{{ $registration->status }}">
                                                {{ ucfirst($registration->status) }}
                                            </span>
                                            @if($registration->status === 'paid' && $registration->progress > 0)
                                            <div class="text-xs text-gray-500 mt-1">Progress: {{ $registration->progress }}%</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $registration->created_at->format('d M Y') }}<br>
                                            <small>{{ $registration->created_at->format('H:i') }}</small>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex gap-2">
                                                @if($registration->status === 'paid')
                                                <a href="{{ route('course.show.detail', $registration->course->id) }}" 
                                                class="text-blue-600 hover:text-blue-900" title="Lihat Course">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endif
                                                
                                                @if($registration->status === 'pending')
                                                <form action="{{ route('course.destroy', $registration->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" 
                                                            onclick="return confirm('Yakin ingin membatalkan pendaftaran ini?')"
                                                            title="Batalkan Pendaftaran">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            <div class="mt-6">
                                {{-- Cek apakah ada halaman (biar gak muncul kotak kosong kalau cuma 1 halaman) --}}
                                @if($allRegistrations->hasPages())
                                    <div class="flex flex-col items-center justify-between gap-4 px-4 py-3 bg-white border rounded-lg shadow-sm sm:flex-row dark:bg-gray-800 dark:border-gray-700 sm:px-6">

                                        {{-- Tombol Pagination Default Laravel --}}
                                        <div class="w-full sm:w-auto">
                                            {{ $allRegistrations->links() }} 
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                        <div class="bg-white rounded-2xl shadow-lg p-6 text-center">
                            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $allRegistrations->where('status', 'paid')->count() }}</div>
                            <p class="text-gray-600">Course Aktif</p>
                        </div>
                        <div class="bg-white rounded-2xl shadow-lg p-6 text-center">
                            <div class="text-3xl font-bold text-yellow-600 mb-2">{{ $allRegistrations->where('status', 'pending')->count() }}</div>
                            <p class="text-gray-600">Menunggu Konfirmasi</p>
                        </div>
                        <div class="bg-white rounded-2xl shadow-lg p-6 text-center">
                            <div class="text-3xl font-bold text-green-600 mb-2">Rp{{ number_format($allRegistrations->where('status', 'paid')->sum('final_price'), 0, ',', '.') }}</div>
                            <p class="text-gray-600">Total Pengeluaran</p>
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-16 bg-white rounded-2xl shadow-lg">
                        <div class="max-w-md mx-auto">
                            <div class="w-32 h-32 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-history text-white text-4xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-4">Belum Ada Riwayat Pembelian</h3>
                            <p class="text-gray-600 mb-8">Anda belum pernah mendaftar course apapun.</p>
                            <a href="/course" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold px-8 py-4 rounded-lg transition-all hover:scale-105 shadow-lg">
                                <i class="fas fa-shopping-cart mr-2"></i> Jelajahi Course
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </section>

    </body>
    </html>