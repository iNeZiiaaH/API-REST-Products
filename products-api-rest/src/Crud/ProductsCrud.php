<?php

namespace App\Crud;

use App\Exception\InternalServerError;
use App\Exception\DeleteProductsSuccess;
use App\Exception\NotFound;
use App\Exception\UpdateProductsSuccess;
use PDO;

class ProductsCrud
{
  public function __construct(private PDO $pdo)
  {
  }

  /**
   * Creates a new product
   *
   * @param array $data name, base price & description (optional)
   * @return int ID of created product
   * @throws Exception
   */
  public function create(array $data): int
  {
    if (!isset($data['name']) || !isset($data['basePrice'])) {
      throw new InternalServerError("Name and base price are required");
    }

    $query = "INSERT INTO products VALUES (null, :name, :basePrice, :description)";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute([
      'name' => $data['name'],
      'basePrice' => $data['basePrice'],
      'description' => $data['description']
    ]);

    return $this->pdo->lastInsertId();
  }

  public function findAll(): array
  {
    $stmt = $this->pdo->query("SELECT * FROM products");
    $products = $stmt->fetchAll();

    return ($products === false) ? [] : $products;
  }

  public function find(int $id): ?array
  {
    $query = "SELECT * FROM products WHERE id = :id";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute(['id' => $id]);

    $product = $stmt->fetch();
    if ($product === false) {
    throw new NotFound("Product not found");
    } else {
      return $product;
    }
  }

      /**
     * modifies an existing product 
     *
     * @param integer $id
     * @param array $data name, baseprice & description
     * @return void
     * @throws Exception
     */

  public function update(int $id, array $data): void
  {
    {
      $query = "UPDATE products SET name = :name, baseProce = :basePrice, description = :description WHERE id = :id;";

      $stmt = $this->pdo->prepare($query);
      $stmt->execute([
          'name' => $data['name'],
          'basePrice' => $data['basePrice'],
          'description' => $data['description'],
          'id' => $id
      ]);
      if ($stmt->rowCount() === 0) {
          throw new InternalServerError('No line could be modified');
      } else {
        throw new UpdateProductsSuccess("Product successfully modified");
      }
  }
  }

  /**
     * deletes an existing product
     *
     * @param integer $id
     * @return void
     * @throws Exception
     */

  public function delete(int $id): void
  {
    $query = "DELETE FROM products WHERE id = :id";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute(['id' => $id]);
    if ($stmt->rowCount() === 0) {
      throw new InternalServerError("Product not found");
  } else {
    throw new DeleteProductsSuccess("Product delete successfully");
  }
}
}