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
        $this->checkLogin();

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
        $this->checkLogin();

        $teams = $this->teamManager->selectAll();
    }

    /**
     * Add a pokemon to a team
     */

/*     public function addPokemon($pokemonId)
    {
        $this->checkLogin();

        //Get the team_id from a user id (returns an array, but we expect only one element)
        $teamIdRequest = $this->userManager->selectTeamIdByUserId($_SESSION['userId']);
        $pokemons = $this->pokemonManager->selectAllWithAttackTypes(); //Get the pokemons in DB
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
    } */

    public function addPokemon($pokemonId){
        $messages = [];
        $teamId = intval($this->userManager->selectTeamIdByUserId($_SESSION['userId']));
        $pokemons = $this->pokemonManager->selectAllWithAttackTypes(); //Get the pokemons in DB
        $nbPokemons = intval($this->teamManager->countPokemonsInTeam($teamId));

        if(isset($teamId)){
            foreach ($pokemons as $pokemon) {
                if ($pokemon['id'] === $pokemonId) {
                    $pokemonName = $pokemon['name'];
                    $pokemonExist = true;
                }
            }
            if(!$pokemonExist){
                $messages[] = "The selected pokemon does not exist. Please select a correct value.";
            }
        } else {
            $messages[] = "The selected team does not exist.";
        }

        if ($nbPokemons < 6) {
            if (empty($messages)) {
                $rowCount = $this->teamManager->addPokemonToTeam($pokemonId, $teamId);
                $messages[] = $rowCount == 1 ? $pokemonName . ' has been added to your team !' : 'An error has occured!';
                $_SESSION['add_pokTeam_msg'] = $messages;
                header('Location: /');
            }
        }
        else{
            $messages[] = "Team is full, please delete a pokemon first";
            $_SESSION['add_pokTeam_msg'] = $messages;
            header('Location: /');   
        }
    }

    /**
     * Delete pokemon from a team
     */

    public function deletePokemon($pokemonId)
    {
        $this->checkLogin();

        //Get the team_id from a user id (returns an array, but we expect only one element)
        $teamId = intval($this->userManager->selectTeamIdByUserId($_SESSION['userId']));
        $pokemons = $this->pokemonManager->selectAllWithAttackTypes(); //Get the pokemons in DB
        $messages = [];

        if (isset($teamId)){
            foreach ($pokemons as $pokemon) {
                if ($pokemon['id'] === $pokemonId) {
                    $pokemonName = $pokemon['name'];
                    $pokemonExist = true;
                }
            }
            if(!$pokemonExist){
                $messages[] = "The selected pokemon does not exist in your team. Please select a correct value.";
            }
        } else {
            $messages[] = "The selected team does not exist. Please select a correct value.";
        }



        if(empty($messages)){
            // Getting the rowCount value which is returned at the end of deletePokemonFromTeam function
            $rowCount = $this->teamManager->deletePokemonFromTeam($pokemonId, $teamId);
            $messages[] = $rowCount == 1 ? $pokemonName . ' has been removed from the team' : 'An error has occured';
            $_SESSION['dlt_pokTeam_msg'] = $messages;
            header('Location: /');
        }
        else{
            $_SESSION['dlt_pokTeam_msg'] = $messages;
            header('Location: /');
        }
    }

    /**
     * Get all pokemons within a team (team_id)
     */
    public function listPokemonTeam(int $teamId)
    {
        $this->checkLogin();

        return $this->teamManager->selectPokemonsInTeam($teamId);
    }

    /**
     * Check if the user is logged in
     */
    private function checkLogin()
    {
        //Redirect to LOGIN if we are not logged in
        if (!isset($_SESSION['username'])) {
            header('Location: /Auth/login');
        }
    }
}
