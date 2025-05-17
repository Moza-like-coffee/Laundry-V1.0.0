<?php
session_start();
include '../database/connect.php';
include 'sidebar.php';
error_log("Outlet ID in session: " . ($_SESSION['outlet_id'] ?? 'null'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Transaction Page</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
  /* Make the entire date input clickable */
  input[type="date"] {
    position: relative;
  }
  input[type="date"]::-webkit-calendar-picker-indicator {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: auto;
    height: auto;
    color: transparent;
    background: transparent;
  }
  input[type="date"]::before {
    content: '';
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #555;
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    content: '\f073';
  }
  </style>
</head>
<body class="bg-gradient-to-b from-[#0B1526] to-[#1E3A5F] min-h-screen font-inter text-black">
  <div class="flex">
    <main class="flex-1 flex flex-col items-center p-6 md:p-10 min-h-screen ml-0 sm:ml-[250px] space-y-6">
      <!-- Transaction Form Section -->
      <section class="bg-white rounded-md p-6 w-full max-w-4xl">
        <div class="border-b border-black pb-2 mb-4">
          <div class="flex flex-wrap gap-2">
          <div class="flex justify-end mt-2">
  <button id="btnExportExcel" class="bg-green-600 hover:bg-green-700 text-white border border-green-700 rounded-md px-4 py-2 text-sm font-normal transition-colors flex items-center gap-2">
    <i class="fas fa-file-excel"></i> Export Excel
  </button>
</div>
            <button id="btnTambah" class="bg-[#C7D9F9] hover:bg-[#B0C9F5] border border-black rounded-md px-4 py-2 text-sm font-normal w-full sm:w-auto transition-colors">Buat Transaksi</button>
            <button id="btnEdit" class="bg-[#FAF5F0] hover:bg-[#F0E6D9] border border-black rounded-md px-4 py-2 text-sm font-normal w-full sm:w-auto transition-colors">Edit Transaksi</button>
          </div>
        </div>

        <div id="form-container">
          <form class="flex flex-col gap-3 text-sm" method="POST" id="formTambah">
            <div class="flex flex-wrap gap-x-6 gap-y-3">
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="nama-lengkap" class="text-xs font-semibold mb-1">Nama Lengkap</label>
                <select id="nama-lengkap" name="nama-lengkap" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none appearance-none bg-[url('data:image/svg+xml;base64,PHN2ZyBmaWxsPSJibGFjayIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiB3aWR0aD0iMjQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTcgMTBsNSA1IDUtNXoiLz48L3N2Zz4=')] bg-no-repeat bg-[right_1rem_center] bg-[length:1rem_1rem]">
                  <option value="" disabled selected>Pilih Member</option>
                </select>
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="diskon" class="text-xs font-semibold mb-1">Diskon</label>
                <input type="number" id="diskon" name="diskon" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" value="0" min="0" onchange="updateTotals()" />
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="tanggal" class="text-xs font-semibold mb-1">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" />
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="tanggal-pembayaran" class="text-xs font-semibold mb-1">Tanggal Pembayaran</label>
                <input type="date" id="tanggal-pembayaran" name="tanggal-pembayaran" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" />
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="estimasi-laundry" class="text-xs font-semibold mb-1">Estimasi Laundry Selesai</label>
                <input type="date" id="estimasi-laundry" name="estimasi-laundry" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" />
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="status-pembayaran" class="text-xs font-semibold mb-1">Status Pembayaran</label>
                <select id="status-pembayaran" name="status-pembayaran" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none appearance-none bg-[url('data:image/svg+xml;base64,PHN2ZyBmaWxsPSJibGFjayIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiB3aWR0aD0iMjQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTcgMTBsNSA1IDUtNXoiLz48L3N2Zz4=')] bg-no-repeat bg-[right_1rem_center] bg-[length:1rem_1rem]">
                  <option value="belum_dibayar" selected>Belum Dibayar</option>
                  <option value="dibayar">Dibayar</option>
                </select>
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="biaya-tambahan" class="text-xs font-semibold mb-1">Biaya Tambahan</label>
                <input type="number" id="biaya-tambahan" name="biaya-tambahan" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" value="0" min="0" onchange="updateTotals()" />
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="status-pesanan" class="text-xs font-semibold mb-1">Status Pesanan</label>
                <select id="status-pesanan" name="status-pesanan" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none appearance-none bg-[url('data:image/svg+xml;base64,PHN2ZyBmaWxsPSJibGFjayIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiB3aWR0aD0iMjQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTcgMTBsNSA1IDUtNXoiLz48L3N2Zz4=')] bg-no-repeat bg-[right_1rem_center] bg-[length:1rem_1rem]">
                  <option value="baru" selected>Baru</option>
                  <option value="proses">Proses</option>
                  <option value="selesai">Selesai</option>
                  <option value="diambil">Diambil</option>
                </select>
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="pajak" class="text-xs font-semibold mb-1">Pajak (%)</label>
                <input type="number" id="pajak" name="pajak" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" value="11" min="0" max="100" readonly />
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="kasir" class="text-xs font-semibold mb-1">Kasir</label>
                <input type="text" id="kasir" name="kasir" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" readonly />
              </div>
            </div>
            
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-lg font-semibold mt-8">Detail Transaksi</h2>
            </div>
            
            <div id="detail-form-container">
              <div class="flex flex-wrap gap-x-6 gap-y-3">
                <div class="flex flex-col w-full sm:w-[45%]">
                  <label for="paket" class="text-xs font-semibold mb-1">Pilih Paket</label>
                  <select id="paket" name="paket" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none appearance-none bg-[url('data:image/svg+xml;base64,PHN2ZyBmaWxsPSJibGFjayIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiB3aWR0aD0iMjQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTcgMTBsNSA1IDUtNXoiLz48L3N2Zz4=')] bg-no-repeat bg-[right_1rem_center] bg-[length:1rem_1rem]">
                    <option value="" disabled selected>Loading paket...</option>
                  </select>
                </div>
                <div class="flex flex-col w-full sm:w-[45%]">
                  <label for="qty" class="text-xs font-semibold mb-1">Qty</label>
                  <input type="number" id="qty" name="qty" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" min="1" value="1" />
                </div>
                <div class="flex flex-col w-full">
                  <label for="keterangan" class="text-xs font-semibold mb-1">Keterangan tambahan</label>
                  <textarea id="keterangan" name="keterangan" class="rounded-md bg-gray-300 px-3 py-2 text-sm outline-none h-24 resize-none"></textarea>
                </div>
              </div>
              <div class="flex justify-start mt-4">
                <button type="button" id="btnTambahDetail" class="bg-blue-200 hover:bg-blue-300 border border-blue-700 rounded-md px-4 py-2 text-sm font-normal text-black transition-colors mr-2">
                  Tambah Detail
                </button>
                <button type="submit" class="bg-green-200 hover:bg-green-300 border border-green-700 rounded-md px-4 py-2 text-sm font-normal text-black transition-colors">Simpan Transaksi</button>
              </div>
            </div>

            <!-- Added Transaction Details Table -->
            <div class="mt-6">
              <h3 class="text-md font-semibold mb-2">Detail Transaksi yang Ditambahkan</h3>
              <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                  <thead>
                    <tr class="border-b border-gray-300">
                      <th class="text-left py-2 px-1">Paket</th>
                      <th class="text-left py-2 px-1">Harga</th>
                      <th class="text-left py-2 px-1">Qty</th>
                      <th class="text-left py-2 px-1">Subtotal</th>
                      <th class="text-left py-2 px-1">Aksi</th>
                    </tr>
                  </thead>
                  <tbody id="details-table-body">
                    <!-- Details will be added here dynamically -->
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Totals Section -->
            <div class="mt-6 border-t border-gray-300 pt-4">
              <div class="flex justify-between">
                <span>Subtotal:</span>
                <span id="display-subtotal">Rp0</span>
              </div>
              <div class="flex justify-between">
                <span>Diskon:</span>
                <span id="display-diskon">- Rp0</span>
              </div>
              <div class="flex justify-between">
                <span>Biaya Tambahan:</span>
                <span id="display-biaya-tambahan">+ Rp0</span>
              </div>
              <div class="flex justify-between">
                <span>Pajak (11%):</span>
                <span id="display-pajak">+ Rp0</span>
              </div>
              <div class="flex justify-between font-bold mt-2">
                <span>Total:</span>
                <span id="display-total">Rp0</span>
              </div>
            </div>
          </form>
        </div>
      </section>
    </main>
  </div>

  <script>
  // Global variables
  let transactionDetails = [];
  let detailCounter = 0;
  
  // Initialize page
  document.addEventListener('DOMContentLoaded', function() {
    // Set default values
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal').value = today;
    document.getElementById('kasir').value = '<?php echo $_SESSION['nama'] ?? "Kasir"; ?>';
    
    // Load initial data
    loadMembers();
    loadPackages();
    
    // Button event listeners
    document.getElementById('btnTambah').addEventListener('click', showAddForm);
    document.getElementById('btnTambahDetail').addEventListener('click', addDetailRow);
    
    // Form submission
    document.getElementById('formTambah').addEventListener('submit', function(e) {
      e.preventDefault();
      showConfirmation();
    });
    
    // Show notifications if any
    <?php if (isset($_GET['success'])): ?>
      showNotification('success', '<?php echo isset($_SESSION['success_message']) ? addslashes($_SESSION['success_message']) : "Transaksi berhasil disimpan"; ?>');
      <?php unset($_SESSION['success_message']); ?>
    <?php elseif (isset($_GET['error'])): ?>
      showNotification('error', '<?php echo isset($_SESSION['error_message']) ? addslashes($_SESSION['error_message']) : "Terjadi kesalahan saat menyimpan transaksi"; ?>');
      <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
  });
  
  // Function to add a new detail row
  function addDetailRow() {
    const paketSelect = document.getElementById('paket');
    const qtyInput = document.getElementById('qty');
    const keteranganInput = document.getElementById('keterangan');
    
    // Only validate if user is trying to add a new detail
    if (paketSelect.value || qtyInput.value) {
        if (!paketSelect.value) {
            showNotification('error', 'Silakan pilih paket');
            return;
        }
        if (!qtyInput.value || parseFloat(qtyInput.value) <= 0) {
            showNotification('error', 'Silakan masukkan jumlah yang valid');
            return;
        }
    } else {
        // No new detail to add
        return;
    }
    
    // Get selected package
    const selectedOption = paketSelect.options[paketSelect.selectedIndex];
    const harga = parseFloat(selectedOption.dataset.harga);
    const qty = parseFloat(qtyInput.value);
    const subtotal = harga * qty;
    
    // Add to transaction details
    const detailId = 'detail-' + detailCounter++;
    transactionDetails.push({
        id: detailId,
        id_paket: paketSelect.value,
        nama_paket: selectedOption.text,
        harga: harga,
        qty: qty,
        keterangan: keteranganInput.value,
        subtotal: subtotal
    });
    
    // Update details table
    updateDetailsTable();
    
    // Reset form inputs
    paketSelect.selectedIndex = 0;
    paketSelect.required = false; // Remove required after first item is added
    qtyInput.value = 1;
    keteranganInput.value = '';
}

  // Function to update the details table
  function updateDetailsTable() {
    const tableBody = document.getElementById('details-table-body');
    tableBody.innerHTML = '';
    
    transactionDetails.forEach(detail => {
      const row = document.createElement('tr');
      row.className = 'border-b border-gray-200';
      row.dataset.id = detail.id;
      
      row.innerHTML = `
        <td class="py-2 px-1">${detail.nama_paket}</td>
        <td class="py-2 px-1">Rp${detail.harga.toLocaleString()}</td>
        <td class="py-2 px-1">${detail.qty}</td>
        <td class="py-2 px-1">Rp${detail.subtotal.toLocaleString()}</td>
        <td class="py-2 px-1">
          <button onclick="removeDetail('${detail.id}')" class="text-red-500 hover:text-red-700">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      `;
      
      tableBody.appendChild(row);
    });
    
    // Update totals
    updateTotals();
  }

  // Function to remove a detail
  function removeDetail(detailId) {
    transactionDetails = transactionDetails.filter(detail => detail.id !== detailId);
    updateDetailsTable();
  }

  // Function to update totals
  function updateTotals() {
    const subtotal = transactionDetails.reduce((sum, detail) => sum + detail.subtotal, 0);
    const diskon = parseFloat(document.getElementById('diskon').value) || 0;
    const biayaTambahan = parseFloat(document.getElementById('biaya-tambahan').value) || 0;
    const pajakPersen = parseFloat(document.getElementById('pajak').value) || 0;
    
    const totalSebelumPajak = subtotal - diskon + biayaTambahan;
    const pajak = totalSebelumPajak * (pajakPersen / 100);
    const totalSetelahPajak = totalSebelumPajak + pajak;
    
    document.getElementById('display-subtotal').textContent = `Rp${subtotal.toLocaleString()}`;
    document.getElementById('display-diskon').textContent = `- Rp${diskon.toLocaleString()}`;
    document.getElementById('display-biaya-tambahan').textContent = `+ Rp${biayaTambahan.toLocaleString()}`;
    document.getElementById('display-pajak').textContent = `+ Rp${pajak.toLocaleString()}`;
    document.getElementById('display-total').textContent = `Rp${totalSetelahPajak.toLocaleString()}`;
  }
  
  // Functions
  function showNotification(type, message) {
    Swal.fire({
      icon: type,
      title: type === 'success' ? 'Sukses' : 'Error',
      text: message,
      confirmButtonText: 'OK'
    }).then(() => {
      window.history.replaceState({}, document.title, window.location.pathname);
    });
  }
  
  function updateButtonStyles(activeButtonId) {
    const buttons = ['btnTambah', 'btnEdit'];
    buttons.forEach(id => {
      const button = document.getElementById(id);
      if (id === activeButtonId) {
        button.classList.add('bg-[#C7D9F9]');
        button.classList.remove('bg-[#FAF5F0]');
      } else {
        button.classList.add('bg-[#FAF5F0]');
        button.classList.remove('bg-[#C7D9F9]');
      }
    });
  }
  
  function getAddFormHTML() {
    return `
          <form class="flex flex-col gap-3 text-sm" method="POST" id="formTambah">
            <div class="flex flex-wrap gap-x-6 gap-y-3">
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="nama-lengkap" class="text-xs font-semibold mb-1">Nama Lengkap</label>
                <select id="nama-lengkap" name="nama-lengkap" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none appearance-none bg-[url('data:image/svg+xml;base64,PHN2ZyBmaWxsPSJibGFjayIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiB3aWR0aD0iMjQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTcgMTBsNSA1IDUtNXoiLz48L3N2Zz4=')] bg-no-repeat bg-[right_1rem_center] bg-[length:1rem_1rem]">
                  <option value="" disabled selected>Pilih Member</option>
                </select>
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="diskon" class="text-xs font-semibold mb-1">Diskon</label>
                <input type="number" id="diskon" name="diskon" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" value="0" min="0" onchange="updateTotals()" />
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="tanggal" class="text-xs font-semibold mb-1">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" />
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="tanggal-pembayaran" class="text-xs font-semibold mb-1">Tanggal Pembayaran</label>
                <input type="date" id="tanggal-pembayaran" name="tanggal-pembayaran" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" />
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="estimasi-laundry" class="text-xs font-semibold mb-1">Estimasi Laundry Selesai</label>
                <input type="date" id="estimasi-laundry" name="estimasi-laundry" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" />
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="status-pembayaran" class="text-xs font-semibold mb-1">Status Pembayaran</label>
                <select id="status-pembayaran" name="status-pembayaran" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none appearance-none bg-[url('data:image/svg+xml;base64,PHN2ZyBmaWxsPSJibGFjayIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiB3aWR0aD0iMjQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTcgMTBsNSA1IDUtNXoiLz48L3N2Zz4=')] bg-no-repeat bg-[right_1rem_center] bg-[length:1rem_1rem]">
                  <option value="belum_dibayar" selected>Belum Dibayar</option>
                  <option value="dibayar">Dibayar</option>
                </select>
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="biaya-tambahan" class="text-xs font-semibold mb-1">Biaya Tambahan</label>
                <input type="number" id="biaya-tambahan" name="biaya-tambahan" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" value="0" min="0" onchange="updateTotals()" />
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="status-pesanan" class="text-xs font-semibold mb-1">Status Pesanan</label>
                <select id="status-pesanan" name="status-pesanan" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none appearance-none bg-[url('data:image/svg+xml;base64,PHN2ZyBmaWxsPSJibGFjayIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiB3aWR0aD0iMjQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTcgMTBsNSA1IDUtNXoiLz48L3N2Zz4=')] bg-no-repeat bg-[right_1rem_center] bg-[length:1rem_1rem]">
                  <option value="baru" selected>Baru</option>
                  <option value="proses">Proses</option>
                  <option value="selesai">Selesai</option>
                  <option value="diambil">Diambil</option>
                </select>
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="pajak" class="text-xs font-semibold mb-1">Pajak (%)</label>
                <input type="number" id="pajak" name="pajak" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" value="11" min="0" max="100" readonly />
              </div>
              <div class="flex flex-col w-full sm:w-[45%]">
                <label for="kasir" class="text-xs font-semibold mb-1">Kasir</label>
                <input type="text" id="kasir" name="kasir" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" readonly />
              </div>
            </div>
            
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-lg font-semibold mt-8">Detail Transaksi</h2>
            </div>
            
            <div id="detail-form-container">
              <div class="flex flex-wrap gap-x-6 gap-y-3">
                <div class="flex flex-col w-full sm:w-[45%]">
                  <label for="paket" class="text-xs font-semibold mb-1">Pilih Paket</label>
                  <select id="paket" name="paket" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none appearance-none bg-[url('data:image/svg+xml;base64,PHN2ZyBmaWxsPSJibGFjayIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiB3aWR0aD0iMjQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTcgMTBsNSA1IDUtNXoiLz48L3N2Zz4=')] bg-no-repeat bg-[right_1rem_center] bg-[length:1rem_1rem]">
                    <option value="" disabled selected>Loading paket...</option>
                  </select>
                </div>
                <div class="flex flex-col w-full sm:w-[45%]">
                  <label for="qty" class="text-xs font-semibold mb-1">Qty</label>
                  <input type="number" id="qty" name="qty" required class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none" min="1" value="1" />
                </div>
                <div class="flex flex-col w-full">
                  <label for="keterangan" class="text-xs font-semibold mb-1">Keterangan tambahan</label>
                  <textarea id="keterangan" name="keterangan" class="rounded-md bg-gray-300 px-3 py-2 text-sm outline-none h-24 resize-none"></textarea>
                </div>
              </div>
              <div class="flex justify-start mt-4">
                <button type="button" id="btnTambahDetail" class="bg-blue-200 hover:bg-blue-300 border border-blue-700 rounded-md px-4 py-2 text-sm font-normal text-black transition-colors mr-2">
                  Tambah Detail
                </button>
                <button type="submit" class="bg-green-200 hover:bg-green-300 border border-green-700 rounded-md px-4 py-2 text-sm font-normal text-black transition-colors">Simpan Transaksi</button>
              </div>
            </div>

            <!-- Added Transaction Details Table -->
            <div class="mt-6">
              <h3 class="text-md font-semibold mb-2">Detail Transaksi yang Ditambahkan</h3>
              <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                  <thead>
                    <tr class="border-b border-gray-300">
                      <th class="text-left py-2 px-1">Paket</th>
                      <th class="text-left py-2 px-1">Harga</th>
                      <th class="text-left py-2 px-1">Qty</th>
                      <th class="text-left py-2 px-1">Subtotal</th>
                      <th class="text-left py-2 px-1">Aksi</th>
                    </tr>
                  </thead>
                  <tbody id="details-table-body">
                    <!-- Details will be added here dynamically -->
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Totals Section -->
            <div class="mt-6 border-t border-gray-300 pt-4">
              <div class="flex justify-between">
                <span>Subtotal:</span>
                <span id="display-subtotal">Rp0</span>
              </div>
              <div class="flex justify-between">
                <span>Diskon:</span>
                <span id="display-diskon">- Rp0</span>
              </div>
              <div class="flex justify-between">
                <span>Biaya Tambahan:</span>
                <span id="display-biaya-tambahan">+ Rp0</span>
              </div>
              <div class="flex justify-between">
                <span>Pajak (11%):</span>
                <span id="display-pajak">+ Rp0</span>
              </div>
              <div class="flex justify-between font-bold mt-2">
                <span>Total:</span>
                <span id="display-total">Rp0</span>
              </div>
            </div>
          </form>
    `;
  }
  
  function showAddForm() {
    document.getElementById('form-container').innerHTML = getAddFormHTML();
    updateButtonStyles('btnTambah');
    
    // Load data yang diperlukan untuk form tambah
    loadMembers();
    loadPackages();
    
    // Set default values
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal').value = today;
    document.getElementById('kasir').value = '<?php echo $_SESSION['nama'] ?? "Kasir"; ?>';
    
    // Reset transaction details
    transactionDetails = [];
    detailCounter = 0;
    updateDetailsTable();
    
    // Add event listeners
    document.getElementById('btnTambahDetail').addEventListener('click', addDetailRow);
    document.getElementById('formTambah').addEventListener('submit', function(e) {
      e.preventDefault();
      showConfirmation();
    });
  }
  
  function loadMembers() {
    fetch('get_members.php')
      .then(response => response.json())
      .then(data => {
        const select = document.getElementById('nama-lengkap');
        if (select) {
          select.innerHTML = '<option value="" disabled selected>Pilih Member</option>';
          
          data.forEach(member => {
            const option = document.createElement('option');
            option.value = member.id;
            option.textContent = `${member.nama} - ${member.tlp}`;
            select.appendChild(option);
          });
        }
      })
      .catch(error => {
        console.error('Error loading members:', error);
      });
  }
  
  function loadPackages() {
    const outletId = <?php echo $_SESSION['id_outlet'] ?? 'null'; ?>;
    
    if (!outletId) {
      console.error('Outlet ID tidak ditemukan');
      showNotification('error', 'Outlet ID tidak ditemukan. Silakan login ulang.');
      return;
    }
    
    fetch('get_packages.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `outlet_id=${outletId}`
    })
    .then(response => {
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      return response.json();
    })
    .then(data => {
      const select = document.getElementById('paket');
      if (select) {
        select.innerHTML = '<option value="" disabled selected>Pilih Paket</option>';
        
        if (!data || data.length === 0) {
          select.innerHTML = '<option value="" disabled selected>Tidak ada paket tersedia</option>';
          return;
        }
        
        data.forEach(pkg => {
          const option = document.createElement('option');
          option.value = pkg.id;
          option.textContent = `${pkg.nama_paket} (${pkg.jenis}) - Rp${pkg.harga.toLocaleString()}`;
          option.dataset.harga = pkg.harga;
          select.appendChild(option);
        });
      }
    })
    .catch(error => {
      console.error('Error loading packages:', error);
      const select = document.getElementById('paket');
      if (select) {
        select.innerHTML = '<option value="" disabled selected>Gagal memuat paket</option>';
      }
    });
  }
  
  function showConfirmation() {
    // Validate inputs
    if (transactionDetails.length === 0) {
        showNotification('error', 'Silakan tambahkan minimal satu detail transaksi');
        return;
    }
    
    const memberSelect = document.getElementById('nama-lengkap');
    if (!memberSelect.value) {
        showNotification('error', 'Silakan pilih member');
        return;
    }
    
    const selectedMember = memberSelect.options[memberSelect.selectedIndex].text;
    
    // Create order details HTML
    const orderDetails = `
      <div class="text-left my-2 p-3 bg-gray-100 rounded-lg">
        <h3 class="font-bold mb-2 text-lg">Rincian Pesanan</h3>
        <p class="mb-1"><strong>Pelanggan:</strong> ${selectedMember}</p>
        <p class="mb-3"><strong>Tanggal:</strong> ${document.getElementById('tanggal').value}</p>
        
        <table class="w-full border-collapse mt-3">
          <thead>
            <tr class="border-b border-gray-300">
              <th class="text-left py-2 px-1">Paket</th>
              <th class="text-left py-2 px-1">Harga</th>
              <th class="text-left py-2 px-1">Qty</th>
              <th class="text-left py-2 px-1">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            ${transactionDetails.map(detail => `
              <tr class="border-b border-gray-200">
                <td class="py-2 px-1">${detail.nama_paket}</td>
                <td class="py-2 px-1">Rp${detail.harga.toLocaleString()}</td>
                <td class="py-2 px-1">${detail.qty}</td>
                <td class="py-2 px-1">Rp${detail.subtotal.toLocaleString()}</td>
              </tr>
            `).join('')}
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="text-right py-2 px-1">Subtotal:</td>
              <td class="py-2 px-1">${document.getElementById('display-subtotal').textContent}</td>
            </tr>
            <tr>
              <td colspan="3" class="text-right py-2 px-1">Diskon:</td>
              <td class="py-2 px-1">${document.getElementById('display-diskon').textContent}</td>
            </tr>
            <tr>
              <td colspan="3" class="text-right py-2 px-1">Biaya Tambahan:</td>
              <td class="py-2 px-1">${document.getElementById('display-biaya-tambahan').textContent}</td>
            </tr>
            <tr>
              <td colspan="3" class="text-right py-2 px-1">Pajak (${document.getElementById('pajak').value}%):</td>
              <td class="py-2 px-1">${document.getElementById('display-pajak').textContent}</td>
            </tr>
            <tr class="border-t border-gray-300">
              <td colspan="3" class="text-right py-2 px-1 font-bold">Total:</td>
              <td class="py-2 px-1 font-bold">${document.getElementById('display-total').textContent}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    `;
    
    // Show confirmation dialog
    Swal.fire({
      title: 'Konfirmasi Pesanan',
      html: orderDetails,
      showCancelButton: true,
      confirmButtonText: 'Konfirmasi',
      cancelButtonText: 'Batal',
      focusConfirm: false,
      customClass: {
        popup: 'text-left'
      }
    }).then((result) => {
      if (result.isConfirmed) {
        // Prepare data for submission
        const formData = new FormData(document.getElementById('formTambah'));
        
        // Add all transaction details
        transactionDetails.forEach((detail, index) => {
          formData.append(`details[${index}][id_paket]`, detail.id_paket);
          formData.append(`details[${index}][qty]`, detail.qty);
          formData.append(`details[${index}][keterangan]`, detail.keterangan);
        });
        
        // Submit the form
        submitTransaction(formData);
      }
    });
  }
  
  function submitTransaction(formData) {
    fetch('process_transaction.php', {
      method: 'POST',
      body: formData
    })
    .then(response => {
      if (response.redirected) {
        window.location.href = response.url;
      } else {
        return response.json();
      }
    })
    .then(data => {
      if (data.success) {
        window.location.href = 'menu_transaksi.php?success=1';
      } else {
        showNotification('error', data.message || 'Terjadi kesalahan saat menyimpan transaksi');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showNotification('error', 'Terjadi kesalahan saat menyimpan transaksi');
    });
  }

  // Event listener untuk tombol "Edit"
  document.getElementById('btnEdit').addEventListener('click', function() {
    const container = document.getElementById('form-container');
    container.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-white"></i></div>';
    
    fetch('edit_transaksi.php')
    .then(response => response.text())
    .then(data => {
        container.innerHTML = data;
        
        // Add improved CSS
        const style = document.createElement('style');
        style.textContent = `
            .invoice-input-container {
                position: relative;
                width: 100%;
            }
            .invoice-validation-icon {
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                pointer-events: none;
            }
            #invoice {
                height: 36px; /* h-9 */
                border-radius: 6px; /* rounded-md */
                background-color: #d1d5db; /* bg-gray-300 */
                padding-left: 12px; /* px-3 */
                padding-right: 32px; /* px-3 + space for icon */
                font-size: 0.875rem; /* text-sm */
                line-height: 1.25rem; /* text-sm */
                outline: none; /* outline-none */
                width: 100%;
            }
        `;
        document.head.appendChild(style);
        
        // Wrap the invoice input in a container if not already wrapped
        const invoiceInput = document.getElementById('invoice');
        if (invoiceInput) {
            // Pastikan class original tetap ada
            invoiceInput.className = 'h-9 rounded-md bg-gray-300 px-3 text-sm outline-none';
            
            // Wrap input if not already wrapped
            if (!invoiceInput.parentNode.classList.contains('invoice-input-container')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'invoice-input-container';
                invoiceInput.parentNode.insertBefore(wrapper, invoiceInput);
                wrapper.appendChild(invoiceInput);
            }
          }
        
        // Add event listener for form submission
        const form = document.getElementById('formEditTransaksi');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const invoice = formData.get('invoice');
                
                fetch('save_edit_transaksi.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Reset form after successful update
                            form.reset();
                            // Remove validation icon when form is reset
                            const icon = document.querySelector('.invoice-validation-icon');
                            if (icon) icon.remove();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message,
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengupdate transaksi',
                        confirmButtonText: 'OK'
                    });
                });
            });
        }
        
        // Add event listener for invoice validation
        if (invoiceInput) {
            invoiceInput.addEventListener('input', function() {
                const invoice = this.value.trim();
                const icon = this.parentNode.querySelector('.invoice-validation-icon');
                
                // Remove icon if input is empty
                if (!invoice && icon) {
                    icon.remove();
                }
            });
            
            invoiceInput.addEventListener('blur', function() {
                const invoice = this.value.trim();
                if (!invoice) return;
                
                // Remove any existing icons
                const existingIcon = this.parentNode.querySelector('.invoice-validation-icon');
                if (existingIcon) existingIcon.remove();
                
                // Show loading indicator
                const icon = document.createElement('span');
                icon.className = 'invoice-validation-icon';
                icon.innerHTML = '<i class="fas fa-spinner fa-spin text-gray-500"></i>';
                this.parentNode.appendChild(icon);
                
                // Check invoice via AJAX
                fetch('check_invoice.php?invoice=' + encodeURIComponent(invoice))
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            icon.innerHTML = '<i class="fas fa-check text-green-500"></i>';
                        } else {
                            icon.innerHTML = '<i class="fas fa-times text-red-500"></i>';
                        }
                    })
                    .catch(error => {
                        icon.innerHTML = '<i class="fas fa-times text-red-500"></i>';
                        console.error('Error checking invoice:', error);
                    });
            });
        }
      })
    .catch(error => {
        console.error('Error loading form:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Gagal memuat form edit transaksi',
            confirmButtonText: 'OK'
        });
    });

    updateButtonStyles('btnEdit');
});

