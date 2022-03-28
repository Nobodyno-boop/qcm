<?php

namespace App\Controller;

use App\Model\User;
use Vroom\Controller\AbstractController;
use Vroom\Router\Decorator\Route;
use Vroom\Router\Request;

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
                        $this->response()->json(['ok']);
                    } else {
                        $this->response()->json(['Wrong credential']);
                    }
                } else $this->response()->json(['Wrong credential']);
            } else $this->response()->json(["message" => "wrong access"]);
        }
    }
    #[Route("/register")]
    public function register()
    {
        if($this->isLogin()){

        }

        $this->renderView("security/register");
    }

    #[Route("/register", methods: ['POST'])]
    public function registerPost(Request $r)
    {
        $token = $r->post('crsf');
        $email = $r->post("email");
        $passord = $r->post("password");

        if($this->matchToken($token)){
            $user = new User();
            $user->setEmail($email);
            $user->setPassword(password_hash($passord, PASSWORD_DEFAULT));
            $user->setUsername("Nobody");
            $user->save();

            $this->response()->json(["message" => "ok"]);
        }

    }

}