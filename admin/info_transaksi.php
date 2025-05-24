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
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-indigo-500 to-blue-600 rounded-lg p-6 shadow-lg text-white">
        <h2 class="text-2xl font-bold flex items-center gap-3">
            <i class="fas fa-exchange-alt"></i>
            Informasi Transaksi
        </h2>
        <p class="opacity-90 mt-1">Daftar lengkap transaksi laundry</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="text-gray-500 text-sm">Total Transaksi</div>
            <div class="text-2xl font-bold text-blue-600"><?= $totalTransactions ?></div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <div class="text-gray-500 text-sm">Status Baru</div>
            <div class="text-2xl font-bold text-yellow-600"><?= $statusCounts['baru'] ?></div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <div class="text-gray-500 text-sm">Dalam Proses</div>
            <div class="text-2xl font-bold text-orange-600"><?= $statusCounts['proses'] ?></div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="text-gray-500 text-sm">Selesai/Diambil</div>
            <div class="text-2xl font-bold text-green-600"><?= $statusCounts['selesai'] + $statusCounts['diambil'] ?></div>
        </div>
    </div>

    <!-- Search and Filter Section
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Transaksi</label>
                <div class="relative">
                    <input type="text" id="search" name="search" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pl-10" placeholder="Cari berdasarkan invoice/nama...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="w-full md:w-48">
                <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Filter Status</label>
                <select id="status-filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="baru">Baru</option>
                    <option value="proses">Proses</option>
                    <option value="selesai">Selesai</option>
                    <option value="diambil">Diambil</option>
                </select>
            </div>
            
        </div>
        <div id="custom-date-range" class="hidden mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="start-date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" id="start-date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="end-date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" id="end-date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>
    </div> -->

    <?php if(mysqli_num_rows($result) > 0): ?>
        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                            <?php if($isAdmin): ?>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outlet</th>
                            <?php endif; ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
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
                        <tr class="<?= ($rowColor ? 'bg-white' : 'bg-gray-50') ?> hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['kode_invoice']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= htmlspecialchars($row['nama_member']) ?></div>
                            </td>
                            <?php if($isAdmin): ?>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars($row['nama_outlet']) ?></div>
                                </td>
                            <?php endif; ?>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= $tgl ?></div>
                                <div class="text-xs text-gray-500">Selesai: <?= $batas_waktu ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
    <div class="text-sm font-medium text-gray-900">
        Rp<?= isset($totals[$row['id']]) ? number_format($totals[$row['id']], 0, ',', '.') : '0' ?>
    </div>
</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="invoice.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3" title="Cetak Invoice">
                                    <i class="fas fa-print"></i>
                                </a>
                                <button class="text-green-600 hover:text-green-900 view-detail" data-id="<?= $row['id'] ?>" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php 
                        $rowColor = !$rowColor;
                        endwhile; 
                        ?>
                    </tbody>
                </table>
            </div>
            

    <?php else: ?>
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <i class="fas fa-exchange-alt text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900">Tidak ada transaksi ditemukan</h3>
            <p class="mt-1 text-sm text-gray-500">Buat transaksi baru untuk memulai</p>
        </div>
    <?php endif; ?>
</div>

<!-- Detail Transaction Modal -->
<div class="hidden fixed inset-0 overflow-y-auto" id="transaction-detail-modal">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                            Detail Transaksi
                        </h3>
                        <div class="mt-2" id="transaction-detail-content">
                            <!-- Content will be loaded here via AJAX -->
                            <div class="text-center py-8">
                                <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm modal-close">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date filter toggle
    const dateFilter = document.getElementById('date-filter');
    const customDateRange = document.getElementById('custom-date-range');
    
    dateFilter.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateRange.classList.remove('hidden');
        } else {
            customDateRange.classList.add('hidden');
        }
    });
    
    // View detail button handler
    document.querySelectorAll('.view-detail').forEach(button => {
        button.addEventListener('click', function() {
            const transactionId = this.getAttribute('data-id');
            showTransactionDetail(transactionId);
        });
    });
    
    // Modal close handler
    document.querySelectorAll('.modal-close').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('transaction-detail-modal').classList.add('hidden');
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('search');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#transactions-table-body tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    });
    
    // Status filter
    const statusFilter = document.getElementById('status-filter');
    statusFilter.addEventListener('change', function() {
        const status = this.value;
        const rows = document.querySelectorAll('#transactions-table-body tr');
        
        rows.forEach(row => {
            if (!status || row.querySelector('span').textContent.toLowerCase().includes(status)) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    });
});