// Fungsi untuk export ke Excel
document.getElementById('btnExportExcel').addEventListener('click', function() {
  // Tampilkan dialog untuk memilih periode
  Swal.fire({
    title: 'Export Data Transaksi',
    html: `
      <div class="text-left">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Tanggal Mulai</label>
          <input type="date" id="exportStartDate" class="w-full p-2 border rounded">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Tanggal Akhir</label>
          <input type="date" id="exportEndDate" class="w-full p-2 border rounded">
        </div>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: 'Export',
    cancelButtonText: 'Batal',
    focusConfirm: false,
    preConfirm: () => {
      const startDate = document.getElementById('exportStartDate').value;
      const endDate = document.getElementById('exportEndDate').value;
      
      if (!startDate || !endDate) {
        Swal.showValidationMessage('Harap isi kedua tanggal');
        return false;
      }
      
      return { startDate, endDate };
    }
  }).then((result) => {
    if (result.isConfirmed) {
      exportToExcel(result.value.startDate, result.value.endDate);
    }
  });
});

// Fungsi untuk melakukan export
function exportToExcel(startDate, endDate) {
  // Tampilkan loading
  Swal.fire({
    title: 'Menyiapkan Excel',
    html: 'Sedang memproses data...',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  // Kirim request ke server
  fetch('export_transaksi_excel.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}&outlet_id=<?php echo $_SESSION['id_outlet'] ?? ''; ?>`
  })
  .then(response => {
    if (!response.ok) throw new Error('Gagal mengunduh file');
    return response.blob();
  })
  .then(blob => {
    // Buat URL untuk blob
    const url = window.URL.createObjectURL(blob);
    
    // Buat link untuk download
    const a = document.createElement('a');
    a.href = url;
    a.download = `Laporan_Transaksi_${startDate}_sd_${endDate}.xlsx`;
    document.body.appendChild(a);
    a.click();
    
    // Bersihkan
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
    
    Swal.close();
  })
  .catch(error => {
    console.error('Error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: 'Terjadi kesalahan saat mengekspor data'
    });
  });
}
  </script>
</body>
</html>