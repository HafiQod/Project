<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'foodrescue');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'Food Sharing - Share a Meal, Share a Moment');

date_default_timezone_set('Asia/Jakarta');

function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function formatTanggalIndonesia($tanggal) {
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    
    $timestamp = strtotime($tanggal);
    $hari = date('d', $timestamp);
    $bulan_num = date('n', $timestamp);
    $tahun = date('Y', $timestamp);
    $jam = date('H:i', $timestamp);
    
    return $hari . ' ' . $bulan[$bulan_num] . ' ' . $tahun . ' - ' . $jam;
}

class SaranManager {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
        $this->ensureStatusColumn();
    }
    
    private function ensureStatusColumn() {
        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM saran LIKE 'status'");
            if ($stmt->rowCount() == 0) {
                $this->pdo->exec("ALTER TABLE saran ADD COLUMN status ENUM('baru', 'dibaca', 'dibalas') DEFAULT 'baru'");
                error_log("Added status column to saran table");
            }
        } catch (PDOException $e) {
            error_log("Error ensuring status column: " . $e->getMessage());
        }
    }
    
    public function tambahSaran($nama, $email, $pesan) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO saran (nama, email, pesan, tanggal_kirim) VALUES (?, ?, ?, NOW())");
            return $stmt->execute([sanitizeInput($nama), sanitizeInput($email), sanitizeInput($pesan)]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getAllSaran() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM saran ORDER BY tanggal_kirim DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function updateStatus($id, $status) {
        try {
            $allowed_status = ['baru', 'dibaca', 'dibalas'];
            if (!in_array($status, $allowed_status)) {
                return false;
            }
            
            $stmt = $this->pdo->prepare("UPDATE saran SET status = ? WHERE id = ?");
            return $stmt->execute([$status, intval($id)]);
        } catch (PDOException $e) {
            error_log("Error updating status: " . $e->getMessage());
            return false;
        }
    }
    
    public function hapusSaran($id) {
        return $this->deleteSaran($id);
    }
    
    public function deleteSaran($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM saran WHERE id = ?");
            return $stmt->execute([intval($id)]);
        } catch (PDOException $e) {
            error_log("Error deleting saran: " . $e->getMessage());
            return false;
        }
    }
    
    public function getStatistik() {
        try {
            $stmt = $this->pdo->query("SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'baru' OR status IS NULL THEN 1 ELSE 0 END) as baru,
                SUM(CASE WHEN status = 'dibaca' THEN 1 ELSE 0 END) as dibaca,
                SUM(CASE WHEN status = 'dibalas' THEN 1 ELSE 0 END) as dibalas
                FROM saran");
            $result = $stmt->fetch();
            return [
                'total' => $result['total'] ?? 0,
                'baru' => $result['baru'] ?? 0,
                'dibaca' => $result['dibaca'] ?? 0,
                'dibalas' => $result['dibalas'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting statistics: " . $e->getMessage());
            return ['total' => 0, 'baru' => 0, 'dibaca' => 0, 'dibalas' => 0];
        }
    }
}

class UserAuth {
    private static $pdo;
    
    private static function getConnection() {
        if (self::$pdo === null) {
            self::$pdo = getDBConnection();
        }
        return self::$pdo;
    }
    
    public static function loginPenyumbang($email, $password) {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM penyumbang WHERE email = ? AND status = 'active'");
            $stmt->execute([sanitizeInput($email)]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['penyumbang_logged_in'] = true;
                $_SESSION['penyumbang_id'] = $user['id'];
                $_SESSION['penyumbang_nama'] = $user['nama'];
                $_SESSION['penyumbang_email'] = $user['email'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login penyumbang error: " . $e->getMessage());
            return false;
        }
    }
    
    public static function loginPenerima($email, $password) {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM penerima WHERE email = ? AND status = 'active'");
            $stmt->execute([sanitizeInput($email)]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['penerima_logged_in'] = true;
                $_SESSION['penerima_id'] = $user['id'];
                $_SESSION['penerima_nama'] = $user['nama'];
                $_SESSION['penerima_email'] = $user['email'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login penerima error: " . $e->getMessage());
            return false;
        }
    }
    
    public static function isPenyumbangLoggedIn() {
        return isset($_SESSION['penyumbang_logged_in']) && $_SESSION['penyumbang_logged_in'] === true;
    }
    
    public static function isPenerimaLoggedIn() {
        return isset($_SESSION['penerima_logged_in']) && $_SESSION['penerima_logged_in'] === true;
    }
    
    public static function getPenyumbangInfo() {
        if (self::isPenyumbangLoggedIn()) {
            return [
                'id' => $_SESSION['penyumbang_id'],
                'nama' => $_SESSION['penyumbang_nama'],
                'email' => $_SESSION['penyumbang_email']
            ];
        }
        return null;
    }
    
    public static function getPenerimaInfo() {
        if (self::isPenerimaLoggedIn()) {
            return [
                'id' => $_SESSION['penerima_id'],
                'nama' => $_SESSION['penerima_nama'],
                'email' => $_SESSION['penerima_email']
            ];
        }
        return null;
    }
    
    public static function logoutPenyumbang() {
        unset($_SESSION['penyumbang_logged_in']);
        unset($_SESSION['penyumbang_id']);
        unset($_SESSION['penyumbang_nama']);
        unset($_SESSION['penyumbang_email']);
    }
    
    public static function logoutPenerima() {
        unset($_SESSION['penerima_logged_in']);
        unset($_SESSION['penerima_id']);
        unset($_SESSION['penerima_nama']);
        unset($_SESSION['penerima_email']);
    }
}

class AdminAuth {
    private static $pdo;
    
    private static function getConnection() {
        if (self::$pdo === null) {
            self::$pdo = getDBConnection();
        }
        return self::$pdo;
    }
    
    public static function login($username, $password) {
        try {
            $pdo = self::getConnection();
            
            $tables = $pdo->query("SHOW TABLES LIKE 'admin'")->fetchAll();
            
            if (count($tables) > 0) {
                $stmt = $pdo->prepare("SELECT id, username, password, nama, email FROM admin WHERE username = ? LIMIT 1");
                $stmt->execute([sanitizeInput($username)]);
                $admin = $stmt->fetch();
                
                if ($admin && password_verify($password, $admin['password'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_nama'] = $admin['nama'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['login_time'] = time();
                    
                    $update_stmt = $pdo->prepare("UPDATE admin SET updated_at = NOW() WHERE id = ?");
                    $update_stmt->execute([$admin['id']]);
                    
                    return true;
                }
            }
            
            $admin_users = [
                'admin' => 'admin123',
                'foodrescue' => 'rescue2024'
            ];
            
            if (isset($admin_users[$username]) && $admin_users[$username] === $password) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                $_SESSION['admin_nama'] = ucfirst($username);
                $_SESSION['login_time'] = time();
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            $admin_users = [
                'admin' => 'admin123',
                'foodrescue' => 'rescue2024'
            ];
            
            if (isset($admin_users[$username]) && $admin_users[$username] === $password) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                $_SESSION['admin_nama'] = ucfirst($username);
                $_SESSION['login_time'] = time();
                return true;
            }
            
            return false;
        }
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    public static function getAdminInfo() {
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['admin_id'] ?? null,
                'username' => $_SESSION['admin_username'] ?? null,
                'nama' => $_SESSION['admin_nama'] ?? $_SESSION['admin_username'] ?? 'Administrator',
                'email' => $_SESSION['admin_email'] ?? null,
                'login_time' => $_SESSION['login_time'] ?? null
            ];
        }
        return null;
    }
    
    public static function logout() {
        session_unset();
        session_destroy();
    }
    
    public static function changePassword($admin_id, $old_password, $new_password) {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("SELECT password FROM admin WHERE id = ?");
            $stmt->execute([$admin_id]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($old_password, $admin['password'])) {
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $pdo->prepare("UPDATE admin SET password = ?, updated_at = NOW() WHERE id = ?");
                return $update_stmt->execute([$new_hash, $admin_id]);
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>