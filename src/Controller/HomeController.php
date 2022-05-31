<?php

namespace App\Controller;

use App\Model\Qcm;
use Vroom\Controller\AbstractController;
use Vroom\Router\Decorator\Route;

class HomeController extends AbstractController
{
    #[Route("/", "app_home")]
    public function index()
    {
        $data = Qcm::findAll(limit: 3, order: 'DESC');
        $this->renderView("home.twig", ['data' => $data ?? []]);
    }
}