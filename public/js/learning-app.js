// public/js/learning-app.js

// --- 1. Theme & Config Initialization ---
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
    // [FIX] Pastikan boolean check benar
    const isAlreadyDone = contentData.dataset.alreadyDone === '1'; 
    const isVideo = contentData.dataset.isVideo === '1'; 
    const isVideoContent = contentData.dataset.isVideoContent === '1';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let isCompleted = isAlreadyDone; // Init status dari server

    // Init Progress Circle
    let circumference = 0;
    if(progressCircle) {
        const radius = progressCircle.r.baseVal.value;
        circumference = radius * 2 * Math.PI;
        progressCircle.style.strokeDasharray = `${circumference} ${circumference}`;
        
        // [FIX] Jika sudah selesai, langsung set circle penuh (0 offset), jika belum set kosong
        if(isAlreadyDone) {
            progressCircle.style.strokeDashoffset = 0;
        } else {
            progressCircle.style.strokeDashoffset = circumference;
        }
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
                // Update local state biar gak kirim request berkali-kali
                contentData.dataset.alreadyDone = '1';
            });
        }
    }

    // Fungsi Buka Kunci Tombol Next & Update UI Header
    function unlockNextButton() {
        isCompleted = true; // Flag JS update
        
        // 1. Update Tombol
        if(btnNext) {
            btnNext.disabled = false;
            btnNext.classList.remove('bg-gray-300', 'dark:bg-slate-700', 'text-gray-500', 'dark:text-gray-400', 'cursor-not-allowed', 'shadow-none');
            btnNext.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-blue-700', 'text-white', 'hover:shadow-lg', 'hover:shadow-blue-500/30', 'transform', 'hover:-translate-y-0.5');
            btnNext.innerHTML = `<span>Selesai & Lanjut</span> <i class="fas fa-check-circle animate-pulse"></i>`;
        }
        
        // 2. Update Circle Header
        if(progressCircle && progressIcon) {
            progressCircle.style.strokeDashoffset = 0; // Penuh
            progressIcon.innerHTML = '<i class="fas fa-check text-green-500 text-xl"></i>';
        }
        
        // 3. Trigger Backend
        markAsComplete();
    }

    // [FIX] Priority Check: Kalau dari server dibilang sudah selesai, jalankan unlock SEKARANG.
    if (isAlreadyDone) {
        unlockNextButton();
    }

    // A. LOGIC VIDEO TRACKING (Untuk Native Video Upload)
    if (isVideo && videoPlayer && !isAlreadyDone) {
        console.log("Tracking Video Progress...");
        
        let supposedCurrentTime = 0;
        videoPlayer.addEventListener('timeupdate', function() {
            if (!videoPlayer.seeking) {
                supposedCurrentTime = videoPlayer.currentTime;
            }
        });
        
        // Unlock saat video selesai
        videoPlayer.addEventListener('ended', function() {
            console.log("Video Finished");
            unlockNextButton();
        });
    }

    // B. LOGIC SCROLL TRACKING (Untuk Teks/Artikel)
    if(scrollContainer && progressCircle && !isVideoContent) {
        scrollContainer.addEventListener('scroll', () => {
            // [FIX] KUNCI UTAMA: Kalau sudah selesai (dari server atau barusan), JANGAN hitung scroll lagi.
            // Biarkan UI tetap 100% (Centang Hijau)
            if (isCompleted || isAlreadyDone) return; 

            const scrollTop = scrollContainer.scrollTop;
            const scrollHeight = scrollContainer.scrollHeight - scrollContainer.clientHeight;
            
            let percent = scrollHeight > 0 ? (scrollTop / scrollHeight) : 1;
            if (percent > 1) percent = 1;

            const offset = circumference - (percent * circumference);
            progressCircle.style.strokeDashoffset = offset;
            progressIcon.innerText = Math.round(percent * 100) + '%';

            // Trigger selesai jika sudah di bawah (toleransi 50px)
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

    // Fallback: Youtube Embed Scroll
    if(isVideoContent && !isVideo && !isAlreadyDone) {
        if(scrollContainer) {
             scrollContainer.addEventListener('scroll', () => {
                if (isCompleted || isAlreadyDone) return; // Fix scroll fallback juga

                const scrollTop = scrollContainer.scrollTop;
                const scrollHeight = scrollContainer.scrollHeight - scrollContainer.clientHeight;
                if (scrollHeight - scrollTop <= 20) {
                    unlockNextButton();
                }
             });
        }
    }
});