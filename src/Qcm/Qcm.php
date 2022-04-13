<?php

namespace App\Qcm;

class Qcm
{
    /**
     * @var array{Question}
     */
    private array $qcm;

    /**
     * @param $qcm
     */
    public function __construct($qcm)
    {
        $this->qcm = $qcm;
    }

    /**
     * @param array $response
     * @return bool
     */
    public function isValid(array $response): bool
    {
        if (count($response) !== count($this->qcm)) {
            return false;
        }

        for ($i = 0; $i < count($response); $i++) {
            $choice = $response[$i];
            $answer = $this->qcm[$i]->toJson();

            if (!(0 <= $choice) && $choice <= count($answer['answers']) - 1) {
                return false;
            }
        }
        return true;
    }

}