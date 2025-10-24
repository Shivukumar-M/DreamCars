<?php

class AdminDashboard extends BasicPage {

    public function render() {
        // Check if admin is logged in
        if (!isset($_SESSION['admin_logged_in'])) {
            header("Location: /admin");
            exit;
        }

        $this->setTitle('Admin Dashboard - RentDream');

        Renderer::render("admin/dashboard.php", [
            'includeNavbar' => false 
        ]);
    }

}
