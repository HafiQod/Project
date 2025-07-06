<?php
// config.php
// File konfigurasi untuk Food Sharing Website

// =============================================================================
// KONFIGURASI DATABASE
// =============================================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'foodrescue');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// =============================================================================
// KONFIGURASI APLIKASI
// =============================================================================
define('SITE_NAME', 'Food Sharing - Share a Meal, Share a Moment');

// Timezone
date_default_timezone_set('Asia/Jakarta');

// =============================================================================
// FUNGSI KONEKSI DATABASE
// =============================================================================
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

// =============================================================================
// FUNGSI UTILITY
// =============================================================================

// Sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Validasi email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Format tanggal Indonesia
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

// =============================================================================
// CLASS UNTUK MENGELOLA SARAN
// =============================================================================
class SaranManager {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
        $this->ensureStatusColumn();
    }
    
    // Pastikan kolom status ada
    private function ensureStatusColumn() {
        try {
            // Cek apakah kolom status sudah ada
            $stmt = $this->pdo->query("SHOW COLUMNS FROM saran LIKE 'status'");
            if ($stmt->rowCount() == 0) {
                // Tambah kolom status jika belum ada
                $this->pdo->exec("ALTER TABLE saran ADD COLUMN status ENUM('baru', 'dibaca', 'dibalas') DEFAULT 'baru'");
                error_log("Added status column to saran table");
            }
        } catch (PDOException $e) {
            error_log("Error ensuring status column: " . $e->getMessage());
        }
    }
    
    // Tambah saran baru
    public function tambahSaran($nama, $email, $pesan) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO saran (nama, email, pesan, tanggal_kirim) VALUES (?, ?, ?, NOW())");
            return $stmt->execute([sanitizeInput($nama), sanitizeInput($email), sanitizeInput($pesan)]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Ambil semua saran
    public function getAllSaran() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM saran ORDER BY tanggal_kirim DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Update status saran
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
    
    // Hapus saran (alias untuk deleteSaran)
    public function hapusSaran($id) {
        return $this->deleteSaran($id);
    }
    
    // Hapus saran
    public function deleteSaran($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM saran WHERE id = ?");
            return $stmt->execute([intval($id)]);
        } catch (PDOException $e) {
            error_log("Error deleting saran: " . $e->getMessage());
            return false;
        }
    }
    
    // Statistik saran
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

// =============================================================================
// CLASS UNTUK ADMIN ANALYTICS
// =============================================================================
class AdminAnalytics {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    // Get overview statistics
    public function getOverviewStats() {
        try {
            // Jika view belum ada, hitung manual
            $stmt = $this->pdo->query("
                SELECT 
                    (SELECT COUNT(*) FROM donasi) as total_donasi_all_time,
                    (SELECT COUNT(*) FROM donasi WHERE status = 'tersedia') as donasi_tersedia,
                    (SELECT COUNT(*) FROM donasi WHERE status = 'diambil') as donasi_diambil,
                    (SELECT COUNT(*) FROM penyumbang WHERE status = 'active') as total_penyumbang,
                    (SELECT COUNT(*) FROM penerima WHERE status = 'active') as total_penerima,
                    (SELECT COUNT(*) FROM saran) as total_saran,
                    (SELECT COUNT(*) FROM saran WHERE status = 'baru' OR status IS NULL) as saran_baru
            ");
            $result = $stmt->fetch();
            
            // Set default values jika tidak ada data
            return [
                'total_donasi_all_time' => $result['total_donasi_all_time'] ?? 0,
                'donasi_tersedia' => $result['donasi_tersedia'] ?? 0,
                'donasi_diambil' => $result['donasi_diambil'] ?? 0,
                'total_porsi_disumbangkan' => 150, // dummy data
                'total_porsi_diselamatkan' => 120, // dummy data
                'total_penyumbang' => $result['total_penyumbang'] ?? 0,
                'total_penerima' => $result['total_penerima'] ?? 0,
                'total_saran' => $result['total_saran'] ?? 0,
                'saran_baru' => $result['saran_baru'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting overview stats: " . $e->getMessage());
            // Return dummy data jika error
            return [
                'total_donasi_all_time' => 25,
                'donasi_tersedia' => 12,
                'donasi_diambil' => 13,
                'total_porsi_disumbangkan' => 150,
                'total_porsi_diselamatkan' => 120,
                'total_penyumbang' => 15,
                'total_penerima' => 28,
                'total_saran' => 8,
                'saran_baru' => 3
            ];
        }
    }
    
    // Get daily stats for charts
    public function getDailyStats($days = 30) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT tanggal, total_donasi, total_porsi_diselamatkan, 
                       total_penyumbang_aktif, total_penerima_aktif
                FROM daily_stats 
                WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                ORDER BY tanggal ASC
            ");
            $stmt->execute([$days]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting daily stats: " . $e->getMessage());
            return [];
        }
    }
    
    // Get monthly summary
    public function getMonthlyStats($months = 6) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    DATE_FORMAT(tanggal, '%Y-%m') as bulan,
                    SUM(total_donasi) as total_donasi,
                    SUM(total_porsi_diselamatkan) as total_porsi_diselamatkan,
                    AVG(total_penyumbang_aktif) as avg_penyumbang,
                    AVG(total_penerima_aktif) as avg_penerima
                FROM daily_stats 
                WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
                GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
                ORDER BY bulan ASC
            ");
            $stmt->execute([$months]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting monthly stats: " . $e->getMessage());
            return [];
        }
    }
    
    // Update today's stats
    public function updateTodayStats() {
        try {
            $this->pdo->exec("CALL UpdateDailyStats()");
            return true;
        } catch (PDOException $e) {
            error_log("Error updating daily stats: " . $e->getMessage());
            return false;
        }
    }
}

// =============================================================================
// CLASS UNTUK AUTENTIKASI PENYUMBANG DAN PENERIMA
// =============================================================================
class UserAuth {
    private static $pdo;
    
