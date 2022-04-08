<?php
require '../vendor/autoload.php';

use App\Model\User;
use Spatie\Ignition\Ignition;
use Vroom\Framework;
use Vroom\Orm\Model\Types;
use Vroom\Utils\Form;

session_start();
Ignition::make()->register();
Framework::newInstance('../config.php', new \App\App());
$user = new User();
$user->setEmail("allan.a@gmail.com");
$form = Form::new(data: ['user' => $user])
    ->add("user", Form::TYPE_MODEL, ['model' => User::class])
    ->add("Send !", Form::TYPE_SUBMIT);


//dump($form->toView("/"));