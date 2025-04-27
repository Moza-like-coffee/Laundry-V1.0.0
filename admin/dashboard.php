<?php 
session_start();
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
  
  <?php include 'sidebar.php'; ?>

  <!-- Main content -->
     <div id="mainContent" class="flex-1 flex flex-col p-8 ml-0 sm:ml-80 space-y-8">


     <section class="max-w-4xl rounded-lg p-6 text-center text-white bg-gradient-to-r from-[#4b3399] to-[#9aa9f9] mt-10 sm:mt-0">
      <p class="text-lg font-semibold">Halo <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</p>
      <p class="text-sm mt-1">Anda login sebagai <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong></p>
    </section>
  </div>
</body>
</html>
