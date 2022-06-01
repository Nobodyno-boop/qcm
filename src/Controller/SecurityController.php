<?php

namespace App\Controller;

use App\Model\User;
use Vroom\Controller\AbstractController;
use Vroom\Router\Decorator\Route;
use Vroom\Router\Request;
use Vroom\Utils\Form;

class SecurityController extends AbstractController
{
    #[Route("/login", "app_login", ['GET', 'POST'])]
    public function loginPost(Request $r)
    {

        if (!$this->isLogin()) {
            $form = Form::new(option: ['label' => ['display' => false]])
                ->add("email", Form::TYPE_EMAIL)
                ->add("password", Form::TYPE_PASSWORD)
                ->add("Se connecter !", Form::TYPE_SUBMIT, ["input" => ["class" => "btn"]]);

            $form->handleRequest($r);
            if ($form->isSent() && $form->isValid()) {
                $email = $form->getReceiveData()->get("email");
                $password = $form->getReceiveData()->get("password");
                if (!$email && !$password) {
                    $form->addError("Formulaire invalide");
                    return $this->renderView("security/login.twig", ["form" => $form->toView()]);
                }

                $user = User::find(['email' => $email]);
                if (!$user) {
                    $form->addError("L'email ne corresponds à aucune donnée dans notre base.");
                    return $this->renderView("security/login.twig", ["form" => $form->toView()]);
                }

                if (!password_verify($password, $user->getPassword())) {
                    $form->addError("Le mot de passe ne corresponde pas");
                    return $this->renderView("security/login.twig", ["form" => $form->toView()]);
                }

                $this->addSession("user", $user);
                $this->response()->redirect("app_home");
            }
            return $this->renderView("security/login.twig", ["form" => $form->toView()]);

        } else $this->response()->redirect("app_home");
    }

    #[Route("/register", "app_register", methods: ['GET', 'POST'])]
    public function register()
    {
        if ($this->isLogin()) {
            $this->response()->redirect("app_home");
        }
        $user = new User();
        $form = Form::new(data: ['user' => $user])
            ->add("user", Form::TYPE_MODEL, ["model" => User::class, 'notdisplay' => ['role']])
            ->add("reset", Form::TYPE_RESET)
            ->add("register !", Form::TYPE_SUBMIT);

        $form->handleRequest($this->request());

        if ($form->isSent() && $form->isValid()) {
            /**
             * @var User $user
             */
            $user = $form->getData()->get("user");
            $user->setRole("USER");
            if ($user) {
                $repo = User::find(['email' => $user->getEmail()]);
                if (!$repo) {
                    $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));
                    $user->save();
                    $this->response()->redirect("app_login");
                } else $form->addError("Il y'a déjà un utilisateur avec cette email");
            }

        }
        $this->renderView("security/register", ['form' => $form->toView()]);
    }


    #[Route('/logout', "app_logout")]
    public function logout()
    {
        if ($this->isLogin()) {
            session_destroy();
        }
        $this->response()->redirect("app_home");
    }

}