<?php
// reset_admin_password.php
require_once 'classes/db/Database.php';

try {
    $db = Database::getInstance()->getDb();
    
    // Reset admin password to 'admin123'
    $newPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("UPDATE user SET password = ? WHERE username = 'admin'");
    $stmt->execute([$newPassword]);
    
    echo "✅ Admin password reset successfully!\n";
    echo "New password: admin123\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}