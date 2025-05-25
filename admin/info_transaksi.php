<?php
session_start();
include '../database/connect.php';

// Determine if user is admin or outlet staff
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
$outletId = $_SESSION['outlet_id'] ?? null;

// Build query based on user role
if ($isAdmin) {
    $query = "SELECT t.*, m.nama as nama_member, o.nama as nama_outlet 
              FROM tb_transaksi t
              JOIN tb_member m ON t.id_member = m.id
              JOIN tb_outlet o ON t.id_outlet = o.id
              ORDER BY t.tgl DESC";
} else {
    $query = "SELECT t.*, m.nama as nama_member, o.nama as nama_outlet 
              FROM tb_transaksi t
              JOIN tb_member m ON t.id_member = m.id
              JOIN tb_outlet o ON t.id_outlet = o.id
              WHERE t.id_outlet = '$outletId'
              ORDER BY t.tgl DESC";
}

$result = mysqli_query($mysqli, $query);
$totalTransactions = mysqli_num_rows($result);

// Get status counts
$statusCounts = [
    'baru' => 0,
    'proses' => 0,
    'selesai' => 0,
    'diambil' => 0
];

if ($isAdmin) {
    $statusQuery = "SELECT status, COUNT(*) as count FROM tb_transaksi GROUP BY status";
} else {
    $statusQuery = "SELECT status, COUNT(*) as count FROM tb_transaksi WHERE id_outlet = '$outletId' GROUP BY status";
}

$statusResult = mysqli_query($mysqli, $statusQuery);
while ($row = mysqli_fetch_assoc($statusResult)) {
    $statusCounts[$row['status']] = $row['count'];
}
// Initialize totals array with all transaction IDs set to 0
$totals = array();
while ($row = mysqli_fetch_assoc($result)) {
    $totals[$row['id']] = 0; // Initialize all transactions with 0
}
mysqli_data_seek($result, 0); // Reset result pointer

// Now get the actual totals from details
$detailsQuery = "SELECT dt.id_transaksi, SUM(p.harga * dt.qty) as subtotal 
                FROM tb_detail_transaksi dt
                JOIN tb_paket p ON dt.id_paket = p.id
                GROUP BY dt.id_transaksi";
$detailsResult = mysqli_query($mysqli, $detailsQuery);

// Update totals with actual values where they exist
while ($row = mysqli_fetch_assoc($detailsResult)) {
    $totals[$row['id_transaksi']] = $row['subtotal'];
}
?>



