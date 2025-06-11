<?php
session_start();
include('koneksi.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Cek apakah parameter ID ada
if (!isset($_GET['id'])) {
    die("ID tidak ditemukan.");
}

$id = intval($_GET['id']);

// Ambil data lama untuk hapus file foto
$query = "SELECT foto_bukti FROM sampah WHERE id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

// Hapus file foto jika ada
if (!empty($data['foto_bukti']) && file_exists('foto_bukti/' . $data['foto_bukti'])) {
    unlink('foto_bukti/' . $data['foto_bukti']);
}

// Hapus data dari database
$query = "DELETE FROM sampah WHERE id = $id";
$result = mysqli_query($conn, $query);

if ($result) {
    header("Location: index.php");
    exit();
} else {
    echo "Gagal menghapus data: " . mysqli_error($conn);
}
?>