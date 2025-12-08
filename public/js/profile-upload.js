document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('profilePictureInput');
    const fileNameDisplay = document.getElementById('fileName');

    // Jika elemen tidak ditemukan (misal di halaman lain), stop.
    if (!dropZone || !fileInput) return;

    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Highlight drop zone when dragging over it
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.add('border-purple-500', 'bg-purple-50', 'ring-2', 'ring-purple-200');
        });
    });

    // Remove highlight when dragging leaves or drops
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.remove('border-purple-500', 'bg-purple-50', 'ring-2', 'ring-purple-200');
        });
    });

    // Handle Drop
    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            fileInput.files = files; // Assign file to input
            updateFileName(files[0]);
        }
    });

    // Handle Click / Change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            updateFileName(e.target.files[0]);
        }
    });

    function updateFileName(file) {
        if (fileNameDisplay) {
            // Validasi sederhana (Image only)
            if (!file.type.startsWith('image/')) {
                fileNameDisplay.textContent = '❌ File harus berupa gambar (JPG/PNG)';
                fileNameDisplay.className = 'text-sm text-red-600 mt-2 font-medium';
                fileInput.value = ''; // Reset input
                return;
            }

            // Validasi Size (Max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                fileNameDisplay.textContent = '❌ Ukuran file terlalu besar (Maks 5MB)';
                fileNameDisplay.className = 'text-sm text-red-600 mt-2 font-medium';
                fileInput.value = '';
                return;
            }

            fileNameDisplay.innerHTML = `<i class="fas fa-check-circle"></i> File dipilih: <strong>${file.name}</strong>`;
            fileNameDisplay.className = 'text-sm text-green-600 mt-2 font-medium';
        }
    }
});