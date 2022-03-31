<?php

namespace App\Controller;

use Vroom\Controller\AbstractController;
use Vroom\Router\Decorator\Route;

#[Route("api", name: "app_api_")]
class ApiController extends AbstractController
{
    #[Route("/")]
    public function index()
    {
        echo 'hey';
    }

    public function qcm()
    {

    }
}