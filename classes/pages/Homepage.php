<?php

class Homepage extends BasicPage {

    public function render() {
        $this->setTitle('RentDream - Premium Car Rental');

        $user = '';
        $featured_cars = [];
        $stats = [];

        if($this->getLoginInfo() != 0 ){
            $user = User::getUserInfo($this->getLoginInfo());
        }

        $all_cars = RentalService::getCars();
        $featured_cars = array_slice($all_cars, 0, 6);
        
        $stats = [
            'total_cars' => count($all_cars),
            'happy_customers' => $this->getHappyCustomersCount(),
            'cities_covered' => $this->getCitiesCovered(),
            'years_experience' => 3
        ];

        Renderer::render("home.php", [
            'user' => $user,
            'cars' => $all_cars,
            'featured_cars' => $featured_cars,
            'stats' => $stats,
            'is_logged_in' => ($this->getLoginInfo() != 0)
        ]);
    }

    private function getHappyCustomersCount() {
        return 1250;
    }

    private function getCitiesCovered() {
        return 15;
    }

}