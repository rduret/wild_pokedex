<?php

namespace App\Model;

class PokemonManager extends AbstractManager
{
    public const TABLE = 'Pokemon';

    /**
     * Insert new pokemon in database
     */
    public function insert(array $pokemonValues): int
    {
        //Add pokemon into pokemon table
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`name`, `image`) VALUES (:name, :image)");
        $statement->bindValue('name', $pokemonValues['name'], \PDO::PARAM_STR);
        $statement->bindValue('image', $pokemonValues['filePath'], \PDO::PARAM_STR);
        $statement->execute();

        $idPokemon = (int)$this->pdo->lastInsertId();

        //Add pokemon and type into pokemon_type table
        for ($i = 1; $i <= 2; $i++) {
            if (isset($pokemonValues['type' . $i]) && trim($pokemonValues['type' . $i]) !== "") {
                $statement = $this->pdo->prepare("INSERT INTO Pokemon_Type (`pokemon_id`, `type_id`) VALUES (:pokemon_id, :type_id)");
                $statement->bindValue('pokemon_id', $idPokemon, \PDO::PARAM_INT);
                $statement->bindValue('type_id', $pokemonValues['type' . $i], \PDO::PARAM_INT);
                $statement->execute();
            }
        }

        //Add pokemon and attack into pokemon_attack table
        for ($i = 1; $i <= 4; $i++) {
            if (isset($pokemonValues['attack' . $i]) && trim($pokemonValues['attack' . $i]) !== "") {
                $statement = $this->pdo->prepare("INSERT INTO Pokemon_Attack (`pokemon_id`, `attack_id`) VALUES (:pokemon_id, :attack_id)");
                $statement->bindValue('pokemon_id', $idPokemon, \PDO::PARAM_INT);
                $statement->bindValue('attack_id', $pokemonValues['attack' . $i], \PDO::PARAM_INT);
                $statement->execute();
            }
        }

        return $idPokemon;
    }

    /**
     * Update pokemon in database
     */
/*     public function update(array $item): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `title` = :title WHERE id=:id");
        $statement->bindValue('id', $item['id'], \PDO::PARAM_INT);
        $statement->bindValue('title', $item['title'], \PDO::PARAM_STR);

        return $statement->execute();
    } */
}
