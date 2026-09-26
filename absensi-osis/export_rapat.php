<?php

require_once "koneksi.php";

error_reporting(0);

// ============================
// CEK RAPAT ID
// ============================

if (!isset($_GET['rapat_id']) || !is_numeric($_GET['rapat_id'])) {
    die("Rapat tidak ditemukan");
}

$rapat_id = (int) $_GET['rapat_id'];

// ============================
// AMBIL DATA RAPAT
// ============================

$stmtRapat = mysqli_prepare(
    $koneksi,
    "SELECT judul, tanggal FROM rapat WHERE id = ? LIMIT 1"
);

mysqli_stmt_bind_param($stmtRapat, "i", $rapat_id);
mysqli_stmt_execute($stmtRapat);

$resultRapat = mysqli_stmt_get_result($stmtRapat);
$rapat = mysqli_fetch_assoc($resultRapat);

if (!$rapat) {
    die("Data rapat tidak ditemukan");
}

$judul = $rapat['judul'];
$tanggal = date('d-m-Y', strtotime($rapat['tanggal']));

// ============================
// NAMA FILE EXCEL
// ============================

$judul_file = preg_replace('/[^A-Za-z0-9\- ]/', '', $judul);
$judul_file = trim($judul_file);

if ($judul_file == '') {
    $judul_file = "absensi_rapat";
}

$judul_file = str_replace(' ', '_', $judul_file);

// ============================
// HEADER EXCEL
// ============================

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"{$judul_file}.xls\"");
header("Pragma: no-cache");
header("Expires: 0");

// ============================
// QUERY ABSENSI
// ============================

$stmtAbsensi = mysqli_prepare(
    $koneksi,
    "SELECT 
        a.nama,
        a.kelas,
        a.jabatan,
        ab.waktu
     FROM absensi ab
     JOIN anggota_osis a ON ab.anggota_id = a.id
     WHERE ab.rapat_id = ?
     ORDER BY ab.waktu ASC"
);

mysqli_stmt_bind_param($stmtAbsensi, "i", $rapat_id);
mysqli_stmt_execute($stmtAbsensi);

$data = mysqli_stmt_get_result($stmtAbsensi);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Absensi <?= htmlspecialchars($judul) ?></title>
</head>

<body>

<table border="1" cellpadding="6" cellspacing="0">

    <tr>
        <th colspan="4">
            Absensi OSIS
            <br>
            <?= htmlspecialchars($judul) ?>
            <br>
            <?= $tanggal ?>
        </th>
    </tr>

    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Kelas</th>
        <th>Jabatan</th>
        <th>Waktu Absen</th>
    </tr>

    <?php
    $no = 1;

    if (mysqli_num_rows($data) > 0) {

        while ($d = mysqli_fetch_assoc($data)) {
    ?>

    <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($d['nama']) ?></td>
        <td><?= htmlspecialchars($d['kelas']) ?></td>
        <td><?= htmlspecialchars($d['jabatan']) ?></td>
        <td>
            <?= date('d-m-Y H:i:s', strtotime($d['waktu'])) ?>
        </td>
    </tr>

    <?php
        }

    } else {
    ?>

    <tr>
        <td colspan="5">
            Belum ada anggota yang melakukan absensi.
        </td>
    </tr>

    <?php
    }
    ?>

</table>

</body>
</html>