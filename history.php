<?php
include('koneksi.php');
session_start();

function highlightKeyword($text, $keyword) {
  if (!$keyword) return htmlspecialchars($text);
  return preg_replace("/(" . preg_quote($keyword, '/') . ")/i", "<strong style='background-color: #dcedc8;'>$1</strong>", htmlspecialchars($text));
}

// Konfigurasi paginasi
$perPageOptions = [5, 10, 20, 50];
$perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where = "";
if ($search != '') {
  $where = "WHERE nama_penyetor LIKE '%$search%' OR jenis_sampah LIKE '%$search%' OR sub_jenis_sampah LIKE '%$search%' OR tanggal_setor LIKE '%$search%'";
}

$totalQuery = "SELECT COUNT(*) as total FROM sampah $where";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalData = $totalRow['total'];
$totalPages = ceil($totalData / $perPage);

$query = "SELECT * FROM sampah $where ORDER BY id ASC LIMIT $offset, $perPage";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Data Sampah - Eco Loco</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* Gaya seperti sebelumnya... */
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f1f8f4;
      margin: 0;
    }
    .header {
      text-align: center;
      padding: 20px;
      background-color: #a5d6a7;
    }
    .header h1 {
      color: #1b5e20;
    }
    table {
      width: 90%;
      margin: 30px auto;
      border-collapse: collapse;
      background: white;
    }
    th, td {
      padding: 10px;
      border: 1px solid #eee;
      text-align: left;
    }
    th {
      background: #c8e6c9;
      color: #1b5e20;
    }
    .btn {
      padding: 6px 10px;
      border-radius: 4px;
      text-decoration: none;
      color: white;
      font-size: 13px;
    }
    .btn-edit {
      background: #43a047;
    }
    .btn-delete {
      background: #e53935;
    }
    .btn-edit:hover {
      background: #2e7d32;
    }
    .btn-delete:hover {
      background: #b71c1c;
    }
    .pagination {
      text-align: center;
      margin: 20px 0;
    }
    .pagination-list {
      list-style: none;
      display: inline-flex;
      padding: 0;
      border: 1px solid #ddd;
      border-radius: 5px;
      overflow: hidden;
    }
    .pagination-list li {
      border-right: 1px solid #ddd;
    }
    .pagination-list li:last-child {
      border-right: none;
    }
    .pagination-list li a,
    .pagination-list li span {
      display: block;
      padding: 6px 12px;
      text-decoration: none;
      color: #333;
      background-color: #fff;
      font-weight: 600;
    }
    .pagination-list li a:hover {
      background-color: #f1f1f1;
    }
    .pagination-list li.active span {
      background-color: #ddd;
      font-weight: bold;
    }
    .pagination-list li.disabled span {
      color: #ccc;
      background-color: #f9f9f9;
      cursor: not-allowed;
    }
    .filter-form {
      width: 90%;
      margin: 20px auto;
    }
    .filter-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }
    .filter-left label {
      font-size: 14px;
      color: #333;
    }
    .filter-left select {
      margin: 0 5px;
      padding: 6px 8px;
      border-radius: 4px;
      border: 1px solid #ccc;
    }
    .filter-right {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .filter-right input[type="text"] {
      padding: 6px 10px;
      border-radius: 4px;
      border: 1px solid #ccc;
    }
    .filter-right button {
      padding: 6px 12px;
      background-color: #43a047;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .filter-right button:hover {
      background-color: #2e7d32;
    }
  </style>
</head>
<body>

<div class="header">
  <h1>Poin Kamu</h1>
</div>

<div class="filter-form">
  <form method="GET" class="filter-row">
    <div class="filter-left">
      <label>Show
        <select name="per_page" onchange="this.form.submit()">
          <?php foreach ($perPageOptions as $option): ?>
            <option value="<?= $option ?>" <?= $perPage == $option ? 'selected' : '' ?>><?= $option ?></option>
          <?php endforeach; ?>
        </select>
        entries
      </label>
    </div>
    <div class="filter-right">
      <input type="text" name="search" placeholder="Search..." 
        value="<?= htmlspecialchars($search); ?>" />
      <button type="submit">Cari</button>
    </div>
  </form>
</div>

<?php if($search != ''): ?>
  <p style="text-align:center;">Menampilkan hasil untuk pencarian: <strong><?= htmlspecialchars($search) ?></strong></p>
<?php endif; ?>

<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Nama Penyetor</th>
      <th>Tanggal Setor</th>
      <th>Jenis Sampah</th>
      <th>Sub Jenis</th>
      <th>Berat (Kg)</th>
      <th>Poin</th>
      <th>Foto Bukti</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $no = $offset + 1;
    while($row = mysqli_fetch_assoc($result)) {
    ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= highlightKeyword($row['nama_penyetor'], $search) ?></td>
      <td><?= highlightKeyword($row['tanggal_setor'], $search) ?></td>
      <td><?= highlightKeyword($row['jenis_sampah'], $search) ?></td>
      <td><?= highlightKeyword($row['sub_jenis_sampah'], $search) ?></td>
      <td><?= number_format($row['berat'], 2, ',', '.') ?></td>
      <td><span class="poin-badge"><?= $row['berat'] * 10 ?> Poin</span></td>
      <td>
        <?php if($row['foto_bukti'] && file_exists('foto_bukti/'.$row['foto_bukti'])): ?>
          <img src="foto_bukti/<?= $row['foto_bukti'] ?>" width="100">
        <?php else: ?>
          <em>Tidak ada foto</em>
        <?php endif; ?>
      </td>
      <td>
        <a href="edit_sampah.php?id=<?= $row['id'] ?>" class="btn btn-edit">Edit</a>
        <a href="hapus_sampah.php?id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
      </td>
    </tr>
    <?php } ?>
  </tbody>
</table>

<div class="pagination">
  <ul class="pagination-list">
    <?php
    $params = $_GET;

    // Tombol ke halaman pertama
    if ($page > 1) {
      $params['page'] = 1;
      echo '<li><a href="?'.http_build_query($params).'">«</a></li>';
    } else {
      echo '<li class="disabled"><span>«</span></li>';
    }

    // Tombol sebelumnya
    if ($page > 1) {
      $params['page'] = $page - 1;
      echo '<li><a href="?'.http_build_query($params).'">‹</a></li>';
    } else {
      echo '<li class="disabled"><span>‹</span></li>';
    }

    // Nomor halaman
    for ($i = 1; $i <= $totalPages; $i++) {
      $params['page'] = $i;
      if ($i == $page) {
        echo '<li class="active"><span>'.$i.'</span></li>';
      } else {
        echo '<li><a href="?'.http_build_query($params).'">'.$i.'</a></li>';
      }
    }

    // Tombol berikutnya
    if ($page < $totalPages) {
      $params['page'] = $page + 1;
      echo '<li><a href="?'.http_build_query($params).'">›</a></li>';
    } else {
      echo '<li class="disabled"><span>›</span></li>';
    }

    // Tombol terakhir
    if ($page < $totalPages) {
      $params['page'] = $totalPages;
      echo '<li><a href="?'.http_build_query($params).'">»</a></li>';
    } else {
      echo '<li class="disabled"><span>»</span></li>';
    }
    ?>
  </ul>
</div>


<!-- TOTAL POIN -->
<h2 style="text-align:center; margin-top:40px;">Penukaran Poin ke Uang</h2>
<div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; padding: 20px;">
  <?php
  include('koneksi.php');

  $queryTukar = "SELECT nama_penyetor, SUM(berat)*10 AS total_poin 
                 FROM sampah 
                 GROUP BY nama_penyetor 
                 ORDER BY total_poin DESC";
  $resultTukar = mysqli_query($conn, $queryTukar);

  while($row = mysqli_fetch_assoc($resultTukar)) {
    $namaPenyetor = $row['nama_penyetor'];
    $totalPoin = $row['total_poin'];
    $estimasiUang = $totalPoin * 100;

    // Cek apakah sudah ditukarkan
    $cekRiwayat = mysqli_query($conn, "SELECT * FROM riwayat_penukaran WHERE nama_penyetor = '$namaPenyetor'");
    $sudahDitukar = mysqli_num_rows($cekRiwayat) > 0;
  ?>
  <div style="background-color:#d0f8ce; border-radius: 12px; padding: 20px; width: 220px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
    <h3 style="margin: 0 0 10px 0; color:#2e7d32;">👤 <?= htmlspecialchars($namaPenyetor) ?></h3>
    <p style="margin: 6px 0;">⭐ <strong><?= number_format($totalPoin, 1) ?> Poin</strong></p>
    <p style="margin: 6px 0;">💰 Rp<strong><?= number_format($estimasiUang, 0, ',', '.') ?></strong></p>

    <?php if ($sudahDitukar): ?>
      <button disabled
        style="margin-top:10px; padding: 8px 12px; background-color:gray; border:none; border-radius:6px; color:white;">
        Sudah Ditukarkan
      </button>
    <?php else: ?>
      <form method="POST" action="tukar_poin.php">
        <input type="hidden" name="nama_penyetor" value="<?= htmlspecialchars($namaPenyetor) ?>">
        <input type="hidden" name="total_poin" value="<?= $totalPoin ?>">
        <input type="hidden" name="estimasi_uang" value="<?= $estimasiUang ?>">
        <button type="submit"
          style="margin-top:10px; padding: 8px 12px; background-color:#66bb6a; border:none; border-radius:6px; color:white; cursor:pointer;">
          Tukarkan
        </button>
      </form>
    <?php endif; ?>

  </div>
  <?php } ?>
</div>

<div style="text-align:center; margin: 40px 0;">
  <a href="index.php" style="
    display: inline-block;
    padding: 10px 20px;
    background-color: #4caf50;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 16px;
  ">⬅️ Kembali ke Beranda</a>
</div>

</body>
</html>