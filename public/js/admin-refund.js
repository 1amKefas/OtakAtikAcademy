document.addEventListener('DOMContentLoaded', function() {
    // 1. Handle Alert Auto-Close (Success & Error)
    const alerts = ['successAlert', 'errorAlert'];
    alerts.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            setTimeout(() => {
                element.style.transition = 'opacity 0.5s ease';
                element.style.opacity = '0';
                setTimeout(() => element.remove(), 500);
            }, 5000);
        }
    });

    // 2. Modal Listener: Click Outside
    const rejectModal = document.getElementById('rejectModal');
    if (rejectModal) {
        rejectModal.addEventListener('click', function(e) {
            if (e.target === this) {
                window.closeRejectModal();
            }
        });
    }

    // 3. Modal Listener: ESC Key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeRejectModal();
        }
    });

    // 4. Form Validation
    const rejectForm = document.getElementById('rejectForm');
    if (rejectForm) {
        rejectForm.addEventListener('submit', function(e) {
            const textarea = this.querySelector('textarea[name="rejection_reason"]');
            if (textarea && textarea.value.trim().length < 10) {
                e.preventDefault();
                alert('Alasan penolakan minimal 10 karakter!');
                textarea.focus();
            }
        });
    }
});

// --- Global Functions (Exposed to Window) ---

window.showRejectModal = function(refundId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    if (modal && form) {
        form.action = `/admin/refunds/${refundId}/reject`;
        modal.classList.remove('hidden');
    }
};

window.closeRejectModal = function() {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    if (modal) {
        modal.classList.add('hidden');
    }
    if (form) {
        form.reset();
    }
};