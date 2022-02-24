<?php

namespace Vroom\Orm\Model;

use ReflectionAttribute;
use Vroom\Orm\decorator\Column;
use Vroom\Orm\decorator\Entity;

class Models
{
    public static function readModel($model)
    {
        try {
            $class = new \ReflectionClass($model);
            if($class->isSubclassOf(Model::class)){
                $entityAttr = $class->getAttributes(Entity::class, ReflectionAttribute::IS_INSTANCEOF);
                if(empty($entityAttr)){
                    throw new \Error("Le model n'est pas instancier !");
                }
                /**@var Entity $entityClass*/
                $entityClass = $entityAttr[0]->newInstance();

                $classproperties = $class->getProperties();
                if(empty($classproperties)){
                    throw  new \Error();
                }
                $properties = [];
                foreach ($classproperties as $k){
                    $columns = $k->getAttributes(Column::class, ReflectionAttribute::IS_INSTANCEOF);
                    if(!empty($columns)){
                        /** @var Column $columnClass */
                        $columnClass = $columns[0]->newInstance();
                        $properties[] =  $columnClass;
                    }
                }

                return [
                    "entity" => $entityClass,
                    "properties" => $properties
                ];

            } // can't load
        } catch (\ReflectionException $e) {
            die($e->getMessage());
        }

        return [];
    }
}