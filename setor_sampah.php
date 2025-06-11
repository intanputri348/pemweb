<?php
session_start();
include('koneksi.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_penyetor = mysqli_real_escape_string($conn, $_POST['nama_penyetor']);
    $tanggal_setor = mysqli_real_escape_string($conn, $_POST['tanggal_setor']);
    $jenis_sampah  = mysqli_real_escape_string($conn, $_POST['jenis_sampah']);
    $sub_jenis_sampah = mysqli_real_escape_string($conn, $_POST['sub_jenis_sampah']);
    $berat         = floatval($_POST['berat']);

    // Proses upload foto
    $foto_name = $_FILES['foto_bukti']['name'];
    $foto_tmp  = $_FILES['foto_bukti']['tmp_name'];
    $foto_error = $_FILES['foto_bukti']['error'];
    $foto_ext  = pathinfo($foto_name, PATHINFO_EXTENSION);
    $foto_baru = uniqid() . '.' . strtolower($foto_ext);
    $upload_dir = 'foto_bukti/';
    $upload_path = $upload_dir . $foto_baru;

    // Validasi file
    $allowed_ext = ['jpg', 'jpeg', 'png'];
    if ($foto_error !== UPLOAD_ERR_OK) {
        $error = "Terjadi kesalahan saat upload file. Kode error: " . $foto_error;
    } elseif (!in_array(strtolower($foto_ext), $allowed_ext)) {
        $error = "Format gambar harus JPG, JPEG, atau PNG!";
    } elseif (!move_uploaded_file($foto_tmp, $upload_path)) {
        $error = "Gagal mengupload foto bukti!";
    } else {
        // Simpan ke database
        $poin = intval($berat * 10);
        $query = "INSERT INTO sampah (nama_penyetor, tanggal_setor, jenis_sampah, sub_jenis_sampah, berat, foto_bukti, poin)
          VALUES ('$nama_penyetor', '$tanggal_setor', '$jenis_sampah', '$sub_jenis_sampah', $berat, '$foto_baru', $poin)";
        $result = mysqli_query($conn, $query);

        if ($result) {
            header("Location: history.php");
            exit();
        } else {
            $error = "Gagal menyimpan ke database: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Sampah - Eco Loco</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg,rgb(215, 233, 193) 0%,rgb(237, 241, 189) 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .header h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .header .subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 300;
        }

        .form-container {
            padding: 40px;
        }

        .alert {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 1.05rem;
        }

        .form-group label i {
            color: #28a745;
            margin-right: 8px;
            width: 16px;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #28a745;
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.2);
        }

        .form-control:hover {
            border-color: #28a745;
            background: white;
        }

        select.form-control {
            cursor: pointer;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px 20px;
            border: 2px dashed #28a745;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8fff9;
            color: #28a745;
            font-weight: 600;
        }

        .file-input-label:hover {
            background: #28a745;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg,rgb(49, 128, 160), #2e7d32);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg,rgb(126, 170, 187),rgb(180, 219, 182));
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
        }

        .stats-bar {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-around;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .stat-item p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .form-container {
                padding: 25px;
            }
            
            .header h1 {
                font-size: 1.8rem;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .stats-bar {
                flex-direction: column;
                gap: 15px;
            }
        }

        .success-animation {
            animation: bounce 0.6s ease-out;
        }

        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <h1>
                    <i class="fas fa-recycle"></i>
                    Eco Loco
                </h1>
                <p class="subtitle">Sistem Pengelolaan Sampah Terpadu</p>
            </div>
        </div>

        <!-- Stats Bar -->
        <div class="stats-bar">
            <div class="stat-item">
                <h3><i class="fas fa-leaf"></i></h3>
                <p>Ramah Lingkungan</p>
            </div>
            <div class="stat-item">
                <h3><i class="fas fa-users"></i></h3>
                <p>Komunitas Peduli</p>
            </div>
            <div class="stat-item">
                <h3><i class="fas fa-coins"></i></h3>
                <p>Bernilai Ekonomis</p>
            </div>
        </div>

        <!-- Form Container -->
        <div class="form-container">
            <h2 style="color:rgba(25, 158, 176, 0.83); margin-bottom: 30px; text-align: center; font-size: 1.8rem;">
                <i class="fas fa-plus-circle" style="color:rgb(45, 99, 129);"></i>
                Tambah Data Sampah
            </h2>

            <?php if (isset($error)): ?>
                <div class="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nama_penyetor">
                        <i class="fas fa-user"></i>
                        Nama Penyetor
                    </label>
                    <input type="text" id="nama_penyetor" name="nama_penyetor" class="form-control" 
                           placeholder="Masukkan nama lengkap penyetor" required>
                </div>

                <div class="form-group">
                    <label for="tanggal_setor">
                        <i class="fas fa-calendar-alt"></i>
                        Tanggal Setor
                    </label>
                    <input type="date" id="tanggal_setor" name="tanggal_setor" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="jenis_sampah">
                        <i class="fas fa-layer-group"></i>
                        Jenis Sampah
                    </label>
                    <select id="jenis_sampah" name="jenis_sampah" class="form-control" required onchange="updateSubJenis()">
                        <option value="">-- Pilih Jenis Sampah --</option>
                        <option value="organik">🌱 Organik</option>
                        <option value="anorganik">♻️ Anorganik</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="sub_jenis_sampah">
                        <i class="fas fa-list-ul"></i>
                        Sub Jenis Sampah
                    </label>
                    <select name="sub_jenis_sampah" id="sub_jenis_sampah" class="form-control" required>
                        <option value="">-- Pilih Sub Jenis Sampah --</option>
                    </select>
                </div>

                <form method="POST" action="" enctype="multipart/form-data" onsubmit="return cekBerat()">
                <div class="form-group">
                    <label for="berat">
                        <i class="fas fa-weight-hanging"></i>
                        Berat (Kilogram)
                    </label>
                    <input type="number" id="berat" name="berat" step="0.1" class="form-control"
                    placeholder="0.0" required>
                </div>
            
                <script>
                function cekBerat() {
                    const beratInput = document.getElementById('berat');
                    const berat = parseFloat(beratInput.value);
                    if (isNaN(berat) || berat < 0.1) {
                        beratInput.focus();
                        return false;
                    }
                    return true;
                }
                </script>

                <div class="form-group">
                    <label>
                        <i class="fas fa-camera"></i>
                        Foto Bukti Sampah
                    </label>
                    <div class="file-input-wrapper">
                        <input type="file" id="foto_bukti" name="foto_bukti" accept="image/*" required>
                        <label for="foto_bukti" class="file-input-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            Pilih File Gambar (JPG, JPEG, PNG)
                        </label>
                    </div>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan Data
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Beranda
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Set tanggal hari ini sebagai default
        document.getElementById('tanggal_setor').value = new Date().toISOString().split('T')[0];

        function updateSubJenis() {
            const jenis = document.getElementById('jenis_sampah').value;
            const subJenis = document.getElementById('sub_jenis_sampah');

            const pilihan = {
                organik: ['Sisa Makanan', 'Daun Kering', 'Sayuran Busuk', 'Kulit Buah', 'Ampas Teh/Kopi'],
                anorganik: ['Botol Plastik', 'Kantong Plastik', 'Kertas Bekas', 'Kardus', 'Kaleng', 'Botol Kaca']
            };

            subJenis.innerHTML = '<option value="">-- Pilih Sub Jenis Sampah --</option>';

            if (pilihan[jenis]) {
                pilihan[jenis].forEach(function(item) {
                    const opt = document.createElement('option');
                    opt.value = item;
                    opt.text = item;
                    subJenis.add(opt);
                });
            }
        }

        // Update label file input ketika file dipilih
        document.getElementById('foto_bukti').addEventListener('change', function(e) {
            const label = document.querySelector('.file-input-label');
            if (e.target.files.length > 0) {
                label.innerHTML = `<i class="fas fa-check-circle"></i> ${e.target.files[0].name}`;
                label.style.background = '#d4edda';
                label.style.color = '#155724';
                label.style.borderColor = '#28a745';
            }
        });

        // Animasi saat form berhasil disubmit
        document.querySelector('form').addEventListener('submit', function() {
            document.querySelector('.container').classList.add('success-animation');
        });

        // Validasi file size (max 5MB)
        document.getElementById('foto_bukti').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.size > 5 * 1024 * 1024) {
                alert('Ukuran file terlalu besar! Maksimal 5MB.');
                e.target.value = '';
                document.querySelector('.file-input-label').innerHTML = '<i class="fas fa-cloud-upload-alt"></i> Pilih File Gambar (JPG, JPEG, PNG)';
            }
        });
    </script>
</body>
</html>