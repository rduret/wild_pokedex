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
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`name`, `image`, `model3d`) VALUES (:name, :image, :model3d)");
        $statement->bindValue('name', $pokemonValues['name'], \PDO::PARAM_STR);
        $statement->bindValue('image', $pokemonValues['filePath'], \PDO::PARAM_STR);
        $statement->bindValue('model3d', $pokemonValues['modelPath'], \PDO::PARAM_STR);
        $statement->execute();

        $idPokemon = (int)$this->pdo->lastInsertId();

        //Add pokemon and type into pokemon_type table
        for ($i = 1; $i <= 2; $i++) {
            if (isset($pokemonValues['type' . $i]) && trim($pokemonValues['type' . $i]) !== "") {
                $this->addTypeToPokemon($pokemonValues['type' . $i], $idPokemon);
            }
        }

        //Add pokemon and attack into pokemon_attack table
        for ($i = 1; $i <= 4; $i++) {
            if (isset($pokemonValues['attack' . $i]) && trim($pokemonValues['attack' . $i]) !== "") {
                $this->addAttackToPokemon($pokemonValues['attack' . $i], $idPokemon);
            }
        }
        return $idPokemon;
    }
    /**
     * Select pokemon's types by id in database
     */
    public function selectPokemonTypesById($id)
    {
        $query = 'SELECT Type.name 
        FROM Type JOIN Pokemon_Type ON Type.id = type_id
        JOIN Pokemon ON pokemon_id = Pokemon.id 
        WHERE pokemon_id = ' . $id;
        return $this->pdo->query($query)->fetchAll();
    }

    /**
     * Select pokemon's attacks by id in database
     */
    public function selectPokemonAttacksById($id)
    {
        $query = 'SELECT Attack.name name, Type.name type
        FROM Attack JOIN Pokemon_Attack ON Attack.id = attack_id
        JOIN Pokemon ON pokemon_id = Pokemon.id 
        JOIN Type ON Type.id = Attack.type_id
        WHERE pokemon_id = ' . $id;
        return $this->pdo->query($query)->fetchAll();
    }


    public function selectTypeByAttackId($id)
    {
        $query = 'SELECT Type.name FROM Type 
        JOIN Attack ON Attack.type_id = Type.id
        WHERE Attack.id = ' . $id;
        return $this->pdo->query($query)->fetch();
    }
    
    /**
     * Select on pokemon by id with his attacks and types
     */
    public function selectOneByIdWithAttackTypes($id)
    {
        //We get the pokemon, attacks and types
        $pokemon = $this->selectOneById($id);
        $pokemonAttacks = $this->selectPokemonAttacksById($id);
        $pokemonTypes = $this->selectPokemonTypesById($id);

        //we had every types into the pokemon types array
        foreach ($pokemonTypes as $type) {
            $pokemon['types'][] = $type['name'];
        }

        //we had every attacks into the pokemon attacks array
        foreach ($pokemonAttacks as $attack) {
            $pokemon['attacks'][] = ["name" => $attack['name'], "type" => $attack["type"]];
        }

        return $pokemon;
    }

    /**
     * Select all pokemons with their attacks and types
     */
    public function selectAllWithAttackTypes()
    {
        $pokemons = [];

        //Get all pokemon in Pokemon table
        $results = $this->selectAll();
        //For each pokemon id, we get pokemon with attacks and types and store it into $pokemons
        foreach ($results as $result) {
            $pokemons[] = $this->selectOneByIdWithAttackTypes($result['id']);
        }

        return $pokemons;
    }

    /**
     * Add type to pokemon with IDs
     */
    public function addTypeToPokemon($typeId, $pokemonId)
    {
        $statement = $this->pdo->prepare("INSERT INTO Pokemon_Type (`pokemon_id`, `type_id`) VALUES (:pokemon_id, :type_id)");
        $statement->bindValue('pokemon_id', $pokemonId, \PDO::PARAM_INT);
        $statement->bindValue('type_id', $typeId, \PDO::PARAM_INT);
        $statement->execute();
    }

    
    /**
     * Add type to pokemon with IDs
     */
    public function addAttackToPokemon($attackId, $pokemonId)
    {
        $statement = $this->pdo->prepare("INSERT INTO Pokemon_Attack (`pokemon_id`, `attack_id`) VALUES (:pokemon_id, :attack_id)");
        $statement->bindValue('pokemon_id', $pokemonId, \PDO::PARAM_INT);
        $statement->bindValue('attack_id', $attackId, \PDO::PARAM_INT);
        $statement->execute();
    }
    /*     Delete Pokemon from list */
    public function deletePokemonFromList(int $id)
    {
        $query = 'DELETE FROM Pokemon WHERE id = :id';
        // :id to bind with bind value
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
        // returns lines which have been affected by an INSERT, UPDATE or DELETE
        return $statement->rowCount();
    }

    public function selectPokemonNameById(int $id)
    {
        $statement = $this->pdo->prepare("SELECT name FROM " . static::TABLE . " WHERE id = :id");
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
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
