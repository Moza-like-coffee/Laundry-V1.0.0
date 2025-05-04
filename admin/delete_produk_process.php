<?php
require '../database/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama-paket'])) {
    $packageId = $_POST['nama-paket'];
    $query = "DELETE FROM tb_paket WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $packageId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>