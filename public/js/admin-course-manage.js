let cropper = null;
let moduleCount = 0;

document.addEventListener('DOMContentLoaded', function() {
    // 1. Module Management
    const modulesContainer = document.getElementById('modules-container');
    const btnAddModule = document.getElementById('btnAddModule');
    
    // Inisialisasi Sortable dan Logic Tambah Modul
    if (modulesContainer) {
        Sortable.create(modulesContainer, {
            handle: '.handle', animation: 150, ghostClass: 'sortable-ghost'
        });
        
        moduleCount = modulesContainer.children.length;
        if (moduleCount === 0) addModuleInput(); // Default ada 1 input

        // Listener Tombol Tambah Modul
        if (btnAddModule) {
            btnAddModule.addEventListener('click', addModuleInput);
        }

        // Listener Tombol Hapus (Event Delegation)
        modulesContainer.addEventListener('click', function(e) {
            const btnRemove = e.target.closest('.btn-remove-module');
            if (btnRemove) {
                btnRemove.closest('.module-group').remove();
            }
        });
    }

    // 2. Instructor Toggle
    const typeSelect = document.getElementById('courseType');
    if (typeSelect) {
        typeSelect.addEventListener('change', function() {
            toggleInstructorField(this.value);
        });
        // Run on load
        toggleInstructorField(typeSelect.value);
    }

    // 3. Cropper Logic
    const imageInput = document.getElementById('imageInput');
    const btnDoCrop = document.getElementById('btn-do-crop');
    const closeButtons = document.querySelectorAll('.btn-close-crop');

    // Listener Input Gambar
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    const img = document.getElementById('imageToCrop');
                    img.src = evt.target.result;
                    document.getElementById('cropModal').classList.remove('hidden');
                    if (cropper) cropper.destroy();
                    cropper = new Cropper(img, { aspectRatio: 16/9, viewMode: 1, autoCropArea: 1 });
                };
                reader.readAsDataURL(files[0]);
            }
        });
    }

    // Listener Tombol Crop (Simpan)
    if (btnDoCrop) {
        btnDoCrop.addEventListener('click', function() {
            if (!cropper) return;
            cropper.getCroppedCanvas({width: 800, height: 450}).toBlob((blob) => {
                const dt = new DataTransfer();
                dt.items.add(new File([blob], "thumb.jpg", {type: "image/jpeg"}));
                imageInput.files = dt.files;
                
                const preview = document.getElementById('imagePreview');
                preview.src = URL.createObjectURL(blob);
                preview.classList.remove('hidden');
                document.getElementById('imagePlaceholder').classList.add('hidden');
                document.getElementById('imageOverlay').classList.remove('hidden');
                
                closeCropperModal();
            }, 'image/jpeg', 0.85);
        });
    }

    // Listener Tombol Close Modal
    closeButtons.forEach(btn => {
        btn.addEventListener('click', closeCropperModal);
    });

    // 4. Auto-close Alerts
    ['alert-success', 'alert-error'].forEach(id => {
        const el = document.getElementById(id);
        if(el) setTimeout(() => el.remove(), 5000);
    });
});

// --- Helper Functions ---

function addModuleInput() {
    const container = document.getElementById('modules-container');
    const index = moduleCount++;
    const html = `
        <div class="flex items-center gap-2 group module-group">
            <div class="flex-1 bg-gray-50 p-3 rounded-lg border border-gray-200 flex items-center gap-3">
                <span class="text-gray-400 font-bold px-2 cursor-grab handle"><i class="fas fa-grip-vertical"></i></span>
                <input type="text" name="modules[${index}][title]" placeholder="Nama Modul" class="flex-1 bg-transparent border-none focus:ring-0 text-sm" required>
            </div>
            <button type="button" class="p-2 text-red-400 hover:bg-red-50 rounded-lg btn-remove-module">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
}

function toggleInstructorField(type) {
    const input = document.getElementById('instructor_id');
    const req = document.getElementById('instructor-required');
    const note = document.getElementById('instructor-note');
    const isOnline = type === 'Full Online';

    if(input) {
        isOnline ? input.removeAttribute('required') : input.setAttribute('required', 'required');
        if(req) req.style.display = isOnline ? 'none' : 'inline';
        if(note) {
            note.innerHTML = isOnline ? 'Opsional' : 'Wajib';
            note.className = isOnline ? 'text-[10px] text-blue-500 mt-1' : 'text-[10px] text-gray-500 mt-1';
        }
    }
}

function closeCropperModal() {
    document.getElementById('cropModal').classList.add('hidden');
    if (cropper) cropper.destroy();
    if (document.getElementById('imagePreview').classList.contains('hidden')) {
        document.getElementById('imageInput').value = '';
    }
}