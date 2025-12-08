@extends('layouts.app')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - OtakAtik Academy</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    
    <script src="{{ asset('js/checkout.js') }}"></script>

    <style>
        .payment-method {
            transition: all 0.3s ease;
        }
        .payment-method.selected {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <div id="finalPriceData" data-price="{{ $finalPrice }}"></div>
    <div id="courseData" data-id="{{ $course->id }}"></div>

    <section class="pt-32 pb-20 px-6">
        <div class="max-w-4xl mx-auto">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="md:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Order Summary</h2>
                        
                        <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg mb-6">
                            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                [C]
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-800 text-lg">{{ $course->title }}</h3>
                                <p class="text-gray-600 text-sm mb-2">{{ $course->type }} Course</p>
                                <p class="text-sm text-gray-500">Instruktur: {{ $course->instructor->name ?? 'Tidak tersedia' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-800">Rp{{ number_format($course->price, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        @if(!Auth::user()->is_instructor)
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Voucher Code (Optional)</label>
                            <div class="flex gap-3">
                                <input type="text" id="voucherCode" placeholder="Enter voucher code" 
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <button type="button" onclick="applyVoucher()" 
                                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold px-6 py-3 rounded-lg transition-all">
                                    Apply
                                </button>
                            </div>
                            <div id="voucherMessage" class="mt-2 text-sm"></div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Method</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="payment-method border-2 border-gray-200 rounded-lg p-4 cursor-pointer" data-method="bank_transfer">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl">🏦</span>
                                        <div>
                                            <p class="font-medium text-gray-800">Bank Transfer</p>
                                            <p class="text-sm text-gray-600">BCA, BNI, BRI, etc</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="payment-method border-2 border-gray-200 rounded-lg p-4 cursor-pointer" data-method="credit_card">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl"></span>
                                        <div>
                                            <p class="font-medium text-gray-800">Credit Card</p>
                                            <p class="text-sm text-gray-600">Visa, Mastercard</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="payment-method border-2 border-gray-200 rounded-lg p-4 cursor-pointer" data-method="gopay">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl"></span>
                                        <div>
                                            <p class="font-medium text-gray-800">GoPay</p>
                                            <p class="text-sm text-gray-600">E-Wallet</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="payment-method border-2 border-gray-200 rounded-lg p-4 cursor-pointer" data-method="shopeepay">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl"></span>
                                        <div>
                                            <p class="font-medium text-gray-800">ShopeePay</p>
                                            <p class="text-sm text-gray-600">E-Wallet</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="selectedPaymentMethod" name="payment_method" required>
                        </div>

                        <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-lg">
                            <input type="checkbox" id="termsAgreement" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 mt-1">
                            <label for="termsAgreement" class="text-sm text-gray-700">
                                I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and 
                                <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>. I understand that 
                                all payments are processed securely through Midtrans.
                            </label>
                        </div>
                        @else
                        <div class="flex items-start gap-3 p-4 bg-green-50 rounded-lg border-2 border-green-200">
                            🌟
                            <div>
                                <h4 class="font-bold text-green-800 mb-1">Instructor Benefits</h4>
                                <p class="text-sm text-green-700">Sebagai seorang instructor, Anda mendapatkan akses gratis ke semua course di platform kami untuk memastikan kualitas pengajaran yang terbaik.</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="md:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-32">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Order Total</h3>
                        
                        @if(Auth::user()->is_instructor)
                            <div class="bg-green-50 border-2 border-green-500 rounded-lg p-4 mb-6">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-2xl">🌟</span>
                                    <h4 class="font-bold text-green-800">Instructor Free Access</h4>
                                </div>
                                <p class="text-green-700 text-sm mb-4">Anda mendapatkan akses gratis ke semua course sebagai instructor.</p>
                                <div class="bg-green-100 rounded-lg p-3 mb-4">
                                    <p class="text-green-800 font-bold text-lg">Gratis 100%</p>
                                </div>
                                <button type="button" onclick="enrollFreeAsInstructor()" 
                                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg transition-all">
                                    Enroll Now
                                </button>
                            </div>
                        @else
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Course Price</span>
                                    <span class="font-medium" id="coursePrice">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Discount</span>
                                    <span class="font-medium text-green-600" id="discountAmount">-Rp0</span>
                                </div>
                                <div class="border-t pt-3">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span>Total</span>
                                        <span id="finalPrice">Rp{{ number_format($finalPrice, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="processPayment()" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg transition-all disabled:bg-gray-400 disabled:cursor-not-allowed"
                                    id="payButton" disabled>
                                  Pay
                            </button>

                            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm text-yellow-800 font-medium mb-2">Development Mode</p>
                                <button type="button" onclick="simulatePayment()" 
                                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-all text-sm">
                                    Simulate Successful Payment
                                </button>
                            </div>
                        @endif
                        
                        <p class="text-xs text-gray-500 text-center mt-4">
                            Secure payment powered by Midtrans
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section> 

    <div id="loadingOverlay" class="hidden fixed inset-0 bg-black/50 z-[9999] flex flex-col items-center justify-center backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white p-6 rounded-2xl shadow-2xl flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-100 border-t-blue-600 mb-4"></div>
            <h3 class="text-lg font-bold text-gray-800">Processing Payment...</h3>
            <p class="text-sm text-gray-500">Please do not close this window.</p>
        </div>
    </div>
</body>
</html>