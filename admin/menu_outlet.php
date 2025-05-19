<?php
session_start();
include '../database/connect.php';
include 'sidebar.php';
$allowed_roles = ['admin'];
$user_role = $_SESSION['role'] ?? null;

if (!in_array($user_role, $allowed_roles)) {
  echo '
  <html>
  <head>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>
  <body>
      <script>
          Swal.fire({
              icon: "error",
              title: "Akses Ditolak!",
              text: "Anda tidak memiliki izin untuk mengakses halaman ini.",
              timer: 2000,
              timerProgressBar: true
          });

          // Redirect otomatis setelah 2 menit
          setTimeout(() => {
              window.location.href = "dashboard.php";
          }, 2000);
      </script>
  </body>
  </html>
  ';
  exit;
}
$swal_script = ''; // Siapkan variabel kosong untuk script SweetAlert

// Cek apakah ada status di URL
if (isset($_GET['status'])) {
  $status = $_GET['status'];

  if ($status == 'success') {
      $swal_script = "
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data outlet berhasil dihapus!',
            showConfirmButton: false,
            timer: 2000
          }).then(function() {
            window.location.href='menu_outlet.php'; // Redirect setelah 2 detik
          });
      ";
  } elseif ($status == 'error') {
      $swal_script = "
          Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal menghapus outlet.',
            showConfirmButton: true
          });
      ";
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cek apakah ini form edit outlet (punya field nama-outlet-baru)
    if (isset($_POST['nama-outlet-baru'])) {
        $id = $_POST['nama-outlet']; // ini sebenarnya ID dari outlet
        $namaBaru = $_POST['nama-outlet-baru'];
        $alamatBaru = $_POST['lokasi-outlet-baru'];
        $telpBaru = $_POST['no-telp-baru'];

        if (!empty($id) && !empty($namaBaru) && !empty($alamatBaru) && !empty($telpBaru)) {
            $stmt = $mysqli->prepare("UPDATE tb_outlet SET nama = ?, alamat = ?, tlp = ? WHERE id = ?");
            $stmt->bind_param("sssi", $namaBaru, $alamatBaru, $telpBaru, $id);

            if ($stmt->execute()) {
                $swal_script = "
                    Swal.fire({
                      icon: 'success',
                      title: 'Berhasil!',
                      text: 'Data outlet berhasil diperbarui!',
                      showConfirmButton: false,
                      timer: 2000
                    }).then(function() {
                      window.location.href='menu_outlet.php';
                    });
                ";
            } else {
                $swal_script = "
                    Swal.fire({
                      icon: 'error',
                      title: 'Gagal!',
                      text: 'Gagal memperbarui data outlet!'
                    });
                ";
            }
        } else {
            $swal_script = "
                Swal.fire({
                  icon: 'warning',
                  title: 'Oops!',
                  text: 'Semua field wajib diisi!'
                });
            ";
        }
    } else {
        // Jika bukan form edit, berarti ini form tambah outlet
        $namaOutlet = mysqli_real_escape_string($mysqli, $_POST['nama-outlet']);
        $lokasiOutlet = mysqli_real_escape_string($mysqli, $_POST['lokasi-outlet']);
        $noTelp = mysqli_real_escape_string($mysqli, $_POST['no-telp']);

        if (!empty($namaOutlet) && !empty($lokasiOutlet) && !empty($noTelp)) {
            $query = "INSERT INTO tb_outlet (nama, alamat, tlp) VALUES ('$namaOutlet', '$lokasiOutlet', '$noTelp')";
            
            if (mysqli_query($mysqli, $query)) {
                $swal_script = "
                    Swal.fire({
                      icon: 'success',
                      title: 'Berhasil!',
                      text: 'Outlet berhasil ditambahkan!',
                      showConfirmButton: false,
                      timer: 2000
                    }).then(function() {
                      window.location.href='menu_outlet.php';
                    });
                ";
            } else {
                $swal_script = "
                    Swal.fire({
                      icon: 'error',
                      title: 'Gagal!',
                      text: 'Gagal menambahkan outlet: " . mysqli_error($mysqli) . "'
                    });
                ";
            }
        } else {
            $swal_script = "
                Swal.fire({
                  icon: 'warning',
                  title: 'Oops!',
                  text: 'Semua field wajib diisi.'
                });
            ";
        }
    }
}
?>



<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Menu Outlet</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
// Fungsi untuk mengubah tombol yang aktif dan non-aktif
function updateButtonStyles(activeButtonId) {
    const buttons = ['btnTambah', 'btnEdit', 'btnHapus'];
    buttons.forEach(id => {
        const button = document.getElementById(id);
        if (id === activeButtonId) {
            if (id === 'btnHapus') {
                button.classList.add('bg-[#d81e2a]', 'text-white');
                button.classList.remove('bg-[#FAF5F0]', 'bg-[#C7D9F9]');
            } else if (id === 'btnTambah') {
                button.classList.add('bg-[#C7D9F9]');
                button.classList.remove('bg-[#FAF5F0]', 'bg-[#d81e2a]', 'text-white');
            } else {
                button.classList.add('bg-[#C7D9F9]');
                button.classList.remove('bg-[#FAF5F0]', 'bg-[#d81e2a]', 'text-white');
            }
        } else {
            // Reset tombol lain ke status non-aktif
            button.classList.add('bg-[#FAF5F0]');
            button.classList.remove('bg-[#C7D9F9]', 'bg-[#d81e2a]', 'text-white');
        }
    });
}


  // Event listener untuk tombol "Edit"
  document.getElementById('btnEdit').addEventListener('click', function() {
  const container = document.getElementById('form-container');
  container.innerHTML = '';

  fetch('edit_outlet.php')
  .then(response => response.text())
  .then(data => {
      container.innerHTML = data;

      // PENTING!! setelah formnya masuk, baru pasang event listener
      initEditOutletForm();
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
        initEditOutletForm();
        
        // Setelah form dimuat, tambahkan event listener untuk form delete
        const deleteForm = document.getElementById('deleteForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Mencegah pengiriman form sebelum konfirmasi

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Data outlet ini akan dihapus secara permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();  // Kirim form jika konfirmasi di-setuju
                    }
                });
            });
        }
    })
    .catch(error => {
        console.error('Error loading form:', error);
        container.innerHTML = '<p class="text-red-500">Gagal memuat form hapus.</p>';
    });

    updateButtonStyles('btnHapus');
});

// Fungsi untuk memeriksa apakah form hapus sedang aktif
function isDeleteFormActive() {
    const formContainer = document.getElementById('form-container');
    if (formContainer) {
        return formContainer.querySelector('#deleteForm') !== null;
    }
    return false;
}


  window.onload = function() {
  <?php echo $swal_script; ?>
}
  </script>
  <script src="../assets/js/edit_outlet.js"></script>

</body>
</html>
