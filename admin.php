<?php
require_once 'config.php';

$success_message = '';
$error_message = '';

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

if (isset($_GET['logout'])) {
    AdminAuth::logout();
    header('Location: admin.php');
    exit;
}

$admin_logged_in = AdminAuth::isLoggedIn();

if ($admin_logged_in) {
    $saranManager = new SaranManager();
    $admin_info = AdminAuth::getAdminInfo();
    
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
    
    if ($_POST && isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if ($new_password === $confirm_password) {
            $admin_id = $admin_info['id'] ?? null;
            if ($admin_id && AdminAuth::changePassword($admin_id, $old_password, $new_password)) {
                $success_message = 'Password berhasil diubah!';
            } else {
                $error_message = 'Gagal mengubah password. Periksa password lama Anda.';
            }
        } else {
            $error_message = 'Password baru dan konfirmasi tidak cocok.';
        }
    }
    
    $saran_data = $saranManager->getAllSaran();
    $stats = $saranManager->getStatistik();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Food Sharing</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <?php if (!$admin_logged_in): ?>
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
                
                <button type="submit" name="login" class="btn" style="width: 100%;">Login</button>

                <div style="text-align: center; margin-top: 15px;">
                    <a href="index.php" style="color: #5fb3a3;">← Kembali ke Website</a>
                </div>
                
            </form>
        </div>
    <?php else: ?>
        <div class="header">
            <div class="container">
                <div class="nav">
                    <div>
                        <h1>🎛️ Admin Panel</h1>
                        <p>Selamat datang, <strong><?php echo htmlspecialchars($admin_info['nama'] ?? $admin_info['username']); ?></strong></p>
                    </div>
                    <div>
                        <a href="index.php">Lihat Website</a>
                        <a href="#" onclick="showPasswordModal()" style="background: rgba(241,196,15,0.8);">Ganti Password</a>
                        <a href="?logout=1" style="background: rgba(231,76,60,0.8);">Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <?php if ($success_message): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total']; ?></div>
                    <div class="stat-label">Total Saran</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #f39c12;"><?php echo $stats['baru']; ?></div>
                    <div class="stat-label">Saran Baru</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #27ae60;"><?php echo $stats['dibaca']; ?></div>
                    <div class="stat-label">Sudah Dibaca</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="color: #3498db;"><?php echo $stats['dibalas'] ?? 0; ?></div>
                    <div class="stat-label">Sudah Dibalas</div>
                </div>
            </div>

            <div class="table-container">
                <div class="table-header">
                    📝 Daftar Saran & Feedback
                </div>
                
                <?php if (empty($saran_data)): ?>
                    <div style="padding: 40px; text-align: center; color: #666;">
                        <h3>Belum ada saran masuk</h3>
                        <p>Saran dari pengguna akan muncul di sini</p>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
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
                                            <button class="btn" onclick="showUpdateModal(<?php echo $saran['id']; ?>, '<?php echo $saran['status'] ?? 'baru'; ?>')" style="font-size: 0.8rem; padding: 8px 15px;">
                                                Update
                                            </button>
                                            <button class="btn btn-danger" onclick="deleteSaran(<?php echo $saran['id']; ?>)" style="font-size: 0.8rem; padding: 8px 15px; margin-left: 5px;">
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

        <div id="updateModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('updateModal')">&times;</span>
                <h3>Update Status Saran</h3>
                <form method="POST" action="">
                    <input type="hidden" id="update_saran_id" name="saran_id">
                    <div class="form-group">
                        <label for="status">Status Baru:</label>
                        <select id="status" name="status" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px;">
                            <option value="baru">Baru</option>
                            <option value="dibaca">Dibaca</option>
                            <option value="dibalas">Dibalas</option>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="btn">Update Status</button>
                </form>
            </div>
        </div>

        <div id="deleteModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('deleteModal')">&times;</span>
                <h3>Konfirmasi Hapus</h3>
                <p>Apakah Anda yakin ingin menghapus saran ini?</p>
                <form method="POST" action="">
                    <input type="hidden" id="delete_saran_id" name="saran_id">
                    <button type="submit" name="delete_saran" class="btn btn-danger">Ya, Hapus</button>
                    <button type="button" class="btn" onclick="closeModal('deleteModal')" style="margin-left: 10px;">Batal</button>
                </form>
            </div>
        </div>

        <div id="messageModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('messageModal')">&times;</span>
                <h3>Pesan Lengkap</h3>
                <div id="fullMessageContent" style="padding: 20px; background: #f8f9fa; border-radius: 8px; line-height: 1.6;">
                </div>
            </div>
        </div>

        <div id="passwordModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('passwordModal')">&times;</span>
                <h3>Ganti Password</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="old_password">Password Lama:</label>
                        <input type="password" id="old_password" name="old_password" required style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px;">
                    </div>
                    <div class="form-group">
                        <label for="new_password">Password Baru:</label>
                        <input type="password" id="new_password" name="new_password" required minlength="6" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px;">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password Baru:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px;">
                    </div>
                    <button type="submit" name="change_password" class="btn">Ubah Password</button>
                </form>
            </div>
        </div>

        <script src="js/admin.js"></script>
    <?php endif; ?>
</body>
</html>