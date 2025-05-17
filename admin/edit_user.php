<?php 
session_start();
include '../database/connect.php';

// Query to fetch all outlet names
$sql = "SELECT id, nama FROM tb_outlet";
$result = mysqli_query($mysqli, $sql);
$outletOptions = "";

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $outletOptions .= "<option value='".$row['id']."'>".$row['nama']."</option>";
    }
} else {
    $outletOptions = "<option value='' disabled>No outlets available</option>";
}

// Define available roles
$roles = ['admin', 'kasir', 'owner'];
?>


<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Menu Pengguna</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>

<body class="bg-gradient-to-b from-[#121B2B] to-[#1F3A5A] min-h-screen flex">
  <main class="flex-1 p-6 md:p-10 max-w-7xl mx-auto w-full">
    <div class="flex flex-col gap-6 text-sm">
      <form class="flex flex-col gap-6 text-sm" id="formEditPengguna" method="POST">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
          <!-- Data Lama -->
          <div class="flex flex-col gap-4">
            <h2 class="text-lg font-semibold mb-2 text-gray-700">Data Lama</h2>
            <div class="flex flex-col gap-3">
              <label for="outlet" class="text-[13px] font-semibold">Outlet</label>
              <select id="outlet" name="outlet" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" aria-required="true" aria-describedby="outlet-desc">
              <option value="" disabled selected>Pilih Outlet</option>
              <?php echo $outletOptions; ?>
              </select>
            </div>
            <div class="flex flex-col gap-3">
              <label for="nama-lengkap" class="text-[13px] font-semibold">Nama Lengkap</label>
              <select id="nama-lengkap" name="nama-lengkap" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" aria-required="true" aria-describedby="nama-lengkap-desc">
                <option value="" disabled selected>Pilih Pengguna</option>
              </select>
            </div>
            <div class="flex flex-col gap-3">
              <label for="username" class="text-[13px] font-semibold">Username</label>
              <input type="text" id="username" name="username" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" aria-required="true" readonly />
            </div>
            <div class="flex flex-col gap-3">
              <label for="role" class="text-[13px] font-semibold">Role</label>
              <input type="text" id="role" name="role" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" aria-required="true" readonly />
            </div>
          </div>

          <!-- Data Baru -->
          <div class="flex flex-col gap-4">
            <h2 class="text-lg font-semibold mb-2 text-gray-700">Data Baru</h2>
            <div class="flex flex-col gap-3">
              <label for="nama-lengkap-baru" class="text-[13px] font-semibold">Nama Lengkap Baru</label>
              <input type="text" id="nama-lengkap-baru" name="nama-lengkap-baru" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" aria-required="true" />
            </div>
            <div class="flex flex-col gap-3">
              <label for="username-baru" class="text-[13px] font-semibold">Username Baru</label>
              <input type="text" id="username-baru" name="username-baru" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" aria-required="true" />
            </div>
            <div class="flex flex-col gap-3">
              <label for="password-baru" class="text-[13px] font-semibold">Password Baru</label>
              <input type="password" id="password-baru" name="password-baru" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" aria-required="true" />
            </div>
            <div class="flex flex-col gap-3">
  <label for="role-baru" class="text-[13px] font-semibold">Role Baru</label>
  <select id="role-baru" name="role-baru" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" aria-required="true">
    <option value="" disabled selected>Pilih Role</option>
    <?php foreach ($roles as $role): ?>
      <option value="<?php echo $role; ?>"><?php echo ucfirst($role); ?></option>
    <?php endforeach; ?>
  </select>
</div>
          </div>
        </div>

        <button type="submit" class="self-start bg-[#D1E7CD] border border-black rounded-md px-6 py-2 text-sm font-normal mt-4 hover:bg-green-300">
          Edit
        </button>
      </form>
    </div>
  </main>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>