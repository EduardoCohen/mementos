<?php
/**
 * Mementos - API de Recuerdos
 */

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/classes/Auth.php';

header('Content-Type: application/json');

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    $db = getDB();

    if ($method === 'GET') {
        $action = $_GET['action'] ?? 'list';

        switch ($action) {
            case 'list':
                $search = $_GET['search'] ?? '';
                $categoria = $_GET['categoria'] ?? 'todas';
                $sql = "SELECT * FROM recuerdos WHERE 1=1";
                $params = [];
                if ($search) {
                    $sql .= " AND (titulo LIKE :s OR contenido LIKE :s OR tags LIKE :s)";
                    $params[':s'] = "%$search%";
                }
                if ($categoria && $categoria !== 'todas') {
                    $sql .= " AND categoria = :c";
                    $params[':c'] = $categoria;
                }
                $sql .= " ORDER BY creado DESC";
                $stmt = $db->prepare($sql);
                foreach ($params as $k => $v) $stmt->bindValue($k, $v);
                $stmt->execute();
                echo json_encode($stmt->fetchAll());
                break;

            case 'get':
                $stmt = $db->prepare("SELECT * FROM recuerdos WHERE id = :id");
                $stmt->execute([':id' => $_GET['id']]);
                $row = $stmt->fetch();
                echo json_encode($row ? ['success' => true, 'data' => $row] : ['success' => false]);
                break;

            default:
                echo json_encode(['error' => 'Acción no válida']);
        }
    } elseif ($method === 'POST') {
        $action = $input['action'] ?? '';

        switch ($action) {
            case 'create':
                $stmt = $db->prepare("INSERT INTO recuerdos (titulo, contenido, categoria, tags) VALUES (:t, :c, :cat, :tags)");
                $stmt->execute([
                    ':t' => $input['titulo'],
                    ':c' => $input['contenido'] ?? '',
                    ':cat' => $input['categoria'] ?? 'general',
                    ':tags' => $input['tags'] ?? '',
                ]);
                echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
                break;

            case 'update':
                $stmt = $db->prepare("UPDATE recuerdos SET titulo=:t, contenido=:c, categoria=:cat, tags=:tags, modificado=datetime('now','localtime') WHERE id=:id");
                $stmt->execute([
                    ':t' => $input['titulo'],
                    ':c' => $input['contenido'] ?? '',
                    ':cat' => $input['categoria'] ?? 'general',
                    ':tags' => $input['tags'] ?? '',
                    ':id' => $input['id'],
                ]);
                echo json_encode(['success' => true]);
                break;

            case 'delete':
                $stmt = $db->prepare("DELETE FROM recuerdos WHERE id = :id");
                $stmt->execute([':id' => $input['id']]);
                echo json_encode(['success' => true]);
                break;

            default:
                echo json_encode(['error' => 'Acción no válida']);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
