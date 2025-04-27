<?php
session_start();
include '../database/connect.php';
include 'sidebar.php';
?>

<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Menu Outlet</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>
<body class="bg-gradient-to-b from-[#0B1526] to-[#1E3A5F] min-h-screen font-inter text-black">
  <div class="flex">
    <main class="flex-1 flex justify-center items-center p-6 md:p-10 min-h-screen ml-0 sm:ml-[250px]">
      <section class="bg-white rounded-md p-6 w-full max-w-4xl">
        <div class="border-b border-black pb-2 mb-4">
          <div class="flex flex-wrap gap-2">
            <!-- Tombol untuk Tambah, Edit, dan Hapus Outlet -->
            <button id="btnTambah" class="bg-[#C7D9F9] border border-black rounded-md px-4 py-2 text-[14px] font-normal w-full sm:w-auto">Tambah Outlet</button>
            <button id="btnEdit" class="bg-[#FAF5F0] border border-black rounded-md px-4 py-2 text-[14px] font-normal w-full sm:w-auto">Edit Outlet</button>
            <button id="btnHapus" class="bg-[#FAF5F0] border border-black rounded-md px-4 py-2 text-[14px] font-normal w-full sm:w-auto">Hapus Outlet</button>
          </div>
        </div>

        <!-- Container untuk load form -->
        <div id="form-container">
          <!-- Form Tambah Outlet Default -->
          <form class="flex flex-col gap-3 text-sm" method="POST" id="formTambah">
            <label for="nama-outlet" class="text-[13px] font-semibold">Nama Outlet</label>
            <input id="nama-outlet" name="nama-outlet" type="text" autocomplete="off" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

            <label for="lokasi-outlet" class="text-[13px] font-semibold">Lokasi Outlet</label>
            <input id="lokasi-outlet" name="lokasi-outlet" type="text" autocomplete="off" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

            <label for="no-telp" class="text-[13px] font-semibold">No. Telp</label>
            <input id="no-telp" name="no-telp" type="text" autocomplete="off" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

            <button type="submit" class="bg-[#D1E7CD] border border-black rounded-md px-4 py-1.5 text-sm font-normal w-max mt-3">Simpan</button>
          </form>
        </div>
        <!-- Akhir Container -->
      </section>
    </main>
  </div>

  <!-- Script untuk handle tombol -->
  <script>
  // Fungsi untuk mengubah tombol yang aktif dan non-aktif
  function updateButtonStyles(activeButtonId) {
  const buttons = ['btnTambah', 'btnEdit', 'btnHapus'];
  buttons.forEach(id => {
    const button = document.getElementById(id);
    if (id === activeButtonId) {
      if (id === 'btnHapus') {
        button.classList.add('bg-[#d81e2a]', 'text-white');
        button.classList.remove('bg-[#FAF5F0]', 'bg-[#C7D9F9]', 'text-black');
      } else {
        button.classList.add('bg-[#C7D9F9]', 'text-black');
        button.classList.remove('bg-[#FAF5F0]', 'bg-[#d81e2a]', 'text-white');
      }
    } else {
      // Reset tombol lain ke status non-aktif
      button.classList.add('bg-[#FAF5F0]', 'text-black');
      button.classList.remove('bg-[#C7D9F9]', 'bg-[#d81e2a]', 'text-white');
    }
  });
}


  // Event listener untuk tombol "Edit"
  document.getElementById('btnEdit').addEventListener('click', function() {
      const container = document.getElementById('form-container');
      container.innerHTML = ''; // Kosongkan isi form

      fetch('edit_outlet.php')
      .then(response => response.text())
      .then(data => {
          container.innerHTML = data; 
      })
      .catch(error => {
          console.error('Error loading form:', error);
          container.innerHTML = '<p class="text-red-500">Gagal memuat form edit.</p>';
      });

      updateButtonStyles('btnEdit');
  });

  // Event listener untuk tombol "Tambah"
  document.getElementById('btnTambah').addEventListener('click', function() {
      const container = document.getElementById('form-container');
      container.innerHTML = ''; // Kosongkan isi form

      // Buat ulang form tambah outlet
      container.innerHTML = `
      <form class="flex flex-col gap-3 text-sm" method="POST" id="formTambah">
        <label for="nama-outlet" class="text-[13px] font-semibold">Nama Outlet</label>
        <input id="nama-outlet" name="nama-outlet" type="text" autocomplete="off" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

        <label for="lokasi-outlet" class="text-[13px] font-semibold">Lokasi Outlet</label>
        <input id="lokasi-outlet" name="lokasi-outlet" type="text" autocomplete="off" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

        <label for="no-telp" class="text-[13px] font-semibold">No. Telp</label>
        <input id="no-telp" name="no-telp" type="text" autocomplete="off" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

        <button type="submit" class="bg-[#D1E7CD] border border-black rounded-md px-4 py-1.5 text-sm font-normal w-max mt-3">Simpan</button>
      </form>
      `;

      updateButtonStyles('btnTambah');
  });

  // Event listener untuk tombol "Hapus"
  document.getElementById('btnHapus').addEventListener('click', function() {
      const container = document.getElementById('form-container');
      container.innerHTML = ''; // Kosongkan isi form

      fetch('hapus_outlet.php')
      .then(response => response.text())
      .then(data => {
          container.innerHTML = data; 
      })
      .catch(error => {
          console.error('Error loading form:', error);
          container.innerHTML = '<p class="text-red-500">Gagal memuat form edit.</p>';
      });

      updateButtonStyles('btnHapus');
  });
  </script>
</body>
</html>
