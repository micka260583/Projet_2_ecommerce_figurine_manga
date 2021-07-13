<?php

namespace App\Model;

use PDO;

class CarouselManager extends AbstractManager
{
    public const TABLE = 'carousel';

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function updateCarousel(array $carousel): bool
    {
        // prepared request
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `image` = :image WHERE id=:id");
        $statement->bindValue('id', $carousel['id'], \PDO::PARAM_INT);
        $statement->bindValue('image', $carousel['image'], \PDO::PARAM_STR);

        return $statement->execute();
    }
}
