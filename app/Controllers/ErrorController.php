<?php

namespace App\Controllers;

class ErrorController extends BaseController
{
    public function notFound()
    {
        http_response_code(404);
        $this->render('errors/404');
    }
}
