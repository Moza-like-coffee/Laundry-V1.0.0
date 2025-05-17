<?php
include '../database/connect.php';

header('Content-Type: application/json');

// Get outlet ID and optional user ID from request
$outletId = isset($_POST['outlet_id']) ? intval($_POST['outlet_id']) : 0;
$userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;

// Prepare query based on parameters
if ($userId) {
    // Query untuk user spesifik
    $query = "SELECT id, nama, username, role FROM tb_user WHERE id_outlet = ? AND id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ii", $outletId, $userId);
} else {
    // Query untuk semua user di outlet
    $query = "SELECT id, nama, username, role FROM tb_user WHERE id_outlet = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $outletId);
}

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'id' => $row['id'],
            'nama' => $row['nama'],
            'username' => $row['username'],
            'role' => $row['role']
        ];
    }

    // Jika query spesifik user, kembalikan objek tunggal atau kosong
    echo $userId ? json_encode($users[0] ?? null) : json_encode($users);
    $stmt->close();
} else {
    echo json_encode(['error' => 'Database query error']);
}

$mysqli->close();
?>