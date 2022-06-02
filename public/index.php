<?php
require '../vendor/autoload.php';
use Vroom\Framework;

Framework::newInstance('../config.php', new \App\App());
