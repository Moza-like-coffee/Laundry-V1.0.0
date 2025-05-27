<?php
session_start();
include '../database/connect.php';
include 'sidebar.php';

// Proses filter jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['tanggal-awal']) || isset($_GET['tanggal-akhir']) || isset($_GET['outlet']) || isset($_GET['petugas']) || isset($_GET['status-pesanan']) || isset($_GET['status-pembayaran']))) {
    $tanggalAwal = isset($_GET['tanggal-awal']) ? $_GET['tanggal-awal'] : '';
    $tanggalAkhir = isset($_GET['tanggal-akhir']) ? $_GET['tanggal-akhir'] : '';
    $outlet = isset($_GET['outlet']) ? $_GET['outlet'] : '';
    $kasir = isset($_GET['petugas']) ? $_GET['petugas'] : '';
    $statusPesanan = isset($_GET['status-pesanan']) ? $_GET['status-pesanan'] : '';
    $statusPembayaran = isset($_GET['status-pembayaran']) ? $_GET['status-pembayaran'] : '';

    // Query untuk mendapatkan data laporan
    $sqlLaporan = "SELECT 
    t.id, 
    t.kode_invoice, 
    t.tgl, 
    t.tgl_bayar, 
    t.status, 
    t.dibayar, 
    COALESCE(o.nama, 'Tidak ada outlet') as outlet, 
    u.nama as kasir,
    COALESCE(SUM(td.qty * p.harga), 0) as total
  FROM tb_transaksi t
  LEFT JOIN tb_outlet o ON t.id_outlet = o.id
  JOIN tb_user u ON t.id_user = u.id
  LEFT JOIN tb_detail_transaksi td ON t.id = td.id_transaksi
  LEFT JOIN tb_paket p ON td.id_paket = p.id
  WHERE 1=1";
    
    // Add date filter only if dates are provided
    if (!empty($tanggalAwal) && !empty($tanggalAkhir)) {
        $sqlLaporan .= " AND t.tgl BETWEEN '$tanggalAwal' AND '$tanggalAkhir'";
    } elseif (!empty($tanggalAwal)) {
        $sqlLaporan .= " AND t.tgl >= '$tanggalAwal'";
    } elseif (!empty($tanggalAkhir)) {
        $sqlLaporan .= " AND t.tgl <= '$tanggalAkhir'";
    }
    
    if (!empty($outlet)) {
      $sqlLaporan .= " AND (t.id_outlet = '$outlet' OR t.id_outlet IS NULL)";
  }
    if (!empty($kasir)) {
        $sqlLaporan .= " AND t.id_user = '$kasir'";
    }
    if (!empty($statusPesanan)) {
        $sqlLaporan .= " AND t.status = '$statusPesanan'";
    }
    if (!empty($statusPembayaran)) {
        $sqlLaporan .= " AND t.dibayar = '$statusPembayaran'";
    }
    
    $sqlLaporan .= " GROUP BY t.id";
    
    $resultLaporan = mysqli_query($mysqli, $sqlLaporan) or die(mysqli_error($mysqli));
    $dataLaporan = [];
    $totalPendapatan = 0;
    
    if (mysqli_num_rows($resultLaporan) > 0) {
        while ($row = mysqli_fetch_assoc($resultLaporan)) {
            $dataLaporan[] = $row;
            $totalPendapatan += $row['total'];
        }
    }
}

// Query untuk dropdown filter (sama seperti sebelumnya)
$sql = "SELECT id, nama FROM tb_outlet";
$result = mysqli_query($mysqli, $sql);
$outletOptions = "";

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $selected = (isset($_GET['outlet']) && $_GET['outlet'] == $row['id']) ? 'selected' : '';
        $outletOptions .= "<option value='".$row['id']."' $selected>".$row['nama']."</option>";
    }
} else {
    $outletOptions = "<option value='' disabled>No outlets available</option>";
}

$sqlKasir = "SELECT id, nama FROM tb_user WHERE role = 'kasir'";
$resultKasir = mysqli_query($mysqli, $sqlKasir);
$kasirOptions = "<option value='' disabled selected>Pilih Kasir</option>";

if (mysqli_num_rows($resultKasir) > 0) {
    while ($row = mysqli_fetch_assoc($resultKasir)) {
        $selected = (isset($_GET['petugas']) && $_GET['petugas'] == $row['id']) ? 'selected' : '';
        $kasirOptions .= "<option value='".$row['id']."' $selected>".$row['nama']."</option>";
    }
}

