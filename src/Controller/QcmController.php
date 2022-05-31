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
                $qcm = \App\Qcm\Qcm::from($qcmdata->getData());

                $this->renderView("qcm/see", ["qcmdata" => $qcmdata, "qcm_author" => $qcmdata->getAuthor()->getId(), "qcm" => $qcm->getQcmAsJson()]);
            } else $this->response()->redirect("app_qcm_list");
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

    #[Route("/result/{see}")]
    public function seeResult($see)
    {
        if (!$this->isLogin()) {
            return $this->response()->redirect("app_login");
        }
        $see = intval($see);
        if (is_int($see)) {
            $qcmdata = Qcm::find($see);
            $qcm = \App\Qcm\Qcm::from($qcmdata->getData());

            if (!$qcm) {
                return $this->response()->notFound();
            }
            $v = $qcm->getVersion();
            $custom = QcmStats::custom()->where(["user" => $this->getSession("user")['id'], "`data`->>'$.version' LIKE '$v'"])->limit(1);
            $stats = QcmStats::runQuery($custom);
            if (!empty($stats)) {
                $this->renderView("qcm/see_result", ["qcmdata" => $qcmdata, "qcm_author" => $qcmdata->getAuthor()->getId(), "qcm" => $qcm->getQcmAsJson(), 'stats' => $stats->getData()]);

            } else $this->response()->redirect("qcm/view/" . $see);
        }
    }

    #[Route("/result/{see}", methods: ['POST'])]
    public function seeResultPost($see)
    {
        $see = intval($see);
        if (is_int($see)) {
            $qcmdata = Qcm::find($see);
            $user =
            $qcm = \App\Qcm\Qcm::from($qcmdata->getData());
            $body = json_decode($this->request()->getBody(), true);
            $qcm->setResponses($body['questions']);
            $stats = $qcm->generateStats();
            if (!empty($stats)) {
                $newstats = new QcmStats();
                $newstats->setQcm($qcmdata);
                $newstats->setUser(User::find($this->getSession("user")['id']));
                $newstats->setData($stats);
                $newstats->save();
                $this->response()->json(['message' => 'ok']);
            } else $this->response()->json(["bite" => 2]);
        }
    }

    #[Route("/new")]
    public function new()
    {
        $token = $this->getToken('qcm/save');

        $this->renderView("qcm/new", ['token' => $token]);
    }

    #[Route("/edit/{id}", name: "edit")]
    public function edit($id)
    {
        if (!$this->isLogin()) {
            $this->response()->redirect("app_login");
        }
        $user = User::find($this->getSession('user')['id']);

        if (!$user) {
            $this->response()->redirect("app_login");
        }

        $qcm = Qcm::find($id);

        if ($qcm) {
            if (!$this->isAdmin() || $user->getId() !== $qcm->getAuthor()->getId()) {
                $this->response()->redirect("qcm/view/$id");
                return;
            }
            $token = $this->getToken('qcm/edit');
            $this->renderView("qcm/edit", ['qcm_id' => $qcm->getId(), 'qcm_title' => $qcm->getTitle(), 'qcm' => $qcm->getData()['question'], "token" => $token]);
        } else $this->response()->redirect("app_qcm_list");
    }

    #[Route("/delete/{id}")]
    public function delete($id)
    {
        if (!$this->isLogin()) {
            $this->response()->redirect("app_login");
        }
        $user = User::find($this->getSession('user')['id']);

        if (!$user) {
            $this->response()->redirect("app_login");
        }


        $qcm = Qcm::find($id);
        if ($qcm) {
            if (!$this->isAdmin() || $user->getId() !== $qcm->getAuthor()->getId()) {
                $this->response()->redirect("qcm/view/$id");
                return;
            }
            $qcm->delete();
            $this->response()->lastRoute();
        } else $this->response()->notFound();
    }

    /**
     * @throws \Exception
     */
    #[Route("/edit/", methods: ['POST'])]
    public function editSave(Request $request)
    {
        if (!$this->isLogin()) {
            $this->response()->redirect("app_home");
        }

        $body = json_decode($request->getBody(), true);

        if (!isset($body['token'])) {
            $this->response()->json(['message' => "le token n'est pas bon"]);
            return;
        }

        if (!$this->matchToken($body['token'])) {
            $this->response()->json([]);
            return;
        }

        if ($body) {
            if ($body) {
                if (!isset($body['id'])) {
                    $this->response()->json(['message' => "l'id du qcm manque"]);
                    return;
                }
                if (!isset($body['qcm'])) {
                    $this->response()->json(['message' => "le qcm manque"]);
                    return;
                }
                if (!isset($body['title'])) {
                    $this->response()->json(['message' => "le titre manque"]);
                    return;
                }

                $id = intval($body['id']);

                if ($id === -1) {
                    $this->response()->json(['message' => "l'id est invalide"]);
                    return;
                }

                $qcmData = Qcm::find($id);

                if (!$qcmData) {
                    $this->response()->json(['message' => "le qcm n'existe pas"]);
                    return;
                }

                $qcm = \App\Qcm\Qcm::from($qcmData->getData());

                $nqcm = $this->fromEditor($body['qcm']);
                $qcm->setQcm($nqcm);


                $qcmData->setTitle($body['title']);
                $dqa = $qcm->getQcmAsJson();
                $qcmData->setData($dqa);
                $qcmData->save();

                $this->response()->json(['message' => "ok"]);
            }

        }
//        $this->renderView("qcm/new");
    }


    #[Route("/save", methods: ['POST'])]
    public function save(Request $request)
    {
        if (!$this->isLogin()) {
            $this->response()->redirect("app_home");
        }
        $body = json_decode($request->getBody(), true);

        if (!isset($body['token'])) {
            $this->response()->json(['message' => "le token n'est pas présent"]);
            return;
        }

        if (!$this->matchToken($body['token'])) {
            $this->response()->json([]);
            return;
        }

        if ($body) {
            if ($body) {
                $q = $this->fromEditor($body['qcm']);
                $qcm = new \App\Qcm\Qcm($q);

                $data = new Qcm();
                $data->setTitle($body['title']);
                $data->setAuthor(User::find($this->getSession('user')['id']));
                $data->setData($qcm->getQcmAsJson());
                $data->save();
                $this->response()->json(['message' => "ok", "id" => $data->getId()]);
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

    private function isAdmin(): bool
    {
        return $this->getRole() === "ADMIN";
    }

}