<?php
session_start();
include '../database/connect.php';

// Check if this is a print request
if (!isset($_GET['print'])) {
    header("Location: menu_laporan.php");
    exit();
}

// Get all filter parameters
$tanggalAwal = isset($_GET['tanggal-awal']) ? $_GET['tanggal-awal'] : '';
$tanggalAkhir = isset($_GET['tanggal-akhir']) ? $_GET['tanggal-akhir'] : '';
$outlet = isset($_GET['outlet']) ? $_GET['outlet'] : '';
$kasir = isset($_GET['petugas']) ? $_GET['petugas'] : '';
$statusPesanan = isset($_GET['status-pesanan']) ? $_GET['status-pesanan'] : '';
$statusPembayaran = isset($_GET['status-pembayaran']) ? $_GET['status-pembayaran'] : '';

// Query untuk mendapatkan data laporan (same as in menu_laporan.php)
$sqlLaporan = "SELECT 
    t.id, 
    t.kode_invoice, 
    t.tgl, 
    t.tgl_bayar, 
    t.status, 
    t.dibayar, 
    o.nama as outlet, 
    u.nama as kasir,
    COALESCE(SUM(td.qty * p.harga), 0) as total
  FROM tb_transaksi t
  JOIN tb_outlet o ON t.id_outlet = o.id
  JOIN tb_user u ON t.id_user = u.id
  LEFT JOIN tb_detail_transaksi td ON t.id = td.id_transaksi
  LEFT JOIN tb_paket p ON td.id_paket = p.id
  WHERE 1=1";
  
if (!empty($tanggalAwal) && !empty($tanggalAkhir)) {
    $sqlLaporan .= " AND t.tgl BETWEEN '$tanggalAwal' AND '$tanggalAkhir'";
} elseif (!empty($tanggalAwal)) {
    $sqlLaporan .= " AND t.tgl >= '$tanggalAwal'";
} elseif (!empty($tanggalAkhir)) {
    $sqlLaporan .= " AND t.tgl <= '$tanggalAkhir'";
}

