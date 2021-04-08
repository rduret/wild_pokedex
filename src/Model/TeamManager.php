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

        return $statement->rowCount();
    }

    /**
     * Delete Pokemon from team with IDs
     * @param int $pokemonId
     * @param int $teamId
     */
    public function deletePokemonFromTeam(int $pokemonId, int $teamId)
    {
        $query = "DELETE FROM Pokemon_Team WHERE pokemon_id = :pokemon_id AND team_id = :team_id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':pokemon_id', $pokemonId, \PDO::PARAM_INT);
        $statement->bindValue(':team_id', $teamId, \PDO::PARAM_INT);
        $statement->execute();
        // returns lines which have been affected by an INSERT, UPDATE or DELETE
        return $statement->rowCount();
    }

    /**
     * Select all pokemons within a team_id
     * @param int $teamId
     */
    public function selectPokemonsInTeam(int $teamId)
    {
        $statement = $this->pdo->prepare("SELECT pokemon_id FROM " . static::TABLE . " WHERE team_id = :team_id");
        $statement->bindValue(':team_id', $teamId, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function countPokemonsInTeam($teamId){
        $statement = $this->pdo->prepare("SELECT COUNT(*) FROM " . static::TABLE . " WHERE team_id = :team_id");
        $statement->bindValue(':team_id', $teamId, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchColumn();
    }
}
