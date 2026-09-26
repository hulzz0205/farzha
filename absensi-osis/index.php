<?php

session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

require_once "koneksi.php";

$error = "";


/* =========================
   PROSES ABSENSI
========================= */

if (isset($_POST['absen'])) {

    $rapat_id = (int) $_POST['rapat_id'];
    $nama = trim($_POST['nama']);
    $kelas = trim($_POST['kelas']);


    if ($rapat_id <= 0 || $nama == "" || $kelas == "") {

        $error = "Semua data wajib diisi.";

    } else {


        /* =========================
           CEK RAPAT MASIH BUKA
        ========================= */

        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id
             FROM rapat
             WHERE id=?
             AND status='buka'
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $rapat_id
        );

        mysqli_stmt_execute($stmt);

        $rapat_result = mysqli_stmt_get_result($stmt);


        if (mysqli_num_rows($rapat_result) == 0) {

            $error = "Rapat sudah ditutup atau tidak ditemukan.";

        } else {


            /* =========================
               CARI ANGGOTA
            ========================= */

            $stmt = mysqli_prepare(
                $koneksi,
                "SELECT id
                 FROM anggota_osis
                 WHERE LOWER(nama)=LOWER(?)
                 AND LOWER(kelas)=LOWER(?)
                 LIMIT 1"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ss",
                $nama,
                $kelas
            );

            mysqli_stmt_execute($stmt);

            $anggota_result = mysqli_stmt_get_result($stmt);


            if (mysqli_num_rows($anggota_result) == 0) {

                $error = "Nama dan kelas tidak ditemukan.";

            } else {

                $anggota = mysqli_fetch_assoc($anggota_result);

                $anggota_id = (int) $anggota['id'];


                /* =========================
                   CEK DUPLIKAT
                ========================= */

                $stmt = mysqli_prepare(
                    $koneksi,
                    "SELECT id
                     FROM absensi
                     WHERE anggota_id=?
                     AND rapat_id=?
                     LIMIT 1"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "ii",
                    $anggota_id,
                    $rapat_id
                );

                mysqli_stmt_execute($stmt);

                $cek = mysqli_stmt_get_result($stmt);


                if (mysqli_num_rows($cek) > 0) {

                    $error = "Kamu sudah absen di rapat ini.";

                } else {


                    /* =========================
                       SIMPAN
                    ========================= */

                    $stmt = mysqli_prepare(
                        $koneksi,
                        "INSERT INTO absensi
                        (anggota_id, rapat_id)
                        VALUES (?, ?)"
                    );

                    mysqli_stmt_bind_param(
                        $stmt,
                        "ii",
                        $anggota_id,
                        $rapat_id
                    );


                    if (mysqli_stmt_execute($stmt)) {

                        header("Location: success.php");
                        exit;

                    } else {

                        if (mysqli_errno($koneksi) == 1062) {

                            $error = "Kamu sudah absen di rapat ini.";

                        } else {

                            $error = "Absensi gagal disimpan.";
                        }
                    }
                }
            }
        }
    }
}


/* =========================
   RAPAT AKTIF
========================= */

$rapat = mysqli_query(
    $koneksi,
    "SELECT id, judul, tanggal
     FROM rapat
     WHERE status='buka'
     AND tanggal >= CURDATE()
     ORDER BY tanggal ASC, id ASC"
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

    <title>Absensi OSIS</title>

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
            max-width: 500px;
            margin: 50px auto;
            padding: 25px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,.08);
        }

        h2 {
            text-align: center;
        }

        .info {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 7px;
        }

        button {
            width: 100%;
            padding: 13px;
            border: 0;
            border-radius: 7px;
            background: #198754;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }

        .error {
            padding: 12px;
            background: #f8d7da;
            color: #842029;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .kosong {
            padding: 15px;
            background: #fff3cd;
            color: #664d03;
            border-radius: 7px;
            text-align: center;
        }

        .logout {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #dc3545;
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="container">

    <h2>📋 Absensi OSIS</h2>

    <div class="info">
        Silakan pilih rapat dan isi data diri.
    </div>


    <?php if ($error != "") { ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php } ?>


    <?php if (mysqli_num_rows($rapat) == 0) { ?>

        <div class="kosong">
            Tidak ada rapat yang sedang dibuka.
        </div>

    <?php } else { ?>

        <form method="post">

            <label>Pilih Rapat</label>

            <select name="rapat_id" required>

                <option value="">
                    -- Pilih Rapat --
                </option>

                <?php while ($r = mysqli_fetch_assoc($rapat)) { ?>

                    <option value="<?= $r['id'] ?>">

                        <?= htmlspecialchars($r['judul']) ?>
                        -
                        <?= htmlspecialchars($r['tanggal']) ?>

                    </option>

                <?php } ?>

            </select>


            <label>Nama Lengkap</label>

            <input
                type="text"
                name="nama"
                placeholder="Masukkan nama lengkap"
                required
            >


            <label>Kelas</label>

            <input
                type="text"
                name="kelas"
                placeholder="Contoh: XI TKJ 3"
                required
            >


            <button
                type="submit"
                name="absen"
            >
                ✅ Absen Sekarang
            </button>

        </form>

    <?php } ?>


    <a
        href="logout.php"
        class="logout"
    >
        Logout
    </a>

</div>

</body>

</html>