<?php

namespace App\Model;

use PDO;

class OrderItemManager extends AbstractManager
{
    public const TABLE = 'order_item';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }


    public function insert(array $order)
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . "(order_id, figurine_id, figurine_quantity) 
        VALUES (:order_id, :figurine_id, :figurine_quantity)");
        $statement->bindValue('order_id', $order['order_id'], PDO::PARAM_INT);
        $statement->bindValue('figurine_id', $order['figurine_id'], PDO::PARAM_INT);
        $statement->bindValue('figurine_quantity', $order['figurine_quantity'], PDO::PARAM_INT);

        $statement->execute();

        return (int) $this->pdo->lastInsertId();
    }

    public function getTicketFromOrderId(int $orderId)
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE order_id= :order_id");
        $statement->bindValue('order_id', $orderId, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll();
    }

    public function totalSales()
    {
        $query = 'SELECT figurine_id, COUNT(*), 
        SUM(figurine_quantity) totalBuy, figurine.price, 
        (SUM(figurine_quantity) * figurine.price) totalPrice 
        FROM ' . self::TABLE . ' 
        JOIN figurine ON figurine_id = figurine.id 
        GROUP BY figurine_id';
        return $this->pdo->query($query)->fetchAll();
    }

    public function bestSeller()
    {
        $query = 'SELECT figurine_id, COUNT(*), SUM(figurine_quantity) 
        totalBuy, figurine.price, 
        (SUM(figurine_quantity) * figurine.price) totalPrice 
        FROM ' . self::TABLE . ' 
        JOIN figurine ON figurine_id = figurine.id 
        GROUP BY figurine_id';
        $mostSells = $this->pdo->query($query . ' ORDER BY totalBuy DESC LIMIT 1;')->fetch();
        $mostMoney = $this->pdo->query($query . ' ORDER BY totalPrice DESC LIMIT 1;')->fetch();
        return [$mostSells, $mostMoney];
    }
}
