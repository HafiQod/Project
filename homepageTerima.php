<?php
session_start();
require_once 'config.php';
require_once 'includes/dummy_data.php';

// Check if user is logged in
if (!UserAuth::isPenerimaLoggedIn()) {
    header('Location: loginTerima.php');
    exit();
}

$user_info = UserAuth::getPenerimaInfo();

// Get filter parameters
$selected_category = $_GET['category'] ?? 'Semua';
$selected_status = $_GET['status'] ?? 'Semua';

// Get data
$all_donations = getDummyDonations();
$filtered_donations = filterDonations($all_donations, $selected_category, $selected_status);
$categories = getFilterCategories();
$statuses = getFilterStatuses();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Rescue - Dashboard Penerima</title>
    <link rel="stylesheet" href="css/homepage.css">
    <meta name="description" content="Dashboard penerima untuk mencari makanan gratis - Food Rescue">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                Food<span class="rescue">Rescue</span>
            </div>
            <div class="profile-icon" title="Profil <?php echo htmlspecialchars($user_info['nama']); ?>">
                <svg viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
        </div>
    </header>

    <!-- Filter Section -->
    <section class="filter-section">
        <div class="filter-container">
            <span class="filter-label">Filter Status:</span>
            
            <select id="categoryFilter" class="filter-select">
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category; ?>" <?php echo $selected_category === $category ? 'selected' : ''; ?>>
                        <?php echo $category; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select id="statusFilter" class="filter-select">
                <?php foreach ($statuses as $status): ?>
                    <option value="<?php echo $status; ?>" <?php echo $selected_status === $status ? 'selected' : ''; ?>>
                        <?php echo $status; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <h2 class="section-title">Makanan Terbaru</h2>
        
        <!-- Cards Grid -->
        <div class="cards-grid">
            <?php foreach ($filtered_donations as $donation): ?>
                <article class="food-card" 
                         data-id="<?php echo $donation['id']; ?>"
                         data-category="<?php echo $donation['kategori']; ?>"
                         data-status="<?php echo $donation['status']; ?>">
                    
                    <!-- Card Header -->
                    <div class="card-header">
                        <div class="donor-info">
                            <div class="donor-avatar">
                                <!-- Placeholder untuk foto profil, ganti dengan <img src="<?php echo $donation['penyumbang_foto']; ?>" alt=""> jika ada foto -->
                                <?php echo strtoupper(substr($donation['penyumbang_nama'], 0, 1)); ?>
                            </div>
                            <div class="donor-details">
                                <h4><?php echo htmlspecialchars($donation['penyumbang_nama']); ?></h4>
                                <div class="donor-contributions">
                                    Kontribusi: <?php echo $donation['penyumbang_kontribusi']; ?>
                                </div>
                            </div>
                        </div>
                        <div class="contribution-count">
                            <?php echo $donation['jumlah_porsi']; ?>
                            <svg viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Food Image -->
                    <div class="food-image">
                        <!-- Placeholder untuk foto makanan, ganti dengan src yang sebenarnya -->
                        <img src="https://via.placeholder.com/400x200/5fb3a3/ffffff?text=<?php echo urlencode($donation['nama_makanan']); ?>" 
                             alt="<?php echo htmlspecialchars($donation['nama_makanan']); ?>"
                             loading="lazy">
                        
                        <!-- Status Badge -->
                        <div class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $donation['status'])); ?>">
                            <?php echo $donation['status']; ?>
                        </div>
                    </div>

                    <!-- Card Content -->
                    <div class="card-content">
                        <h3 class="food-name"><?php echo htmlspecialchars($donation['nama_makanan']); ?></h3>
                        
                        <div class="card-footer">
                            <div class="time-status <?php echo $donation['status'] === 'Expired' ? 'expired' : ($donation['status'] === 'Sudah habis' ? 'taken' : ''); ?>">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M16.2,16.2L11,13V7H12.5V12.2L17,14.7L16.2,16.2Z"/>
                                </svg>
                                <?php echo getTimeAgo($donation['waktu_posting']); ?>
                            </div>
                            
                            <div class="location">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                                </svg>
                                <?php echo htmlspecialchars($donation['lokasi']); ?>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Scroll Sentinel for Infinite Scroll -->
        <div class="scroll-sentinel"></div>
    </main>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <a href="homepageTerima.php" class="nav-item active">
            <svg viewBox="0 0 24 24">
                <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/>
            </svg>
            <span>Home</span>
        </a>
        
        <a href="trackTerima.php" class="nav-item">
            <svg viewBox="0 0 24 24">
                <path d="M12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2M12,4A5,5 0 0,1 17,9C17,11.88 12,18.19 12,18.19C12,18.19 7,11.88 7,9A5,5 0 0,1 12,4Z"/>
            </svg>
            <span>Track</span>
        </a>
        
        <a href="profileTerima.php" class="nav-item">
            <svg viewBox="0 0 24 24">
                <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
            </svg>
            <span>Profile</span>
        </a>
    </nav>

    <script src="js/homepage.js"></script>
</body>
</html>