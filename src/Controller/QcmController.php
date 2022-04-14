<?php

namespace App\Controller;

use App\Model\Qcm;
use App\Model\User;
use Vroom\Controller\AbstractController;
use Vroom\Router\Decorator\Route;

class QcmController extends AbstractController
{
    #[Route("/qcm/")]
    public function see()
    {
        dump(Qcm::find(['author' => 1]));
    }

    #[Route("/qcm/result")]
    public function result()
    {
        $a = [
            \App\Qcm\Question::from("Meow", ["Meow? ", "Hello", "Hi"], 1, "936e5b847881b862bdf6a35acb6189"),
            \App\Qcm\Question::from("lul", ["lol", "swag", "tektonike"], 2, "936e5b847881b862bdf6a35acb6196"),
            \App\Qcm\Question::from("Enta", ["Amandine ", "A meow", "Chou"], 1, "936e5b847881b875bdf6a35acb6189")
        ];

        $qcm = new \App\Qcm\Qcm($a);

        $qcm->setResponses([["id" => "936e5b847881b862bdf6a35acb6189", "answer" => 2], ["id" => "936e5b847881b862bdf6a35acb6196", "answer" => 2], ["id" => "936e5b847881b875bdf6a35acb6189", "answer" => 2]]);

        if ($qcm->isValid()) {
            dump($qcm->generateStats());
            dump(json_encode($qcm->getQcmAsJson()));
        }
//        $this->renderView('qcm/see', ["qcm"=> $qcm->getQcmAsJson()]);
    }

}