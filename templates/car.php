<?php 
function getStockColor($stock) {
    if($stock > 50) return 'bg-green-100 text-green-800 border-green-200';
    if($stock > 20) return 'bg-yellow-100 text-yellow-800 border-yellow-200';
    return 'bg-red-100 text-red-800 border-red-200';
}

function getStockIcon($stock) {
    if($stock > 50) return 'fas fa-check-circle text-green-500';
    if($stock > 20) return 'fas fa-exclamation-circle text-yellow-500';
    return 'fas fa-times-circle text-red-500';
}

function getStockText($stock) {
    if($stock > 50) return 'Available';
    if($stock > 20) return 'Limited Stock';
    return 'Out of Stock';
}


// Fix image path function
function getCarImage($imagePath) {
    // If image path is empty, return placeholder
    if (empty($imagePath)) {
        return '/images/car-placeholder.jpg';
    }
    
    // Remove any leading slashes or dots
    $imagePath = ltrim($imagePath, './');
    
    // Ensure the path starts with images/cars/
    if (strpos($imagePath, 'images/cars/') !== 0) {
        $imagePath = 'images/cars/' . basename($imagePath);
    }
    
    // Add leading slash for web access
    return '/' . $imagePath;
}
?>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if(!isset($car) || $car == null): ?>
            <div class="bg-white rounded-2xl shadow-lg border border-red-200 p-8 text-center">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Car Not Found</h2>
                <p class="text-gray-600 mb-6">The car you're looking for doesn't exist or has been removed.</p>
                <a href="/" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Car Listings
                </a>
            </div>
        <?php else: ?>
            

            <!-- Breadcrumb -->
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm text-gray-600">
                    <li><a href="/" class="hover:text-blue-600 transition duration-200">Home</a></li>
                    <li><i class="fas fa-chevron-right text-gray-400 text-xs"></i></li>
                    <li><a href="/" class="hover:text-blue-600 transition duration-200">Cars</a></li>
                    <li><i class="fas fa-chevron-right text-gray-400 text-xs"></i></li>
                    <li class="text-gray-900 font-medium"><?= $car['name'] ?></li>
                </ol>
            </nav>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <!-- Header Section -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h1 class="text-3xl lg:text-4xl font-bold text-white mb-2"><?= $car['name'] ?></h1>
                            <div class="flex items-center space-x-4 text-blue-100">
                                <span class="flex items-center">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <?= $car['year'] ?> Year
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-gas-pump mr-2"></i>
                                    <?= $car['fuel_type'] ?? 'Petrol' ?>
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-car mr-2"></i>
                                    <?= $car['type'] ?? 'Standard' ?>
                                </span>
                            </div>
                        </div>
                        <div class="mt-4 lg:mt-0">
                            <div class="<?= getStockColor($car['stock']) ?> px-4 py-2 rounded-full border inline-flex items-center space-x-2">
                                <i class="<?= getStockIcon($car['stock']) ?>"></i>
                                <span class="font-semibold"><?= getStockText($car['stock']) ?> (<?= $car['stock'] ?> left)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <!-- Main Content Grid -->
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                        <!-- Car Image & Gallery -->
                        <div class="xl:col-span-2">
                            <div class="bg-gray-100 rounded-2xl overflow-hidden mb-6">
                                <!-- Updated image src with path fix -->
                                <img src="<?= getCarImage($car['pic']) ?>" alt="<?= $car['name'] ?>" 
     onerror="this.src='/images/car-placeholder.jpg'; console.log('Image failed to load: <?= $car['pic'] ?>')"
     class="w-full h-80 lg:h-96 object-cover hover:scale-105 transition duration-500 cursor-zoom-in">
                            </div>
                            
                            <!-- Car Specifications -->
                            <div class="bg-gray-50 rounded-2xl p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-list-alt text-blue-600 mr-3"></i>
                                    Car Specifications
                                </h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="text-center p-4 bg-white rounded-lg border border-gray-200">
                                        <i class="fas fa-car text-blue-500 text-xl mb-2"></i>
                                        <p class="text-sm text-gray-600">Type</p>
                                        <p class="font-semibold text-gray-900"><?= $car['type'] ?? 'Standard' ?></p>
                                    </div>
                                    <div class="text-center p-4 bg-white rounded-lg border border-gray-200">
                                        <i class="fas fa-gas-pump text-green-500 text-xl mb-2"></i>
                                        <p class="text-sm text-gray-600">Fuel</p>
                                        <p class="font-semibold text-gray-900"><?= $car['fuel_type'] ?? 'Petrol' ?></p>
                                    </div>
                                    <div class="text-center p-4 bg-white rounded-lg border border-gray-200">
                                        <i class="fas fa-calendar text-purple-500 text-xl mb-2"></i>
                                        <p class="text-sm text-gray-600">Year</p>
                                        <p class="font-semibold text-gray-900"><?= $car['year'] ?? '2023' ?></p>
                                    </div>
                                    <div class="text-center p-4 bg-white rounded-lg border border-gray-200">
                                        <i class="fas fa-boxes text-orange-500 text-xl mb-2"></i>
                                        <p class="text-sm text-gray-600">Stock</p>
                                        <p class="font-semibold text-gray-900"><?= $car['stock'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Booking Section -->
                        <div class="space-y-6">
                            <!-- Pricing Card -->
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white">
                                <h3 class="text-xl font-bold mb-4 flex items-center">
                                    <i class="fas fa-tag mr-3"></i>
                                    Rental Rates
                                </h3>
                                
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center p-3 bg-blue-400 bg-opacity-30 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-clock text-blue-200"></i>
                                            <span>Per Hour</span>
                                        </div>
                                        <span class="text-xl font-bold">₹<?= $car['rate_by_hour'] ?></span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center p-3 bg-blue-400 bg-opacity-30 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-sun text-blue-200"></i>
                                            <span>Per Day</span>
                                        </div>
                                        <span class="text-xl font-bold">₹<?= $car['rate_by_day'] ?></span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center p-3 bg-blue-400 bg-opacity-30 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-road text-blue-200"></i>
                                            <span>Per KM</span>
                                        </div>
                                        <span class="text-xl font-bold">₹<?= $car['rate_by_km'] ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-4">
                                <?php if ($loginInfo != 0): ?>
                                    <?php if ($car['stock'] > 0): ?>
                                        <a href="/rent/<?= $car['_id'] ?>" 
                                           class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-4 px-6 rounded-xl font-bold text-lg transition duration-200 flex items-center justify-center space-x-3 shadow-lg">
                                            <i class="fas fa-calendar-check text-xl"></i>
                                            <span>Rent This Car</span>
                                        </a>
                                    <?php else: ?>
                                        <button disabled class="w-full bg-gray-400 text-white py-4 px-6 rounded-xl font-bold text-lg cursor-not-allowed flex items-center justify-center space-x-3">
                                            <i class="fas fa-times-circle text-xl"></i>
                                            <span>Out of Stock</span>
                                        </button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-center">
                                        <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mb-2"></i>
                                        <p class="text-yellow-800 font-medium mb-3">Sign In to Rent This Car</p>
                                        <div class="flex space-x-3">
                                            <a href="/signin" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-lg font-semibold transition duration-200 text-center">
                                                Sign In
                                            </a>
                                            <a href="/register" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold transition duration-200 text-center">
                                                Register
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <a href="/" class="w-full bg-gray-600 hover:bg-gray-700 text-white py-3 px-6 rounded-xl font-semibold transition duration-200 flex items-center justify-center space-x-2">
                                    <i class="fas fa-arrow-left"></i>
                                    <span>Back to Cars</span>
                                </a>
                            </div>

                            <!-- Quick Info -->
                            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200">
                                <h4 class="font-bold text-gray-900 mb-3 flex items-center">
                                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                    Quick Info
                                </h4>
                                <ul class="space-y-2 text-sm text-gray-600">
                                    <li class="flex items-center space-x-2">
                                        <i class="fas fa-check text-green-500"></i>
                                        <span>Free cancellation up to 24 hours</span>
                                    </li>
                                    <li class="flex items-center space-x-2">
                                        <i class="fas fa-check text-green-500"></i>
                                        <span>Comprehensive insurance included</span>
                                    </li>
                                    <li class="flex items-center space-x-2">
                                        <i class="fas fa-check text-green-500"></i>
                                        <span>24/7 roadside assistance</span>
                                    </li>
                                    <li class="flex items-center space-x-2">
                                        <i class="fas fa-check text-green-500"></i>
                                        <span>Unlimited mileage</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Car Description -->
                    <div class="mt-8 bg-white rounded-2xl p-6 border border-gray-200">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-file-alt text-blue-600 mr-3"></i>
                            About This Car
                        </h3>
                        <div class="prose max-w-none text-gray-700 leading-relaxed">
                            <?= str_replace('\n', '<br>', $car['info']) ?>
                        </div>
                    </div>

                    <!-- Features & Amenities -->
                    <div class="mt-8 bg-gray-50 rounded-2xl p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-star text-yellow-500 mr-3"></i>
                            Car Features & Amenities
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <?php 
                            $features = [
                                ['icon' => 'fas fa-snowflake', 'name' => 'Air Conditioning', 'color' => 'text-blue-500'],
                                ['icon' => 'fas fa-music', 'name' => 'Premium Audio', 'color' => 'text-purple-500'],
                                ['icon' => 'fas fa-blender-phone', 'name' => 'Bluetooth', 'color' => 'text-green-500'],
                                ['icon' => 'fas fa-map-marker-alt', 'name' => 'GPS Navigation', 'color' => 'text-red-500'],
                                ['icon' => 'fas fa-camera', 'name' => 'Backup Camera', 'color' => 'text-indigo-500'],
                                ['icon' => 'fas fa-shield-alt', 'name' => 'Safety Features', 'color' => 'text-orange-500'],
                                ['icon' => 'fas fa-suitcase', 'name' => 'Spacious Trunk', 'color' => 'text-teal-500'],
                                ['icon' => 'fas fa-gas-pump', 'name' => 'Fuel Efficient', 'color' => 'text-lime-500'],
                            ];
                            
                            foreach($features as $feature): 
                            ?>
                            <div class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200 hover:shadow-md transition duration-200">
                                <i class="<?= $feature['icon'] ?> <?= $feature['color'] ?> text-lg"></i>
                                <span class="text-sm font-medium text-gray-700"><?= $feature['name'] ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Similar Cars Section -->
            <div class="mt-12">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-car-side text-blue-600 mr-3"></i>
                        Similar Cars You Might Like
                    </h2>
                    <a href="/" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center">
                        View All
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- This would be populated with similar cars from your database -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-200 text-center">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-car text-4xl"></i>
                        </div>
                        <p class="text-gray-600">More cars coming soon</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Image zoom functionality
document.addEventListener('DOMContentLoaded', function() {
    const carImage = document.querySelector('img[alt="<?= $car['name'] ?? '' ?>"]');
    if (carImage) {
        carImage.addEventListener('click', function() {
            this.classList.toggle('scale-150');
            this.classList.toggle('cursor-zoom-out');
            this.classList.toggle('cursor-zoom-in');
        });
    }
});

// Smooth scroll to sections
function scrollToSection(sectionId) {
    document.getElementById(sectionId).scrollIntoView({
        behavior: 'smooth'
    });
}
</script>