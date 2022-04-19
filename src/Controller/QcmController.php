<?php

namespace App\Controller;

use App\Model\Qcm;
use App\Model\QcmStats;
use App\Model\User;
use App\Qcm\Question;
use Vroom\Controller\AbstractController;
use Vroom\Router\Decorator\Route;

class QcmController extends AbstractController
{
    #[Route("/qcm/view/{see}")]
    public function see($see)
    {
        $see = intval($see);
        if(is_int($see)){
            $qcmdata = Qcm::find($see);
            $qcm = \App\Qcm\Qcm::from($qcmdata->getData());

            $this->renderView("qcm/see", ["qcm" => $qcm->getQcmAsJson()]);
        }
    }

    #[Route("/qcm/result/{see}", methods: ['POST'])]
    public function seeResult($see)
    {
        $see = intval($see);
        if(is_int($see)){
            $qcmdata = Qcm::find($see);
            $qcm = \App\Qcm\Qcm::from($qcmdata->getData());
            $body = $this->getRequest()->getBody();


            $qcm->setResponses($body['questions']);
            if($qcm->isValid()){
                $this->response()->json(["bite" => 2]);
            } else $this->response()->json(["bite" => 4]);


//            $this->renderView("qcm/see", ["qcm" => $qcm->getQcmAsJson()]);
        }
    }



//    #[Route("/qcm/result")]
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
//            $qcmm = new Qcm();
//            $qcmm->setTitle("Meow");
//            $qcmm->setData($qcm->getQcmAsJson());
//            $qcmm->setAuthor(User::find(2));
//            $qcmm->save();

            $qcmStats = new QcmStats();
            $qcmStats->setQcm(Qcm::find(3));
            $qcmStats->setData($qcm->generateStats());
            $qcmStats->setUser(User::find($_SESSION['user']['id']));
            $qcmStats->save();
        }
//        $this->renderView('qcm/see', ["qcm"=> $qcm->getQcmAsJson()]);
    }

}