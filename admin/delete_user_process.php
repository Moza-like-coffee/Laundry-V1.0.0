<?php
session_start();
include '../database/connect.php';

// Cek apakah request berasal dari method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: menu_user.php?status=error&message=" . urlencode('Metode request tidak valid'));
    exit();
}

// Cek role admin
$allowed_roles = ['admin'];
$user_role = $_SESSION['role'] ?? null;

if (!in_array($user_role, $allowed_roles)) {
    header("Location: menu_user.php?status=error&message=" . urlencode('Anda tidak memiliki izin untuk melakukan aksi ini'));
    exit();
}

// Validasi input
$userId = $_POST['nama-lengkap'] ?? null; // Ini sebenarnya ID user
$username = $_POST['username'] ?? null;

if (empty($userId) || empty($username)) {
    header("Location: menu_user.php?status=error&message=" . urlencode('Data pengguna tidak valid'));
    exit();
}

// Cek apakah pengguna yang akan dihapus adalah admin
$checkAdminQuery = "SELECT role FROM tb_user WHERE id = ?";
$stmt = $mysqli->prepare($checkAdminQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: menu_user.php?status=error&message=" . urlencode('Pengguna tidak ditemukan'));
    exit();
}

$userData = $result->fetch_assoc();
if ($userData['role'] === 'admin') {
    header("Location: menu_user.php?status=error&message=" . urlencode('Tidak dapat menghapus akun admin'));
    exit();
}

// Mulai transaksi
$mysqli->begin_transaction();

try {
    // 1. Hapus data terkait pengguna di tabel lain (jika ada)
    // Contoh: $mysqli->query("DELETE FROM tabel_lain WHERE user_id = $userId");
    
    // 2. Hapus pengguna dari tb_user
    $deleteQuery = "DELETE FROM tb_user WHERE id = ? AND username = ?";
    $stmt = $mysqli->prepare($deleteQuery);
    $stmt->bind_param("is", $userId, $username);
    $stmt->execute();
    
    // Cek apakah ada baris yang terpengaruh
    if ($stmt->affected_rows === 0) {
        throw new Exception('Gagal menghapus pengguna atau data tidak ditemukan');
    }
    
    // Commit transaksi jika semua query berhasil
    $mysqli->commit();
    
    $_SESSION['status'] = 'success';
    $_SESSION['message'] = 'Pengguna berhasil dihapus!';
    header("Location: menu_user.php?status=success");
    exit();
    
} catch (Exception $e) {
    // Rollback transaksi jika ada error
    $mysqli->rollback();
    
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Gagal menghapus pengguna: ' . $e->getMessage();
    header("Location: menu_user.php?status=error");
    exit();
}
?>