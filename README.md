tambahkan di phpymyadmin untuk sql ini
CREATE TABLE riwayat_penukaran (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_penyetor VARCHAR(100),
  total_poin FLOAT,
  estimasi_uang INT,
  waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
