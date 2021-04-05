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

    public function selectAllWithAttackTypes()
    {
        $pokemons = [];

        //We get all pokemons and stock them inside $pokemons with their id as key
        $results = $this->selectAll();
        foreach ($results as $result) {
            $pokemons[$result['id']] = ['name' => $result['name'], 'image' => $result['image']];
        }

        //For each pokemon we get their types id and types name
        foreach ($pokemons as $id => $pokemon) {
            //Get the types ID
            $query = 'SELECT type_id FROM Pokemon_Type WHERE pokemon_id = ' . $id;
            $types = $this->pdo->query($query)->fetchAll();
            $typesName = [];
            //For each type id we get its type name and add it to the pokemon
            foreach ($types as $type) {
                $query = 'SELECT name FROM Type WHERE id = ' . $type['type_id'];
                $typesName = $this->pdo->query($query)->fetch();

                //Add types to current pokemon
                $pokemons[$id]['types'][] = $typesName['name'];
            }
        }

        //Same for attacks
        foreach ($pokemons as $id => $pokemon) {
            $query = 'SELECT attack_id FROM Pokemon_Attack WHERE pokemon_id = ' . $id;
            $attacks = $this->pdo->query($query)->fetchAll();
            $attacksName = [];
            foreach ($attacks as $attack) {
                $query = 'SELECT name FROM Attack WHERE id = ' . $attack['attack_id'];
                $attacksName = $this->pdo->query($query)->fetch();
                $pokemons[$id]['attacks'][] = $attacksName['name'];
            }
        }
        return $pokemons;
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
