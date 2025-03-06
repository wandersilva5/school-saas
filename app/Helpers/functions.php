<?php

use App\Helpers\UrlHelper;

if (!function_exists('base_url')) {
    function base_url($path = '')
    {
        return UrlHelper::baseUrl($path);
    }
}