<?php
/**
 * Mementos - Sistema de Recuerdos
 * Stack: PHP 8.3 + SQLite + JS vanilla
 */

define('DB_PATH', __DIR__ . '/mementos.db');

function getDB() {
    $db = new PDO('sqlite:' . DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->exec("PRAGMA journal_mode=WAL");
    return $db;
}

function initDB() {
    $db = getDB();
    $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS recuerdos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            titulo TEXT NOT NULL,
            contenido TEXT,
            categoria TEXT DEFAULT 'general',
            tags TEXT DEFAULT '',
            creado TEXT DEFAULT (datetime('now','localtime')),
            modificado TEXT DEFAULT (datetime('now','localtime'))
        )
SQL;
    $db->exec($sql);
    $sql2 = <<<SQL
        CREATE TABLE IF NOT EXISTS config (
            clave TEXT PRIMARY KEY,
            valor TEXT
        )
SQL;
    $db->exec($sql2);
}

initDB();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mementos</title>
    <style>
        :root {
            --bg: #0f0f14;
            --bg-card: #18181f;
            --bg-input: #1e1e28;
            --text: #e0e0e8;
            --text-dim: #8888a0;
            --accent: #7c6ff7;
            --accent-border: #5f56d4;
            --success: #34d399;
            --danger: #f87171;
            --border: #2a2a3a;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }
        .container { max-width: 900px; margin: 0 auto; padding: 2rem 1.5rem; }
        header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);
        }
        header h1 { font-size: 1.8rem; color: var(--accent); }
        header h1 span { color: var(--text-dim); font-size: 0.9rem; margin-left: 0.5rem; }
        .btn {
            padding: 0.6rem 1.2rem; border: none; border-radius: 8px;
            cursor: pointer; font-size: 0.9rem; font-weight: 600;
            transition: all 0.2s;
        }
        .btn-primary { background: var(--accent); color: #fff; border: 1px solid var(--accent-border); }
        .btn-primary:hover { background: var(--accent-border); }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-sm { padding: 0.35rem 0.7rem; font-size: 0.8rem; }

        .search-box {
            width: 100%; padding: 0.8rem 1rem; border: 1px solid var(--border);
            border-radius: 10px; background: var(--bg-input); color: var(--text);
            font-size: 1rem; margin-bottom: 1.5rem;
        }
        .search-box:focus { outline: none; border-color: var(--accent); }

        .filtros { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .filtro-btn {
            padding: 0.4rem 1rem; border: 1px solid var(--border); border-radius: 20px;
            background: var(--bg-card); color: var(--text-dim); cursor: pointer;
            font-size: 0.85rem; transition: all 0.2s;
        }
        .filtro-btn.active, .filtro-btn:hover { border-color: var(--accent); color: var(--accent); background: rgba(124,111,247,0.1); }

        .memorias { display: flex; flex-direction: column; gap: 1rem; }
        .memorias-count {
            color: var(--text-dim); font-size: 0.85rem; margin-bottom: 1rem;
        }

        .card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 12px; padding: 1.2rem 1.5rem; transition: all 0.2s;
        }
        .card:hover { border-color: var(--accent); }
        .card-header {
            display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.8rem;
        }
        .card-titulo { font-size: 1.1rem; font-weight: 600; }
        .card-fecha { color: var(--text-dim); font-size: 0.8rem; }
        .card-categoria {
            display: inline-block; padding: 0.2rem 0.6rem; border-radius: 12px;
            background: rgba(124,111,247,0.15); color: var(--accent);
            font-size: 0.75rem; margin-bottom: 0.5rem;
        }
        .card-contenido {
            color: var(--text-dim); font-size: 0.95rem; line-height: 1.5;
            white-space: pre-wrap;
        }
        .card-tags { margin-top: 0.8rem; display: flex; gap: 0.4rem; flex-wrap: wrap; }
        .tag {
            padding: 0.15rem 0.5rem; border-radius: 10px;
            background: var(--bg-input); color: var(--text-dim);
            font-size: 0.75rem;
        }
        .card-actions {
            display: flex; gap: 0.5rem; margin-top: 1rem;
            padding-top: 0.8rem; border-top: 1px solid var(--border);
        }

        /* Modal */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.7); z-index: 100;
            justify-content: center; align-items: center;
        }
        .modal-overlay.active { display: flex; }
        .modal {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 16px; padding: 2rem; width: 90%; max-width: 550px;
            max-height: 85vh; overflow-y: auto;
        }
        .modal h2 { margin-bottom: 1.5rem; color: var(--accent); }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; color: var(--text-dim); font-size: 0.85rem; margin-bottom: 0.4rem; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; padding: 0.7rem 1rem; border: 1px solid var(--border);
            border-radius: 8px; background: var(--bg-input); color: var(--text);
            font-size: 0.95rem;
        }
        .form-group textarea { min-height: 120px; resize: vertical; font-family: inherit; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--accent); }
        .modal-actions { display: flex; gap: 0.8rem; justify-content: flex-end; }

        .empty { text-align: center; padding: 3rem; color: var(--text-dim); }
        .empty-icon { font-size: 3rem; margin-bottom: 1rem; }

        @media (max-width: 600px) {
            .container { padding: 1rem; }
            header { flex-direction: column; gap: 1rem; align-items: flex-start; }
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>🕰️ Mementos <span>v1.0</span></h1>
        <button class="btn btn-primary" onclick="abrirModal()">+ Nuevo recuerdo</button>
    </header>

    <input type="text" class="search-box" id="search" placeholder="Buscar recuerdos..." oninput="cargarRecuerdos()">

    <div class="filtros" id="filtros">
        <button class="filtro-btn active" data-cat="todas" onclick="filtrar('todas',this)">Todas</button>
        <?php
        $db = getDB();
        $cats = $db->query("SELECT DISTINCT categoria FROM recuerdos WHERE categoria != '' ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($cats as $c) {
            $escaped_c = htmlspecialchars($c, ENT_QUOTES, 'UTF-8');
            echo '<button class="filtro-btn" data-cat="' . $escaped_c . '" onclick="filtrar(\'' . $escaped_c . '\',this)">' . $escaped_c . '</button>';
        }
        ?>
    </div>

    <div class="memorias-count" id="count"></div>
    <div class="memorias" id="memorias"></div>
</div>

<!-- Modal -->
<div class="modal-overlay" id="modal">
    <div class="modal">
        <h2 id="modal-title">Nuevo Recuerdo</h2>
        <input type="hidden" id="recuerdo-id">
        <div class="form-group">
            <label>Título</label>
            <input type="text" id="recuerdo-titulo" placeholder="De qué es este recuerdo...">
        </div>
        <div class="form-group">
            <label>Contenido</label>
            <textarea id="recuerdo-contenido" placeholder="Escribí lo que quieras recordar..."></textarea>
        </div>
        <div class="form-group">
            <label>Categoría</label>
            <input type="text" id="recuerdo-categoria" placeholder="general, trabajo, idea, personal..." list="cats-list">
            <datalist id="cats-list">
                <option value="general">
                <option value="trabajo">
                <option value="idea">
                <option value="personal">
                <option value="aprendizaje">
                <option value="proyecto">
            </datalist>
        </div>
        <div class="form-group">
            <label>Tags (separados por coma)</label>
            <input type="text" id="recuerdo-tags" placeholder="php, docker, aprendizaje...">
        </div>
        <div class="modal-actions">
            <button class="btn btn-sm" onclick="cerrarModal()" style="background:var(--bg-input);color:var(--text-dim);">Cancelar</button>
            <button class="btn btn-primary btn-sm" onclick="guardarRecuerdo()">Guardar</button>
        </div>
    </div>
</div>

<script>
const API = 'api.php';

function abrirModal(id = null) {
    document.getElementById('modal').classList.add('active');
    document.getElementById('modal-title').textContent = id ? 'Editar Recuerdo' : 'Nuevo Recuerdo';
    if (!id) {
        document.getElementById('recuerdo-id').value = '';
        document.getElementById('recuerdo-titulo').value = '';
        document.getElementById('recuerdo-contenido').value = '';
        document.getElementById('recuerdo-categoria').value = 'general';
        document.getElementById('recuerdo-tags').value = '';
    }
}

function cerrarModal() {
    document.getElementById('modal').classList.remove('active');
}

async function cargarRecuerdos() {
    const search = encodeURIComponent(document.getElementById('search').value);
    const cat = document.querySelector('.filtro-btn.active')?.dataset.cat || 'todas';
    try {
        const resp = await fetch(`${API}?action=list&search=${search}&categoria=${encodeURIComponent(cat)}`);
        const data = await resp.json();
        renderRecuerdos(data);
    } catch (e) {
        console.error(e);
    }
}

function renderRecuerdos(items) {
    const container = document.getElementById('memorias');
    document.getElementById('count').textContent = `${items.length} recuerdo${items.length !== 1 ? 's' : ''}`;

    if (!items.length) {
        container.innerHTML = '<div class="empty"><div class="empty-icon">🕰️</div><p>Todavía no hay recuerdos.<br>Empezá creando uno nuevo.</p></div>';
        return;
    }

    container.innerHTML = items.map(r => `
        <div class="card">
            <div class="card-header">
                <div>
                    ${r.categoria ? `<span class="card-categoria">${escapeHtml(r.categoria)}</span>` : ''}
                    <div class="card-titulo">${escapeHtml(r.titulo)}</div>
                </div>
                <div class="card-fecha">${r.creado}</div>
            </div>
            <div class="card-contenido">${escapeHtml(r.contenido || '')}</div>
            ${r.tags ? `<div class="card-tags">${r.tags.split(',').map(t => `<span class="tag">#${escapeHtml(t.trim())}</span>`).join('')}</div>` : ''}
            <div class="card-actions">
                <button class="btn btn-sm btn-primary" onclick="editarRecuerdo(${r.id})">Editar</button>
                <button class="btn btn-sm btn-danger" onclick="eliminarRecuerdo(${r.id})">Eliminar</button>
            </div>
        </div>
    `).join('');
}

function escapeHtml(s) {
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}

function filtrar(cat, btn) {
    document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    cargarRecuerdos();
}

async function guardarRecuerdo() {
    const id = document.getElementById('recuerdo-id').value;
    const payload = {
        action: id ? 'update' : 'create',
        id: id || undefined,
        titulo: document.getElementById('recuerdo-titulo').value,
        contenido: document.getElementById('recuerdo-contenido').value,
        categoria: document.getElementById('recuerdo-categoria').value || 'general',
        tags: document.getElementById('recuerdo-tags').value,
    };
    if (!payload.titulo.trim()) return;

    try {
        const resp = await fetch(API, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        const r = await resp.json();
        if (r.success) {
            cerrarModal();
            cargarRecuerdos();
        }
    } catch (e) { console.error(e); }
}

async function editarRecuerdo(id) {
    try {
        const resp = await fetch(`${API}?action=get&id=${id}`);
        const r = await resp.json();
        if (r.success) {
            abrirModal(id);
            document.getElementById('recuerdo-titulo').value = r.data.titulo;
            document.getElementById('recuerdo-contenido').value = r.data.contenido;
            document.getElementById('recuerdo-categoria').value = r.data.categoria;
            document.getElementById('recuerdo-tags').value = r.data.tags;
            document.getElementById('recuerdo-id').value = r.data.id;
        }
    } catch (e) { console.error(e); }
}

async function eliminarRecuerdo(id) {
    if (!confirm('¿Eliminar este recuerdo?')) return;
    try {
        const resp = await fetch(API, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'delete', id})
        });
        const r = await resp.json();
        if (r.success) cargarRecuerdos();
    } catch (e) { console.error(e); }
}

cargarRecuerdos();
</script>
</body>
</html>
