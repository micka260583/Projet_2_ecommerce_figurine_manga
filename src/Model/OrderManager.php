<?php

namespace App\Model;

use PDO;

class OrderManager extends AbstractManager
{
    public const TABLE = 'commande';

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function insert(array $order)
    {
        $billing_address = $order['billing_address'];
        $delivery_address = $order['delivery_address'];

        if (gettype($order['billing_address']) == "array") {
            $billing_address = '';
            foreach ($order['billing_address'] as $key => $value) {
                $billing_address .= $key . ':' . $value . ";";
            }
        }
        if (gettype($order['delivery_address']) == "array") {
            $delivery_address = '';
            foreach ($order['delivery_address'] as $key => $value) {
                $delivery_address .= $key . ':' . $value . ";";
            }
        }

        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
        "(order_date, total_price, user_id, billing_address, delivery_address, status)
        VALUES (:order_date, :total_price, :user_id, :billing_address, :delivery_address, :status)");
        $statement->bindValue('order_date', $order['order_date'], PDO::PARAM_STR);
        $statement->bindValue('total_price', $order['total_price'], PDO::PARAM_STR);
        $statement->bindValue('user_id', $order['user_id'], PDO::PARAM_INT);
        $statement->bindValue('billing_address', $billing_address, PDO::PARAM_STR);
        $statement->bindValue('delivery_address', $delivery_address, PDO::PARAM_STR);
        $statement->bindValue('status', $order['status'], PDO::PARAM_STR);

        $statement->execute();

        return (int) $this->pdo->lastInsertId();
    }


    public function selectAll(string $order = ''): array
    {
        return $this->pdo->query('
        SELECT commande.id, commande.order_date, commande.total_price, 
        commande.status, user.name as user_name FROM ' . self::TABLE . '
        JOIN user ON user.id = commande.user_id
        ORDER BY commande.id')->fetchAll();
    }

    public function selectAllFromOneUser(int $id): array
    {
        return $this->pdo->query('
        SELECT commande.id, commande.order_date, commande.total_price, 
        commande.status, user.name as user_name, user.email as user_email FROM ' . self::TABLE . '
        JOIN user ON user.id = commande.user_id
        WHERE user.id = ' . $id . ';')->fetchAll();
    }

    public function selectOneById(int $id): array
    {
        $statement =  $this->pdo->prepare('
        SELECT commande.id, commande.order_date, commande.total_price, 
        commande.status, user.name as user_name, user.email as user_email FROM ' . self::TABLE . '
        JOIN user ON user.id = commande.user_id
        WHERE commande.id = :id');
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function selectDetails(int $id): array
    {
        $statement = $this->pdo->prepare(
            'SELECT order_item.figurine_quantity, figurine.name figurine_name, figurine.price figurine_price 
            FROM order_item 
            JOIN figurine ON figurine.id = order_item.figurine_id
            WHERE order_item.order_id = :id'
        );
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
    public function selectByUserId(int $id): array
    {
        $statement =  $this->pdo->prepare('
        SELECT commande.id, commande.order_date, commande.total_price, 
        commande.status, user.name as user_name, user.email as user_email FROM ' . self::TABLE . '
        JOIN user ON user.id = commande.user_id
        WHERE commande.user_id = :id');
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
