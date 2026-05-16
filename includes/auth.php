<?php

if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('require_login')) {
    function require_login() {
        if (!is_logged_in()) {
            header('Location: login.php');
            exit();
        }
    }
}

if (!function_exists('logout')) {
    function logout() {
        session_destroy();
        header('Location: login.php');
        exit();
    }
}

if (!function_exists('get_current_user_id')) {
    function get_current_user_id() {
        return $_SESSION['user_id'] ?? null;
    }
}

if (!function_exists('sanitize_input')) {
    function sanitize_input($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

?>