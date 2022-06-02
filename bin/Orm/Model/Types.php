<?php

namespace Vroom\Orm\Model;

class Types
{
    // Custom
    const ID = 1;
    const ONE_TO_ONE = 2;
    const ONE_TO_MANY = 3;
    const MANY_TO_ONE = 4;
    const MANY_TO_MANY = 5;
    // basic
    const VARCHAR = 101;
    const INT = 102;
    const JSON = 105;
    const DATETIME = 120;
}