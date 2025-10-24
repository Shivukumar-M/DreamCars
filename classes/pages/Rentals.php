<?php

class Rentals extends BasicPage {

    public function render() {
        $this->setTitle('Rentals');

        $user = '';

        $user_id = $this->getLoginInfo();
        if($user_id != 0 ){
            $user = User::getUserInfo($this->getLoginInfo());
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['transaction_id']) && strlen($_POST['transaction_id']) != 0) {
                RentalService::removeRental($_POST['transaction_id']);
            }
        }

        $rentals = RentalService::getRentalsForUser($user_id);
        
        // Calculate and add amount for each rental if not already set
        foreach ($rentals as &$rental) {
            if (!isset($rental['amount'])) {
                $rate = $rental["rate_by_" . $rental["mode"]] ?? 0;
                $value = $rental['value'] ?? 0;
                $rental['amount'] = $rate * $value;
            }
        }

        Renderer::render("rentals.php", [
            'user' => $user,
            'rentals' => $rentals
        ]);
    }
}