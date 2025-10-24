<?php
// create_admin.php - Run this once to create default admin
require_once 'classes/db/Database.php';

try {
    $db = Database::getInstance()->getDb();
    
    echo "=== Creating Default Admin Account ===\n\n";
    
    // Check if admin already exists
    $stmt = $db->query("SELECT COUNT(*) FROM admins");
    $adminCount = $stmt->fetchColumn();
    
    if ($adminCount > 0) {
        echo "❌ Admin account already exists!\n";
        echo "Existing admins:\n";
        
        $stmt = $db->query("
            SELECT u._id, u.username, u.email, u.first_name, u.last_name 
            FROM user u 
            JOIN admins a ON u._id = a.user_id
        ");
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($admins as $admin) {
            echo " - ID: {$admin['_id']}, Username: {$admin['username']}, Email: {$admin['email']}, Name: {$admin['first_name']} {$admin['last_name']}\n";
        }
        exit;
    }
    
    // Check if admin user already exists in user table
    $stmt = $db->prepare("SELECT _id FROM user WHERE username = ? OR email = ?");
    $stmt->execute(['admin', 'admin@rentdream.com']);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingUser) {
        echo "⚠️  Admin user exists but not in admins table. Adding to admins...\n";
        $userId = $existingUser['_id'];
    } else {
        // Create new admin user
        echo "Creating new admin user...\n";
        
        $username = 'admin';
        $email = 'admin@rentdream.com';
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $firstName = 'RentDream';
        $lastName = 'Administrator';
        
        // Insert into user table
        $stmt = $db->prepare("
            INSERT INTO user (username, email, password, first_name, last_name, street, city, state, country, zip, join_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $username, 
            $email, 
            $password, 
            $firstName, 
            $lastName,
            '123 Admin Street',
            'Admin City', 
            'Admin State',
            'Admin Country',
            '000000'
        ]);
        
        $userId = $db->lastInsertId();
        echo "✅ Admin user created with ID: $userId\n";
    }
    
    // Insert into admins table
    $stmt = $db->prepare("INSERT INTO admins (user_id) VALUES (?)");
    $stmt->execute([$userId]);
    
    echo "✅ Default admin account created successfully!\n\n";
    echo "=== Login Credentials ===\n";
    echo "📧 Username: admin\n";
    echo "🔑 Password: admin123\n";
    echo "📨 Email: admin@rentdream.com\n";
    echo "🆔 User ID: $userId\n\n";
    echo "⚠️  IMPORTANT: Change the default password after first login!\n";
    echo "🔗 Admin Login URL: http://localhost:8000/admin\n";
    
} catch (PDOException $e) {
    echo "❌ Error creating admin account: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n";
}