    private static function getConnection() {
        if (self::$pdo === null) {
            self::$pdo = getDBConnection();
        }
        return self::$pdo;
    }
    
    // Login penyumbang
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
    
    // Login penerima
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
    
    // Check if penyumbang logged in
    public static function isPenyumbangLoggedIn() {
        return isset($_SESSION['penyumbang_logged_in']) && $_SESSION['penyumbang_logged_in'] === true;
    }
    
    // Check if penerima logged in
    public static function isPenerimaLoggedIn() {
        return isset($_SESSION['penerima_logged_in']) && $_SESSION['penerima_logged_in'] === true;
    }
    
    // Get penyumbang info
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
    
    // Get penerima info
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
    
    // Logout penyumbang
    public static function logoutPenyumbang() {
        unset($_SESSION['penyumbang_logged_in']);
        unset($_SESSION['penyumbang_id']);
        unset($_SESSION['penyumbang_nama']);
        unset($_SESSION['penyumbang_email']);
    }
    
    // Logout penerima
    public static function logoutPenerima() {
        unset($_SESSION['penerima_logged_in']);
        unset($_SESSION['penerima_id']);
        unset($_SESSION['penerima_nama']);
        unset($_SESSION['penerima_email']);
    }
}

// =============================================================================
// CLASS UNTUK AUTENTIKASI ADMIN
// =============================================================================
class AdminAuth {
    private static $pdo;
    
    private static function getConnection() {
        if (self::$pdo === null) {
            self::$pdo = getDBConnection();
        }
        return self::$pdo;
    }
    
    // Login admin dengan database atau fallback ke hardcoded
    public static function login($username, $password) {
        try {
            // Coba login dengan database dulu
            $pdo = self::getConnection();
            
            // Cek apakah tabel admin ada
            $tables = $pdo->query("SHOW TABLES LIKE 'admin'")->fetchAll();
            
            if (count($tables) > 0) {
                // Tabel admin ada, coba login dengan database
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
                    
                    // Update last login
                    $update_stmt = $pdo->prepare("UPDATE admin SET updated_at = NOW() WHERE id = ?");
                    $update_stmt->execute([$admin['id']]);
                    
                    return true;
                }
            }
            
            // Fallback ke hardcoded credentials jika tabel admin tidak ada atau login gagal
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
            // Jika ada error database, fallback ke hardcoded
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
    
    // Cek apakah admin sudah login
    public static function isLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    // Get admin info
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
    
    // Logout admin
    public static function logout() {
        session_unset();
        session_destroy();
    }
    
    // Change password
    public static function changePassword($admin_id, $old_password, $new_password) {
        try {
            $pdo = self::getConnection();
            
            // Verify old password
            $stmt = $pdo->prepare("SELECT password FROM admin WHERE id = ?");
            $stmt->execute([$admin_id]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($old_password, $admin['password'])) {
                // Update with new password
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

// Inisialisasi session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>