<?php 
include 'database/connect.php';

//ambil data total kasir dari database
$result_karyawan = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tb_user WHERE role = 'kasir'");
$karyawan = mysqli_fetch_assoc($result_karyawan)['total'];

//ambil data total outlet dari database
$result_outlet = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tb_outlet");
$outlet = mysqli_fetch_assoc($result_outlet)['total'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>Nyuci Kilat</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#1a5de8',
            accent: '#cbb9ff',
            background: '#c9ecff',
            secondary: '#b7d1db',
            danger: '#e52a0a',
          },
          transitionProperty: {
            'banner': 'transform, opacity',
          },
          transitionTimingFunction: {
            'banner': 'cubic-bezier(0.4, 0, 0.2, 1)',
          }
        }
      }
    }
  </script>
  <style>
      html {
      scroll-behavior: smooth;
    }
    /* Custom CSS for smooth banner transitions */
    .banner-slide {
      transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
  
    /* Responsive image container */
    .responsive-banner {
      width: 100%;
      height: 0;
      padding-bottom: 30%; /* Adjust this percentage based on your desired aspect ratio */
      position: relative;
      overflow: hidden;
    }
    
    .responsive-banner img {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  </style>
</head>
<body class="bg-background font-sans">
  <!-- Sticky Header -->
  <header id="navbar" class="flex items-center justify-between px-6 py-4 bg-white sticky-nav">
    <img alt="logo" class="w-12 h-12" height="50" src="assets/img/logo.png" width="50"/>
    <nav class="flex space-x-6 text-base font-medium">
      <a class="text-black hover:underline" href="index.php">Home</a>
      <a class="text-black hover:underline" href="menu_invoice.php">Cek Status Laundry</a>
    </nav>
    <button class="bg-primary text-white px-5 py-2 rounded-md text-base font-medium hover:bg-blue-700 transition-colors" type="button">
      <a href="login.php">Login</a>
    </button>
  </header>
  
  <!-- Add padding to account for fixed navbar -->
  <div class="pt-20"></div>
  
  <!-- Banner Slider Section -->
  <section class="max-w-[1020px] mx-auto mt-6 relative px-4">
    <div class="relative w-full responsive-banner overflow-hidden rounded-xl shadow-xl">
      <!-- Banner 1 -->
      <div class="banner-slide absolute w-full h-full transition-[banner] duration-800 opacity-0 translate-x-full rounded-xl overflow-hidden active">
        <img src="assets/img/banner 1.png" alt="Promo 1" class="w-full h-full object-cover rounded-xl">
      </div>
      
      <!-- Banner 2 -->
      <div class="banner-slide absolute w-full h-full transition-[banner] duration-800 opacity-0 translate-x-full rounded-xl overflow-hidden next">
        <img src="assets/img/banner 2.jpg" alt="Promo 2" class="w-full h-full object-cover rounded-xl">
      </div>
      
      <!-- Banner 3 -->
      <div class="banner-slide absolute w-full h-full transition-[banner] duration-800 opacity-0 translate-x-full rounded-xl overflow-hidden">
        <img src="assets/img/banner 3.png" alt="Promo 3" class="w-full h-full object-cover rounded-xl">
      </div>
      
      <!-- Banner 4 -->
      <div class="banner-slide absolute w-full h-full transition-[banner] duration-800 opacity-0 translate-x-full rounded-xl overflow-hidden">
        <img src="assets/img/banner 4.jpg" alt="Promo 4" class="w-full h-full object-cover rounded-xl">
      </div>
      
      <!-- Banner 5 -->
      <div class="banner-slide absolute w-full h-full transition-[banner] duration-800 opacity-0 translate-x-full rounded-xl overflow-hidden">
        <img src="assets/img/banner 5.jpg" alt="Promo 5" class="w-full h-full object-cover rounded-xl">
      </div>
      
      <!-- Banner 6 -->
      <div class="banner-slide absolute w-full h-full transition-[banner] duration-800 opacity-0 translate-x-full rounded-xl overflow-hidden">
        <img src="assets/img/banner 6.jpg" alt="Promo 6" class="w-full h-full object-cover rounded-xl">
      </div>
    </div>
    
    <!-- Dot Indicators -->
    <div class="flex justify-center gap-2 mt-4">
      <button class="dot w-3 h-3 rounded-full bg-white border border-gray-400" data-index="0"></button>
      <button class="dot w-3 h-3 rounded-full bg-gray-300 border border-gray-400" data-index="1"></button>
      <button class="dot w-3 h-3 rounded-full bg-gray-300 border border-gray-400" data-index="2"></button>
      <button class="dot w-3 h-3 rounded-full bg-gray-300 border border-gray-400" data-index="3"></button>
      <button class="dot w-3 h-3 rounded-full bg-gray-300 border border-gray-400" data-index="4"></button>
      <button class="dot w-3 h-3 rounded-full bg-gray-300 border border-gray-400" data-index="5"></button>
    </div>
    
    <!-- Navigation Arrows -->
    <button class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/30 text-white p-2 rounded-full z-10 hover:bg-black/40 transition-colors" id="prevBtn">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
    </button>
    <button class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/30 text-white p-2 rounded-full z-10 hover:bg-black/40 transition-colors" id="nextBtn">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </button>
  </section>

  <main class="max-w-5xl mx-auto mt-10 px-4">
    <!-- Content section -->
    <section class="mt-12 flex flex-col md:flex-row items-center md:items-start justify-between gap-8">
      <div class="max-w-xl flex-shrink-0">
        <h1 class="font-mono text-[2.5rem] md:text-[4rem] leading-tight mb-2 font-feature-liga-off">
          Cucian Kotor?
        </h1>
        <h2 class="font-mono text-xl md:text-2xl mb-6 font-feature-liga-off">
          ... Tapi males
          <span class="text-danger">nyuci?</span> 🤔
        </h2>
        <p class="font-bold text-sm leading-snug">
          Selamat datang di Nyuci Kilat! solusi cepat, bersih, dan praktis untuk
          semua kebutuhan laundry kamu!
          <br/>
          Kami hadir buat kamu yang ingin punya lebih banyak waktu tanpa repot
          dengan cucian. Tinggal antar atau order online, kami yang urus
          semuanya.
          <br/>
          Pakaian bersih, wangi, dan rapi, siap kamu pakai lagi!
        </p>
        <div class="mt-6 flex flex-wrap gap-6">
          <button class="bg-accent border border-black/25 rounded px-6 py-2 text-lg font-normal hover:bg-purple-200 transition-colors" type="button">
            Tentang Kami
          </button>
          <button class="bg-accent border border-black/25 rounded px-6 py-2 text-lg font-normal hover:bg-purple-200 transition-colors" type="button">
            Fakta Nyuci Kilat
          </button>
        </div>
      </div>
      <div class="bg-secondary rounded-full w-40 h-40 md:w-56 md:h-56 flex items-center justify-center shrink-0">
        <img alt="males nyuci" class="w-32 h-auto md:w-44" height="180" src="assets/img/laundry.png" width="180"/>
      </div>
    </section>
  </main>


  <!-- Tentang Kami -->
  <main id="tentang-kami" class="px-5 py-16 text-center sm:px-20 sm:py-24">
    <h1 class="mb-12 text-xl font-medium sm:text-2xl">
      Siapa sih <span class="text-[#ff2a00]">Nyuci Kilat</span> itu Sebenernya?
    </h1>
    
    <div class="flex flex-col items-center gap-8 mb-10 sm:flex-row sm:justify-center sm:gap-10 sm:mb-16 sm:items-stretch">
      <!-- Card 1 -->
      <div class="w-full p-5 bg-white rounded shadow-md sm:w-72 sm:p-6 h-full flex flex-col">
        <h2 class="mb-4 text-lg font-semibold text-[#1a5de8] sm:text-xl">Siapa kami?</h2>
        <p class="text-xs text-left sm:text-sm sm:leading-relaxed">
          Kami adalah tim profesional yang peduli dengan kebersihan dan kenyamanan kamu. Berdiri sejak 2025, Nyuci Kilat hadir untuk membantu kamu menghemat waktu dan tenaga. Kami percaya, cucian bersih bisa bikin hari-hari jadi lebih semangat!
        </p>
      </div>
      
      <!-- Card 2 -->
      <div class="w-full p-5 bg-white rounded shadow-md sm:w-72 sm:p-6 h-full flex flex-col">
        <h2 class="mb-4 text-lg font-semibold text-[#1a5de8] sm:text-xl">Apa yang Kami Tawarkan?</h2>
        <p class="text-xs text-left sm:text-sm sm:leading-relaxed">
          Dari cuci kiloan, laundry satuan, hingga layanan antar-jemput semua bisa kamu nikmati dengan harga terjangkau dan hasil maksimal. Kami menggunakan deterjen berkualitas dan mesin cuci modern agar pakaian kamu selalu terawat dan wangi tahan lama.
        </p>
      </div>
      
      <!-- Card 3 -->
      <div class="w-full p-5 bg-white rounded shadow-md sm:w-72 sm:p-6 h-full flex flex-col">
        <h2 class="mb-4 text-lg font-semibold text-[#1a5de8] sm:text-xl">Kenapa Pilih Kami?</h2>
        <p class="text-xs text-left sm:text-sm sm:leading-relaxed">
          Karena kami mengutamakan kualitas, kecepatan, dan kepuasan pelanggan. Layanan ramah, proses cepat, dan hasil bersih jadi komitmen utama kami. Banyak pelanggan puas dan jadi langganan tetap. Sekali coba, pasti balik lagi!
        </p>
      </div>
    </div>
    
    <div class="flex flex-wrap justify-center gap-4 sm:gap-12">
      <button class="px-6 py-1 text-sm border border-black rounded-full bg-[#b9b0e6] sm:text-base sm:px-8 sm:py-2">
        Cepat Kilat
      </button>
      <button class="px-6 py-1 text-sm border border-black rounded-full bg-[#b9b0e6] sm:text-base sm:px-8 sm:py-2">
        Terpercaya
      </button>
      <button class="px-6 py-1 text-sm border border-black rounded-full bg-[#b9b0e6] sm:text-base sm:px-8 sm:py-2">
        Rating Tinggi
      </button>
    </div>
  </main>
 
  <!-- Fakta Nyuci Kilat -->
  <main id="fakta-nyuci" class="flex flex-col items-center text-center px-5 py-10 flex-grow">
    <h1 class="text-2xl md:text-3xl font-normal mb-5">
      Fakta Tentang <span class="text-[#f36f2f]">Nyuci Kilat</span>
    </h1>
    <img src="assets/img/logo.png" alt="" class="w-40 h-40 mb-10" />
    <div class="flex flex-col md:flex-row md:justify-center md:space-x-8 space-y-6 md:space-y-0 max-w-5xl w-full">
      <div class="bg-white shadow-md rounded p-5 flex-1 max-w-md mx-auto md:max-w-none">
        <div class="text-[#1a5de8] text-sm mb-2">Jumlah Outlet</div>
        <div class="font-bold text-3xl mb-2"><?php echo $outlet; ?></div>
        <div class="font-extrabold text-xs">Cabang yang tersebar di seluruh dunia</div>
      </div>
      <div class="bg-white shadow-md rounded p-5 flex-1 max-w-md mx-auto md:max-w-none">
        <div class="text-[#1a5de8] text-sm mb-2">Ulasan dan Rating</div>
        <div class="font-bold text-3xl mb-2 flex items-center space-x-1">
          <span>4.99</span>
          <span class="text-yellow-400 text-3xl select-none">⭐️⭐️⭐️⭐️⭐️</span>
        </div>
        <div class="font-extrabold text-xs">Berdasarkan total jumlah rating selama 2025</div>
      </div>
      <div class="bg-white shadow-md rounded p-5 flex-1 max-w-md mx-auto md:max-w-none">
        <div class="text-[#1a5de8] text-sm mb-2">Total Pegawai</div>
        <div class="font-bold text-3xl mb-2"><?php echo $karyawan; ?></div>
        <div class="font-extrabold text-xs">Sebanyak <?php echo $karyawan; ?> orang yang tersebar di <?php echo $outlet; ?> cabang</div>
      </div>
    </div>
  </main>

  <footer class="bg-white border-t border-black text-center py-3 font-extrabold font-mono text-sm">
    © 2025 Nyuci Kilat. All rights reserved.
  </footer>
  
  <!-- WhatsApp floating button -->
  <div class="fixed bottom-5 right-5 z-50">
    <a href="https://wa.me/+628984612344" class="w-12 h-12 rounded-full relative cursor-pointer hover:scale-110 transition-transform duration-200 block">
      <img alt="Whatsapp section" class="w-full h-full rounded-full" height="48" src="assets/img/whatsapp section.png" width="48"/>
    </a>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Banner slider functionality
      const banners = document.querySelectorAll('.banner-slide');
      const dots = document.querySelectorAll('.dot');
      const prevBtn = document.getElementById('prevBtn');
      const nextBtn = document.getElementById('nextBtn');
      let currentIndex = 0;
      let slideInterval;
      
      function updateSlides() {
        banners.forEach((slide, index) => {
          slide.classList.remove('active', 'prev', 'next');
          
          if (index === currentIndex) {
            slide.classList.add('active');
            slide.classList.remove('opacity-0', 'translate-x-full');
            slide.classList.add('opacity-100', 'translate-x-0');
          } else if (index === (currentIndex - 1 + banners.length) % banners.length) {
            slide.classList.add('prev');
            slide.classList.remove('opacity-0', 'translate-x-0');
            slide.classList.add('opacity-0', '-translate-x-full');
          } else if (index === (currentIndex + 1) % banners.length) {
            slide.classList.add('next');
            slide.classList.remove('opacity-0', 'translate-x-0');
            slide.classList.add('opacity-0', 'translate-x-full');
          } else {
            slide.classList.add('opacity-0', 'translate-x-full');
          }
        });
        
        dots.forEach((dot, index) => {
          if (index === currentIndex) {
            dot.classList.replace('bg-gray-300', 'bg-white');
          } else {
            dot.classList.replace('bg-white', 'bg-gray-300');
          }
        });
      }
      
      function goToSlide(index) {
        currentIndex = (index + banners.length) % banners.length;
        updateSlides();
      }
      
      function startSlider() {
        slideInterval = setInterval(() => {
          goToSlide(currentIndex + 1);
        }, 5000);
      }
      
      function resetTimer() {
        clearInterval(slideInterval);
        startSlider();
      }
      
      dots.forEach(dot => {
        dot.addEventListener('click', () => {
          const index = parseInt(dot.getAttribute('data-index'));
          goToSlide(index);
          resetTimer();
        });
      });
      
      prevBtn.addEventListener('click', () => {
        goToSlide(currentIndex - 1);
        resetTimer();
      });
      
      nextBtn.addEventListener('click', () => {
        goToSlide(currentIndex + 1);
        resetTimer();
      });
      
      // Initialize the slider
      updateSlides();
      startSlider();
      const tentangKamiBtn = document.querySelectorAll('button');
  tentangKamiBtn.forEach(button => {
    if (button.textContent.includes('Tentang Kami')) {
      button.addEventListener('click', function() {
        document.getElementById('tentang-kami').scrollIntoView({
          behavior: 'smooth'
        });
      });
    }
    
    if (button.textContent.includes('Fakta Nyuci Kilat')) {
      button.addEventListener('click', function() {
        document.getElementById('fakta-nyuci').scrollIntoView({
          behavior: 'smooth'
        });
      });
    }
  });
      // Sticky navbar functionality
      const navbar = document.getElementById('navbar');
      const headerOffset = navbar.offsetTop;
      
      function stickyNav() {
        if (window.pageYOffset > headerOffset) {
          navbar.classList.add('sticky-nav');
        } else {
          navbar.classList.remove('sticky-nav');
        }
      }
      
      window.addEventListener('scroll', stickyNav);
      
      // Make banner responsive
      function adjustBannerHeight() {
        const bannerContainer = document.querySelector('.responsive-banner');
        if (window.innerWidth < 768) {
          // Adjust padding-bottom for mobile (taller aspect ratio)
          bannerContainer.style.paddingBottom = '50%';
        } else {
          // Default aspect ratio for desktop
          bannerContainer.style.paddingBottom = '30%';
        }
      }
      
      // Run on load and resize
      adjustBannerHeight();
      window.addEventListener('resize', adjustBannerHeight);
    });
  </script>
</body>
</html>