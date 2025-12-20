// Tunggu Alpine tersedia, register data component
function registerAlpineData() {
    if (typeof Alpine === 'undefined') {
        // Alpine belum load, coba lagi
        setTimeout(registerAlpineData, 100);
        return;
    }

    Alpine.data('contentManager', () => ({
        // Modal States
        showModuleModal: false,
        showContentModal: false,
        showQuizModal: false,
        
        // Module open/close tracking
        expandedModules: {},
        
        // Edit Mode Flags
        moduleEditMode: false,
        contentEditMode: false,
        quizEditMode: false,

        // Data Models
        moduleTitle: '',
        moduleFormAction: '',
        moduleCategory: 'module',
        moduleFormLoading: false,
        moduleFormError: '',
        
        // Announcement fields
        announcementTitle: '',
        announcementDate: '',
        announcementTime: '',
        announcementDayOfWeek: '',
        announcementDesc: '',
        
        contentTitle: '',
        contentUrl: '',
        contentFormAction: '',
        
        quizTitle: '',
        quizDesc: '',
        quizDuration: 30,
        quizScore: 70,
        quizFormAction: '',
        
        // Helper Functions
        getDayOfWeek(dateString) {
            const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const date = new Date(dateString);
            return hari[date.getDay()];
        },

        updateDayOfWeek() {
            if (this.announcementDate) {
                this.announcementDayOfWeek = this.getDayOfWeek(this.announcementDate);
            }
        },

        validateModuleForm() {
            this.moduleFormError = '';

            if (this.moduleCategory === 'module') {
                if (!this.moduleTitle.trim()) {
                    this.moduleFormError = 'Judul modul harus diisi';
                    return false;
                }
            } else if (this.moduleCategory === 'announcement') {
                if (!this.announcementTitle.trim()) {
                    this.moduleFormError = 'Judul pemberitahuan harus diisi';
                    return false;
                }
                if (!this.announcementDate) {
                    this.moduleFormError = 'Tanggal pemberitahuan harus diisi';
                    return false;
                }
                if (!this.announcementTime) {
                    this.moduleFormError = 'Waktu pemberitahuan harus diisi';
                    return false;
                }
            }

            return true;
        },

        handleModuleSubmit() {
            if (!this.validateModuleForm()) {
                return;
            }

            this.moduleFormLoading = true;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const courseId = this.getCourseId();

            const formData = new FormData();
            formData.append('_token', csrfToken);

            if (this.moduleCategory === 'module') {
                formData.append('module_category', 'module');
                formData.append('title', this.moduleTitle);
            } else {
                formData.append('module_category', 'announcement');
                formData.append('announcement_title', this.announcementTitle);
                formData.append('announcement_date', this.announcementDate);
                formData.append('announcement_time', this.announcementTime);
                formData.append('announcement_description', this.announcementDesc);
            }

            fetch(`/instructor/courses/${courseId}/modules`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw err;
                    });
                }
                return response.text();
            })
            .then(data => {
                this.moduleFormLoading = false;
                this.showModuleModal = false;
                window.location.reload();
            })
            .catch(error => {
                this.moduleFormLoading = false;
                console.error('Error:', error);
                
                if (error.errors) {
                    const firstError = Object.values(error.errors)[0];
                    this.moduleFormError = Array.isArray(firstError) ? firstError[0] : firstError;
                } else if (error.message) {
                    this.moduleFormError = error.message;
                } else {
                    this.moduleFormError = 'Terjadi kesalahan. Silakan coba lagi.';
                }
            });
        },

        // Module Functions
        openCreateModuleModal(url) {
            this.moduleEditMode = false;
            this.moduleTitle = '';
            this.moduleCategory = 'module';
            this.announcementTitle = '';
            this.announcementDate = '';
            this.announcementTime = '';
            this.announcementDayOfWeek = '';
            this.announcementDesc = '';
            this.moduleFormError = '';
            this.moduleFormAction = url;
            this.showModuleModal = true;
        },

        openUpdateModuleModal(id, title) {
            this.moduleEditMode = true;
            this.moduleCategory = 'module';
            this.moduleTitle = title;
            this.moduleFormAction = `/instructor/modules/${id}`;
            this.moduleFormError = '';
            this.showModuleModal = true;
        },

        // Content (Material) Functions
        openCreateMaterialModal(url) {
            this.contentEditMode = false;
            this.contentTitle = '';
            this.contentUrl = '';
            this.contentFormAction = url;
            
            if(tinymce.get('richEditor')) tinymce.get('richEditor').setContent('');
            this.showContentModal = true;
        },
        
        openEditMaterialModal(id, title, url, el) {
            this.contentEditMode = true;
            this.contentTitle = title;
            this.contentUrl = url || '';
            this.contentFormAction = `/instructor/materials/${id}`;
            
            let encodedContent = el.dataset.content;
            if(tinymce.get('richEditor') && encodedContent) {
                tinymce.get('richEditor').setContent(atob(encodedContent));
            }
            this.showContentModal = true;
        },

        // Quiz Functions
        openCreateQuizModal(url) {
            this.quizEditMode = false;
            this.quizTitle = '';
            this.quizDesc = '';
            this.quizDuration = 30;
            this.quizScore = 70;
            this.quizFormAction = url;
            this.showQuizModal = true;
        },

        openEditQuizModal(id, title, desc, dur, score) {
            this.quizEditMode = true;
            this.quizTitle = title;
            this.quizDesc = desc;
            this.quizDuration = dur;
            this.quizScore = score;
            this.quizFormAction = `/instructor/courses/${this.getCourseId()}/quiz/${id}`;
            this.showQuizModal = true;
        },

        toggleModuleExpand(moduleId) {
            if (this.expandedModules[moduleId] === undefined) {
                this.expandedModules[moduleId] = false;
            } else {
                this.expandedModules[moduleId] = !this.expandedModules[moduleId];
            }
        },

        isModuleExpanded(moduleId) {
            // Default adalah true (expanded) kecuali sudah di-toggle
            return this.expandedModules[moduleId] !== false;
        },

        getCourseId() {
            return document.querySelector('meta[name="course-id"]').content;
        }
    }));

    // Initialize SortableJS, TinyMCE, & Flash Messages
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const courseId = document.querySelector('meta[name="course-id"]').content;
    
    // Flash Message Handler
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

    // TinyMCE Init
    if(document.getElementById('richEditor')) {
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
    var moduleList = document.getElementById('modules-list');
    if(moduleList) {
        Sortable.create(moduleList, {
            handle: '.handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function (evt) {
                var orderedIds = this.toArray();
                fetch(`/instructor/courses/${courseId}/modules/reorder`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrfToken },
                    body: JSON.stringify({ ordered_ids: orderedIds })
                });
            }
        });
    }

    // Sortable Contents
    document.querySelectorAll('.contents-list').forEach(function(el) {
        Sortable.create(el, {
            handle: '.handle-content',
            animation: 150,
            ghostClass: 'sortable-ghost',
            group: 'contents', 
            onEnd: function (evt) {
                var url = el.getAttribute('data-reorder-url');
                var itemsData = [];
                el.querySelectorAll('.content-item').forEach((item, index) => {
                    itemsData.push({ id: item.getAttribute('data-id'), type: item.getAttribute('data-type') });
                });
                fetch(url, {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrfToken },
                    body: JSON.stringify({ items: itemsData })
                });
            }
        });
    });
}

// Panggil Alpine registration
registerAlpineData();
