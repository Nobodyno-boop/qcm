<?php

namespace App;

use App\Controller\HomeController;
use App\Controller\SecurityController;
use App\Controller\UserController;
use App\Model\User;
use Vroom\App\AbstractApp;

class App extends AbstractApp
{

    public function controller(): array
    {
        return [
            HomeController::class,
            UserController::class,
            SecurityController::class
        ];
    }

    public function models(): array
    {
        return [
            User::class
        ];
    }
}