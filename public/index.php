<?php
require '../vendor/autoload.php';

use Spatie\Ignition\Ignition;
use Vroom\Framework;

session_start();
Ignition::make()->register();
Framework::newInstance('../config.php', new \App\App());

//$form = Form::new(data: ['user' => $user])
//    ->add("user", Form::TYPE_MODEL, ['model' => User::class])
//    ->add("Send !", Form::TYPE_SUBMIT);


//dump($form->toView("/"));
