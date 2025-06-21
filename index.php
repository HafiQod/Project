<?php
session_start();

require_once 'config.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDBConnection();
        
        error_log("POST data received: " . print_r($_POST, true));
        
        $nama = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $pesan = isset($_POST['message']) ? trim($_POST['message']) : '';
        
        error_log("Data to insert - Nama: $nama, Email: $email, Pesan: $pesan");
        
        if (!empty($nama) && !empty($email) && !empty($pesan)) {
            $checkTable = $pdo->query("SHOW TABLES LIKE 'saran'");
            if ($checkTable->rowCount() == 0) {
                $createTable = "CREATE TABLE IF NOT EXISTS saran (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nama VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    pesan TEXT NOT NULL,
                    tanggal_kirim DATETIME NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                $pdo->exec($createTable);
                error_log("Table 'saran' created");
            }
            
            $stmt = $pdo->prepare("INSERT INTO saran (nama, email, pesan, tanggal_kirim) VALUES (?, ?, ?, NOW())");
            $result = $stmt->execute([$nama, $email, $pesan]);
            
            if ($result) {
                $message = 'Terima kasih atas saran Anda! Pesan telah berhasil dikirim.';
                $message_type = 'success';
                error_log("Data inserted successfully with ID: " . $pdo->lastInsertId());
                
                header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
                exit();
            } else {
                $message = 'Gagal menyimpan data ke database.';
                $message_type = 'error';
                error_log("Failed to insert data");
            }
        } else {
            $message = 'Mohon lengkapi semua field yang diperlukan.';
            $message_type = 'error';
            error_log("Empty fields - Nama: '$nama', Email: '$email', Pesan: '$pesan'");
        }
    } catch (PDOException $e) {
        $message = 'Terjadi kesalahan saat mengirim pesan: ' . $e->getMessage();
        $message_type = 'error';
        error_log("Database error: " . $e->getMessage());
    }
}

if (isset($_GET['success']) && $_GET['success'] == '1') {
    $message = 'Terima kasih atas saran Anda! Pesan telah berhasil dikirim.';
    $message_type = 'success';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?> show" id="message">
            <?php echo htmlspecialchars($message); ?>
            <button class="close-btn" onclick="closeMessage()">&times;</button>
        </div>
    <?php endif; ?>

    <section class="hero">
        <div class="hero-content">
            <h1>"Share a Meal,<br>Share a Moment."</h1>
            <a href="pilih.php" class="btn-start">Start →</a>
        </div>
    </section>

    <section class="stats fade-in" id="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item fade-in-scale">
                    <div class="stat-icon">🌍</div>
                    <div class="stat-title">Data Dunia</div>
                    <div class="stat-desc">Indonesia adalah negara penyumbang sampah makanan terbesar ke-2 di dunia</div>
                </div>
                <div class="stat-item fade-in-scale">
                    <div class="stat-icon">📊</div>
                    <div class="stat-title">Data Indonesia</div>
                    <div class="stat-desc">Dalam satu tahun, food waste orang Indonesia mencapai 1,3 juta ton atau rata-rata 300 kg/ orang</div>
                </div>
                <div class="stat-item fade-in-scale">
                    <div class="stat-icon">👥</div>
                    <div class="stat-title">Fakta Penduduk</div>
                    <div class="stat-desc">Jumlah food waste tersebut mampu menghidupi sekitar 11% juta 29,4 juta penduduk Indonesia yang kekurangan gizi/kelaparan</div>
                </div>
                <div class="stat-item fade-in-scale">
                    <div class="stat-icon">👤</div>
                    <div class="stat-title">Personal</div>
                    <div class="stat-desc">Banyak orang yang memiliki makanan berlebihan dan bingung harus diapakan selain dibuang begitu saja</div>
                </div>
            </div>
        </div>
    </section>

    <section class="quote-section fade-in">
        <div class="container">
            <div class="quote-content">
                <div class="quote-image fade-in-left">
                    <img src = "css/image 4.png" alt="Food sharing illustration">
                </div>
                <div class="quote-text fade-in-right">
                    <h2>"Daripada Dibuang, Lebih Baik Dibagikan."</h2>
                    <p>Setiap hari, jutaan ton makanan yang layak konsumsi dibuang begitu saja, padahal ada banyak orang yang tidur dalam keadaan lapar. Indonesia termasuk negara dengan tingkat pemborosan makanan tertinggi di dunia.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="action-section fade-in">
        <div class="container">
            <div class="action-grid">
                <div class="action-content fade-in-left">
                    <h2>🌱 Bersama Kita Bisa Mengubah Dunia</h2>
                    <ul class="action-list">
                        <li class="fade-in">Sisihkan makanan berlebih untuk dibagikan</li>
                        <li class="fade-in">Simpan makanan dengan benar untuk memperpanjang umur simpan</li>
                        <li class="fade-in">Donasikan makanan sisa yang masih layak ke bank makanan atau komunitas lokal</li>
                        <li class="fade-in">Olah limbah makanan menjadi kompos atau pakan ternak</li>
                    </ul>
                    <div class="action-question fade-in">
                        <h3>💡 Apa yang Bisa Kita Lakukan?</h3>
                        <p>Dengan tindakan kecil seperti membagikan makanan berlebih, 
                            Anda ikut membantu mengurangi kelaparan dan menjaga lingkungan dari tumpukan sampah makanan.</p>
                    </div>
                </div>
                <div class="action-image fade-in-right">
                    <img src="css/image 5.png" alt="Community sharing">
                </div>
            </div>
        </div>
    </section>

    <section class="contact fade-in">
        <div class="container">
            <h2 class="fade-in">Kontak kami</h2>
            <p class="contact-intro fade-in">Jika ada kritik atau saran, bisa melalui formulir di bawah ini.</p>
            
            <form class="contact-form fade-in" method="POST" action="" id="contactForm">
                <div class="form-group">
                    <label for="name">*Nama</label>
                    <input type="text" id="name" name="name" required maxlength="255">
                </div>
                <div class="form-group">
                    <label for="email">*Email</label>
                    <input type="email" id="email" name="email" required maxlength="255">
                </div>
                <div class="form-group full-width">
                    <label for="message">*Pesan</label>
                    <textarea id="message" name="message" placeholder="Tulis pesan Anda di sini..." required maxlength="1000"></textarea>
                </div>
                <button type="submit" class="btn-submit" id="submitBtn">
                    <span class="btn-text">Kirim</span>
                    <span class="btn-loading" style="display: none;">Mengirim...</span>
                </button>
            </form>
        </div>
    </section>

    <script src="js/tes.js"></script>

</body>
</html>