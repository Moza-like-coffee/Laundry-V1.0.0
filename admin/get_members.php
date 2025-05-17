<?php
require '../database/connect.php';

header('Content-Type: application/json');

try {
    $query = "SELECT id, nama, alamat, tlp FROM tb_member ORDER BY nama";
    $result = $mysqli->query($query);
    
    $members = [];
    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }
    
    echo json_encode($members);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}