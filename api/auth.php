<?php
/**
 * Mementos - API de Autenticación
 */

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../classes/Auth.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    if ($method === 'POST') {
        $action = $input['action'] ?? '';

        switch ($action) {
            case 'login':
                $username = $input['username'] ?? '';
                $password = $input['password'] ?? '';
                $result = Auth::login($username, $password);
                echo json_encode($result);
                break;

            case 'logout':
                $result = Auth::logout();
                echo json_encode($result);
                break;

            default:
                echo json_encode(['error' => 'Acción no válida']);
        }
    } elseif ($method === 'GET') {
        $action = $_GET['action'] ?? 'check';

        switch ($action) {
            case 'check':
                echo json_encode(Auth::check());
                break;

            default:
                echo json_encode(['error' => 'Acción no válida']);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
