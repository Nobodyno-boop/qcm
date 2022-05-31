<?php

namespace App\Controller;


use App\Model\User;
use App\Utils\Utils;
use Vroom\Controller\AbstractController;
use Vroom\Router\Decorator\Route;
use Vroom\Router\Request;
use Vroom\Utils\Form;

#[Route("/admin", "app_admin_")]
class AdminController extends AbstractController
{
    private function isAdmin()
    {
        if (!($this->getRole() === "ADMIN")) {
            $this->response()->redirect("app_home");
        }
    }

    #[Route("/user/", name: "user_list")]
    public function user_list(Request $r)
    {
        $this->isAdmin();


        $page = $r->get()->getOrDefault("page", 1);
        $count = User::count();
        $maxPerPage = 10;
        $pagination = Utils::Pagination($count, $maxPerPage, $page);
        $users = User::findAll(limit: $maxPerPage, offset: $pagination['offset']);
        $this->renderView("admin/user/list", ["numberPage" => $pagination['numberPage'], "currentPage" => $page, "data" => $users]);
    }

    #[Route("/user/edit", name: "user_edit", methods: ['GET', 'POST'])]
    public function userEdit(Request $r)
    {
        $this->isAdmin();
        $page = $r->get()->getOrDefault("user", null);
        if (is_null($page)) {
            $this->response()->redirect("app_admin_user_list");
        }
        $user = User::find($page);
        if (is_null($user)) {
            $this->response()->redirect("app_admin_user_list");
        }

        $form = Form::new(data: ['user' => $user])
            ->add("user", Form::TYPE_MODEL, ["model" => User::class, "notdisplay" => ['password']])
            ->add("update", Form::TYPE_SUBMIT);

        $form->handleRequest($this->request());

        if ($form->isSent() && $form->isValid()) {
            /**
             * @var User $user
             */
            $user = $form->getData()->get("user");
            if ($user) {
                $repo = User::find(['email' => $user->getEmail()]);
                $id = $user->getId();
                if ($repo) {
                    if ($repo->getEmail() === $user->getEmail() && $id !== $repo->getId()) {
                        $form->addError("Vous ne pouvez pas renseigné une adresse mail qui est déjà dans la base de donnée");
                    }
                } else {
                    $user->save();
                    $this->response()->redirect("app_admin_user_list");
                }
            }

        }
        $this->renderView("admin/user/edit", ["form" => $form, "data" => $user]);
    }

    #[Route("/user/delete", name: "user_edit")]
    public function userDelete(Request $r)
    {
        $this->isAdmin();
        $page = $r->get()->getOrDefault("user", null);
        if (is_null($page)) {
            $this->response()->redirect("app_admin_user_list");
        }
        $user = User::find($page);
        if (is_null($user)) {
            $this->response()->redirect("app_admin_user_list");
        }

        $user->delete();
    }


}