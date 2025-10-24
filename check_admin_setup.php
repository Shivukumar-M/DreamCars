<?php
// check_admin_setup.php
require_once 'classes/db/Database.php';

echo "<h1>Admin Setup Check</h1>";

try {
    $db = Database::getInstance()->getDb();
    
    echo "<h2>1. Database Connection</h2>";
    echo "<p style='color: green;'>✅ Database connected successfully</p>";
    
    echo "<h2>2. Admins Table Check</h2>";
    $stmt = $db->query("SHOW TABLES LIKE 'admins'");
    $adminsTableExists = $stmt->fetch();
    
    if ($adminsTableExists) {
        echo "<p style='color: green;'>✅ Admins table exists</p>";
        
        // Check admins table structure
        $stmt = $db->query("DESCRIBE admins");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Admins Table Structure:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "<td>{$col['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ Admins table does not exist</p>";
    }
    
    echo "<h2>3. Admin Accounts</h2>";
    $stmt = $db->query("
        SELECT a._id as admin_id, u._id as user_id, u.username, u.email, u.first_name, u.last_name 
        FROM admins a 
        LEFT JOIN user u ON a.user_id = u._id
    ");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($admins)) {
        echo "<p style='color: red;'>❌ No admin accounts found</p>";
        echo "<p>Run <code>php create_admin.php</code> to create a default admin account.</p>";
    } else {
        echo "<p style='color: green;'>✅ Admin accounts found:</p>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Admin ID</th><th>User ID</th><th>Username</th><th>Email</th><th>Name</th></tr>";
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>{$admin['admin_id']}</td>";
            echo "<td>{$admin['user_id']}</td>";
            echo "<td>{$admin['username']}</td>";
            echo "<td>{$admin['email']}</td>";
            echo "<td>{$admin['first_name']} {$admin['last_name']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>4. Test Login</h2>";
    echo "<p>Try logging in at: <a href='http://localhost:8000/admin' target='_blank'>http://localhost:8000/admin</a></p>";
    echo "<p><strong>Default credentials:</strong></p>";
    echo "<ul>";
    echo "<li>Username: <code>admin</code></li>";
    echo "<li>Password: <code>admin123</code></li>";
    echo "<li>Email: <code>admin@rentdream.com</code></li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}