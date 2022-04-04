<?php

namespace Vroom\Utils;

class Form
{
    const TYPE_BUTTON = 1;
    const TYPE_CHECKBOX = 2;
    const TYPE_COLOR = 3;
    const TYPE_DATE = 4;
    const TYPE_DATE_LOCAL = 5;
    const TYPE_EMAIL = 6;
    const TYPE_FILE = 7;
    const TYPE_HIDDEN = 8;
    const TYPE_IMAGE = 9;
    const TYPE_MONTH = 10;
    const TYPE_NUMBER = 11;
    const TYPE_PASSWORD = 12;
    const TYPE_RADIO = 13;
    const TYPE_RANGE = 14;
    const TYPE_RESET = 15;
    const TYPE_SEARCH = 16;
    const TYPE_SUBMIT = 17;
    const TYPE_TEL = 18;
    const TYPE_TEXT = 19;
    const TYPE_TIME = 20;
    const TYPE_URL = 21;
    const TYPE_WEEK = 22;

    /**
     * @var array{name: string, type: int, option: array}
     */
    private array $inputs;

    public function __construct()
    {

    }


    public function toView(): string
    {
        $result = "";
        foreach ($this->inputs as $input){
            $result .= "<label>";
            $result .= $this->makeInput($input);
        }


        return $result;
    }

    /**
     * ```php
     * Form::new()->add("name", Form::TYPE_TEXT);
     * ```
     * @param string $name
     * @param int $type constant type
     * @param array $option
     * @return Form
     */
    public function add(string $name, int $type, array $option = []): Form
    {
        $this->inputs[] = ["name" => $name, "type" => $type, "option" => $option];

        return $this;
    }
    /**
     * @param $data array{name: string, type: int, option: array}
     * @return string
     */
    private function makeInput($data): string
    {
        $array = ArrayUtils::from($data);
        $typeName = $data['name'];
        $type = $data['type'];
        $option = $data['option'];
        $base = ["id" => $typeName];

        $classOption = $array->getOrDefault("input.class", null);
        if(is_array($classOption)) {
            $classOption = implode(" ", $classOption);
            $base['class'] = $classOption;
        }
        $class = $classOption !== null ? "class='$classOption'" : "";

        $placeholder = $array->getOrDefault("input.placeholder", "");

        switch ($type){
            case self::TYPE_BUTTON: {
                $value = $option['value'] ?? "";

                return "<input type='button' $class value='$value'>";
            }
            case self::TYPE_RADIO:
            case self::TYPE_CHECKBOX: {
                $choice = $option['choice'];


                return "<input type='$typeName' $class >";
            }
            case self::TYPE_EMAIL: {

                $placeholder = empty($placeholder) ? ucfirst($typeName) : $placeholder;
//                return "<input type='email' $class placeholder='$placeholder'>";
                return $this->input($base);
            }

        }
        return "";
    }

    private function input(array $option): string
    {
        foreach ($option as $key => $value){
            
        }
        return "";
    }

    public static function new(): Form
    {
        return new Form();
    }
}