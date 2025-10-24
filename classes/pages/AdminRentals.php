<?php
require_once("../classes/pages/BasicPage.php");

class AdminRentals extends BasicPage {
    
    public function render() {
        // Check if admin is logged in
        if(!isset($_SESSION['admin_logged_in'])) {
            header("Location: /admin");
            exit;
        }

        $this->setTitle('Manage Rentals - Admin - RentDream');

        $rentals = [];
        $errorMessage = '';

        try {
            // Use the new method that queries RENTALS table
            $rentals = RentalService::getAllRentals();
            
        } catch (Exception $e) {
            error_log("Admin rentals fetch error: " . $e->getMessage());
            $errorMessage = "Error loading rentals: " . $e->getMessage();
        }

        // Calculate statistics
        $totalRentals = count($rentals);
        $totalRevenue = array_sum(array_column($rentals, 'amount'));
        $activeCustomers = count(array_unique(array_column($rentals, 'user_id')));
        $activeRentals = count(array_filter($rentals, function($rental) { 
            return ($rental['status'] ?? 'active') === 'active'; 
        }));

        Renderer::render("admin/rentals.php", [
            'rentals' => $rentals,
            'totalRentals' => $totalRentals,
            'totalRevenue' => $totalRevenue,
            'activeCustomers' => $activeCustomers,
            'activeRentals' => $activeRentals,
            'errorMessage' => $errorMessage
        ]);
    }
}
?>