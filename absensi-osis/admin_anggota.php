<?php

session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

if (!isset($_SESSION['admin_login'])) {
    header("Location: login_rekap.php");
    exit;
}

require_once "koneksi.php";


// ============================
// TAMBAH ANGGOTA
// ============================

if (isset($_POST['tambah'])) {

    $nama = trim($_POST['nama']);
    $kelas = trim($_POST['kelas']);
    $jabatan = trim($_POST['jabatan']);

    if ($nama != "" && $kelas != "" && $jabatan != "") {

        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO anggota_osis (nama, kelas, jabatan)
             VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sss",
            $nama,
            $kelas,
            $jabatan
        );

        mysqli_stmt_execute($stmt);

        header("Location: admin_anggota.php");
        exit;
    }
}


// ============================
// HAPUS ANGGOTA
// ============================

if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    // Cek apakah anggota sudah memiliki data absensi
    $stmtCek = mysqli_prepare(
        $koneksi,
        "SELECT COUNT(*) AS total
         FROM absensi
         WHERE anggota_id = ?"
    );

    mysqli_stmt_bind_param($stmtCek, "i", $id);
    mysqli_stmt_execute($stmtCek);

    $resultCek = mysqli_stmt_get_result($stmtCek);
    $cek = mysqli_fetch_assoc($resultCek);

    if ($cek['total'] > 0) {

        echo "
        <script>
            alert('Anggota tidak bisa dihapus karena sudah memiliki riwayat absensi.');
            window.location.href='admin_anggota.php';
        </script>
        ";

        exit;
    }

    $stmtHapus = mysqli_prepare(
        $koneksi,
        "DELETE FROM anggota_osis WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmtHapus, "i", $id);
    mysqli_stmt_execute($stmtHapus);

    header("Location: admin_anggota.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Kelola Anggota OSIS</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 15px;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f5f5;
            color: #111;
        }

        .container {
            max-width: 1020px;
            margin: auto;
            background: white;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        }

        h1,
        h2,
        h3 {
            margin-top: 0;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        h3 {
            font-size: 21px;
            margin-top: 0;
            margin-bottom: 18px;
        }

        .nav {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-bottom: 25px;
        }

        .nav a {
            color: #5b2aa8;
            text-decoration: none;
            font-size: 17px;
        }

        .nav a:hover {
            text-decoration: underline;
        }

        .nav span {
            color: #777;
        }

        hr {
            border: 0;
            border-top: 1px solid #bbb;
            margin: 25px 0;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 17px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #aaa;
            border-radius: 4px;
            font-size: 16px;
        }

        input:focus {
            outline: none;
            border-color: #754ef9;
        }

        .btn {
            border: 0;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 15px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-tambah {
            background: #198754;
            color: white;
        }

        .btn-tambah:hover {
            background: #157347;
        }

        .btn-edit {
            background: #ffc107;
            color: #111;
        }

        .btn-hapus {
            background: #dc3545;
            color: white;
        }

        .btn-edit:hover {
            background: #e0a800;
        }

        .btn-hapus:hover {
            background: #bb2d3b;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 11px;
            text-align: left;
        }

        th {
            background: #eee;
            text-align: center;
            font-size: 16px;
        }

        td:first-child {
            text-align: center;
            width: 70px;
        }

        .aksi {
            text-align: center;
            white-space: nowrap;
        }

        .aksi a {
            margin: 2px;
        }

        .empty {
            text-align: center;
            color: #666;
            padding: 20px;
        }

        @media (max-width: 700px) {

            body {
                padding: 15px 10px;
            }

            .container {
                padding: 20px 15px;
            }

            h2 {
                font-size: 24px;
            }

            .nav {
                line-height: 1.8;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <!-- ============================ -->
    <!-- JUDUL -->
    <!-- ============================ -->

    <h2>👥 Kelola Anggota OSIS</h2>


    <!-- ============================ -->
    <!-- NAVIGASI -->
    <!-- ============================ -->

    <div class="nav">

        <a href="admin_dashboard.php">
            ← Dashboard
        </a>

        <span>|</span>

        <a href="rekap.php">
            ← Kembali ke Rekap
        </a>

        <span>|</span>

        <a href="admin_rapat.php">
            Kelola Rapat
        </a>

        <span>|</span>

        <a href="logout_rekap.php">
            Logout
        </a>

    </div>


    <hr>


    <!-- ============================ -->
    <!-- TAMBAH ANGGOTA -->
    <!-- ============================ -->

    <h3>➕ Tambah Anggota</h3>

    <form method="post">

        <div class="form-group">

            <label for="nama">
                Nama Lengkap
            </label>

            <input
                type="text"
                id="nama"
                name="nama"
                placeholder="Contoh: Budi Santoso"
                required
            >

        </div>


        <div class="form-group">

            <label for="kelas">
                Kelas
            </label>

            <input
                type="text"
                id="kelas"
                name="kelas"
                placeholder="Contoh: XI TKJ 3"
                required
            >

        </div>


        <div class="form-group">

            <label for="jabatan">
                Jabatan
            </label>

            <input
                type="text"
                id="jabatan"
                name="jabatan"
                placeholder="Contoh: Anggota"
                required
            >

        </div>


        <button
            type="submit"
            name="tambah"
            class="btn btn-tambah"
        >
            + Tambah Anggota
        </button>

    </form>


    <hr>


    <!-- ============================ -->
    <!-- DAFTAR ANGGOTA -->
    <!-- ============================ -->

    <h3>📋 Daftar Anggota</h3>

    <div class="table-wrapper">

        <table>

            <thead>

                <tr>

                    <th>No</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Jabatan</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

            <?php

            $anggota = mysqli_query(
                $koneksi,
                "SELECT *
                 FROM anggota_osis
                 ORDER BY nama ASC"
            );

            $no = 1;

            if (mysqli_num_rows($anggota) > 0) {

                while ($a = mysqli_fetch_assoc($anggota)) {

            ?>

                <tr>

                    <td>
                        <?= $no++ ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($a['nama']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($a['kelas']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($a['jabatan']) ?>
                    </td>

                    <td class="aksi">

                        <a
                            href="edit_anggota.php?id=<?= $a['id'] ?>"
                            class="btn btn-edit"
                        >
                            Edit
                        </a>

                        <a
                            href="admin_anggota.php?hapus=<?= $a['id'] ?>"
                            class="btn btn-hapus"
                            onclick="return confirm('Yakin ingin menghapus anggota ini?');"
                        >
                            Hapus
                        </a>

                    </td>

                </tr>

            <?php

                }

            } else {

            ?>

                <tr>

                    <td
                        colspan="5"
                        class="empty"
                    >
                        Belum ada anggota OSIS.

                    </td>

                </tr>

            <?php

            }

            ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>