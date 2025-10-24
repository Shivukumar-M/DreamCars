<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="flex justify-center">
                <div class="w-16 h-16 <?= $isAdminLogin ? 'bg-blue-600' : 'bg-green-600' ?> rounded-full flex items-center justify-center">
                    <i class="<?= $isAdminLogin ? 'fas fa-shield-alt' : 'fas fa-user' ?> text-white text-2xl"></i>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                <?= $isAdminLogin ? 'Admin Portal' : 'Welcome Back' ?>
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                <?= $isAdminLogin ? 'RentDream Administration' : 'Sign in to your RentDream account' ?>
            </p>
            
            <!-- Switch between admin and user login -->
            <div class="mt-4 text-center">
                <?php if($isAdminLogin): ?>
                    <a href="/login" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        <i class="fas fa-user mr-1"></i>User Login
                    </a>
                <?php else: ?>
                    <a href="/login?admin=true" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        <i class="fas fa-shield-alt mr-1"></i>Admin Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <form class="mt-8 space-y-6" method="POST">
            <input type="hidden" name="is_admin" value="<?= $isAdminLogin ? 'true' : 'false' ?>">
            
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="username" class="sr-only">Username or Email</label>
                    <input id="username" name="username" type="text" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                           placeholder="Username or Email" value="<?= @$values['username'] ?>">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                           placeholder="Password">
                </div>
            </div>

            <!-- Show Password Toggle -->
            <div class="flex items-center">
                <input type="checkbox" id="showPassword" onchange="document.getElementById('password').type = this.checked ? 'text' : 'password'" class="mr-2">
                <label for="showPassword" class="text-sm text-gray-600">Show Password</label>
            </div>

            <!-- Error Messages -->
            <?php if(isset($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="bg-red-50 border border-red-200 rounded-md p-4 flex items-start">
                        <i class="fas fa-exclamation-triangle text-red-500 mt-0.5 mr-3"></i>
                        <span class="text-red-700 text-sm"><?= $error ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white <?= $isAdminLogin ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700' ?> focus:outline-none focus:ring-2 focus:ring-offset-2 <?= $isAdminLogin ? 'focus:ring-blue-500' : 'focus:ring-green-500' ?> transition duration-200">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="<?= $isAdminLogin ? 'fas fa-shield-alt' : 'fas fa-sign-in-alt' ?> <?= $isAdminLogin ? 'text-blue-500' : 'text-green-500' ?> group-hover:<?= $isAdminLogin ? 'text-blue-400' : 'text-green-400' ?>"></i>
                    </span>
                    <?= $isAdminLogin ? 'Sign in to Admin Panel' : 'Sign In to Your Account' ?>
                </button>
            </div>

            <?php if(!$isAdminLogin): ?>
                <!-- Register Link for regular users -->
                <div class="text-center">
                    <p class="text-gray-600 text-sm">
                        Don't have an account? 
                        <a href="/register" class="text-blue-600 hover:text-blue-500 font-medium">Create one here</a>
                    </p>
                </div>
            <?php endif; ?>

            <div class="text-center">
                <a href="/" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Main Site
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-focus on username field
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('username').focus();
});
</script>