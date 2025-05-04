<?php
include '../database/connect.php';

// Query untuk mengambil semua nama outlet
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
?>

<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Outlet Hapus Page</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
  />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gradient-to-b from-[#0B1526] to-[#1E3A5F] min-h-screen font-inter text-black">
  <div class="flex">
    <section class="bg-white rounded-md p-6 w-full max-w-5xl">
    <form class="flex flex-col gap-6 text-sm" id="deleteForm" method="post" action="delete_outlet_process.php">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
          <!-- Data Outlet yang Akan Dihapus -->
          <div class="flex flex-col gap-4">
            <h2 class="text-lg font-semibold mb-2 text-gray-700">Data Outlet yang Akan Dihapus</h2>
            <div class="flex flex-col gap-3">
              <label for="nama-outlet" class="text-[13px] font-semibold">Nama Outlet</label>
              <select id="nama-outlet" name="nama-outlet" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" required>
                <option value="" disabled selected>Pilih Outlet</option>
                <?php echo $outletOptions; ?>
              </select>
            </div>
            <div class="flex flex-col gap-3">
              <label for="lokasi-outlet" class="text-[13px] font-semibold">Lokasi Outlet</label>
              <input type="text" id="lokasi-outlet" name="lokasi-outlet" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" required readonly />
            </div>
            <div class="flex flex-col gap-3">
              <label for="no-telp" class="text-[13px] font-semibold">No. Telp</label>
              <input type="text" id="no-telp" name="no-telp" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" required readonly />
            </div>
          </div>
        </div> <!-- Tutup div grid -->

        <!-- Tombol Hapus dipindahkan ke bawah form -->
        <div class="flex justify-start mt-4">
          <button type="submit" class="bg-[#d81e2a] border border-black rounded-md px-6 py-2 text-sm font-normal hover:bg-red-700 text-white">
            Hapus Outlet
          </button>
        </div>
      </form>
    </section>
  </div>

  <script>
$(document).ready(function() {
  $('#nama-outlet').change(function() {
    var outletId = $(this).val();

    if (outletId) {
      $.ajax({
        url: 'fetch_outlet_data.php',
        type: 'GET',
        data: { id: outletId },
        success: function(response) {
          console.log(response);
          try {
            var data = JSON.parse(response);
            console.log(data);
            if (data && data.alamat && data.tlp) {
              $('#lokasi-outlet').val(data.alamat);
              $('#no-telp').val(data.tlp);
            } else {
              console.error("Data tidak valid:", data);
            }
          } catch (e) {
            console.error("Error parsing JSON:", e);
          }
        },
        error: function(xhr, status, error) {
          console.error("AJAX Error:", error);
        }
      });
    } else {
      $('#lokasi-outlet').val('');
      $('#no-telp').val('');
    }
  });
});
  </script>
  
</body>
</html> 