<?php

// Charge l'autoloader PSR-4 de Composer
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\DbInitializer;
use App\Config\ExceptionHandlerInitializer;
use App\Exception\UnprocessableContentException;
use App\Crud\ProductsCrud;
use App\httpCode\ResponseCode;
use Symfony\Component\Dotenv\Dotenv;

header('Content-type: application/json; charset=UTF-8');

// Charge les variables d'environnement
$dotenv = new Dotenv();
$dotenv->loadEnv('.env');

// Définit un gestionnaire d'exceptions au niveau global
ExceptionHandlerInitializer::registerGlobalExceptionHandler();
$pdo = DbInitializer::getPdoInstance();

$uri = $_SERVER['REQUEST_URI'];
$httpMethod = $_SERVER['REQUEST_METHOD'];
const RESOURCES = ['products'];

$uriParts = explode('/', $uri);
$isItemOperation = count($uriParts) === 3;
$productsCrud = new ProductsCrud($pdo);

// Collection de produits
if ($uri === '/products' && $httpMethod === 'GET') {
  echo json_encode($productsCrud->findAll());
  exit;
}

// Création de produit
if ($uri === '/products' && $httpMethod === 'POST') {
  $data = json_decode(file_get_contents('php://input'), true);
  $productId = $productsCrud->create($data);
}


// Identifie si on est sur une opération sur un élément
if (!$isItemOperation) {
  http_response_code(404);
  echo json_encode([
    'error' => 'Route non trouvée'
  ]);
  exit;
}

// Identifie si l'ID est valide (pas s'il existe en bdd)
$resourceName = $uriParts[1];
$id = intval($uriParts[2]);
if ($id === 0) {
  http_response_code(400);
  echo json_encode([
    'error' => 'ID non valide'
  ]);
  exit;
}

if ($resourceName === 'products' && $isItemOperation && $httpMethod === 'GET') {

  echo json_encode($productsCrud->find($id));
}

if ($resourceName === 'products' && $isItemOperation && $httpMethod === 'PUT') {
  $data = json_decode(file_get_contents('php://input'), true);

  if (!isset($data['name']) || !isset($data['basePrice'])) {
    http_response_code(422);
    echo json_encode([
      'error' => 'Name and base price are required'
    ]);
    exit;
  }
  echo json_encode($productsCrud->update($id, $data));
}

if ($resourceName === 'products' && $isItemOperation && $httpMethod === 'DELETE') {
  echo json_encode($productsCrud->delete($id));
}
