<?php

namespace Vroom\Utils;

use Vroom\Orm\Decorator\Column;
use Vroom\Orm\Model\Model;
use Vroom\Orm\Model\Models;
use Vroom\Orm\Model\Types;
use Vroom\Router\Request;
use Vroom\Security\Token;

class Form
{

    //basic HTML input
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

    //custom input
    const TYPE_MODEL = "model";

    /**
     * @var array{name: string, type: string, option: array}
     */
    private array $inputs;
    private array $currentInputs;
    private array $options;
    private mixed $data;
    private Request $request;
    private array $errors;

    /**
     * @param array $options
     */
    public function __construct(array $options, mixed $data)
    {
        $this->options = $options;
        $this->data = $data ?? [];
        $this->errors = [];
    }

    private function updateCurrentInput():array
    {
        $tempInputs = [];
        // pre match if its a model
        foreach ($this->inputs as $input){
            if($input['type'] === self::TYPE_MODEL){
                $model = $input['option']['model'] ?? null;
                if($model){
                    $class = Models::get($model);
                    $tempInputs = [...$this->modelToInputs($class['properties'], $input['option'], $input['name'])];
                }
            } else {
                $tempInputs[] = $input;
            }
        }

        $this->currentInputs = $tempInputs;
        return $tempInputs;
    }

    public function countCurrentInputs():int
    {
        $count = 0;
        $crsf = $this->options['crsf'] ?? true;
        if($crsf){
            $count++;
        }

        foreach ($this->currentInputs as $input){
            if($input['type'] != self::TYPE_SUBMIT && $input['type'] != self::TYPE_RESET && $input['type'] != self::TYPE_MODEL){
                $count++;
            }
        }
        return $count;
    }

    public function toView(string $url = ""): string
    {
        $formAttr = $this->attributesToString($this->options['input_attr'] ?? []);
        $result = "<form action='$url' method='post' $formAttr>" . PHP_EOL;
        $tempInputs = [];


        $tempInputs = $this->currentInputs;
        // add crsf
        $haveCrsf = $this->options['crsf'] ?? true;
        if($haveCrsf){
            $url = empty($url) ? Container::get("currentRoute") : $url;
            $token = Token::getToken(url:$url);
            $_SESSION['_crsf'] = serialize($token);
            $tempInputs[] = ['name' => 'crsf', "type" => self::TYPE_HIDDEN, "option" => ["input" => ['attr' => ['value' => $token->token]]]];

        }
          foreach ($tempInputs as $input) {
            $array = ArrayUtils::from($input);
            if ($input['type'] !== self::TYPE_SUBMIT && $input['type'] !== self::TYPE_RESET && $input['type'] != self::TYPE_HIDDEN) {
                $text = $array->getOrDefault("label.text", ucfirst($array->get("name")));
                $result .= "<label for='" . $input['name'] . "'>$text</label>" . PHP_EOL;
            }
            $result .= $this->makeInput($input) . PHP_EOL;
        }
        $result .= "</form>";
        return $result;
    }

    /**
     * @param Column[] $data
     * @param $option
     * @return array
     */
    private function modelToInputs(array $data, $option, string $input): array
    {
        $inputs = [];
        /**
         * @var Model|null $obj
         */
        $obj = $this->data['user'] ?? null;
        $displayNullable = $this->options['displayNullable'] ?? false;
        $option['inputmodel'] = $input;
        foreach ($data as $column) {
            $type = $column->getType();
            $see = true;
            $types = match ($type) {
                Types::int => self::TYPE_NUMBER,
                Types::ID => null,
                default => $column->getFormType()
            };
            $value = null;
            if ($obj && get_parent_class($obj) === Model::class) {
                $tmpValue = $obj->getVariable($column->getName());

                if($tmpValue){
                    $value = $tmpValue;
                }
            }
            if ($column->isNullable()) {
                if (!$displayNullable) {
                    $see = false;
                }
            }

            if ($types != null && $see) {
                $name = $column->getName();
                $inputs[] = ["name" => $column->getName(), "type" => $types, "option" => ["require" => !$column->isNullable(), "input" => ["attr" => ["value" => $value ?? ""]], ...$option]];
            }
        }
        return $inputs;
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
        $this->updateCurrentInput();
        return $this;
    }

