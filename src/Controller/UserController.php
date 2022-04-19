<?php

namespace App\Controller;

use App\Model\User;
use Vroom\Controller\AbstractController;
use Vroom\Router\Decorator\Route;
use Vroom\Router\Request;
use Vroom\Utils\Form;

class UserController extends AbstractController
{
    #[Route("/profile/see/{username}", "app_user_profile_username")]
    public function profileUser(Request $request, $username)
    {
        $this->renderView("user/profile_username", ["username" => $username]);
    }

    #[Route("/profile/", "app_user_profile")]
    public function profile(Request $request)
    {
        $this->renderView("user/profile");
    }

    #[Route("/profile/edit", "app_user_editprofile", methods: ['GET', "POST"])]
    public function editProfile(Request $request)
    {
        if(!$this->isLogin()) {
            $this->response()->redirect("app_home");
        }
        $user = User::find($this->getSession("user")['id']);

        $form = Form::new()
            ->add("currentpassword", Form::TYPE_PASSWORD)
            ->add("newpassword", Form::TYPE_PASSWORD)
            ->add("secondpassword", Form::TYPE_PASSWORD)
            ->add("Update", Form::TYPE_SUBMIT);

        $form->handleRequest($request);

        if($form->isSent() && $form->isValid()){
            $data = $form->getReceiveData();
            $cpass = $data->get("currentpassword");

            if($cpass){
                if(password_verify($cpass, $user->getPassword())){
                    if($data->get('newpassword') === $data->get("secondpassword")){
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


//    #[Route("/login")]
    public function login()
    {
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