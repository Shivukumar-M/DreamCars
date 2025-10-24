<?php
// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: /admin");
    exit;
}

// ... (car and user deletion code remains the same)

// Initialize variables
$userCount = 0;
$carCount = 0;
$rentalCount = 0;
$totalRevenue = 0;
$recentRentals = [];
$popularCars = [];
$allCars = [];
$allUsers = [];

try {
    $db = Database::getInstance()->getDb();
    
    // Get counts - using correct table names
    $userCount = $db->query("SELECT COUNT(*) FROM user")->fetchColumn();
    $carCount = $db->query("SELECT COUNT(*) FROM cars")->fetchColumn();
    
    // Get all cars for management
    $allCars = $db->query("SELECT * FROM cars ORDER BY _id DESC")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all users for management
    $allUsers = $db->query("SELECT * FROM user ORDER BY _id DESC")->fetchAll(PDO::FETCH_ASSOC);
    
    // Check if transaction table exists by trying to query it - CHANGED rentals to transaction
    try {
        $rentalCount = $db->query("SELECT COUNT(*) FROM transaction")->fetchColumn(); // CHANGED
        $rentalsTableExists = true;
    } catch (PDOException $e) {
        $rentalsTableExists = false;
        $rentalCount = 0;
        $totalRevenue = 0;
    }
    
    if ($rentalsTableExists) {
        // Get total revenue - CHANGED rentals to transaction
        $totalRevenue = $db->query("SELECT SUM(amount) FROM transaction")->fetchColumn() ?: 0; // CHANGED
        
        // Get recent rentals - simplified query - CHANGED rentals to transaction
        try {
            $recentRentals = $db->query("
                SELECT 
                    t.*, 
                    u.first_name, 
                    u.last_name, 
                    u.email, 
                    c.name as car_name,
                    t.time as start_time,  -- ADDED: alias for time
                    'active' as status     -- ADDED: default status
                FROM transaction t         -- CHANGED: rentals to transaction
                JOIN user u ON t.user_id = u._id 
                JOIN cars c ON t.car_id = c._id 
                ORDER BY t.time DESC       -- CHANGED: start_time to time
                LIMIT 5
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $recentRentals = [];
        }
        
        // Get popular cars - CHANGED rentals to transaction
        try {
            $popularCars = $db->query("
                SELECT c._id, c.name, c.pic, COUNT(t._id) as rental_count, c.stock
                FROM cars c 
                LEFT JOIN transaction t ON c._id = t.car_id  -- CHANGED: rentals to transaction
                GROUP BY c._id 
                ORDER BY rental_count DESC 
                LIMIT 3
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $popularCars = [];
        }
    }
    
} catch (PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage());
}
?>


<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
    <!-- Header -->
    <header class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-600 p-2 rounded-lg">
                            <i class="fas fa-shield-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                            <p class="text-sm text-gray-600">Welcome back, <?= $_SESSION['admin_name'] ?? 'Administrator' ?></p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="/" class="text-gray-600 hover:text-blue-600 transition duration-200 flex items-center">
                            <i class="fas fa-home mr-2"></i> Main Site
                        </a>
                        <a href="/admin/users" class="text-gray-600 hover:text-blue-600 transition duration-200 flex items-center">
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

    <!-- Success/Error Messages -->
    <?php if(isset($_SESSION['success_message'])): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline"><?= $_SESSION['success_message'] ?></span>
                <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['error_message'])): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline"><?= $_SESSION['error_message'] ?></span>
                <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Users Stat -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2"><?= $userCount ?></p>
                            <p class="text-xs text-green-600 mt-1 flex items-center">
                                <i class="fas fa-arrow-up mr-1"></i> Registered users
                            </p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cars Stat -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Cars</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2"><?= $carCount ?></p>
                            <p class="text-xs text-blue-600 mt-1 flex items-center">
                                <i class="fas fa-car mr-1"></i> In fleet
                            </p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-car text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rentals Stat -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Rentals</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2"><?= $rentalCount ?></p>
                            <p class="text-xs text-yellow-600 mt-1 flex items-center">
                                <i class="fas fa-calendar-check mr-1"></i> All bookings
                            </p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <i class="fas fa-calendar-check text-yellow-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Stat -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                ₹<?= number_format($totalRevenue, 2) ?>
                            </p>
                            <p class="text-xs text-purple-600 mt-1 flex items-center">
                                <i class="fas fa-chart-line mr-1"></i> All time
                            </p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-full">
                            <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Quick Actions -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-bolt text-yellow-500 mr-3"></i>
                            Quick Actions
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Add Car -->
                            <a href="/admin/add-car" class="group bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4 rounded-xl hover:from-blue-600 hover:to-blue-700 transition duration-300 transform hover:-translate-y-1 shadow-md">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <i class="fas fa-plus-circle text-2xl mb-2"></i>
                                        <h3 class="font-semibold">Add New Car</h3>
                                        <p class="text-blue-100 text-sm mt-1">Add to fleet</p>
                                    </div>
                                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition duration-200"></i>
                                </div>
                            </a>

                            <!-- Manage Users -->
                            <a href="#all-users" class="group bg-gradient-to-r from-green-500 to-green-600 text-white p-4 rounded-xl hover:from-green-600 hover:to-green-700 transition duration-300 transform hover:-translate-y-1 shadow-md">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <i class="fas fa-users-cog text-2xl mb-2"></i>
                                        <h3 class="font-semibold">Manage Users</h3>
                                        <p class="text-green-100 text-sm mt-1">View all users</p>
                                    </div>
                                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition duration-200"></i>
                                </div>
                            </a>

                            <!-- Manage Rentals -->
                            <a href="/admin/rentals" class="group bg-gradient-to-r from-purple-500 to-purple-600 text-white p-4 rounded-xl hover:from-purple-600 hover:to-purple-700 transition duration-300 transform hover:-translate-y-1 shadow-md">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <i class="fas fa-calendar-alt text-2xl mb-2"></i>
                                        <h3 class="font-semibold">Manage Rentals</h3>
                                        <p class="text-purple-100 text-sm mt-1">View bookings</p>
                                    </div>
                                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition duration-200"></i>
                                </div>
                            </a>

                            <!-- View All Cars -->
                            <a href="#all-cars" class="group bg-gradient-to-r from-orange-500 to-orange-600 text-white p-4 rounded-xl hover:from-orange-600 hover:to-orange-700 transition duration-300 transform hover:-translate-y-1 shadow-md">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <i class="fas fa-car-side text-2xl mb-2"></i>
                                        <h3 class="font-semibold">View All Cars</h3>
                                        <p class="text-orange-100 text-sm mt-1">Manage fleet</p>
                                    </div>
                                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition duration-200"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Cars -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-fire text-red-500 mr-3"></i>
                        Popular Cars
                    </h2>
                    <div class="space-y-4">
                        <?php if(empty($popularCars)): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-car text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500 text-sm">No rental data yet</p>
                            </div>
                        <?php else: ?>
                            <?php foreach($popularCars as $car): ?>
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200">
                                    <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg overflow-hidden">
                                        <?php if($car['pic']): ?>
                                            <img src="/<?= $car['pic'] ?>" alt="<?= $car['name'] ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-full bg-blue-200 flex items-center justify-center">
                                                <i class="fas fa-car text-blue-600"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate"><?= $car['name'] ?></p>
                                        <p class="text-xs text-gray-500"><?= $car['rental_count'] ?> rentals</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs <?= $car['stock'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                            <?= $car['stock'] ?> in stock
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- All Users Management Section -->
        <div id="all-users" class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-8">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-users text-green-500 mr-3"></i>
                        All Users Management
                    </h2>
                    <span class="text-sm text-gray-500"><?= count($allUsers) ?> users total</span>
                </div>

                <?php if(empty($allUsers)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500 text-lg">No users registered yet</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach($allUsers as $user): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user text-blue-600"></i>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500">ID: <?= $user['_id'] ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= htmlspecialchars($user['email']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= isset($user['created_at']) ? date('M j, Y', strtotime($user['created_at'])) : 'N/A' ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete user <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>? This action cannot be undone.');">
                                                <input type="hidden" name="user_id" value="<?= $user['_id'] ?>">
                                                <button type="submit" name="delete_user" class="bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded transition duration-200">
                                                    <i class="fas fa-trash mr-1"></i>Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- All Cars Management Section -->
        <div id="all-cars" class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-8">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-car text-blue-500 mr-3"></i>
                        All Cars Management
                    </h2>
                    <span class="text-sm text-gray-500"><?= count($allCars) ?> cars total</span>
                </div>

                <?php if(empty($allCars)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-car text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500 text-lg mb-2">No cars in the fleet</p>
                        <p class="text-gray-400 text-sm mb-6">Add your first car to get started</p>
                        <a href="/admin/add-car" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200 inline-flex items-center">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Add First Car
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach($allCars as $car): ?>
                            <div class="bg-gray-50 rounded-xl border border-gray-200 hover:shadow-md transition duration-300 overflow-hidden">
                                <div class="h-48 bg-gray-200 overflow-hidden">
                                    <?php if($car['pic']): ?>
                                        <img src="/<?= $car['pic'] ?>" alt="<?= $car['name'] ?>" class="w-full h-full object-cover hover:scale-105 transition duration-300">
                                    <?php else: ?>
                                        <div class="w-full h-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-car text-blue-400 text-4xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-bold text-gray-900 text-lg mb-2"><?= htmlspecialchars($car['name']) ?></h3>
                                    <div class="space-y-2 mb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Price:</span>
                                            <span class="font-semibold text-blue-600">₹<?= $car['price'] ?? '0' ?>/day</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Stock:</span>
                                            <span class="font-semibold <?= $car['stock'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                                <?= $car['stock'] ?> available
                                            </span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Type:</span>
                                            <span class="font-medium text-gray-700"><?= $car['type'] ?? 'N/A' ?></span>
                                        </div>
                                    </div>
                                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete <?= htmlspecialchars($car['name']) ?>? This action cannot be undone.');">
                                        <input type="hidden" name="car_id" value="<?= $car['_id'] ?>">
                                        <button type="submit" name="delete_car" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg font-medium transition duration-200 flex items-center justify-center">
                                            <i class="fas fa-trash mr-2"></i>
                                            Delete Car
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-history text-blue-500 mr-3"></i>
                        Recent Activity
                    </h2>
                    <a href="/admin/rentals" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        View All
                    </a>
                </div>
                
                <?php if(empty($recentRentals)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-times text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No recent activity</p>
                        <p class="text-sm text-gray-400 mt-1">Rental transactions will appear here</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach($recentRentals as $rental): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition duration-200 group">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-car text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm"><?= htmlspecialchars($rental['car_name']) ?></p>
                                        <p class="text-xs text-gray-500">by <?= htmlspecialchars($rental['first_name'] . ' ' . $rental['last_name']) ?></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-blue-600 text-sm">₹<?= $rental['amount'] ?? '0' ?></p>
                                    <p class="text-xs text-gray-500"><?= date('M j, g:i A', strtotime($rental['start_time'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script>
// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>