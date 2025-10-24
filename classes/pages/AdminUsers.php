<?php

class AdminUsers extends BasicPage {

    public function render() {
        // Check if admin is logged in
        if(!isset($_SESSION['admin_logged_in'])) {
            header("Location: /admin");
            exit;
        }

        $this->setTitle('Manage Users - Admin - RentDream');

        Renderer::render("admin/users.php");
    }

}