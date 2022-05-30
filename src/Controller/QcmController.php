<?php

namespace App\Controller;

use App\Model\Qcm;
use App\Model\QcmStats;
use App\Model\User;
use App\Qcm\Question;
use App\Utils\Utils;
use Vroom\Controller\AbstractController;
use Vroom\Router\Decorator\Route;
use Vroom\Router\Request;

#[Route("qcm", name: "app_qcm_")]
class QcmController extends AbstractController
{
    #[Route("/view/{see}", name: "see")]
    public function see($see)
    {
        $see = intval($see);
        if (is_int($see) && $see >= 1) {
            $qcmdata = Qcm::find($see);
            if ($qcmdata) {
                $id = $this->getSession("user")['id'];
                $stats = QcmStats::find(['qcm' => $qcmdata->getId(), 'user' => $id]);
                $qcm = \App\Qcm\Qcm::from($qcmdata->getData());
                $this->renderView("qcm/see", ["qcm_id" => $qcmdata->getId(), "qcm_author" => $qcmdata->getAuthor()->getId(), "qcm" => $qcm->getQcmAsJson(), "stats" => $stats->getData() ?? []]);
            }
        } else $this->response()->redirect("app_qcm_list");
    }

    #[Route("/", name: "list")]
    public function qcmlist()
    {
        $maxPerPage = 4;
        $currentPage = $this->request()->get()->getOrDefault("page", 1);
        $count = Qcm::count();
        $paginations = Utils::Pagination($count, $maxPerPage, $currentPage);
        $qcms = Qcm::findAll(limit: $maxPerPage, offset: $paginations['offset']);
        $this->renderView("qcm/list", ["numberPage" => $paginations['numberPage'], "currentPage" => $currentPage, "data" => $qcms]);
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

    #[Route("/new")]
    public function write(Request $request)
    {

        $body = json_decode($request->getBody());

        if ($body) {
            $q = $this->fromEditor($body);
        }
        $this->renderView("qcm/new");
    }

    #[Route("/edit/{id}")]
    public function edit($id)
    {

        $qcm = Qcm::find($id);

        if ($qcm) {

            $this->renderView("qcm/edit", ['qcm' => $qcm->getData()['question']]);
        } else $this->response()->redirect("app_qcm_list");

    }


    #[Route("/save", methods: ['POST'])]
    public function save(Request $request)
    {
        if (!$this->isLogin()) {
            $this->response()->redirect("app_home");
        }
        $body = json_decode($request->getBody(), true);
        if ($body) {
            if ($body) {
                $q = $this->fromEditor($body);
                $qcm = new \App\Qcm\Qcm($q);

                $data = new Qcm();
                $data->setTitle("titre");
                $data->setAuthor(User::find($this->getSession('user')['id']));
                $data->setData($qcm->getQcmAsJson());
                $data->save();
                $this->response()->json(['message' => "ok"]);
            }

        }
//        $this->renderView("qcm/new");
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


    public function fromEditor($body): array
    {
        $q = [];
        foreach ($body as $question) {
            $title = $question['question'];
            $correct = $question['correct'];
            $answer = [];
            $id = $question['id'];
            for ($i = 0; $i < count($question['answers']); $i++) {
                $value = $question['answers'][$i];
                $answer[] = $value;
            }
            if ($correct !== -1) {
                $q[] = Question::from($title, $answer, $correct, $id);
            }
        }
        return $q;
    }

}