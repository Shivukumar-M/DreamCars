<?php

class Profile extends BasicPage {

    public function render() {
        $this->setTitle('Profile - RentDream');

        $user = '';
        $rentals = [];
        $admin = false;
        $userRentals = [];
        $stats = [];

        $id = $this->getLoginInfo();
        if($id != 0 ){
            $user = User::getUserDetails($id);
            $admin = User::isUserAdmin($id);

            // Get user's rental history
            $userRentals = RentalService::getRentalsForUser($id);
            
            // Calculate amounts for each rental if not set
            foreach ($userRentals as &$rental) {
                if (!isset($rental['amount'])) {
                    $rate = $rental["rate_by_" . $rental["mode"]] ?? 0;
                    $value = $rental['value'] ?? 0;
                    $rental['amount'] = $rate * $value;
                }
            }
            
            // Get user stats
            $stats = $this->getUserStats($id, $userRentals, $user);

            if($admin) {
                if($_SERVER['REQUEST_METHOD'] == 'POST') {
                    if (isset($_POST['transaction_id']) && strlen($_POST['transaction_id']) != 0) {
                        RentalService::removeRental($_POST['transaction_id']);
                        $_SESSION['flash_message'] = 'Rental cancelled successfully!';
                        $_SESSION['flash_type'] = 'success';
                    }
                }
                $rentals = RentalService::getRentals();
                
                // Calculate amounts for admin rentals too
                foreach ($rentals as &$rental) {
                    if (!isset($rental['amount'])) {
                        $rate = $rental["rate_by_" . $rental["mode"]] ?? 0;
                        $value = $rental['value'] ?? 0;
                        $rental['amount'] = $rate * $value;
                    }
                }
            }
        }

        Renderer::render("profile.php", [
            'user' => $user,
            'admin' => $admin,
            'rentals' => $rentals,
            'userRentals' => $userRentals,
            'stats' => $stats
        ]);
    }

    private function getUserStats($userId, $rentals, $user) {
        $totalRentals = count($rentals);
        $totalSpent = 0;
        $favoriteCar = 'None';
        
        // Calculate total spent
        foreach ($rentals as $rental) {
            $amount = $rental['amount'] ?? 0;
            
            if (is_numeric($amount)) {
                $totalSpent += (float)$amount;
            }
        }
        
        // Find favorite car
        $carCounts = [];
        foreach ($rentals as $rental) {
            $carName = $rental['name'] ?? '';
            if ($carName) {
                $carCounts[$carName] = ($carCounts[$carName] ?? 0) + 1;
            }
        }
        
        if (!empty($carCounts)) {
            arsort($carCounts);
            $favoriteCar = array_key_first($carCounts);
        }
        
        return [
            'total_rentals' => $totalRentals,
            'total_spent' => $totalSpent,
            'favorite_car' => $favoriteCar,
            'member_since' => date('M Y', strtotime($user['join_date'] ?? 'now'))
        ];
    }
}