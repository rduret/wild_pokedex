<?php

/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\PokemonManager;

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

    public function __construct()
    {
        parent::__construct();
        $this->pokemonManager = new PokemonManager;
    }
    
    public function index()
    {
        $pokemons = $this->pokemonManager->selectAllWithAttackTypes();
        return $this->twig->render('Home/index.html.twig',['pokemons' => $pokemons]);
    }
}
