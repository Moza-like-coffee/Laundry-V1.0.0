<?php
require '../database/connect.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    
    if ($isAdmin || isset($_POST['outlet_id'])) {
        $query = "SELECT id, nama_paket, jenis, harga FROM tb_paket";
        
        if (!$isAdmin) {
            $outletId = $_POST['outlet_id'];
            $query .= " WHERE id_outlet = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("i", $outletId);
        } else {
            $stmt = $mysqli->prepare($query);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $packages = [];
        while ($row = $result->fetch_assoc()) {
            $packages[] = $row;
        }
        
        echo json_encode($packages);
        exit;
    }
}

echo json_encode([]);