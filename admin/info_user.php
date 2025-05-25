<?php
session_start();
include '../database/connect.php';

$query = "SELECT u.id, u.nama, u.username, u.role, o.nama as outlet_nama 
          FROM tb_user u 
          LEFT JOIN tb_outlet o ON u.id_outlet = o.id 
          ORDER BY u.nama ASC";
$result = mysqli_query($mysqli, $query);
$totalUsers = mysqli_num_rows($result);

// Get counts for each role
$roleCounts = [];
$roleQuery = "SELECT role, COUNT(*) as count FROM tb_user GROUP BY role";
$roleResult = mysqli_query($mysqli, $roleQuery);
while ($row = mysqli_fetch_assoc($roleResult)) {
    $roleCounts[$row['role']] = $row['count'];
}
?>

<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-indigo-500 to-blue-600 rounded-lg p-4 md:p-6 shadow-lg text-white">
        <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2 md:gap-3">
            <i class="fas fa-users"></i>
            Informasi Pengguna
        </h2>
        <p class="opacity-90 mt-1 text-sm md:text-base">Daftar lengkap pengguna yang terdaftar dalam sistem</p>
    </div>

    <?php if(mysqli_num_rows($result) > 0): ?>
        <!-- Stats Cards - Stack on mobile -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
            <div class="bg-white rounded-lg shadow p-3 md:p-4 border-l-4 border-blue-500">
                <div class="text-gray-500 text-xs md:text-sm">Total Pengguna</div>
                <div class="text-xl md:text-2xl font-bold text-blue-600"><?= $totalUsers ?></div>
            </div>
            
            <?php foreach ($roleCounts as $role => $count): ?>
                <div class="bg-white rounded-lg shadow p-3 md:p-4 border-l-4 border-<?= 
                    $role === 'admin' ? 'purple' : 
                    ($role === 'kasir' ? 'green' : 'yellow') 
                ?>-500">
                    <div class="text-gray-500 text-xs md:text-sm"><?= ucfirst($role) ?></div>
                    <div class="text-xl md:text-2xl font-bold text-<?= 
                        $role === 'admin' ? 'purple' : 
                        ($role === 'kasir' ? 'green' : 'yellow') 
                    ?>-600"><?= $count ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Table - Scrollable on mobile -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-4 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Username</th>
                            <th class="px-4 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Outlet</th>
                            <th class="px-4 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-4 py-2 md:px-6 md:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php 
                        $rowColor = true;
                        while($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr class="<?= ($rowColor ? 'bg-white' : 'bg-gray-50') ?> hover:bg-blue-50 transition-colors">
                            <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 md:h-10 md:w-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                                        <i class="fas fa-user text-sm md:text-base"></i>
                                    </div>
                                    <div class="ml-2 md:ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['nama']) ?></div>
                                        <div class="text-xs text-gray-500"><?= htmlspecialchars($row['username']) ?></div>
                                        <div class="text-xs text-gray-500 md:hidden"><?= $row['outlet_nama'] ? htmlspecialchars($row['outlet_nama']) : '-' ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap hidden sm:table-cell">
                                <div class="text-sm text-gray-900"><?= htmlspecialchars($row['username']) ?></div>
                            </td>
                            <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap hidden md:table-cell">
                                <div class="text-sm text-gray-900"><?= $row['outlet_nama'] ? htmlspecialchars($row['outlet_nama']) : '-' ?></div>
                            </td>
                            <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?= 
                                        $row['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 
                                        ($row['role'] === 'kasir' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800')
                                    ?>">
                                    <?= ucfirst($row['role']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap hidden sm:table-cell">
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
            </div>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow p-6 md:p-8 text-center">
            <i class="fas fa-user-slash text-3xl md:text-4xl text-gray-300 mb-3 md:mb-4"></i>
            <h3 class="text-md md:text-lg font-medium text-gray-900">Tidak ada pengguna terdaftar</h3>
            <p class="mt-1 text-xs md:text-sm text-gray-500">Tambahkan pengguna baru untuk memulai</p>
        </div>
    <?php endif; ?>
</div>