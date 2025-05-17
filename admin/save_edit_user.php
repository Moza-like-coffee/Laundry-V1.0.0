<?php
include '../database/connect.php';

// Set response header
header('Content-Type: application/json');

// Get form data
$userId = isset($_POST['nama-lengkap']) ? intval($_POST['nama-lengkap']) : null;
$newNama = isset($_POST['nama-lengkap-baru']) ? trim($_POST['nama-lengkap-baru']) : null;
$newUsername = isset($_POST['username-baru']) ? trim($_POST['username-baru']) : null;
$newPassword = isset($_POST['password-baru']) ? $_POST['password-baru'] : null;
$newRole = isset($_POST['role-baru']) ? trim($_POST['role-baru']) : null;

// Validate required fields
if (!$userId || !$newNama || !$newUsername || !$newRole) {
    echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi!']);
    exit;
}

// Validate role
$validRoles = ['admin', 'kasir', 'owner'];
if (!in_array($newRole, $validRoles)) {
    echo json_encode(['success' => false, 'message' => 'Role tidak valid!']);
    exit;
}

try {
    // Check if username already exists (excluding current user)
    $checkQuery = "SELECT id FROM tb_user WHERE username = ? AND id != ?";
    $checkStmt = $mysqli->prepare($checkQuery);
    $checkStmt->bind_param("si", $newUsername, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username sudah digunakan!']);
        exit;
    }
    // hash version
    // if (!empty($newPassword)) {
    //     // If password is being changed
    //     $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    //     $query = "UPDATE tb_user SET nama = ?, username = ?, password = ?, role = ? WHERE id = ?";
    //     $stmt = $mysqli->prepare($query);
    //     $stmt->bind_param("ssssi", $newNama, $newUsername, $hashedPassword, $newRole, $userId);
    // } else {

    // Prepare update query
    if (!empty($newPassword)) {
        // If password is being changed (stored as plaintext)
        $query = "UPDATE tb_user SET nama = ?, username = ?, password = ?, role = ? WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ssssi", $newNama, $newUsername, $newPassword, $newRole, $userId);
    } else {
        // Without password change
        $query = "UPDATE tb_user SET nama = ?, username = ?, role = ? WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sssi", $newNama, $newUsername, $newRole, $userId);
    }

    // Execute update
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Data pengguna berhasil diperbarui!',
            'data' => [
                'nama' => $newNama,
                'username' => $newUsername,
                'role' => $newRole
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data pengguna.']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$mysqli->close();
?>