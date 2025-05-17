<?php
include 'database/connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $invoice = $_POST['invoice'];

    $stmt = $conn->prepare("
        SELECT 
            t.kode_invoice,
            o.nama AS outlet,
            u.nama AS kasir,
            m.nama AS customer,
            t.status,
            t.dibayar
        FROM tb_transaksi t
        JOIN tb_outlet o ON t.id_outlet = o.id
        JOIN tb_user u ON t.id_user = u.id
        JOIN tb_member m ON t.id_member = m.id
        WHERE t.kode_invoice = ?
    ");
    $stmt->bind_param("s", $invoice);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            "success" => true,
            "data" => $row
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Invoice tidak ditemukan!"
        ]);
    }

    $stmt->close();
    $conn->close();
}
?>
