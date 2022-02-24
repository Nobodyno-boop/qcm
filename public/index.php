<?php
require '../vendor/autoload.php';

\Vroom\Framework::newInstance('../config.php');

$models = \Vroom\Orm\Model\Models::readModel(\App\Model\UserModel::class);

$sql = new \Vroom\Orm\Sql\Sql();

$sql->connect();

$stm = $sql->getCon()->query("select * from user");

$var = $stm->fetch(PDO::FETCH_ASSOC);
dump($var);
dump($models);

$values = [];
$u = new ReflectionClass(\App\Model\UserModel::class);
$user = $u->newInstance();
foreach ($models['properties'] as $k){
    $name = $k->getName();
    if(isset($var[$name])){
        call_user_func_array([$user, 'set'.ucfirst($name)],[$var[$name]]);
    }
}



dump($user);