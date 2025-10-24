<?php if($loginInfo == 0) {
    include_once('../templates/logout.php');
} else { ?>
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="card mb-8">
        <div class="p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">My Rentals</h1>
            <p class="text-gray-600">Manage your current and past car rentals</p>
        </div>
    </div>

    <?php if(!isset($rentals) || count($rentals) == 0): ?>
        <div class="card text-center py-16">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-car text-blue-600 text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">No Rentals Yet</h2>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">
                You haven't rented any cars yet. Start your journey by exploring our amazing collection of vehicles.
            </p>
            <div class="space-x-4">
                <a href="/" class="btn-primary text-lg px-6 py-3">
                    <i class="fas fa-search mr-2"></i>Browse Cars
                </a>
            </div>
        </div>
    <?php else: ?>
        <?php include_once('../templates/rental_item.php'); ?>
    <?php endif; ?>
</div>
<?php } ?>