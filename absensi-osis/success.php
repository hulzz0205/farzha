<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Absensi Berhasil</title>
    <link rel="stylesheet" href="succes.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>

<div class="success-container">
    <div class="card">
        <h2>✅ Absensi Berhasil</h2>
        <p>Terima kasih, absensi kamu sudah tercatat.</p>

        <div class="actions">
            <a href="index.php" class="btn">Kembali</a>
            <a href="logout.php" class="btn logout">Logout</a>
        </div>
    </div>
</div>

</body>
</html>
