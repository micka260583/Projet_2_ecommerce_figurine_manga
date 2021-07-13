<?php

namespace App\Model;

use DateTime;
use PDO;
use App\Model\OrderManager;

class UserManager extends AbstractManager
{
    public const TABLE = 'user';

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectOneByEmail(string $email)
    {
        $statement = $this->pdo->prepare('SELECT * FROM ' . self::TABLE . ' WHERE email = :email;');
        $statement->bindValue('email', $email, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }

    public function insert(array $user)
    {
        $insertDate = date_format(new DateTime(), 'Y-m-d H:i:s');
        $statement = $this->pdo->prepare(
            'INSERT INTO ' . self::TABLE . ' (name, email, password, date_of_creation, last_update) 
            VALUES (:name, :email, :password, :date_of_creation, :last_update);'
        );
        $statement->bindValue('name', $user['name'], PDO::PARAM_STR);
        $statement->bindValue('email', $user['email'], PDO::PARAM_STR);
        $statement->bindValue('password', $user['password'], PDO::PARAM_STR);
        $statement->bindValue('date_of_creation', $insertDate);
        $statement->bindValue('last_update', $insertDate);
        $statement->execute();

        return (int)$this->pdo->lastInsertId();
    }

    public function update(array $user)
    {
        $adress = '';
        foreach ($user['adress'] as $key => $value) {
            $adress .= $key . ':' . $value . ";";
        }
        $updateDate = date_format(new DateTime(), 'Y-m-d H:i:s');
        $statement = $this->pdo->prepare(
            'UPDATE ' . self::TABLE . ' SET 
            name = :name,
            email = :email,
            password = :password,
            adress = :adress,
            last_update = :last_update
            WHERE id = :id;'
        );
        $statement->bindValue('id', $user['id'], PDO::PARAM_INT);
        $statement->bindValue('name', $user['name'], PDO::PARAM_STR);
        $statement->bindValue('email', $user['email'], PDO::PARAM_STR);
        $statement->bindValue('adress', $adress, PDO::PARAM_STR);
        $statement->bindValue('password', $user['password'], PDO::PARAM_STR);
        $statement->bindValue('last_update', $updateDate);
        $statement->execute();

        return $this->selectOneById($user['id']);
    }

    public function bestBuyer()
    {
        $query = 'SELECT commande.id order_id, commande.total_price, 
        commande.user_id, user.name user_name, user.email user_email, 
        user.date_of_creation FROM ' . self::TABLE . '
        JOIN commande ON commande.user_id = user.id
        ORDER BY user.id;';
        return $this->pdo->query($query)->fetchAll();
    }

    public function admin(int $id, int $admin)
    {
        $statement = $this->pdo->prepare(
            'UPDATE ' . self::TABLE . ' 
            SET admin = :admin 
            WHERE id = :id;'
        );
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->bindValue('admin', $admin, PDO::PARAM_INT);
        $statement->execute();
    }
}
