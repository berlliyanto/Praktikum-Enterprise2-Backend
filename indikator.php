<?php
require('config/connection.php');

header("Content-Type: application/json");
$jsonData = file_get_contents('php://input', true);
$data = json_decode($jsonData);

if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    $query = mysqli_query($conn, 'SELECT * FROM indikator');
        $result = array();
        while ($row = mysqli_fetch_array($query)) {
            array_push(
                $result,
                array(
                    'id' => $row['id'],
                    'emg' => $row['emg'],
                    'running' => $row['running'],
                )
            );
        }
        echo json_encode(
            array('data' => $result),
            JSON_PRETTY_PRINT
        );
}elseif($_SERVER['REQUEST_METHOD'] === 'PUT'){
    $emg = $data->emg;
    $running = $data->running;
    $query = mysqli_query($conn,"UPDATE indikator SET emg = '$emg', running = '$running'");
    if($query){
        echo json_encode(
            "Sukses Ubah Indikator"
        );
    }else{
        echo json_encode(
            "Gagal Ubah Indikator"
        );
    }
}

$conn->close();
?>