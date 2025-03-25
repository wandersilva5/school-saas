<?php

use App\Helpers\UrlHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\AuthHelper;
use App\Helpers\AuthRoleHelper;
use App\Helpers\DateHelper;

if (!function_exists('base_url')) {
    function base_url($path = '')
    {
        return UrlHelper::baseUrl($path);
    }
}

if (!function_exists('push')) {
    function push($section)
    {
        TemplateHelper::startPush($section);
    }
}

if (!function_exists('endpush')) {
    function endpush()
    {
        TemplateHelper::endPush();
    }
}

if (!function_exists('render_scripts')) {
    function render_scripts()
    {
        return TemplateHelper::renderScripts();
    }
}

if (!function_exists('render_styles')) {
    function render_styles()
    {
        return TemplateHelper::renderStyles();
    }
}

if (!function_exists('check_auth')) {
    function check_auth()
    {
        AuthHelper::checkAuth();
    }
}

if (!function_exists('check_guest')) {
    function check_guest()
    {
        AuthHelper::checkGuest();
    }
}

if (!function_exists('current_user_can')) {
    function current_user_can($role)
    {
        return AuthRoleHelper::checkUserCan($role);
    }
}

if (!function_exists('calculate_age')) {
    /**
     * Calcula a idade a partir da data de nascimento
     */
    function calculate_age($birthDate) {
        return DateHelper::calculateAge($birthDate);
    }
}

if (!function_exists('format_date')) {
    /**
     * Formata a data para o formato brasileiro
     */
    function format_date($date) {
        return DateHelper::formatDate($date);
    }
}

if (!function_exists('check_menu_permissions')) {
    /**
     * Checks if user has permission to access a route, redirects if not
     */
    function check_menu_permissions($route) {
        \App\Helpers\MenuPermissionHelper::verifyAccess($route);
    }
}


if (!function_exists('check_responsavel_institution')) {
    /**
     * Verify that a user with 'Responsavel' role has an institution_id set
     * If not, redirects to institution selection page
     */
    function check_responsavel_institution() {
        \App\Helpers\UserRoleHelper::checkResponsavelRedirect();
    }
}

if (!function_exists('has_role')) {
    /**
     * Check if the current user has a specific role
     */
    function has_role($role) {
        return \App\Helpers\UserRoleHelper::hasRole($role);
    }
}