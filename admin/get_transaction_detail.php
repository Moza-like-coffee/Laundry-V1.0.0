<?php
session_start();
include '../database/connect.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID transaksi tidak valid']);
    exit;
}

$transactionId = $_GET['id'];

// Get transaction data
$query = "SELECT t.*, m.nama as nama_member, m.alamat as alamat_member, m.tlp as tlp_member, 
                 o.nama as nama_outlet, u.nama as nama_user
          FROM tb_transaksi t
          JOIN tb_member m ON t.id_member = m.id
          JOIN tb_outlet o ON t.id_outlet = o.id
          JOIN tb_user u ON t.id_user = u.id
          WHERE t.id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $transactionId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Transaksi tidak ditemukan']);
    exit;
}

$transaction = $result->fetch_assoc();

// Get transaction details
$detailQuery = "SELECT d.*, p.nama_paket, p.jenis, p.harga
                FROM tb_detail_transaksi d
                JOIN tb_paket p ON d.id_paket = p.id
                WHERE d.id_transaksi = ?";
$detailStmt = $mysqli->prepare($detailQuery);
$detailStmt->bind_param('i', $transactionId);
$detailStmt->execute();
$detailResult = $detailStmt->get_result();

$details = [];
while ($row = $detailResult->fetch_assoc()) {
    $details[] = $row;
}

echo json_encode([
    'success' => true,
    'transaction' => $transaction,
    'details' => $details
]);