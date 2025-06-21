<?php
// debug_login.php
// File untuk debug masalah login admin

require_once 'config.php';

echo "<h2>🔍 Debug Login Admin</h2>";

// Test koneksi database
try {
    $pdo = getDBConnection();
    echo "✅ <strong>Database Connection: SUCCESS</strong><br><br>";
} catch (Exception $e) {
    echo "❌ <strong>Database Connection: FAILED</strong><br>";
    echo "Error: " . $e->getMessage() . "<br><br>";
    exit;
}

// Tampilkan semua admin di database
echo "<h3>📋 Admin Data in Database:</h3>";
try {
    $stmt = $pdo->query("SELECT id, username, password, nama, email, created_at FROM admin");
    $admins = $stmt->fetchAll();
    
    if (empty($admins)) {
        echo "❌ <strong>No admin found in database!</strong><br>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Password Hash</th><th>Nama</th><th>Email</th><th>Created</th></tr>";
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>{$admin['id']}</td>";
            echo "<td><strong>{$admin['username']}</strong></td>";
            echo "<td style='font-family: monospace; font-size: 10px;'>" . substr($admin['password'], 0, 50) . "...</td>";
            echo "<td>{$admin['nama']}</td>";
            echo "<td>{$admin['email']}</td>";
            echo "<td>{$admin['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (PDOException $e) {
    echo "❌ <strong>Error reading admin table:</strong> " . $e->getMessage() . "<br>";
}

// Form test login
echo "<br><h3>🔐 Test Login</h3>";
echo "<form method='POST' style='background: #f9f9f9; padding: 20px; border-radius: 8px; max-width: 400px;'>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label><strong>Username:</strong></label><br>";
echo "<input type='text' name='test_username' required style='width: 100%; padding: 8px; margin-top: 5px;'>";
echo "</div>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label><strong>Password:</strong></label><br>";
echo "<input type='password' name='test_password' required style='width: 100%; padding: 8px; margin-top: 5px;'>";
echo "</div>";
echo "<button type='submit' name='test_login' style='background: #5fb3a3; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Test Login</button>";
echo "</form>";

// Process test login
if ($_POST && isset($_POST['test_login'])) {
    $test_username = $_POST['test_username'] ?? '';
    $test_password = $_POST['test_password'] ?? '';
    
    echo "<br><h3>🧪 Login Test Results:</h3>";
    echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    
    try {
        // Cari admin berdasarkan username
        $stmt = $pdo->prepare("SELECT id, username, password, nama, email FROM admin WHERE username = ? LIMIT 1");
        $stmt->execute([sanitizeInput($test_username)]);
        $admin = $stmt->fetch();
        
        echo "<strong>1. Username Search:</strong><br>";
        if ($admin) {
            echo "✅ Admin found: {$admin['nama']} ({$admin['email']})<br>";
            echo "📝 Username in DB: '{$admin['username']}'<br>";
            echo "📝 Username entered: '{$test_username}'<br><br>";
            
            echo "<strong>2. Password Verification:</strong><br>";
            echo "📝 Password entered: '{$test_password}'<br>";
            echo "📝 Hash in DB: " . substr($admin['password'], 0, 60) . "...<br>";
            
            // Cek apakah password ter-hash dengan benar
            if (strlen($admin['password']) < 50) {
                echo "⚠️ <strong>WARNING:</strong> Password in database is NOT hashed! Length: " . strlen($admin['password']) . " characters<br>";
                echo "💡 <strong>Solution:</strong> Password should be hashed with password_hash()<br>";
                
                // Test direct comparison untuk password plain text
                if ($admin['password'] === $test_password) {
                    echo "✅ Direct match with plain text password<br>";
                } else {
                    echo "❌ No direct match with plain text password<br>";
                }
            } else {
                echo "✅ Password appears to be hashed (Length: " . strlen($admin['password']) . " characters)<br>";
                
                // Test password_verify
                if (password_verify($test_password, $admin['password'])) {
                    echo "✅ <strong>password_verify(): SUCCESS</strong><br>";
                    echo "🎉 <strong>Login should work!</strong><br>";
                } else {
                    echo "❌ <strong>password_verify(): FAILED</strong><br>";
                    echo "💡 Password hash might be corrupted or wrong algorithm used<br>";
                }
            }
            
        } else {
            echo "❌ No admin found with username: '{$test_username}'<br>";
            echo "💡 Check if username exists in database<br>";
        }
        
    } catch (PDOException $e) {
        echo "❌ <strong>Database Error:</strong> " . $e->getMessage() . "<br>";
    }
    
    echo "</div>";
}

// Tools untuk hash password
echo "<br><h3>🔧 Password Hash Tool</h3>";
echo "<form method='POST' style='background: #e8f4f2; padding: 20px; border-radius: 8px; max-width: 400px;'>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label><strong>Password to hash:</strong></label><br>";
echo "<input type='text' name='password_to_hash' style='width: 100%; padding: 8px; margin-top: 5px;'>";
echo "</div>";
echo "<button type='submit' name='hash_password' style='background: #f39c12; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Generate Hash</button>";
echo "</form>";

if ($_POST && isset($_POST['hash_password'])) {
    $password_to_hash = $_POST['password_to_hash'] ?? '';
    if (!empty($password_to_hash)) {
        $hashed = password_hash($password_to_hash, PASSWORD_DEFAULT);
        echo "<br><div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
        echo "<strong>Original Password:</strong> {$password_to_hash}<br>";
        echo "<strong>Hashed Password:</strong><br>";
        echo "<code style='word-break: break-all; background: white; padding: 10px; display: block; margin: 10px 0; border-radius: 4px;'>{$hashed}</code>";
        echo "<strong>SQL Update:</strong><br>";
        echo "<code style='word-break: break-all; background: white; padding: 10px; display: block; margin: 10px 0; border-radius: 4px;'>UPDATE admin SET password = '{$hashed}' WHERE username = 'your_username';</code>";
        echo "</div>";
    }
}

echo "<br><h3>💡 Common Issues & Solutions:</h3>";
echo "<ul>";
echo "<li><strong>Password not hashed:</strong> Use password_hash() to hash password in database</li>";
echo "<li><strong>Wrong username:</strong> Check exact spelling and case sensitivity</li>";
echo "<li><strong>Wrong hash algorithm:</strong> Use PASSWORD_DEFAULT in password_hash()</li>";
echo "<li><strong>Database encoding:</strong> Ensure UTF-8 encoding</li>";
echo "</ul>";

echo "<br><strong>🔗 Links:</strong><br>";
echo "<a href='admin.php'>🎛️ Admin Panel</a> | ";
echo "<a href='index.php'>🏠 Index Page</a>";

?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    background: #f5f5f5;
}

h2, h3 {
    color: #5fb3a3;
    border-bottom: 2px solid #5fb3a3;
    padding-bottom: 10px;
}

table {
    background: white;
    width: 100%;
    font-size: 12px;
}

th, td {
    padding: 8px 12px;
    text-align: left;
    border: 1px solid #ddd;
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
    margin-right: 10px;
}

a:hover {
    background: #5fb3a3;
    color: white;
}

code {
    background: #f8f9fa;
    padding: 2px 5px;
    border-radius: 3px;
    font-family: monospace;
}
</style>