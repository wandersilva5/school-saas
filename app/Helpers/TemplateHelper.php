<?php

namespace App\Helpers;

class TemplateHelper
{
    private static $scripts = [];
    private static $styles = [];
    private static $currentSection = null;
    private static $buffer = '';

    public static function startPush($section)
    {
        self::$currentSection = $section;
        ob_start();
    }

    public static function endPush()
    {
        $content = ob_get_clean();
        if (self::$currentSection === 'scripts') {
            self::$scripts[] = $content;
        } elseif (self::$currentSection === 'styles') {
            self::$styles[] = $content;
        }
        self::$currentSection = null;
    }

    public static function renderScripts()
    {
        return implode("\n", self::$scripts);
    }

    public static function renderStyles()
    {
        return implode("\n", self::$styles);
    }
}