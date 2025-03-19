<?php

namespace App\Controllers;

class HomeController
{
    public function index()
    {
        require_once __DIR__ . '/../Views/home/index.php';
    }

    public function error()
    {
        require_once __DIR__ . '/../Views/errors/index.php';
    }
} 