<?php
require('./config/connection.php');

// Menerima data JSON dari Arduino
date_default_timezone_set('Asia/Jakarta');
$jsonData = file_get_contents('php://input', true);
$data = json_decode($jsonData);
switch ($_GET['method']) {
    default:
        echo "No Method";
        break;

    case 'start':
        header('Content-Type: application/json');
        $value = $data->value;
        $amountData = mysqli_query($conn, "SELECT * FROM amountbk WHERE idbk = 1");
        $fetch = mysqli_fetch_assoc($amountData);
        $amount = $fetch['amount'];
        if ($amount > 0) {
            $sql = "INSERT INTO pickplace (masuk, stamped, verified,cycle_time, state) VALUES ('$value','0','0','0','1')";
            if ($conn->query($sql) === TRUE) {
                echo "Benda Kerja Masuk";
            } else {
                echo "Error: " . $sql . "<br>";
            }
        } else {
            echo json_encode("Stock Kurang Dari 0");
        }

        break;

    case 'stamped':
        header('Content-Type: application/json');
        $value = $data->value;
        $amountData = mysqli_query($conn, "SELECT * FROM amountbk WHERE idbk = 1");
        $fetch = mysqli_fetch_assoc($amountData);
        $amount = $fetch['amount'];
        if ($amount > 0) {
            $queryupdate = mysqli_query($conn, "UPDATE pickplace SET stamped = $value ORDER BY idcount DESC LIMIT 1");
            if ($queryupdate) {
                echo json_encode("Benda Kerja Stamped");
            } else {
                echo json_encode("Gagal Stamped");
            }
        } else {
            echo json_encode("Stock Kurang Dari 0");
        }

        break;

    case 'verified':
        header('Content-Type: application/json');
        $value = $data->value;
        $amountData = mysqli_query($conn, "SELECT * FROM amountbk WHERE idbk = 1");
        $fetch = mysqli_fetch_assoc($amountData);
        $amount = $fetch['amount'];
        if ($amount > 0) {
            $queryupdate = mysqli_query($conn, "UPDATE pickplace SET verified = $value ORDER BY idcount DESC LIMIT 1");
            if ($queryupdate) {
                echo json_encode("Benda Kerja Verified");
            } else {
                echo json_encode("Gagal Verified");
            }
        } else {
            echo json_encode("Stock Kurang Dari 0");
        }

        break;

    case 'cycle':
        header('Content-Type: application/json');
        $cycle_time = $data->cycle_time;

        $queryupdate = mysqli_query($conn, "UPDATE pickplace SET cycle_time = $cycle_time ORDER BY idcount DESC LIMIT 1");
        if ($queryupdate) {
            echo json_encode("Cycle time masuk");
        } else {
            echo json_encode("Gagal cycle");
        }
        break;

    case 'get':
        $query = mysqli_query($conn, "SELECT * FROM pickplace");
        $result = array();
        while ($fetch = mysqli_fetch_array($query)) {
            array_push(
                $result,
                array(
                    "status" => $fetch['status'],
                    "waktucycle" => $fetch['waktucycle'],
                    "state" => $fetch['state'],
                )
            );
        }
        echo json_encode(
            array('data' => $result)
        );
        break;

    case 'count':
        header('Content-Type: application/json');
        $queryVerified = mysqli_query($conn, "SELECT COUNT(idcount) AS verified FROM pickplace WHERE verified='1' AND state = '1'");
        $queryStamped = mysqli_query($conn, "SELECT COUNT(idcount) AS stamped FROM pickplace WHERE stamped='1' AND state = '1'");
        $queryMulai = mysqli_query($conn, "SELECT COUNT(idcount) AS mulai FROM pickplace WHERE masuk='1' AND state = '1'");
        $queryCycle = mysqli_query($conn, "SELECT cycle_time AS cycle FROM pickplace ORDER BY idcount DESC LIMIT 1");

        $fetchVerified = mysqli_fetch_assoc($queryVerified);
        $fetchStamped = mysqli_fetch_assoc($queryStamped);
        $fetchMulai = mysqli_fetch_assoc($queryMulai);
        $fetchCycle = mysqli_fetch_assoc($queryCycle);
        $Verified = $fetchVerified['verified'];
        $Stamped = $fetchStamped['stamped'];
        $Mulai = $fetchMulai['mulai'];
        $Cycle = $fetchCycle['cycle'];
        $result = array(
            "verified" => $Verified,
            "stamped" => $Stamped,
            "mulai" => $Mulai,
            "cycle" => $Cycle,
        );
        echo json_encode(
            array(
                'data' => $result
            ),
            JSON_PRETTY_PRINT
        );
        break;

    case 'reset':
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $queryUpdate = mysqli_query($conn, "UPDATE pickplace SET state = '0'");
            if ($queryUpdate) {
                $response = [
                    'success' => true,
                    'message' => 'Berhasil Reset.'
                ];
                echo json_encode($response);
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Gagal mengubah data pengguna: ' . mysqli_error($conn)
                ];
                echo json_encode($response);
            }
        }
        break;

    case 'kurangi':
        header('Content-Type: application/json');
        $value = $data->value;
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $amountData = mysqli_query($conn, "SELECT * FROM amountbk WHERE idbk = 1");
            $fetch = mysqli_fetch_assoc($amountData);
            $amount = $fetch['amount'];
            if ($amount > 0) {
                $queryUpdate = mysqli_query($conn, "UPDATE amountbk SET amount = amount - $value WHERE idbk = 1");
                if ($queryUpdate) {
                    $response = [
                        'success' => true,
                        'message' => 'Berhasil Kurangi.'
                    ];
                    echo json_encode($response);
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Gagal mengubah data pengguna: ' . mysqli_error($conn)
                    ];
                    echo json_encode($response);
                }
            } else {
                echo json_encode(
                    "Stock Kurang Dari 0"
                );
            }

        }
        break;

    case 'tambah':
        header('Content-Type: application/json');
        $value = $data->value;

        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $queryUpdate = mysqli_query($conn, "UPDATE amountbk SET amount = amount + $value WHERE idbk = 1");
            if ($queryUpdate) {
                $response = [
                    'success' => true,
                    'message' => 'Berhasil Tambah.'
                ];
                echo json_encode($response);
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Gagal mengubah data pengguna: ' . mysqli_error($conn)
                ];
                echo json_encode($response);
            }
        }
        break;
    case 'getAmount':
        $query = mysqli_query($conn, "SELECT * FROM amountbk");
        $result = array();
        while ($fetch = mysqli_fetch_array($query)) {
            array_push(
                $result,
                array(
                    "idbk" => $fetch['idbk'],
                    "amount" => $fetch['amount'],
                )
            );
        }
        echo json_encode(
            array('data' => $result)
        );
        break;
    case 'history':
        if (empty($_GET['startDate']) && empty($_GET['endDate'])) {
            $query = "SELECT 
                        COUNT(*) AS totalData,
                        SUM(masuk = 1) AS totalMasuk,
                        SUM(stamped = 1) AS totalStamped,
                        SUM(verified = 1) AS totalVerified,
                        DATE(tanggal) AS tanggalSekarang
                        FROM pickplace GROUP BY DATE(tanggal)";

            $result = mysqli_query($conn, $query);
            if ($result) {
                $resultData = array();
                while ($row = mysqli_fetch_array($result)) {
                    array_push(
                        $resultData,
                        array(
                            'tanggal' => $row['tanggalSekarang'],
                            'totalData' => $row['totalData'],
                            'totalMasuk' => $row['totalMasuk'],
                            'totalStamped' => $row['totalStamped'],
                            'totalVerified' => $row['totalVerified']
                        )
                    );
                }
                header('Content-Type: application/json');
                echo json_encode(
                    array('data' => $resultData),
                    JSON_PRETTY_PRINT
                );
            } else {
                echo "Query error: " . mysqli_error($conn);
            }
        } else {
            $startDate = $_GET['startDate'];
            $endDate = $_GET['endDate'];

            $query = "SELECT 
                        COUNT(*) AS totalData,
                        SUM(masuk = 1) AS totalMasuk,
                        SUM(stamped = 1) AS totalStamped,
                        SUM(verified = 1) AS totalVerified,
                        DATE(tanggal) AS tanggalSekarang
                        FROM pickplace 
                        WHERE tanggal BETWEEN DATE('$startDate') AND DATE('$endDate') GROUP BY DATE(tanggal)";

            $result = mysqli_query($conn, $query);
            if ($result) {
                $resultData = array();
                while ($row = mysqli_fetch_array($result)) {
                    array_push(
                        $resultData,
                        array(
                            'tanggal' => $row['tanggalSekarang'],
                            'totalData' => $row['totalData'],
                            'totalMasuk' => $row['totalMasuk'],
                            'totalStamped' => $row['totalStamped'],
                            'totalVerified' => $row['totalVerified']
                        )
                    );
                }
                header('Content-Type: application/json');
                echo json_encode(
                    array('data' => $resultData),
                    JSON_PRETTY_PRINT
                );
            } else {
                echo "Query error: " . mysqli_error($conn);
            }
        }
}
$conn->close();
?>