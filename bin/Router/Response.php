<?php

namespace Vroom\Router;

use Vroom\Orm\Model\Model;

class Response
{
    public function json(mixed $value = "{}")
    {
        $json = null;

        $json = match (gettype($value)) {
            "array" => json_encode($value),
            "object" => $this->objectToJson($value),
            default => "{}"
        };

        if (is_string($json)) {
            header("Content-Type: application/json");
            echo $json ?? '{}';
        }
    }

    private function objectToJson($value)
    {
        $json = null;
        if (get_parent_class($value) === Model::class) {
            $json = json_encode($value->serialize());
        }

        return $json;
    }
}