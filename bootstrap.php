<?php
/**
 * Mementos - Bootstrap
 * Inicializa DB y sesión
 */

define('DB_PATH', __DIR__ . '/mementos.db');

session_start();

function getDB() {
    $db = new PDO('sqlite:' . DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->exec("PRAGMA journal_mode=WAL");
    return $db;
}

function initDB() {
    $db = getDB();
    // recuerdos
    $db->exec("
        CREATE TABLE IF NOT EXISTS recuerdos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            titulo TEXT NOT NULL,
            contenido TEXT,
            categoria TEXT DEFAULT 'general',
            tags TEXT DEFAULT '',
            creado TEXT DEFAULT (datetime('now','localtime')),
            modificado TEXT DEFAULT (datetime('now','localtime'))
        )
    ");
    // config
    $db->exec("
        CREATE TABLE IF NOT EXISTS config (
            clave TEXT PRIMARY KEY,
            valor TEXT
        )
    ");
    // usuarios
    $db->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL,
            nombre TEXT DEFAULT '',
            rol TEXT DEFAULT 'admin',
            activo INTEGER DEFAULT 1,
            creado TEXT DEFAULT (datetime('now','localtime'))
        )
    ");

    // Categorías por defecto
    $default_cats = ['general', 'trabajo', 'idea', 'personal', 'aprendizaje', 'proyecto', 'salud', 'finanzas', 'viaje', 'familia'];
    $stmt = $db->prepare("INSERT OR IGNORE INTO config (clave, valor) VALUES (?, ?)");
    foreach ($default_cats as $cat) {
        $stmt->execute(["cat_$cat", $cat]);
    }

    // Admin por defecto: admin / mementos2026
    $admin_hash = password_hash('mementos2026', PASSWORD_BCRYPT);
    $stmt = $db->prepare("INSERT OR IGNORE INTO usuarios (username, password_hash, nombre, rol) VALUES (?, ?, ?, ?)");
    $stmt->execute(['admin', $admin_hash, 'Administrador', 'admin']);
}

initDB();
