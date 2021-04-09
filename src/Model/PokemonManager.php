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
        $query = 'SELECT Type.name, Type.id 
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
        $query = 'SELECT Attack.id id, Attack.name name, Type.name type 
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

        //we add every types into the pokemon types array
        foreach ($pokemonTypes as $type) {
            $pokemon['types'][] = ["id" => $type['id'], "name" => $type["name"]];
        }

        //we add every attacks into the pokemon attacks array
        foreach ($pokemonAttacks as $attack) {
            $pokemon['attacks'][] = ["id" => $attack['id'], "name" => $attack['name'], "type" => $attack["type"]];
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
     * Update type to pokemon with IDs
     */
    public function updatePokemonType(int $pokemonId, int $typeId, int $oldTypeId){
        $statement = $this->pdo->prepare("UPDATE Pokemon_Type SET `type_id`=:type_id WHERE pokemon_id=:pokemon_id AND type_id=:oldType_id");
        $statement->bindValue('pokemon_id', $pokemonId, \PDO::PARAM_INT);
        $statement->bindValue('type_id', $typeId, \PDO::PARAM_INT);
        $statement->bindValue('oldType_id', $oldTypeId, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Delete type to pokemon with IDs
     */
    public function deletePokemonType(int $pokemonId, int $typeId){
        $statement = $this->pdo->prepare("DELETE FROM Pokemon_Type WHERE pokemon_id=:pokemon_id AND type_id=:type_id");
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

    
    /**
     * Update attack to pokemon with IDs
     */
    public function updatePokemonAttack(int $pokemonId, int $attackId, int $oldAttackId){
        $statement = $this->pdo->prepare("UPDATE Pokemon_Attack SET `attack_id`=:attack_id WHERE pokemon_id=:pokemon_id AND attack_id=:oldAttack_id");
        $statement->bindValue('pokemon_id', $pokemonId, \PDO::PARAM_INT);
        $statement->bindValue('attack_id', $attackId, \PDO::PARAM_INT);
        $statement->bindValue('oldAttack_id', $oldAttackId, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
    * Delete attack to pokemon with IDs
    */
   public function deletePokemonAttack(int $pokemonId, int $attackId){
       $statement = $this->pdo->prepare("DELETE FROM Pokemon_Attack WHERE pokemon_id=:pokemon_id AND attack_id=:attack_id");
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

     /**
     * Update pokemon in database
     */
    public function updatePokemon($newPokemon, $oldPokemon)
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET name = :name, image = :image, model3d = :model3d WHERE id = :id");
        $statement->bindValue('name', $newPokemon['name'], \PDO::PARAM_STR);
        $statement->bindValue('image', $newPokemon['image'], \PDO::PARAM_STR);
        $statement->bindValue('model3d', $newPokemon['model3d'], \PDO::PARAM_STR);
        $statement->bindValue('id', $oldPokemon['id'], \PDO::PARAM_INT);
        $statement->execute();

        for ($i = 1; $i <= 2; $i++) {
            if(array_key_exists('types', $oldPokemon)){
                if ($newPokemon['type'.$i] !== "" && $i>count($oldPokemon['types'])) {
                    $this->addTypeToPokemon($newPokemon['type'.$i], $oldPokemon['id']);
                } elseif ($newPokemon['type'.$i] !== ""){
                    $statement = $this->pdo->prepare("UPDATE Pokemon_Type SET `type_id` = :type_id
                    WHERE pokemon_id=:pokemon_id AND type_id=:oldType_id");
                    $statement->bindValue('pokemon_id', $oldPokemon['id'], \PDO::PARAM_INT);
                    $statement->bindValue('type_id', $newPokemon['type' . $i], \PDO::PARAM_INT);
                    $statement->bindValue('oldType_id', $oldPokemon['types'][$i-1]['id'], \PDO::PARAM_INT);
                    $statement->execute();
                }
            }
            else{
                $this->addTypeToPokemon($newPokemon['type'.$i], $oldPokemon['id']);
            }
        }

        for ($i = 1; $i <= 4; $i++) {
            if (array_key_exists('attacks', $oldPokemon)) {
                if ($newPokemon['attack'.$i] !== "" && $i>count($oldPokemon['attacks'])) {
                    $this->addAttackToPokemon($newPokemon['attack'.$i], $oldPokemon['id']);
                } elseif ($newPokemon['attack'.$i] !== "") {
                    $statement = $this->pdo->prepare("UPDATE Pokemon_Attack SET `attack_id` = :attack_id 
                    WHERE pokemon_id=:pokemon_id AND attack_id=:oldAttack_id");
                    $statement->bindValue('pokemon_id', $oldPokemon['id'], \PDO::PARAM_INT);
                    $statement->bindValue('attack_id', $newPokemon['attack' . $i], \PDO::PARAM_INT);
                    $statement->bindValue('oldAttack_id', $oldPokemon['attacks'][$i-1]['id'], \PDO::PARAM_INT);
                    $statement->execute();
                }
            }
            else{
                $this->addAttackToPokemon($newPokemon['attack'.$i], $oldPokemon['id']);
            }
        }

        return $oldPokemon['id'];
    }
}
