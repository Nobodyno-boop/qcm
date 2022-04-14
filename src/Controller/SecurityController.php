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
            $form = Form::new()
                ->add("email", Form::TYPE_EMAIL)
                ->add("password", Form::TYPE_PASSWORD)
                ->add("Ce connecter !", Form::TYPE_SUBMIT);

            $form->handleRequest($r);
            if ($form->isSent() && $form->isValid()) {
                $email = $form->getReceiveData()->get("email");
                $password = $form->getReceiveData()->get("password");
                if ($email && $password) {
                    /**
                     * @var User $user
                     */
                    $user = $this->repository(User::class)->findBy("email", $email);
                    if ($user) {
                        if (password_verify($password, $user->getPassword())) {
                            $this->addSession("user", $user);
                            $this->response()->redirect("app_home");
                        } else $form->addError("Le mot de passe ne corresponde pas");
                    } else $form->addError("L'email ne corresponds à aucune donnée dans notre base.");
                } else $form->addError("Formulaire invalide");
            }
            $this->renderView("security/login.twig", ["form" => $form->toView()]);

        }
    }

    #[Route("/register", "app_register", methods: ['GET', 'POST'])]
    public function register()
    {
        if ($this->isLogin()) {
            $this->response()->redirect("app_home");
        }
        $user = new User();
        $form = Form::new(data: ['user' => $user])
            ->add("user", Form::TYPE_MODEL, ["model" => User::class])
            ->add("reset", Form::TYPE_RESET)
            ->add("register !", Form::TYPE_SUBMIT);

        $form->handleRequest($this->getRequest());

        if ($form->isSent() && $form->isValid()) {
            /**
             * @var User $user
             */
            $user = $form->getData()->get("user");
            if ($user) {
                $repo = $this->repository(User::class)->findBy("email", $user->getEmail());
                if (!$repo) {
                    $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));
                    $user->save();
                    $this->response()->redirect("app_login");
                } else $form->addError("Il y'a déjà un utilisateur avec cette email");
            }

        }
        $this->renderView("security/register", ['form' => $form->toView()]);
    }

//    #[Route("/register", methods: ['POST'])]
//    public function registerPost(Request $r)
//    {
//        $token = $r->post('crsf');
//        $email = $r->post("email");
//        $passord = $r->post("password");
//
//        if($this->matchToken($token)){
//            $user = new User();
//            $user->setEmail($email);
//            $user->setPassword(password_hash($passord, PASSWORD_DEFAULT));
//            $user->setUsername("Nobody");
//            $user->save();
//
//            $this->response()->json(["message" => "ok"]);
//        }
//    }

    #[Route('/logout', "app_logout")]
    public function logout()
    {
        if ($this->isLogin()) {
            session_destroy();
        }
        $this->response()->redirect("app_home");
    }

}