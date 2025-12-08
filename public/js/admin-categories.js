document.addEventListener('DOMContentLoaded', function() {
    // 1. Auto Close Alerts
    const alert = document.querySelector('.alert-dismissible');
    if (alert) setTimeout(() => alert.remove(), 4000);

    // 2. Modal Edit Logic
    const editModal = document.getElementById('editCategoryModal');
    const editForm = document.getElementById('editCategoryForm');
    const editName = document.getElementById('edit_name');
    const editDesc = document.getElementById('edit_description'); // Jika ada field deskripsi
    
    document.querySelectorAll('.btn-edit-category').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            // const desc = this.dataset.description; 

            if(editForm) editForm.action = `/admin/categories/${id}`;
            if(editName) editName.value = name;
            // if(editDesc) editDesc.value = desc;
            
            if(editModal) editModal.classList.remove('hidden');
        });
    });

    // 3. Close Modal Logic
    document.querySelectorAll('.btn-close-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            if(editModal) editModal.classList.add('hidden');
        });
    });

    // Close on click outside
    if(editModal) {
        editModal.addEventListener('click', (e) => {
            if (e.target === editModal) editModal.classList.add('hidden');
        });
    }
});