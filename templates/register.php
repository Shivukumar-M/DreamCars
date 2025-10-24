<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="card">
        <div class="p-6">
            <?php if($loginInfo == 0): ?>

            <form class="space-y-6" method="post" action="">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-800">Join RentDream</h2>
                    <p class="text-gray-600 mt-2">Create your account and start driving your dreams</p>
                </div>

                <!-- Name Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type='text' class='w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200' 
                               name='first_name' value='<?=@$values["first_name"]?>' placeholder='First Name' required>
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type='text' class='w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200' 
                               name='last_name' value='<?=@$values["last_name"]?>' placeholder='Last Name' required>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type='email' class='w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200' 
                               name='email' value='<?=@$values["email"]?>' placeholder='Email' required>
                    </div>
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type='text' class='w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200' 
                               name='username' value='<?=@$values["username"]?>' placeholder='Username' required>
                    </div>
                </div>

                <!-- Password Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type='password' id='password' class='w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200' 
                               name='password' value='<?=@$values["password"]?>' placeholder='Password' required>
                    </div>
                    <div>
                        <label for="password_two" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type='password' class='w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200' 
                               name='password_two' value='<?=@$values["password_two"]?>' placeholder='Repeat Password' required>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="showPassword" onchange="document.getElementById('password').type = this.checked ? 'text' : 'password'" class="mr-2">
                    <label for="showPassword" class="text-sm text-gray-600">Show Password</label>
                </div>

                <!-- Address Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="street" class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                        <input type='text' class='w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200' 
                               name='street' value='<?=@$values["street"]?>' placeholder='Street Address' required>
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <input type='text' class='w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200' 
                               name='city' value='<?=@$values["city"]?>' placeholder='City' required>
                    </div>
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                        <input type='text' class='w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200' 
                               name='state' value='<?=@$values["state"]?>' placeholder='State' required>
                    </div>
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                        <input type='text' class='w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200' 
                               name='country' value='<?=@$values["country"]?>' placeholder='Country' required>
                    </div>
                    <div>
                        <label for="zip" class="block text-sm font-medium text-gray-700 mb-1">Zip Code</label>
                        <input type='text' class='w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200' 
                               name='zip' value='<?=@$values["zip"]?>' placeholder='Zip Code' required>
                    </div>
                    <div>
                        <label for="ph_no" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type='text' class='w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200' 
                               name='ph_no' value='<?=@$values["ph_no"]?>' placeholder='Phone Number'>
                    </div>
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Gender</label>
                    <div class="flex space-x-6">
                        <label class="flex items-center">
                            <input type="radio" name="gender" value="F" class="mr-2">
                            <span class="text-gray-700">Female</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="gender" value="M" class="mr-2">
                            <span class="text-gray-700">Male</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="gender" value="U" checked class="mr-2">
                            <span class="text-gray-700">Unspecified</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex space-x-4 pt-4">
                    <button type="reset" class="btn-secondary flex-1">Clear Form</button>
                    <button type="submit" class="btn-primary flex-1">Create Account</button>
                </div>

                <div class="text-center">
                    <p class="text-gray-600">
                        Already have an account? 
                        <a href="/signin" class="text-blue-600 hover:text-blue-800 font-medium">Sign In</a>
                    </p>
                </div>
            </form>

            <!-- Messages -->
            <div class="mt-6 space-y-4">
                <?php if(isset($errors)): ?>
                    <?php foreach ($errors as $error): ?>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start">
                            <i class="fas fa-exclamation-triangle text-red-500 mt-1 mr-3"></i>
                            <span class="text-red-700"><?= $error ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if(isset($success) && strlen($success) > 0): ?>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                        <span class="text-green-700"><?= $success ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <?php else: ?>
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-check text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">You're already logged in!</h3>
                <p class="text-gray-600 mb-4">Log out to create a new account.</p>
                <a href="/logout" class="btn-primary">Log Out</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>