<?php
require '../vendor/autoload.php';

use Spatie\Ignition\Ignition;
use Vroom\Framework;

session_start();
Ignition::make()->register();
Framework::newInstance('../config.php', new \App\App());