$statusPesananOptions = "<option value='' disabled selected>Pilih Status</option>
                        <option value='baru' ".((isset($_GET['status-pesanan']) && $_GET['status-pesanan'] == 'baru') ? 'selected' : '').">Baru</option>
                        <option value='proses' ".((isset($_GET['status-pesanan']) && $_GET['status-pesanan'] == 'proses') ? 'selected' : '').">Proses</option>
                        <option value='selesai' ".((isset($_GET['status-pesanan']) && $_GET['status-pesanan'] == 'selesai') ? 'selected' : '').">Selesai</option>
                        <option value='diambil' ".((isset($_GET['status-pesanan']) && $_GET['status-pesanan'] == 'diambil') ? 'selected' : '').">Diambil</option>";

$statusPembayaranOptions = "<option value='' disabled selected>Pilih Status</option>
                           <option value='dibayar' ".((isset($_GET['status-pembayaran']) && $_GET['status-pembayaran'] == 'dibayar') ? 'selected' : '').">Dibayar</option>
                           <option value='belum_dibayar' ".((isset($_GET['status-pembayaran']) && $_GET['status-pembayaran'] == 'belum_dibayar') ? 'selected' : '').">Belum Dibayar</option>";
?>

<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Menu Laporan</title>
<link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<script src="https://cdn.tailwindcss.com"></script>
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
  
  /* Table styling */
  .report-table {
    width: 100%;
    border-collapse: collapse;
  }
  .report-table th {
    background-color: #f0f0f0;
    padding: 8px 12px;
    text-align: left;
    font-size: 13px;
    border: 1px solid #ddd;
  }
  .report-table td {
    padding: 8px 12px;
    font-size: 13px;
    border: 1px solid #ddd;
  }
  .report-table tr:nth-child(even) {
    background-color: #f9f9f9;
  }
  .report-table tr:hover {
    background-color: #f0f0f0;
  }
</style>
</head>
<body class="bg-gradient-to-b from-[#12192b] to-[#1f3a5a] min-h-screen text-black">
<div class="flex items-center justify-center min-h-screen">
  <main class="p-6 md:p-10 w-full max-w-3xl">
    <section class="bg-white rounded-lg p-6 w-full">
      <button type="button" class="bg-[#c7d9f7] border border-black rounded-md px-4 py-2 text-sm mb-3">Filter Laporan</button>
      <hr class="border-black mb-4" />
      
      <div class="w-full max-w-l mx-auto">
        <form class="flex flex-wrap gap-x-6 gap-y-4 justify-center">
          <div class="flex flex-col w-full sm:w-[47%]">
            <label for="tanggal-awal" class="text-[13px] font-semibold mb-1">Tanggal Awal</label>
            <input type="date" id="tanggal-awal" name="tanggal-awal" value="<?= isset($_GET['tanggal-awal']) ? $_GET['tanggal-awal'] : '' ?>" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none cursor-pointer" />
          </div>
          <div class="flex flex-col w-full sm:w-[47%]">
            <label for="tanggal-akhir" class="text-[13px] font-semibold mb-1">Tanggal Akhir</label>
            <input type="date" id="tanggal-akhir" name="tanggal-akhir" value="<?= isset($_GET['tanggal-akhir']) ? $_GET['tanggal-akhir'] : '' ?>" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none cursor-pointer" />
          </div>
          <div class="flex flex-col w-full sm:w-[47%]">
            <label for="outlet" class="text-[13px] font-semibold mb-1">Outlet</label>
            <select id="outlet" name="outlet" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none cursor-pointer">
              <option value="" disabled selected>Pilih Outlet</option>
              <?php echo $outletOptions; ?>
            </select>
          </div>
          <div class="flex flex-col w-full sm:w-[47%]">
            <label for="petugas" class="text-[13px] font-semibold mb-1">Kasir</label>
            <select id="petugas" name="petugas" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none cursor-pointer">
              <?php echo $kasirOptions; ?>
            </select>
          </div>
          <div class="flex flex-col w-full sm:w-[47%]">
            <label for="status-pesanan" class="text-[13px] font-semibold mb-1">Status Pesanan</label>
            <select id="status-pesanan" name="status-pesanan" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none cursor-pointer">
              <?php echo $statusPesananOptions; ?>
            </select>
          </div>
          <div class="flex flex-col w-full sm:w-[47%]">
            <label for="status-pembayaran" class="text-[13px] font-semibold mb-1">Status Pembayaran</label>
            <select id="status-pembayaran" name="status-pembayaran" class="h-9 rounded-md bg-gray-300 px-3 text-sm outline-none cursor-pointer">
              <?php echo $statusPembayaranOptions; ?>
            </select>
          </div>
          <div class="w-full">
  <button type="submit" class="bg-[#c9e6c1] border border-black rounded-md px-6 py-2 text-sm font-normal mt-6 hover:bg-green-300">
    Buat Laporan
  </button>
