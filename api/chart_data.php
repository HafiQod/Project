<?php
// api/chart_data.php
// API untuk mengambil data chart berdasarkan periode

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
    $period = $_GET['period'] ?? 'month';
    $analytics = new AdminAnalytics();
    
    $response = [
        'success' => true,
        'labels' => [],
        'donasi' => [],
        'diselamatkan' => []
    ];
    
    switch ($period) {
        case 'week':
            // Data 7 hari terakhir
            $daily_stats = $analytics->getDailyStats(7);
            
            if (!empty($daily_stats)) {
                foreach ($daily_stats as $stat) {
                    $response['labels'][] = date('D', strtotime($stat['tanggal']));
                    $response['donasi'][] = (int)$stat['total_donasi'];
                    $response['diselamatkan'][] = (int)$stat['total_porsi_diselamatkan'];
                }
            } else {
                // Dummy data untuk 7 hari
                $response['labels'] = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
                $response['donasi'] = [12, 18, 15, 22, 28, 35, 25];
                $response['diselamatkan'] = [10, 15, 12, 19, 25, 30, 22];
            }
            break;
            
        case 'year':
            // Data 12 bulan terakhir
            $monthly_stats = $analytics->getMonthlyStats(12);
            
            if (!empty($monthly_stats)) {
                foreach ($monthly_stats as $stat) {
                    $response['labels'][] = date('M Y', strtotime($stat['bulan'] . '-01'));
                    $response['donasi'][] = (int)$stat['total_donasi'];
                    $response['diselamatkan'][] = (int)$stat['total_porsi_diselamatkan'];
                }
            } else {
                // Dummy data untuk 12 bulan
                $response['labels'] = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                $response['donasi'] = [45, 52, 38, 67, 73, 89, 95, 102, 87, 93, 108, 115];
                $response['diselamatkan'] = [38, 45, 32, 58, 65, 78, 85, 89, 76, 82, 95, 102];
            }
            break;
            
        case 'month':
        default:
            // Data 30 hari terakhir
            $daily_stats = $analytics->getDailyStats(30);
            
            if (!empty($daily_stats)) {
                foreach ($daily_stats as $stat) {
                    $response['labels'][] = date('j M', strtotime($stat['tanggal']));
                    $response['donasi'][] = (int)$stat['total_donasi'];
                    $response['diselamatkan'][] = (int)$stat['total_porsi_diselamatkan'];
                }
            } else {
                // Dummy data untuk 30 hari
                for ($i = 29; $i >= 0; $i--) {
                    $date = date('j M', strtotime("-$i days"));
                    $response['labels'][] = $date;
                    $response['donasi'][] = rand(15, 35);
                    $response['diselamatkan'][] = rand(10, 30);
                }
            }
            break;
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