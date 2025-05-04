<?php
include '../database/connect.php';

// Ambil data dari form
$old_id = $_POST['nama-outlet'];
$new_nama = $_POST['nama-outlet-baru'];
$new_lokasi = $_POST['lokasi-outlet-baru'];
$new_telp = $_POST['no-telp-baru'];

// Validasi sederhana
if (empty($old_id) || empty($new_nama) || empty($new_lokasi) || empty($new_telp)) {
    echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi.']);
    exit;
}

// Update ke database
$sql = "UPDATE tb_outlet 
        SET nama = ?, alamat = ?, tlp = ?
        WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sssi", $new_nama, $new_lokasi, $new_telp, $old_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Data outlet berhasil diperbarui.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data outlet.']);
}

$stmt->close();
$mysqli->close();
?>
