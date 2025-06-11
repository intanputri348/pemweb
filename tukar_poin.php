<?php
include('koneksi.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = mysqli_real_escape_string($conn, $_POST['nama_penyetor']);
  $total_poin = (float) $_POST['total_poin'];
  $estimasi_uang = (int) $_POST['estimasi_uang'];

  // Cek apakah sudah pernah ditukar sebelumnya
  $check = mysqli_query($conn, "SELECT * FROM riwayat_penukaran WHERE nama_penyetor = '$nama'");
  if (mysqli_num_rows($check) > 0) {
    echo "<script>alert('Poin sudah pernah ditukarkan sebelumnya.'); window.location.href='nama_file_ini.php';</script>";
    exit;
  }

  // Simpan ke riwayat
  $insert = "INSERT INTO riwayat_penukaran (nama_penyetor, total_poin, estimasi_uang) 
             VALUES ('$nama', $total_poin, $estimasi_uang)";
  if (mysqli_query($conn, $insert)) {
    echo "<script>
            alert('Penukaran poin berhasil!😊');
            window.location.href = 'history.php'; 
          </script>";
  } else {
    echo "Gagal menukarkan poin.";
  }
}
?>
