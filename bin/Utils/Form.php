<?php

namespace Vroom\Utils;

class Form
{
    const TYPE_BUTTON = "button";
    const TYPE_CHECKBOX = "checkbox";
    const TYPE_COLOR = "color";
    const TYPE_DATE = "date";
    const TYPE_DATE_LOCAL = "datetime-local";
    const TYPE_EMAIL = "email";
    const TYPE_FILE = "file";
    const TYPE_HIDDEN = "hidden";
    const TYPE_IMAGE = "image";
    const TYPE_MONTH = "month";
    const TYPE_NUMBER = "number";
    const TYPE_PASSWORD = "password";
    const TYPE_RADIO = "radio";
    const TYPE_RANGE = "range";
    const TYPE_RESET = "reset";
    const TYPE_SEARCH = "search";
    const TYPE_SUBMIT = "submit";
    const TYPE_TEL = "tel";
    const TYPE_TEXT = "text";
    const TYPE_TIME = "time";
    const TYPE_URL = "url";
    const TYPE_WEEK = "week";

    /**
     * @var array{name: string, type: string, option: array}
     */
    private array $inputs;

    public function __construct()
    {

    }


    public function toView(): string
    {
        $result = "";
        foreach ($this->inputs as $input){
            $array = ArrayUtils::from($input);
            if($input['type'] != self::TYPE_SUBMIT){
                $text = $array->getOrDefault("label.text", ucfirst($array->get("name")));

                $result .= "<label for='".$input['name']."'>$text</label>";
            }
            $result .= $this->makeInput($input);
        }


        return $result;
    }

    /**
     * ```php
     * Form::new()->add("name", Form::TYPE_TEXT);
     * ```
     * @param string $name
     * @param string $type constant type
     * @param array $option
     * @return Form
     */
    public function add(string $name, string $type, array $option = []): Form
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
        $base = ["id" => $array->getOrDefault("input.attr.id", $typeName), "name" => $array->getOrDefault("input.attr.name", $typeName) ];

        $classOption = $array->getOrDefault("input.class", null);
        $inputAttr = $array->getOrDefault("input.attr", []);
        if(is_array($inputAttr)){
            $base = [...$base, ...$inputAttr];
        }
        if(is_array($classOption)) {
            $classOption = implode(" ", $classOption);
            $base['class'] = $classOption;
        }
        $class = $classOption !== null ? "class='$classOption'" : "";


        switch ($type){
            case self::TYPE_SUBMIT:
            case self::TYPE_BUTTON: {
                $base['id'] = $type;
                unset($base['name']);
                $value = $array->getOrDefault("input.attr.value", $typeName);
                return $this->input([...$base,"type" => $type ,"value" => $value]);
            }
            case self::TYPE_EMAIL: {
                $placeholder = $array->getOrDefault("input.attr.placeholder", "");
                $placeholder = empty($placeholder) ? ucfirst($typeName) : $placeholder;
                return $this->input([...$base, "type" => "email", "placeholder" => $placeholder]);
            }
            case self::TYPE_TEXT: {
                $placeholder = $array->getOrDefault("input.attr.placeholder", "");
                $placeholder = empty($placeholder) ? ucfirst($typeName) : $placeholder;
                return $this->input([...$base, "type" => "text", "placeholder" => $placeholder]);
            }
            default: {
                return $this->input([...$base, "type" => $type]);
            }
            case self::TYPE_RADIO:
            case self::TYPE_CHECKBOX: {
                $choice = $option['choice'];
                return "<input type='$typeName' $class >";
            }
        }
        return "";
    }

    private function input(array $option, bool $required = true): string
    {
        $text = $this->attributesToString($option);

        if($required){
            $text .= " required";
        }
        return "<input ".$text. ">";
    }

    private function attributesToString(array $attr):string
    {
        $attributes = [];
        foreach ($attr as $key => $value){
            $attributes[] = $key."='".$value."'";
        }
        return implode(" ", $attributes);
    }

    public static function new(): Form
    {
        return new Form();
    }
}