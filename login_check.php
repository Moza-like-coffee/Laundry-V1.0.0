<?php
session_start();
include 'database/connect.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM tb_user WHERE username='$username' AND password='$password'";
$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $_SESSION['username'] = $data['username'];
    $_SESSION['role'] = $data['role'];
    $_SESSION['nama'] = $data['nama'];
    header("Location: admin/dashboard.php");
} else {
    echo "<script>alert('Username atau Password salah!'); window.location='login.php';</script>";
}
?>
