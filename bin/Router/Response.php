<?php

namespace Vroom\Router;

use Vroom\Orm\Model\Model;
use Vroom\Utils\Container;

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
        if($json){
            return $json;
        } else return "";
    }

    /**
     * @param string $url can be a full url or route prefix
     * @return void
     */
    public function redirect(string $url)
    {
        $url = Router::getFromPrefix($url) ?? $url;
        $site = Container::get("_config")->get("site.url");
        if(is_object($url)){
            $url = $url->getPath();
        }

        if(!str_starts_with($url, "/")){
            $url = "/".$url;
        }
        header("Location: ".$site.$url);
        exit();
    }
}