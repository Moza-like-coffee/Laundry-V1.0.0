<?php
$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>  
  
  <!-- Tombol Buka Sidebar Khusus Desktop -->
   <button id="openSidebarBtn" class="hidden md:flex items-center px-4 py-2 bg-[#3ca87f] text-white font-bold rounded-md fixed top-5 left-5 z-20">
  <i class="fas fa-bars mr-2"></i> Menu
</button>
    <!-- Mobile hamburger button -->
    <header class="md:hidden flex items-center mb-4">
  <button id="hamburgerBtn" aria-label="Open sidebar" class="bg-white rounded-md p-2 shadow-md focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500 fixed top-5 left-5 z-20">
    <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <line x1="3" y1="12" x2="21" y2="12" />
      <line x1="3" y1="6" x2="21" y2="6" />
      <line x1="3" y1="18" x2="21" y2="18" />
    </svg>
  </button>
</header>

<!-- Sidebar -->
<aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-80 bg-white p-8 flex flex-col rounded-tr-lg rounded-br-lg transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
  <div class="flex items-center mb-12 justify-between">
    <div class="flex items-center">
      <div class="w-10 h-10 bg-[#3ca87f] rounded-lg flex-shrink-0"></div>
      <div class="ml-3">
        <div class="text-sm font-medium text-gray-900 leading-tight"> <?php echo htmlspecialchars($_SESSION['username']); ?></div>
        <div class="text-xs text-gray-400 leading-tight"> <?php echo htmlspecialchars($_SESSION['role']); ?></div>
      </div>
    </div>
 
    <!-- Tombol X khusus desktop -->
<button id="closeSidebarBtn" class="hidden md:flex bg-gray-300 rounded-md px-3 py-1 font-bold text-gray-900">
  X
</button>

  </div>
  <nav class="flex flex-col space-y-5 flex-grow">
  <a href="dashboard.php" class="flex items-center text-sm font-medium text-gray-900 hover:text-gray-700">
      <i class="fas fa-home mr-3 text-lg"></i>Dashboard
    </a>
  <?php if ($role === 'admin' || $role === 'kasir'): ?>
    <a href="registrasi_pelanggan.php" class="flex items-center text-sm font-medium text-gray-900 hover:text-gray-700">
      <i class="fas fa-user mr-3 text-lg"></i>Registrasi Pelanggan
    </a>
  <?php endif; ?>

  <?php if ($role === 'admin'): ?>
    <a href="menu_outlet.php" class="flex items-center text-sm font-medium text-gray-900 hover:text-gray-700">
      <i class="fas fa-shopping-cart mr-3 text-lg"></i>Menu Outlet
    </a>
    <a href="menu_produk.php" class="flex items-center text-sm font-medium text-gray-900 hover:text-gray-700">
      <i class="fas fa-box mr-3 text-lg"></i>Menu Produk
    </a>
    <a href="menu_pengguna.php" class="flex items-center text-sm font-medium text-gray-900 hover:text-gray-700">
      <i class="fas fa-user-plus mr-3 text-lg"></i>Menu Pengguna
    </a>
  <?php endif; ?>

  <?php if ($role === 'admin' || $role === 'kasir'): ?>
    <a href="menu_transaksi.php" class="flex items-center text-sm font-medium text-gray-900 hover:text-gray-700">
      <i class="fas fa-comment-alt mr-3 text-lg"></i>Menu Transaksi
    </a>
  <?php endif; ?>

  <?php if ($role === 'admin' || $role === 'owner' || $role === 'kasir'): ?>
    <a href="menu_laporan.php" class="flex items-center text-sm font-medium text-gray-900 hover:text-gray-700">
      <i class="far fa-file-alt mr-3 text-lg"></i>Menu Laporan
    </a>
  <?php endif; ?>

  <?php if ($role === 'admin'|| $role === 'owner' || $role === 'kasir'): ?>
    <a href="settings.php" class="flex items-center text-sm font-medium text-gray-900 hover:text-gray-700">
      <i class="fas fa-cog mr-3 text-lg"></i>Settings
    </a>
  <?php endif; ?>
</nav>

  <!-- Tombol Sembunyikan khusus mobile -->
  <button id="hideSidebarBtn" class="md:hidden mt-auto flex items-center text-gray-600 text-sm font-medium hover:text-gray-900 focus:outline-none">
    <i class="fas fa-angle-left mr-2 text-lg"></i>Sembunyikan Sidebar
  </button>
<button id="logoutBtn" class="mt-3 flex items-center text-gray-600 text-sm font-medium hover:text-gray-900 focus:outline-none">
  <i class="fas fa-sign-out-alt mr-2 text-lg"></i>
  <a href="logout.php">Log out</a>
</button>

</aside>

<!-- Overlay for mobile -->
<div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden md:hidden"></div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');
    const hideSidebarBtn = document.getElementById('hideSidebarBtn');

    function openSidebar() {
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
    }

    function closeSidebar() {
  sidebar.classList.add('-translate-x-full');
  sidebar.classList.remove('md:translate-x-0');
  overlay.classList.add('hidden');
}

const openSidebarBtn = document.getElementById('openSidebarBtn');

openSidebarBtn?.addEventListener('click', () => {
  console.log('Open sidebar button clicked');
  sidebar.classList.remove('-translate-x-full');
  sidebar.classList.add('md:translate-x-0');
});


    hamburgerBtn?.addEventListener('click', openSidebar);
    overlay?.addEventListener('click', closeSidebar);
    closeSidebarBtn?.addEventListener('click', () => {
      console.log('Close sidebar button clicked');
      closeSidebar();
    });
    hideSidebarBtn?.addEventListener('click', closeSidebar);
  });
</script>

