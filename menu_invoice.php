<?php
session_start();
include 'database/connect.php'; // Sesuaikan dengan path database connection Anda

// Fungsi untuk mendapatkan detail invoice
function getInvoiceDetails($kode_invoice, $mysqli) {
    $query = "SELECT 
                t.*,
                o.nama as outlet_nama,
                u.nama as kasir_nama,
                m.nama as member_nama
              FROM tb_transaksi t
              LEFT JOIN tb_outlet o ON t.id_outlet = o.id
              LEFT JOIN tb_user u ON t.id_user = u.id
              LEFT JOIN tb_member m ON t.id_member = m.id
              WHERE t.kode_invoice = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $kode_invoice);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Proses pengecekan invoice jika ada request
$invoiceData = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kode_invoice'])) {
    $kode_invoice = $_POST['kode_invoice'];
    $invoiceData = getInvoiceDetails($kode_invoice, $mysqli);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cek Status Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .sticky-nav {
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-[#c9edff] font-sans">
    <!-- Header -->
    <header id="navbar" class="flex items-center justify-between px-6 py-4 bg-white sticky-nav">
        <img alt="logo" class="w-12 h-12" height="50" src="assets/img/logo.png" width="50"/>
        <nav class="flex space-x-6 text-base font-medium">
            <a class="text-black hover:underline" href="index.php">Home</a>
            <a class="text-black hover:underline" href="check_invoice.php">Cek Status Laundry</a>
        </nav>
        <button class="bg-[#1a5de8] text-white px-5 py-2 rounded-md text-base font-medium hover:bg-blue-700 transition-colors" type="button">
            <a href="login.php">Login</a>
        </button>
    </header>

    <!-- Main Container -->
    <div class="container mx-auto px-4 py-8">
        <!-- Form Pengecekan Invoice -->
        <div class="flex justify-center mb-10"> <!-- Reduced margin-bottom from 16 to 10 -->
            <div class="bg-white rounded-md shadow-lg w-full max-w-2xl p-8 text-center">
                <h1 class="text-xl font-semibold text-black mb-6">Masukan Nomor Invoice</h1>
                <form method="POST" action="" class="flex flex-col items-center space-y-4">
                    <input
                        id="invoice"
                        name="kode_invoice"
                        type="text"
                        placeholder="Contoh: KILAT123456"
                        class="w-full max-w-xs py-3 px-4 rounded-lg shadow-sm bg-[#d9d9d9] focus:outline-none focus:ring-2 focus:ring-[#1a5de8]"
                        required
                    />
                    <button
                        type="submit"
                        class="bg-[#1a5de8] text-white px-6 py-2.5 rounded-md hover:bg-blue-700 transition duration-300 text-sm font-medium"
                    >
                        Periksa Invoice
                    </button>
                </form>
            </div>
        </div>

        <!-- Hasil Pengecekan Invoice -->
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <?php if ($invoiceData): ?>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Invoice Ditemukan',
                        text: 'Detail invoice berhasil dimuat',
                        timer: 2000,
                        showConfirmButton: false
                    });
                </script>
                
                <div class="flex justify-center mb-20"> <!-- Adjusted spacing -->
                    <div class="bg-white w-full max-w-5xl p-6 md:p-8 rounded-lg shadow-md">
                        <div class="inline-block bg-gray-200 px-6 py-2 rounded-lg text-base mb-3 select-none">
                            <?= htmlspecialchars($invoiceData['kode_invoice']) ?>
                        </div>
                        <hr class="border-t border-gray-300 mb-4" />
                        <div class="space-y-3 text-base">
                            <p><strong>Outlet:</strong> <?= htmlspecialchars($invoiceData['outlet_nama'] ?? '-') ?></p>
                            <p><strong>Kasir:</strong> <?= htmlspecialchars($invoiceData['kasir_nama'] ?? '-') ?></p>
                            <p><strong>Customer:</strong> <?= htmlspecialchars($invoiceData['member_nama'] ?? '-') ?></p>
                            <p><strong>Tanggal:</strong> <?= date('d/m/Y H:i', strtotime($invoiceData['tgl'])) ?></p>
                            <p><strong>Batas Waktu:</strong> <?= date('d/m/Y H:i', strtotime($invoiceData['batas_waktu'])) ?></p>
                            <p><strong>Status:</strong> 
                                <?php 
                                    $status = [
                                        'baru' => 'Baru',
                                        'proses' => 'Diproses',
                                        'selesai' => 'Selesai',
                                        'diambil' => 'Sudah Diambil'
                                    ];
                                    echo $status[$invoiceData['status']] ?? $invoiceData['status'];
                                ?>
                            </p>
                            <p><strong>Pembayaran:</strong> 
                                <?= $invoiceData['dibayar'] === 'dibayar' ? 'Lunas' : 'Belum Dibayar' ?>
                                <?php if ($invoiceData['dibayar'] === 'dibayar' && !empty($invoiceData['tgl_bayar'])): ?>
                                    (<?= date('d/m/Y H:i', strtotime($invoiceData['tgl_bayar'])) ?>)
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Invoice Tidak Ditemukan',
                        text: 'Nomor invoice yang Anda masukkan tidak valid atau tidak ditemukan',
                        confirmButtonText: 'Coba Lagi'
                    });
                </script>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        // Fungsi untuk validasi sebelum submit
        function validateInvoice() {
            const invoiceInput = document.getElementById('invoice');
            if (invoiceInput.value.trim() === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Harap masukkan nomor invoice terlebih dahulu',
                    confirmButtonText: 'Mengerti'
                });
                return false;
            }
            return true;
        }

        // Event listener untuk form
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!validateInvoice()) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>