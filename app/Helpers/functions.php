<?php

use App\Helpers\UrlHelper;
use App\Helpers\TemplateHelper;

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