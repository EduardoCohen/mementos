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

    public static function changePassword($userId, $currentPassword, $newPassword) {
        $db = self::db();

        // Verificar contraseña actual
        $stmt = $db->prepare("SELECT password_hash FROM usuarios WHERE id = :id AND activo = 1");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'error' => 'Usuario no encontrado'];
        }

        if (!self::verifyPassword($currentPassword, $user['password_hash'])) {
            return ['success' => false, 'error' => 'La contraseña actual no es correcta'];
        }

        // Validar nueva contraseña
        if (strlen($newPassword) < 4) {
            return ['success' => false, 'error' => 'La nueva contraseña debe tener al menos 4 caracteres'];
        }

        // Actualizar
        $newHash = self::hashPassword($newPassword);
        $stmt = $db->prepare("UPDATE usuarios SET password_hash = :hash WHERE id = :id");
        $stmt->execute([':hash' => $newHash, ':id' => $userId]);

        return ['success' => true];
    }
}
