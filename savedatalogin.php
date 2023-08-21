<?php

require('./config/connection.php');

$data = json_decode(file_get_contents('php://input',true));

if (empty($data->username)) {
    http_response_code(400);
    echo json_encode(array("message" => "Mohon isi semua field."));
    exit();
}

$username = $data->username;

$getIdUser = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");
while ($getId = mysqli_fetch_assoc($getIdUser)) {
    $iduser = $getId['iduser'];
}
if ($getId > 0) {
    $query = "INSERT INTO data_login_user(iduser,username) VALUES ('$iduser','$username')";
    $queryResult = $conn->query($query);

    // Login berhasil
    $result = array();
    while ($fetchData = $queryResult->fetch_assoc()) {
        $result[] = $fetchData;
    }
}


http_response_code(200);
echo json_encode($getIdUser);

?>