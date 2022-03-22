<?php

namespace App\Controller;

use Vroom\Controller\AbstractController;
use Vroom\Router\Decorator\Route;

class HomeController extends AbstractController
{
    #[Route("/")]
    public function index()
    {
        $this->renderView("home.twig");
    }
}