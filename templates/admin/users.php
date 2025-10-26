<?php
// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: /admin");
    exit;
}

$db = Database::getInstance()->getDb();

// Get users - removed join_date if it doesn't exist
$users = $db->query("SELECT _id, first_name, last_name, email, username, ph_no FROM user ORDER BY _id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$newThisMonth = $db->query("SELECT COUNT(*) FROM user")->fetchColumn(); // Simplified - counts all users

// Check if rentals table exists for active renters count
try {
    $activeRenters = $db->query("SELECT COUNT(DISTINCT user_id) FROM rentals")->fetchColumn();
} catch (PDOException $e) {
    $activeRenters = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Hide the main website navbar in admin panel */
        nav, .navbar, header:not(.admin-header) {
            display: none !important;
        }
        
        /* Ensure admin header stays visible */
        .admin-header, header.bg-white {
            display: block !important;
        }
        
        /* Add padding to account for fixed header */
        body {
            padding-top: 80px;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Admin Header - THIS STAYS VISIBLE -->
    <header class="bg-white shadow-lg border-b border-gray-200 w-full z-50 fixed top-0 left-0 right-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-600 p-2 rounded-lg">
                            <i class="fas fa-shield-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Manage Users</h1>
                            <p class="text-sm text-gray-600">Admin Panel</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="/admin/dashboard" class="text-gray-600 hover:text-blue-600 transition duration-200 flex items-center">
                            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                        </a>
                        <a href="/admin/users" class="text-blue-600 font-semibold transition duration-200 flex items-center">
                            <i class="fas fa-users mr-2"></i> Users
                        </a>
                        <a href="/admin/rentals" class="text-gray-600 hover:text-blue-600 transition duration-200 flex items-center">
                            <i class="fas fa-calendar-alt mr-2"></i> Rentals
                        </a>
                    </div>
                    <a href="/logout" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200 flex items-center shadow-md">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <!-- Users Table -->
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            User
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Contact
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            User ID
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach($users as $user): ?>
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user text-blue-600"></i>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        @<?= htmlspecialchars($user['username']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= htmlspecialchars($user['email']) ?></div>
                                            <?php if($user['ph_no']): ?>
                                                <div class="text-sm text-gray-500"><?= htmlspecialchars($user['ph_no']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                ID: <?= $user['_id'] ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button class="text-blue-600 hover:text-blue-900 transition duration-150"
                                                        onclick="viewUserDetails(<?= $user['_id'] ?>)">
                                                    <i class="fas fa-eye mr-1"></i>View
                                                </button>
                                                <button class="text-green-600 hover:text-green-900 transition duration-150"
                                                        onclick="sendMessage(<?= $user['_id'] ?>)">
                                                    <i class="fas fa-envelope mr-1"></i>Message
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <?php if(empty($users)): ?>
            <div class="text-center py-12">
                <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Users Found</h3>
                <p class="text-gray-500 mb-6">There are no registered users in the system yet.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Quick Stats -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-blue-50 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-blue-900">Total Users</h3>
                        <p class="text-2xl font-bold text-blue-600"><?= count($users) ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-plus text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-green-900">Registered Users</h3>
                        <p class="text-2xl font-bold text-green-600"><?= $newThisMonth ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-purple-50 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-check text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-purple-900">Active Renters</h3>
                        <p class="text-2xl font-bold text-purple-600"><?= $activeRenters ?></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function viewUserDetails(userId) {
    alert('View details for user ID: ' + userId);
    // In a real application, you would show a modal or redirect to user details page
}

function sendMessage(userId) {
    const message = prompt('Enter message to send to user:');
    if (message) {
        alert('Message sent to user ID: ' + userId + '\nMessage: ' + message);
        // In a real application, you would send this to your backend
    }
}
</script>