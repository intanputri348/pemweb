<?php
include('koneksi.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password !== $password_confirm) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } else {
        $cekUsername = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
        $cekEmail = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");

        if (mysqli_num_rows($cekUsername) > 0) {
            $error = "Username sudah dipakai!";
        } elseif (mysqli_num_rows($cekEmail) > 0) {
            $error = "Email sudah digunakan!";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password_hash')";
            $insert = mysqli_query($conn, $query);

            if ($insert) {
                echo "<script>
                    alert('Yay! Registrasi berhasil 🎉');
                    window.location.href = 'login.php';
                </script>";
                exit();
            }else {
                $error = "Gagal mendaftar. Silakan coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register | Eco Loco</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #e6f2e6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .register-container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0px 0px 15px rgba(0,0,0,0.1);
            width: 350px;
        }

        h2 {
            text-align: center;
            color: #2e7d32;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4caf50;
            border: none;
            color: white;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #388e3c;
        }

        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }

        .login-link {
            display: block;
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: #2e7d32;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="register-container">
    <h2>Daftar Akun Eco Loco</h2>

    <?php if (isset($error)) : ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email Aktif" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="password_confirm" placeholder="Konfirmasi Password" required>
        <button type="submit">Daftar</button>
    </form>

    <div class="login-link">
        Sudah punya akun? <a href="login.php">Login di sini</a>
    </div>
</div>
</body>
</html>