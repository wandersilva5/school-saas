<?php

namespace App\Controllers;

class CalendarController extends BaseController
{
    public function index()
    {
        return $this->render('calendar/index', [
            'pageTitle' => 'Calendário',
            'currentPage' => 'calendar'
        ]);
    }
} 