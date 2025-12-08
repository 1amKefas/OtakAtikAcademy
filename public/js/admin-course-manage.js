let cropper = null;
let moduleCount = 0;

document.addEventListener('DOMContentLoaded', function() {
    // 1. Toggle Instructor Field Logic
    const typeSelect = document.querySelector('select[name="type"]');
    if (typeSelect) {
        // Initial check
        window.toggleInstructorField(typeSelect.value);
        
        // Listener
        typeSelect.addEventListener('change', function() {
            window.toggleInstructorField(this.value);
        });
    }

    // 2. SortableJS Init (Untuk drag & drop modul)
    const modulesContainer = document.getElementById('modules-container');
    if (modulesContainer) {
        Sortable.create(modulesContainer, {
            handle: '.handle',
            animation: 150,
            ghostClass: 'sortable-ghost'
        });

        // Tambah 1 modul kosong jika belum ada (saat create)
        if (modulesContainer.children.length === 0) {
            window.addModuleInput();
        } else {
             moduleCount = modulesContainer.children.length;
        }
    }

    // 3. Image Cropper Logic
    const imageInput = document.getElementById('imageInput');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const file = files[0];
                
                if (!file.type.startsWith('image/')) {
                    alert('File harus berupa gambar!');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    const imageToCrop = document.getElementById('imageToCrop');
                    imageToCrop.src = event.target.result;
                    document.getElementById('cropModal').classList.remove('hidden');
                    
                    if (cropper) cropper.destroy();
                    
                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: 16 / 9,
                        viewMode: 1,
                        autoCropArea: 1,
                        background: false,
                        responsive: true,
                    });
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // 4. Auto-close Alerts
    const successAlert = document.getElementById('alert-success');
    const errorAlert = document.getElementById('alert-error');
    if(successAlert) setTimeout(() => successAlert.remove(), 5000);
    if(errorAlert) setTimeout(() => errorAlert.remove(), 5000);
});

// --- Global Functions (Exposed for onclick attributes) ---

window.toggleInstructorField = function(type) {
    const instructorInput = document.getElementById('instructor_id');
    const requiredStar = document.getElementById('instructor-required');
    const noteText = document.getElementById('instructor-note');
    
    if (type === 'Full Online') {
        instructorInput.removeAttribute('required');
        requiredStar.style.display = 'none';
        noteText.innerHTML = 'Opsional untuk Full Online (Bisa dipilih jika ada)';
        noteText.classList.add('text-blue-500');
        noteText.classList.remove('text-gray-500');
    } else {
        instructorInput.setAttribute('required', 'required');
        requiredStar.style.display = 'inline';
        noteText.innerHTML = 'Wajib untuk Hybrid & Tatap Muka';
        noteText.classList.remove('text-blue-500');
        noteText.classList.add('text-gray-500');
    }
};

window.cropImage = function() {
    if (!cropper) return;
    
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const imagePlaceholder = document.getElementById('imagePlaceholder');
    const imageOverlay = document.getElementById('imageOverlay');

    cropper.getCroppedCanvas({
        width: 800, 
        height: 450,
        fillColor: '#fff'
    }).toBlob((blob) => {
        const newFile = new File([blob], "thumbnail_cropped.jpg", { type: "image/jpeg" });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(newFile);
        imageInput.files = dataTransfer.files;

        const url = URL.createObjectURL(blob);
        imagePreview.src = url;
        imagePreview.classList.remove('hidden');
        imagePlaceholder.classList.add('hidden');
        imageOverlay.classList.remove('hidden');
        
        window.closeCropper();
    }, 'image/jpeg', 0.85);
};

window.closeCropper = function() {
    document.getElementById('cropModal').classList.add('hidden');
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    const imagePreview = document.getElementById('imagePreview');
    const imageInput = document.getElementById('imageInput');
    if (imagePreview.classList.contains('hidden')) {
        imageInput.value = '';
    }
};

window.addModuleInput = function() {
    const container = document.getElementById('modules-container');
    const index = moduleCount;

    const html = `
        <div class="flex items-center gap-2 group" id="module-row-${index}">
            <div class="flex-1 bg-gray-50 p-3 rounded-lg border border-gray-200 flex items-center gap-3">
                <span class="text-gray-400 font-bold px-2 cursor-grab handle"><i class="fas fa-grip-vertical"></i></span>
                <input type="text" name="modules[${index}][title]" placeholder="Nama Modul (Contoh: Pengenalan HTML)" 
                       class="flex-1 bg-transparent border-none focus:ring-0 text-sm font-medium text-gray-800 placeholder-gray-400" required>
            </div>
            <button type="button" onclick="removeModuleRow(${index})" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
    moduleCount++;
};

window.removeModuleRow = function(index) {
    const row = document.getElementById(`module-row-${index}`);
    if(row) row.remove();
};