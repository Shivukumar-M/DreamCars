<?php

class Utils {

    public static function login($id) {
        $_SESSION['id'] = $id;
    }

    public static function logout() {
        session_unset();
        session_destroy();
    }

    public static function getLoggedIn() {
        if(isset($_SESSION['id']))
            return $_SESSION['id'];
        else
            return 0;
    }

    public static function redirect($url, $statusCode = 303) {
        header('Location: ' . $url, true, $statusCode);
        exit;
    }

    // Flash message methods - ADD THESE
    public static function setFlashMessage($message, $type = 'success') {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }

    public static function getFlashMessage() {
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            $type = $_SESSION['flash_type'];
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
            return ['message' => $message, 'type' => $type];
        }
        return null;
    }

}