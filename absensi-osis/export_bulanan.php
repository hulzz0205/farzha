<?php
require_once "koneksi.php";

$bulan = $_GET['bulan']; // contoh: 2026-01

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=absensi_$bulan.xls");

echo "Nama\tKelas\tJabatan\tJudul Rapat\tTanggal\n";

$bulan = $_GET['bulan']; // contoh: 01
$tahun = $_GET['tahun']; // contoh: 2026

$data = mysqli_query($koneksi, "
    SELECT 
        a.nama,
        a.kelas,
        a.jabatan,
        DATE_FORMAT(ab.waktu_absen, '%d-%m-%Y %H:%i:%s') AS waktu_absen
    FROM absensi ab
    JOIN anggota_osis a ON ab.anggota_id = a.id
    WHERE MONTH(ab.waktu_absen) = '$bulan'
      AND YEAR(ab.waktu_absen) = '$tahun'
    ORDER BY ab.waktu_absen ASC
");

while ($row = mysqli_fetch_assoc($data)) {
    echo "{$row['nama']}\t{$row['kelas']}\t{$row['jabatan']}\t{$row['judul']}\t{$row['tanggal']}\n";
}
