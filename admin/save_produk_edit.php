<?php
session_start();
include '../database/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPaket = $_POST['nama-paket'] ?? '';
    $namaBaru = $_POST['nama-paket-baru'] ?? '';
    $jenisBaru = $_POST['jenis-produk-2'] ?? '';
    $hargaBaru = $_POST['harga-baru'] ?? '';
    
    if (!empty($idPaket) && !empty($namaBaru) && !empty($jenisBaru) && !empty($hargaBaru)) {
        $stmt = $mysqli->prepare("UPDATE tb_paket SET nama_paket = ?, jenis = ?, harga = ? WHERE id = ?");
        $stmt->bind_param("ssdi", $namaBaru, $jenisBaru, $hargaBaru, $idPaket);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi']);
    }
}
?>