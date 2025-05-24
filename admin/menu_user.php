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

// Get role values from tb_user
$roleOptions = getEnumValues($mysqli, 'tb_user', 'role');

$swal_script = ''; // Siapkan variabel kosong untuk script SweetAlert

// Cek apakah ada status di URL
if (isset($_GET['status'])) {
    $status = $_GET['status'];

    if ($status == 'success') {
        $swal_script = "
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data pengguna berhasil disimpan!',
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location.href='menu_user.php';
            });
        ";
    } elseif ($status == 'error') {
        $swal_script = "
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal menyimpan pengguna.',
                showConfirmButton: true
            });
        ";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cek apakah ini form tambah pengguna
    if (isset($_POST['nama-lengkap'])) {
        // Handle form submission for user
        $namaLengkap = mysqli_real_escape_string($mysqli, $_POST['nama-lengkap'] ?? '');
        $outlet = mysqli_real_escape_string($mysqli, $_POST['outlet'] ?? '');
        $username = mysqli_real_escape_string($mysqli, $_POST['username'] ?? '');
        $password = mysqli_real_escape_string($mysqli, $_POST['password'] ?? '');
        $role = mysqli_real_escape_string($mysqli, $_POST['role'] ?? '');

        // For admin role, set outlet to NULL
        if ($role === 'admin') {
            $outlet = 'NULL'; // Will be inserted as NULL in database
        }

        if (!empty($namaLengkap) && !empty($username) && !empty($password) && !empty($role)) {
            // Versi ada hashnya
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO tb_user (nama, id_outlet, username, password, role) 
                      VALUES ('$namaLengkap', ".($role === 'admin' ? 'NULL' : "'$outlet'").", '$username', '$hashedPassword', '$role')";

            // Versi langsung tanpa hash
            // $query = "INSERT INTO tb_user (nama, id_outlet, username, password, role) 
            //           VALUES ('$namaLengkap', ".($role === 'admin' ? 'NULL' : "'$outlet'").", '$username', '$password', '$role')";
            
            if (mysqli_query($mysqli, $query)) {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = 'Pengguna berhasil ditambahkan!';
                header("Location: menu_user.php?status=success");
                exit();
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Gagal menambahkan pengguna: ' . mysqli_error($mysqli);
                header("Location: menu_user.php?status=error");
                exit();
            }
        } else {
            $_SESSION['status'] = 'warning';
            $_SESSION['message'] = 'Semua field wajib diisi kecuali outlet untuk admin.';
            header("Location: menu_user.php");
            exit();
        }
    }
    // Cek apakah ini form edit pengguna
    elseif (isset($_POST['nama-lengkap-baru'])) {
        // Handle form edit submission
        $idUser = $_POST['id-user'] ?? '';
        $namaBaru = $_POST['nama-lengkap-baru'] ?? '';
        $outletBaru = $_POST['outlet-baru'] ?? '';
        $usernameBaru = $_POST['username-baru'] ?? '';
        $roleBaru = $_POST['role-baru'] ?? '';
        
        // For admin role, set outlet to NULL
        if ($roleBaru === 'admin') {
            $outletBaru = 'NULL'; // Will be updated as NULL in database
        }
        
        if (!empty($idUser) && !empty($namaBaru) && !empty($usernameBaru) && !empty($roleBaru)) {
            $stmt = $mysqli->prepare("UPDATE tb_user SET nama = ?, id_outlet = ?, username = ?, role = ? WHERE id = ?");
            $stmt->bind_param("sissi", $namaBaru, ($roleBaru === 'admin' ? NULL : $outletBaru), $usernameBaru, $roleBaru, $idUser);
            
            if ($stmt->execute()) {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = 'Data pengguna berhasil diperbarui!';
                header("Location: menu_user.php?status=success");
                exit();
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Gagal memperbarui pengguna: ' . $mysqli->error;
                header("Location: menu_user.php?status=error");
                exit();
            }
        } else {
            $_SESSION['status'] = 'warning';
            $_SESSION['message'] = 'Semua field wajib diisi kecuali outlet untuk admin.';
            header("Location: menu_user.php");
            exit();
        }
    }
    // Cek apakah ini form hapus pengguna
    elseif (isset($_POST['nama-lengkap']) && isset($_POST['username'])) {
        // Handle delete form submission
        $userId = $_POST['nama-lengkap']; // Ini sebenarnya ID user
        $username = $_POST['username'];
        
        $query = "DELETE FROM tb_user WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $userId);
        
        if ($stmt->execute()) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Pengguna berhasil dihapus!';
            header("Location: menu_user.php?status=success");
            exit();
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Gagal menghapus pengguna: ' . $mysqli->error;
            header("Location: menu_user.php?status=error");
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
            " . ($_SESSION['status'] == 'success' ? "window.location.href='menu_user.php';" : "") . "
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
    <title>Menu Pengguna</title>
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
    <button id="btnTambah" class="bg-[#C7D9F9] border border-black rounded-md px-4 py-2 text-[14px] font-normal w-full sm:w-auto">Tambah User</button>
    <button id="btnEdit" class="bg-[#FAF5F0] border border-black rounded-md px-4 py-2 text-[14px] font-normal w-full sm:w-auto">Edit User</button>
    <button id="btnHapus" class="bg-[#FAF5F0] border border-black rounded-md px-4 py-2 text-[14px] font-normal w-full sm:w-auto">Hapus User</button>
    <button id="btnInfo" class="bg-[#FAF5F0] border border-black rounded-md px-4 py-2 text-[14px] font-normal w-full sm:w-auto">Info User</button>
</div>
                </div>

                <div id="form-container">
                    <!-- Form Tambah Pengguna Default -->
                    <form class="flex flex-col gap-3 text-sm" method="POST" id="formTambah">
                        <label for="nama-lengkap" class="text-[13px] font-semibold">Nama Lengkap</label>
                        <input id="nama-lengkap" name="nama-lengkap" type="text" autocomplete="name" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

                        <div id="outlet-container" class="flex flex-col gap-1">
                            <label for="outlet" class="text-[13px] font-semibold">Outlet</label>
                            <select id="outlet" name="outlet" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm appearance-none bg-[url('data:image/svg+xml;utf8,<svg fill=\'black\' height=\'6\' viewBox=\'0 0 10 6\' width=\'10\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M1 1L5 5L9 1\' stroke=\'black\' stroke-width=\'1.5\'/></svg>')] bg-no-repeat bg-right-3 bg-center cursor-pointer">
                                <option value="" disabled selected>Pilih Outlet</option>
                                <?php
                                $query = "SELECT id, nama FROM tb_outlet";
                                $result = mysqli_query($mysqli, $query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='".$row['id']."'>".$row['nama']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <p id="admin-note" class="text-xs text-gray-500 hidden">*Outlet tidak diperlukan untuk role Admin</p>

                        <label for="username" class="text-[13px] font-semibold">Username</label>
                        <input id="username" name="username" type="text" autocomplete="username" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

                        <label for="password" class="text-[13px] font-semibold">Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

                        <label for="role" class="text-[13px] font-semibold">Role</label>
                        <select id="role" name="role" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm appearance-none bg-[url('data:image/svg+xml;utf8,<svg fill=\'black\' height=\'6\' viewBox=\'0 0 10 6\' width=\'10\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M1 1L5 5L9 1\' stroke=\'black\' stroke-width=\'1.5\'/></svg>')] bg-no-repeat bg-right-3 bg-center cursor-pointer">
                            <option value="" disabled selected>Pilih Role</option>
                            <?php foreach ($roleOptions as $role): ?>
                                <option value="<?= $role ?>"><?= $role ?></option>
                            <?php endforeach; ?>
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
            } else if (id === 'btnInfo') {
                button.classList.add('bg-[#C7D9F9]');
                button.classList.remove('bg-[#FAF5F0]', 'bg-[#d81e2a]', 'text-white');
            } else {
                button.classList.add('bg-[#C7D9F9]');
                button.classList.remove('bg-[#FAF5F0]', 'bg-[#d81e2a]', 'text-white');
            }
        } else {
            button.classList.add('bg-[#FAF5F0]');
            button.classList.remove('bg-[#C7D9F9]', 'bg-[#d81e2a]', 'text-white');
        }
    });
}

    // Event listener untuk perubahan role
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const outletContainer = document.getElementById('outlet-container');
        const adminNote = document.getElementById('admin-note');
        const outletSelect = document.getElementById('outlet');

        if (roleSelect) {
            roleSelect.addEventListener('change', function() {
                if (this.value === 'admin') {
                    outletContainer.classList.add('hidden');
                    adminNote.classList.remove('hidden');
                    outletSelect.removeAttribute('required');
                } else {
                    outletContainer.classList.remove('hidden');
                    adminNote.classList.add('hidden');
                    outletSelect.setAttribute('required', '');
                }
            });
        }
    });

    // Event listener untuk tombol "Edit"
    document.getElementById('btnEdit').addEventListener('click', function() {
        const container = document.getElementById('form-container');
        container.innerHTML = ''; // Kosongkan container
        
        // Load form edit pengguna
        fetch('edit_user.php')
        .then(response => response.text())
        .then(data => {
            container.innerHTML = data;
            
            // Load jQuery jika belum ada
            if (typeof jQuery == 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
                script.onload = function() {
                    // Setelah jQuery dimuat, load script edit_user.js
                    const editScript = document.createElement('script');
                    editScript.src = '../assets/js/edit_user.js';
                    document.body.appendChild(editScript);
                };
                document.head.appendChild(script);
            } else {
                // Jika jQuery sudah ada, langsung load script edit_user.js
                const editScript = document.createElement('script');
                editScript.src = '../assets/js/edit_user.js';
                document.body.appendChild(editScript);
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
            <label for="nama-lengkap" class="text-[13px] font-semibold">Nama Lengkap</label>
            <input id="nama-lengkap" name="nama-lengkap" type="text" autocomplete="name" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

           <div id="outlet-container" class="flex flex-col gap-1">
                <label for="outlet" class="text-[13px] font-semibold">Outlet</label>
                <select id="outlet" name="outlet" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm appearance-none bg-[url('data:image/svg+xml;utf8,<svg fill=\'black\' height=\'6\' viewBox=\'0 0 10 6\' width=\'10\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M1 1L5 5L9 1\' stroke=\'black\' stroke-width=\'1.5\'/></svg>')] bg-no-repeat bg-right-3 bg-center cursor-pointer">
                    <option value="" disabled selected>Pilih Outlet</option>
                    <?php
                    $query = "SELECT id, nama FROM tb_outlet";
                    $result = mysqli_query($mysqli, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='".$row['id']."'>".$row['nama']."</option>";
                    }
                    ?>
                </select>
            </div>
            <p id="admin-note" class="text-xs text-gray-500 hidden">*Outlet tidak diperlukan untuk role Admin</p>

            <label for="username" class="text-[13px] font-semibold">Username</label>
            <input id="username" name="username" type="text" autocomplete="username" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

            <label for="password" class="text-[13px] font-semibold">Password</label>
            <input id="password" name="password" type="password" autocomplete="new-password" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" required />

            <label for="role" class="text-[13px] font-semibold">Role</label>
            <select id="role" name="role" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm appearance-none bg-[url('data:image/svg+xml;utf8,<svg fill=\'black\' height=\'6\' viewBox=\'0 0 10 6\' width=\'10\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M1 1L5 5L9 1\' stroke=\'black\' stroke-width=\'1.5\'/></svg>')] bg-no-repeat bg-right-3 bg-center cursor-pointer">
                <option value="" disabled selected>Pilih Role</option>
                <?php foreach ($roleOptions as $role): ?>
                    <option value="<?= $role ?>"><?= $role ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="bg-[#D1E7CD] border border-black rounded-md px-4 py-1.5 text-sm font-normal w-max mt-3">Simpan</button>
        </form>
        `;

        // Add event listener for role change in the newly created form
        const roleSelect = document.getElementById('role');
        const outletContainer = document.getElementById('outlet-container');
        const adminNote = document.getElementById('admin-note');
        const outletSelect = document.getElementById('outlet');

        if (roleSelect) {
            roleSelect.addEventListener('change', function() {
                if (this.value === 'admin') {
                    outletContainer.classList.add('hidden');
                    adminNote.classList.remove('hidden');
                    outletSelect.removeAttribute('required');
                } else {
                    outletContainer.classList.remove('hidden');
                    adminNote.classList.add('hidden');
                    outletSelect.setAttribute('required', '');
                }
            });
        }

        updateButtonStyles('btnTambah');
    });

    document.getElementById('btnInfo').addEventListener('click', function() {
    const container = document.getElementById('form-container');
    container.innerHTML = ''; // Kosongkan container
    
    // Load info pengguna
    fetch('info_user.php')
    .then(response => response.text())
    .then(data => {
        container.innerHTML = data;
    })
    .catch(error => {
        console.error('Error loading info:', error);
        container.innerHTML = '<p class="text-red-500">Gagal memuat informasi pengguna.</p>';
    });

    updateButtonStyles('btnInfo');
});
    // Event listener untuk tombol "Hapus"
    document.getElementById('btnHapus').addEventListener('click', function() {
        const container = document.getElementById('form-container');
        container.innerHTML = '';

        // Query untuk outlet
        const outletOptions = `<?php 
            $query = "SELECT id, nama FROM tb_outlet";
            $result = mysqli_query($mysqli, $query);
            $options = '';
            while ($row = mysqli_fetch_assoc($result)) {
                $options .= "<option value='".$row['id']."'>".$row['nama']."</option>";
            }
            echo $options;
        ?>`;

        container.innerHTML = `
        <section class="bg-white rounded-md p-6 w-full max-w-md">
            <form class="flex flex-col gap-6 text-sm" id="deleteForm" method="post">
                <h2 class="text-lg font-semibold mb-2 text-gray-700">Data Pengguna yang Akan Dihapus</h2>
                
                <div class="flex flex-col gap-3">
                    <label for="outlet" class="text-[13px] font-semibold">Outlet</label>
                    <select id="outlet" name="outlet" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none cursor-pointer" required>
                        <option value="" disabled selected>Pilih Outlet</option>
                        ${outletOptions}
                    </select>
                </div>
                
                <div class="flex flex-col gap-3">
                    <label for="nama-lengkap" class="text-[13px] font-semibold">Nama Lengkap</label>
                    <select id="nama-lengkap" name="nama-lengkap" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none cursor-pointer" required disabled>
                        <option value="" disabled selected>Pilih Outlet terlebih dahulu</option>
                    </select>
                </div>
                
                <div class="flex flex-col gap-3">
                    <label for="username" class="text-[13px] font-semibold">Username</label>
                    <input type="text" id="username" name="username" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" readonly />
                </div>

                <div class="flex justify-start mt-4">
                    <button type="submit" class="bg-[#d81e2a] border border-black rounded-md px-6 py-2 text-sm font-normal hover:bg-red-700 text-white">
                        Hapus Pengguna
                    </button>
                </div>
            </form>
        </section>
        `;

        // Add event listeners for the delete form
        document.getElementById('outlet').addEventListener('change', function() {
            const outletId = this.value;
            if (outletId) {
                fetch('fetch_user_by_outlet.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `outlet_id=${outletId}`
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('nama-lengkap').innerHTML = '<option value="" disabled selected>Pilih user yang akan dihapus</option>' + data;
                    document.getElementById('nama-lengkap').disabled = false;
                    document.getElementById('username').value = '';
                    
                    // Update username when user is selected
                    document.getElementById('nama-lengkap').addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        const username = selectedOption.getAttribute('data-username');
                        document.getElementById('username').value = username || '';
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            } else {
                document.getElementById('nama-lengkap').innerHTML = '<option value="" disabled selected>Pilih Outlet terlebih dahulu</option>';
                document.getElementById('nama-lengkap').disabled = true;
                document.getElementById('username').value = '';
            }
        });

        // Handle form submission
        document.getElementById('deleteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menghapus pengguna ini secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d81e2a',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form
                    const formData = new FormData(this);
                    
                    fetch('menu_user.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.text();
                        }
                        throw new Error('Network response was not ok.');
                    })
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Pengguna berhasil dihapus!',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = 'menu_user.php';
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Gagal menghapus pengguna.',
                            showConfirmButton: true
                        });
                    });
                }
            });
        });

        updateButtonStyles('btnHapus');
    });

    window.onload = function() {
        <?php echo $swal_script; ?>
    }
    </script>
    <script src="../assets/js/edit_user.js"></script>
</body>
</html>