<?php

namespace App\Controller;

use App\Model\Qcm;
use App\Model\QcmStats;
use App\Model\User;
use Vroom\Controller\AbstractController;
use Vroom\Router\Decorator\Route;
#[Route("qcm", name: "app_qcm_")]
class QcmController extends AbstractController
{
    #[Route("/view/{see}", name: "see")]
    public function see($see)
    {
        $see = intval($see);
        if (is_int($see)) {
            $qcmdata = Qcm::find($see);
            if ($qcmdata) {
                $id = $this->getSession("user")['id'];
                $stats = QcmStats::find(['qcm' => $qcmdata->getId(), 'user' => $id]);
                $qcm = \App\Qcm\Qcm::from($qcmdata->getData());
                $this->renderView("qcm/see", ["qcm_id" => $qcmdata->getId(), "qcm" => $qcm->getQcmAsJson(), "stats" => $stats->getData()]);
            }
        }
    }

    #[Route("/", name: "home")]
    public function qcmlist()
    {
        $maxPerPage = 2;
        $currentPage = $this->request()->get()->getOrDefault("page", 1);
        $count = Qcm::count();
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
        $qcms = Qcm::findAll(limit: $maxPerPage, offset: $offset);
        $this->renderView("qcm/list", ["numberPage" => $numberPage, "currentPage" => $currentPage, "data" => $qcms]);
    }

    #[Route("/result/{see}", methods: ['POST'])]
    public function seeResult($see)
    {
        $see = intval($see);
        if (is_int($see)) {
            $qcmdata = Qcm::find($see);
            $qcm = \App\Qcm\Qcm::from($qcmdata->getData());
            $body = json_decode($this->request()->getBody(), true);
            $qcm->setResponses($body['questions']);
            $stats = $qcm->generateStats();
            if (!empty($stats)) {
                $this->response()->json($stats);
            } else $this->response()->json(["bite" => 2]);

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