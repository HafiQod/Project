<?php
session_start();
require_once 'config.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDBConnection();
        
        $nama = trim($_POST['nama']);
        $email = trim($_POST['email']);
        $no_hp = trim($_POST['no_hp']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $alamat = trim($_POST['alamat']);
        
        if (empty($nama) || empty($email) || empty($no_hp) || empty($password) || empty($alamat)) {
            $message = 'Mohon lengkapi semua field!';
            $message_type = 'error';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Format email tidak valid!';
            $message_type = 'error';
        } elseif (strlen($password) < 6) {
            $message = 'Password minimal 6 karakter!';
            $message_type = 'error';
        } elseif ($password !== $confirm_password) {
            $message = 'Konfirmasi password tidak cocok!';
            $message_type = 'error';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM penyumbang WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $message = 'Email sudah terdaftar!';
                $message_type = 'error';
            } else {
                $createTable = "CREATE TABLE IF NOT EXISTS penyumbang (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nama VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL UNIQUE,
                    no_hp VARCHAR(20) NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    alamat TEXT,
                    status ENUM('active', 'inactive') DEFAULT 'active',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
                $pdo->exec($createTable);
                
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO penyumbang (nama, email, no_hp, password, alamat) VALUES (?, ?, ?, ?, ?)");
                
                if ($stmt->execute([$nama, $email, $no_hp, $hashed_password, $alamat])) {
                    $message = 'Registrasi berhasil! Silakan login dengan akun Anda.';
                    $message_type = 'success';
                    
                    $_POST = array();
                } else {
                    $message = 'Gagal mendaftar. Silakan coba lagi.';
                    $message_type = 'error';
                }
            }
        }
    } catch (PDOException $e) {
        $message = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        $message_type = 'error';
        error_log("Registration error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Penyumbang - Food Sharing</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <a href="loginBeri.php" class="back-link">←</a>
    
    <div class="auth-container">
        <div class="auth-header">
            <div class="role-icon">
                <img src="images/vector.png" alt="Icon Penyumbang">
            </div>
            <h1 class="auth-title">Daftar Penyumbang</h1>
            <p class="auth-subtitle">Bergabung untuk mulai berbagi makanan</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form class="auth-form" method="POST" action="" id="registerForm">
            <div class="form-group">
                <input 
                    type="text" 
                    name="nama" 
                    placeholder="Nama Lengkap"
                    value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>"
                    required
                >
            </div>
            
            <div class="form-group">
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Email"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    required
                >
            </div>
            
            <div class="form-group">
                <input 
                    type="tel" 
                    name="no_hp" 
                    placeholder="No. HP"
                    value="<?php echo isset($_POST['no_hp']) ? htmlspecialchars($_POST['no_hp']) : ''; ?>"
                    required
                >
            </div>
            
            <div class="form-group">
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Password (min. 6 karakter)"
                    required
                    minlength="6"
                >
            </div>
            
            <div class="form-group">
                <input 
                    type="password" 
                    name="confirm_password" 
                    placeholder="Konfirmasi Password"
                    required
                    minlength="6"
                >
            </div>
            
            <div class="form-group">
                <input 
                    type="text" 
                    name="alamat" 
                    placeholder="Alamat Lengkap"
                    value="<?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?>"
                    required
                >
            </div>
            
            <button type="submit" class="btn-primary" id="submitBtn">
                <span class="btn-text">Daftar</span>
                <span class="btn-loading" style="display: none;">Loading...</span>
            </button>
        </form>

        <div class="auth-links">
            <p>Sudah punya akun? <a href="loginBeri.php">Login di sini</a></p>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Konfirmasi password tidak cocok!');
                return;
            }
            
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });

        setTimeout(function() {
            const message = document.querySelector('.message');
            if (message) {
                message.style.opacity = '0';
                setTimeout(function() {
                    message.style.display = 'none';
                }, 300);
            }
        }, 5000);

        document.querySelector('input[name="no_hp"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('0')) {
                value = '62' + value.substring(1);
            } else if (!value.startsWith('62')) {
                value = '62' + value;
            }
            e.target.value = value;
        });
    </script>
</body>
</html>