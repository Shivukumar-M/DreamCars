<?php
// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: /admin");
    exit;
}

$db = Database::getInstance()->getDb();

// Initialize variables
$rentals = [];
$errorMessage = '';
$successMessage = '';

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['rental_id']) && isset($_POST['action'])) {
        $rental_id = $_POST['rental_id'];
        $action = $_POST['action'];
        
        try {
            switch($action) {
                case 'complete':
                    $stmt = $db->prepare("UPDATE rentals SET status = 'completed', end_time = NOW() WHERE _id = ?");
                    $stmt->execute([$rental_id]);
                    $successMessage = "Rental #$rental_id marked as completed!";
                    break;
                    
                case 'cancel':
                    $stmt = $db->prepare("UPDATE rentals SET status = 'cancelled', end_time = NOW() WHERE _id = ?");
                    $stmt->execute([$rental_id]);
                    
                    // Also update car stock when cancelled
                    $rentalStmt = $db->prepare("SELECT car_id FROM rentals WHERE _id = ?");
                    $rentalStmt->execute([$rental_id]);
                    $rental = $rentalStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($rental) {
                        $updateStmt = $db->prepare("UPDATE cars SET stock = stock + 1 WHERE _id = ?");
                        $updateStmt->execute([$rental['car_id']]);
                    }
                    
                    $successMessage = "Rental #$rental_id cancelled successfully!";
                    break;
                    
                case 'delete':
                    // Get car_id first to update stock
                    $rentalStmt = $db->prepare("SELECT car_id FROM rentals WHERE _id = ?");
                    $rentalStmt->execute([$rental_id]);
                    $rental = $rentalStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($rental) {
                        // Update car stock
                        $updateStmt = $db->prepare("UPDATE cars SET stock = stock + 1 WHERE _id = ?");
                        $updateStmt->execute([$rental['car_id']]);
                        
                        // Delete the rental
                        $deleteStmt = $db->prepare("DELETE FROM rentals WHERE _id = ?");
                        $deleteStmt->execute([$rental_id]);
                        $successMessage = "Rental #$rental_id deleted successfully!";
                    }
                    break;
            }
        } catch (PDOException $e) {
            error_log("Rental action error: " . $e->getMessage());
            $errorMessage = "Error processing request: " . $e->getMessage();
        }
    }
}

