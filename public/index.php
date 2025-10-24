<?php
// public/index.php

// Enable error reporting but don't show warnings in production
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Buffer output to prevent headers already sent errors
ob_start();

try {
    require_once __DIR__ . '/../classes/Router.php';
    Router::route();
} catch (Exception $e) {
    // Clear any previous output
    ob_clean();
    
    // Set proper error header
    header('HTTP/1.1 500 Internal Server Error');
    
    // Display error page
    echo "<h1>Application Error</h1>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    
    // Only show detailed errors in development
    if (ini_get('display_errors')) {
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
}

// Flush output buffer
ob_end_flush();