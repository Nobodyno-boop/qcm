<?php

namespace Vroom\Orm\Decorator;

use Vroom\Utils\Form;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column
{
    private string $name;
    private int $type;
    private bool $nullable;
    private string $formType;
    /**
     * @param string $name
     * @param int $type
     */
    public function __construct(string $name, int $type, bool $nullable = false, string $formType = Form::TYPE_TEXT)
    {
        $this->name = $name;
        $this->type = $type;
        $this->nullable = $nullable;
        $this->formType = $formType;
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
    /**
     * @return string
     */
    public function getFormType(): string
    {
        return $this->formType;
    }

}