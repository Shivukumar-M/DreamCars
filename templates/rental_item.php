<div class="mt-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6"><?= $admin ? 'All Rentals' : 'My Rentals' ?></h2>
    
    <?php if(empty($rentals)): ?>
        <div class="card text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-car text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No Rentals Found</h3>
            <p class="text-gray-500 mb-6"><?= $admin ? 'No rental transactions found.' : 'You haven\'t rented any cars yet.' ?></p>
            <?php if(!$admin): ?>
                <a href="/" class="btn-primary">Browse Cars</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($rentals as $rental): ?>
                <?php $rate = $rental["rate_by_" . $rental["mode"]]; ?>
                <div class="card">
                    <div class="p-6">
                        <!-- Customer Info (Admin only) -->
                        <?php if($admin): ?>
                            <div class="mb-4 pb-4 border-b border-gray-200">
                                <h4 class="font-semibold text-gray-800 text-center">
                                    <?=$rental['first_name'] . " " . $rental['last_name']?>
                                </h4>
                                <p class="text-sm text-gray-600 text-center">Rented this vehicle</p>
                            </div>
                        <?php endif; ?>

                        <!-- Car Info -->
                        <div class="text-center mb-4">
                            <a href="/car/<?=$rental['car_id']?>">
                                <img src="<?=$rental['pic']?>" alt="<?=$rental['name']?>" 
                                     class="w-full h-32 object-cover rounded-lg mb-3">
                            </a>
                            <h5 class="font-semibold text-gray-800"><?=$rental['name']?></h5>
                        </div>

                        <!-- Rental Details -->
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Duration:</span>
                                <span class="font-medium text-gray-800">
                                    <?=$rental['value'] . " " . $rental['mode'] ?>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span>Rental Date:</span>
                                <span class="font-medium text-gray-800"><?=$rental['time']?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Total Amount:</span>
                                <span class="font-bold text-blue-600">₹ <?=$rental['value']*$rate?></span>
                            </div>
                        </div>

                        <!-- Cancel Button -->
                        <form method="post" class="mt-4">
                            <input name="transaction_id" value="<?=$rental["_id"]?>" type="hidden"/>
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded transition duration-200"
                                    onclick="return confirm('Are you sure you want to cancel this rental?')">
                                <i class="fas fa-times mr-2"></i>Cancel Rental
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>