    /**
     * @param $data array{name: string, type: int, option: array}
     * @return string
     */
    private function makeInput(array $data): string
    {
        $array = ArrayUtils::from($data['option']);
        $typeName = $data['name'];
        $type = $data['type'];
        $option = $data['option'];
        $base = ["id" => $array->getOrDefault("input.attr.id", $typeName), "name" => $array->getOrDefault("input.attr.name", $typeName) ];
        $required = $array->getOrDefault("require", true);
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
            case self::TYPE_RESET:
            case self::TYPE_BUTTON:
            {
                $base['id'] = $type;
                unset($base['name']);
                $value = $array->getOrDefault("input.attr.value", $typeName);
                return $this->input([...$base, "type" => $type, "value" => $value], false);
            }
            case self::TYPE_EMAIL: {
                $placeholder = $array->getOrDefault("input.attr.placeholder", "");
                $placeholder = empty($placeholder) ? ucfirst($typeName) : $placeholder;
                return $this->input([...$base, "type" => "email", "placeholder" => $placeholder], $required);
            }
            case self::TYPE_TEXT: {
                $placeholder = $array->getOrDefault("input.attr.placeholder", "");
                $placeholder = empty($placeholder) ? ucfirst($typeName) : $placeholder;
                return $this->input([...$base, "type" => "text", "placeholder" => $placeholder], $required);
            }
            default: {
                return $this->input([...$base, "type" => $type], $required);
            }
            case self::TYPE_RADIO:
            case self::TYPE_CHECKBOX: {
                $choice = $option['choice'];
                return "<input type='$typeName' $class >";
            }
        }
    }

    private function input(array $option, bool $required = true): string
    {
        $text = $this->attributesToString($option);

        if($required){
            $text .= " required";
        }
        return "<input ".$text. ">";
    }

    private function attributesToString(array $attr): string
    {
        $attributes = [];
        foreach ($attr as $key => $value) {
            $attributes[] = $key . "='" . $value . "'";
        }
        return implode(" ", $attributes);
    }

    public static function new(array $option = [], mixed $data = null): Form
    {
        return new Form($option, $data);
    }

    public function isValid()
    {
        if (!$this->request->post()->isEmpty()) {
            foreach ($this->request->post()->getArray() as $key => $input) {
//                $arr = ArrayUtils::from($input);
                if($this->countCurrentInputs() == count($this->request->post()->getArray())){
                    $filter = array_values(array_filter($this->currentInputs, function ($map) use($key){
                        if($map['name'] === $key){
                            return $map;
                        }
                    }));

                    if(!empty($filter)){
                        $value = match ($filter[0]['type']) {
                            self::TYPE_URL => $filter($input, FILTER_SANITIZE_URL),
                            self::TYPE_EMAIL => filter_var($input, FILTER_SANITIZE_EMAIL),
                            default => filter_var($input, FILTER_SANITIZE_FULL_SPECIAL_CHARS)
                        };

                        $value = match ($filter[0]['type']){
                            self::TYPE_EMAIL => filter_var($value, FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE),
                            self::TYPE_NUMBER => filter_var($value, FILTER_VALIDATE_INT),
                            default => $input
                        };

                        if(!$value){
                            $this->errors['input'][$key] = "L'input n'est pas valide";
                        } else {
                            $ismodel = $filter[0]['model'] ?? null;
                            if($ismodel){
                                $nameModel = $filter[0]['inputmodel'];
                                $obj = $this->data[$nameModel] ?? null;
                                $clasz = $filter[0]['model'];
                                if($obj && get_class($obj) === $clasz){
                                    try {
                                        call_user_func([$obj, 'set'.Model::varName($key)]);
                                    }catch (\Error $e){

                                    }
                                }
                            }
                        }
                    } else if($key === "crsf") {
                        $token = $_SESSION['_crsf'] ?? null;
                        if($token){
                            $token = unserialize($token);
                            $token->match($input, $this->request->getRoute()->getPath());
                        } else $this->errors['form'][] = "no token"; // illegal access
                    } else {
                        $this->errors['form'][] = "Le formulaire n'est pas valide";
                    }
                }
            }

            if(empty($this->errors)){
                return true;
            }
        }


        return false;
    }

    public function isSent(): bool
    {
        if (isset($this->request)) {
            if (!$this->request->post()->isEmpty()) {
                return true;
            }
        }

        return false;
    }

    public function handleRequest(Request $r)
    {
        if ($r->getRoute()->getMethod() === "POST") {
            $this->request = $r;


        }
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getData(): ArrayUtils
    {
        return $this->request->post();
    }
}