<?php
include '../database/connect.php';

$query = "SELECT p.id, p.nama_paket, p.jenis, p.harga, o.nama as outlet_nama 
          FROM tb_paket p 
          JOIN tb_outlet o ON p.id_outlet = o.id 
          ORDER BY p.id DESC";
$result = mysqli_query($mysqli, $query);
?>

<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-indigo-500 to-blue-600 rounded-lg p-6 shadow-lg text-white">
        <h2 class="text-2xl font-bold flex items-center gap-3">
            <i class="fas fa-box-open"></i>
            Daftar Produk
        </h2>
        <p class="opacity-90 mt-1">Daftar lengkap paket layanan yang tersedia</p>
    </div>

    <!-- Stats Cards -->
    <?php
    $totalProduk = mysqli_num_rows($result);
    $queryJenis = "SELECT jenis, COUNT(*) as jumlah FROM tb_paket GROUP BY jenis";
    $resultJenis = mysqli_query($mysqli, $queryJenis);
    $jenisProduk = [];
    while ($row = mysqli_fetch_assoc($resultJenis)) {
        $jenisProduk[$row['jenis']] = $row['jumlah'];
    }
    ?>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="text-gray-500 text-sm">Total Produk</div>
            <div class="text-2xl font-bold text-blue-600"><?= $totalProduk ?></div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="text-gray-500 text-sm">Produk Kiloan</div>
            <div class="text-2xl font-bold text-green-600"><?= $jenisProduk['kiloan'] ?? 0 ?></div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <div class="text-gray-500 text-sm">Produk Bed Cover</div>
            <div class="text-2xl font-bold text-purple-600"><?= $jenisProduk['bed_cover'] ?? 0 ?></div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <div class="text-gray-500 text-sm">Produk Selimut</div>
            <div class="text-2xl font-bold text-purple-600"><?= $jenisProduk['selimut'] ?? 0 ?></div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <div class="text-gray-500 text-sm">Produk Kaos</div>
            <div class="text-2xl font-bold text-purple-600"><?= $jenisProduk['kaos'] ?? 0 ?></div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <div class="text-gray-500 text-sm">Produk Lain</div>
            <div class="text-2xl font-bold text-purple-600"><?= $jenisProduk['lain'] ?? 0 ?></div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Paket</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outlet</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php 
                    $rowColor = true;
                    while ($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <tr class="<?= ($rowColor ? 'bg-white' : 'bg-gray-50') ?> hover:bg-blue-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?= $row['id'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?= $row['nama_paket'] ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?= $row['jenis'] === 'kiloan' ? 'bg-blue-100 text-blue-800' : 
                                   ($row['jenis'] === 'selimut' ? 'bg-purple-100 text-purple-800' : 
                                   ($row['jenis'] === 'bed_cover' ? 'bg-amber-100 text-amber-800' : 
                                   'bg-gray-100 text-gray-800')) ?>">
                                <?= ucfirst(str_replace('_', ' ', $row['jenis'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                                    <i class="fas fa-store text-xs"></i>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900"><?= $row['outlet_nama'] ?></div>
                                </div>
                            </div>
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

    <?php if(mysqli_num_rows($result) == 0): ?>
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <i class="fas fa-box-open text-4xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900">Tidak ada produk terdaftar</h3>
        <p class="mt-1 text-sm text-gray-500">Tambahkan produk baru untuk memulai</p>
    </div>
    <?php endif; ?>
</div>