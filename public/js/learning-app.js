// public/js/learning-app.js

// --- 1. Theme & Config Initialization (Runs immediately) ---
(function() {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
})();

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

// --- 3. DOM Logic (Scroll Progress & Video Tracking) ---
document.addEventListener('DOMContentLoaded', () => {
    const scrollContainer = document.getElementById('mainScrollContainer');
    const btnNext = document.getElementById('btnNext');
    const progressCircle = document.getElementById('progressCircle');
    const progressIcon = document.getElementById('progressIcon');
    const contentData = document.getElementById('content-data');
    const videoPlayer = document.getElementById('courseVideo');

    if (!contentData) return;

    const nextUrl = contentData.dataset.nextUrl;
    const completeUrl = contentData.dataset.completeUrl;
    const isAlreadyDone = contentData.dataset.alreadyDone === '1';
    const isVideo = contentData.dataset.isVideo === '1'; // Native Video Upload
    const isVideoContent = contentData.dataset.isVideoContent === '1'; // Any Video (Upload/Embed)
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let isCompleted = isAlreadyDone;

    // Init Progress Circle (Jika ada)
    let circumference = 0;
    if(progressCircle) {
        const radius = progressCircle.r.baseVal.value;
        circumference = radius * 2 * Math.PI;
        progressCircle.style.strokeDasharray = `${circumference} ${circumference}`;
        progressCircle.style.strokeDashoffset = circumference;
    }

    // Fungsi Mark Complete via AJAX
    function markAsComplete() {
        if(!isAlreadyDone) {
            fetch(completeUrl, { 
                method: 'POST', 
                headers: { 
                    'X-CSRF-TOKEN': csrfToken, 
                    'Content-Type': 'application/json' 
                }, 
                body: JSON.stringify({}) 
            }).then(() => {
                console.log("Material Marked as Completed");
                contentData.dataset.alreadyDone = '1';
            });
        }
    }

    // Fungsi Buka Kunci Tombol Next
    function unlockNextButton() {
        isCompleted = true;
        if(btnNext) {
            btnNext.disabled = false;
            btnNext.classList.remove('bg-gray-300', 'dark:bg-slate-700', 'text-gray-500', 'dark:text-gray-400', 'cursor-not-allowed', 'shadow-none');
            btnNext.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-blue-700', 'text-white', 'hover:shadow-lg', 'hover:shadow-blue-500/30', 'transform', 'hover:-translate-y-0.5');
            btnNext.innerHTML = `<span>Selesai & Lanjut</span> <i class="fas fa-check-circle animate-pulse"></i>`;
        }
        if(progressCircle) {
            progressCircle.style.strokeDashoffset = 0;
            progressIcon.innerHTML = '<i class="fas fa-check text-green-500 text-xl"></i>';
        }
        
        // Auto mark complete saat unlock
        markAsComplete();
    }

    // A. LOGIC VIDEO TRACKING (Untuk Native Video Upload)
    if (isVideo && videoPlayer && !isAlreadyDone) {
        console.log("Tracking Video Progress...");
        
        // Prevent Seeking (Opsional - Matikan kalau mau strict banget)
        let supposedCurrentTime = 0;
        videoPlayer.addEventListener('timeupdate', function() {
            if (!videoPlayer.seeking) {
                supposedCurrentTime = videoPlayer.currentTime;
            }
        });
        
        // Kalau user coba skip manual, balikin ke posisi terakhir (Opsional)
        // videoPlayer.addEventListener('seeking', function() {
        //     var delta = videoPlayer.currentTime - supposedCurrentTime;
        //     if (Math.abs(delta) > 0.01) {
        //         videoPlayer.currentTime = supposedCurrentTime; 
        //     }
        // });

        // Unlock saat video selesai
        videoPlayer.addEventListener('ended', function() {
            console.log("Video Finished");
            unlockNextButton();
        });
    }

    // B. LOGIC SCROLL TRACKING (Untuk Teks/Artikel)
    if(scrollContainer && progressCircle && !isVideoContent && !isAlreadyDone) {
        scrollContainer.addEventListener('scroll', () => {
            if (isCompleted) return;

            const scrollTop = scrollContainer.scrollTop;
            const scrollHeight = scrollContainer.scrollHeight - scrollContainer.clientHeight;
            
            let percent = scrollHeight > 0 ? (scrollTop / scrollHeight) : 1;
            if (percent > 1) percent = 1;

            const offset = circumference - (percent * circumference);
            progressCircle.style.strokeDashoffset = offset;
            progressIcon.innerText = Math.round(percent * 100) + '%';

            if (scrollHeight - scrollTop <= 50) {
                unlockNextButton();
            }
        });
    }

    // C. Handle Tombol Next Click
    if(btnNext) {
        btnNext.addEventListener('click', () => { 
            if(isCompleted || isAlreadyDone) {
                window.location.href = nextUrl; 
            }
        });
    }

    // Fallback: Jika konten Youtube Embed (Tidak bisa track 'ended' mudah via iframe)
    // Kita unlock otomatis atau biarkan user klik manual (tergantung kebijakan)
    // Di sini kita unlock manual jika user scroll mentok khusus untuk youtube embed
    if(isVideoContent && !isVideo && !isAlreadyDone) {
        // Bisa pakai timer atau scroll mentok sebagai pengganti tracking API Youtube
        if(scrollContainer) {
             scrollContainer.addEventListener('scroll', () => {
                const scrollTop = scrollContainer.scrollTop;
                const scrollHeight = scrollContainer.scrollHeight - scrollContainer.clientHeight;
                if (scrollHeight - scrollTop <= 20) { // Kalau user scroll sampai bawah iframe
                    unlockNextButton();
                }
             });
        }
    }
});