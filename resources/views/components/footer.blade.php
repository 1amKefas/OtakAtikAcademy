<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Desain Responsif</title>
    <!-- Tailwind CSS CDN --><script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons CDN for social media icons --><script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            /* Latar belakang BODY tetap silver kebiruan (lebih gelap dari footer) */
            background-color: #eef4f7;
        }
        .footer-link {
            @apply text-gray-700 hover:text-blue-600 transition duration-150;
        }
        /* Custom class for the logos to blend in */
        .logo-blend {
            /* Remove default styling for the placeholder links */
            @apply !border-none !bg-transparent !p-0 flex-shrink-0; 
        }
        .logo-blend img {
            @apply h-auto object-contain; /* Adjust height and maintain aspect ratio */
        }
    </style>
</head>
<body>

    <!-- Main Content for context (optional, remove in final implementation) -->
    <!-- Blok konten utama sekarang mewarisi warna silver kebiruan dari BODY -->
    <div class="h-[60vh] flex items-center justify-center">
        <p class="text-xl text-gray-500">Konten Halaman Anda di sini</p>
    </div>

    <!-- START: Footer Component -->
    <!-- DIUBAH: Menggunakan warna KUSTOM Tailwind [bg-[#e8f0f5]] yang lebih terlihat. -->
    <footer class="bg-[#e8f0f5] pt-16 pb-8 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Link Columns Section --><div class="grid grid-cols-2 md:grid-cols-4 gap-10 md:gap-8">
                
                <!-- Column 1: OtakAtik Academy --><div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">OtakAtik Academy</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="footer-link">Tentang</a></li>
                        <li><a href="#" class="footer-link">Apa yang kita tawarkan</a></li>
                        <li><a href="#" class="footer-link">Kepemimpinan</a></li>
                        <li><a href="#" class="footer-link">Karier</a></li>
                        <li><a href="#" class="footer-link">Katalog</a></li>
                        <li><a href="#" class="footer-link">OtakAtik Plus</a></li>
                        <li><a href="#" class="footer-link">Sertifikat Profesional</a></li>
                        <li><a href="#" class="footer-link">Sertifikat MasterTrack®</a></li>
                        <li><a href="#" class="footer-link">Gelar</a></li>
                        <li><a href="#" class="footer-link">Untuk Perusahaan</a></li>
                        <li><a href="#" class="footer-link">Untuk Pemerintahan</a></li>
                        <li><a href="#" class="footer-link">Untuk Kampus</a></li>
                        <li><a href="#" class="footer-link">Menjadi Mitra</a></li>
                        <li><a href="#" class="footer-link">Dampak sosial</a></li>
                    </ul>
                </div>

                <!-- Column 2: Komunitas --><div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Komunitas</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="footer-link">Pembelajar</a></li>
                        <li><a href="#" class="footer-link">Mitra</a></li>
                        <li><a href="#" class="footer-link">Tester Beta</a></li>
                        <li><a href="#" class="footer-link">Blog</a></li>
                        <li><a href="#" class="footer-link">Podcast OtakAtik</a></li>
                        <li><a href="#" class="footer-link">Blog teknologi</a></li>
                    </ul>
                </div>

                <!-- Column 3: Lebih banyak --><div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Lebih banyak</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="footer-link">Tekan</a></li>
                        <li><a href="#" class="footer-link">Investor</a></li>
                        <li><a href="#" class="footer-link">Istilah</a></li>
                        <li><a href="#" class="footer-link">Privasi</a></li>
                        <li><a href="#" class="footer-link">Bantuan</a></li>
                        <li><a href="#" class="footer-link">Aksesibilitas</a></li>
                        <li><a href="#" class="footer-link">Kontak</a></li>
                        <li><a href="#" class="footer-link">Artikel</a></li>
                        <li><a href="#" class="footer-link">Direktori</a></li>
                        <li><a href="#" class="footer-link">Afiliasi</a></li>
                        <li><a href="#" class="footer-link">Pernyataan tentang Perbudakan Modern</a></li>
                        <li><a href="#" class="footer-link">Kelola preferensi cookie</a></li>
                    </ul>
                </div>

                <!-- Column 4: Sponsored By (Logos) --><div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sponsored By</h3>
                    <div class="flex flex-wrap items-center gap-4"> <!-- Use flex and gap for side-by-side --><!-- Logo PNJ --><a href="#" target="_blank" rel="noopener noreferrer" class="logo-blend w-auto h-12">
                            <img 
                                src="images/logo_PNJ.png" 
                                alt="Logo PNJ" 
                                class="h-full max-w-full"
                            >
                        </a>
                        
                        <!-- Logo TIK --><a href="#" target="_blank" rel="noopener noreferrer" class="logo-blend w-auto h-12">
                            <img 
                                src="images/logo_TIK.png" 
                                alt="Logo TIK" 
                                class="h-full max-w-full"
                            >
                        </a>

                    </div>
                </div>

            </div>
            
            <!-- Separator Line --><hr class="my-10 border-gray-200">

            <!-- Copyright and Social Media Section --><div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                
                <!-- Copyright --><p class="text-sm text-gray-500 order-2 md:order-1">
                    © 2025 Politeknik Negeri Jakarta Inc. Hak cipta dilindungi.
                </p>

                <!-- Social Media Icons --><div class="flex space-x-4 order-1 md:order-2">
                    <a href="#" class="text-gray-500 hover:text-gray-900 transition duration-150" aria-label="Facebook">
                        <i data-lucide="facebook" class="w-6 h-6"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-900 transition duration-150" aria-label="LinkedIn">
                        <i data-lucide="linkedin" class="w-6 h-6"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-900 transition duration-150" aria-label="Twitter">
                        <i data-lucide="twitter" class="w-6 h-6"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-900 transition duration-150" aria-label="Instagram">
                        <i data-lucide="instagram" class="w-6 h-6"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-900 transition duration-150" aria-label="Youtube">
                        <i data-lucide="youtube" class="w-6 h-6"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-900 transition duration-150" aria-label="Ganti Bahasa">
                        <i data-lucide="globe" class="w-6 h-6"></i>
                    </a>
                </div>
            </div>

        </div>
    </footer>
    <!-- END: Footer Component --><!-- Script to initialize Lucide icons --><script>
        // Initialize Lucide icons after the DOM is loaded
        lucide.createIcons();
    </script>
</body>
</html>