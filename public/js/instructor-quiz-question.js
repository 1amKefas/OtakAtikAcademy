document.addEventListener('DOMContentLoaded', () => {
    // 1. Initial Setup
    const typeRadio = document.querySelector('input[name="question_type"]:checked');
    const type = typeRadio ? typeRadio.value : 'multiple_choice';
    
    // [FIX] Inisialisasi State Jawaban dari Data Server
    if (typeof window.savedCorrectAnswer !== 'undefined') {
        window.currentSelection = window.savedCorrectAnswer;
    } else {
        window.currentSelection = null;
    }

    // Jalankan fungsi awal
    window.changeQuestionType(type);
    window.updateCorrectAnswerOptions();
    
    // 2. Listener untuk Update Opsi saat ngetik
    const optionsContainer = document.getElementById('optionsContainer');
    if (optionsContainer) {
        optionsContainer.addEventListener('input', (e) => {
            if(e.target.name === 'options[]') window.updateCorrectAnswerOptions();
        });
    }

    // 3. [FIX] Listener Global untuk "Mengingat" Jawaban yang dipilih User
    // Kita pasang di document body (delegation) karena elemen radio/checkbox sering dibuat ulang
    document.addEventListener('change', (e) => {
        // Jika yang berubah adalah Radio Button Jawaban Benar
        if (e.target.name === 'correct_answer') {
            window.currentSelection = e.target.value;
        }
        // Jika yang berubah adalah Checkbox Jawaban Benar
        else if (e.target.name === 'correct_answers[]') {
            // Ambil semua checkbox yang dicentang, simpan sebagai Array string ID
            const checkedBoxes = Array.from(document.querySelectorAll('input[name="correct_answers[]"]:checked'))
                                      .map(cb => cb.value);
            // Simpan dalam format JSON string biar konsisten sama format DB/SavedAnswer
            window.currentSelection = JSON.stringify(checkedBoxes);
        }
    });
});

// --- Global Functions ---

window.changeQuestionType = function(type) {
    ['mcCorrectAnswer', 'msCorrectAnswer', 'tfCorrectAnswer', 'essayCorrectAnswer', 'optionsSection'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    });

    const optionsSection = document.getElementById('optionsSection');
    
    if (type === 'multiple_choice') {
        document.getElementById('mcCorrectAnswer').classList.remove('hidden');
        optionsSection.classList.remove('hidden');
    } else if (type === 'multiple_select') {
        document.getElementById('msCorrectAnswer').classList.remove('hidden');
        optionsSection.classList.remove('hidden');
    } else if (type === 'true_false') {
        document.getElementById('tfCorrectAnswer').classList.remove('hidden');
    } else if (type === 'essay') {
        document.getElementById('essayCorrectAnswer').classList.remove('hidden');
    }
};

window.addOption = function() {
    const container = document.getElementById('optionsContainer');
    const count = container.children.length;
    const letter = String.fromCharCode(65 + count);
    const html = `
        <div class="flex items-center gap-3 optionItem">
            <span class="text-sm font-bold text-gray-600 w-6">${letter}</span>
            <input type="text" name="options[]" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg" placeholder="Masukkan pilihan...">
            <button type="button" onclick="removeOption(this)" class="bg-red-500 text-white px-3 py-2 rounded">🗑️</button>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
    window.updateCorrectAnswerOptions();
};

window.removeOption = function(btn) {
    btn.closest('.optionItem').remove();
    document.querySelectorAll('.optionItem').forEach((el, idx) => {
        el.querySelector('span').innerText = String.fromCharCode(65 + idx);
    });
    window.updateCorrectAnswerOptions();
};

window.updateCorrectAnswerOptions = function() {
    const options = Array.from(document.querySelectorAll('input[name="options[]"]')).map(el => el.value);
    const containerRadio = document.getElementById('correctAnswerOptions');
    const containerCheck = document.getElementById('correctAnswerCheckboxes');
    
    let htmlRadio = '';
    let htmlCheck = '';

    // [FIX] Gunakan currentSelection (Data Real-time) bukan savedCorrectAnswer (Data Basi)
    let selectedAnswer = window.currentSelection;

    options.forEach((opt, idx) => {
        if(opt) {
            const letter = String.fromCharCode(65 + idx);
            const strIdx = String(idx);

            // LOGIC CHECKED RADIO
            let isRadioChecked = '';
            if (selectedAnswer !== null && String(selectedAnswer) === strIdx) {
                isRadioChecked = 'checked';
            }

            // LOGIC CHECKED CHECKBOX
            let isBoxChecked = '';
            if (selectedAnswer !== null) {
                let ansArray = [];
                try {
                    // Coba parse JSON, kalau gagal berarti mungkin single value atau array JS
                    ansArray = Array.isArray(selectedAnswer) ? selectedAnswer : JSON.parse(selectedAnswer);
                    
                    // Normalisasi ke array of strings
                    if(Array.isArray(ansArray)) {
                        ansArray = ansArray.map(String);
                        if (ansArray.includes(strIdx)) {
                            isBoxChecked = 'checked';
                        }
                    }
                } catch (e) {
                    // Fallback sederhana jika format aneh
                    if (String(selectedAnswer) === strIdx) isBoxChecked = 'checked';
                }
            }

            htmlRadio += `<label class="flex items-center cursor-pointer p-2 hover:bg-gray-50 rounded">
                <input type="radio" name="correct_answer" value="${idx}" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500" ${isRadioChecked}>
                <span class="ml-2 text-gray-800 font-medium">${letter}. ${opt}</span>
            </label>`;

            htmlCheck += `<label class="flex items-center cursor-pointer p-2 hover:bg-gray-50 rounded">
                <input type="checkbox" name="correct_answers[]" value="${idx}" class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500" ${isBoxChecked}>
                <span class="ml-2 text-gray-800 font-medium">${letter}. ${opt}</span>
            </label>`;
        }
    });

    if(containerRadio) containerRadio.innerHTML = htmlRadio;
    if(containerCheck) containerCheck.innerHTML = htmlCheck;
};