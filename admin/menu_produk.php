<?php
session_start();
include '../database/connect.php';
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

// Function to get ENUM values from a column
function getEnumValues($mysqli, $table, $column) {
    $query = "SHOW COLUMNS FROM $table LIKE '$column'";
    $result = mysqli_query($mysqli, $query);
    $row = mysqli_fetch_assoc($result);
    
    preg_match("/^enum\(\'(.*)\'\)$/", $row['Type'], $matches);
    $enum = explode("','", $matches[1]);
    
    return $enum;
}

// Get jenis values from tb_paket
$jenisProdukOptions = getEnumValues($mysqli, 'tb_paket', 'jenis');

$swal_script = ''; // Siapkan variabel kosong untuk script SweetAlert

// Cek apakah ada status di URL
if (isset($_GET['status'])) {
    $status = $_GET['status'];

    if ($status == 'success') {
        $swal_script = "
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data produk berhasil disimpan!',
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location.href='menu_produk.php';
            });
        ";
    } elseif ($status == 'error') {
        $swal_script = "
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal menyimpan produk.',
                showConfirmButton: true
            });
        ";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cek apakah ini form tambah produk
    if (isset($_POST['jenis-produk'])) {
        // Handle form submission for product
        $namaPaket = mysqli_real_escape_string($mysqli, $_POST['nama-paket'] ?? '');
        $jenisProduk = mysqli_real_escape_string($mysqli, $_POST['jenis-produk'] ?? '');
        $harga = mysqli_real_escape_string($mysqli, $_POST['harga'] ?? '');
        $outlet = mysqli_real_escape_string($mysqli, $_POST['outlet'] ?? '');

        if (!empty($namaPaket) && !empty($jenisProduk) && !empty($harga) && !empty($outlet)) {
            $query = "INSERT INTO tb_paket (nama_paket, jenis, harga, id_outlet) VALUES ('$namaPaket', '$jenisProduk', '$harga', '$outlet')";
            
            if (mysqli_query($mysqli, $query)) {
                $_SESSION['status'] = 'success';
                header("Location: menu_produk.php?status=success");
                exit();
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Gagal menambahkan produk: ' . mysqli_error($mysqli);
                header("Location: menu_produk.php?status=error");
                exit();
            }
        } else {
            $_SESSION['status'] = 'warning';
            $_SESSION['message'] = 'Semua field wajib diisi.';
            header("Location: menu_produk.php");
            exit();
        }
    }
    // Cek apakah ini form edit produk
    elseif (isset($_POST['nama-paket-baru'])) {
        // Handle form edit submission
        $idPaket = $_POST['nama-paket'] ?? '';
        $namaBaru = $_POST['nama-paket-baru'] ?? '';
        $jenisBaru = $_POST['jenis-produk-2'] ?? '';
        $hargaBaru = $_POST['harga-baru'] ?? '';
        
        if (!empty($idPaket) && !empty($namaBaru) && !empty($jenisBaru) && !empty($hargaBaru)) {
            $stmt = $mysqli->prepare("UPDATE tb_paket SET nama_paket = ?, jenis = ?, harga = ? WHERE id = ?");
            $stmt->bind_param("ssdi", $namaBaru, $jenisBaru, $hargaBaru, $idPaket);
            
            if ($stmt->execute()) {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = 'Data produk berhasil diperbarui!';
                header("Location: menu_produk.php?status=success");
                exit();
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Gagal memperbarui produk: ' . $mysqli->error;
                header("Location: menu_produk.php?status=error");
                exit();
            }
        } else {
            $_SESSION['status'] = 'warning';
            $_SESSION['message'] = 'Semua field wajib diisi.';
            header("Location: menu_produk.php");
            exit();
        }
    }
}

// Tampilkan pesan dari session jika ada
if (isset($_SESSION['message'])) {
    $swal_script = "
        Swal.fire({
            icon: '" . ($_SESSION['status'] ?? 'info') . "',
            title: '" . ($_SESSION['status'] == 'success' ? 'Berhasil!' : ($_SESSION['status'] == 'error' ? 'Gagal!' : 'Peringatan!')) . "',
            text: '" . addslashes($_SESSION['message']) . "',
            showConfirmButton: " . ($_SESSION['status'] == 'success' ? 'false' : 'true') . ",
            timer: " . ($_SESSION['status'] == 'success' ? '2000' : 'null') . "
        }).then(function() {
            " . ($_SESSION['status'] == 'success' ? "window.location.href='menu_produk.php';" : "") . "
        });
    ";
    unset($_SESSION['status']);
    unset($_SESSION['message']);
}

