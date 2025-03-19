<?php

namespace App\Helpers;

class AuthRoleHelper
{
    public static function checkUserCan($role)
    {
        if (isset($_SESSION['user']) && isset($_SESSION['user']['roles']) && is_array($_SESSION['user']['roles'])) {
            return in_array($role, $_SESSION['user']['roles'], true);
        }

        if (isset($_SESSION['user_role'])) {
            return strtolower($_SESSION['user_role']) === strtolower($role);
        }

        if (isset($_SESSION['roles']) && is_array($_SESSION['roles'])) {
            return in_array($role, $_SESSION['roles'], true);
        }

        return false;
    }
}
