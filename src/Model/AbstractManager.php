<?php

/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 07/03/18
 * Time: 20:52
 * PHP version 7
 */

namespace App\Model;

use App\Model\Connection;
use PDO;

/**
 * Abstract class handling default manager.
 */
abstract class AbstractManager
{
    protected PDO $pdo; //variable de connexion

    protected string $table;

    protected string $className;

    public function __construct(string $table)
    {
        $this->table = $table;
        $this->className = __NAMESPACE__ . '\\' . ucfirst($table);
        $connection = new Connection();
        $this->pdo = $connection->getPdoConnection();
    }

    public function selectAll(string $order = ''): array
    {
        $query = 'SELECT * FROM ' . $this->table;
        if ($order !== '') {
            $query .= ' ORDER BY ' . $order;
        }
        return $this->pdo->query($query)->fetchAll();
    }

    public function selectOneById(int $id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM $this->table WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }
    public function selectOneByName(string $name)
    {
        $statement = $this->pdo->prepare("SELECT * FROM $this->table WHERE name=:name");
        $statement->bindValue('name', $name, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare("DELETE FROM $this->table WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }
}
