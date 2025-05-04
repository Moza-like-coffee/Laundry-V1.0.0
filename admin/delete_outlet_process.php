<?php
include '../database/connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $outletId = $_POST['nama-outlet'];

    // Hapus outlet dari database
    $sql = "DELETE FROM tb_outlet WHERE id = ?";
    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $outletId);

    if (mysqli_stmt_execute($stmt)) {
        // Jika penghapusan berhasil, redirect dengan parameter status
        header('Location: menu_outlet.php?status=success');
        exit();
    } else {
        // Jika gagal, redirect dengan parameter error
        header('Location: menu_outlet.php?status=error');
        exit();
    }

    mysqli_stmt_close($stmt);
} else {
    // Redirect jika tidak ada POST
    header('Location: menu_outlet.php');
    exit();
}
?>
