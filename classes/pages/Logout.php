<?php

class Logout extends BasicPage {

    public function render() {
        $this->setTitle('Log Out');

        Utils::logout();
        $this->refreshStatus();

        // Redirect to home page
        header('Location: /');
        exit();
        
        // The render call is removed since we're redirecting
    }

}