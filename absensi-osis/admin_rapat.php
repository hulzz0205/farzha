<?php
session_start();

if (!isset($_SESSION['admin_login'])) {
    header("Location: login_rekap.php");
    exit;
}

require_once "koneksi.php";


/* =========================
   TAMBAH RAPAT
========================= */

if (isset($_POST['tambah'])) {

    $judul = trim($_POST['judul']);
    $tanggal = $_POST['tanggal'];

    if ($judul != "" && $tanggal != "") {

        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO rapat (judul, tanggal, status)
             VALUES (?, ?, 'buka')"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ss",
            $judul,
            $tanggal
        );

        mysqli_stmt_execute($stmt);
    }

    header("Location: admin_rapat.php");
    exit;
}


/* =========================
   BUKA RAPAT
========================= */

if (isset($_GET['buka'])) {

    $id = $_GET['buka'];

    $stmt = mysqli_prepare(
        $koneksi,
        "UPDATE rapat
         SET status='buka'
         WHERE id=?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id
    );

    mysqli_stmt_execute($stmt);

    header("Location: admin_rapat.php");
    exit;
}


/* =========================
   TUTUP RAPAT
========================= */

if (isset($_GET['tutup'])) {

    $id = $_GET['tutup'];

    $stmt = mysqli_prepare(
        $koneksi,
        "UPDATE rapat
         SET status='tutup'
         WHERE id=?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id
    );

    mysqli_stmt_execute($stmt);

    header("Location: admin_rapat.php");
    exit;
}


/* =========================
   HAPUS RAPAT
========================= */

if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];


    /* CEK APAKAH SUDAH ADA ABSENSI */

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id
         FROM absensi
         WHERE rapat_id=?
         LIMIT 1"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id
    );

    mysqli_stmt_execute($stmt);

    $hasil = mysqli_stmt_get_result($stmt);


    if (mysqli_num_rows($hasil) == 0) {

        /* Belum ada absensi → boleh hapus */

        $stmt = mysqli_prepare(
            $koneksi,
            "DELETE FROM rapat
             WHERE id=?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $id
        );

        mysqli_stmt_execute($stmt);

    } else {

        /*
         * Sudah ada absensi.
         * Jangan dihapus agar riwayat aman.
         */
    }

    header("Location: admin_rapat.php");
    exit;
}


/* =========================
   DATA RAPAT
========================= */

$rapat = mysqli_query(
    $koneksi,
    "SELECT *
     FROM rapat
     ORDER BY tanggal DESC, id DESC"
);

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Kelola Rapat</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        h2 {
            margin-top: 0;
        }

        h3 {
            margin-top: 20px;
        }

        .menu {
            margin-bottom: 20px;
        }

        .menu a {
            text-decoration: none;
            margin-right: 10px;
        }

        hr {
            border: 0;
            border-top: 1px solid #ddd;
            margin: 25px 0;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 11px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 6px;
        }

        .btn-tambah {
            background: #198754;
            color: white;
        }

        .btn-edit {
            background: #ffc107;
            color: black;
            text-decoration: none;
            padding: 7px 10px;
            border-radius: 5px;
            display: inline-block;
            margin: 2px;
        }

        .btn-hapus {
            background: #dc3545;
            color: white;
            text-decoration: none;
            padding: 7px 10px;
            border-radius: 5px;
            display: inline-block;
            margin: 2px;
        }

        .btn-rekap {
            background: #0d6efd;
            color: white;
            text-decoration: none;
            padding: 7px 10px;
            border-radius: 5px;
            display: inline-block;
            margin: 2px;
        }

        .btn-buka {
            background: #198754;
            color: white;
            text-decoration: none;
            padding: 7px 10px;
            border-radius: 5px;
            display: inline-block;
            margin: 2px;
        }

        .btn-tutup {
            background: #6c757d;
            color: white;
            text-decoration: none;
            padding: 7px 10px;
            border-radius: 5px;
            display: inline-block;
            margin: 2px;
        }

        .status-buka {
            color: #198754;
            font-weight: bold;
        }

        .status-tutup {
            color: #dc3545;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }

        @media (max-width: 700px) {

            .container {
                padding: 18px;
            }

            table {
                font-size: 13px;
            }

            th,
            td {
                padding: 7px;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <h2>📅 Kelola Rapat OSIS</h2>


    <div class="menu">

        <a href="admin_dashboard.php">
            ← Dashboard
        </a>

        <a href="admin_anggota.php">
            👥 Kelola Anggota
        </a>

        <a href="rekap.php">
            📊 Rekap
        </a>

        <a href="logout_rekap.php">
            🚪 Logout
        </a>

    </div>


    <hr>


    <!-- =========================
         TAMBAH RAPAT
    ========================= -->

    <h3>➕ Buat Rapat</h3>

    <form method="post">

        <label>Judul Rapat</label>

        <input
            type="text"
            name="judul"
            placeholder="Contoh: Rapat Rutin OSIS"
            required
        >


        <label>Tanggal Rapat</label>

        <input
            type="date"
            name="tanggal"
            required
        >


        <button
            type="submit"
            name="tambah"
            class="btn-tambah"
        >
            + Buat Rapat
        </button>

    </form>


    <hr>


    <!-- =========================
         DAFTAR RAPAT
    ========================= -->

    <h3>📋 Daftar Rapat</h3>

    <table>

        <tr>

            <th>No</th>

            <th>Judul</th>

            <th>Tanggal</th>

            <th>Status</th>

            <th>Aksi</th>

        </tr>


        <?php

        $no = 1;

        while ($r = mysqli_fetch_assoc($rapat)) {

        ?>

        <tr>

            <td>
                <?= $no ?>
            </td>


            <td>
                <?= htmlspecialchars($r['judul']) ?>
            </td>


            <td>
                <?= htmlspecialchars($r['tanggal']) ?>
            </td>


            <td>

                <?php if ($r['status'] == 'buka') { ?>

                    <span class="status-buka">
                        🟢 BUKA
                    </span>

                <?php } else { ?>

                    <span class="status-tutup">
                        🔴 TUTUP
                    </span>

                <?php } ?>

            </td>


            <td>

                <a
                    href="rekap.php?rapat_id=<?= $r['id'] ?>"
                    class="btn-rekap"
                >
                    📊 Rekap
                </a>


                <a
                    href="edit_rapat.php?id=<?= $r['id'] ?>"
                    class="btn-edit"
                >
                    ✏️ Edit
                </a>


                <?php if ($r['status'] == 'buka') { ?>

                    <a
                        href="admin_rapat.php?tutup=<?= $r['id'] ?>"
                        class="btn-tutup"
                        onclick="return confirm('Tutup rapat ini? Setelah ditutup, anggota tidak dapat melakukan absensi lagi.')"
                    >
                        🔒 Tutup
                    </a>

                <?php } else { ?>

                    <a
                        href="admin_rapat.php?buka=<?= $r['id'] ?>"
                        class="btn-buka"
                        onclick="return confirm('Buka kembali rapat ini?')"
                    >
                        🔓 Buka
                    </a>

                <?php } ?>


                <a
                    href="admin_rapat.php?hapus=<?= $r['id'] ?>"
                    class="btn-hapus"
                    onclick="return confirm('Yakin ingin menghapus rapat ini? Rapat yang sudah memiliki absensi tidak akan terhapus.')"
                >
                    🗑️ Hapus
                </a>

            </td>

        </tr>

        <?php

            $no++;

        }

        ?>

    </table>

</div>

</body>

</html>