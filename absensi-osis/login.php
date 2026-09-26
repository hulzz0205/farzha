<?php
session_start();
require_once "koneksi.php";

// kalau sudah login, langsung ke index
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$error = "";

// PROSES LOGIN
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM admin 
         WHERE username='$username' 
         AND password='$password'"
    );

    if (mysqli_num_rows($query) === 1) {
        $_SESSION['login'] = true;
        $_SESSION['username'] = $username;

        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau password salah";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Absensi OSIS</title>
    <link rel="stylesheet" href="style2.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="container">
    <h2>Login Absensi OSIS</h2>

    <?php if ($error != "") { ?>
        <p style="color:red"><?= $error ?></p>
    <?php } ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>

</body>
</html>
