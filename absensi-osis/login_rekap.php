<?php

session_start();

require_once "koneksi.php";

$error = "";

if (isset($_SESSION['admin_login'])) {
    header("Location: admin_dashboard.php");
    exit;
}


if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, username, password
         FROM admin_rekap
         WHERE username = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $username
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $admin = mysqli_fetch_assoc($result);

        /*
         * Sistem lama menggunakan MD5.
         * Tetap kompatibel dengan database sekarang.
         */

        if ($admin['password'] === md5($password)) {

            $_SESSION['admin_login'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            header("Location: admin_dashboard.php");
            exit;

        } else {

            $error = "Username atau password salah.";
        }

    } else {

        $error = "Username atau password salah.";
    }
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

    <title>Login Admin</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .box {
            width: 100%;
            max-width: 400px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,.08);
        }

        h2 {
            text-align: center;
            margin-top: 0;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 7px;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 7px;
            background: #0d6efd;
            color: white;
            cursor: pointer;
        }

        .error {
            background: #f8d7da;
            color: #842029;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 15px;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="box">

    <h2>🔐 Login Admin</h2>

    <?php if ($error != "") { ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php } ?>

    <form method="post">

        <label>Username</label>

        <input
            type="text"
            name="username"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            required
        >

        <button
            type="submit"
            name="login"
        >
            Login
        </button>

    </form>

    <a
        href="login.php"
        class="back"
    >
        ← Login Absensi
    </a>

</div>

</body>

</html>