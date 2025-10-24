<!-- Hero Section -->
<section class="gradient-bg text-white py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-5xl font-bold mb-4">Drive Your Dreams</h1>
                <p class="text-xl mb-6 opacity-90">Premium Car Rental Experience</p>
                <p class="text-lg mb-8 opacity-80">
                    Discover the perfect vehicle for every journey. From luxury sedans to family SUVs, 
                    we have the wheels for your adventure.
                </p>
                <div class="flex flex-wrap gap-4">
                    <?php if (!$is_logged_in): ?>
                        <a href="/register" class="btn-primary text-lg px-8 py-3">Get Started</a>
                        <a href="/signin" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-600 font-bold py-3 px-8 rounded transition duration-200">Sign In</a>
                    <?php else: ?>
                        <a href="#featured-cars" class="btn-primary text-lg px-8 py-3">Browse Cars</a>
                        <a href="/rentals" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-600 font-bold py-3 px-8 rounded transition duration-200">My Rentals</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="text-center">
                <i class="fas fa-car text-white text-8xl opacity-50"></i>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="p-6">
                <h3 class="text-4xl font-bold text-blue-600 mb-2"><?= $stats['total_cars'] ?>+</h3>
                <p class="text-gray-600">Vehicles Available</p>
            </div>
            <div class="p-6">
                <h3 class="text-4xl font-bold text-blue-600 mb-2"><?= $stats['happy_customers'] ?>+</h3>
                <p class="text-gray-600">Happy Customers</p>
            </div>
            <div class="p-6">
                <h3 class="text-4xl font-bold text-blue-600 mb-2"><?= $stats['cities_covered'] ?>+</h3>
                <p class="text-gray-600">Cities Covered</p>
            </div>
            <div class="p-6">
                <h3 class="text-4xl font-bold text-blue-600 mb-2"><?= $stats['years_experience'] ?>+</h3>
                <p class="text-gray-600">Years Experience</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Cars Section -->
<section id="featured-cars" class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-4">Featured Vehicles</h2>
        <p class="text-gray-600 text-center mb-12">Our most popular choices for your next adventure</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($featured_cars as $car): ?>
                <div class="card overflow-hidden">
                    <div class="relative">
                        <img src="<?= $car['pic'] ?>" alt="<?= htmlspecialchars($car['name'] ?? '') ?>" 
                             class="w-full h-48 object-cover">
                        <div class="absolute top-4 right-4 bg-blue-600 text-white px-3 py-1 rounded-full text-sm">
                            <?= $car['type'] ?? 'Standard' ?>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">
                            <?= htmlspecialchars($car['name'] ?? '') ?>
                        </h3>
                        <p class="text-gray-600 mb-4">
                            <?= htmlspecialchars($car['year'] ?? '') ?> • 
                            <?= htmlspecialchars($car['fuel_type'] ?? 'N/A') ?>
                        </p>
                        
                        <div class="flex space-x-2">
                            <a href="/car/<?= $car['_id'] ?>" 
                               class="flex-1 text-center border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white font-medium py-2 px-4 rounded transition duration-200">
                                View Details
                            </a>
                            <?php if ($loginInfo != 0 && $stock > 0): ?>
                                <a href="/rent/<?= $car['_id'] ?>" 
                                   class="flex-1 text-center btn-primary">
                                    Rent Now
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-4">How It Works</h2>
        <p class="text-gray-600 text-center mb-12">Rent your dream car in 3 simple steps</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">1</div>
                <h3 class="text-xl font-semibold mb-2">Choose Your Car</h3>
                <p class="text-gray-600">Browse our wide selection of vehicles and pick your favorite</p>
            </div>
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">2</div>
                <h3 class="text-xl font-semibold mb-2">Book & Confirm</h3>
                <p class="text-gray-600">Select your rental period and complete the booking process</p>
            </div>
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">3</div>
                <h3 class="text-xl font-semibold mb-2">Hit The Road</h3>
                <p class="text-gray-600">Pick up your vehicle and start your adventure</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="gradient-bg text-white py-16">
    <div class="max-w-4xl mx-auto text-center px-4">
        <h2 class="text-3xl font-bold mb-4">Ready to Start Your Journey?</h2>
        <p class="text-xl mb-8 opacity-90">Join thousands of satisfied customers who trust us for their transportation needs</p>
        <?php if (!$is_logged_in): ?>
            <a href="/register" class="btn-primary text-lg px-8 py-3 bg-white text-blue-600 hover:bg-gray-100">Create Account</a>
        <?php else: ?>
            <a href="/rentals" class="btn-primary text-lg px-8 py-3 bg-white text-blue-600 hover:bg-gray-100">View My Rentals</a>
        <?php endif; ?>
    </div>
</section>