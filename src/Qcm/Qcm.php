<?php

namespace App\Qcm;

class Qcm
{
    /**
     * @var array
     */
    private array $qcm;

    /**
     * @param $qcm
     */
    public function __construct($qcm)
    {
        $this->qcm = $qcm;
    }

    public function check(array $response): bool
    {
        return true;
    }

}