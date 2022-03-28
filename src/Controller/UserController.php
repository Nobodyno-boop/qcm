<?php

namespace App\Controller;

use App\Model\User;
use Vroom\Controller\AbstractController;
use Vroom\Router\Decorator\Route;
use Vroom\Router\Request;

class UserController extends AbstractController
{
    #[Route("/user/", methods: ['POST'])]
    public function index(Request $r)
    {
        if($this->matchToken($r->getBody()->token)){
            $this->response()->json(["dance" => "singe"]);
        }
    }

    #[Route("/user/{id}/")]
    public function getUser(Request $request, $id)
    {
        $user = $this->repository(User::class)->get($id);
        if($user != null){
            $this->response()->json($user);
        } else {
            $this->response()->json();
        }
    }

//    #[Route("/login")]
    public function login(){
//        $token = $this->getToken();
//            $this->renderView('user/login.twig');
//        echo /** HTML */'<form id="form" action="/user" method="post">
//                 <input type="hidden" name="crsf" id="crsf_token" value="'.$token.'" >
//                <input type="email" name="email" id="email"><br>
//                <input type="password" name="password" id="password"><br>
//                <button type="submit">Login </button>
//            </form>
//
//             <script>
//                    document.getElementById("form").addEventListener(("submit"), (e) => {
//                        e.preventDefault()
//
//                        let email = document.getElementById("email").value
//                        let password = document.getElementById("password").value
//                        let token = document.getElementById("crsf_token").value;
//                        fetch("/user/", {
//                            method: "POST",
//                            headers: {
//                                "Content-Type": "application/json"
//                            },
//                            body: JSON.stringify({email:email, password:password, token})
//                        }).catch(e => console.log(e)).then(x => x.text()).then(console.log)
//                    })
//
//                </script>

//            ';
    }
}