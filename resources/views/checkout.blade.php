<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - OtakAtik Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Midtrans Snap JS -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
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
    
    <!-- Navbar -->
    <nav class="bg-white shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-lg">O</span>
                </div>
                <span class="text-xl font-bold text-gray-800">OtakAtik</span>
            </div>
            
            <!-- Progress Steps -->
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                    <span class="text-sm font-medium text-blue-600">Checkout</span>
                </div>
                <div class="w-8 h-0.5 bg-gray-300"></div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                    <span class="text-sm font-medium text-gray-500">Payment</span>
                </div>
                <div class="w-8 h-0.5 bg-gray-300"></div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">3</div>
                    <span class="text-sm font-medium text-gray-500">Complete</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Checkout Section -->
    <section class="pt-32 pb-20 px-6">
        <div class="max-w-4xl mx-auto">
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Order Summary -->
                <div class="md:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Order Summary</h2>
                        
                        <!-- Course Info -->
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
                        <!-- Voucher Code -->
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

                        <!-- Payment Method -->
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

                        <!-- Terms Agreement -->
                        <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-lg">
                            <input type="checkbox" id="termsAgreement" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 mt-1">
                            <label for="termsAgreement" class="text-sm text-gray-700">
                                I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and 
                                <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>. I understand that 
                                all payments are processed securely through Midtrans.
                            </label>
                        </div>
                        @else
                        <!-- Instructor Info -->
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

                <!-- Order Total -->
                <div class="md:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-32">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Order Total</h3>
                        
                        @if(Auth::user()->is_instructor)
                            <!-- Instructor Free Access -->
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
                            <!-- Regular User Payment -->
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

                            <!-- Development Only - Simulate Payment -->
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

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <div class="flex items-center justify-center gap-2 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-xl">O</span>
                </div>
                <span class="text-2xl font-bold">OtakAtik Academy</span>
            </div>
            <p class="text-gray-400 mb-4">Secure Learning Platform</p>
            <p class="text-gray-500 text-sm">&copy; 2025 OtakAtik Academy. All rights reserved.</p>
        </div>
    </footer>

    <script>
        let selectedPaymentMethod = '';
        let snapToken = '';
        let orderId = '';
        let finalPrice = {{ $finalPrice }};
        let courseId = {{ $course->id }};

        // Payment method selection
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                document.querySelectorAll('.payment-method').forEach(m => {
                    m.classList.remove('selected');
                });
                this.classList.add('selected');
                selectedPaymentMethod = this.dataset.method;
                document.getElementById('selectedPaymentMethod').value = selectedPaymentMethod;
                checkPaymentReady();
            });
        });

        // Terms agreement
        const termsElement = document.getElementById('termsAgreement');
        if (termsElement) {
            termsElement.addEventListener('change', checkPaymentReady);
        }

        function checkPaymentReady() {
            const termsChecked = document.getElementById('termsAgreement').checked;
            const payButton = document.getElementById('payButton');
            
            if (selectedPaymentMethod && termsChecked) {
                payButton.disabled = false;
            } else {
                payButton.disabled = true;
            }
        }

        // Apply voucher
        async function applyVoucher() {
            const voucherCode = document.getElementById('voucherCode').value;
            const messageDiv = document.getElementById('voucherMessage');

            if (!voucherCode) {
                messageDiv.innerHTML = '<p class="text-red-600">Please enter a voucher code</p>';
                return;
            }

            try {
                const response = await fetch('/checkout/voucher-check', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        voucher_code: voucherCode,
                        course_id: courseId
                    })
                });

                const data = await response.json();

                if (data.valid) {
                    messageDiv.innerHTML = `<p class="text-green-600">${data.message}</p>`;
                    document.getElementById('discountAmount').textContent = `-Rp${data.discount_amount.toLocaleString()}`;
                    document.getElementById('finalPrice').textContent = `Rp${data.final_price.toLocaleString()}`;
                    finalPrice = data.final_price;
                } else {
                    messageDiv.innerHTML = `<p class="text-red-600">${data.message}</p>`;
                    resetPrices();
                }
            } catch (error) {
                messageDiv.innerHTML = '<p class="text-red-600">Error checking voucher</p>';
                console.error('Voucher error:', error);
            }
        }

        function resetPrices() {
            document.getElementById('discountAmount').textContent = '-Rp0';
            document.getElementById('finalPrice').textContent = `Rp${finalPrice.toLocaleString()}`;
        }

        // Enroll as Instructor (Free)
        async function enrollFreeAsInstructor() {
            const button = event.target;
            button.disabled = true;
            button.innerHTML = 'Enrolling...';

            try {
                const response = await fetch(`/checkout/process/{{ $course->id }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        payment_method: 'instructor_free',
                        voucher_code: ''
                    })
                });

                const data = await response.json();

                if (data.success && data.is_instructor) {
                    // Instructor enrollment success - redirect to my courses
                    window.location.href = '/my-courses?enrolled=success';
                } else {
                    throw new Error(data.message || 'Enrollment failed');
                }
            } catch (error) {
                alert('Enrollment failed: ' + error.message);
                button.disabled = false;
                button.innerHTML = 'Enroll Now';
            }
        }

        // Process payment
        async function processPayment() {
            const voucherCode = document.getElementById('voucherCode').value;
            const payButton = document.getElementById('payButton');
            
            payButton.disabled = true;
            payButton.innerHTML = 'Processing...';

            try {
                const response = await fetch(`/checkout/process/{{ $course->id }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        payment_method: selectedPaymentMethod,
                        voucher_code: voucherCode
                    })
                });

                const data = await response.json();

                if (data.success) {
                    snapToken = data.snap_token;
                    orderId = data.order_id;
                    
                    // Open Midtrans Snap
                    window.snap.pay(snapToken, {
                        onSuccess: function(result) {
                            console.log('Payment success:', result);
                            window.location.href = '/purchase-history?payment=success';
                        },
                        onPending: function(result) {
                            console.log('Payment pending:', result);
                            window.location.href = '/purchase-history?payment=pending';
                        },
                        onError: function(result) {
                            console.log('Payment error:', result);
                            alert('Payment failed. Please try again.');
                            payButton.disabled = false;
                            payButton.innerHTML = 'Pay Now';
                        },
                        onClose: function() {
                            console.log('Payment popup closed');
                            payButton.disabled = false;
                            payButton.innerHTML = 'Pay Now';
                        }
                    });
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                alert('Payment processing failed: ' + error.message);
                payButton.disabled = false;
                payButton.innerHTML = 'Pay Now';
            }
        }

        // Simulate payment (development only)
        async function simulatePayment() {
            const voucherCode = document.getElementById('voucherCode').value;
            
            try {
                const response = await fetch(`/checkout/process/{{ $course->id }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        payment_method: 'bank_transfer',
                        voucher_code: voucherCode
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Redirect to simulation success page
                    window.location.href = `/checkout/simulate-success/${data.order_id}`;
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                alert('Simulation failed: ' + error.message);
            }
        }
    </script>

</body>
</html>