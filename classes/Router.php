<?php

require_once("../classes/pages/BasicPage.php");
require_once("../classes/pages/Homepage.php");
require_once("../classes/pages/Register.php");
require_once("../classes/pages/Login.php"); // Changed from Signin.php
require_once("../classes/pages/Logout.php");
require_once("../classes/pages/Profile.php");
require_once("../classes/pages/CarDetails.php");
require_once("../classes/pages/Rent.php");
require_once("../classes/pages/Rentals.php");
require_once("../classes/pages/NotFound.php");
// Removed: require_once("../classes/pages/AdminLogin.php"); - Now using combined Login
require_once("../classes/pages/AdminDashboard.php");
require_once("../classes/pages/AdminAddCar.php");
require_once("../classes/pages/AdminUsers.php");
require_once("../classes/pages/AdminRentals.php");

class Router {

    private static function getCurrentUri() {
        $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        $uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
        if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        $uri = '/' . trim($uri, '/');
        return $uri;
    }

    public static function route() {
        $base_url = self::getCurrentUri();
        $routes = array_filter(explode('/', $base_url));
        
        // Debug (comment out in production)
        // echo "<!-- Base URL: $base_url -->";
        // echo "<!-- Routes: " . implode(', ', $routes) . " -->";

        // Handle empty route (homepage)
        if (empty($routes) || (count($routes) === 1 && empty($routes[1]))) {
            (new Homepage())->render();
            return;
        }

        // Admin routes
        if ($routes[1] === "admin") {
            $adminRoute = $routes[2] ?? '';
            
            switch ($adminRoute) {
                case '':
                case 'login':
                    // Redirect to combined login with admin parameter
                    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                        header("Location: /login?admin=true");
                        exit;
                    } else {
                        // Handle POST requests directly
                        (new Login())->render();
                    }
                    break;
                case 'dashboard':
                    (new AdminDashboard())->render();
                    break;
                case 'add-car':
                    (new AdminAddCar())->render();
                    break;
                case 'users':
                    (new AdminUsers())->render();
                    break;
                case 'rentals':
                    (new AdminRentals())->render();
                    break;
                default:
                    (new NotFound())->render();
            }
            return;
        }

        // Main site routes
        switch ($routes[1]) {
            case "register":
                (new Register())->render();
                break;
            case "logout":
                (new Logout())->render();
                break;
            case "signin":
                // Redirect old signin route to new login
                header("Location: /login");
                exit;
                break;
            case "login": // New combined login route
                (new Login())->render();
                break;
            case "profile":
                (new Profile())->render();
                break;
            case "car":
                $carId = $routes[2] ?? null;
                if ($carId) {
                    (new CarDetails($carId))->render();
                } else {
                    (new NotFound())->render();
                }
                break;
            case "rent":
                $carId = $routes[2] ?? null;
                if ($carId) {
                    (new Rent($carId))->render();
                } else {
                    (new NotFound())->render();
                }
                break;
            case "rentals":
                (new Rentals())->render();
                break;
            default:
                (new NotFound())->render();
        }
    }

}