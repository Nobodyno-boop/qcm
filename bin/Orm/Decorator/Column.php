<?php

namespace Vroom\Orm\Decorator;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column
{
    private string $name;
    private int $type;
    private bool $nullable;

    /**
     * @param string $name
     * @param int $type
     */
    public function __construct(string $name, int $type, bool $nullable = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->nullable = $nullable;
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

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

}