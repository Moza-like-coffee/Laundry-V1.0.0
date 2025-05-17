<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Menu Laporan</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>
<body class="bg-gradient-to-b from-[#0B1526] to-[#1F3A5A] min-h-screen flex">
  <aside class="bg-white rounded-xl m-5 flex flex-col justify-between w-72 min-w-[18rem]">
    <div>
      <div class="flex items-center gap-3 px-5 py-6">
        <div class="w-10 h-10 rounded-md bg-[#3AA77A]" aria-label="User icon with green background"></div>
        <div class="flex flex-col">
          <span class="text-black text-sm font-medium leading-tight">Username</span>
          <span class="text-gray-400 text-xs leading-tight">Role</span>
        </div>
      </div>
      <nav>
        <ul class="px-5 space-y-4">
          <li>
            <a href="#" class="flex items-center gap-3 text-xs font-medium text-black">
              <i class="fas fa-user text-lg"></i>
              Registrasi Pelanggan
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center gap-3 text-xs font-medium text-black">
              <i class="fas fa-shopping-cart text-lg"></i>
              Menu Outlet
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center gap-3 text-xs font-medium text-black">
              <i class="fas fa-box text-lg"></i>
              Menu Produk
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center gap-3 text-xs font-medium text-black">
              <i class="fas fa-user-plus text-lg"></i>
              Menu Pengguna
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center gap-3 text-xs font-medium text-black">
              <i class="far fa-comment-dots text-lg"></i>
              Menu Transaksi
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center gap-3 text-xs font-medium text-black bg-gray-300 rounded-lg px-3 py-2">
              <i class="far fa-file-alt text-lg"></i>
              Menu Laporan
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center gap-3 text-xs font-medium text-black">
              <i class="fas fa-cog text-lg"></i>
              Settings
            </a>
          </li>
        </ul>
      </nav>
    </div>
    <button class="flex items-center gap-2 text-xs font-normal text-black px-5 py-4 focus:outline-none" aria-label="Log out">
      <i class="fas fa-redo-alt text-base text-gray-500"></i>
      Log out
    </button>
  </aside>
  <main class="flex-1 m-5 flex flex-col gap-10 max-w-5xl">
    <div class="bg-white rounded-lg p-3 max-w-full w-full">
      <button aria-label="Close menu" class="bg-gray-300 rounded-md px-3 py-2 font-bold text-xl select-none">X</button>
    </div>
    <section class="bg-white rounded-lg flex flex-col max-w-full w-full">
      <div class="border-b border-black/10 px-4 py-3">
        <button class="bg-slate-300 border border-black rounded-md px-4 py-2 text-sm font-medium">Filter Laporan</button>
      </div>
      <div class="bg-gray-300 rounded-lg mx-4 my-4 grid grid-cols-[40px_1.5fr_1.5fr_1.5fr_1.5fr_1fr_1fr_1fr] items-center font-semibold text-sm px-4 py-3">
        <div>No</div>
        <div>Tanggal</div>
        <div>Invoice</div>
        <div>Pelanggan</div>
        <div>Pembayaran</div>
        <div>Harga</div>
        <div>Status</div>
        <div>Kasir</div>
      </div>
      <div class="bg-gray-300 rounded-lg mx-4 mb-4 min-h-[200px]"></div>
    </section>
  </main>

  <style>
    @media (max-width: 640px) {
      aside {
        width: 100% !important;
        min-width: auto !important;
        margin: 0 0 12px 0 !important;
        border-radius: 10px !important;
      }
      main {
        margin: 0 12px !important;
      }
      .grid {
        grid-template-columns: 30px 1fr 1fr 1fr 1fr 1fr 1fr 1fr !important;
        font-size: 0.75rem !important;
        margin: 0 8px 8px 8px !important;
        padding: 8px 12px !important;
      }
      .bg-gray-300.min-h-[200px] {
        min-height: 150px !important;
        margin: 0 8px 8px 8px !important;
      }
      button {
        padding: 6px 12px !important;
        font-size: 0.75rem !important;
      }
      .topbar > button {
        padding: 6px 10px !important;
        font-size: 1.125rem !important;
      }
    }
  </style>
</body>
</html>