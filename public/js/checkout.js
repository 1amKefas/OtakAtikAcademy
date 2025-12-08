document.addEventListener('DOMContentLoaded', function() {
    
    // --- VARIABLES (Diambil dari global scope yang di-pass via data-attributes nanti) ---
    let selectedPaymentMethod = '';
    let snapToken = '';
    let orderId = '';
    
    // Ambil data harga dari elemen HTML (agar tidak hardcode PHP di JS file ini)
    const priceElement = document.getElementById('finalPriceData');
    let finalPrice = priceElement ? parseFloat(priceElement.dataset.price) : 0;
    
    const courseElement = document.getElementById('courseData');
    let courseId = courseElement ? parseInt(courseElement.dataset.id) : 0;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // --- LOADING HELPERS ---
    window.showLoading = () => document.getElementById('loadingOverlay').classList.remove('hidden');
    window.hideLoading = () => document.getElementById('loadingOverlay').classList.add('hidden');

    // --- PAYMENT METHOD SELECTION ---
    document.querySelectorAll('.payment-method').forEach(method => {
        method.addEventListener('click', function() {
            // Reset semua style
            document.querySelectorAll('.payment-method').forEach(m => {
                m.classList.remove('selected', 'border-blue-500', 'bg-blue-50');
                m.classList.add('border-gray-200');
            });
            
            // Highlight yang dipilih
            this.classList.add('selected', 'border-blue-500', 'bg-blue-50');
            this.classList.remove('border-gray-200');
            
            // Simpan value
            selectedPaymentMethod = this.dataset.method;
            const input = document.getElementById('selectedPaymentMethod');
            if(input) input.value = selectedPaymentMethod;
            
            // Cek apakah tombol Pay boleh aktif
            checkPaymentReady();
        });
    });

    // --- TERMS CHECKBOX ---
    const termsElement = document.getElementById('termsAgreement');
    if (termsElement) {
        termsElement.addEventListener('change', checkPaymentReady);
    }

    // --- CHECK READY STATE ---
    function checkPaymentReady() {
        const termsChecked = document.getElementById('termsAgreement') ? document.getElementById('termsAgreement').checked : true;
        const payButton = document.getElementById('payButton');
        
        if (payButton) {
            // Tombol Pay hanya nyala jika Method dipilih & Terms dicentang
            if (selectedPaymentMethod && termsChecked) {
                payButton.disabled = false;
                payButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                payButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
            } else {
                payButton.disabled = true;
                payButton.classList.add('bg-gray-400', 'cursor-not-allowed');
                payButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            }
        }
    }

    // --- VOUCHER LOGIC ---
    window.applyVoucher = async function() {
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
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    voucher_code: voucherCode,
                    course_id: courseId
                })
            });

            const data = await response.json();

            if (data.valid) {
                messageDiv.innerHTML = `<p class="text-green-600 font-medium"><i class="fas fa-check-circle"></i> ${data.message}</p>`;
                document.getElementById('discountAmount').textContent = `-Rp${data.discount_amount.toLocaleString()}`;
                document.getElementById('finalPrice').textContent = `Rp${data.final_price.toLocaleString()}`;
                finalPrice = data.final_price;
            } else {
                messageDiv.innerHTML = `<p class="text-red-600"><i class="fas fa-times-circle"></i> ${data.message}</p>`;
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

    // --- ENROLL INSTRUCTOR (FREE) ---
    window.enrollFreeAsInstructor = async function() {
        showLoading(); 

        try {
            const response = await fetch(`/checkout/process/${courseId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    payment_method: 'instructor_free',
                    voucher_code: ''
                })
            });

            const data = await response.json();

            if (data.success && data.is_instructor) {
                window.location.href = '/my-courses?enrolled=success';
            } else {
                throw new Error(data.message || 'Enrollment failed');
            }
        } catch (error) {
            hideLoading();
            alert('Enrollment failed: ' + error.message);
        }
    }

    // --- REAL PAYMENT (MIDTRANS) ---
    window.processPayment = async function() {
        if (!selectedPaymentMethod) {
            alert('Silakan pilih metode pembayaran terlebih dahulu!');
            return;
        }

        const voucherCode = document.getElementById('voucherCode') ? document.getElementById('voucherCode').value : '';
        
        showLoading(); 

        try {
            const response = await fetch(`/checkout/process/${courseId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    payment_method: selectedPaymentMethod,
                    voucher_code: voucherCode
                })
            });

            const data = await response.json();

            if (data.success) {
                hideLoading(); 
                
                snapToken = data.snap_token;
                orderId = data.order_id;
                
                if (window.snap) {
                    window.snap.pay(snapToken, {
                        onSuccess: function(result) {
                            showLoading(); 
                            window.location.href = '/purchase-history?payment=success';
                        },
                        onPending: function(result) {
                            showLoading(); 
                            window.location.href = '/purchase-history?payment=pending';
                        },
                        onError: function(result) {
                            console.log('Payment error:', result);
                            alert('Payment failed. Please try again.');
                        },
                        onClose: function() {
                            console.log('Payment popup closed');
                        }
                    });
                } else {
                    alert("Midtrans Snap not loaded");
                }
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            hideLoading();
            alert('Payment processing failed: ' + error.message);
        }
    }

    // --- SIMULATE PAYMENT (DEV MODE) ---
    window.simulatePayment = async function() {
        const voucherCode = document.getElementById('voucherCode') ? document.getElementById('voucherCode').value : '';
        
        if (!selectedPaymentMethod) {
            alert('Harap pilih Payment Method terlebih dahulu untuk simulasi!');
            return;
        }

        const btn = event.target;
        const originalText = btn.innerText;
        btn.classList.remove('bg-green-600', 'hover:bg-green-700');
        btn.classList.add('bg-green-800', 'cursor-wait');
        btn.innerText = 'Simulating...';
        
        showLoading(); 

        try {
            const response = await fetch(`/checkout/process/${courseId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    payment_method: selectedPaymentMethod,
                    voucher_code: voucherCode
                })
            });

            const data = await response.json();

            if (data.success) {
                window.location.href = `/checkout/simulate-success/${data.order_id}`;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            hideLoading();
            btn.classList.add('bg-green-600', 'hover:bg-green-700');
            btn.classList.remove('bg-green-800', 'cursor-wait');
            btn.innerText = originalText;
            
            alert('Simulation failed: ' + error.message);
        }
    }
});