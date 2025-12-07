document.addEventListener('alpine:init', () => {
    Alpine.data('contentManager', () => ({
        // Modal States
        showModuleModal: false,
        showContentModal: false,
        showQuizModal: false,
        
        // Edit Mode Flags
        moduleEditMode: false,
        contentEditMode: false,
        quizEditMode: false,

        // Data Models
        moduleTitle: '',
        moduleFormAction: '',
        
        contentTitle: '',
        contentUrl: '',
        contentFormAction: '',
        
        quizTitle: '',
        quizDesc: '',
        quizDuration: 30,
        quizScore: 70,
        quizFormAction: '',
        
        // -- Module Functions --
        openCreateModuleModal(url) {
            this.moduleEditMode = false;
            this.moduleTitle = '';
            this.moduleFormAction = url;
            this.showModuleModal = true;
        },
        openUpdateModuleModal(id, title) {
            this.moduleEditMode = true;
            this.moduleTitle = title;
            this.moduleFormAction = `/instructor/modules/${id}`;
            this.showModuleModal = true;
        },

        // -- Content (Material) Functions --
        openCreateMaterialModal(url) {
            this.contentEditMode = false;
            this.contentTitle = '';
            this.contentUrl = '';
            this.contentFormAction = url;
            
            // Reset TinyMCE
            if(tinymce.get('richEditor')) tinymce.get('richEditor').setContent('');
            this.showContentModal = true;
        },
        
        // [SECURE] Content diambil dari data-attribute element, bukan argumen inline script
        openEditMaterialModal(id, title, url, el) {
            this.contentEditMode = true;
            this.contentTitle = title;
            this.contentUrl = url || '';
            this.contentFormAction = `/instructor/materials/${id}`;
            
            // Ambil content terenkripsi dari data-content tombol
            let encodedContent = el.dataset.content;
            
            // Decode & Set Content
            if(tinymce.get('richEditor') && encodedContent) {
                tinymce.get('richEditor').setContent(atob(encodedContent));
            }
            this.showContentModal = true;
        },

        // -- Quiz Functions --
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

        // Helper untuk ambil Course ID dari Meta Tag
        getCourseId() {
            return document.querySelector('meta[name="course-id"]').content;
        }
    }))
});

// SortableJS & TinyMCE Initialization
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const courseId = document.querySelector('meta[name="course-id"]').content;
    
    // TinyMCE Init
    if(document.getElementById('richEditor')) {
        tinymce.init({
            selector: '#richEditor',
            height: 400,
            menubar: false,
            plugins: 'image media link lists table code preview',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image media | code',
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
});