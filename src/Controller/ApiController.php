<?php

namespace App\Controller;

use App\Model\User;
use Vroom\Controller\AbstractController;
use Vroom\Router\Decorator\Route;

#[Route("api", name: "app_api_")]
class ApiController extends AbstractController
{
    #[Route("/")]
    public function index()
    {
        $user = User::find(3);
        dump($user);
    }

    public function qcm()
    {

    }
}