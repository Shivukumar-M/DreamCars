<?php

class Renderer {

    private static $injection = array();

    public static function inject($key, $value) {
        self::$injection[$key] = $value;
    }

    public static function render($contentFile, $variables = array()) {
        $contentFileFullPath = "../templates/" . $contentFile;

        // Merge global and local injected variables
        $mergedVars = array_merge(self::$injection, $variables);

        // Extract variables into the local scope
        extract($mergedVars);

        // Include Header
        require_once("../templates/components/header.php");

        echo "\n<main class=\"min-h-screen\">\n";

        // ✅ Conditionally include Navbar (NOW WORKS)
        if (!isset($includeNavbar) || $includeNavbar !== false) {
            $navbarPath = "../templates/components/navbar.php";
            if (file_exists($navbarPath)) {
                require_once($navbarPath);
            }
        }

        // Include the main content file
        if (file_exists($contentFileFullPath)) {
            require_once($contentFileFullPath);
        } else {
            require_once("../templates/error.php");
        }

        echo "</main>\n";

        // Always include footer
        require_once("../templates/components/footer.php");
    }
}
