<?php

namespace App\Controller;

use Vroom\Controller\AbstractController;
use Vroom\Router\decorator\Route;

class UserController extends AbstractController
{
    #[Route("/user")]
    public function index()
    {

    }

    #[Route("/login")]
    public function login(){

    }
}