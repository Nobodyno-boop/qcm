<?php
require '../vendor/autoload.php';

use App\Model\User;
use Spatie\Ignition\Ignition;
use Vroom\Orm\Model\Types;
use Vroom\Utils\Form;

session_start();
Ignition::make()->register();
//Framework::newInstance('../config.php', new \App\App());

$form = Form::new()
    ->add("user", Form::TYPE_MODEL, ['model' => User::class])
    ->add("Send !", Form::TYPE_SUBMIT);


dump($form->toView("/"));