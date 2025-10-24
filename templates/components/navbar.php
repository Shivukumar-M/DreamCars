<nav class="bg-white shadow-lg fixed top-0 w-full z-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-2">
                    <i class="fas fa-car text-blue-600 text-2xl"></i>
                    <span class="text-xl font-bold text-gray-800">RentDream</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="/" class="text-gray-600 hover:text-blue-600 font-medium transition duration-200">Home</a>
                <a href="/rentals" class="text-gray-600 hover:text-blue-600 font-medium transition duration-200">My Rentals</a>
            </div>

            <!-- Auth Buttons -->
            <div class="flex items-center space-x-4">
                <?php if($loginInfo == 0) { ?>
                    <a href="/register" class="btn-primary">Register</a>
                    <a href="/signin" class="btn-secondary">Sign In</a>
                <?php } else { ?>
                    <a href="/profile" class="text-gray-600 hover:text-blue-600 font-medium transition duration-200">
                        <i class="fas fa-user-circle mr-1"></i>Profile
                    </a>
                    <a href="/logout" class="btn-danger">Logout</a>
                <?php } ?>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button type="button" class="text-gray-600 hover:text-blue-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>
</nav>

<div class="h-16"></div> <!-- Spacer for fixed navbar -->