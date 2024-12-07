<?php
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../utils/response.php';

// Configuración de CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejo de solicitudes preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// Luego, procesa el path si existe
$path = isset($_SERVER['REQUEST_URI']) ? explode('/', trim($_SERVER['REQUEST_URI'], '/')) : [];

// Verificar si el endpoint es correcto
if (empty($path) || $path[1] !== 'products') {
    sendResponse(['error' => 'Endpoint not found'], 404);
    exit;
}

switch ($method) {
    case 'GET':

        if (empty($path[2])) {
            // Get all products
            $stmt = $pdo->query('SELECT * FROM products');
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse($products);
        }else {
            $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
            $stmt->execute(['id' => $path[2]]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($product) {
                sendResponse($product);
            } else {
                sendResponse(['error' => 'Producto no encontrado'], 404);
            }
        }
        break;

    case 'POST':
        // Add a new product
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['name']) || empty($input['price']) || empty($input['quantity']) || empty($input['description'])) {
            sendResponse(['error' => 'Missing required fields'], 400);
        }
        $stmt = $pdo->prepare('INSERT INTO products (name, price, quantity, description) VALUES (?, ?, ?, ?)');
        $stmt->execute([$input['name'], $input['price'], $input['quantity'], $input['description']]);
        sendResponse(['message' => 'Product added'], 201);
        break;

    case 'PUT':
        // Update a product
        $id = $path[2] ?? null;
        if (!$id) {
            sendResponse(['error' => 'ID not provided'], 400);
        }
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['name']) || empty($input['price']) || empty($input['quantity']) || empty($input['description'])) {
            sendResponse(['error' => 'Missing required fields'], 400);
        }
        $stmt = $pdo->prepare('UPDATE products SET name = ?, price = ?, quantity = ?, description = ? WHERE id = ?');
        $stmt->execute([$input['name'], $input['price'], $input['quantity'], $input['description'], $id]);
        sendResponse(['message' => 'Product updated']);
        break;

    case 'DELETE':
        // Delete a product
        $id = $path[2] ?? null;
        if (!$id) {
            sendResponse(['error' => 'ID not provided'], 400);
        }
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
        sendResponse(['message' => 'Product deleted']);
        break;

    default:
        sendResponse(['error' => 'Method not supported'], 405);
        break;
}
