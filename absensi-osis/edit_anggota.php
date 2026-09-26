<?php
session_start();

if (!isset($_SESSION['admin_login'])) {
    header("Location: login_rekap.php");
    exit;
}

require_once "koneksi.php";

/* =========================
   CEK ID
========================= */

if (!isset($_GET['id'])) {
    header("Location: admin_anggota.php");
    exit;
}

$id = $_GET['id'];


/* =========================
   AMBIL DATA ANGGOTA
========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM anggota_osis WHERE id='$id'"
);

if (mysqli_num_rows($query) == 0) {
    die("Anggota tidak ditemukan");
}

$data = mysqli_fetch_assoc($query);


/* =========================
   PROSES UPDATE
========================= */

if (isset($_POST['update'])) {

    $nama = trim($_POST['nama']);
    $kelas = trim($_POST['kelas']);
    $jabatan = trim($_POST['jabatan']);

    mysqli_query(
        $koneksi,
        "UPDATE anggota_osis
         SET
            nama='$nama',
            kelas='$kelas',
            jabatan='$jabatan'
         WHERE id='$id'"
    );

    header("Location: admin_anggota.php");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Anggota OSIS</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        h2 {
            margin-top: 0;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        button {
            margin-top: 20px;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            background: #0d6efd;
            color: white;
            cursor: pointer;
        }

        .kembali {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="container">

    <a
        href="admin_anggota.php"
        class="kembali"
    >
        ← Kembali
    </a>

    <h2>✏️ Edit Anggota OSIS</h2>

    <form method="post">

        <label>Nama Lengkap</label>

        <input
            type="text"
            name="nama"
            value="<?= htmlspecialchars($data['nama']) ?>"
            required
        >

        <label>Kelas</label>

        <input
            type="text"
            name="kelas"
            value="<?= htmlspecialchars($data['kelas']) ?>"
            required
        >

        <label>Jabatan</label>

        <input
            type="text"
            name="jabatan"
            value="<?= htmlspecialchars($data['jabatan']) ?>"
        >

        <button
            type="submit"
            name="update"
        >
            Simpan Perubahan
        </button>

    </form>

</div>

</body>

</html>