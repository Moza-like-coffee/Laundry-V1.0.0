<?php
session_start();
include '../database/connect.php';

$query = "SELECT * FROM tb_outlet ORDER BY nama ASC";
$result = mysqli_query($mysqli, $query);
$totalOutlets = mysqli_num_rows($result);
?>

<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-indigo-500 to-blue-600 rounded-lg p-6 shadow-lg text-white">
        <h2 class="text-2xl font-bold flex items-center gap-3">
            <i class="fas fa-store-alt"></i>
            Informasi Outlet
        </h2>
        <p class="opacity-90 mt-1">Daftar lengkap outlet yang terdaftar dalam sistem</p>
    </div>

    <?php if(mysqli_num_rows($result) > 0): ?>
        <!-- Stats Cards - Responsive Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <div class="text-gray-500 text-sm">Total Outlet</div>
                <div class="text-2xl font-bold text-blue-600"><?= $totalOutlets ?></div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="text-gray-500 text-sm">Outlet Aktif</div>
                <div class="text-2xl font-bold text-green-600"><?= $totalOutlets ?></div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
                <div class="text-gray-500 text-sm">Terakhir Diupdate</div>
                <div class="text-2xl font-bold text-purple-600"><?= date('d M Y') ?></div>
            </div>
        </div>

        <!-- Responsive Table Container -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <!-- Table for larger screens -->
                <table class="min-w-full divide-y divide-gray-200 hidden md:table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Outlet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php 
                        $rowColor = true;
                        while($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr class="<?= ($rowColor ? 'bg-white' : 'bg-gray-50') ?> hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['nama']) ?></div>
                                        <div class="text-sm text-gray-500">ID: <?= $row['id'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= htmlspecialchars($row['alamat']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-phone-alt text-blue-500"></i>
                                    <?= htmlspecialchars($row['tlp']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            </td>
                        </tr>
                        <?php 
                        $rowColor = !$rowColor;
                        endwhile; 
                        ?>
                    </tbody>
                </table>

                <!-- Cards for mobile screens -->
                <div class="md:hidden space-y-3 p-4">
                    <?php 
                    mysqli_data_seek($result, 0); // Reset result pointer
                    while($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 mr-3">
                                <i class="fas fa-store"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-medium text-gray-900 truncate"><?= htmlspecialchars($row['nama']) ?></h3>
                                <p class="text-xs text-gray-500">ID: <?= $row['id'] ?></p>
                                
                                <div class="mt-2 text-sm text-gray-700">
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                                        <span class="truncate"><?= htmlspecialchars($row['alamat']) ?></span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-phone-alt text-gray-400 mr-2"></i>
                                        <span><?= htmlspecialchars($row['tlp']) ?></span>
                                    </div>
                                </div>
                                
                                <div class="mt-2">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <i class="fas fa-store-slash text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900">Tidak ada outlet terdaftar</h3>
            <p class="mt-1 text-sm text-gray-500">Tambahkan outlet baru untuk memulai</p>
        </div>
    <?php endif; ?>
</div>