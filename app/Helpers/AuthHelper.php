<?php

namespace App\Helpers;

class AuthHelper
{
    public static function isLoggedIn()
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }

    public static function hasPermission($permission)
    {
        if (!self::isLoggedIn()) {
            return false;
        }

        return isset($_SESSION['user']['permissions']) &&
            in_array($permission, $_SESSION['user']['permissions']);
    }

    public static function checkAuth()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function checkGuest()
    {
        if (isset($_SESSION['user'])) {
            header('Location: /dashboard');
            exit;
        }
    }
}
