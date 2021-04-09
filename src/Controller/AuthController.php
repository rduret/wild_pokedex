<?php

namespace App\Controller;

use App\Model\RoleManager;
use App\Model\UserManager;

class AuthController extends AbstractController
{
    private UserManager $userManager;
    private RoleManager $roleManager;

    public function __construct()
    {
        parent::__construct();
        $this->userManager = new UserManager;
        $this->roleManager = new RoleManager;
    }

    public function login(){

        $errors = [];

        //if connected redirect to home page (/)
        if(isset($_SESSION['username'])){
            header('Location: /');
        }elseif($_SERVER['REQUEST_METHOD'] === 'POST'){           //else if form has been submited, do check validation, if user exist connect him
            //Checking if name is set and not too long
            if (!isset($_POST['username']) || trim($_POST['username']) == '') {
                $errors[] = "Username empty.";
            } elseif (strlen($_POST['username']) > 50) {
                $errors[] = "Username must not be longer than 50 characters";
            }

            //Checking if password is set and not too long
            if (!isset($_POST['password']) || trim($_POST['password']) == '') {
                $errors[] = "Password empty.";
            } elseif (strlen($_POST['password']) > 50) {
                $errors[] = "Your password must not be longer than 50 characters";
            }

            //Checking if user exist in database if checkvalidation is ok
            if(empty($errors)){
                $user = $this->userManager->selectByUsernamePassword($_POST['username'], $_POST['password']);
                if($user){
                    $role = $this->roleManager->selectOneById($user['role_id']);
                    $_SESSION['username'] = $_POST['username'];   
                    $_SESSION['userRole'] = $role['role'];
                    $_SESSION['userId'] = $user['id'];
                    header('Location: /');
                }else{
                    $errors[] = "Username does not exist or wrong password";
                }
            }
        }
        return $this->twig->render('Auth/login.html.twig', ['errorMessages' => $errors]);
    }

    public function logout()
    {
        session_destroy();
        header('Location: /');
    }
}
