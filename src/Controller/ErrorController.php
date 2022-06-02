<?php

namespace App\Controller;

use Vroom\Router\Decorator\Route;

class ErrorController extends \Vroom\Controller\AbstractController
{
    #[Route("/404", name: "404")]
    public function notFound()
    {
        $this->renderView("errors/404");
    }
}