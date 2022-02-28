<?php

namespace App;

use App\Controller\UserController;
use App\Model\UserModel;
use Vroom\App\AbstractApp;

class App extends AbstractApp
{

    public function controller(): array
    {
        return [
            UserController::class
        ];
    }

    public function models(): array
    {
        return [
            UserModel::class
        ];
    }
}