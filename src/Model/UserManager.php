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
}
