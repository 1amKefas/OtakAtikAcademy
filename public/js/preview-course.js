document.addEventListener('alpine:init', () => {
    Alpine.data('previewCourse', () => ({
        sidebarOpen: false,

        init() {
            // Optional: Bisa tambah logic lain di sini kalau preview mode butuh interaksi lebih
            console.log('Preview Mode Initialized');
        },

        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        }
    }));
});