<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#3B82F6">
    <meta name="msapplication-navbutton-color" content="#3B82F6">
    <meta name="apple-mobile-web-app-status-bar-style" content="#3B82F6">
    <link rel="shortcut icon" href="/images/car-logo.png" />
    <title><?= $title ?> - RentDream</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .btn-primary {
            @apply bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200;
        }
        .btn-secondary {
            @apply bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-200;
        }
        .btn-danger {
            @apply bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-200;
        }
        .card {
            @apply bg-white rounded-lg shadow-md hover:shadow-lg transition duration-300;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
    <?php
    $flash_message = $_SESSION['flash_message'];
    $flash_type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
    ?>
    <div id="flash-message" class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 <?= $flash_type == 'success' ? 'border-green-500' : 'border-red-500' ?> transform translate-x-full transition-transform duration-300">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <?php if ($flash_type == 'success'): ?>
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    <?php else: ?>
                        <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                    <?php endif; ?>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900">
                        <?= htmlspecialchars($flash_message) ?>
                    </p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="hideFlashMessage()">
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Show flash message with animation
    document.addEventListener('DOMContentLoaded', function() {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            // Show message
            setTimeout(() => {
                flashMessage.classList.remove('translate-x-full');
                flashMessage.classList.add('translate-x-0');
            }, 100);

            // Auto hide after 5 seconds
            setTimeout(() => {
                hideFlashMessage();
            }, 5000);
        }
    });

    function hideFlashMessage() {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            flashMessage.classList.remove('translate-x-0');
            flashMessage.classList.add('translate-x-full');
            setTimeout(() => {
                if (flashMessage.parentNode) {
                    flashMessage.remove();
                }
            }, 300);
        }
    }
    </script>
    <?php endif; ?>

    <?php require_once('../templates/components/navbar.php') ?>