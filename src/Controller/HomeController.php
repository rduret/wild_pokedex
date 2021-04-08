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

    public function __construct()
    {
        parent::__construct();
        $this->pokemonManager = new PokemonManager;
        $this->userManager = new UserManager;
    }

    public function index()
    {
        $trainers = $this->userManager->selectSomeUsers();
        $pokemons = $this->pokemonManager->selectAllWithAttackTypes();
        return $this->twig->render('Home/index.html.twig', ['pokemons' => $pokemons, 'session' => $_SESSION, 'trainers' => $trainers]);
    }
}
