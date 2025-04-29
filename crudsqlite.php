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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen List Tugas</title>
</head>
<body>
    <h1>Manajemen List Tugas</h1>

    <h2>Tambah Tugas</h2>
    <form method="POST" action="?method=tambah">
        <input type="text" name="tugas" placeholder="Masukkan tugas" required>
        <input type="number" name="waktu" placeholder="Waktu yang diperlukan (menit)" required>
        <button type="submit">Tambah</button>
    </form>
    

    <h2>Daftar Tugas</h2>
    <ol>
        <?php foreach ($tugas as $t): ?>
            <li>
                <?= htmlspecialchars($t['deskripsi']) ?> - <?= htmlspecialchars($t['waktu']) ?> menit
                <a href="?method=edit&id=<?= $t['id'] ?>">[Edit]</a>
                <form method="POST" style="display:inline-block" action="?method=hapus" onsubmit="return confirm('Hapus tugas ini?');">
                    <input type="hidden" name="id" value="<?= $t['id'] ?>" />
                    <button type="submit">[Hapus]</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ol>


    <?php if (isset($tugas) && isset($tugas['id'])): ?>
        <hr>
        <h2>Edit Tugas</h2>
        <form method="POST" action="?method=update">
            <input type="hidden" name="id" value="<?= $tugas['id'] ?>" />
            <input type="text" name="tugas" value="<?= htmlspecialchars($tugas['deskripsi']) ?>" required>
            <input type="number" name="waktu" value="<?= htmlspecialchars($tugas['waktu']) ?>" required>
            <button type="submit">Simpan Perubahan</button>
            <a href="<?= $_SERVER['SCRIPT_NAME'] ?>">[Batal]</a>
        </form>
    <?php endif; ?>

    <?php if (isset($selectedTugas)): ?>
        <h2>Detail Tugas</h2>
        <p><strong>Tugas:</strong> <?= htmlspecialchars($selectedTugas['deskripsi']) ?></p>
        <p><strong>Waktu yang diperlukan:</strong> <?= htmlspecialchars($selectedTugas['waktu']) ?> menit</p>
    <?php endif; ?>
</body>
</html>
