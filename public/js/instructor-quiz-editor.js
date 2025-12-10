// public/js/instructor-quiz-editor.js

document.addEventListener('alpine:init', () => {
    Alpine.data('quizEditor', (initialData) => ({
        // State Halaman
        mode: 'add',
        currentQuestionId: null,
        formAction: initialData.addUrl,
        
        // State Quiz Global
        maxScore: parseInt(initialData.maxScore) || 100,
        currentTotalScore: 0, // Total dari database (sebelum edit)
        questionsList: initialData.questions || [],

        // State Form Input
        questionType: 'multiple_choice',
        points: 10,
        oldPoints: 0, // Untuk tracking perubahan saat edit
        order: 1,
        options: ['', '', '', ''],
        correctAnswer: 0,
        correctAnswersArray: [],
        essayExplanation: '',

        // -- Lifecycle --
        init() {
            this.recalculateTotal();
            this.resetForm();
            
            // Listener External (TinyMCE Sync)
            // Kita bind manual karena TinyMCE diluar scope Alpine
        },

        // -- Logic Kalkulasi Nilai Real-time --
        
        // Menghitung Total Score Estimasi (Current DB + Form Input Change)
        get projectedTotalScore() {
            let baseScore = this.currentTotalScore;
            
            if (this.mode === 'edit') {
                // Kalau lagi edit, kurangi nilai lama, tambah nilai input baru
                return (baseScore - this.oldPoints) + parseInt(this.points || 0);
            } else {
                // Kalau tambah baru, langsung tambahkan nilai input
                return baseScore + parseInt(this.points || 0);
            }
        },

        get scoreStatus() {
            const total = this.projectedTotalScore;
            const max = this.maxScore;

            if (total < max) return { 
                color: 'bg-yellow-500', 
                text: 'text-yellow-600', 
                msg: `⚠️ Total nilai ${total}. Kurang ${max - total} poin lagi untuk mencapai target.` 
            };
            if (total === max) return { 
                color: 'bg-green-500', 
                text: 'text-green-600', 
                msg: '✅ Sempurna! Total nilai pas sesuai target.' 
            };
            return { 
                color: 'bg-red-500', 
                text: 'text-red-600', 
                msg: `⛔ Berlebih! Total nilai ${total}. Kelebihan ${total - max} poin.` 
            };
        },

        recalculateTotal() {
            // Hitung total dari list questions yang di-pass dari server
            this.currentTotalScore = this.questionsList.reduce((sum, q) => sum + parseInt(q.points), 0);
            // Update order default untuk soal baru
            if (this.mode === 'add') {
                this.order = this.questionsList.length + 1;
            }
        },

        // -- Form Handling Functions --

        openAddMode() {
            this.resetForm();
            this.scrollToForm();
        },

        loadQuestion(q) {
            this.mode = 'edit';
            this.currentQuestionId = q.id;
            this.formAction = initialData.updateUrlBase + '/' + q.id;
            
            // Set Form Data
            this.questionType = q.question_type;
            this.points = parseInt(q.points);
            this.oldPoints = parseInt(q.points); // Simpan nilai lama buat kalkulasi
            this.order = q.order;
            
            // TinyMCE Set Content
            if(tinymce.get('questionEditor')) {
                tinymce.get('questionEditor').setContent(q.question);
            }

            // Parse Options
            this.handleOptionsParsing(q);

            // UI Fixes
            this.changeType(this.questionType);
            this.scrollToForm();
        },

        resetForm() {
            this.mode = 'add';
            this.currentQuestionId = null;
            this.formAction = initialData.addUrl;
            
            this.questionType = 'multiple_choice';
            this.points = 10;
            this.oldPoints = 0;
            this.order = this.questionsList.length + 1;
            this.options = ['', '', '', ''];
            this.correctAnswer = 0;
            this.correctAnswersArray = [];
            this.essayExplanation = '';

            if(tinymce.get('questionEditor')) {
                tinymce.get('questionEditor').setContent('');
            }
        },

        // -- Helpers --

        handleOptionsParsing(q) {
            if (['multiple_choice', 'multiple_select'].includes(q.question_type)) {
                let opts = q.options;
                if (typeof opts === 'string') {
                    try { opts = JSON.parse(opts); } catch(e) { opts = []; }
                }
                this.options = Array.isArray(opts) ? opts : [];
                
                if (q.question_type === 'multiple_choice') {
                    this.correctAnswer = parseInt(q.correct_answer);
                } else {
                    let ans = q.correct_answer;
                    if (typeof ans === 'string') {
                        try { ans = JSON.parse(ans); } catch(e) { ans = []; }
                    }
                    this.correctAnswersArray = Array.isArray(ans) ? ans.map(Number) : [];
                }
            } else if (q.question_type === 'true_false') {
                this.correctAnswer = q.correct_answer;
            } else if (q.question_type === 'essay') {
                this.essayExplanation = q.correct_answer;
            }
        },

        changeType(newType) {
            if(newType === 'multiple_choice') this.correctAnswer = 0;
            if(newType === 'multiple_select') this.correctAnswersArray = [];
            if(newType === 'true_false') this.correctAnswer = 'true';
        },

        addOption() {
            this.options.push('');
        },

        removeOption(index) {
            if (this.options.length <= 2) {
                alert('Minimal harus ada 2 pilihan!');
                return;
            }
            this.options.splice(index, 1);
        },

        scrollToForm() {
            // Mobile UX
            if(window.innerWidth < 1024) {
                this.$refs.quizFormContainer.scrollIntoView({ behavior: 'smooth' });
            } else {
                 window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    }));
});

// TinyMCE Init (Global)
document.addEventListener('DOMContentLoaded', () => {
    if(typeof tinymce !== 'undefined') {
        tinymce.remove();
        tinymce.init({
            selector: '#questionEditor',
            height: 250,
            menubar: false,
            plugins: 'image media link lists table code preview',
            toolbar: 'undo redo | fontfamily fontsize | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image media | code preview',
            content_style: "@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap'); body { font-family: 'Inter', sans-serif; font-size: 14px; }",
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
            }
        });
    }
});