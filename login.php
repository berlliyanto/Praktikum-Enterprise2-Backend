<?php
require('./config/connection.php');

header("Content-Type: application/json; charset=UTF-8");
$data = json_decode(file_get_contents("php://input"));

if (empty($data->username) || empty($data->password)) {
    http_response_code(400);
    echo json_encode(array("message" => "Mohon isi semua field."));
    exit();
}

$username = $data->username;
$password = $data->password;

$query = "SELECT * FROM user WHERE username = '$username' AND password = '$password'";
$queryResult = $conn->query($query);

if ($queryResult->num_rows === 0) {
    http_response_code(401);
    echo json_encode(array("message" => "Username atau password salah."));
    exit();
}

// Login berhasil
$result = array();
while ($fetchData = $queryResult->fetch_assoc()) {
    $result[] = $fetchData;
}

http_response_code(200);
echo json_encode($result,JSON_PRETTY_PRINT);
?>