</div>

        </form>
        
      <?php if (isset($dataLaporan)): ?>
      <div class="mt-8">
        <h3 class="text-lg font-semibold mb-4">Hasil Laporan</h3>
        
        <div class="flex justify-between items-center mb-4">
  <div>
    <?php 
    if (!empty($tanggalAwal) || !empty($tanggalAkhir)): 
      $displayAwal = !empty($tanggalAwal) ? date('d M Y', strtotime($tanggalAwal)) : 'Awal';
      $displayAkhir = !empty($tanggalAkhir) ? date('d M Y', strtotime($tanggalAkhir)) : 'Sekarang';
    ?>
      <p class="text-sm"><span class="font-semibold">Periode:</span> 
        <?= "$displayAwal - $displayAkhir" ?>
      </p>
    <?php endif; ?>
    
    <?php if (!empty($outlet)): ?>
      <?php 
        $outletName = '';
        $outletQuery = mysqli_query($mysqli, "SELECT nama FROM tb_outlet WHERE id = '$outlet'");
        if ($outletRow = mysqli_fetch_assoc($outletQuery)) {
          $outletName = $outletRow['nama'];
        }
      ?>
      <p class="text-sm"><span class="font-semibold">Outlet:</span> <?= $outletName ?></p>
    <?php endif; ?>
  </div>
  <div class="bg-[#f0f7ff] px-4 py-2 rounded-md border border-blue-200">
    <p class="text-sm font-semibold">Total Pendapatan: <span class="text-blue-600">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></span></p>
  </div>
</div>
        
        <div class="overflow-x-auto">
          <table class="report-table">
            <thead>
              <tr>
                <th>No. Invoice</th>
                <th>Tanggal</th>
                <th>Outlet</th>
                <th>Kasir</th>
                <th>Status Pesanan</th>
                <th>Status Pembayaran</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($dataLaporan) > 0): ?>
                <?php foreach ($dataLaporan as $transaksi): ?>
                  <tr>
                    <td><?= $transaksi['kode_invoice'] ?></td>
                    <td><?= date('d M Y', strtotime($transaksi['tgl'])) ?></td>
                    <td><?= $transaksi['outlet'] ?></td>
                    <td><?= $transaksi['kasir'] ?></td>
                    <td>
                      <?php 
                        $statusPesanan = '';
                        switch ($transaksi['status']) {
                          case 'baru': $statusPesanan = 'Baru'; break;
                          case 'proses': $statusPesanan = 'Proses'; break;
                          case 'selesai': $statusPesanan = 'Selesai'; break;
                          case 'diambil': $statusPesanan = 'Diambil'; break;
                          default: $statusPesanan = $transaksi['status'];
                        }
                        echo $statusPesanan;
                      ?>
                    </td>
                    <td>
                      <?= $transaksi['dibayar'] == 'dibayar' ? 'Dibayar' : 'Belum Dibayar' ?>
                      <?php if ($transaksi['dibayar'] == 'dibayar' && !empty($transaksi['tgl_bayar'])): ?>
                        <br><span class="text-xs text-gray-500">(<?= date('d M Y', strtotime($transaksi['tgl_bayar'])) ?>)</span>
                      <?php endif; ?>
                    </td>
                    <td>Rp <?= number_format($transaksi['total'], 0, ',', '.') ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center py-4">Tidak ada data transaksi untuk filter yang dipilih</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        
        <div class="mt-8 flex justify-end space-x-3">
    <a href="cetak_laporan.php?<?= http_build_query($_GET) ?>&print=true" 
       target="_blank"
       class="no-print inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
        </svg>
        Cetak Laporan
    </a>
    <button onclick="exportToExcel()" class="no-print inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
        Export Excel
    </button>
</div>
      </div>
      <?php endif; ?>
    </section>
  </main>
</div>

<script>
  function exportToExcel() {
  // Ambil semua parameter filter dari URL saat ini
  const urlParams = new URLSearchParams(window.location.search);
  
  // Redirect ke export_excel.php dengan parameter yang sama
  window.location.href = 'export_excel.php?' + urlParams.toString();
}
</script>
</body>
</html>