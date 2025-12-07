/**
 * Logic Pengerjaan Quiz OtakAtik Academy
 * Menangani timer, navigasi soal, dan autosave state.
 */
function quizApp(duration, total) {
    return {
        timeLeft: duration,
        formattedTime: '00:00:00',
        progress: 0,
        totalQuestions: total,
        answeredCount: 0,
        currentIndex: 0,
        showConfirmModal: false,
        answers: {}, // Object untuk track ID soal yg sudah dijawab

        init() {
            this.startTimer();
            // Peringatan saat mau close tab
            window.onbeforeunload = () => "Waktu berjalan. Yakin keluar?";
            
            // [SECURITY & BUG FIX] Scan jawaban yang sudah terisi saat reload halaman
            // Ini mencegah progress balik ke 0% kalau user refresh
            this.scanExistingAnswers();
        },

        scanExistingAnswers() {
            // Cek Radio & Checkbox
            document.querySelectorAll('input[type="radio"]:checked, input[type="checkbox"]:checked').forEach(el => {
                // Regex ambil angka ID dari name="answers[123]"
                let match = el.name.match(/\[(\d+)\]/);
                if(match) this.answers[match[1]] = true;
            });

            // Cek Textarea (Essay)
            document.querySelectorAll('textarea').forEach(el => {
                let match = el.name.match(/\[(\d+)\]/);
                if(match && el.value.trim().length > 0) this.answers[match[1]] = true;
            });

            this.recalcProgress();
        },

        startTimer() {
            const t = setInterval(() => {
                if (this.timeLeft <= 0) {
                    clearInterval(t);
                    this.finalSubmit(true); 
                } else {
                    this.timeLeft--;
                    this.formatTime();
                }
            }, 1000);
        },

        formatTime() {
            const h = Math.floor(this.timeLeft / 3600);
            const m = Math.floor((this.timeLeft % 3600) / 60);
            const s = this.timeLeft % 60;
            this.formattedTime = 
                (h < 10 ? "0"+h : h) + ":" + 
                (m < 10 ? "0"+m : m) + ":" + 
                (s < 10 ? "0"+s : s);
        },

        markAnswered(questionId) {
            this.answers[questionId] = true;
            this.recalcProgress();
        },
        
        checkTextarea(questionId, el) {
            if (el.value.trim().length > 0) {
                this.answers[questionId] = true;
            } else {
                delete this.answers[questionId];
            }
            this.recalcProgress();
        },

        checkCheckbox(questionId) {
            const name = `answers[${questionId}][]`;
            const checked = document.querySelectorAll(`input[name="${name}"]:checked`).length > 0;
            
            if (checked) {
                this.answers[questionId] = true;
            } else {
                delete this.answers[questionId];
            }
            this.recalcProgress();
        },

        recalcProgress() {
            this.answeredCount = Object.keys(this.answers).length;
            this.progress = Math.round((this.answeredCount / this.totalQuestions) * 100);
        },

        isAnswered(questionId) {
            return !!this.answers[questionId];
        },

        scrollToTop() {
            const container = document.getElementById('questionContainer');
            if(container) container.scrollTop = 0;
        },

        next() {
            if (this.currentIndex < this.totalQuestions - 1) {
                this.currentIndex++;
                this.scrollToTop();
            }
        },

        prev() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
                this.scrollToTop();
            }
        },

        confirmSubmit() {
            if (this.answeredCount < this.totalQuestions) {
                alert("Masih ada soal yang belum dijawab!");
                return;
            }
            this.showConfirmModal = true;
        },

        finalSubmit(force = false) {
            window.onbeforeunload = null;
            document.getElementById('quizForm').submit();
        }
    }
}