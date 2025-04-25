<?php
session_start();
include '../database/connect.php';

$success = null;
if (isset($_SESSION['status'])) {
    $success = $_SESSION['status'];
    unset($_SESSION['status']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = $_POST['nama-pelanggan'];
  $alamat = $_POST['alamat'];
  $jenis_kelamin = $_POST['jenis-kelamin'];
  $tlp = $_POST['no-telp'];

  $query = "INSERT INTO tb_member (nama, alamat, jenis_kelamin, tlp) VALUES (?, ?, ?, ?)";
  $stmt = $mysqli->prepare($query);
  if ($stmt) {
      $stmt->bind_param("ssss", $nama, $alamat, $jenis_kelamin, $tlp);
      if ($stmt->execute()) {
          $_SESSION['status'] = true;
      } else {
          $_SESSION['status'] = false;
      }
      $stmt->close();
  } else {
      $_SESSION['status'] = false;
  }

 // anti resubmission form
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}
include 'sidebar.php';
?>

<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registrasi Pelanggan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <style>
    nav ul::-webkit-scrollbar {
      height: 6px;
    }
    nav ul::-webkit-scrollbar-thumb {
      background-color: #b7b7b7;
      border-radius: 3px;
    }
  </style>
</head>
<body class="bg-gradient-to-b from-[#0B1526] to-[#1E3A5F] min-h-screen font-inter text-black">
  <div class="flex">
    <main class="ml-[250px] flex-1 flex justify-center items-center p-6 md:p-10 min-h-screen">
      <section class="bg-white rounded-md p-6 w-full max-w-4xl">
        <div class="border-b border-black pb-2 mb-4">
          <button class="bg-[#C7D9F9] border border-black rounded-md px-4 py-2 text-[14px] font-normal">Tambah Pelanggan</button>
        </div>
        <form method="POST" class="flex flex-col gap-4 text-sm"> 
          <label for="nama-pelanggan" class="text-[13px] font-semibold">Nama Pelanggan</label>
          <input id="nama-pelanggan" name="nama-pelanggan" type="text" autocomplete="off"
              class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

          <label for="alamat" class="text-[13px] font-semibold">Alamat</label>
          <input id="alamat" name="alamat" type="text" autocomplete="off"
              class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

              <label for="jenis-kelamin" class="text-[13px] font-semibold">Jenis Kelamin</label>
              <div class="relative max-w-sm">
              <select id="jenis-kelamin" name="jenis-kelamin" required
                class="appearance-none h-9 w-full rounded-md bg-gray-300 px-3 text-sm outline-none pr-8">
                <option value="" disabled selected>Pilih jenis kelamin</option>
                <option value="L">Laki-Laki</option>
                <option value="P">Perempuan</option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-700">
                <i class="fas fa-chevron-down text-sm"></i>
              </div>
            </div>



          <label for="no-telp" class="text-[13px] font-semibold">No. Telp</label>
          <input id="no-telp" name="no-telp" type="text" autocomplete="off"
              class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

          <button type="submit"
              class="bg-[#D1E7CD] border border-black rounded-md px-4 py-1.5 text-sm font-normal w-max mt-3">Konfirmasi</button>
        </form>
      </section>
    </main>
  </div>

  <?php if ($success === true): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Data pelanggan berhasil disimpan.',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Oke'
      });
    </script>
  <?php elseif ($success === false): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: 'Terjadi kesalahan saat menyimpan data.',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Tutup'
      });
    </script>
  <?php endif; ?>
</body>
</html>
