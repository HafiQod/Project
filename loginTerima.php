<?php
session_start();
require_once 'config.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDBConnection();
        
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        
        if (!empty($email) && !empty($password)) {
            $stmt = $pdo->prepare("SELECT * FROM penerima WHERE email = ? AND status = 'active'");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['penerima_logged_in'] = true;
                $_SESSION['penerima_id'] = $user['id'];
                $_SESSION['penerima_nama'] = $user['nama'];
                $_SESSION['penerima_email'] = $user['email'];
                
                header('Location: homepageTerima.php');
                exit();
            } else {
                $message = 'Email atau password salah!';
                $message_type = 'error';
            }
        } else {
            $message = 'Mohon lengkapi semua field!';
            $message_type = 'error';
        }
    } catch (PDOException $e) {
        $message = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        $message_type = 'error';
        error_log("Login error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Penerima - Food Sharing</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <a href="pilih.php" class="back-link">←</a>
    
    <div class="auth-container">
        <div class="auth-header">
            <div class="role-icon">
                <img src="images/Vector2.png" alt="Icon Penerima">
            </div>
            <h1 class="auth-title">Login Penerima</h1>
            <p class="auth-subtitle">Masuk untuk mengurangi sampah makanan</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form class="auth-form" method="POST" action="" id="loginForm">
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
                    type="password" 
                    name="password" 
                    placeholder="Password"
                    required
                >
            </div>
            
            <button type="submit" class="btn-primary" id="submitBtn">
                <span class="btn-text">Login</span>
                <span class="btn-loading" style="display: none;">Loading...</span>
            </button>
        </form>

        <div class="auth-links">
            <p>Belum punya akun? <a href="registerTerima.php">Daftar di sini</a></p>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
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
    </script>
</body>
</html>