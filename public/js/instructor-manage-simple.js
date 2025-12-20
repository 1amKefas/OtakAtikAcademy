// Simple Modal Management without Alpine.js
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const courseId = document.querySelector('meta[name="course-id"]')?.content;
    
    // Helper: Get day of week
    window.getDayOfWeek = function(dateString) {
        const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const date = new Date(dateString + 'T00:00:00');
        return hari[date.getDay()];
    };
    
    // Helper: Update module type form visibility
    window.updateModuleType = function(type) {
        const learningForm = document.getElementById('moduleLearningForm');
        const announcementForm = document.getElementById('moduleAnnouncementForm');
        
        if (type === 'module') {
            learningForm.style.display = 'block';
            announcementForm.style.display = 'none';
            document.querySelector('input[name="module_category"]').value = 'module';
        } else {
            learningForm.style.display = 'none';
            announcementForm.style.display = 'block';
            document.querySelector('input[name="module_category"]').value = 'announcement';
        }
    };
    
    // Helper: Update announcement day
    window.updateAnnouncementDay = function() {
        const dateInput = document.getElementById('announcementDate');
        const dayDisplay = document.getElementById('announcementDay');
        
        if (dateInput.value) {
            const day = getDayOfWeek(dateInput.value);
            dayDisplay.textContent = day;
        } else {
            dayDisplay.textContent = 'Pilih tanggal terlebih dahulu';
        }
    };
    
    // Modal references
    const moduleModal = document.querySelector('[data-modal="module"]');
    const contentModal = document.querySelector('[data-modal="content"]');
    const quizModal = document.querySelector('[data-modal="quiz"]');
    
    // Open Module Modal
    window.openCreateModuleModal = function(url) {
        const modal = document.getElementById('moduleModal') || document.querySelector('[x-show="showModuleModal"]');
        const form = document.getElementById('moduleForm');
        
        if (url && form) {
            form.action = url;
            console.log('Form action set to:', url);
        }
        
        if (modal) {
            modal.style.display = 'flex';
        }
    };
    
    // Close Module Modal
    window.closeModuleModal = function() {
        const modal = document.getElementById('moduleModal') || document.querySelector('[x-show="showModuleModal"]');
        if (modal) {
            modal.style.display = 'none';
        }
    };
    
    // Open Content Modal
    window.openCreateMaterialModal = function(url) {
        const modal = document.getElementById('contentModal') || document.querySelector('[x-show="showContentModal"]');
        const form = document.getElementById('contentForm');
        
        if (url && form) {
            form.action = url;
            console.log('Content form action set to:', url);
        }
        
        if (modal) {
            modal.style.display = 'flex';
        }
    };
    
    // Close Content Modal
    window.closeContentModal = function() {
        const modal = document.getElementById('contentModal') || document.querySelector('[x-show="showContentModal"]');
        if (modal) {
            modal.style.display = 'none';
        }
    };
    
    // Open Quiz Modal
    window.openCreateQuizModal = function(url) {
        const modal = document.getElementById('quizModal') || document.querySelector('[x-show="showQuizModal"]');
        if (modal) {
            modal.style.display = 'flex';
        }
    };
    
    // Close Quiz Modal
    window.closeQuizModal = function() {
        const modal = document.getElementById('quizModal') || document.querySelector('[x-show="showQuizModal"]');
        if (modal) {
            modal.style.display = 'none';
        }
    };
    
    // Toggle Module Expand
    window.toggleModuleExpand = function(moduleId) {
        const moduleDiv = document.querySelector(`[data-id="${moduleId}"]`);
        if (moduleDiv) {
            const contentDiv = moduleDiv.querySelector('[x-collapse]');
            if (contentDiv) {
                if (contentDiv.style.display === 'none' || contentDiv.style.height === '0px') {
                    contentDiv.style.display = 'block';
                    contentDiv.style.height = 'auto';
                } else {
                    contentDiv.style.display = 'none';
                    contentDiv.style.height = '0px';
                }
            }
        }
    };
    
    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        const modal = e.target.closest('[x-show]');
        if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
            e.target.style.display = 'none';
        }
    });
    
    // Close modal with close button
    document.querySelectorAll('[x-show] .fa-times').forEach(button => {
        button.closest('button')?.addEventListener('click', function() {
            const modal = this.closest('[x-show]');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    });
    
    // TinyMCE Init
    if (document.getElementById('richEditor')) {
        tinymce.init({
            selector: '#richEditor',
            height: 400,
            menubar: false,
            plugins: 'image media link lists table code preview',
            toolbar: 'undo redo | fontfamily fontsize | formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image media | code | preview | table | removeformat | help | forecolor backcolor | fullscreen',
            file_picker_types: 'image',
        });
    }
    
    // Sortable Modules
    const moduleList = document.getElementById('modules-list');
    if (moduleList && typeof Sortable !== 'undefined') {
        Sortable.create(moduleList, {
            handle: '.handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function(evt) {
                const orderedIds = Array.from(moduleList.querySelectorAll('[data-id]')).map(el => el.getAttribute('data-id'));
                fetch(`/instructor/courses/${courseId}/modules/reorder`, {
                    method: "POST",
                    headers: { 
                        "Content-Type": "application/json", 
                        "X-CSRF-TOKEN": csrfToken 
                    },
                    body: JSON.stringify({ ordered_ids: orderedIds })
                });
            }
        });
    }
    
    // Sortable Contents
    document.querySelectorAll('.contents-list').forEach(function(el) {
        if (typeof Sortable !== 'undefined') {
            Sortable.create(el, {
                handle: '.handle-content',
                animation: 150,
                ghostClass: 'sortable-ghost',
                group: 'contents',
                onEnd: function(evt) {
                    const url = el.getAttribute('data-reorder-url');
                    const itemsData = [];
                    el.querySelectorAll('.content-item').forEach((item) => {
                        itemsData.push({
                            id: item.getAttribute('data-id'),
                            type: item.getAttribute('data-type')
                        });
                    });
                    fetch(url, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify({ items: itemsData })
                    });
                }
            });
        }
    });
    
    // Flash Messages Auto-hide
    const flashMessage = document.querySelector('.fixed.bottom-6');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.style.transition = 'opacity 0.5s ease-out';
            flashMessage.style.opacity = '0';
            setTimeout(() => {
                flashMessage.remove();
            }, 500);
        }, 4000);
    }
});
