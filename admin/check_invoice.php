<?php
session_start();
include '../database/connect.php';

header('Content-Type: application/json');

$invoice = isset($_GET['invoice']) ? trim($_GET['invoice']) : '';

if (empty($invoice)) {
    echo json_encode(['exists' => false]);
    exit;
}

// Escape input untuk keamanan
$invoice = mysqli_real_escape_string($mysqli, $invoice);

// Query cek invoice
$sql = "SELECT id FROM tb_transaksi WHERE kode_invoice = '$invoice'";
$result = mysqli_query($mysqli, $sql);

if (!$result) {
    echo json_encode(['exists' => false]);
    exit;
}

// Jika ada hasil, berarti invoice ditemukan
$exists = mysqli_num_rows($result) > 0;

echo json_encode(['exists' => $exists]);


mysqli_close($mysqli);
?>
