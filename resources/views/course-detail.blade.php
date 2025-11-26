@extends('layouts.app')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} - OtakAtik Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    
    @include('components.navbar')

    <!-- Course Detail Section -->
    <section class="pt-32 pb-20 px-6">
        <div class="max-w-4xl mx-auto">
            <!-- Course Header -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                <div class="h-48 bg-gradient-to-r from-blue-500 to-purple-600 relative">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                        <div class="text-center text-white">
                            <h1 class="text-4xl font-bold mb-4">{{ $course->title }}</h1>
                            <p class="text-xl opacity-90">{{ $course->type }} Course</p>
                            <p class="text-lg opacity-80 mt-2">by {{ $course->instructor->name ?? 'Tidak tersedia' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Content -->
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="md:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Course Description</h2>
                        <p class="text-gray-600 leading-relaxed">{{ $course->description }}</p>
                    </div>

                    <!-- Course Materials -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Course Materials</h2>
                        @if($course->materials->count() > 0)
                            <div class="space-y-4">
                                @foreach($course->materials as $material)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-blue-300 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-file-pdf text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $material->title }}</h4>
                                            @if($material->description)
                                            <p class="text-sm text-gray-600">{{ $material->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($isEnrolled)
                                    <a href="/storage/{{ $material->file_path }}" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-all flex items-center gap-2"
                                       target="_blank">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    @else
                                    <span class="text-gray-500 text-sm">Enroll to access</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">No materials available yet.</p>
                        @endif
                    </div>

                    <!-- Assignments -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Course Assignments</h2>
                        @if($course->assignments->count() > 0)
                            <div class="space-y-6">
                                @foreach($course->assignments as $assignment)
                                <div class="border border-gray-200 rounded-lg p-6 hover:border-blue-300 transition-colors">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h4 class="text-xl font-bold text-gray-800">{{ $assignment->title }}</h4>
                                            @if($assignment->description)
                                            <p class="text-gray-600 mt-2">{{ $assignment->description }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm text-gray-500">Due Date</div>
                                            <div class="font-semibold text-gray-800">{{ $assignment->due_date->format('d M Y, H:i') }}</div>
                                            <div class="text-sm text-blue-600 font-medium">{{ $assignment->max_points }} Points</div>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                        <h5 class="font-semibold text-gray-800 mb-2">Instructions:</h5>
                                        <p class="text-gray-700">{{ $assignment->instructions }}</p>
                                    </div>
                                    
                                    @if($isEnrolled)
                                    <a href="{{ route('student.assignment.submit.form', $assignment->id) }}" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-all inline-flex items-center gap-2">
                                        <i class="fas fa-upload"></i> Submit Assignment
                                    </a>
                                    @else
                                    <p class="text-gray-500 text-sm">Enroll to submit assignments</p>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">No assignments available yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="md:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-32">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Course Info</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600">Instructor</p>
                                <p class="font-semibold text-gray-800">{{ $course->instructor->name ?? 'Tidak tersedia' }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Course Type</p>
                                <p class="font-semibold text-gray-800">{{ $course->type }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Price</p>
                                @if($course->discount_percent > 0)
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl font-bold text-green-600">
                                        {{ 'Rp' . number_format($course->price * (1 - $course->discount_percent/100), 0, ',', '.') }}
                                    </span>
                                    <span class="text-lg text-gray-500 line-through">
                                        {{ 'Rp' . number_format($course->price, 0, ',', '.') }}
                                    </span>
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
                                        -{{ $course->discount_percent }}%
                                    </span>
                                </div>
                                @else
                                <span class="text-2xl font-bold text-gray-800">
                                    {{ 'Rp' . number_format($course->price, 0, ',', '.') }}
                                </span>
                                @endif
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Available Slots</p>
                                <p class="font-semibold text-gray-800">{{ $course->max_quota - $course->current_enrollment }} / {{ $course->max_quota }}</p>
                            </div>
                        </div>

                        @if(!$isEnrolled)
                        <button onclick="showRegistrationForm({{ $course->id }})" 
                                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-lg transition-all mt-6">
                            <i class="fas fa-shopping-cart mr-2"></i> Enroll Now
                        </button>
                        @else
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-6">
                            <p class="text-green-800 font-semibold text-center">
                                <i class="fas fa-check-circle mr-2"></i>You are enrolled in this course
                            </p>
                            <p class="text-green-600 text-sm text-center mt-2">Progress: {{ $userRegistration->progress }}%</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration Modal -->
    <div id="registrationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
            <!-- Header -->
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-3xl">🎓</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Ready to Enroll?</h3>
                <p class="text-gray-600 text-sm">Your profile information will be used</p>
            </div>

            <!-- Course Summary -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6 border border-blue-100">
                <p class="text-sm text-gray-700 mb-1">Course</p>
                <p class="font-bold text-gray-800">{{ $course->title }}</p>
                <p class="text-lg font-bold text-blue-600 mt-2">Rp{{ number_format($course->price, 0, ',', '.') }}</p>
            </div>

            <form action="{{ route('checkout.show', $course->id) }}" method="GET">
                <input type="hidden" name="course_id" id="course_id">
                
                <!-- Discount Code Section -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        🎁 Discount Code (Optional)
                    </label>
                    <div class="flex gap-2">
                        <input type="text" name="discount_code" id="discountCodeInput"
                               class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                               placeholder="Enter code if you have one">
                        <button type="button" onclick="validateDiscountCode()" 
                                class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-4 py-3 rounded-lg transition-all">
                            Check
                        </button>
                    </div>
                    <div id="discountMessage" class="mt-2 text-sm hidden"></div>
                </div>

                <!-- Price Summary -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6 space-y-2 border border-gray-200">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">Original Price</span>
                        <span class="font-medium text-gray-800" id="originalPrice">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm hidden" id="discountRowDiv">
                        <span class="text-gray-600">Discount</span>
                        <span class="font-medium text-green-600" id="discountAmount">-Rp0</span>
                    </div>
                    <div class="border-t border-gray-300 pt-2 flex justify-between items-center">
                        <span class="font-semibold text-gray-800">Total Price</span>
                        <span class="font-bold text-lg text-blue-600" id="finalPrice">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-all shadow-lg hover:shadow-xl">
                        <span class="flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i> Proceed to Checkout
                        </span>
                    </button>
                    <button type="button" onclick="hideRegistrationForm()" 
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let courseId = {{ $course->id }};
        let originalPrice = {{ $course->price }};

        function showRegistrationForm(cId) {
            document.getElementById('course_id').value = cId;
            document.getElementById('registrationModal').classList.remove('hidden');
            // Reset discount when opening modal
            resetDiscount();
        }
        
        function hideRegistrationForm() {
            document.getElementById('registrationModal').classList.add('hidden');
        }

        async function validateDiscountCode() {
            const code = document.getElementById('discountCodeInput').value.trim();
            const messageDiv = document.getElementById('discountMessage');
            const coursePrice = {{ $course->price }};

            if (!code) {
                messageDiv.classList.add('hidden');
                resetDiscount();
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
                        voucher_code: code,
                        course_id: courseId
                    })
                });

                const data = await response.json();

                if (data.valid) {
                    messageDiv.innerHTML = '<p class="text-green-600 font-semibold flex items-center gap-2"><i class="fas fa-check-circle"></i>' + data.message + '</p>';
                    messageDiv.classList.remove('hidden');
                    
                    // Update price display
                    const discountAmount = coursePrice - data.final_price;
                    document.getElementById('discountRowDiv').classList.remove('hidden');
                    document.getElementById('discountAmount').textContent = '-Rp' + formatPrice(discountAmount);
                    document.getElementById('finalPrice').textContent = 'Rp' + formatPrice(data.final_price);
                } else {
                    messageDiv.innerHTML = '<p class="text-red-600 font-semibold flex items-center gap-2"><i class="fas fa-times-circle"></i>' + data.message + '</p>';
                    messageDiv.classList.remove('hidden');
                    resetDiscount();
                }
            } catch (error) {
                messageDiv.innerHTML = '<p class="text-red-600">Error validating code</p>';
                messageDiv.classList.remove('hidden');
                resetDiscount();
            }
        }

        function resetDiscount() {
            document.getElementById('discountRowDiv').classList.add('hidden');
            document.getElementById('discountAmount').textContent = '-Rp0';
            document.getElementById('finalPrice').textContent = 'Rp' + formatPrice(originalPrice);
        }

        function formatPrice(price) {
            return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    </script>

</body>
</html>