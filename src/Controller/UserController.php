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
     * List all trainers and their pokemons
     */
    public function list()
    {
        $trainers = $this->userManager->selectByRole(2);
        $pokemonsInTeam = [];


        //Loop on trainers to get their pokemons
        foreach ($trainers as $index => $trainer) {
            $trainer['pokemons'] = [];
            $teamId = $trainer['team_id'];
            //Store pokemon_id from each pokemon in team
            $pokemonsInTeam = $this->listPokemonTeam($teamId);



            //Loop on pokemons in team to store their names into $trainers
            foreach ($pokemonsInTeam as $key => $pokemon) {
                $pokemonNameRequest = $this->pokemonManager->selectPokemonNameById($pokemon['pokemon_id']);
                //Keep only the string value

                $pokemonName = $pokemonNameRequest[0]['name'];
                //Store the each string within $trainer with the index 'pokemons'
                array_push($trainer['pokemons'], $pokemonName);
            }
            $trainers[$index] = $trainer;
        }
        // var_dump($trainers);
        // exit();
        return $this->twig->render('Trainers/list.html.twig', ['trainers' => $trainers, 'session' => $_SESSION]);
    }

    /**
     * Delete a trainer
     */
    public function delete(int $id)
    {
        if (isset($_SESSION['username']) && $_SESSION['userRole'] == "admin") {
            // Getting the rowCount value which is returned at the end of deleteTrainerById function
            $rowCount = $this->userManager->deleteTrainerById($id);
            $validationMessage = $rowCount == 1 ? 'Le trainer a bien été supprimé!' : 'erreur!';
            // créer une variable de session $_SESSION['dlt_trainer_msg']
            $_SESSION['dlt_trainer_msg'] = $validationMessage;
            header('Location: /User/list');
        } else {
            header('Location: /');
        }
    }

    /**
     * List all teams with at least one pokemon
     */
    public function listTeams()
    {
        $teams = $this->teamManager->selectAll();
    }

    /**
     * Add a pokemon to a team
     */

    public function addPokemon($pokemonId)
    {
        //Get the team_id from a user id (returns an array, but we expect only one element)
        $teamIdRequest = $this->userManager->selectTeamIdByUserId($_SESSION['userId']);
        $pokemons = $this->pokemonManager->selectAllWithAttackTypes();//Get the pokemons in DB
        $teamId = null;
        $errors = [];

        //If the 1st element within the array is not null,
        //then assign the content to $teamID
        if ($teamIdRequest[0]) {
            $teamId = intval($teamIdRequest[0]); //Convert the string value stored in $_SESSION to int
        } else {
            $errors[] = "The selected team does not exist. Please select a correct value.";
        }

        //Checking if pokemon exist in database if not null
        $pokemonExist = false;
        foreach ($pokemons as $pokemon) {
            if ($pokemon['id'] === $pokemonId) {
                $pokemonExist = true;
            } else {
                $errors[] = "The selected pokemon does not exist. Please select a correct value.";
            }
        }

        if ($pokemonExist && !$errors) {
            $this->teamManager->addPokemonToTeam($pokemonId, $teamId);
        } else {
            foreach ($errors as $error) {
                echo $error;
            }
        }
    }

    /**
     * Delete pokemon from a team
     */

    public function deletePokemon($pokemonId)
    {
        //Get the team_id from a user id (returns an array, but we expect only one element)
        $teamIdRequest = $this->userManager->selectTeamIdByUserId($_SESSION['userId']);
        $teamId = null;
        $errors = [];

        if ($teamIdRequest[0]) {
            $teamId = intval($teamIdRequest[0]); //Convert the string value stored in $_SESSION to int
        } else {
            $errors[] = "The selected team does not exist. Please select a correct value.";
        }

        // Getting the rowCount value which is returned at the end of deletePokemonFromTeam function
        $rowCount = $this->teamManager->deletePokemonFromTeam($pokemonId, $teamId);
        $validationMessage = $rowCount == 1 ? 'Le pokémon a bien été retiré de l\'équipe!' : 'erreur!';
        // créer une variable de session $_SESSION['dlt_pokTeam_msg']
        $_SESSION['dlt_pokTeam_msg'] = $validationMessage;
        header('Location: /Pokemon/list');
        //
    }

    /**
     * Get all pokemons within a team (team_id)
     */
    public function listPokemonTeam(int $teamId)
    {
        return $this->teamManager->selectPokemonsInTeam($teamId);
    }
}
