let cropper = null;
// Ambil jumlah modul awal dari elemen HTML (misal hidden input atau hitung elemen)
// Tapi karena di script lama pakai variabel PHP, kita akali dengan menghitung elemen yang ada saat load.
let editModuleCount = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Hitung jumlah modul yang sudah ada untuk index selanjutnya
    const existingModules = document.querySelectorAll('.module-item');
    editModuleCount = existingModules.length + 100; // Offset biar aman

    // --- 1. Cropper Logic ---
    const imageInput = document.getElementById('imageInput');
    const cropModal = document.getElementById('cropModal');
    const imageToCrop = document.getElementById('imageToCrop');
    
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const file = files[0];
                if (!file.type.startsWith('image/')) {
                    alert('Mohon upload file gambar (JPG/PNG).');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(event) {
                    imageToCrop.src = event.target.result;
                    cropModal.classList.remove('hidden');
                    if (cropper) cropper.destroy();
                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: 16 / 9, viewMode: 1, autoCropArea: 1, background: false, responsive: true,
                    });
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // --- 2. Sortable Logic ---
    var el = document.getElementById('admin-modules-list');
    if(el) {
        Sortable.create(el, {
            handle: '.handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function (evt) {
                var orderedIds = this.toArray();
                // Ambil URL dari atribut data container atau meta tag (opsional, jika perlu AJAX reorder real-time)
                // Di kode lama ada fetch ke route reorder, tapi karena ini form edit biasa, 
                // urutan biasanya disave saat submit form utama. 
                // TAPI, di script lama ada fetch. Jadi kita pertahankan fetch-nya.
                
                // Note: Kita butuh Course ID. Ambil dari atribut data
                const courseId = el.getAttribute('data-course-id'); 
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                fetch(`/admin/courses/${courseId}/modules/reorder`, { // Pastikan route ini sesuai
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrfToken },
                    body: JSON.stringify({ ordered_ids: orderedIds })
                }).then(res => res.json())
                  .then(data => console.log('Reorder berhasil'))
                  .catch(err => console.error('Reorder error', err));
            }
        });
    }
    
    // Auto remove alerts
    const successAlert = document.getElementById('alert-success');
    const errorAlert = document.getElementById('alert-error');
    if(successAlert) setTimeout(() => successAlert.remove(), 4000);
});

// --- Global Functions ---

window.cropImage = function() {
    if (!cropper) return;
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const imagePlaceholder = document.getElementById('imagePlaceholder');
    const imageOverlay = document.getElementById('imageOverlay');

    cropper.getCroppedCanvas({ width: 800, height: 450, fillColor: '#fff' }).toBlob((blob) => {
        const newFile = new File([blob], "thumbnail_edited.jpg", { type: "image/jpeg" });
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
    if (cropper) { cropper.destroy(); cropper = null; }
};

window.toggleInstructorField = function(type) {
    // Logic handled backend or CSS state if needed. 
    // Di view lama function ini kosong, jadi biarkan kosong atau implementasi sesuai kebutuhan.
};

window.addEditModuleInput = function() {
    const emptyMsg = document.getElementById('no-modules-msg');
    if(emptyMsg) emptyMsg.remove();

    const container = document.getElementById('admin-modules-list');
    const index = editModuleCount;

    const html = `
        <div class="flex items-center gap-2 group module-item" id="edit-module-row-new-${index}" data-id="new-${index}">
            <div class="flex-1 bg-blue-50 p-3 rounded-lg border border-blue-200 flex items-center gap-3">
                <span class="text-blue-400 font-bold px-2 cursor-grab handle"><i class="fas fa-grip-vertical"></i></span>
                <input type="text" name="modules[new_${index}][title]" placeholder="Judul Modul Baru..." 
                       class="flex-1 bg-transparent border-none focus:ring-0 text-sm font-medium text-gray-800 placeholder-blue-300" required>
            </div>
            <button type="button" onclick="removeEditModuleRow('new-${index}')" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    editModuleCount++;
};

window.removeEditModuleRow = function(idSuffix) {
    const row = document.getElementById(`edit-module-row-${idSuffix}`);
    if(row) row.remove();
};