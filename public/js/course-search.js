document.addEventListener('alpine:init', () => {
    Alpine.data('courseSearch', (initialQuery = '') => ({
        query: initialQuery,
        suggestions: [],
        showSuggestions: false,
        active: -1,
        
        search() {
            // Minimal 2 karakter baru mencari
            if (this.query.length < 2) {
                this.suggestions = [];
                this.showSuggestions = false;
                return;
            }
            
            // Fetch ke endpoint search API
            // Pastikan route '/courses/search' sudah ada di web.php
            fetch(`/courses/search?query=${this.query}`)
                .then(res => res.json())
                .then(data => {
                    this.suggestions = data;
                    this.showSuggestions = true;
                    this.active = -1; // Reset pilihan keyboard
                })
                .catch(err => {
                    console.error('Search error:', err);
                });
        },
        
        select(courseId) {
            // Redirect ke halaman detail course
            window.location.href = `/course/${courseId}`;
        },
        
        clear() {
            this.query = '';
            this.suggestions = [];
            this.showSuggestions = false;
            // Submit form kosong untuk reset filter server-side jika perlu
            // this.$el.closest('form').submit(); 
        }
    }));
});