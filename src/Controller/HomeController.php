<?php

/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\PokemonManager;
use App\Model\UserManager;
use App\Model\TeamManager;

class HomeController extends AbstractController
{
    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */

    private PokemonManager $pokemonManager;
    private UserManager $userManager;
    private TeamManager $teamManager;


    public function __construct()
    {
        parent::__construct();
        $this->pokemonManager = new PokemonManager;
        $this->userManager = new UserManager;
        $this->teamManager = new TeamManager;
    }

    public function index()
    {
        $trainers = $this->userManager->selectSomeUsers();
        $pokemons = $this->pokemonManager->selectAllWithAttackTypes();
        return $this->twig->render('Home/index.html.twig', ['pokemons' => $pokemons, 'session' => $_SESSION, 'trainers' => $trainers]);
        $pokemonsId = "";
        if (isset($_SESSION['userId'])) {
            $pokemonsId = $this->listPokemonTeamByUser();
        }
        $pokemons = $this->pokemonManager->selectAllWithAttackTypes();
        return $this->twig->render('Home/index.html.twig', ['pokemons' => $pokemons, 'session' => $_SESSION, 'pokemonsId' => $pokemonsId]);
    }

    /**
     * Get list of pokemons for a trainer team
     */
    public function listPokemonTeamByUser()
    {
        //Get the team_id from a user id (returns an array, but we expect only one element)
        $teamIdRequest = $this->userManager->selectTeamIdByUserId($_SESSION['userId']);
        $teamId = null;
        $errors = [];
        $pokemonsId = [];

        if ($teamIdRequest[0]) {
            $teamId = intval($teamIdRequest[0]['team_id']); //Convert the string value stored in $_SESSION to int
        } else {
            $errors[] = "The selected team does not exist. Please select a correct value.";
        }
        $pokemonsInTeam = $this->teamManager->selectPokemonsInTeam($teamId);
        foreach ($pokemonsInTeam as $pokemonsArray) {
            foreach ($pokemonsArray as $pokemon) {
                array_push($pokemonsId, $pokemon);
            }
        }
        return $pokemonsId;
    }
}
