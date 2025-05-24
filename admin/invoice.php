<?php
session_start();
include '../database/connect.php';

// Pastikan ada parameter id_transaksi
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID Transaksi tidak valid");
}

$id_transaksi = $_GET['id'];

// Ambil data transaksi
$query = "SELECT t.*, m.nama as nama_member, m.tlp, m.alamat, 
                 u.nama as nama_kasir, 
                 o.nama as nama_outlet, o.alamat as alamat_outlet
          FROM tb_transaksi t
          JOIN tb_member m ON t.id_member = m.id
          JOIN tb_user u ON t.id_user = u.id
          LEFT JOIN tb_outlet o ON t.id_outlet = o.id
          WHERE t.id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_transaksi);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Transaksi tidak ditemukan");
}

$transaksi = $result->fetch_assoc();

// Ambil detail transaksi
$query_detail = "SELECT dt.*, p.nama_paket, p.jenis, p.harga
                 FROM tb_detail_transaksi dt
                 JOIN tb_paket p ON dt.id_paket = p.id
                 WHERE dt.id_transaksi = ?";
$stmt_detail = $mysqli->prepare($query_detail);
$stmt_detail->bind_param("i", $id_transaksi);
$stmt_detail->execute();
$detail_transaksi = $stmt_detail->get_result()->fetch_all(MYSQLI_ASSOC);

// Hitung total
$subtotal = array_reduce($detail_transaksi, function($sum, $item) {
    return $sum + ($item['harga'] * $item['qty']);
}, 0);

