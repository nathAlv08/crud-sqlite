<?php
$db = new PDO('sqlite:tugas.db');

// Buat tabel jika belum ada
$db->exec("CREATE TABLE IF NOT EXISTS tugas (id INTEGER PRIMARY KEY AUTOINCREMENT, deskripsi TEXT NOT NULL)");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $method = $_GET['method'] ?? false;
  switch ($method) {
    case 'tambah':
      $tugasBaru = $_POST['tugas'] ?? '';
      if ($tugasBaru) {
        $stmt = $db->prepare("INSERT INTO tugas (deskripsi) VALUES (:deskripsi)");
        $stmt->execute([':deskripsi' => $tugasBaru]);
      }
      header('Location: ' . $_SERVER['SCRIPT_NAME']);
      break;

    case 'hapus':
      $id = $_POST['id'] ?? null;
      if ($id !== null) {
        $stmt = $db->prepare("DELETE FROM tugas WHERE id = :id");
        $stmt->execute([':id' => $id]);
      }
      header('Location: ' . $_SERVER['SCRIPT_NAME']);
      break;

    case 'update':
      $id = $_POST['id'] ?? null;
      $tugas = $_POST['tugas'] ?? '';
      if ($id !== null && $tugas) {
        $stmt = $db->prepare("UPDATE tugas SET deskripsi = :deskripsi WHERE id = :id");
        $stmt->execute([':deskripsi' => $tugas, ':id' => $id]);
      }
      header('Location: ' . $_SERVER['SCRIPT_NAME']);
      break;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $method = $_GET['method'] ?? false;
  switch ($method) {
    case 'hapus-semua':
      $db->exec("DELETE FROM tugas");
      header('Location: ' . $_SERVER['SCRIPT_NAME']);
      break;

    case 'edit':
      $id = $_GET['id'] ?? null;
      if ($id !== null) {
        $stmt = $db->prepare("SELECT * FROM tugas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
          echo renderFormEdit($row['id'], $row['deskripsi']);
          exit;
        }
      }
      header('Location: ' . $_SERVER['SCRIPT_NAME']);
      break;

    default:
      $stmt = $db->query("SELECT * FROM tugas");
      $tugas = $stmt->fetchAll(PDO::FETCH_ASSOC);
      echo renderListingTugas($tugas);
      break;
  }
}

function renderListingTugas($daftarTugas) {
  $html = "<h1>Apa lagi?</h1>
  <form name='apalagi' method='post' action='?method=tambah'>
    <input name='tugas' type='text' placeholder='tulis tugas' />
    <button type='submit'>Simpan</button>
  </form>
  <h2>Daftar tugas</h2>";

  if ($daftarTugas) {
    $html .= "<ol>";
    foreach ($daftarTugas as $tugas) {
      $html .= <<<HTML
      <li>
        {$tugas['deskripsi']}
        <a href="?method=edit&id={$tugas['id']}">EDIT</a>
        <form style="display:inline-block" method="post" action="?method=hapus">
          <input type="hidden" name="id" value="{$tugas['id']}" />
          <button type="submit">🗑️</button>
        </form>
      </li>
      HTML;
    }
    $html .= "</ol>";
  } else {
    $html .= "Belum ada tugas";
  }

  $html .= "<hr /><a href='?method=hapus-semua'>HAPUS SEMUA ☢️</a>";
  return $html;
}

function renderFormEdit($id, $deskripsi) {
  return <<<HTML
<h1>EDIT</h1>
<form name="update" method="post" action="?method=update">
  <input type="hidden" name="id" value="{$id}" />
  <input name="tugas" value="{$deskripsi}" type="text" placeholder="tulis tugas" />
  <button type="submit">Simpan</button>
</form>
HTML;
}
?>
