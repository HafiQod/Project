<?php
session_start();
require_once 'config.php';

// Check if admin is logged in
if (!AdminAuth::isLoggedIn()) {
    header('Location: admin_dashboard.php');
    exit;
}

$admin_info = AdminAuth::getAdminInfo();
$saranManager = new SaranManager();

$success_message = '';
$error_message = '';

// Handle update status
if ($_POST && isset($_POST['update_status'])) {
    $saran_id = $_POST['saran_id'] ?? '';
    $status = $_POST['status'] ?? '';
    
    if (!empty($saran_id) && !empty($status)) {
        if ($saranManager->updateStatus($saran_id, $status)) {
            $success_message = 'Status saran berhasil diperbarui!';
        } else {
            $error_message = 'Gagal memperbarui status saran.';
        }
    } else {
        $error_message = 'Data tidak lengkap untuk update status.';
    }
}

// Handle delete saran
if ($_POST && isset($_POST['delete_saran'])) {
    $saran_id = $_POST['saran_id'] ?? '';
    
    if (!empty($saran_id)) {
        if ($saranManager->deleteSaran($saran_id)) {
            $success_message = 'Saran berhasil dihapus!';
        } else {
            $error_message = 'Gagal menghapus saran.';
        }
    } else {
        $error_message = 'ID saran tidak valid.';
    }
}

// Load data
$saran_data = $saranManager->getAllSaran();
$stats = $saranManager->getStatistik();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saran & Feedback - Admin Food Rescue</title>
    <link rel="stylesheet" href="css/admin_new.css">
</head>
<body>
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
                        <a href="admin_dashboard.php" class="nav-link" data-page="dashboard">
                            <svg class="nav-icon" viewBox="0 0 24 24">
                                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                            </svg>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="admin_saran.php" class="nav-link active" data-page="saran">
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
                <h1 class="header-title">Saran & Feedback</h1>
                <p class="header-subtitle">Kelola saran dan feedback dari pengguna Food Rescue</p>
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
                            <div class="stat-title">Total Saran</div>
                            <div class="stat-icon">📝</div>
                        </div>
                        <div class="stat-number"><?php echo number_format($stats['total']); ?></div>
                        <div class="stat-change">
                            <span>→</span> Semua feedback yang masuk
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Saran Baru</div>
                            <div class="stat-icon">🆕</div>
                        </div>
                        <div class="stat-number" style="color: #f39c12;"><?php echo number_format($stats['baru']); ?></div>
                        <div class="stat-change">
                            <span>→</span> Menunggu untuk ditinjau
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Sudah Dibaca</div>
                            <div class="stat-icon">👀</div>
                        </div>
                        <div class="stat-number" style="color: #3498db;"><?php echo number_format($stats['dibaca']); ?></div>
                        <div class="stat-change">
                            <span>→</span> Sudah ditinjau admin
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Sudah Dibalas</div>
                            <div class="stat-icon">✅</div>
                        </div>
                        <div class="stat-number" style="color: #27ae60;"><?php echo number_format($stats['dibalas']); ?></div>
                        <div class="stat-change">
                            <span>→</span> Telah mendapat respon
                        </div>
                    </div>
                </div>
                
                <!-- Suggestions Table -->
                <div class="table-container">
                    <div class="table-header">
                        <span>📝 Daftar Saran & Feedback</span>
                        <div class="table-actions">
                            <button class="btn btn-primary btn-sm" onclick="location.reload()">
                                <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z"/>
                                </svg>
                                Refresh
                            </button>
                        </div>
                    </div>
                    
                    <?php if (empty($saran_data)): ?>
                        <div style="padding: 60px; text-align: center; color: #666;">
                            <svg style="width: 80px; height: 80px; fill: #ddd; margin-bottom: 20px;" viewBox="0 0 24 24">
                                <path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
                            </svg>
                            <h3>Belum ada saran masuk</h3>
                            <p>Saran dan feedback dari pengguna akan muncul di sini</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Pesan</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($saran_data as $saran): ?>
                                        <tr>
                                            <td><?php echo $saran['id']; ?></td>
                                            <td><?php echo htmlspecialchars($saran['nama']); ?></td>
                                            <td><?php echo htmlspecialchars($saran['email']); ?></td>
                                            <td>
                                                <div class="message-text"><?php echo htmlspecialchars(substr($saran['pesan'], 0, 50)); ?><?php echo strlen($saran['pesan']) > 50 ? '...' : ''; ?></div>
                                                <span class="view-full" onclick="showFullMessage('<?php echo htmlspecialchars($saran['pesan'], ENT_QUOTES); ?>')">Lihat lengkap</span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($saran['tanggal_kirim'])); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $saran['status'] ?? 'baru'; ?>">
                                                    <?php echo ucfirst($saran['status'] ?? 'baru'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-primary btn-sm" onclick="showUpdateModal(<?php echo $saran['id']; ?>, '<?php echo $saran['status'] ?? 'baru'; ?>')">
                                                    Update
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="deleteSaran(<?php echo $saran['id']; ?>)" style="margin-left: 5px;">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Update Status Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Update Status Saran</h3>
                <button class="close" onclick="closeModal('updateModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" id="updateForm">
                    <input type="hidden" id="update_saran_id" name="saran_id">
                    <div class="form-group">
                        <label for="status">Status Baru:</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="baru">Baru</option>
                            <option value="dibaca">Dibaca</option>
                            <option value="dibalas">Dibalas</option>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                    <button type="button" class="btn" onclick="closeModal('updateModal')" style="margin-left: 10px;">Batal</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Konfirmasi Hapus</h3>
                <button class="close" onclick="closeModal('deleteModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus saran ini? Tindakan ini tidak dapat dibatalkan.</p>
                <form method="POST" action="" id="deleteForm">
                    <input type="hidden" id="delete_saran_id" name="saran_id">
                    <button type="submit" name="delete_saran" class="btn btn-danger">Ya, Hapus</button>
                    <button type="button" class="btn" onclick="closeModal('deleteModal')" style="margin-left: 10px;">Batal</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Full Message Modal -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Pesan Lengkap</h3>
                <button class="close" onclick="closeModal('messageModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div id="fullMessageContent" style="padding: 20px; background: #f8f9fa; border-radius: 8px; line-height: 1.6;">
                </div>
            </div>
        </div>
    </div>

    <script src="js/admin_new.js"></script>
</body>
</html>