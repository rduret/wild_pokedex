<?php

namespace App\Model;

class TeamManager extends AbstractManager
{
    public const TABLE = 'Pokemon_Team';

    /**
     * Add pokemon to team with IDs
     * @param int $pokemonId
     * @param int $teamId
     */
    public function addPokemonToTeam(int $pokemonId, int $teamId)
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO " . self::TABLE . " (`pokemon_id`, `team_id`) VALUES (:pokemon_id, :team_id)"
        );
        $statement->bindValue('pokemon_id', $pokemonId, \PDO::PARAM_INT);
        $statement->bindValue('team_id', $teamId, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Delete Pokemon from team with IDs
     * @param int $pokemonId
     * @param int $teamId
     */
    public function deletePokemonFromTeam(int $pokemonId, int $teamId)
    {
        $query = "DELETE FROM " . self::TABLE . " WHERE pokemon_id = :pokemon_id AND team_id = :team_id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':pokemon_id', $pokemonId, \PDO::PARAM_INT);
        $statement->bindValue(':team_id', $teamId, \PDO::PARAM_INT);
        $statement->execute();
        // returns lines which have been affected by an INSERT, UPDATE or DELETE
        return $statement->rowCount();
    }
}
