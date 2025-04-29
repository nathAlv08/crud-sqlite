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
        exit;
        break;
    case 'hapus':
        $sql = 'DELETE FROM tugas WHERE id = :id';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
        $stmt->execute();
        header('Location: ' . $_SERVER['SCRIPT_NAME']);
        exit;
        break;
    case 'hapus_semua':
        $conn->exec('DELETE FROM tugas');
        header('Location: ' . $_SERVER['SCRIPT_NAME']);
        exit;
        break;
    case 'update':
        $sql = 'UPDATE tugas SET deskripsi = :deskripsi, waktu = :waktu WHERE id = :id';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
        $stmt->bindParam(':deskripsi', $_POST['tugas']);
        $stmt->bindParam(':waktu', $_POST['waktu'], PDO::PARAM_INT);
        $stmt->execute();
        header('Location: ' . $_SERVER['SCRIPT_NAME']);
        exit;
        break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $method = $_GET['method'] ?? null;
    $lihat = $_GET['lihat'] ?? null;
  
    if ($lihat) {
      // Menampilkan detail tugas berdasarkan ID
      $sql = 'SELECT * FROM tugas WHERE id = :id';
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':id', $lihat, PDO::PARAM_INT);
      $stmt->execute();
      $tugasDetail = $stmt->fetch(PDO::FETCH_ASSOC);
  
      // Jika tugas ditemukan, tampilkan detail
      if ($tugasDetail) {
        echo renderDetailTugas($tugasDetail);
      } else {
        echo "<p>Tugas tidak ditemukan.</p>";
      }
    } elseif ($method === 'edit' && isset($_GET['id'])) {
      // Menampilkan form edit tugas
      $sql = 'SELECT * FROM tugas WHERE id = :id';
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
      $stmt->execute();
      $tugas = $stmt->fetch(PDO::FETCH_ASSOC);
      echo renderFormEdit($tugas);
    } else {
      // Menampilkan daftar tugas
      $sql = 'SELECT * FROM tugas';
      $stmt = $conn->query($sql);
      $tugas = $stmt->fetchAll(PDO::FETCH_ASSOC);
      echo renderListingTugas($tugas);
    }
  }
  
  function renderListingTugas($daftarTugas) {
    $list = "<ol>";
  
    foreach ($daftarTugas as $t) {
      $list .= <<<HTML
      <li>
        {$t['deskripsi']} ({$t['waktu']} menit)
        <a href="?lihat={$t['id']}">[Lihat Detail]</a> 
        <a href="?method=edit&id={$t['id']}">[Edit]</a>
        <form style="display:inline-block" method="post" action="?method=hapus">
          <input type="hidden" name="id" value="{$t['id']}" />
          <button type="submit">🗑️</button>
        </form>
      </li>
      HTML;
    }
    $list .= "</ol>";
    $list .= <<<HTML
        <form method="post" action="?method=hapus_semua" onsubmit="return confirm('Yakin ingin menghapus semua tugas?');">
        <button type="submit" style="color:red;">🗑️ Hapus Semua</button>
        </form>
HTML;
    return <<<HTML
    <!DOCTYPE html>
    <html lang="id">
    <head>
      <meta charset="UTF-8">
      <title>Manajemen List Tugas</title>
    </head>
    <body>
      <h1>Manajemen List Tugas</h1>
      <form method="post" action="?method=tambah">
        <input name="tugas" type="text" placeholder="Masukkan tugas" required />
        <input name="waktu" type="text" placeholder="Waktu yang diperlukan" required />
        <button type="submit">Tambah</button>
      </form>
      <h2>Daftar Tugas</h2>
      {$list}
    
    </body>
    </html>
  HTML;
  }
  
  function renderFormEdit($tugas) {
    return <<<HTML
    <h1>Edit Tugas</h1>
    <form method="post" action="?method=update">
      <input type="hidden" name="id" value="{$tugas['id']}" />
      <input name="tugas" type="text" value="{$tugas['deskripsi']}" required />
      <input name="waktu" type="text" value="{$tugas['waktu']}" required />
      <button type="submit">Simpan Perubahan</button>
    </form>
    <a href="{$_SERVER['SCRIPT_NAME']}">Kembali ke Daftar Tugas</a>
  HTML;
  }
  
  function renderDetailTugas($tugas) {
    return <<<HTML
    <h1>Detail Tugas</h1>
    <p><strong>Tugas:</strong> {$tugas['deskripsi']}</p>
    <p><strong>Waktu yang diperlukan:</strong> {$tugas['waktu']} menit</p>
    <a href="{$_SERVER['SCRIPT_NAME']}">Kembali ke Daftar Tugas</a>
  HTML;
  }
?>

