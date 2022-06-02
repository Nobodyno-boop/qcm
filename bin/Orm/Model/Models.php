<?php

namespace Vroom\Orm\Model;

use PDO;
use ReflectionAttribute;
use ReflectionClass;
use Vroom\Container\Container;
use Vroom\Orm\Decorator\Column;
use Vroom\Orm\Decorator\Entity;
use Vroom\Orm\Sql\QueryBuilder;
use Vroom\Orm\Sql\Sql;

class Models
{
    const CONTAINER_NAMESPACE = "_models";

    public static function readModel($model)
    {
        try {
            $class = new \ReflectionClass($model);
            if ($class->isSubclassOf(Model::class)) {
                $entityAttr = $class->getAttributes(Entity::class, ReflectionAttribute::IS_INSTANCEOF);
                if (empty($entityAttr)) {
                    throw new \Error("Le model n'est pas instancier !");
                }
                /**@var Entity $entityClass */
                $entityClass = $entityAttr[0]->newInstance();

                $classproperties = $class->getProperties();
                if (empty($classproperties)) {
                    throw  new \Error();
                }
                $properties = [];
                foreach ($classproperties as $k) {
                    $columns = $k->getAttributes(Column::class, ReflectionAttribute::IS_INSTANCEOF);
                    if (!empty($columns)) {
                        /** @var Column $columnClass */
                        $columnClass = $columns[0]->newInstance();
                        $properties[] = $columnClass;
                    }
                }
                $m = [
                    "entity" => $entityClass,
                    "properties" => $properties,
                    "class" => $class->getName()
                ];
                if (Container::isEmpty(Models::CONTAINER_NAMESPACE)) {
                    Container::set(Models::CONTAINER_NAMESPACE, [
                        $entityClass->getName() => $m
                    ]);
                } else {
                    $models = Container::get(Models::CONTAINER_NAMESPACE);
                    $models = array_merge([$entityClass->getName() => $m], $models);
                    Container::set(self::CONTAINER_NAMESPACE, $models);
                }
                return $m;

            }
            return []; // can't load
        } catch (\ReflectionException $e) {
            return [];
        }
    }

    public static function get($model)
    {
        if (is_object($model)) {
            $model = get_class($model);
        }

        if (is_string($model)) {
            $models = Container::get(self::CONTAINER_NAMESPACE);
            return $models[$model] ?? self::readModel($model);
        } else return [];
    }

}