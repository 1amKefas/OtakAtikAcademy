document.addEventListener('DOMContentLoaded', function() {
    // Cari elemen alert notifikasi (bisa success atau error)
    const alertElement = document.querySelector('.fixed.top-6');

    if (alertElement) {
        setTimeout(() => {
            // Tambahkan transisi agar hilangnya halus
            alertElement.style.transition = 'opacity 0.5s ease-out';
            alertElement.style.opacity = '0';
            
            // Hapus dari DOM setelah transisi selesai
            setTimeout(() => {
                alertElement.remove();
            }, 500);
        }, 5000); // Tunggu 5 detik sebelum mulai menghilang
    }
});