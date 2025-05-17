<?php
session_start();
include '../database/connect.php';

header('Content-Type: application/json');

// Cek apakah request-nya POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Ambil data dari POST
$invoice = isset($_POST['invoice']) ? trim($_POST['invoice']) : '';
$paymentStatus = isset($_POST['payment-status']) ? $_POST['payment-status'] : '';
$orderStatus = isset($_POST['order-status']) ? $_POST['order-status'] : '';

// Validasi input
if (empty($invoice) || empty($paymentStatus) || empty($orderStatus)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Escape input untuk keamanan
$invoice = mysqli_real_escape_string($mysqli, $invoice);
$paymentStatus = mysqli_real_escape_string($mysqli, $paymentStatus);
$orderStatus = mysqli_real_escape_string($mysqli, $orderStatus);

// Cek apakah invoice ada
$sql_check = "SELECT id FROM tb_transaksi WHERE kode_invoice = '$invoice'";
$result = mysqli_query($mysqli, $sql_check);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . mysqli_error($mysqli)]);
    exit;
}

if (mysqli_num_rows($result) === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invoice tidak ditemukan',
        'invoice_exists' => false
    ]);
    exit;
}

// Update transaksi
$sql_update = "UPDATE tb_transaksi 
               SET dibayar = '$paymentStatus', status = '$orderStatus' 
               WHERE kode_invoice = '$invoice'";
$update_result = mysqli_query($mysqli, $sql_update);

if (!$update_result) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . mysqli_error($mysqli),
        'invoice_exists' => true
    ]);
    exit;
}

if (mysqli_affected_rows($mysqli) > 0) {
    echo json_encode([
        'success' => true,
        'message' => 'Status transaksi berhasil diperbarui',
        'invoice_exists' => true
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Tidak ada perubahan data',
        'invoice_exists' => true
    ]);
}

// Tutup koneksi
mysqli_close($mysqli);
?>
