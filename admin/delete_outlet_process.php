<?php
include '../database/connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $outletId = $_POST['nama-outlet'];

    // 1. Set NULL pada id_outlet di tb_user
    $sql1 = "UPDATE tb_user SET id_outlet = NULL WHERE id_outlet = ?";
    $stmt1 = mysqli_prepare($mysqli, $sql1);
    mysqli_stmt_bind_param($stmt1, 'i', $outletId);
    mysqli_stmt_execute($stmt1);
    mysqli_stmt_close($stmt1);

    // 2. Set NULL pada id_outlet di tb_paket
    $sql2 = "UPDATE tb_paket SET id_outlet = NULL WHERE id_outlet = ?";
    $stmt2 = mysqli_prepare($mysqli, $sql2);
    mysqli_stmt_bind_param($stmt2, 'i', $outletId);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    // 3. Set NULL pada id_outlet di tb_transaksi
    $sql3 = "UPDATE tb_transaksi SET id_outlet = NULL WHERE id_outlet = ?";
    $stmt3 = mysqli_prepare($mysqli, $sql3);
    mysqli_stmt_bind_param($stmt3, 'i', $outletId);
    mysqli_stmt_execute($stmt3);
    mysqli_stmt_close($stmt3);

    // kalo mau opsi hapus unblock
    // // 1. Hapus semua data di tb_user yang terkait dengan outlet
    // $sql1 = "DELETE FROM tb_user WHERE id_outlet = ?";
    // $stmt1 = mysqli_prepare($mysqli, $sql1);
    // mysqli_stmt_bind_param($stmt1, 'i', $outletId);
    // mysqli_stmt_execute($stmt1);
    // mysqli_stmt_close($stmt1);

    // // 2. Hapus semua data di tb_paket yang terkait dengan outlet
    // $sql2 = "DELETE FROM tb_paket WHERE id_outlet = ?";
    // $stmt2 = mysqli_prepare($mysqli, $sql2);
    // mysqli_stmt_bind_param($stmt2, 'i', $outletId);
    // mysqli_stmt_execute($stmt2);
    // mysqli_stmt_close($stmt2);

    // // 3. Hapus semua transaksi yang terkait dengan outlet
    // $sql3 = "DELETE FROM tb_transaksi WHERE id_outlet = ?";
    // $stmt3 = mysqli_prepare($mysqli, $sql3);
    // mysqli_stmt_bind_param($stmt3, 'i', $outletId);
    // mysqli_stmt_execute($stmt3);
    // mysqli_stmt_close($stmt3);

    // 4. Hapus outlet
    $sql4 = "DELETE FROM tb_outlet WHERE id = ?";
    $stmt4 = mysqli_prepare($mysqli, $sql4);
    mysqli_stmt_bind_param($stmt4, 'i', $outletId);

    if (mysqli_stmt_execute($stmt4)) {
        mysqli_stmt_close($stmt4);
        header('Location: menu_outlet.php?status=success');
        exit();
    } else {
        mysqli_stmt_close($stmt4);
        header('Location: menu_outlet.php?status=error');
        exit();
    }
} else {
    header('Location: menu_outlet.php');
    exit();
}
?>
