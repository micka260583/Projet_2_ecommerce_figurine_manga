<?php

namespace App\Model;

use PDO;

class FigurineManager extends AbstractManager
{
    public const TABLE = 'figurine';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectOneById(int $id): array
    {
        $statement = $this->pdo->query('SELECT figurine.id, figurine.name, short_description, full_description,
        price, price_reduction, quantity, license.name license_name,
        maker.name maker_name, image_main, figurine.license_id, figurine.maker_id
        FROM figurine
        JOIN license ON license.id = figurine.license_id
        JOIN maker ON maker.id = figurine.maker_id
        WHERE figurine.id = ' . $id);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function selectAll(string $order = ''): array
    {
        $query = 'SELECT figurine.id, figurine.name, short_description, full_description,
        price, price_reduction, quantity, license.name license_name,
        maker.name maker_name, image_main, figurine.license_id, figurine.maker_id
        FROM figurine
        JOIN license ON license.id = figurine.license_id
        JOIN maker ON maker.id = figurine.maker_id';
        if ($order !== '') {
            $query .= ' ORDER BY ' . $order;
        }
        return $this->pdo->query($query)->fetchAll();
    }

    public function selectCarousel(): array
    {
        $query = 'SELECT figurine.id, figurine.name, price, price_reduction, image_main
        FROM figurine
        LIMIT 3';

        return $this->pdo->query($query)->fetchAll();
    }

    public function selectCards(): array
    {
        $query = 'SELECT figurine.id, figurine.name, price, price_reduction, image_main
        FROM figurine
        LIMIT 5';

        return $this->pdo->query($query)->fetchAll();
    }

    public function selectByLicenseId(int $id, int $makerId)
    {
        $statement = $this->pdo->prepare(
            'SELECT figurine.id id, figurine.name name, short_description, 
            full_description, price, price_reduction, quantity, 
            license.name license_name,
             maker.name maker_name, image_main, 
             figurine.license_id license_id, maker_id 
              FROM ' . self::TABLE . ' 
              JOIN license ON license.id = :id
              JOIN maker ON maker.id = :makerId
              WHERE figurine.license_id = :id;'
        );
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->bindValue('makerId', $makerId, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function insert(array $item): int
    {
        // prepared request
        $statement = $this->pdo->prepare("INSERT INTO figurine 
        (name, short_description, full_description license_id, maker_id,
        price, price_reduction, quantity, image_main) 
        VALUES (:name, :short_description, :full_description, :license_id, 
        :maker_id, :price, :price_reduction, :quantity, :image_main)");
        $statement->bindValue('name', $item['name'], \PDO::PARAM_STR);
        $statement->bindValue('short_description', $item['description'], \PDO::PARAM_STR);
        $statement->bindValue('full_description', $item['full_description'], \PDO::PARAM_STR);
        $statement->bindValue('license_id', $item['license'], \PDO::PARAM_INT);
        $statement->bindValue('maker_id', $item['maker'], \PDO::PARAM_INT);
        $statement->bindValue('price', $item['price'], \PDO::PARAM_STR);
        $statement->bindValue('price_reduction', $item['priceReduction'], \PDO::PARAM_STR);
        $statement->bindValue('quantity', $item['quantity'], \PDO::PARAM_INT);
        $statement->bindValue('image_main', $item['image'], \PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    public function update(array $figurine): bool
    {
        // prepared request
        $statement = $this->pdo->prepare("UPDATE figurine SET
        name = :name,
        short_description = :short_description,
        full_description = :full_description,
        license_id = :license_id,
        maker_id = :maker_id,
        price = :price,
        price_reduction = :price_reduction,
        quantity = :quantity,
        image_main = :image_main
        WHERE id = :id");
        $statement->bindValue('id', $figurine['id'], \PDO::PARAM_INT);
        $statement->bindValue('name', $figurine['name'], \PDO::PARAM_STR);
        $statement->bindValue('short_description', $figurine['description'], \PDO::PARAM_STR);
        $statement->bindValue('full_description', $figurine['full_description'], \PDO::PARAM_STR);
        $statement->bindValue('license_id', $figurine['license'], \PDO::PARAM_INT);
        $statement->bindValue('maker_id', $figurine['maker'], \PDO::PARAM_INT);
        $statement->bindValue('price', $figurine['price'], \PDO::PARAM_STR);
        $statement->bindValue('price_reduction', $figurine['priceReduction'], \PDO::PARAM_STR);
        $statement->bindValue('quantity', $figurine['quantity'], \PDO::PARAM_INT);
        $statement->bindValue('image_main', $figurine['image'], \PDO::PARAM_STR);

        return $statement->execute();
    }

    public function removeQuantity(int $id, int $qty): bool
    {
        $figurine = $this->selectOneById($id);
        $statement = $this->pdo->prepare(
            'UPDATE ' . self::TABLE . ' SET 
            quantity = :quantity WHERE 
            id = :id;'
        );
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->bindValue('quantity', intval($figurine['quantity']) - $qty, PDO::PARAM_INT);
        return $statement->execute();
    }
}
