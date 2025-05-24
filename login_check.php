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

$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM tb_user WHERE username = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    
    if (password_verify($password, $data['password'])) {
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['nama'] = $data['nama'];
        
        header("Location: admin/dashboard.php");
        exit();
    } else {
        $_SESSION['login_error'] = 'Username atau Password salah!';
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['login_error'] = 'Username atau Password salah!';
    header("Location: login.php");
    exit();
}

$stmt->close();
$mysqli->close();
?>