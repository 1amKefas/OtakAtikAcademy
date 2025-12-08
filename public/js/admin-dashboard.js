document.addEventListener('DOMContentLoaded', function() {
    // Cari elemen alert notifikasi
    const successAlert = document.querySelector('.bg-green-500.fixed.top-6');
    const errorAlert = document.querySelector('.bg-red-500.fixed.top-6');

    // Fungsi untuk menghilangkan alert
    const removeAlert = (alertElement) => {
        if (alertElement) {
            setTimeout(() => {
                alertElement.style.transition = 'opacity 0.5s ease-out'; // Tambah efek fade out halus
                alertElement.style.opacity = '0';
                setTimeout(() => {
                    alertElement.remove();
                }, 500); // Tunggu transisi selesai baru remove DOM
            }, 5000);
        }
    };

    removeAlert(successAlert);
    removeAlert(errorAlert);
});