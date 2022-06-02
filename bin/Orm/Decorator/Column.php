<?php

namespace Vroom\Orm\Decorator;


use Vroom\Orm\Model\Types;
use Vroom\Utils\Form;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column
{
    private string $name;
    private int $type;
    private bool $nullable;
    private string $formType;
    private string $join;


    /**
     * @param string $name
     * @param int $type
     * @param bool $nullable
     * @param string $formType
     * @param string $join
     */
    public function __construct(string $name, int $type, bool $nullable = false, string $formType = Form::TYPE_TEXT, string $join = "")
    {
        $this->name = $name;
        $this->type = $type;
        $this->nullable = $nullable;
        $this->formType = $formType;
        $this->join = $join;
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

    /**
     * @return string
     */
    public function getJoin(): string
    {
        return $this->join;
    }

}