function showTransactionDetail(transactionId) {
    const modal = document.getElementById('transaction-detail-modal');
    const content = document.getElementById('transaction-detail-content');
    
    // Show modal with loading spinner
    modal.classList.remove('hidden');
    content.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i></div>';
    
    // Fetch transaction details
    fetch(`get_transaction_detail.php?id=${transactionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Format the transaction details
                const transaction = data.transaction;
                const details = data.details;
                
                let html = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Informasi Transaksi</h4>
                            <div class="space-y-2">
                                <p><span class="text-gray-600">Invoice:</span> ${transaction.kode_invoice}</p>
                                <p><span class="text-gray-600">Tanggal:</span> ${new Date(transaction.tgl).toLocaleDateString()}</p>
                                <p><span class="text-gray-600">Batas Waktu:</span> ${new Date(transaction.batas_waktu).toLocaleDateString()}</p>
                                <p><span class="text-gray-600">Tanggal Bayar:</span> ${transaction.tgl_bayar ? new Date(transaction.tgl_bayar).toLocaleDateString() : '-'}</p>
                                <p><span class="text-gray-600">Status:</span> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusClass(transaction.status)}">${capitalizeFirstLetter(transaction.status)}</span></p>
                                <p><span class="text-gray-600">Pembayaran:</span> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${transaction.dibayar === 'dibayar' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">${transaction.dibayar === 'dibayar' ? 'Dibayar' : 'Belum Dibayar'}</span></p>
                            </div>
                            
                            <h4 class="font-medium text-gray-900 mt-4 mb-2">Informasi Member</h4>
                            <div class="space-y-2">
                                <p><span class="text-gray-600">Nama:</span> ${transaction.nama_member}</p>
                                <p><span class="text-gray-600">Alamat:</span> ${transaction.alamat_member || '-'}</p>
                                <p><span class="text-gray-600">Telepon:</span> ${transaction.tlp_member || '-'}</p>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Detail Pesanan</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paket</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                `;
                
                details.forEach(detail => {
                    html += `
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">${detail.nama_paket} (${detail.jenis})</td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">Rp${detail.harga.toLocaleString()}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">${detail.qty}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">Rp${(detail.harga * detail.qty).toLocaleString()}</td>
                        </tr>
                    `;
                });
                
                html += `
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="3" class="px-3 py-2 text-right text-sm font-medium text-gray-900">Subtotal</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">Rp${transaction.subtotal.toLocaleString()}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="px-3 py-2 text-right text-sm font-medium text-gray-900">Diskon</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">Rp${transaction.diskon.toLocaleString()}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="px-3 py-2 text-right text-sm font-medium text-gray-900">Biaya Tambahan</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">Rp${transaction.biaya_tambahan.toLocaleString()}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="px-3 py-2 text-right text-sm font-medium text-gray-900">Pajak (${transaction.pajak}%)</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">Rp${transaction.pajak_harga.toLocaleString()}</td>
                                        </tr>
                                        <tr class="border-t border-gray-200">
                                            <td colspan="3" class="px-3 py-2 text-right text-sm font-bold text-gray-900">Total</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-bold text-gray-900">Rp${transaction.total_harga.toLocaleString()}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <div class="mt-4">
                                <h4 class="font-medium text-gray-900 mb-2">Keterangan</h4>
                                <p class="text-sm text-gray-600">${transaction.keterangan || 'Tidak ada keterangan tambahan'}</p>
                            </div>
                        </div>
                    </div>
                `;
                
                content.innerHTML = html;
            } else {
                content.innerHTML = `<div class="text-center py-8 text-red-500">${data.message || 'Gagal memuat detail transaksi'}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = '<div class="text-center py-8 text-red-500">Terjadi kesalahan saat memuat detail transaksi</div>';
        });
}

function getStatusClass(status) {
    const classes = {
        'baru': 'bg-blue-100 text-blue-800',
        'proses': 'bg-yellow-100 text-yellow-800',
        'selesai': 'bg-green-100 text-green-800',
        'diambil': 'bg-purple-100 text-purple-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
</script>