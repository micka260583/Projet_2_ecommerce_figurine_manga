<?php

namespace App\Model;

use PDO;

class LicenseManager extends AbstractManager
{
    public const TABLE = 'license';

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function insert(string $licenseName): bool
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (name) VALUES (:name)");
        $statement->bindValue('name', $licenseName, PDO::PARAM_STR);
        return $statement->execute();
    }

    public function update(array $license): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET name = :name WHERE id = :id");
        $statement->bindValue('id', $license['id'], PDO::PARAM_STR);
        $statement->bindValue('name', $license['name'], PDO::PARAM_STR);
        return $statement->execute();
    }
}
