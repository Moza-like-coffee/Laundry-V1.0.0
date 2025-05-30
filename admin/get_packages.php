<?php
require '../database/connect.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['outlet_id'])) {
    $outletId = $_POST['outlet_id'];
    
    // Query yang benar: selalu filter berdasarkan outlet_id, baik untuk admin maupun non-admin
    $query = "SELECT id, nama_paket, jenis, harga FROM tb_paket WHERE id_outlet = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $outletId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $packages = [];
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
    
    echo json_encode($packages);
    exit;
}

echo json_encode([]);