// Ubah ini untuk diskon nominal langsung (bukan persen)
$diskon_amount = $transaksi['diskon']; // Langsung ambil nilai diskon dari database
$total_sebelum_pajak = $subtotal - $diskon_amount + $transaksi['biaya_tambahan'];
$pajak_amount = $total_sebelum_pajak * ($transaksi['pajak'] / 100);
$total = $total_sebelum_pajak + $pajak_amount;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= $transaksi['kode_invoice'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            body {
                font-size: 12px;
                line-height: 1.2;
                margin: 0;
                padding: 0;
            }
            .invoice-print {
                width: 100%;
                max-width: 100%;
                padding: 10px;
                margin: 0;
                box-shadow: none;
            }
            .no-print {
                display: none !important;
            }
            .compact-table th, .compact-table td {
                padding: 4px 6px;
            }
            .header-gradient {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            @page {
                size: auto;
                margin: 5mm;
            }
        }
        .header-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }
        .divider {
            border-top: 1px dashed #e5e7eb;
        }
        .total-box {
            background-color: #f8fafc;
            border-left: 3px solid #3b82f6;
        }
        .badge {
            font-size: 0.75rem;
            padding: 0.15rem 0.5rem;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-2">
        <div class="invoice-print bg-white rounded-lg shadow-sm max-w-3xl mx-auto">
            <!-- Header Compact -->
            <div class="header-gradient text-white p-4">
    <div class="grid grid-cols-2 gap-4 items-center">
        <div>
            <h1 class="text-xl md:text-2xl font-bold">INVOICE</h1>
            <p class="text-blue-100 text-xs"><?= $transaksi['kode_invoice'] ?></p>
        </div>
        <div class="text-right">
            <h2 class="text-lg md:text-xl font-semibold"><?= $transaksi['nama_outlet'] ?></h2>
            <p class="text-blue-100 text-xs"><?= $transaksi['alamat_outlet'] ?></p>
        </div>
    </div>
</div>
            
            <!-- Info Compact -->
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                    <div class="bg-gray-50 p-3 rounded">
                        <h3 class="font-semibold text-sm mb-2 text-gray-700 border-b pb-1">
                            <i class="fas fa-user mr-1 text-blue-500"></i>Pelanggan
                        </h3>
                        <div class="text-xs space-y-1">
                            <p><span class="font-medium"><?= $transaksi['nama_member'] ?></span></p>
                            <p><?= $transaksi['tlp'] ?></p>
                            <p><?= $transaksi['alamat'] ?></p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-3 rounded">
                        <h3 class="font-semibold text-sm mb-2 text-gray-700 border-b pb-1">
                            <i class="fas fa-receipt mr-1 text-blue-500"></i>Transaksi
                        </h3>
                        <div class="text-xs space-y-1">
                            <p><span class="font-medium">Tanggal:</span> <?= date('d/m/Y H:i', strtotime($transaksi['tgl'])) ?></p>
                            <p><span class="font-medium">Batas Waktu:</span> <?= date('d/m/Y H:i', strtotime($transaksi['batas_waktu'])) ?></p>
                            <?php if ($transaksi['tgl_bayar']): ?>
                            <p><span class="font-medium">Dibayar:</span> <?= date('d/m/Y H:i', strtotime($transaksi['tgl_bayar'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Tabel Compact -->
                <div class="mb-4 overflow-x-auto">
                    <table class="w-full compact-table">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 text-xs">
                                <th class="py-2 px-2 text-left border-b">Paket</th>
                                <th class="py-2 px-2 text-left border-b">Jenis</th>
                                <th class="py-2 px-2 text-right border-b">Harga</th>
                                <th class="py-2 px-2 text-right border-b">Qty</th>
                                <th class="py-2 px-2 text-right border-b">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-xs">
                            <?php foreach ($detail_transaksi as $detail): ?>
                            <tr>
                                <td class="py-2 px-2"><?= $detail['nama_paket'] ?></td>
                                <td class="py-2 px-2">
                                    <span class="badge rounded-full 
                                        <?= $detail['jenis'] == 'kiloan' ? 'bg-blue-100 text-blue-800' : 
                                           ($detail['jenis'] == 'selimut' ? 'bg-green-100 text-green-800' : 
                                           ($detail['jenis'] == 'bed_cover' ? 'bg-purple-100 text-purple-800' : 
                                           'bg-yellow-100 text-yellow-800')) ?>">
                                        <?= substr(ucfirst(str_replace('_', ' ', $detail['jenis'])), 0) ?>
                                    </span>
                                </td>
                                <td class="py-2 px-2 text-right">Rp<?= number_format($detail['harga'], 0, ',', '.') ?></td>
                                <td class="py-2 px-2 text-right"><?= $detail['qty'] ?></td>
                                <td class="py-2 px-2 text-right font-medium">Rp<?= number_format($detail['harga'] * $detail['qty'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Total Compact -->
                <div class="flex justify-end">
                    <div class="w-full md:w-2/3 total-box p-3 rounded">
                        <div class="space-y-1 text-xs">
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span class="font-medium">Rp<?= number_format($subtotal, 0, ',', '.') ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Biaya Tambahan:</span>
                                <span class="text-green-500">+ Rp<?= number_format($transaksi['biaya_tambahan'], 0, ',', '.') ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Pajak (<?= $transaksi['pajak'] ?>%):</span>
                                <span class="text-green-500">+ Rp<?= number_format($pajak_amount, 0, ',', '.') ?></span>
                            </div>
                            <div class="flex justify-between">
                            <span>Diskon:</span>
                            <span class="text-red-500">- Rp<?= number_format($transaksi['diskon'], 0, ',', '.') ?></span>
                             </div>
                            <div class="divider my-1"></div>
                            <div class="flex justify-between font-bold">
                                <span>TOTAL:</span>
                                <span class="text-blue-600">Rp<?= number_format($total, 0, ',', '.') ?></span>
                            </div>
                        </div>
                        
                        <div class="mt-3 p-2 rounded text-xs text-center 
                            <?= $transaksi['dibayar'] == 'dibayar' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                            <i class="fas <?= $transaksi['dibayar'] == 'dibayar' ? 'fa-check-circle' : 'fa-times-circle' ?> mr-1"></i>
                            <span><?= $transaksi['dibayar'] == 'dibayar' ? 'LUNAS' : 'BELUM LUNAS' ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Compact -->
                <div class="mt-4 pt-2 border-t text-center text-gray-500 text-xs">
                    <p class="italic">Terima kasih atas kepercayaan Anda</p>
                    <p class="mt-1"><i class="fas fa-phone-alt mr-1"></i> (021) 1234567 | <i class="fas fa-envelope ml-2 mr-1"></i> info@laundrykilat.com</p>
                </div>
            </div>
        </div>
        
        <!-- Tombol Aksi -->
        <div class="mt-4 text-center no-print">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded text-sm">
                <i class="fas fa-print mr-1"></i>Cetak
            </button>
            <a href="menu_transaksi.php" class="ml-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-1.5 rounded text-sm">
                <i class="fas fa-arrow-left mr-1"></i>Kembali
            </a>
        </div>
    </div>

    <script>
        // Auto print jika parameter print=1
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if(urlParams.has('print')) {
                setTimeout(() => {
                    window.print();
                }, 500);
            }
        });
    </script>
</body>
</html>