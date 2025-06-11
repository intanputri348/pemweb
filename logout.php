<?php
session_start();
session_unset();      // Menghapus semua variabel sesi
session_destroy();    // Menghancurkan sesi

echo "<script>
  alert('Kamu telah logout. Sampai jumpa lagi di Eco Loco! 👋');
  window.location.href = 'login.php'; // atau 'index.php' sesuai tujuan kamu
</script>";
exit();
?>
