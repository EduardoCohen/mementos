<?php
/**
 * Mementos - Clase de Autenticación
 */

require_once __DIR__ . '/../bootstrap.php';

class Auth {
    private static function db() {
        return getDB();
    }

    public static function hashPassword($pass) {
        return password_hash($pass, PASSWORD_BCRYPT);
    }

    public static function verifyPassword($pass, $hash) {
        return password_verify($pass, $hash);
    }

    public static function login($username, $password) {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE username = :u AND activo = 1");
        $stmt->execute([':u' => $username]);
        $user = $stmt->fetch();

        if ($user && self::verifyPassword($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['rol'] = $user['rol'];
            return ['success' => true, 'username' => $user['username'], 'rol' => $user['rol']];
        }
        return ['success' => false, 'error' => 'Credenciales inválidas'];
    }

    public static function logout() {
        session_unset();
        session_destroy();
        return ['success' => true];
    }

    public static function check() {
        if (isset($_SESSION['user_id'])) {
            return ['logged' => true, 'username' => $_SESSION['username'], 'rol' => $_SESSION['rol']];
        }
        return ['logged' => false];
    }

    public static function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    }
}
