<?php
/**
 * Session Debug Script
 * Place this in your web root and access it to check your session
 */

session_start();

echo "<h1>Session Debug</h1>";
echo "<style>
body { font-family: Arial; padding: 20px; }
.success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; }
.error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; }
.info { color: blue; background: #d1ecf1; padding: 10px; border-radius: 5px; }
pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow: auto; }
</style>";

echo "<h2>1. Session Status</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p class='success'>✓ Session is active</p>";
    echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
} else {
    echo "<p class='error'>✗ Session is not active</p>";
}

echo "<h2>2. Session Data</h2>";
if (!empty($_SESSION)) {
    echo "<p class='success'>✓ Session contains data:</p>";
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
    
    // Check for common user ID keys
    $userIdKeys = ['user_id', 'id', 'userId', 'uid', 'logged_in', 'login_id'];
    echo "<h3>Looking for user ID...</h3>";
    foreach ($userIdKeys as $key) {
        if (isset($_SESSION[$key])) {
            echo "<p class='success'>✓ Found: \$_SESSION['$key'] = " . $_SESSION[$key] . "</p>";
        }
    }
} else {
    echo "<p class='error'>✗ Session is empty - User is not logged in!</p>";
    echo "<p>Please log in first, then run this script again.</p>";
}

echo "<h2>3. Cookie Information</h2>";
if (!empty($_COOKIE)) {
    echo "<p class='info'>Cookies present:</p>";
    echo "<pre>" . print_r($_COOKIE, true) . "</pre>";
} else {
    echo "<p class='info'>No cookies found</p>";
}

echo "<h2>4. What to check:</h2>";
echo "<ol>";
echo "<li>Make sure you're logged in to the website</li>";
echo "<li>Check what key is used to store the user_id in \$_SESSION</li>";
echo "<li>Update your BasicPage::getLoginInfo() method to use the correct session key</li>";
echo "<li>The session key used in login should match the key checked in getLoginInfo()</li>";
echo "</ol>";

echo "<hr>";
echo "<h2>5. Test Login Info Method</h2>";

// Try to simulate what getLoginInfo() might be doing
$possibleUserIds = [
    'Direct check' => $_SESSION['user_id'] ?? 'NOT SET',
    'id key' => $_SESSION['id'] ?? 'NOT SET',
    'login_id key' => $_SESSION['login_id'] ?? 'NOT SET',
];

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Method</th><th>Result</th></tr>";
foreach ($possibleUserIds as $method => $value) {
    echo "<tr><td>$method</td><td>" . ($value !== 'NOT SET' ? "<strong>$value</strong>" : "<em>$value</em>") . "</td></tr>";
}
echo "</table>";

echo "<p class='info'><strong>Next step:</strong> Make sure your BasicPage::getLoginInfo() uses the correct session variable name!</p>";
?>