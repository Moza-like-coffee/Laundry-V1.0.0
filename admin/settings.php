<?php
session_start();
include '../database/connect.php';
include 'sidebar.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data from session or database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM tb_user WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $new_password = $_POST['newpass'] ?? '';

  // Validate new password
  if (!empty($new_password)) {
      // Hash the password sebelum disimpan
      $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

      // Update password di database
      $update_query = "UPDATE tb_user SET password = ? WHERE id = ?";
      $update_stmt = $mysqli->prepare($update_query);
      $update_stmt->bind_param("si", $hashed_password, $user_id);
      
      if ($update_stmt->execute()) {
          $success_message = "Password berhasil diperbarui!";
      } else {
          $error_message = "Gagal memperbarui password: " . $mysqli->error;
      }
  } else {
      $error_message = "Password baru tidak boleh kosong!";
  }
}

//     // Versi ada ga ada
//     if (!empty($new_password)) {

//         $update_query = "UPDATE tb_user SET password = ? WHERE id = ?";
//         $update_stmt = $mysqli->prepare($update_query);
//         $update_stmt->bind_param("si", $new_password, $user_id);
        
//         if ($update_stmt->execute()) {
//             $success_message = "Password berhasil diperbarui!";
//         } else {
//             $error_message = "Gagal memperbarui password: " . $mysqli->error;
//         }
//     } else {
//         $error_message = "Password baru tidak boleh kosong!";
//     }
// }
?>

<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Settings</title>
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
    <main class="flex-1 flex justify-center items-center p-6 md:p-10 min-h-screen ml-0 sm:ml-[250px]">
      <section class="bg-white rounded-md p-6 w-full max-w-4xl">
        <div class="border-b border-black pb-2 mb-4">
          <button class="bg-[#C7D9F9] border border-black rounded-md px-4 py-2 text-[14px] font-normal">Kelola Akun</button>
        </div>
        <form method="POST" class="flex flex-col gap-4 text-sm"> 
          <label for="nama-lengkap" class="text-[13px] font-semibold">Nama Lengkap</label>
          <input id="nama-lengkap" name="nama-lengkap" type="text" autocomplete="off"
              class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" 
              value="<?php echo htmlspecialchars($user['nama'] ?? ''); ?>" readonly />

          <label for="username" class="text-[13px] font-semibold">Username</label>
          <input id="username" name="username" type="text" autocomplete="off"
              class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" 
              value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" readonly />

          <label for="role" class="text-[13px] font-semibold">Role</label>
          <input id="role" name="role" type="text" autocomplete="off"
              class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" 
              value="<?php echo htmlspecialchars($user['role'] ?? ''); ?>" readonly />

          <label for="newpass" class="text-[13px] font-semibold">Password Baru</label>
          <input id="newpass" name="newpass" type="password" autocomplete="new-password"
              class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none max-w-sm" 
              placeholder="Masukkan password baru" required />

          <button type="submit"
              class="bg-[#D1E7CD] border border-black rounded-md px-4 py-1.5 text-sm font-normal w-max mt-3">Konfirmasi</button>
        </form>
      </section>
    </main>
  </div>

  <!-- SweetAlert Notifications -->
  <script>
    <?php if (isset($success_message)): ?>
      Swal.fire({
        icon: 'success',
        title: 'Sukses!',
        text: '<?php echo $success_message; ?>',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK'
      });
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
      Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '<?php echo $error_message; ?>',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK'
      });
    <?php endif; ?>
  </script>
</body>
</html>