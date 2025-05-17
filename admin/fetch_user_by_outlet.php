<?php
include '../database/connect.php';

if (isset($_POST['outlet_id'])) {
    $outletId = $_POST['outlet_id'];
    $sql = "SELECT id, nama, username FROM tb_user WHERE role != 'admin' AND id_outlet = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $outletId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $options = "";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $options .= "<option value='".$row['id']."' data-username='".$row['username']."'>".$row['nama']."</option>";
        }
    } else {
        $options = "<option value='' disabled>No users available for this outlet</option>";
    }
    echo $options;
}
?>