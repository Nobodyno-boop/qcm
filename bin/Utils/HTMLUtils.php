<?php

namespace Vroom\Utils;

class HTMLUtils
{
    private array $elements;
    private $current;

    public function __construct()
    {
        $this->elements = [];
    }

    public function add(string $node, string $text = "", array $attr = []): HTMLUtils
    {
        $data = ["node" => $node, "text" => $text, "attr" => $attr, "childs" => []];
        if(isset($this->current)){

        }else {
            $this->elements[] = $data;
            $this->current = &$this->elements[count($this->elements)-1];
        }

        return $this;
    }


    public function in(string $node, string $text = "", array $attr = []): HTMLUtils
    {
        $data = ["node" => $node, "text" => $text, "attr" => $attr, "childs" => []];

        if(isset($this->current)){
            $this->current['childs'][] = &$data;
            $this->current = &$data;
        }

        return $this;
    }


    public function to()
    {
        dump($this->elements);
    }

}