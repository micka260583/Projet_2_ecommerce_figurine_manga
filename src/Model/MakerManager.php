<?php

namespace App\Model;

use PDO;

class MakerManager extends AbstractManager
{
    public const TABLE = 'maker';

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function insert(string $makerName): bool
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (name) VALUES (:name)");
        $statement->bindValue('name', $makerName, PDO::PARAM_STR);
        return $statement->execute();
    }

    public function update(array $maker): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET name = :name WHERE id = :id");
        $statement->bindValue('id', $maker['id'], PDO::PARAM_STR);
        $statement->bindValue('name', $maker['name'], PDO::PARAM_STR);
        return $statement->execute();
    }
}
