<?php
// test_db.php
// File untuk test koneksi database

require_once 'config.php';

echo "<h2>🔍 Test Database Connection</h2>";

try {
    $pdo = getDBConnection();
    echo "✅ <strong>Database Connection: SUCCESS</strong><br><br>";
    
    // Test tabel saran
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM saran");
        $total_saran = $stmt->fetch()['total'];
        echo "✅ <strong>Tabel 'saran' found:</strong> {$total_saran} records<br>";
    } catch (PDOException $e) {
        echo "❌ <strong>Tabel 'saran' not found:</strong> " . $e->getMessage() . "<br>";
    }
    
    // Test tabel admin
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM admin");
        $total_admin = $stmt->fetch()['total'];
        echo "✅ <strong>Tabel 'admin' found:</strong> {$total_admin} records<br>";
        
        // Tampilkan admin yang ada
        if ($total_admin > 0) {
            echo "<br><strong>📋 Admin accounts in database:</strong><br>";
            $stmt = $pdo->query("SELECT id, username, nama, email, created_at FROM admin");
            $admins = $stmt->fetchAll();
            
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>ID</th><th>Username</th><th>Nama</th><th>Email</th><th>Created</th></tr>";
            foreach ($admins as $admin) {
                echo "<tr>";
                echo "<td>{$admin['id']}</td>";
                echo "<td>{$admin['username']}</td>";
                echo "<td>{$admin['nama']}</td>";
                echo "<td>{$admin['email']}</td>";
                echo "<td>{$admin['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } catch (PDOException $e) {
        echo "❌ <strong>Tabel 'admin' not found:</strong> " . $e->getMessage() . "<br>";
    }
    
    echo "<br><strong>🔗 Links:</strong><br>";
    echo "<a href='index.php'>🏠 Index Page</a> | ";
    echo "<a href='admin.php'>🎛️ Admin Panel</a><br><br>";
    
    echo "<small>💡 <strong>Note:</strong> Jika tabel tidak ditemukan, pastikan database 'foodrescue' sudah dibuat dan tabel sudah diimport.</small>";
    
} catch (Exception $e) {
    echo "❌ <strong>Database Connection: FAILED</strong><br>";
    echo "<strong>Error:</strong> " . $e->getMessage() . "<br><br>";
    echo "<strong>🔧 Troubleshooting:</strong><br>";
    echo "1. Pastikan MySQL/MariaDB sudah running<br>";
    echo "2. Cek kredensial database di config.php<br>";
    echo "3. Pastikan database 'foodrescue' sudah dibuat<br>";
}

?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background: #f5f5f5;
}

h2 {
    color: #5fb3a3;
    border-bottom: 2px solid #5fb3a3;
    padding-bottom: 10px;
}

table {
    background: white;
    width: 100%;
}

th, td {
    padding: 8px 12px;
    text-align: left;
}

th {
    background: #5fb3a3;
    color: white;
}

a {
    color: #5fb3a3;
    text-decoration: none;
    padding: 5px 10px;
    background: white;
    border-radius: 5px;
    border: 1px solid #5fb3a3;
}

a:hover {
    background: #5fb3a3;
    color: white;
}
</style>