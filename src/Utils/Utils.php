<?php

namespace App\Utils;

use JetBrains\PhpStorm\ArrayShape;

class Utils
{
    #[ArrayShape(['offset' => "float|int", "numberPage" => "int"])]
    public static function Pagination($count, $maxPerPage, $currentPage = 1): array
    {
        $numberPage = $count / $maxPerPage;

        if ($numberPage < 0) {
            $numberPage = 1;
        } else {
            if (round($numberPage) < $numberPage) {
                $numberPage = round($numberPage) + 1;
            } else { // 3.5 -> 4
                $numberPage = round($numberPage);
            }
        }
        $numberPage = intval($numberPage);
        $offset = $maxPerPage * ($currentPage - 1);
        if ($currentPage == 1) {
            $offset = 0;
        }

        return ['offset' => $offset, "numberPage" => $numberPage];
    }
}