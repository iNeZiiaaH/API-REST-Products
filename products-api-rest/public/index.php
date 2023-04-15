<?php

// Charge l'autoloader PSR-4 de Composer
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\DbInitializer;
use App\Config\ExceptionHandlerInitializer;
use App\Exception\UnprocessableContentException;
use App\Exception\InternalServerError;
use App\Exception\NotFound;
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
  try {
    $productId = $productsCrud->create($data);
    http_response_code(ResponseCode::CREATED);
    echo json_encode(['message' => 'Product successfully created', 'id' => $productId]);
  } catch (UnprocessableContentException $e) {
    http_response_code(ResponseCode::UNPROCESSABLE_CONTENT);
    echo json_encode(['error' => $e->getMessage()]);
  } catch (InternalServerError $e) {
    http_response_code(ResponseCode::INTERNAL_SERVER_ERROR);
    echo json_encode(['error' => $e->getMessage()]);
  }
  exit;
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

// Méthode pour afficher un seul produit
if ($resourceName === 'products' && $isItemOperation && $httpMethod === 'GET') {
  $productsCrud = new ProductsCrud($pdo);
  try {
    $product = $productsCrud->find($id);
    echo json_encode($product);
  } catch (NotFound $e) {
    http_response_code(ResponseCode::NOT_FOUND);
    echo json_encode(['error' => $e->getMessage()]);
  }
  exit;
}

// Pour modifier le produit, on utilise la méthode 'PUT'
if ($resourceName === 'products' && $isItemOperation && $httpMethod === 'PUT') {
  $data = json_decode(file_get_contents('php://input'), true);
  // Je vien récupérer ma classe ProductsCrud pour ensuite récupérer la méthode update
  $productsCrud = new ProductsCrud($pdo);

  // je vien gérer mes erreurs en entourant ma méthode update
  try {
    $productsCrud->update($id, $data);
    http_response_code(ResponseCode::OK);
    echo json_encode(['message' => 'Product successfully updated']);
  } catch (NotFound $e) {
    http_response_code(ResponseCode::NOT_FOUND);
    echo json_encode(['error' => $e->getMessage()]);
  } catch (InternalServerError $e) {
    http_response_code(ResponseCode::INTERNAL_SERVER_ERROR);
    echo json_encode(['error' => $e->getMessage()]);
  } catch (UnprocessableContentException $e) {
    http_response_code(ResponseCode::UNPROCESSABLE_CONTENT);
    echo json_encode(['error' => $e->getMessage()]);
  }
  exit;
}

// On vient supprimé le produit avec la méthode 'DELETE'
if ($resourceName === 'products' && $isItemOperation && $httpMethod === 'DELETE') {
  $productsCrud = new ProductsCrud($pdo);
  try {
    $productsCrud->delete($id);
    http_response_code(ResponseCode::OK);
    echo json_encode(['message' => 'Product successfully deleted']);
  } catch (NotFound $e) {
    http_response_code(ResponseCode::NOT_FOUND);
    echo json_encode(['error' => $e->getMessage()]);
  } catch (InternalServerError $e) {
    http_response_code(ResponseCode::INTERNAL_SERVER_ERROR);
    echo json_encode(['error' => $e->getMessage()]);
  }
  exit;
}
