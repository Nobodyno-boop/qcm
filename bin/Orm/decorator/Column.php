<?php

namespace Vroom\Orm\decorator;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column
{
    private string $name;
    private int $type;

    /**
     * @param string $name
     * @param int $type
     */
    public function __construct(string $name, int $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }


}