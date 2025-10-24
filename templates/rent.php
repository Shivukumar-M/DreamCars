<?php if($loginInfo == 0) {
    include_once('../templates/logout.php');
} else if(!isset($car) || $car == null) { ?>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 flex items-center justify-center px-4 py-16">
        <div class="bg-white rounded-2xl shadow-lg border border-red-200 p-8 text-center max-w-md w-full">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Car Not Found</h2>
            <p class="text-gray-600 mb-6">The car you're trying to rent doesn't exist or has been removed.</p>
            <a href="/" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 inline-flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Car Listings
            </a>
        </div>
    </div>
<?php } else { ?>
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

// Calculate estimated amounts
$estimatedAmounts = [
    'hour' => $car['rate_by_hour'] * (isset($values['value']) ? $values['value'] : 1),
    'day' => $car['rate_by_day'] * (isset($values['value']) ? $values['value'] : 1),
    'km' => $car['rate_by_km'] * (isset($values['value']) ? $values['value'] : 1)
];

// Fix image path
$imagePath = $car['pic'];
if (!preg_match('/^(http|\/)/', $imagePath)) {
    $imagePath = '/' . ltrim($imagePath, '/');
}
$imagePath = !empty($car['pic']) ? $imagePath : '/images/car-placeholder.jpg';
?>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Complete Your Rental</h1>
            <p class="text-lg text-gray-600">You're just one step away from driving your dream car</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Car Summary Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-car mr-3"></i>
                        Car Summary
                    </h2>
                </div>
                
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
                        <div class="flex-shrink-0">
                            <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($car['name']) ?>" 
                                 class="w-32 h-32 rounded-xl object-cover shadow-md"
                                 onerror="this.src='/images/car-placeholder.jpg'; this.alt='Image not available'">
                        </div>
                        <div class="flex-1 text-center sm:text-left">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($car['name']) ?></h3>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                    <i class="fas fa-calendar mr-1"></i><?= htmlspecialchars($car['year']) ?>
                                </span>
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                    <i class="fas fa-gas-pump mr-1"></i><?= htmlspecialchars($car['fuel_type'] ?? 'Petrol') ?>
                                </span>
                                <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                                    <i class="fas fa-car mr-1"></i><?= htmlspecialchars($car['type'] ?? 'Standard') ?>
                                </span>
                            </div>
                            <div class="<?= getStockColor($car['stock']) ?> px-4 py-2 rounded-full border inline-flex items-center space-x-2">
                                <i class="<?= getStockIcon($car['stock']) ?>"></i>
                                <span class="font-semibold"><?= getStockText($car['stock']) ?></span>
                                <span class="text-sm">(<?= $car['stock'] ?> available)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Car Features -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-star text-yellow-500 mr-2"></i>
                            Key Features
                        </h4>
                        <div class="grid grid-cols-2 gap-3 text-sm text-gray-600">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-snowflake text-blue-500"></i>
                                <span>Air Conditioning</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-music text-purple-500"></i>
                                <span>Premium Audio</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-blender-phone text-green-500"></i>
                                <span>Bluetooth</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-shield-alt text-red-500"></i>
                                <span>Safety Features</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rental Form Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-calendar-check mr-3"></i>
                        Rental Details
                    </h2>
                </div>
                
                <div class="p-6">
                    <?php if ($car['stock'] > 0): ?>
                    <form class="space-y-6" method="post" action="">
                        <!-- CRITICAL: Add these hidden inputs for booking processing -->
                        <input type="hidden" name="car_id" value="<?= $car['_id'] ?>">
                        <input type="hidden" id="calculated_amount" name="amount" value="<?= $estimatedAmounts['hour'] ?>">
                        
                        <!-- Hidden rate inputs -->
                        <input type="hidden" id="hour_rate" value="<?= $car['rate_by_hour'] ?>">
                        <input type="hidden" id="day_rate" value="<?= $car['rate_by_day'] ?>">
                        <input type="hidden" id="km_rate" value="<?= $car['rate_by_km'] ?>">
                        <input type="hidden" id="current_rate" name="rate" value="<?= $car['rate_by_hour'] ?>">

                        <!-- Rental Mode Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-clock text-blue-500 mr-2"></i>
                                Select Rental Mode
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <?php 
                                $modes = [
                                    'hour' => ['icon' => 'fas fa-clock', 'label' => 'Per Hour', 'rate' => $car['rate_by_hour'], 'desc' => 'Short trips'],
                                    'day' => ['icon' => 'fas fa-sun', 'label' => 'Per Day', 'rate' => $car['rate_by_day'], 'desc' => 'Daily rental'],
                                    'km' => ['icon' => 'fas fa-road', 'label' => 'Per KM', 'rate' => $car['rate_by_km'], 'desc' => 'Distance based']
                                ];
                                
                                foreach($modes as $modeKey => $mode): 
                                    $isChecked = isset($values['mode']) ? $values['mode'] == $modeKey : $modeKey == 'hour';
                                ?>
                                <label class="relative">
                                    <input type="radio" name="mode" value="<?= $modeKey ?>" 
                                           <?= $isChecked ? 'checked' : '' ?> class="peer hidden rental-mode">
                                    <div class="border-2 border-gray-300 rounded-xl p-4 text-center cursor-pointer transition-all duration-200 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:shadow-md hover:border-gray-400">
                                        <i class="<?= $mode['icon'] ?> text-gray-600 peer-checked:text-green-600 text-xl mb-2"></i>
                                        <div class="font-semibold text-gray-800 peer-checked:text-green-800"><?= $mode['label'] ?></div>
                                        <div class="text-lg font-bold text-gray-900 mt-1">₹<?= $mode['rate'] ?></div>
                                        <div class="text-xs text-gray-500 mt-1"><?= $mode['desc'] ?></div>
                                    </div>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Quantity and Pricing -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="val" class="block text-sm font-semibold text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-hashtag text-purple-500 mr-2"></i>
                                        <span id="quantity-label">Duration (hours)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" min="1" max="1000" id="val" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 font-semibold text-center" 
                                               name='value' value='<?= isset($values['value']) ? $values['value'] : 1 ?>' required>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <span id="unit-display" class="text-gray-700 font-medium">hours</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-calculator text-orange-500 mr-2"></i>
                                        Total Amount
                                    </label>
                                    <div class="bg-white border border-gray-300 rounded-lg p-3 text-center">
                                        <div class="text-2xl font-bold text-green-600">₹<span id="rent"><?= $estimatedAmounts['hour'] ?></span></div>
                                        <div class="text-xs text-gray-500 mt-1" id="rate-display">₹<?= $car['rate_by_hour'] ?> per hour</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rental Summary -->
                        <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                            <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-receipt text-blue-500 mr-2"></i>
                                Rental Summary
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Base rate:</span>
                                    <span class="font-semibold" id="base-rate">₹<?= $car['rate_by_hour'] ?>/hour</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600" id="quantity-summary-label">Duration:</span>
                                    <span class="font-semibold"><span id="duration-display"><?= isset($values['value']) ? $values['value'] : 1 ?></span> <span id="duration-unit">hours</span></span>
                                </div>
                                <div class="flex justify-between border-t border-blue-200 pt-2">
                                    <span class="text-gray-800 font-semibold">Total Amount:</span>
                                    <span class="text-green-600 font-bold text-lg">₹<span id="total-display"><?= $estimatedAmounts['hour'] ?></span></span>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-info-circle text-yellow-500 mt-1"></i>
                                <div class="text-sm text-yellow-800">
                                    <p class="font-semibold">Important Information</p>
                                    <ul class="mt-1 space-y-1">
                                        <li>• Free cancellation up to 24 hours before rental</li>
                                        <li>• Comprehensive insurance included</li>
                                        <li>• 24/7 roadside assistance available</li>
                                        <li>• Car stock will decrease after booking and increase upon cancellation</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-4 px-6 rounded-xl font-bold text-lg transition duration-200 flex items-center justify-center space-x-3 shadow-lg transform hover:scale-105">
                                <i class="fas fa-lock text-xl"></i>
                                <span>Confirm & Book Now</span>
                            </button>
                            <p class="text-center text-xs text-gray-500 mt-3">
                                By confirming, you agree to our <a href="#" class="text-blue-600 hover:underline">Terms of Service</a>
                            </p>
                        </div>
                    </form>
                    <?php else: ?>
                        <!-- Out of Stock State -->
                        <div class="text-center py-8">
                            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-times-circle text-red-500 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Currently Unavailable</h3>
                            <p class="text-gray-600 mb-6">This car is out of stock. Please check back later or browse other available cars.</p>
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <a href="/" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center justify-center space-x-2">
                                    <i class="fas fa-search"></i>
                                    <span>Browse Other Cars</span>
                                </a>
                                <a href="/car/<?= $car['_id'] ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center justify-center space-x-2">
                                    <i class="fas fa-info-circle"></i>
                                    <span>View Details</span>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <?php if(isset($errors) && !empty($errors)): ?>
        <div class="mt-8 space-y-4">
            <?php foreach ($errors as $error): ?>
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start">
                    <i class="fas fa-exclamation-triangle text-red-500 mt-1 mr-3 text-lg"></i>
                    <div>
                        <p class="font-semibold text-red-800">Booking Error</p>
                        <p class="text-red-700"><?= $error ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if(isset($success) && strlen($success) > 0): ?>
        <div class="mt-8 bg-green-50 border border-green-200 rounded-xl p-4 flex items-start">
            <i class="fas fa-check-circle text-green-500 mt-1 mr-3 text-lg"></i>
            <div>
                <p class="font-semibold text-green-800">Booking Successful!</p>
                <p class="text-green-700"><?= $success ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const valInput = document.getElementById('val');
    const rentDisplay = document.getElementById('rent');
    const totalDisplay = document.getElementById('total-display');
    const durationDisplay = document.getElementById('duration-display');
    const unitDisplay = document.getElementById('unit-display');
    const rateDisplay = document.getElementById('rate-display');
    const baseRate = document.getElementById('base-rate');
    const durationUnit = document.getElementById('duration-unit');
    const quantityLabel = document.getElementById('quantity-label');
    const quantitySummaryLabel = document.getElementById('quantity-summary-label');
    const currentRateInput = document.getElementById('current_rate');
    const calculatedAmountInput = document.getElementById('calculated_amount'); // ADDED

    // Get rate values
    const hourRate = parseFloat(document.getElementById('hour_rate').value) || 0;
    const dayRate = parseFloat(document.getElementById('day_rate').value) || 0;
    const kmRate = parseFloat(document.getElementById('km_rate').value) || 0;

    function calculateTotal() {
        const quantity = parseInt(valInput.value) || 1;
        const selectedMode = document.querySelector('input[name="mode"]:checked').value;
        
        let rate = 0;
        switch(selectedMode) {
            case 'hour':
                rate = hourRate;
                break;
            case 'day':
                rate = dayRate;
                break;
            case 'km':
                rate = kmRate;
                break;
        }
        
        const total = rate * quantity;
        
        // Update displays
        rentDisplay.textContent = total.toFixed(2);
        totalDisplay.textContent = total.toFixed(2);
        durationDisplay.textContent = quantity;
        
        // Update hidden inputs for form submission
        currentRateInput.value = rate;
        calculatedAmountInput.value = total; // ADDED - Critical for rentals table
    }

    function updateUnitDisplay(mode) {
        const units = {
            'hour': {unit: 'hours', label: 'Duration (hours)', summary: 'Duration:', rateText: `₹${hourRate} per hour`, baseRate: `₹${hourRate}/hour`},
            'day': {unit: 'days', label: 'Duration (days)', summary: 'Duration:', rateText: `₹${dayRate} per day`, baseRate: `₹${dayRate}/day`},
            'km': {unit: 'kilometers', label: 'Distance (km)', summary: 'Distance:', rateText: `₹${kmRate} per km`, baseRate: `₹${kmRate}/km`}
        };

        const unitInfo = units[mode];
        unitDisplay.textContent = unitInfo.unit;
        quantityLabel.textContent = unitInfo.label;
        quantitySummaryLabel.textContent = unitInfo.summary;
        rateDisplay.textContent = unitInfo.rateText;
        baseRate.textContent = unitInfo.baseRate;
        durationUnit.textContent = unitInfo.unit;
    }

    // Event listeners
    valInput.addEventListener('input', calculateTotal);
    valInput.addEventListener('change', calculateTotal);

    document.querySelectorAll('input[name="mode"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const mode = this.value;
            updateUnitDisplay(mode);
            calculateTotal();
        });
    });

    // Initialize
    const initialMode = document.querySelector('input[name="mode"]:checked').value;
    updateUnitDisplay(initialMode);
    calculateTotal();
});
</script>
<?php } ?>