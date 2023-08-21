<?php
require('./config/connection.php');

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['username']) && isset($data['password']) && isset($data['level']) && isset($data['nama'])) {
    $username = $data['username'];
    $password = $data['password'];
    $nama = $data['nama'];
    $level = $data['level'];

    $sql = "INSERT INTO user (username, password, nama, level) VALUES ('$username', '$password','$nama', '$level')";

    if ($conn->query($sql) === true) {
        $response = array('status' => 'success', 'message' => 'Data berhasil disimpan');
    } else {
        $response = array('status' => 'error', 'message' => 'Gagal menyimpan data: ');
    }


    $conn->close();
} else {
    $response = array('status' => 'error', 'message' => 'Data yang dibutuhkan tidak lengkap');
}

header('Content-Type: application/json');
echo json_encode($response);

?>