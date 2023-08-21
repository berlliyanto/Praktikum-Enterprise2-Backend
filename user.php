<?php
require_once('./config/connection.php');
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (empty($_GET)) {
        $query = mysqli_query($conn, 'SELECT * FROM user');
        $resultData = array();
        while ($row = mysqli_fetch_array($query)) {
            array_push(
                $resultData,
                array(
                    'iduser' => $row['iduser'],
                    'username' => $row['username'],
                    'password' => $row['password'],
                    'nama' => $row['nama'],
                    'level' => $row['level'],
                    'tanggal' => $row['tanggal']
                )
            );
        }
        echo json_encode(
            array('data' => $resultData),
            JSON_PRETTY_PRINT
        );
    } else {
        $querySingle = mysqli_query($conn, 'SELECT * FROM user WHERE iduser =' . $_GET['id']);
        $result = array();
        while ($row = $querySingle->fetch_assoc()) {
            $result = array(
                'id' => $row['iduser'],
                'username' => $row['username'],
                'password' => $row['password'],
                'nama' => $row['nama'],
                'level' => $row['level'],
                'tanggal' => $row['tanggal']
            );
        }
        echo json_encode(
            $result,
            JSON_PRETTY_PRINT
        );
    }
}elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    $data = json_decode(file_get_contents('php://input'), true);
    
    $id = $data['id'];
    $newUsername = $data['username'];
    $newPassword = $data['password'];
    $newNama = $data['nama'];
    $newLevel = $data['level'];

    $sql = "UPDATE user SET username='$newUsername', password='$newPassword', nama='$newNama', level='$newLevel' WHERE iduser='$id'";
    if (mysqli_query($conn, $sql)) {
        $response = [
            'success' => true,
            'message' => 'Data pengguna berhasil diubah.'
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
elseif($_SERVER['REQUEST_METHOD']==='DELETE'){
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['iduser'];

    $sql = "DELETE FROM user WHERE iduser = $id";
    $deleteUser = mysqli_query($conn,$sql);
    if($deleteUser){
        echo json_decode('berhasil hapus');
    } else {
        $response1 = [
            'success' => false,
            'message' => 'Gagal menghapus data pengguna: ' . mysqli_error($conn)
        ];
        echo json_decode("Gagal Hapus");
    }
    
}
?>
