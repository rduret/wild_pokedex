<?php

namespace App\Model;

class UserManager extends AbstractManager
{
    public const TABLE = 'User';

    public function selectByUsernamePassword(string $name, string $password)
    {
        // prepared request
        $statement = $this->pdo->prepare(
            "SELECT * FROM " . static::TABLE . " WHERE name = :name AND password = :password"
        );
        $statement->bindValue('name', $name, \PDO::PARAM_STR);
        $statement->bindValue('password', $password, \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }

    /**
     * Select all users with trainer role
     */

    public function selectByRole(int $roleId)
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE role_id =:roleId");
        $statement->bindValue('roleId', $roleId, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /*     Delete trainer */
    public function deleteTrainerById(int $id)
    {
        $query = 'DELETE FROM User WHERE id = :id';
        // :id to bind with bind value
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
        // returns lines which have been affected by an INSERT, UPDATE or DELETE
        return $statement->rowCount();
    }


    /**
     * Get user's team_id by its own id
     * @param int $userId
     */
    public function selectTeamIdByUserId(int $userId)
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT team_id FROM " . static::TABLE . " WHERE id =:userId");
        $statement->bindValue('userId', $userId, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch();
    }

    public function selectSomeUsers()
    {
        $statement = $this->pdo->prepare("SELECT name FROM " . static::TABLE . " WHERE role_id = 2  LIMIT 5");
        $statement->execute();
        return $statement->fetchAll();
    }
}
