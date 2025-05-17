<?php
include '../database/connect.php';

// Query untuk outlet
$sqlOutlet = "SELECT id, nama FROM tb_outlet";
$resultOutlet = mysqli_query($mysqli, $sqlOutlet);
$outletOptions = "";

if (mysqli_num_rows($resultOutlet) > 0) {
    while ($row = mysqli_fetch_assoc($resultOutlet)) {
        $outletOptions .= "<option value='".$row['id']."'>".$row['nama']."</option>";
    }
} else {
    $outletOptions = "<option value='' disabled>No outlets available</option>";
}
?>

<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Menu Pengguna - Hapus</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gradient-to-b from-[#0B1526] to-[#1E3A5F] min-h-screen font-inter text-black">
    <section class="bg-white rounded-md p-6 w-full max-w-md">
      <form class="flex flex-col gap-6 text-sm" id="deleteForm" method="post" action="delete_user_process.php">
        <h2 class="text-lg font-semibold mb-2 text-gray-700">Data Pengguna yang Akan Dihapus</h2>
        
        <div class="flex flex-col gap-3">
          <label for="outlet" class="text-[13px] font-semibold">Outlet</label>
          <select id="outlet" name="outlet" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none cursor-pointer" required onchange="getUsersByOutlet()">
            <option value="" disabled selected>Pilih Outlet</option>
            <?php echo $outletOptions; ?>
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

<script>
  function getUsersByOutlet() {
    const outletId = $('#outlet').val();
    if (outletId) {
        $.ajax({
            url: 'fetch_user_by_outlet.php',
            type: 'POST',
            data: { outlet_id: outletId },
            success: function(response) {
                // Tambahkan opsi default "Pilih user yang akan dihapus" di awal
                $('#nama-lengkap').html('<option value="" disabled selected>Pilih user yang akan dihapus</option>' + response);
                $('#nama-lengkap').prop('disabled', false);
                $('#username').val('');
                
                // Langsung update username saat nama dipilih
                $('#nama-lengkap').off('change').on('change', function() {
                    const username = $(this).find('option:selected').data('username');
                    $('#username').val(username || '');
                });
            },
            error: function() {
                alert('Error loading users');
            }
        });
    } else {
        $('#nama-lengkap').html('<option value="" disabled selected>Pilih Outlet terlebih dahulu</option>');
        $('#nama-lengkap').prop('disabled', true);
        $('#username').val('');
    }
}

$(document).ready(function() {
    // Tangani submit form
    $('#deleteForm').on('submit', function(e) {
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
          // Jika user mengkonfirmasi, submit form
          document.getElementById('deleteForm').submit();
        }
      });
    });

    // Tangani pesan dari proses delete (jika ada)
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const message = urlParams.get('message');

    if (status && message) {
      Swal.fire({
        icon: status === 'success' ? 'success' : 'error',
        title: status === 'success' ? 'Berhasil' : 'Error',
        text: decodeURIComponent(message),
        confirmButtonColor: '#d81e2a'
      });

      // Hapus parameter dari URL tanpa reload
      history.replaceState(null, null, window.location.pathname);
    }
  });
</script>
</body>
</html>