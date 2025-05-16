<?php 
session_start();

include '../database/connect.php';

$result_outlet = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tb_outlet");
$outlet = mysqli_fetch_assoc($result_outlet)['total'];

$result_member = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tb_member");
$member = mysqli_fetch_assoc($result_member)['total'];

$result_karyawan = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM tb_user WHERE role = 'kasir'");
$karyawan = mysqli_fetch_assoc($result_karyawan)['total'];
include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>
<body class="bg-gradient-to-b from-[#121c2a] to-[#243a57] min-h-screen flex">



     <div id="mainContent" class="flex-1 flex flex-col p-8 ml-0 sm:ml-80 space-y-8">


     <section class="max-w-4xl rounded-lg p-6 text-center text-white bg-gradient-to-r from-[#4b3399] to-[#9aa9f9] mt-10 sm:mt-0">
      <p class="text-lg font-semibold">Halo <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</p>
      <p class="text-sm mt-1">Anda login sebagai <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong></p>
    </section>



    <section class="max-w-4xl w-full flex flex-col sm:flex-row gap-6">
  <article class="flex-1 bg-gradient-to-r from-[#7f7ed2] to-[#b38adf] rounded-md p-6 flex justify-between items-center text-white">
    <div>
      <h2 class="text-2xl font-semibold mb-3">Total Outlet</h2>
      <div class="flex items-center gap-3">
        <i class="fas fa-shopping-cart text-5xl"></i>
        <div class="text-2xl font-medium"><?php echo $outlet; ?></div>
      </div>
    </div>
  </article>

  <article class="flex-1 bg-gradient-to-r from-[#7f7ed2] to-[#b38adf] rounded-md p-6 flex justify-between items-center text-white">
    <div>
      <h2 class="text-2xl font-semibold mb-3">Total Pelanggan</h2>
      <div class="flex items-center gap-3">
        <i class="far fa-user text-5xl"></i>
        <div class="text-2xl font-medium"><?php echo $member; ?></div>
      </div>
    </div>
  </article>

  <article class="flex-1 bg-gradient-to-r from-[#7f7ed2] to-[#b38adf] rounded-md p-6 flex justify-between items-center text-white">
    <div>
      <h2 class="text-2xl font-semibold mb-3">Total Karyawan</h2>
      <div class="flex items-center gap-3">
        <i class="fas fa-user-tie text-5xl"></i>
        <div class="text-2xl font-medium"><?php echo $karyawan; ?></div>
      </div>
    </div>
  </article>
</section>


  </div>
</body>
</html>

