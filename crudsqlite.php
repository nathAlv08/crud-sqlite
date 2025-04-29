<?php 
$sql = "SELECT * FROM tugas";

try {
    $conn = new \PDO('sqlite:./database.db');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    echo "Error: " . $e->getMessage();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_GET['method'] ?? null;
  
    switch ($method) {
      case 'tambah':
        $sql = 'INSERT INTO tugas (deskripsi, waktu) VALUES (:deskripsi, :waktu)';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':deskripsi', $_POST['tugas']);
        $stmt->bindParam(':waktu', $_POST['waktu'], PDO::PARAM_INT);
        $stmt->execute();
        header('Location: ' . $_SERVER['SCRIPT_NAME']);
        break;
    }
}
?>