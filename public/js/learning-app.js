// --- 1. Theme & Config Initialization (Runs immediately) ---

// Theme Initialization (Dark Mode Support)
(function() {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
})();

// Tailwind Configuration
if (typeof tailwind !== 'undefined') {
    tailwind.config = {
        darkMode: 'class',
        theme: { 
            extend: {
                fontFamily: { sans: ['Inter', 'sans-serif'] },
                colors: { slate: { 850: '#1e293b', 900: '#0f172a' } }
            } 
        }
    };
}

// --- 2. Alpine.js Components ---

document.addEventListener('alpine:init', () => {
    Alpine.data('layout', () => ({
        sidebarOpen: false,
        theme: localStorage.getItem('theme') || 'system',
        
        init() {
            this.setTheme(this.theme);
            this.$watch('theme', val => this.setTheme(val));
        },

        setTheme(val) {
            this.theme = val;
            localStorage.setItem('theme', val);
            if (val === 'dark' || (val === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }));
});

// --- 3. DOM Logic (Scroll Progress & Completion) ---

document.addEventListener('DOMContentLoaded', () => {
    const scrollContainer = document.getElementById('mainScrollContainer');
    const btnNext = document.getElementById('btnNext');
    const progressCircle = document.getElementById('progressCircle');
    const progressIcon = document.getElementById('progressIcon');
    const contentData = document.getElementById('content-data'); // Element rahasia buat nyimpen data PHP

    if (!contentData) return; // Kalau halaman quiz, mungkin ga ada element ini

    const nextUrl = contentData.dataset.nextUrl;
    const completeUrl = contentData.dataset.completeUrl;
    const isAlreadyDone = contentData.dataset.alreadyDone === '1';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let isCompleted = false;

    // Init Progress Circle
    let circumference = 0;
    if(progressCircle) {
        const radius = progressCircle.r.baseVal.value;
        circumference = radius * 2 * Math.PI;
        progressCircle.style.strokeDasharray = `${circumference} ${circumference}`;
        progressCircle.style.strokeDashoffset = circumference;
    }

    // Fungsi Buka Kunci Tombol Next
    function unlockNextButton() {
        isCompleted = true;
        if(btnNext) {
            btnNext.disabled = false;
            // Ganti style tombol jadi aktif
            btnNext.classList.remove('bg-gray-300', 'dark:bg-slate-700', 'text-gray-500', 'dark:text-gray-400', 'cursor-not-allowed', 'shadow-none');
            btnNext.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-blue-700', 'text-white', 'hover:shadow-lg', 'hover:shadow-blue-500/30', 'transform', 'hover:-translate-y-0.5');
            btnNext.innerHTML = `<span>Selesai & Lanjut</span> <i class="fas fa-check-circle animate-pulse"></i>`;
        }
        if(progressCircle) {
            progressCircle.style.strokeDashoffset = 0;
            progressIcon.innerHTML = '<i class="fas fa-check text-green-500 text-xl"></i>';
        }
    }

    // Kalau sudah pernah selesai, langsung unlock
    if (isAlreadyDone) unlockNextButton();

    // Event Scroll Listener
    if(scrollContainer && progressCircle) {
        scrollContainer.addEventListener('scroll', () => {
            if (isCompleted) return;

            const scrollTop = scrollContainer.scrollTop;
            const scrollHeight = scrollContainer.scrollHeight - scrollContainer.clientHeight;
            
            // Hitung persentase scroll
            let percent = scrollHeight > 0 ? (scrollTop / scrollHeight) : 1;
            if (percent > 1) percent = 1;

            // Update Circle
            const offset = circumference - (percent * circumference);
            progressCircle.style.strokeDashoffset = offset;
            progressIcon.innerText = Math.round(percent * 100) + '%';

            // Jika sudah di bawah (toleransi 50px), tandai selesai
            if (scrollHeight - scrollTop <= 50) {
                unlockNextButton();
                // Kirim request ke server bahwa materi selesai
                fetch(completeUrl, { 
                    method: 'POST', 
                    headers: { 
                        'X-CSRF-TOKEN': csrfToken, 
                        'Content-Type': 'application/json' 
                    }, 
                    body: JSON.stringify({}) 
                });
            }
        });
    }

    // Handle Klik Tombol Next
    if(btnNext) {
        btnNext.addEventListener('click', () => { 
            if(isCompleted) window.location.href = nextUrl; 
        });
    }
});