include 'sidebar.php';
?>

<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Menu Produk</title>
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
    <button id="btnTambah" class="bg-[#C7D9F9] border border-black rounded-md px-4 py-2 text-[14px] font-normal w-full sm:w-auto">Tambah Produk</button>
    <button id="btnEdit" class="bg-[#FAF5F0] border border-black rounded-md px-4 py-2 text-[14px] font-normal w-full sm:w-auto">Edit Produk</button>
    <button id="btnHapus" class="bg-[#FAF5F0] border border-black rounded-md px-4 py-2 text-[14px] font-normal w-full sm:w-auto">Hapus Produk</button>
    <button id="btnInfo" class="bg-[#FAF5F0] border border-black rounded-md px-4 py-2 text-[14px] font-normal w-full sm:w-auto">Info Produk</button>
</div>
                </div>

                <div id="form-container">
                    <!-- Form Tambah Produk Default -->
                    <form class="flex flex-col gap-3 text-sm" method="POST" id="formTambah">
                        <label for="nama-paket" class="text-[13px] font-semibold">Nama Paket</label>
                        <input id="nama-paket" name="nama-paket" type="text" autocomplete="off" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

                        <label for="jenis-produk" class="text-[13px] font-semibold">Jenis Produk</label>
                        <select id="jenis-produk" name="jenis-produk" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm appearance-none bg-[url('data:image/svg+xml;utf8,<svg fill=\'black\' height=\'6\' viewBox=\'0 0 10 6\' width=\'10\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M1 1L5 5L9 1\' stroke=\'black\' stroke-width=\'1.5\'/></svg>')] bg-no-repeat bg-right-3 bg-center cursor-pointer">
                            <option value="" disabled selected>Pilih Jenis Produk</option>
                            <?php foreach ($jenisProdukOptions as $jenis): ?>
                                <option value="<?= $jenis ?>"><?= $jenis ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label for="harga" class="text-[13px] font-semibold">Harga</label>
                        <input id="harga" name="harga" type="text" autocomplete="off" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

                        <label for="outlet" class="text-[13px] font-semibold">Outlet</label>
                        <select id="outlet" name="outlet" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm appearance-none bg-[url('data:image/svg+xml;utf8,<svg fill=\'black\' height=\'6\' viewBox=\'0 0 10 6\' width=\'10\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M1 1L5 5L9 1\' stroke=\'black\' stroke-width=\'1.5\'/></svg>')] bg-no-repeat bg-right-3 bg-center cursor-pointer">
                            <option value="" disabled selected>Pilih Outlet</option>
                            <?php
                            $query = "SELECT id, nama FROM tb_outlet";
                            $result = mysqli_query($mysqli, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='".$row['id']."'>".$row['nama']."</option>";
                            }
                            ?>
                        </select>

                        <button type="submit" class="bg-[#D1E7CD] border border-black rounded-md px-4 py-1.5 text-sm font-normal w-max mt-3">Simpan</button>
                    </form>
                </div>
            </section>
        </main>
    </div>

    <script>
    // Fungsi untuk mengubah tombol yang aktif dan non-aktif
    function updateButtonStyles(activeButtonId) {
    const buttons = ['btnTambah', 'btnEdit', 'btnHapus', 'btnInfo'];
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
    container.innerHTML = ''; // Kosongkan container
    
    // Load form edit produk
    fetch('edit_produk.php')
    .then(response => response.text())
    .then(data => {
        container.innerHTML = data;
        
        // Pastikan jQuery sudah dimuat
        if (typeof jQuery == 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
            script.onload = function() {
                // Setelah jQuery dimuat, inisialisasi form
                if (typeof initEditProdukForm === 'function') {
                    initEditProdukForm();
                }
            };
            document.head.appendChild(script);
        } else {
            // Jika jQuery sudah ada, langsung inisialisasi
            if (typeof initEditProdukForm === 'function') {
                initEditProdukForm();
            }
        }
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
        container.innerHTML = '';

        container.innerHTML = `
        <form class="flex flex-col gap-3 text-sm" method="POST" id="formTambah">
            <label for="nama-paket" class="text-[13px] font-semibold">Nama Paket</label>
            <input id="nama-paket" name="nama-paket" type="text" autocomplete="off" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

            <label for="jenis-produk" class="text-[13px] font-semibold">Jenis Produk</label>
            <select id="jenis-produk" name="jenis-produk" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm appearance-none bg-[url('data:image/svg+xml;utf8,<svg fill=\'black\' height=\'6\' viewBox=\'0 0 10 6\' width=\'10\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M1 1L5 5L9 1\' stroke=\'black\' stroke-width=\'1.5\'/></svg>')] bg-no-repeat bg-right-3 bg-center cursor-pointer">
                <option value="" disabled selected>Pilih Jenis Produk</option>
                <?php foreach ($jenisProdukOptions as $jenis): ?>
                    <option value="<?= $jenis ?>"><?= $jenis ?></option>
                <?php endforeach; ?>
            </select>

            <label for="harga" class="text-[13px] font-semibold">Harga</label>
            <input id="harga" name="harga" type="text" autocomplete="off" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

            <label for="outlet" class="text-[13px] font-semibold">Outlet</label>
            <select id="outlet" name="outlet" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm appearance-none bg-[url('data:image/svg+xml;utf8,<svg fill=\'black\' height=\'6\' viewBox=\'0 0 10 6\' width=\'10\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M1 1L5 5L9 1\' stroke=\'black\' stroke-width=\'1.5\'/></svg>')] bg-no-repeat bg-right-3 bg-center cursor-pointer">
                <option value="" disabled selected>Pilih Outlet</option>
                <?php
                $query = "SELECT id, nama FROM tb_outlet";
                $result = mysqli_query($mysqli, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='".$row['id']."'>".$row['nama']."</option>";
                }
                ?>
            </select>

            <button type="submit" class="bg-[#D1E7CD] border border-black rounded-md px-4 py-1.5 text-sm font-normal w-max mt-3">Simpan</button>
        </form>
        `;

        updateButtonStyles('btnTambah');
    });

    // Event listener for tombol "Info Produk"
document.getElementById('btnInfo').addEventListener('click', function() {
    const container = document.getElementById('form-container');
    container.innerHTML = '';

    fetch('info_produk.php')
    .then(response => response.text())
    .then(data => {
        container.innerHTML = data;
        
        // Ekstrak dan jalankan script yang ada dalam form info
        const scripts = container.querySelectorAll('script');
        scripts.forEach(script => {
            const newScript = document.createElement('script');
            if (script.src) {
                newScript.src = script.src;
            } else {
                newScript.textContent = script.textContent;
            }
            document.body.appendChild(newScript).parentNode.removeChild(newScript);
        });
    })
    .catch(error => {
        console.error('Error loading product info:', error);
        container.innerHTML = '<p class="text-red-500">Gagal memuat informasi produk.</p>';
    });

    updateButtonStyles('btnInfo');
});
// Event listener untuk tombol "Hapus"
document.getElementById('btnHapus').addEventListener('click', function() {
    const container = document.getElementById('form-container');
    container.innerHTML = '';

    fetch('hapus_produk.php')
    .then(response => response.text())
    .then(data => {
        container.innerHTML = data;
        
        // Ekstrak dan jalankan script yang ada dalam form hapus
        const scripts = container.querySelectorAll('script');
        scripts.forEach(script => {
            const newScript = document.createElement('script');
            if (script.src) {
                newScript.src = script.src;
            } else {
                newScript.textContent = script.textContent;
            }
            document.body.appendChild(newScript).parentNode.removeChild(newScript);
        });
    })
    .catch(error => {
        console.error('Error loading form:', error);
        container.innerHTML = '<p class="text-red-500">Gagal memuat form hapus.</p>';
    });

    updateButtonStyles('btnHapus');
});

    window.onload = function() {
        <?php echo $swal_script; ?>
    }
    </script>
      <script src="../assets/js/edit_produk.js"></script>
</body>
</html>