<?php
// session_start();
// include 'database/connect.php';

// $username = $_POST['username'];
// $password = $_POST['password'];

// // Versi tanpa password hashing (TIDAK DISARANKAN untuk produksi)
// $query = "SELECT * FROM tb_user WHERE username = ? AND password = ?";
// $stmt = $mysqli->prepare($query);

// if ($stmt) {
//     $stmt->bind_param("ss", $username, $password);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     if ($result->num_rows > 0) {
//         $data = $result->fetch_assoc();
        
//         // Set session variables
//         $_SESSION['user_id'] = $data['id']; // ID user
//         $_SESSION['username'] = $data['username'];
//         $_SESSION['role'] = $data['role'];
//         $_SESSION['nama'] = $data['nama'];
//         $_SESSION['id_outlet'] = $data['id_outlet']; // ID outlet
        
//         header("Location: admin/dashboard.php");
//         exit();
//     } else {
//         $_SESSION['login_error'] = 'Username atau Password salah!';
//         header("Location: login.php");
//         exit();
//     }
    
//     $stmt->close();
// } else {
//     $_SESSION['login_error'] = 'Terjadi kesalahan sistem!';
//     header("Location: login.php");
//     exit();
// }

// $mysqli->close();
?>


<?php
session_start();
include 'database/connect.php';

// Validasi sederhana sebelum proses DB
if (!isset($_POST['username'], $_POST['password'])) {
    $_SESSION['login_error'] = 'Harap isi semua kolom!';
    header("Location: login.php");
    exit();
}

$username = trim($_POST['username']);
$password = trim($_POST['password']);

// Pastikan tidak kosong setelah di-trim
if ($username === '' || $password === '') {
    $_SESSION['login_error'] = 'Username dan Password tidak boleh kosong!';
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM tb_user WHERE username = ?";
$stmt = $mysqli->prepare($query);

if ($stmt) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();

        // Cek password
        if (password_verify($password, $data['password'])) {
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = $data['role'];
            $_SESSION['nama'] = $data['nama'];
            $_SESSION['id_outlet'] = $data['id_outlet']; // jika diperlukan
            $_SESSION['user_id'] = $data['id']; // jika diperlukan

            header("Location: admin/dashboard.php");
            exit();
        }
    }

    // Jika username tidak ditemukan atau password salah
    $_SESSION['login_error'] = 'Username atau Password salah!';
    header("Location: login.php");
    exit();

    $stmt->close();
} else {
    // Jika prepare gagal
    $_SESSION['login_error'] = 'Terjadi kesalahan pada sistem. Silakan coba lagi.';
    header("Location: login.php");
    exit();
}

$mysqli->close();
?>
