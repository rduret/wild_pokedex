<?php

namespace App\Controller;

use App\Model\UserManager;

class UserController extends AbstractController
{
    private UserManager $userManager;

    public function __construct()
    {
        parent::__construct();
        $this->userManager = new UserManager();
    }

    /**
     * List all trainers
     */
    public function list()
    {
        $trainers = $this->userManager->selectByRole(2);

        return $this->twig->render('User/list.html.twig', ['trainers' => $trainers]);
    }
}
