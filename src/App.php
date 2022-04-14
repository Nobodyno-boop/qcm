<?php

namespace App;

use App\Controller\ApiController;
use App\Controller\HomeController;
use App\Controller\QcmController;
use App\Controller\SecurityController;
use App\Controller\UserController;
use App\Model\Qcm;
use App\Model\User;
use Vroom\App\AbstractApp;

class App extends AbstractApp
{

    public function controller(): array
    {
        return [
            HomeController::class,
            UserController::class,
            SecurityController::class,
            QcmController::class,
            ApiController::class
        ];
    }

    public function models(): array
    {
        return [
            User::class,
            Qcm::class
        ];
    }
}