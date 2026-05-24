<?php
/**
 * Mementos - API
 */

define('DB_PATH', __DIR__ . '/mementos.db');
header('Content-Type: application/json');

function getDB() {
    $db = new PDO('sqlite:' . DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->exec("PRAGMA journal_mode=WAL");
    return $db;
}

function initDB() {
    $db = getDB();
    $db->exec("
        CREATE TABLE IF NOT EXISTS recuerdos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            titulo TEXT NOT NULL,
            contenido TEXT,
            categoria TEXT DEFAULT 'general',
            tags TEXT DEFAULT '',
            creado TEXT DEFAULT (datetime('now','localtime')),
            modificado TEXT DEFAULT (datetime('now','localtime'))
        )");
}

initDB();

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
