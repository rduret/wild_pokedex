<?php

namespace App\Controller;

use App\Model\UserManager;
use App\Model\TeamManager;
use App\Model\PokemonManager;
use Exception;

class UserController extends AbstractController
{
    private UserManager $userManager;
    private TeamManager $teamManager;
    private PokemonManager $pokemonManager;

    public function __construct()
    {
        parent::__construct();
        $this->userManager = new UserManager();
        $this->teamManager = new TeamManager();
        $this->pokemonManager = new PokemonManager();
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
