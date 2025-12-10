document.addEventListener('DOMContentLoaded', () => {
    // Initial state setup
    const typeRadio = document.querySelector('input[name="question_type"]:checked');
    const type = typeRadio ? typeRadio.value : 'multiple_choice';
    
    window.changeQuestionType(type);
    window.updateCorrectAnswerOptions();
    
    const optionsContainer = document.getElementById('optionsContainer');
    if (optionsContainer) {
        optionsContainer.addEventListener('input', (e) => {
            if(e.target.name === 'options[]') window.updateCorrectAnswerOptions();
        });
    }
});

// --- Global Functions ---

window.changeQuestionType = function(type) {
    // Hide all sections
    ['mcCorrectAnswer', 'msCorrectAnswer', 'tfCorrectAnswer', 'essayCorrectAnswer', 'optionsSection'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    });

    // Show active section
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
    // Re-index letters
    document.querySelectorAll('.optionItem').forEach((el, idx) => {
        el.querySelector('span').innerText = String.fromCharCode(65 + idx);
    });
    window.updateCorrectAnswerOptions();
};

// public/js/instructor-quiz-question.js

window.updateCorrectAnswerOptions = function() {
    const options = Array.from(document.querySelectorAll('input[name="options[]"]')).map(el => el.value);
    const containerRadio = document.getElementById('correctAnswerOptions');
    const containerCheck = document.getElementById('correctAnswerCheckboxes');
    
    let htmlRadio = '';
    let htmlCheck = '';

    // Ambil data yang kita lempar dari Blade tadi
    let savedAnswer = window.savedCorrectAnswer; 

    options.forEach((opt, idx) => {
        if(opt) {
            const letter = String.fromCharCode(65 + idx);
            const strIdx = String(idx); // Convert index ke string biar aman bandinginnya

            // LOGIC CHECKED UNTUK RADIO (Single Choice)
            // Cek apakah index ini SAMA dengan yang tersimpan di DB
            let isRadioChecked = '';
            if (savedAnswer !== null && String(savedAnswer) === strIdx) {
                isRadioChecked = 'checked';
            }

            // LOGIC CHECKED UNTUK CHECKBOX (Multiple Select)
            // Kalau multiple, DB nyimpennya string JSON '["0","2"]', jadi kita parse dulu kalau string
            let isBoxChecked = '';
            if (savedAnswer !== null) {
                let ansArray = [];
                try {
                    // Kalau tipe datanya array (dari json blade), pake langsung. Kalau string, parse.
                    ansArray = Array.isArray(savedAnswer) ? savedAnswer : JSON.parse(savedAnswer);
                    
                    // Pastikan semua elemen jadi string buat perbandingan
                    ansArray = ansArray.map(String);
                    
                    if (ansArray.includes(strIdx)) {
                        isBoxChecked = 'checked';
                    }
                } catch (e) {
                    // Fallback kalau bukan JSON valid (misal data lama)
                    console.log("Error parsing answers", e);
                }
            }

            // Radio HTML
            htmlRadio += `<label class="flex items-center cursor-pointer p-2 hover:bg-gray-50 rounded">
                <input type="radio" name="correct_answer" value="${idx}" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500" ${isRadioChecked}>
                <span class="ml-2 text-gray-800 font-medium">${letter}. ${opt}</span>
            </label>`;
            
            // Checkbox HTML
            htmlCheck += `<label class="flex items-center cursor-pointer p-2 hover:bg-gray-50 rounded">
                <input type="checkbox" name="correct_answers[]" value="${idx}" class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500" ${isBoxChecked}>
                <span class="ml-2 text-gray-800 font-medium">${letter}. ${opt}</span>
            </label>`;
        }
    });

    if(containerRadio) containerRadio.innerHTML = htmlRadio;
    if(containerCheck) containerCheck.innerHTML = htmlCheck;
};