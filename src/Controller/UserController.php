<?php

namespace App\Controller;

use Vroom\Controller\AbstractController;
use Vroom\Router\decorator\Route;
use Vroom\Router\Request;

class UserController extends AbstractController
{
    #[Route("/user")]
    public function index()
    {
    }
    #[Route("/user/:id/:slug")]
    public function getUser(Request $request, $id, $slug, $other)
    {
        dump($request, $id, $slug, $other);
    }

    #[Route("/login")]
    public function login(){

    }
}