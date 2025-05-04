<?php
include '../database/connect.php';

if (isset($_GET['id'])) {
    $outletId = $_GET['id'];

    // Pastikan query di sini benar-benar mengembalikan data yang diinginkan
    $sql = "SELECT alamat, tlp FROM tb_outlet WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $outletId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $outletData = $result->fetch_assoc();
        echo json_encode($outletData);  // Pastikan ini adalah format JSON
    } else {
        echo json_encode(['error' => 'No data found for the given outlet ID']);
    }
} else {
    echo json_encode(['error' => 'Outlet ID is missing']);
}
?>
