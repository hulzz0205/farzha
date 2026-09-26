<?php

session_start();

if (!isset($_SESSION['admin_login'])) {
    header("Location: login_rekap.php");
    exit;
}

require_once "koneksi.php";


/* =========================
   STATISTIK
========================= */

$q_anggota = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM anggota_osis"
);

$total_anggota = mysqli_fetch_assoc($q_anggota)['total'];


$q_rapat = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM rapat"
);

$total_rapat = mysqli_fetch_assoc($q_rapat)['total'];


$q_rapat_buka = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM rapat
     WHERE status='buka'"
);

$rapat_buka = mysqli_fetch_assoc($q_rapat_buka)['total'];


$q_absensi = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM absensi"
);

$total_absensi = mysqli_fetch_assoc($q_absensi)['total'];

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 25px;
        }

        .header {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,.06);
        }

        .header h1 {
            margin: 0 0 8px;
        }

        .header p {
            color: #666;
            margin: 0;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,.06);
        }

        .stat h3 {
            margin: 0 0 10px;
            font-size: 15px;
            color: #666;
        }

        .stat .number {
            font-size: 30px;
            font-weight: bold;
        }

        .menu {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-decoration: none;
            color: #222;
            box-shadow: 0 3px 10px rgba(0,0,0,.06);
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .card .icon {
            font-size: 35px;
            margin-bottom: 10px;
        }

        .card h3 {
            margin: 0 0 7px;
        }

        .card p {
            margin: 0;
            color: #666;
        }

        .logout {
            display: inline-block;
            margin-top: 20px;
            color: #dc3545;
            text-decoration: none;
        }

        @media (max-width: 800px) {

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .menu {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 500px) {

            .stats {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 15px;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <div class="header">

        <h1>👋 Dashboard Admin</h1>

        <p>
            Selamat datang,
            <b><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></b>
        </p>

    </div>


    <div class="stats">

        <div class="stat">

            <h3>👥 Total Anggota</h3>

            <div class="number">
                <?= $total_anggota ?>
            </div>

        </div>


        <div class="stat">

            <h3>📅 Total Rapat</h3>

            <div class="number">
                <?= $total_rapat ?>
            </div>

        </div>


        <div class="stat">

            <h3>🟢 Rapat Dibuka</h3>

            <div class="number">
                <?= $rapat_buka ?>
            </div>

        </div>


        <div class="stat">

            <h3>✅ Total Absensi</h3>

            <div class="number">
                <?= $total_absensi ?>
            </div>

        </div>

    </div>


    <div class="menu">

        <a
            href="admin_anggota.php"
            class="card"
        >

            <div class="icon">
                👥
            </div>

            <h3>Kelola Anggota</h3>

            <p>
                Tambah, edit, dan hapus anggota OSIS.
            </p>

        </a>


        <a
            href="admin_rapat.php"
            class="card"
        >

            <div class="icon">
                📅
            </div>

            <h3>Kelola Rapat</h3>

            <p>
                Buat, buka, tutup, edit, dan kelola rapat.
            </p>

        </a>


        <a
            href="rekap.php"
            class="card"
        >

            <div class="icon">
                📊
            </div>

            <h3>Rekap Absensi</h3>

            <p>
                Lihat data hadir dan tidak hadir.
            </p>

        </a>

    </div>


    <a
        href="logout_rekap.php"
        class="logout"
    >
        🚪 Logout Admin
    </a>

</div>

</body>

</html>