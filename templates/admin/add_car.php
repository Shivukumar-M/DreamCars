<?php
// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: /admin");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $info = $_POST['info'];
    $stock = $_POST['stock'];
    $rate_by_hour = $_POST['rate_by_hour'];
    $rate_by_day = $_POST['rate_by_day'];
    $rate_by_km = $_POST['rate_by_km'];

    // Handle image upload
    $pic = '';
    $upload_success = false;
    
    if(isset($_FILES['pic']) && $_FILES['pic']['error'] === UPLOAD_ERR_OK) {
        // Use relative path for better compatibility
        $upload_dir = __DIR__ . '/../../public/images/cars/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Validate file type
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['pic']['type'];
        $file_extension = strtolower(pathinfo($_FILES['pic']['name'], PATHINFO_EXTENSION));
        
        // Also check file extension as backup
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_type, $allowed_types) && in_array($file_extension, $allowed_extensions)) {
            // Generate clean filename from car name
            $clean_name = preg_replace('/[^a-zA-Z0-9]/', '_', $name);
            $filename = strtolower($clean_name) . '.' . $file_extension;

            // Check if file already exists, if yes, add a number
            $counter = 1;
            $original_filename = $filename;
            while (file_exists($upload_dir . $filename)) {
                $filename = pathinfo($original_filename, PATHINFO_FILENAME) . '_' . $counter . '.' . $file_extension;
                $counter++;
            }
            
            $target_path = $upload_dir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['pic']['tmp_name'], $target_path)) {
                // Store relative path for database - NO leading slash
                $pic = 'images/cars/' . $filename;
                $upload_success = true;
            } else {
                $message = "Error: Failed to upload image. Please check directory permissions.";
                error_log("Upload failed. Target path: " . $target_path);
                error_log("Temp file: " . $_FILES['pic']['tmp_name']);
            }
        } else {
            $message = "Error: Only JPG, JPEG, PNG & GIF files are allowed. Your file type: " . $file_type;
        }
    } else {
        $upload_error = $_FILES['pic']['error'] ?? UPLOAD_ERR_NO_FILE;
        switch ($upload_error) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $message = "Error: File size too large. Maximum size is " . ini_get('upload_max_filesize');
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "Error: File upload was incomplete.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "Error: Please select an image file.";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Error: Missing temporary folder.";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Error: Failed to write file to disk.";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "Error: A PHP extension stopped the file upload.";
                break;
            default:
                $message = "Error: Upload failed with error code " . $upload_error;
        }
    }

    // Only proceed if upload was successful
    if ($upload_success) {
        try {
            $db = Database::getInstance()->getDb();
            
            // Start transaction to ensure both inserts succeed
            $db->beginTransaction();
            
            // First, insert into cars table
            $stmt = $db->prepare("INSERT INTO cars (name, pic, info, stock) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $pic, $info, $stock]);
            $car_id = $db->lastInsertId();
            
            // Then, insert into car_rates table
            $stmt = $db->prepare("INSERT INTO car_rates (car_id, rate_by_hour, rate_by_day, rate_by_km) VALUES (?, ?, ?, ?)");
            $stmt->execute([$car_id, $rate_by_hour, $rate_by_day, $rate_by_km]);
            
            // Commit the transaction
            $db->commit();
            $message = "Car added successfully! Image saved as: " . $filename;
            
            // Clear form fields after successful submission
            $_POST = array();
            
        } catch (PDOException $e) {
            // Rollback transaction on error
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $message = "Error: " . $e->getMessage();
        }
    }
}
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
                        <h1 class="text-2xl font-bold text-gray-900">Add New Car</h1>
                        <p class="text-sm text-gray-600">Add a new vehicle to the rental fleet</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/admin/dashboard" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                        <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                    </a>
                    
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <?php if($message): ?>
                    <div class="mb-6 <?= strpos($message, 'Error') === false ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' ?> rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="<?= strpos($message, 'Error') === false ? 'fas fa-check-circle text-green-400' : 'fas fa-exclamation-triangle text-red-400' ?>"></i>
                            </div>
                            <div class="ml-3">
                                <p class="<?= strpos($message, 'Error') === false ? 'text-green-800' : 'text-red-800' ?> text-sm"><?= $message ?></p>
                                <?php if(strpos($message, 'Error') === false): ?>
                                    <p class="text-green-600 text-xs mt-1">The image has been saved with a clean filename based on the car name.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="space-y-6" id="carForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Car Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Car Name *</label>
                            <input type="text" name="name" id="name" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="e.g., Toyota Camry 2023"
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                            <p class="text-xs text-gray-500 mt-1">This will be used for the image filename</p>
                        </div>

                        <!-- Stock -->
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700">Stock Quantity *</label>
                            <input type="number" name="stock" id="stock" min="0" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="10"
                                   value="<?= htmlspecialchars($_POST['stock'] ?? '') ?>">
                        </div>

                        <!-- Image Upload -->
                        <div class="md:col-span-2">
                            <label for="pic" class="block text-sm font-medium text-gray-700">Car Image *</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md" id="upload-area">
                                <div class="space-y-1 text-center">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mx-auto"></i>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="pic" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                            <span>Upload a file</span>
                                            <input id="pic" name="pic" type="file" class="sr-only" accept="image/jpeg,image/jpg,image/png,image/gif" required>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                    <p class="text-xs text-blue-500" id="filename-preview"></p>
                                    <p class="text-xs text-red-500" id="file-error"></p>
                                    <div id="image-preview" class="hidden mt-2">
                                        <p class="text-xs text-green-600 mb-1">Image Preview:</p>
                                        <img id="preview-image" src="" alt="Preview" class="max-w-xs max-h-32 rounded border mx-auto">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Section -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Pricing Information</h3>
                        </div>

                        <div>
                            <label for="rate_by_hour" class="block text-sm font-medium text-gray-700">Rate per Hour (₹) *</label>
                            <input type="number" name="rate_by_hour" id="rate_by_hour" min="0" step="0.01" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="500"
                                   value="<?= htmlspecialchars($_POST['rate_by_hour'] ?? '') ?>">
                        </div>

                        <div>
                            <label for="rate_by_day" class="block text-sm font-medium text-gray-700">Rate per Day (₹) *</label>
                            <input type="number" name="rate_by_day" id="rate_by_day" min="0" step="0.01" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="3000"
                                   value="<?= htmlspecialchars($_POST['rate_by_day'] ?? '') ?>">
                        </div>

                        <div>
                            <label for="rate_by_km" class="block text-sm font-medium text-gray-700">Rate per KM (₹) *</label>
                            <input type="number" name="rate_by_km" id="rate_by_km" min="0" step="0.01" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="15"
                                   value="<?= htmlspecialchars($_POST['rate_by_km'] ?? '') ?>">
                        </div>

                        <!-- Car Information -->
                        <div class="md:col-span-2">
                            <label for="info" class="block text-sm font-medium text-gray-700">Car Description *</label>
                            <textarea name="info" id="info" rows="4" required
                                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Describe the car features, specifications, and amenities..."><?= htmlspecialchars($_POST['info'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-6">
                        <a href="/admin/dashboard" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium transition duration-200 flex items-center">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Add Car to Fleet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('pic');
    const fileError = document.getElementById('file-error');
    const filenamePreview = document.getElementById('filename-preview');
    const imagePreview = document.getElementById('image-preview');
    const previewImage = document.getElementById('preview-image');
    const uploadArea = document.getElementById('upload-area');
    const carNameInput = document.getElementById('name');

    // Function to generate clean filename
    function generateCleanFilename(carName, originalFilename) {
        const cleanName = carName.replace(/[^a-zA-Z0-9]/g, '_').toLowerCase();
        const extension = originalFilename.split('.').pop();
        return cleanName + '.' + extension;
    }

    // Update filename preview when car name or file changes
    function updateFilenamePreview() {
        const carName = carNameInput.value.trim();
        const file = fileInput.files[0];
        
        if (carName && file) {
            const cleanFilename = generateCleanFilename(carName, file.name);
            filenamePreview.textContent = 'Will be saved as: ' + cleanFilename;
            filenamePreview.classList.remove('hidden');
        } else {
            filenamePreview.textContent = '';
            filenamePreview.classList.add('hidden');
        }
    }

    // File upload validation and preview
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            const maxSize = 10 * 1024 * 1024; // 10MB
            
            if (!allowedTypes.includes(file.type)) {
                fileError.textContent = 'Please select a valid image file (JPG, PNG, GIF).';
                fileInput.value = '';
                imagePreview.classList.add('hidden');
                filenamePreview.textContent = '';
            } else if (file.size > maxSize) {
                fileError.textContent = 'File size must be less than 10MB.';
                fileInput.value = '';
                imagePreview.classList.add('hidden');
                filenamePreview.textContent = '';
            } else {
                fileError.textContent = '';
                updateFilenamePreview();
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        } else {
            fileError.textContent = '';
            imagePreview.classList.add('hidden');
            filenamePreview.textContent = '';
        }
    });

    // Update filename when car name changes
    carNameInput.addEventListener('input', updateFilenamePreview);

    // Drag and drop functionality
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('border-blue-500', 'bg-blue-50');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-blue-500', 'bg-blue-50');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-blue-500', 'bg-blue-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });

    // Form submission handling
    document.getElementById('carForm').addEventListener('submit', function(e) {
        const file = fileInput.files[0];
        const carName = carNameInput.value.trim();
        
        if (!file) {
            e.preventDefault();
            fileError.textContent = 'Please select an image file.';
            return;
        }
        
        if (!carName) {
            e.preventDefault();
            alert('Please enter a car name.');
            return;
        }
    });
});
</script>