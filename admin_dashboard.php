<?php
session_start();
require_once 'config.php';

$success_message = '';
$error_message = '';

// Handle login
if ($_POST && isset($_POST['login'])) {
    $input_username = $_POST['username'] ?? '';
    $input_password = $_POST['password'] ?? '';
    
    if (AdminAuth::login($input_username, $input_password)) {
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $login_error = 'Username atau password salah!';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    AdminAuth::logout();
    header('Location: admin_dashboard.php');
    exit;
}

// Check if admin is logged in
$admin_logged_in = AdminAuth::isLoggedIn();

if ($admin_logged_in) {
    $admin_info = AdminAuth::getAdminInfo();
    $analytics = new AdminAnalytics();
    
    // Get overview statistics
    $overview_stats = $analytics->getOverviewStats();
    $daily_stats = $analytics->getDailyStats(30);
    
    // Calculate percentage changes (dummy for now)
    $stats = [
        'total_donasi' => $overview_stats['total_donasi_all_time'] ?? 0,
        'donasi_tersedia' => $overview_stats['donasi_tersedia'] ?? 0,
        'makanan_diselamatkan' => $overview_stats['total_porsi_diselamatkan'] ?? 0,
        'total_users' => ($overview_stats['total_penyumbang'] ?? 0) + ($overview_stats['total_penerima'] ?? 0),
        'saran_baru' => $overview_stats['saran_baru'] ?? 0
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Food Rescue</title>
    <link rel="stylesheet" href="css/admin_new.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php if (!$admin_logged_in): ?>
        <!-- Login Form -->
        <div class="login-container">
            <form class="login-form" method="POST" action="">
                <h2>🔐 Admin Login</h2>
                
                <?php if (isset($login_error)): ?>
                    <div class="message error"><?php echo $login_error; ?></div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" name="login" class="btn btn-primary" style="width: 100%;">Login</button>

                <div style="text-align: center; margin-top: 15px;">
                    <a href="index.php" style="color: #5fb3a3;">← Kembali ke Website</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <!-- Admin Dashboard Layout -->
        <div class="admin-layout">
            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="sidebar-header">
                    <div class="sidebar-logo">Food Rescue</div>
                    <button class="toggle-btn">☰</button>
                </div>
                
                <nav class="nav-menu">
                    <ul>
                        <li class="nav-item">
                            <a href="admin_dashboard.php" class="nav-link active" data-page="dashboard">
                                <svg class="nav-icon" viewBox="0 0 24 24">
                                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                                </svg>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_saran.php" class="nav-link" data-page="saran">
                                <svg class="nav-icon" viewBox="0 0 24 24">
                                    <path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 13.5l-2.5-2.5L9 14.5l4 4 8-8-1.5-1.5L13 15.5z"/>
                                </svg>
                                <span class="nav-text">Saran & Feedback</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_users.php" class="nav-link" data-page="users">
                                <svg class="nav-icon" viewBox="0 0 24 24">
                                    <path d="M16 7c0-2.76-2.24-5-5-5s-5 2.24-5 5 2.24 5 5 5 5-2.24 5-5zM12 14c-3.31 0-10 1.66-10 5v3h20v-3c0-3.34-6.69-5-10-5z"/>
                                </svg>
                                <span class="nav-text">Manajemen User</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_donasi.php" class="nav-link" data-page="donasi">
                                <svg class="nav-icon" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                <span class="nav-text">Donasi Makanan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_settings.php" class="nav-link" data-page="settings">
                                <svg class="nav-icon" viewBox="0 0 24 24">
                                    <path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/>
                                </svg>
                                <span class="nav-text">Pengaturan</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                
                <div class="logout-section">
                    <button class="logout-btn">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                        </svg>
                        <span class="nav-text">Logout</span>
                    </button>
                </div>
            </aside>
            
            <!-- Main Content -->
            <main class="main-content">
                <!-- Content Header -->
                <header class="content-header">
                    <h1 class="header-title">Dashboard Admin</h1>
                    <p class="header-subtitle">Selamat datang, <?php echo htmlspecialchars($admin_info['nama'] ?? $admin_info['username']); ?> | Kelola sistem Food Rescue</p>
                </header>
                
                <!-- Content Area -->
                <div class="content-area">
                    <?php if ($success_message): ?>
                        <div class="message success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="message error"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <!-- Statistics Cards -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-title">Total Donasi</div>
                                <div class="stat-icon">📊</div>
                            </div>
                            <div class="stat-number" id="total-donasi"><?php echo number_format($stats['total_donasi']); ?></div>
                            <div class="stat-change positive">
                                <span>↗</span> +12% dari bulan lalu
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-title">Donasi Tersedia</div>
                                <div class="stat-icon">🍽️</div>
                            </div>
                            <div class="stat-number" id="donasi-tersedia"><?php echo number_format($stats['donasi_tersedia']); ?></div>
                            <div class="stat-change positive">
                                <span>↗</span> +8% dari bulan lalu
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-title">Makanan Diselamatkan</div>
                                <div class="stat-icon">💚</div>
                            </div>
                            <div class="stat-number" id="makanan-diselamatkan"><?php echo number_format($stats['makanan_diselamatkan']); ?></div>
                            <div class="stat-change positive">
                                <span>↗</span> +15% dari bulan lalu
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-title">Total Users</div>
                                <div class="stat-icon">👥</div>
                            </div>
                            <div class="stat-number" id="total-users"><?php echo number_format($stats['total_users']); ?></div>
                            <div class="stat-change positive">
                                <span>↗</span> +5% dari bulan lalu
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chart Section -->
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3 class="chart-title">Analisis Donasi dan Makanan Diselamatkan</h3>
                            <div class="chart-controls">
                                <button class="chart-btn" data-period="week" onclick="switchChartPeriod('week', this)">7 Hari</button>
                                <button class="chart-btn active" data-period="month" onclick="switchChartPeriod('month', this)">30 Hari</button>
                                <button class="chart-btn" data-period="year" onclick="switchChartPeriod('year', this)">1 Tahun</button>
                            </div>
                        </div>
                        <div style="position: relative; height: 400px;">
                            <canvas id="donationChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="stats-grid">
                        <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='admin_saran.php'">
                            <div class="stat-header">
                                <div class="stat-title">Saran Baru</div>
                                <div class="stat-icon">💬</div>
                            </div>
                            <div class="stat-number"><?php echo number_format($stats['saran_baru']); ?></div>
                            <div class="stat-change">
                                <span>→</span> Klik untuk melihat detail
                            </div>
                        </div>
                        
                        <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='admin_users.php'">
                            <div class="stat-header">
                                <div class="stat-title">Manajemen User</div>
                                <div class="stat-icon">⚙️</div>
                            </div>
                            <div class="stat-number">-</div>
                            <div class="stat-change">
                                <span>→</span> Kelola penyumbang & penerima
                            </div>
                        </div>
                        
                        <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='admin_donasi.php'">
                            <div class="stat-header">
                                <div class="stat-title">Kelola Donasi</div>
                                <div class="stat-icon">🎁</div>
                            </div>
                            <div class="stat-number">-</div>
                            <div class="stat-change">
                                <span>→</span> Monitor donasi makanan
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    <?php endif; ?>
    
    <script src="js/admin_new.js"></script>
</body>
</html>