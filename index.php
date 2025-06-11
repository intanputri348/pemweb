<?php
include('koneksi.php');
session_start();

// Ambil total berat dari tabel 'sampah'
$query = "SELECT SUM(berat) AS total_kg FROM sampah";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

$total_kg = $data['total_kg'] ?? 0;

// Hitung poin dari berat, misalnya 1 kg = 10 poin
$total_poin = $total_kg * 10;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECO LOCO - Bank Sampah Digital</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="navbar">
    <a href="register.php" class="btn">📝 Register</a>
    <a href="login.php" class="btn">🔐 Login</a>
    <a href="logout.php" style="color: red; text-decoration: none;">🚪 Logout</a>
  </div>

  <header>
    <h1>🌿 Selamat Datang di Eco Loco! ♻️</h1>
    <p>🌱 Mari jaga lingkungan dengan cara seru dan asik!</p>
  </header>

  <div class="dashboard">
  <div class="card">
    <h2>🗑️ Total Sampah</h2>
    <div class="data"><?php echo number_format($total_kg, 2, ',', '.'); ?> Kg</div> 
  </div>
  <div class="card">
    <h2>🎁 Poin Anda</h2>
    <div class="data"><?php echo number_format($total_poin, 0, ',', '.'); ?> Poin</div> 
  </div>
</div>

  <div class="actions">
    <a href="setor_sampah.php" class="btn">♻️ Setor Sampah</a>
    <a href="history.php" class="btn">⭐ Cek History</a>
  </div>

  <footer>
    © 2025 Eco Loco. Semua hak cipta dilindungi. 🌱
  </footer>

  <div class="bubble bubble1"></div>
  <div class="bubble bubble2"></div>
</body>
</body>
</html>