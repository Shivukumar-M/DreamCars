<?php

class AdminAddCar extends BasicPage {

    public function render() {
        // Check if admin is logged in
        if(!isset($_SESSION['admin_logged_in'])) {
            header("Location: /admin");
            exit;
        }

        $this->setTitle('Add Car - Admin - RentDream');

        Renderer::render("admin/add_car.php");
    }

}