<?php

namespace App\Controller;

use App\Model\UserModel;
use Vroom\Controller\AbstractController;
use Vroom\Orm\Sql\QueryBuilder;
use Vroom\Router\Decorator\Route;
use Vroom\Router\Request;

class UserController extends AbstractController
{
    #[Route("/user/", methods: ['POST'])]
    public function index()
    {
        dump($this->getRequest());
    }
    #[Route("/user/:id")]
    public function getUser(Request $request, $id)
    {
        $user = $this->repository(UserModel::class)->get($id);

        dump($user);
    }

    #[Route("/")]
    public function login(){
        echo /** HTML */'<form id="form" action="/user" method="post">
                <input type="email" name="email" id="email"><br>
                <input type="password" name="password" id="password"><br>
                <button type="submit">Login </button>
            </form>
            
             <script>
                    document.getElementById("form").addEventListener(("submit"), (e) => {
                        e.preventDefault()
                        
                        let email = document.getElementById("email").value
                        let password = document.getElementById("password").value
                           
                        fetch("/user/", {
                            method: "POST",
                            headers: {
                                "Content-type": "application/json"
                            },
                            body: JSON.stringify({email:email, password:password})
                        }).catch(e => console.log(e)).then(x => x.text()).then(console.log)
                    })
             
                </script>
            
            ';
    }
}