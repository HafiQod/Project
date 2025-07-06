<?php
// api/admin_stats.php
// API untuk mengambil data statistik admin dashboard

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

session_start();
require_once '../config.php';

// Check if admin is logged in
if (!AdminAuth::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $analytics = new AdminAnalytics();
    $saranManager = new SaranManager();
    
    // Get overview statistics
    $overview_stats = $analytics->getOverviewStats();
    $saran_stats = $saranManager->getStatistik();
    
    // Prepare response data
    $response = [
        'success' => true,
        'stats' => [
            'totalDonasi' => $overview_stats['total_donasi_all_time'] ?? 0,
            'donasiTersedia' => $overview_stats['donasi_tersedia'] ?? 0,
            'makananDiselamatkan' => $overview_stats['total_porsi_diselamatkan'] ?? 0,
            'totalUsers' => ($overview_stats['total_penyumbang'] ?? 0) + ($overview_stats['total_penerima'] ?? 0),
            'saranBaru' => $saran_stats['baru'] ?? 0,
            'totalSaran' => $saran_stats['total'] ?? 0
        ],
        'chartData' => [
            'labels' => [],
            'donasi' => [],
            'diselamatkan' => []
        ]
    ];
    
    // Get chart data for last 30 days
    $daily_stats = $analytics->getDailyStats(30);
    
    if (!empty($daily_stats)) {
        foreach ($daily_stats as $stat) {
            $response['chartData']['labels'][] = date('M j', strtotime($stat['tanggal']));
            $response['chartData']['donasi'][] = (int)$stat['total_donasi'];
            $response['chartData']['diselamatkan'][] = (int)$stat['total_porsi_diselamatkan'];
        }
    } else {
        // Dummy data jika belum ada data real
        $response['chartData'] = [
            'labels' => ['Jun 1', 'Jun 2', 'Jun 3', 'Jun 4', 'Jun 5', 'Jun 6', 'Jun 7', 'Jun 8', 'Jun 9', 'Jun 10'],
            'donasi' => [12, 15, 8, 18, 22, 19, 25, 16, 21, 28],
            'diselamatkan' => [10, 12, 6, 15, 18, 16, 20, 13, 17, 23]
        ];
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ]);
}
?>