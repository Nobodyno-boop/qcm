<?php

namespace App\Controller;

use App\Model\User;
use Vroom\Controller\AbstractController;
use Vroom\Router\Decorator\Route;
use Vroom\Router\Request;
use Vroom\Utils\Form;

class UserController extends AbstractController
{
    #[Route("/profile/see/{id}", "app_user_profile_username")]
    public function profileUser($id)
    {
        if (intval($id) <= 0) {
            return $this->response()->redirect("404");
        }

        $user = User::find($id);
        if (!$user) {
            return $this->response()->redirect("404");
        }
        $this->renderView("user/profile", ["user" => $user]);
    }

    #[Route("/profile/", "app_user_profile")]
    public function profile(Request $request)
    {
        if (!$this->isLogin()) {
            $this->response()->redirect('app_login');
        }
        $user = User::find($this->getSession("user")['id']);
        if ($user) {
            $this->renderView("user/profile", ['user' => $user]);
        } else $this->response()->notFound();
    }

    #[Route("/profile/edit", "app_user_editprofile", methods: ['GET', "POST"])]
    public function editProfile(Request $request)
    {
        if (!$this->isLogin()) {
            $this->response()->redirect("app_home");
        }
        $user = User::find($this->getSession("user")['id']);

        $form = Form::new()
            ->add("currentpassword", Form::TYPE_PASSWORD)
            ->add("newpassword", Form::TYPE_PASSWORD)
            ->add("secondpassword", Form::TYPE_PASSWORD)
            ->add("Update", Form::TYPE_SUBMIT);

        $form->handleRequest($request);

        if ($form->isSent() && $form->isValid()) {
            $data = $form->getReceiveData();
            $cpass = $data->get("currentpassword");

            if ($cpass) {
                if (password_verify($cpass, $user->getPassword())) {
                    if ($data->get('newpassword') === $data->get("secondpassword")) {
                        $pass = password_hash($data->get("newpassword"), PASSWORD_DEFAULT);
                        $user->setPassword($pass);
                        $user->save();
                        $this->addSession("user", $user);
                    } else $form->addError("Les nouveaux mots de passe doivent être identique");
                } else $form->addError("Le mots de passe ne correspond pas !");
            } else $form->addError("Le formulaire n'est pas correct");
        }


        $this->renderView("user/profile_edit", ["form" => $form]);
    }

    #[Route("/profile/delete", name: "user_delete")]
    public function userDelete(Request $r)
    {
        if (!$this->isLogin()) {
            $this->response()->redirect("app_login");
        }

        $id = $this->getSession('user')['id'];
        $user = User::find($id);
        if (is_null($user)) {
            $this->response()->redirect("app_login");
        }
        $user->delete();
        $this->response()->redirect("app_logout");
    }
}