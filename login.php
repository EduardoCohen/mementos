<?php
/**
 * Mementos - Login
 */

require_once __DIR__ . '/bootstrap.php';

// Si ya está logueado, redirigir a la app
if (isset($_SESSION['user_id'])) {
    header('Location: /mementos/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mententos - Login</title>
    <style>
        :root {
            --bg: #0f0f14;
            --bg-card: #18181f;
            --bg-input: #1e1e28;
            --text: #e0e0e8;
            --text-dim: #8888a0;
            --accent: #7c6ff7;
            --accent-border: #5f56d4;
            --danger: #f87171;
            --border: #2a2a3a;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-box {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2.5rem;
            width: 90%;
            max-width: 400px;
        }
        .login-box h1 {
            text-align: center;
            color: var(--accent);
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }
        .login-box .subtitle {
            text-align: center;
            color: var(--text-dim);
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; color: var(--text-dim); font-size: 0.85rem; margin-bottom: 0.4rem; }
        .form-group input {
            width: 100%; padding: 0.8rem 1rem; border: 1px solid var(--border);
            border-radius: 8px; background: var(--bg-input); color: var(--text);
            font-size: 1rem;
        }
        .form-group input:focus { outline: none; border-color: var(--accent); }
        .btn {
            width: 100%; padding: 0.8rem; border: none; border-radius: 8px;
            cursor: pointer; font-size: 1rem; font-weight: 600;
            background: var(--accent); color: #fff;
            transition: background 0.2s;
        }
        .btn:hover { background: var(--accent-border); }
        .error {
            background: rgba(248,113,113,0.1); border: 1px solid var(--danger);
            color: var(--danger); padding: 0.8rem; border-radius: 8px;
            margin-bottom: 1rem; font-size: 0.9rem; display: none;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h1>🕰️ Mementos</h1>
    <p class="subtitle">Iniciá sesión para acceder a tus recuerdos</p>
    <div class="error" id="error"></div>
    <form id="login-form" onsubmit="return doLogin(event)">
        <div class="form-group">
            <label>Usuario</label>
            <input type="text" id="username" placeholder="Tu usuario" required autocomplete="username">
        </div>
        <div class="form-group">
            <label>Contraseña</label>
            <input type="password" id="password" placeholder="Tu contraseña" required autocomplete="current-password">
        </div>
        <button type="submit" class="btn">Ingresar</button>
    </form>
</div>

<script>
const AUTH_API = '/mementos/api/auth.php';

async function doLogin(e) {
    e.preventDefault();
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    const errorDiv = document.getElementById('error');

    try {
        const resp = await fetch(AUTH_API, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'login', username, password})
        });
        const data = await resp.json();

        if (data.success) {
            window.location.href = '/mementos/';
        } else {
            errorDiv.textContent = data.error || 'Error al iniciar sesión';
            errorDiv.style.display = 'block';
        }
    } catch (e) {
        errorDiv.textContent = 'Error de conexión';
        errorDiv.style.display = 'block';
    }
    return false;
}

// Focus en username al cargar
document.getElementById('username').focus();
</script>
</body>
</html>
