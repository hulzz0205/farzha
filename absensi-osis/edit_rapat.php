<?php

session_start();

if (!isset($_SESSION['admin_login'])) {
    header("Location: login_rekap.php");
    exit;
}

require_once "koneksi.php";


if (!isset($_GET['id'])) {
    header("Location: admin_rapat.php");
    exit;
}

$id = (int) $_GET['id'];


/* =========================
   AMBIL DATA
========================= */

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT *
     FROM rapat
     WHERE id=?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($result) == 0) {
    die("Rapat tidak ditemukan.");
}

$data = mysqli_fetch_assoc($result);


/* =========================
   UPDATE
========================= */

if (isset($_POST['update'])) {

    $judul = trim($_POST['judul']);
    $tanggal = $_POST['tanggal'];

    if ($judul == "" || $tanggal == "") {
        die("Judul dan tanggal wajib diisi.");
    }

    $stmt = mysqli_prepare(
        $koneksi,
        "UPDATE rapat
         SET judul=?, tanggal=?
         WHERE id=?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ssi",
        $judul,
        $tanggal,
        $id
    );

    mysqli_stmt_execute($stmt);

    header("Location: admin_rapat.php");
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

    <title>Edit Rapat</title>

    <style>

        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .box {
            max-width: 600px;
            margin: 50px auto;
            padding: 25px;
            background: white;
            border-radius: 12px;
        }

        label {
            display: block;
            font-weight: bold;
            margin: 15px 0 6px;
        }

        input {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 7px;
        }

        button {
            margin-top: 20px;
            padding: 11px 15px;
            border: 0;
            border-radius: 7px;
            background: #0d6efd;
            color: white;
            cursor: pointer;
        }

        a {
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="box">

    <a href="admin_rapat.php">
        ← Kembali
    </a>

    <h2>✏️ Edit Rapat</h2>

    <form method="post">

        <label>Judul Rapat</label>

        <input
            type="text"
            name="judul"
            value="<?= htmlspecialchars($data['judul']) ?>"
            required
        >

        <label>Tanggal</label>

        <input
            type="date"
            name="tanggal"
            value="<?= htmlspecialchars($data['tanggal']) ?>"
            required
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