if (!empty($outlet)) {
    $sqlLaporan .= " AND t.id_outlet = '$outlet'";
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

// Get outlet name if filtered
$outletName = 'Semua Outlet';
if (!empty($outlet)) {
    $outletQuery = mysqli_query($mysqli, "SELECT nama FROM tb_outlet WHERE id = '$outlet'");
    if ($outletRow = mysqli_fetch_assoc($outletQuery)) {
        $outletName = $outletRow['nama'];
    }
}

// Get kasir name if filtered
$kasirName = 'Semua Kasir';
if (!empty($kasir)) {
    $kasirQuery = mysqli_query($mysqli, "SELECT nama FROM tb_user WHERE id = '$kasir'");
    if ($kasirRow = mysqli_fetch_assoc($kasirQuery)) {
        $kasirName = $kasirRow['nama'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4;
            margin: 0.5cm;
        }
        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            font-size: 10pt;
        }
        @media print {
            body {
                padding: 0;
                width: 100%;
                background: white;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                page-break-inside: avoid;
            }
            .header-section, .info-section {
                page-break-after: avoid;
            }
            .table-section {
                page-break-inside: auto;
            }
            .summary-section {
                page-break-before: avoid;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                page-break-inside: auto;
            }
            th, td {
                padding: 4px 6px;
                border: 1px solid #e2e8f0;
            }
            th {
                background-color: #f8fafc !important;
                color: #64748b !important;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body class="font-sans bg-white text-gray-800">
    <div class="print-container print-only">
    <!-- Main Print Container -->
    <div class="print-container">
        <!-- Header Section -->
        <div class="header-section bg-gradient-to-r from-blue-600 to-blue-800 text-white p-3 text-center">
            <h1 class="text-lg font-bold">LAPORAN TRANSAKSI LAUNDRY</h1>
            <p class="text-blue-100 text-xs">Sistem Manajemen Laundry Modern</p>
        </div>
        
        <!-- Info Section -->
        <div class="info-section p-3 border-b border-gray-200">
            <div class="grid grid-cols-3 gap-2 text-xs">
                <div>
                    <h3 class="font-semibold text-gray-700">Periode</h3>
                    <p class="text-gray-600">
                        <?= !empty($tanggalAwal) ? date('d/m/y', strtotime($tanggalAwal)) : 'Awal' ?> - 
                        <?= !empty($tanggalAkhir) ? date('d/m/y', strtotime($tanggalAkhir)) : 'Sekarang' ?>
                    </p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-700">Outlet</h3>
                    <p class="text-gray-600"><?= $outletName ?></p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-700">Kasir</h3>
                    <p class="text-gray-600"><?= $kasirName ?></p>
                </div>
            </div>
        </div>
        
        <!-- Table Section -->
        <div class="table-section">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="text-left">#</th>
                        <th class="text-left">Invoice</th>
                        <th class="text-left">Tanggal</th>
                        <th class="text-left">Outlet</th>
                        <th class="text-left">Kasir</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Bayar</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataLaporan as $index => $transaksi): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td class="text-blue-600"><?= $transaksi['kode_invoice'] ?></td>
                        <td><?= date('d/m/y', strtotime($transaksi['tgl'])) ?></td>
                        <td><?= $transaksi['outlet'] ?></td>
                        <td><?= $transaksi['kasir'] ?></td>
                        <td>
                            <span class="status-badge <?= $transaksi['status'] ?>">
                                <?= ucfirst($transaksi['status']) ?>
                            </span>
                        </td>
                        <td>
                        <span class="payment-status <?= $transaksi['dibayar'] ?>">
                    <?= $transaksi['dibayar'] == 'dibayar' ? 'Dibayar' : 'Belum' ?>
                </span>
                <?php if ($transaksi['dibayar'] == 'dibayar' && !empty($transaksi['tgl_bayar'])): ?>
                    <div class="text-2xs text-gray-400">
                        <?= date('d/m/y', strtotime($transaksi['tgl_bayar'] ?? '')) ?>
                    </div>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">Rp<?= number_format($transaksi['total'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Summary Section -->
        <div class="summary-section bg-gray-50 p-3 border-t border-gray-200">
            <div class="flex justify-between items-center text-xs">
                <div class="text-gray-500">
                    Dicetak: <?= date('d/m/Y H:i') ?>
                </div>
                <div class="font-semibold text-blue-700">
                    TOTAL: Rp <?= number_format($totalPendapatan, 0, ',', '.') ?>
                </div>
            </div>
        </div>
    </div>

    <style>
        .status-badge {
            display: inline-block;
            padding: 0.15rem 0.3rem;
            border-radius: 0.25rem;
            font-size: 0.65rem;
            font-weight: 500;
        }
        .status-badge.baru { background-color: #dbeafe; color: #1e40af; }
        .status-badge.proses { background-color: #fef3c7; color: #92400e; }
        .status-badge.selesai { background-color: #d1fae5; color: #065f46; }
        .status-badge.diambil { background-color: #ede9fe; color: #5b21b6; }
        
        .payment-status {
            display: inline-block;
            padding: 0.15rem 0.3rem;
            border-radius: 0.25rem;
            font-size: 0.65rem;
            font-weight: 500;
        }
        .payment-status.dibayar { background-color: #d1fae5; color: #065f46; }
        .payment-status.belum_dibayar { background-color: #fee2e2; color: #b91c1c; }
    </style>

<script>
        // Enhanced print handling with redirect
        let printed = false;
        
        // Function to handle print completion
        function afterPrint() {
            if (!printed) {
                // If not printed, redirect back after 3 seconds
                setTimeout(() => {
                    window.location.href = 'menu_laporan.php';
                }, 3000);
            } else {
                // If printed, close the window
                window.close();
            }
        }
        
        // Set up event listeners
        window.matchMedia('print').addListener((mql) => {
            if (!mql.matches) {
                afterPrint();
            }
        });
        
        // Fallback for older browsers
        window.onafterprint = afterPrint;
        
        // Try to print automatically
        setTimeout(() => {
            window.print();
            printed = true;
        }, 500);
        
        // Redirect if still on page after 10 seconds
        setTimeout(() => {
            if (!printed) {
                window.location.href = 'menu_laporan.php';
            }
        }, 10000);
    </script>
</body>
</html>