<?php

namespace Vroom\Utils;

class Session extends ArrayUtils
{

    public function __construct()
    {
        parent::__construct($_SESSION);
    }


    public static function from(array $array = []): ArrayUtils
    {
        return new Session();
    }
}