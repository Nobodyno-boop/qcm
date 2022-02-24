<?php

namespace Vroom\Orm\decorator;

#[\Attribute]
class Type
{
    private int $type;

    /**
     * @param int $type
     */
    public function __construct(int $type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

}