try {
    // Get rentals with user and car information from RENTALS table
    $stmt = $db->query("
        SELECT 
            r._id,
            r.user_id,
            r.car_id,
            r.mode,
            r.value,
            r.amount,
            r.start_time,
            r.end_time,
            r.status,
            r.created_at,
            u.first_name,
            u.last_name,
            u.email,
            c.name as car_name,
            c.pic as car_image
        FROM rentals r
        LEFT JOIN user u ON r.user_id = u._id
        LEFT JOIN cars c ON r.car_id = c._id
        ORDER BY r.created_at DESC
    ");
    
    $rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Rentals fetch error: " . $e->getMessage());
    $errorMessage = "Error loading rentals: " . $e->getMessage();
}

// Calculate statistics
$totalRentals = count($rentals);
$totalRevenue = array_sum(array_column($rentals, 'amount'));
$activeCustomers = count(array_unique(array_column($rentals, 'user_id')));
$activeRentals = count(array_filter($rentals, function($rental) { 
    return ($rental['status'] ?? 'active') === 'active'; 
}));
$completedRentals = count(array_filter($rentals, function($rental) { 
    return ($rental['status'] ?? '') === 'completed'; 
}));
$cancelledRentals = count(array_filter($rentals, function($rental) { 
    return ($rental['status'] ?? '') === 'cancelled'; 
}));
?>


<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <a href="/admin/dashboard" class="text-blue-600 hover:text-blue-500 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Manage Rentals</h1>
                        <p class="text-sm text-gray-600">View and manage all rental transactions</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if($totalRentals > 0): ?>
                    <span class="text-sm text-gray-600">
                        Total Rentals: <span class="font-semibold"><?= $totalRentals ?></span>
                    </span>
                    <?php endif; ?>
                    <a href="/admin/dashboard" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                        <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <?php if($errorMessage): ?>
            <!-- Error Message -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-2xl mr-3"></i>
                    <div>
                        <h3 class="text-lg font-medium text-red-800">Error</h3>
                        <p class="text-red-700 mt-1"><?= htmlspecialchars($errorMessage) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if($successMessage): ?>
            <!-- Success Message -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
                    <div>
                        <h3 class="text-lg font-medium text-green-800">Success</h3>
                        <p class="text-green-700 mt-1"><?= htmlspecialchars($successMessage) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if($totalRentals === 0 && !$errorMessage): ?>
            <!-- No data message -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-8 text-center">
                <i class="fas fa-calendar-times text-blue-400 text-6xl mb-4"></i>
                <h3 class="text-2xl font-bold text-blue-900 mb-3">No Rentals Yet</h3>
                <p class="text-blue-700 mb-2">The rentals table is ready, but no bookings have been made yet.</p>
                <p class="text-sm text-blue-600 mb-6">When users rent cars, their bookings will appear here for you to manage.</p>
                <div class="flex justify-center space-x-4">
                    <a href="/" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition duration-200 inline-flex items-center">
                        <i class="fas fa-car mr-2"></i>Browse Cars
                    </a>
                    <a href="/admin/dashboard" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition duration-200 inline-flex items-center">
                        <i class="fas fa-tachometer-alt mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                            <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Total Rentals</h3>
                            <p class="text-2xl font-bold text-gray-900"><?= $totalRentals ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                            <i class="fas fa-indian-rupee-sign text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Total Revenue</h3>
                            <p class="text-2xl font-bold text-gray-900">₹<?= number_format($totalRevenue, 2) ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                            <i class="fas fa-users text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Active Rentals</h3>
                            <p class="text-2xl font-bold text-gray-900"><?= $activeRentals ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                            <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Completed</h3>
                            <p class="text-2xl font-bold text-gray-900"><?= $completedRentals ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Summary -->
            <div class="mb-6 bg-white shadow rounded-lg p-4">
                <div class="flex flex-wrap gap-4 justify-center">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Active: <?= $activeRentals ?></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Completed: <?= $completedRentals ?></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Cancelled: <?= $cancelledRentals ?></span>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="mb-6 bg-white shadow rounded-lg p-4">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Rentals</label>
                        <input type="text" id="search" placeholder="Search by customer name, car name, or email..." 
                               class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                        <select id="status-filter" class="border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label for="mode-filter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Mode</label>
                        <select id="mode-filter" class="border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="all">All Modes</option>
                            <option value="hour">Per Hour</option>
                            <option value="day">Per Day</option>
                            <option value="km">Per KM</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Rentals Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6" id="rentals-container">
                <?php foreach($rentals as $rental): ?>
                    <?php
                    $customerName = trim(($rental['first_name'] ?? '') . ' ' . ($rental['last_name'] ?? ''));
                    if(empty($customerName)) $customerName = 'Unknown User';
                    
                    $customerEmail = $rental['email'] ?? 'No email';
                    $carName = $rental['car_name'] ?? 'Unknown Car';
                    $carImage = $rental['car_image'] ?? '';
                    $status = $rental['status'] ?? 'active';
                    
                    $statusColor = 'from-blue-600 to-blue-700';
                    $statusIcon = 'fas fa-play-circle';
                    if($status == 'completed') {
                        $statusColor = 'from-green-600 to-green-700';
                        $statusIcon = 'fas fa-check-circle';
                    }
                    if($status == 'cancelled') {
                        $statusColor = 'from-red-600 to-red-700';
                        $statusIcon = 'fas fa-times-circle';
                    }
                    ?>
                    
                    <div class="bg-white shadow-lg rounded-xl overflow-hidden rental-card hover:shadow-xl transition duration-300" 
                         data-status="<?= $status ?>"
                         data-mode="<?= $rental['mode'] ?>"
                         data-search="<?= strtolower(htmlspecialchars($customerName . ' ' . $carName . ' ' . $customerEmail)) ?>">
                        
                        <!-- Header with status-based color -->
                        <div class="bg-gradient-to-r <?= $statusColor ?> px-4 py-3">
                            <div class="flex justify-between items-center">
                                <span class="text-white font-semibold">Rental #<?= $rental['_id'] ?></span>
                                <div class="flex items-center space-x-2">
                                    <span class="bg-white bg-opacity-20 text-white text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-<?= $rental['mode'] == 'hour' ? 'clock' : ($rental['mode'] == 'day' ? 'sun' : 'road') ?> mr-1"></i>
                                        <?= ucfirst($rental['mode']) ?>
                                    </span>
                                    <span class="bg-white bg-opacity-30 text-white text-xs px-2 py-1 rounded-full font-medium">
                                        <i class="<?= $statusIcon ?> mr-1"></i>
                                        <?= ucfirst($status) ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <!-- Customer Info -->
                            <div class="flex items-center mb-4 pb-4 border-b border-gray-100">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($customerName) ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($customerEmail) ?></p>
                                    <p class="text-xs text-gray-400">User ID: <?= $rental['user_id'] ?></p>
                                </div>
                            </div>

                            <!-- Car Info -->
                            <div class="flex items-center mb-4 pb-4 border-b border-gray-100">
                                <div class="flex-shrink-0 h-12 w-12 bg-gray-100 rounded-lg overflow-hidden">
                                    <?php if(!empty($carImage)): ?>
                                        <img src="/<?= $carImage ?>" alt="<?= htmlspecialchars($carName) ?>" class="h-full w-full object-cover">
                                    <?php else: ?>
                                        <div class="h-full w-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-car text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($carName) ?></p>
                                    <p class="text-xs text-gray-500">Car ID: <?= $rental['car_id'] ?></p>
                                </div>
                            </div>

                            <!-- Rental Details -->
                            <div class="space-y-2 text-sm mb-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 flex items-center">
                                        <i class="fas fa-<?= $rental['mode'] == 'hour' ? 'clock' : ($rental['mode'] == 'day' ? 'calendar' : 'road') ?> text-blue-500 mr-1"></i>
                                        <?= $rental['mode'] == 'hour' ? 'Hours' : ($rental['mode'] == 'day' ? 'Days' : 'Distance') ?>:
                                    </span>
                                    <span class="font-medium text-gray-900">
                                        <?= $rental['value'] ?> <?= $rental['mode'] ?><?= $rental['value'] > 1 ? 's' : '' ?>
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 flex items-center">
                                        <i class="fas fa-play-circle text-green-500 mr-1"></i>
                                        Start Time:
                                    </span>
                                    <span class="font-medium text-gray-900 text-xs">
                                        <?= date('M j, Y g:i A', strtotime($rental['start_time'])) ?>
                                    </span>
                                </div>
                                <?php if(!empty($rental['end_time'])): ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 flex items-center">
                                        <i class="fas fa-stop-circle text-red-500 mr-1"></i>
                                        End Time:
                                    </span>
                                    <span class="font-medium text-gray-900 text-xs">
                                        <?= date('M j, Y g:i A', strtotime($rental['end_time'])) ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                                <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                    <span class="text-gray-600 font-medium flex items-center">
                                        <i class="fas fa-indian-rupee-sign text-green-500 mr-1"></i>
                                        Amount:
                                    </span>
                                    <span class="font-bold text-blue-600 text-lg">
                                        ₹<?= number_format($rental['amount'], 2) ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 flex space-x-2">
                                <?php if($status == 'active'): ?>
                                    <form method="POST" class="flex-1">
                                        <input type="hidden" name="rental_id" value="<?= $rental['_id'] ?>">
                                        <input type="hidden" name="action" value="complete">
                                        <button type="submit" 
                                                class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition duration-200 flex items-center justify-center">
                                            <i class="fas fa-check mr-1"></i>Complete
                                        </button>
                                    </form>
                                    <form method="POST" class="flex-1">
                                        <input type="hidden" name="rental_id" value="<?= $rental['_id'] ?>">
                                        <input type="hidden" name="action" value="cancel">
                                        <button type="submit" 
                                                class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition duration-200 flex items-center justify-center"
                                                onclick="return confirm('Are you sure you want to cancel this rental?')">
                                            <i class="fas fa-times mr-1"></i>Cancel
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="flex-1 text-center py-2">
                                        <span class="text-sm font-semibold
                                            <?= $status == 'completed' ? 'text-green-600' : 'text-red-600' ?>">
                                            <i class="fas fa-<?= $status == 'completed' ? 'check-circle' : 'times-circle' ?> mr-1"></i>
                                            <?= ucfirst($status) ?>
                                        </span>
                                    </div>
                                    <form method="POST" class="flex-1">
                                        <input type="hidden" name="rental_id" value="<?= $rental['_id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" 
                                                class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition duration-200 flex items-center justify-center"
                                                onclick="return confirm('Are you sure you want to delete this rental record? This cannot be undone.')">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('status-filter');
    const modeFilter = document.getElementById('mode-filter');
    const searchInput = document.getElementById('search');
    const rentalCards = document.querySelectorAll('.rental-card');

    function filterRentals() {
        const statusValue = statusFilter?.value || 'all';
        const modeValue = modeFilter?.value || 'all';
        const searchValue = searchInput?.value.toLowerCase() || '';

        rentalCards.forEach(card => {
            const status = card.getAttribute('data-status');
            const mode = card.getAttribute('data-mode');
            const searchText = card.getAttribute('data-search');

            let statusMatch = statusValue === 'all' || status === statusValue;
            let modeMatch = modeValue === 'all' || mode === modeValue;
            let searchMatch = searchText.includes(searchValue);

            if (statusMatch && modeMatch && searchMatch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', filterRentals);
    }
    
    if (modeFilter) {
        modeFilter.addEventListener('change', filterRentals);
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', filterRentals);
    }
});
</script>