<?php

namespace App\Controller;

use App\Model\User;
use Vroom\Controller\AbstractController;
use Vroom\Router\Decorator\Route;
use Vroom\Router\Request;
use Vroom\Utils\Form;

class SecurityController extends AbstractController
{
    #[Route('/login', "app_login")]
    public function index()
    {
        if($this->isLogin()){
            // redirect
        }

        $this->renderView("security/login.twig");
    }

    #[Route("/login", methods: ['POST'])]
    public function loginPost(Request $r)
    {
        if(!$this->isLogin()){
            $token = $r->post('crsf');
            $email = $r->post("email");
            $passord = $r->post("password");

            if($this->matchToken($token)) {
                $user = $this->repository(User::class)->findBy("email", $email);
                if($user){
                    if(password_verify($passord, $user->getPassword())){
                        $this->addSession("user", $user);
                        $this->response()->redirect("app_home");
                    } else {
                        $this->response()->json(['Wrong credential']);
                    }
                } else $this->response()->json(['Wrong credential']);
            } else $this->response()->json(["message" => "wrong access"]);
        }
    }

    #[Route("/register", "app_register", methods: ['GET', 'POST'])]
    public function register()
    {
        if ($this->isLogin()) {

        }
        $user = new User();
        $form = Form::new()
            ->add("user", Form::TYPE_MODEL, ["model" => User::class])
            ->add("reset", Form::TYPE_RESET)
            ->add("register !", Form::TYPE_SUBMIT);

        $form->handleRequest($this->getRequest());

        if ($form->isSent() && $form->isValid()) {

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