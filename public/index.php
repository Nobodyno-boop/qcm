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
    ->add("email", Form::TYPE_EMAIL, ["input_class" => "red"])
    ->add("password", Form::TYPE_PASSWORD)
    ->add("Send !", Form::TYPE_SUBMIT);


dump($form->toView("/"));