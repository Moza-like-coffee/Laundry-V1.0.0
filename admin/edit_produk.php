<?php
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
?>

<div class="flex flex-col gap-6 text-sm">
  <form class="flex flex-col gap-6 text-sm" id="formEditProduk" method="POST">
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
          <label for="nama-paket" class="text-[13px] font-semibold">Nama Paket</label>
          <select id="nama-paket" name="nama-paket" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" aria-required="true" aria-describedby="nama-paket-desc">
            <option value="" disabled selected>Pilih outlet terlebih dahulu</option>
          </select>
        </div>
        <div class="flex flex-col gap-3">
          <label for="jenis-produk-1" class="text-[13px] font-semibold">Jenis Produk</label>
          <input type="text" id="jenis-produk-1" name="jenis-produk-1" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" aria-required="true" readonly />
        </div>
        <div class="flex flex-col gap-3">
          <label for="harga" class="text-[13px] font-semibold">Harga</label>
          <input type="text" id="harga" name="harga" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" aria-required="true" readonly />
        </div>
      </div>

      <!-- Data Baru -->
      <div class="flex flex-col gap-4">
        <h2 class="text-lg font-semibold mb-2 text-gray-700">Data Baru</h2>
        <div class="flex flex-col gap-3">
          <label for="nama-paket-baru" class="text-[13px] font-semibold">Nama Paket Baru</label>
          <input type="text" id="nama-paket-baru" name="nama-paket-baru" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" aria-required="true" />
        </div>
        <div class="flex flex-col gap-3">
          <label for="jenis-produk-2" class="text-[13px] font-semibold">Jenis Produk</label>
          <select id="jenis-produk-2" name="jenis-produk-2" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" aria-required="true">
            <option value="" disabled selected>Pilih Jenis</option>
            <option value="kiloan">Kiloan</option>
            <option value="selimut">Selimut</option>
            <option value="bed cover">Bed Cover</option>
            <option value="kaos">Kaos</option>
            <option value="lain">Lain-lain</option>
          </select>
        </div>
        <div class="flex flex-col gap-3">
          <label for="harga-baru" class="text-[13px] font-semibold">Harga Baru</label>
          <input type="text" id="harga-baru" name="harga-baru" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" aria-required="true" />
        </div>
      </div>
    </div>

    <button type="submit" class="self-start bg-[#D1E7CD] border border-black rounded-md px-6 py-2 text-sm font-normal mt-4 hover:bg-green-300">
      Edit
    </button>
  </form>
</div>

<script>
// Fungsi untuk inisialisasi form edit produk
function initEditProdukForm() {
  // Ketika outlet dipilih
  $('#outlet').change(function() {
    const outletId = $(this).val();
    
    if (outletId) {
      // Ambil data paket untuk outlet yang dipilih
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

  // Ketika paket dipilih
  $('#nama-paket').change(function() {
    const selectedOption = $(this).find('option:selected');
    $('#jenis-produk-1').val(selectedOption.data('jenis'));
    $('#harga').val(selectedOption.data('harga'));
  });

  // Handle submit form edit
  $('#formEditProduk').submit(function(e) {
    e.preventDefault();
    
    // Validasi form
    if ($('#nama-paket').val() === '' || $('#nama-paket-baru').val() === '' || 
        $('#jenis-produk-2').val() === '' || $('#harga-baru').val() === '') {
      Swal.fire({
        icon: 'warning',
        title: 'Peringatan',
        text: 'Semua field harus diisi!'
      });
      return;
    }
    
    // Kirim data via AJAX
    $.ajax({
      url: 'save_edit_produk.php',
      type: 'POST',
      data: $(this).serialize(),
      success: function(response) {
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Data produk berhasil diperbarui!',
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
          text: 'Terjadi kesalahan saat memperbarui data produk.'
        });
      }
    });
  });
}

// Panggil fungsi inisialisasi saat dokumen siap
$(document).ready(function() {
  initEditProdukForm();
});
</script>