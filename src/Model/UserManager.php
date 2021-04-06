<?php

namespace App\Model;

class UserManager extends AbstractManager
{
    public const TABLE = 'User';

    public function selectByUsernamePassword(string $name, string $password)
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE name = :name AND password = :password");
        $statement->bindValue('name', $name, \PDO::PARAM_STR);
        $statement->bindValue('password', $password, \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }


    /**
     * Add pokemon to team with IDs
     * @param $pokemonId
     * @param $teamId
     */
    public function addPokemonToTeam(int $pokemonId, int $teamId)
    {
        $statement = $this->pdo->prepare("INSERT INTO Pokemon_Team (`pokemon_id`, `team_id`) VALUES (:pokemon_id, :team_id)");
        $statement->bindValue('pokemon_id', $pokemonId, \PDO::PARAM_INT);
        $statement->bindValue('team_id', $teamId, \PDO::PARAM_INT);
        $statement->execute();
    }


    /**
     * Delete Pokemon from team with IDs
     * @param $pokemonId
     * @param $teamId
     */
    public function deletePokemonFromTeam(int $pokemonId, int $teamId)
    {
        $query = 'DELETE FROM Pokemon_Team WHERE pokemon_id = :pokemon_id AND team_id = :team_id';
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':pokemon_id', $pokemonId, \PDO::PARAM_INT);
        $statement->bindValue(':team_id', $teamId, \PDO::PARAM_INT);
        $statement->execute();
        // returns lines which have been affected by an INSERT, UPDATE or DELETE
        return $statement->rowCount();
    }
}