<div class="space-y-6">
    <!-- Header Section - Mobile: Smaller padding, tighter spacing -->
    <div class="bg-gradient-to-r from-indigo-500 to-blue-600 rounded-lg p-4 md:p-6 shadow-lg text-white">
        <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2 md:gap-3">
            <i class="fas fa-exchange-alt text-sm md:text-base"></i>
            Informasi Transaksi
        </h2>
        <p class="opacity-90 mt-1 text-xs md:text-sm">Daftar lengkap transaksi laundry</p>
    </div>

    <!-- Stats Cards - Mobile: Stack vertically, smaller text -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 md:gap-4">
        <div class="bg-white rounded-lg shadow p-3 md:p-4 border-l-4 border-blue-500">
            <div class="text-gray-500 text-xs md:text-sm">Total Transaksi</div>
            <div class="text-xl md:text-2xl font-bold text-blue-600"><?= $totalTransactions ?></div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-3 md:p-4 border-l-4 border-yellow-500">
            <div class="text-gray-500 text-xs md:text-sm">Status Baru</div>
            <div class="text-xl md:text-2xl font-bold text-yellow-600"><?= $statusCounts['baru'] ?></div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-3 md:p-4 border-l-4 border-orange-500">
            <div class="text-gray-500 text-xs md:text-sm">Dalam Proses</div>
            <div class="text-xl md:text-2xl font-bold text-orange-600"><?= $statusCounts['proses'] ?></div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-3 md:p-4 border-l-4 border-green-500">
            <div class="text-gray-500 text-xs md:text-sm">Selesai/Diambil</div>
            <div class="text-xl md:text-2xl font-bold text-green-600"><?= $statusCounts['selesai'] + $statusCounts['diambil'] ?></div>
        </div>
    </div>

    <?php if(mysqli_num_rows($result) > 0): ?>
        <!-- Table - Mobile: Horizontal scroll, compact view -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 hidden md:table-header-group">
                        <tr>
                            <th class="px-4 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                            <th class="px-4 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                            <?php if($isAdmin): ?>
                                <th class="px-4 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outlet</th>
                            <?php endif; ?>
                            <th class="px-4 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="transactions-table-body">
                        <?php 
                        $rowColor = true;
                        while($row = mysqli_fetch_assoc($result)): 
                            // Format dates
                            $tgl = date('d M Y', strtotime($row['tgl']));
                            $tgl_bayar = $row['tgl_bayar'] ? date('d M Y', strtotime($row['tgl_bayar'])) : '-';
                            $batas_waktu = date('d M Y', strtotime($row['batas_waktu']));
                            
                            // Status badge color
                            $statusClass = [
                                'baru' => 'bg-blue-100 text-blue-800',
                                'proses' => 'bg-yellow-100 text-yellow-800',
                                'selesai' => 'bg-green-100 text-green-800',
                                'diambil' => 'bg-purple-100 text-purple-800'
                            ][$row['status']];
                        ?>
                        <!-- Mobile View - Card Layout -->
                        <tr class="md:hidden block border-b border-gray-200 <?= ($rowColor ? 'bg-white' : 'bg-gray-50') ?> hover:bg-blue-50 transition-colors">
                            <td colspan="7" class="px-4 py-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-medium text-gray-900 text-sm"><?= htmlspecialchars($row['kode_invoice']) ?></div>
                                        <div class="text-gray-600 text-xs mt-1"><?= htmlspecialchars($row['nama_member']) ?></div>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </div>
                                
                                <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-gray-500">Tanggal:</span>
                                        <span class="text-gray-900 block"><?= $tgl ?></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Total:</span>
                                        <span class="text-gray-900 block">Rp<?= isset($totals[$row['id']]) ? number_format($totals[$row['id']], 0, ',', '.') : '0' ?></span>
                                    </div>
                                </div>
                                
                                <div class="mt-2 flex justify-end space-x-2">
                                    <a href="invoice.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:text-blue-900" title="Cetak Invoice">
                                        <i class="fas fa-print text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Desktop View - Normal Table Row -->
                        <tr class="hidden md:table-row <?= ($rowColor ? 'bg-white' : 'bg-gray-50') ?> hover:bg-blue-50 transition-colors">
                            <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['kode_invoice']) ?></div>
                            </td>
                            <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= htmlspecialchars($row['nama_member']) ?></div>
                            </td>
                            <?php if($isAdmin): ?>
                                <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars($row['nama_outlet']) ?></div>
                                </td>
                            <?php endif; ?>
                            <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= $tgl ?></div>
                                <div class="text-xs text-gray-500">Selesai: <?= $batas_waktu ?></div>
                            </td>
                            <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    Rp<?= isset($totals[$row['id']]) ? number_format($totals[$row['id']], 0, ',', '.') : '0' ?>
                                </div>
                            </td>
                            <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap text-sm font-medium">
                                <a href="invoice.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3" title="Cetak Invoice">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                        <?php 
                        $rowColor = !$rowColor;
                        endwhile; 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php else: ?>
        <!-- Empty State - Mobile: Smaller padding and text -->
        <div class="bg-white rounded-lg shadow p-6 md:p-8 text-center">
            <i class="fas fa-exchange-alt text-3xl md:text-4xl text-gray-300 mb-3 md:mb-4"></i>
            <h3 class="text-base md:text-lg font-medium text-gray-900">Tidak ada transaksi ditemukan</h3>
            <p class="mt-1 text-xs md:text-sm text-gray-500">Buat transaksi baru untuk memulai</p>
        </div>
    <?php endif; ?>
</div>


