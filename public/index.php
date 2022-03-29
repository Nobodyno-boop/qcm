<?php
require '../vendor/autoload.php';

use App\Model\User;
use Spatie\Ignition\Ignition;
use Vroom\Framework;
use Vroom\Orm\Model\Types;

session_start();
Ignition::make()->register();
Framework::newInstance('../config.php', new \App\App());
//dump($_SERVER['REQUEST_URI']);
//function getRoutes() {
//    $routes = [new Route(["prefix" => "", "url" => "api/user/{id}"], \App\Controller\UserController::class, "GET"), new Route(["prefix" => "", "url" => "api/user/"], \App\Controller\UserController::class, "GET")];
//
//    foreach ($routes as $route){
//        if(!$route->match($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'])){
//            continue;
//        }
//
//        return $route;
//    }
//}
//
//dump(getRoutes());


//$models = \Vroom\Orm\Model\Models::readModel(\App\Model\User::class);

//$sql = new \Vroom\Orm\Sql\Sql();
//
//$sql->connect();
//
//$stm = $sql->getCon()->query("select * from user");
//
//$var = $stm->fetch(PDO::FETCH_ASSOC);
//dump($var);
//dump($models);
//
//$values = [];
//$u = new ReflectionClass(\App\Model\User::class);
//$user = $u->newInstance();
//foreach ($models['properties'] as $k){
//    $name = $k->getName();
//    if(isset($var[$name])){
//        call_user_func_array([$user, 'set'.ucfirst($name)],[$var[$name]]);
//    }
//}
//dump($user);

//$q = \Vroom\Orm\Sql\QueryBuilder::newInstance(\App\Model\User::class);
//$qq = $q->where(['id' => 1]);
//dump($qq->__toString());
//
//$q2 = \Vroom\Orm\Sql\QueryBuilder::newInstance(\App\Model\User::class)->update([
//    "name"=> "Nobody"
//])->where(['id' => 1]);
//
//dump($q2->__toString());
//
//$q3 = \Vroom\Orm\Sql\QueryBuilder::newInstance(\Ap p\Model\User::class)->delete()->where(['id' => 3]);
//
//dump($q3->__toString());
//
// $q4 = \Vroom\Orm\Sql\QueryBuilder::newInstance(\App\Model\User::class)->insert([
//    "name" => "Nobody"
// ]);
//
// dump($q4->__toString());

//$user = new \App\Model\User();
//
//$user->setName("Noboda");
//$user->save();
// select * from user where id = 2
//$user = \Vroom\Orm\Model\Models::findBy(\App\Model\User::class, ['id' => 2]);
//