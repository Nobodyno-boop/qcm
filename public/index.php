<?php
require '../vendor/autoload.php';

use Spatie\Ignition\Ignition;
use Vroom\Framework;

Ignition::make()->register();
Framework::newInstance('../config.php', new \App\App());

//$models = \Vroom\Orm\Model\Models::readModel(\App\Model\UserModel::class);

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
//$u = new ReflectionClass(\App\Model\UserModel::class);
//$user = $u->newInstance();
//foreach ($models['properties'] as $k){
//    $name = $k->getName();
//    if(isset($var[$name])){
//        call_user_func_array([$user, 'set'.ucfirst($name)],[$var[$name]]);
//    }
//}
//dump($user);

//$q = \Vroom\Orm\Sql\QueryBuilder::newInstance(\App\Model\UserModel::class);
//$qq = $q->where(['id' => 1]);
//dump($qq->__toString());
//
//$q2 = \Vroom\Orm\Sql\QueryBuilder::newInstance(\App\Model\UserModel::class)->update([
//    "name"=> "Nobody"
//])->where(['id' => 1]);
//
//dump($q2->__toString());
//
//$q3 = \Vroom\Orm\Sql\QueryBuilder::newInstance(\Ap p\Model\UserModel::class)->delete()->where(['id' => 3]);
//
//dump($q3->__toString());
//
// $q4 = \Vroom\Orm\Sql\QueryBuilder::newInstance(\App\Model\UserModel::class)->insert([
//    "name" => "Nobody"
// ]);
//
// dump($q4->__toString());

//$user = new \App\Model\UserModel();
//
//$user->setName("Noboda");
//$user->save();
// select * from user where id = 2
//$user = \Vroom\Orm\Model\Models::findBy(\App\Model\UserModel::class, ['id' => 2]);
//