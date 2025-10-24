<?php if ($loginInfo == 0) {
    include_once('../templates/logout.php');
} else { ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Header -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-8">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <div class="relative">
                            <img src="<?= $user['avatar'] ?>" 
                                 alt="<?= $user['first_name'] ?>" 
                                 class="w-24 h-24 rounded-full border-4 border-white shadow-lg">
                            <?php if($admin): ?>
                                <div class="absolute -bottom-2 -right-2 bg-red-500 text-white p-1 rounded-full">
                                    <i class="fas fa-crown text-xs"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="text-white">
                            <h1 class="text-3xl font-bold"><?= $user['first_name'] . " " . $user['last_name'] ?></h1>
                            <p class="text-blue-100 mt-1"><?= $user['email'] ?></p>
                            <div class="flex items-center space-x-4 mt-2">
                                <?php if($admin): ?>
                                    <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                        <i class="fas fa-shield-alt mr-1"></i>Administrator
                                    </span>
                                <?php endif; ?>
                                <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                    <i class="fas fa-user mr-1"></i>Member since <?= date('M Y', strtotime($user['join_date'])) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="/rentals" class="bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center space-x-2">
                            <i class="fas fa-calendar-check"></i>
                            <span>My Rentals</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Stats Section -->
            <div class="lg:col-span-1 space-y-6">
                <!-- User Stats -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                        Rental Stats
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="bg-blue-100 p-2 rounded-lg">
                                    <i class="fas fa-calendar text-blue-600"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Total Rentals</span>
                            </div>
                            <span class="text-lg font-bold text-blue-600"><?= $stats['total_rentals'] ?></span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="bg-green-100 p-2 rounded-lg">
                                    <i class="fas fa-rupee-sign text-green-600"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Total Spent</span>
                            </div>
                            <span class="text-lg font-bold text-green-600">
                                <?php if(isset($stats['total_spent']) && $stats['total_spent'] > 0): ?>
                                    ₹<?= number_format($stats['total_spent']) ?>
                                <?php else: ?>
                                    ₹0
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="bg-purple-100 p-2 rounded-lg">
                                    <i class="fas fa-car text-purple-600"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Favorite Car</span>
                            </div>
                            <span class="text-sm font-bold text-purple-600 text-right">
                                <?= $stats['favorite_car'] ?? 'No rentals yet' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                        Quick Actions
                    </h3>
                    <div class="space-y-3">
                        <a href="/rentals" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition duration-200 group">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-car text-gray-400 group-hover:text-blue-600"></i>
                                <span class="font-medium">My Rentals</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600"></i>
                        </a>
                        
                        <a href="/" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition duration-200 group">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-search text-gray-400 group-hover:text-blue-600"></i>
                                <span class="font-medium">Browse Cars</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600"></i>
                        </a>
                        
                        <?php if($admin): ?>
                        <a href="/admin/dashboard" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-red-50 hover:text-red-600 transition duration-200 group">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-shield-alt text-gray-400 group-hover:text-red-600"></i>
                                <span class="font-medium">Admin Panel</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-red-600"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-8">
                <!-- Profile Information -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-user-circle text-blue-600 mr-3"></i>
                            Profile Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Personal Info -->
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                        <i class="fas fa-id-card text-blue-500 mr-2"></i>
                                        Personal Information
                                    </h3>
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <span class="text-sm font-medium text-gray-600">Full Name</span>
                                            <span class="font-semibold text-gray-900"><?= $user['first_name'] . " " . $user['last_name'] ?></span>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <span class="text-sm font-medium text-gray-600">Username</span>
                                            <span class="font-semibold text-gray-900">@<?= $user['username'] ?></span>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <span class="text-sm font-medium text-gray-600">Email</span>
                                            <span class="font-semibold text-gray-900"><?= $user['email'] ?></span>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <span class="text-sm font-medium text-gray-600">Phone</span>
                                            <span class="font-semibold text-gray-900"><?= $user['ph_no'] ?: 'Not provided' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Address & Membership -->
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                        <i class="fas fa-map-marker-alt text-green-500 mr-2"></i>
                                        Address
                                    </h3>
                                    <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                        <div class="flex items-start space-x-3">
                                            <i class="fas fa-road text-gray-400 mt-1"></i>
                                            <span class="text-gray-700"><?= $user['street'] ?></span>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <i class="fas fa-city text-gray-400 mt-1"></i>
                                            <span class="text-gray-700"><?= $user['city'] . ", " . $user['state'] ?></span>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <i class="fas fa-flag text-gray-400 mt-1"></i>
                                            <span class="text-gray-700"><?= $user['country'] . " - " . $user['zip'] ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                        <i class="fas fa-award text-purple-500 mr-2"></i>
                                        Membership
                                    </h3>
                                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-semibold">RentDream Member</p>
                                                <p class="text-purple-100 text-sm">Since <?= date('F j, Y', strtotime($user['join_date'])) ?></p>
                                            </div>
                                            <i class="fas fa-gem text-2xl text-yellow-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Rentals -->
                <?php if(!empty($userRentals)): ?>
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-history text-orange-500 mr-3"></i>
                            Recent Rentals
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php 
                            $recentRentals = array_slice($userRentals, 0, 3);
                            $calculatedTotal = 0;
                            foreach($recentRentals as $rental): 
                                $calculatedTotal += $rental['amount'] ?? 0;
                            ?>
                            <div class="bg-gray-50 rounded-lg p-4 hover:bg-blue-50 transition duration-200 border border-gray-200">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg overflow-hidden flex items-center justify-center">
                                        <?php if(isset($rental['pic']) && $rental['pic']): ?>
                                            <img src="/<?= $rental['pic'] ?>" alt="<?= $rental['name'] ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <i class="fas fa-car text-blue-600"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm"><?= $rental['name'] ?></p>
                                        <p class="text-xs text-gray-500"><?= date('M j, Y', strtotime($rental['time'])) ?></p>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-600">₹<?= $rental['amount'] ?? '0' ?></span>
                                    <span class="text-xs px-2 py-1 rounded-full <?= 
                                        ($rental['status'] == 'active') ? 'bg-green-100 text-green-800' : 
                                        (($rental['status'] == 'completed') ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')
                                    ?>">
                                        <?= ucfirst($rental['status'] ?? 'active') ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Total Spent Display -->
                        <div class="mt-6 p-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-semibold">Total Amount Spent</p>
                                    <p class="text-green-100 text-sm">Across all your rentals</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold">₹<?= number_format($calculatedTotal) ?></p>
                                    <p class="text-green-100 text-sm"><?= count($userRentals) ?> rentals</p>
                                </div>
                            </div>
                        </div>
                        
                        <?php if(count($userRentals) > 3): ?>
                        <div class="mt-4 text-center">
                            <a href="/rentals" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                View All Rentals (<?= count($userRentals) ?>)
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Admin Section -->
                <?php if($admin): ?>
                <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl shadow-lg">
                    <div class="px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-shield-alt mr-3"></i>
                            Administrator Panel
                        </h2>
                    </div>
                    <div class="bg-white rounded-b-2xl p-6">
                        <p class="text-gray-600 mb-4">You have administrative privileges. Manage the rental system from the admin dashboard.</p>
                        <div class="flex space-x-4">
                            <a href="/admin/dashboard" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition duration-200 flex items-center space-x-2">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Admin Dashboard</span>
                            </a>
                            <a href="/admin/users" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-semibold transition duration-200 flex items-center space-x-2">
                                <i class="fas fa-users"></i>
                                <span>Manage Users</span>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Admin Rental Management -->
        <?php if($admin && !empty($rentals)): ?>
        <div class="mt-8">
            <?php include_once('../templates/rental_item.php') ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php } ?>