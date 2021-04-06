<?php

namespace App\Controller;

use App\Model\UserManager;
use App\Model\TeamManager;
use App\Model\PokemonManager;

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

    /**
     * Delete a trainer
     */
    public function delete(int $id)
    {
        // Getting the rowCount value which is returned at the end of deleteTrainerById function
        $rowCount = $this->userManager->deleteTrainerById($id);
        $validationMessage = $rowCount == 1 ? 'Le trainer a bien été supprimé!' : 'erreur!';
        // créer une variable de session $_SESSION['dlt_trainer_msg']
        $_SESSION['dlt_trainer_msg'] = $validationMessage;
        header('Location: /User/list');
        //
    }

    /**
     * Add a pokemon to a team
     */

    public function addPokemon()
    {
        $teamId = $this->userManager->selectTeamIdByUserId($_SESSION['id']);
        $pokemons = $this->pokemonManager->selectAllWithAttackTypes();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            $pokemonId = $_POST['pokemon_id'];

            //Checking if pokemon exist in database if not null
            $pokemon_exist = false;
            foreach ($pokemons as $pokemon) {
                if ($pokemon['id'] === $pokemonId) {
                    $pokemon_exist = true;
                }
            }

            if (!$pokemon_exist && trim($pokemonId) !== "") {
                $errors[] = "The selected pokemon does not exist. Please select a correct value.";
            } else {
                $this->teamManager->addPokemonToTeam($pokemonId, $teamId[0]);
            }
        }
    }

    /**
     * Delete pokemon from a team
     */

    public function deletePokemon($pokemonId, $teamId)
    {
        $teamId = $_SESSION['id'];
        $pokemonId = $_POST['pokemon_id'];
        // Getting the rowCount value which is returned at the end of deletePokemonFromTeam function
        $rowCount = $this->teamManager->deletePokemonFromTeam($pokemonId, $teamId);
        $validationMessage = $rowCount == 1 ? 'Le pokémon a bien été retiré de l\'équipe!' : 'erreur!';
        // créer une variable de session $_SESSION['dlt_pokTeam_msg']
        $_SESSION['dlt_pokTeam_msg'] = $validationMessage;
        header('Location: /Pokemon/list');
        //
    }
}
