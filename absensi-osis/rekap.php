<?php

session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

if (!isset($_SESSION['admin_login'])) {
    header("Location: login_rekap.php");
    exit;
}

require_once "koneksi.php";

$rapat_id = null;

if (isset($_GET['rapat_id']) && is_numeric($_GET['rapat_id'])) {
    $rapat_id = (int) $_GET['rapat_id'];
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi OSIS</title>
    <link rel="stylesheet" href="rekap.css">
</head>

<body>

<div class="container">

    <h2>Rekap Absensi OSIS</h2>

    <!-- PILIH RAPAT -->

    <form method="get">

        <select name="rapat_id" required onchange="this.form.submit()">

            <option value="">-- Pilih Rapat --</option>

            <?php

            $rapat = mysqli_query(
                $koneksi,
                "SELECT * FROM rapat ORDER BY tanggal DESC"
            );

            while ($r = mysqli_fetch_assoc($rapat)) {

                $selected = ($rapat_id == $r['id']) ? 'selected' : '';

                echo "
                    <option value='{$r['id']}' $selected>
                        " . htmlspecialchars($r['judul']) . "
                        (" . htmlspecialchars($r['tanggal']) . ")
                    </option>
                ";
            }

            ?>

        </select>

    </form>


<?php if ($rapat_id) { ?>

    <!-- TOMBOL EXPORT EXCEL PER RAPAT -->

    <br>

    <a href="export_rapat.php?rapat_id=<?= $rapat_id ?>">
        <button type="button" style="margin-bottom:15px;">
            Download Excel Rapat Ini
        </button>
    </a>

    <hr>


<?php

// ============================
// HITUNG TOTAL
// ============================

$total_anggota = mysqli_num_rows(
    mysqli_query(
        $koneksi,
        "SELECT id FROM anggota_osis"
    )
);

$total_hadir = mysqli_num_rows(
    mysqli_query(
        $koneksi,
        "SELECT id FROM absensi WHERE rapat_id = '$rapat_id'"
    )
);

$total_tidak_hadir = $total_anggota - $total_hadir;

?>

    <p>
        <b>Total Anggota:</b>
        <?= $total_anggota ?>
    </p>

    <p>
        <b>Hadir:</b>
        <?= $total_hadir ?>
    </p>

    <p>
        <b>Tidak Hadir:</b>
        <?= $total_tidak_hadir ?>
    </p>

    <hr>


    <!-- ============================ -->
    <!-- DAFTAR HADIR -->
    <!-- ============================ -->

    <h3>✅ Daftar Hadir</h3>

    <table border="1" cellpadding="6">

        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>Jabatan</th>
        </tr>

        <?php

        $no = 1;

        $hadir = mysqli_query(
            $koneksi,

            "SELECT anggota_osis.*
             FROM absensi
             JOIN anggota_osis
             ON absensi.anggota_id = anggota_osis.id
             WHERE absensi.rapat_id = '$rapat_id'
             ORDER BY anggota_osis.nama ASC"
        );

        if (mysqli_num_rows($hadir) > 0) {

            while ($h = mysqli_fetch_assoc($hadir)) {

                echo "
                    <tr>
                        <td>$no</td>
                        <td>" . htmlspecialchars($h['nama']) . "</td>
                        <td>" . htmlspecialchars($h['kelas']) . "</td>
                        <td>" . htmlspecialchars($h['jabatan']) . "</td>
                    </tr>
                ";

                $no++;
            }

        } else {

            echo "
                <tr>
                    <td colspan='4'>
                        Belum ada anggota yang hadir.
                    </td>
                </tr>
            ";
        }

        ?>

    </table>


    <hr>


    <!-- ============================ -->
    <!-- TIDAK HADIR -->
    <!-- ============================ -->

    <h3>❌ Tidak Hadir</h3>

    <table border="1" cellpadding="6">

        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>Jabatan</th>
        </tr>

        <?php

        $no = 1;

        $tidak_hadir = mysqli_query(
            $koneksi,

            "SELECT *
             FROM anggota_osis
             WHERE id NOT IN (
                 SELECT anggota_id
                 FROM absensi
                 WHERE rapat_id = '$rapat_id'
             )
             ORDER BY nama ASC"
        );

        if (mysqli_num_rows($tidak_hadir) > 0) {

            while ($t = mysqli_fetch_assoc($tidak_hadir)) {

                echo "
                    <tr>
                        <td>$no</td>
                        <td>" . htmlspecialchars($t['nama']) . "</td>
                        <td>" . htmlspecialchars($t['kelas']) . "</td>
                        <td>" . htmlspecialchars($t['jabatan']) . "</td>
                    </tr>
                ";

                $no++;
            }

        } else {

            echo "
                <tr>
                    <td colspan='4'>
                        Semua anggota sudah hadir.
                    </td>
                </tr>
            ";
        }

        ?>

    </table>

<?php } ?>


    <!-- ============================ -->
    <!-- LOGOUT -->
    <!-- ============================ -->

    <form
        action="logout_rekap.php"
        method="post"
        style="text-align:center;"
    >

        <button class="logout-btn">
            Logout Admin
        </button>

    </form>

</div>

</body>

</html>