<?php
require '../database/connect.php';

// Get outlet names
$outletOptions = '';
$result = $mysqli->query("SELECT id, nama FROM tb_outlet");
while ($row = $result->fetch_assoc()) {
    $outletOptions .= "<option value='{$row['id']}'>{$row['nama']}</option>";
}
?>

<div id="hapusProdukForm">
  <form class="flex flex-col gap-6 text-sm" id="deleteForm">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
      <div class="flex flex-col gap-4">
        <h2 class="text-lg font-semibold mb-2 text-gray-700">Data Produk yang Akan Dihapus</h2>
        <div class="flex flex-col gap-3">
          <label for="outlet" class="text-[13px] font-semibold">Outlet</label>
          <select id="outlet" name="outlet" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none">
            <option value="" disabled selected>Pilih Outlet</option>
            <?= $outletOptions ?>
          </select>
        </div>
        <div class="flex flex-col gap-3">
          <label for="nama-paket" class="text-[13px] font-semibold">Nama Paket</label>
          <select id="nama-paket" name="nama-paket" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none">
            <option value="" disabled selected>Pilih outlet terlebih dahulu</option>
          </select>
        </div>
        <div class="flex flex-col gap-3">
          <label for="jenis-produk" class="text-[13px] font-semibold">Jenis Produk</label>
          <input type="text" id="jenis-produk" name="jenis-produk" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" readonly />
        </div>
        <div class="flex flex-col gap-3">
          <label for="harga" class="text-[13px] font-semibold">Harga</label>
          <input type="text" id="harga" name="harga" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" readonly />
        </div>
      </div>
    </div>

    <div class="flex justify-start mt-4">
      <button type="submit" class="bg-[#d81e2a] border border-black rounded-md px-6 py-2 text-sm font-normal hover:bg-red-700 text-white">
        Hapus
      </button>
    </div>
  </form>
</div>

<script>
// Fungsi untuk menginisialisasi form hapus produk
function initHapusProdukForm() {
  // Pastikan jQuery sudah dimuat
  if (typeof jQuery == 'undefined') {
    const script = document.createElement('script');
    script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
    script.onload = function() {
      setupHapusProdukForm();
    };
    document.head.appendChild(script);
  } else {
    setupHapusProdukForm();
  }
}

// Fungsi untuk setup form hapus produk
function setupHapusProdukForm() {
  $(document).ready(function() {
    // When outlet is selected
    $('#outlet').change(function() {
      const outletId = $(this).val();
      
      if (outletId) {
        // Get packages for selected outlet
        $.ajax({
          url: 'get_packages.php',
          type: 'POST',
          data: { outlet_id: outletId },
          dataType: 'json',
          success: function(response) {
            const packageSelect = $('#nama-paket');
            packageSelect.empty();
            
            if (response.length > 0) {
              packageSelect.append('<option value="" disabled selected>Pilih Paket</option>');
              $.each(response, function(index, package) {
                packageSelect.append(`<option value="${package.id}" data-jenis="${package.jenis}" data-harga="${package.harga}">${package.nama_paket}</option>`);
              });
            } else {
              packageSelect.append('<option value="" disabled>Outlet ini belum memiliki paket</option>');
            }
          },
          error: function() {
            $('#nama-paket').html('<option value="" disabled>Error loading packages</option>');
          }
        });
      } else {
        $('#nama-paket').html('<option value="" disabled selected>Pilih outlet terlebih dahulu</option>');
      }
    });

    // When package is selected
    $('#nama-paket').change(function() {
      const selectedOption = $(this).find('option:selected');
      $('#jenis-produk').val(selectedOption.data('jenis'));
      $('#harga').val(selectedOption.data('harga'));
    });

    // Handle form submission
    $('#deleteForm').submit(function(e) {
      e.preventDefault();
      
      // Validate form
      if ($('#nama-paket').val() === '') {
        Swal.fire({
          icon: 'warning',
          title: 'Peringatan',
          text: 'Silakan pilih paket yang akan dihapus!'
        });
        return;
      }
      
      // Send data via AJAX
      $.ajax({
        url: 'delete_produk_process.php',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Paket berhasil dihapus!',
            showConfirmButton: false,
            timer: 2000
          }).then(() => {
            window.location.reload();
          });
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Terjadi kesalahan saat menghapus paket.'
          });
        }
      });
    });
  });
}

// Panggil fungsi inisialisasi
initHapusProdukForm();
</script>