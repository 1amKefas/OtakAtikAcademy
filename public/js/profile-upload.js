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

/* ==========================================
   TAMBAHAN: LOGIC WILAYAH INDONESIA (AUTO-SELECT)
   ========================================== */
document.addEventListener('DOMContentLoaded', () => {
    const provinceSelect = document.getElementById('provinceSelect');
    const citySelect = document.getElementById('citySelect');
    const locationInput = document.getElementById('locationInput');

    if (!provinceSelect || !citySelect || !locationInput) return;

    let selectedProvinceName = '';
    
    // Cek apakah ada data lama (Format: "Nama Kota, Nama Provinsi")
    const savedLocation = locationInput.value;
    let savedCity = '';
    let savedProvince = '';

    if (savedLocation && savedLocation.includes(',')) {
        const parts = savedLocation.split(',');
        savedCity = parts[0].trim();
        savedProvince = parts[1].trim();
    }

    // 1. Ambil Data Provinsi
    fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
        .then(response => response.json())
        .then(provinces => {
            provinces.forEach(province => {
                const option = document.createElement('option');
                option.value = province.id; 
                option.text = province.name;
                option.dataset.name = province.name;
                
                // AUTO-SELECT PROVINSI
                if (savedProvince && province.name.toUpperCase() === savedProvince.toUpperCase()) {
                    option.selected = true;
                    selectedProvinceName = province.name; // Simpan nama
                }
                
                provinceSelect.appendChild(option);
            });

            // Jika ada provinsi terpilih dari data lama, trigger load kota
            if (provinceSelect.value) {
                loadCities(provinceSelect.value, savedCity);
            }
        })
        .catch(err => console.error('Gagal load provinsi:', err));

    // Fungsi Load Kota (Dipisah biar bisa dipanggil manual)
    function loadCities(provinceId, autoSelectCity = null) {
        citySelect.innerHTML = '<option value="">Loading...</option>';
        citySelect.disabled = true;

        fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`)
            .then(response => response.json())
            .then(cities => {
                citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten...</option>';
                cities.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.name;
                    option.text = city.name;
                    
                    // AUTO-SELECT KOTA
                    if (autoSelectCity && city.name.toUpperCase() === autoSelectCity.toUpperCase()) {
                        option.selected = true;
                    }

                    citySelect.appendChild(option);
                });
                citySelect.disabled = false;
            });
    }

    // 2. Listener Ganti Provinsi (Manual User)
    provinceSelect.addEventListener('change', function() {
        const provinceId = this.value;
        selectedProvinceName = this.options[this.selectedIndex].dataset.name || '';
        
        if (provinceId) {
            loadCities(provinceId);
            // Reset input hidden sementara sampai user pilih kota
            locationInput.value = ''; 
        } else {
            citySelect.innerHTML = '<option value="">Pilih Provinsi Dahulu...</option>';
            citySelect.disabled = true;
        }
    });

    // 3. Listener Ganti Kota (Update Input Hidden)
    citySelect.addEventListener('change', function() {
        const cityName = this.value;
        if (cityName && selectedProvinceName) {
            locationInput.value = `${cityName}, ${selectedProvinceName}`;
        }
    });
});