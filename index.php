<?php
/**
 * Mementos - App Principal
 * Stack: PHP 8.3 + SQLite + JS vanilla
 */

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/classes/Auth.php';

$check = Auth::check();
if (!$check['logged']) {
    header('Location: /mementos/login.php');
    exit;
}
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

        /* Header user area */
        .header-right { display: flex; align-items: center; gap: 1rem; }
        .user-badge {
            color: var(--text-dim); font-size: 0.85rem;
            display: flex; align-items: center; gap: 0.4rem;
        }
        .user-badge .username {
            color: var(--accent); font-weight: 600;
            cursor: pointer; text-decoration: none;
            border-bottom: 1px dashed transparent;
            transition: all 0.2s;
        }
        .user-badge .username:hover {
            border-bottom-color: var(--accent);
        }
        .btn-logout {
            background: transparent; color: var(--text-dim); border: 1px solid var(--border);
            padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer;
            font-size: 0.85rem; transition: all 0.2s;
        }
        .btn-logout:hover { border-color: var(--danger); color: var(--danger); }

        .search-box {
            width: 100%; padding: 0.8rem 1rem; border: 1px solid var(--border);
            border-radius: 10px; background: var(--bg-input); color: var(--text);
            font-size: 1rem; margin-bottom: 1.5rem;
        }
        .search-box:focus { outline: none; border-color: var(--accent); }

        /* Filtros */
        .filtros { display: flex; gap: 0.5rem; margin-bottom: 0.5rem; flex-wrap: wrap; }
        .filtros-tags { display: flex; gap: 0.4rem; margin-bottom: 1.5rem; flex-wrap: wrap; min-height: 0; }
        .filtros-tags:empty { margin-bottom: 0; }
        .filtro-btn, .filtro-tag-btn {
            padding: 0.4rem 1rem; border: 1px solid var(--border); border-radius: 20px;
            background: var(--bg-card); color: var(--text-dim); cursor: pointer;
            font-size: 0.85rem; transition: all 0.2s;
        }
        .filtro-tag-btn {
            padding: 0.25rem 0.7rem; font-size: 0.78rem;
            background: rgba(124,111,247,0.08);
        }
        .filtro-btn.active, .filtro-btn:hover,
        .filtro-tag-btn.active, .filtro-tag-btn:hover {
            border-color: var(--accent); color: var(--accent); background: rgba(124,111,247,0.1);
        }

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
        /* Tag clickeable en cards */
        .card-tags .tag {
            cursor: pointer;
            transition: all 0.2s;
        }
        .card-tags .tag:hover {
            background: rgba(124,111,247,0.15);
            color: var(--accent);
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
        .form-group select { cursor: pointer; }
        .form-group textarea { min-height: 120px; resize: vertical; font-family: inherit; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--accent); }
        .modal-actions { display: flex; gap: 0.8rem; justify-content: flex-end; }
        .modal-msg {
            padding: 0.7rem 1rem; border-radius: 8px; font-size: 0.85rem;
            margin-bottom: 1rem; display: none;
        }
        .modal-msg.error { background: rgba(248,113,113,0.1); border: 1px solid var(--danger); color: var(--danger); }
        .modal-msg.success { background: rgba(52,211,153,0.1); border: 1px solid var(--success); color: var(--success); }

        /* Toast */
        .toast {
            position: fixed; bottom: 2rem; right: 2rem;
            padding: 0.8rem 1.5rem; border-radius: 10px;
            background: var(--bg-card); border: 1px solid var(--border);
            color: var(--text); font-size: 0.9rem;
            z-index: 200; opacity: 0; transform: translateY(10px);
            transition: all 0.3s;
        }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.success { border-color: var(--success); color: var(--success); }
        .toast.error { border-color: var(--danger); color: var(--danger); }

        .empty { text-align: center; padding: 3rem; color: var(--text-dim); }
        .empty-icon { font-size: 3rem; margin-bottom: 1rem; }

        /* Tag input con chips */
        .tag-input-container {
            display: flex; flex-wrap: wrap; gap: 0.4rem;
            padding: 0.5rem; border: 1px solid var(--border);
            border-radius: 8px; background: var(--bg-input);
            min-height: 42px; align-items: center;
        }
        .tag-input-container:focus-within { border-color: var(--accent); }
        .tag-chips { display: flex; flex-wrap: wrap; gap: 0.3rem; }
        .tag-chip {
            display: inline-flex; align-items: center; gap: 0.3rem;
            padding: 0.2rem 0.5rem; border-radius: 6px;
            background: rgba(124,111,247,0.2); color: var(--accent);
            font-size: 0.8rem;
        }
        .tag-chip .remove {
            cursor: pointer; opacity: 0.6; font-size: 0.9rem;
            line-height: 1;
        }
        .tag-chip .remove:hover { opacity: 1; }
        .tag-input {
            border: none; background: transparent; color: var(--text);
            font-size: 0.9rem; outline: none; flex: 1; min-width: 80px;
            padding: 0.2rem;
        }

        /* Filtro tag activo indicator */
        .filtros-tags-label {
            color: var(--text-dim); font-size: 0.75rem; margin-right: 0.3rem;
            display: flex; align-items: center;
        }

        @media (max-width: 600px) {
            .container { padding: 1rem; }
            header { flex-direction: column; gap: 1rem; align-items: flex-start; }
            .header-right { width: 100%; justify-content: space-between; }
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>🕰️ Mementos <span>v1.0</span></h1>
        <div class="header-right">
            <button class="btn btn-primary" onclick="abrirModal()">+ Nuevo recuerdo</button>
            <span class="user-badge">
                👤 <span class="username" onclick="openPasswordModal()" title="Cambiar contraseña"><?=htmlspecialchars($_SESSION['username'])?></span>
            </span>
            <button class="btn-logout" onclick="doLogout()">🚪 Salir</button>
        </div>
    </header>

    <input type="text" class="search-box" id="search" placeholder="🔍 Buscar en títulos, contenido o tags..." oninput="cargarRecuerdos()">

    <div class="filtros" id="filtros">
        <button class="filtro-btn active" data-cat="todas" onclick="filtrar('todas',this)">Todas</button>
        <?php
        $db = getDB();
        $cats_data = $db->query("SELECT DISTINCT categoria FROM recuerdos WHERE categoria != '' ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);
        $cats_config = $db->query("SELECT valor FROM config WHERE clave LIKE 'cat_%' ORDER BY valor")->fetchAll(PDO::FETCH_COLUMN);
        $all_cats = array_unique(array_merge($cats_data, $cats_config));
        sort($all_cats);
        foreach ($all_cats as $c) {
            $escaped_c = htmlspecialchars($c, ENT_QUOTES, 'UTF-8');
            echo '<button class="filtro-btn" data-cat="' . $escaped_c . '" onclick="filtrar(\'' . $escaped_c . '\',this)">' . ucfirst($escaped_c) . '</button>';
        }
        ?>
    </div>

    <div class="filtros-tags" id="filtros-tags"></div>

    <div class="memorias-count" id="count"></div>
    <div class="memorias" id="memorias"></div>
</div>

<!-- Modal Recuerdo -->
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
            <select id="recuerdo-categoria" onchange="toggleCategoriaOtra()">
                <?php
                $db2 = getDB();
                $cats = $db2->query("SELECT valor FROM config WHERE clave LIKE 'cat_%' ORDER BY valor")->fetchAll(PDO::FETCH_COLUMN);
                foreach ($cats as $cat) {
                    $sel = ($cat === 'general') ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') . '" ' . $sel . '>' . ucfirst(htmlspecialchars($cat, ENT_QUOTES, 'UTF-8')) . '</option>';
                }
                ?>
                <option value="__otra__">Otra...</option>
            </select>
            <input type="text" id="recuerdo-categoria-otra" placeholder="Escribí la nueva categoría..." style="display:none;margin-top:0.5rem;">
        </div>
        <div class="form-group">
            <label>Tags</label>
            <div class="tag-input-container" id="tag-input-container" onclick="document.getElementById('tag-input').focus()">
                <div class="tag-chips" id="tag-chips"></div>
                <input type="text" class="tag-input" id="tag-input" placeholder="Escribí un tag y apretá Enter..." onkeydown="handleTagInput(event)">
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-sm" onclick="cerrarModal()" style="background:var(--bg-input);color:var(--text-dim);">Cancelar</button>
            <button class="btn btn-primary btn-sm" onclick="guardarRecuerdo()">Guardar</button>
        </div>
    </div>
</div>

<!-- Modal Cambiar Contraseña -->
<div class="modal-overlay" id="modal-password">
    <div class="modal" style="max-width:420px;">
        <h2>🔒 Cambiar Contraseña</h2>
        <div class="modal-msg" id="password-msg"></div>
        <div class="form-group">
            <label>Contraseña actual</label>
            <input type="password" id="pwd-current" placeholder="Tu contraseña actual">
        </div>
        <div class="form-group">
            <label>Nueva contraseña</label>
            <input type="password" id="pwd-new" placeholder="Mínimo 4 caracteres">
        </div>
        <div class="form-group">
            <label>Repetir nueva contraseña</label>
            <input type="password" id="pwd-confirm" placeholder="Repetila acá">
        </div>
        <div class="modal-actions">
            <button class="btn btn-sm" onclick="closePasswordModal()" style="background:var(--bg-input);color:var(--text-dim);">Cancelar</button>
            <button class="btn btn-primary btn-sm" onclick="changePassword()">Cambiar</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<script>
const API = '/mementos/api.php';
const AUTH_API = '/mementos/api/auth.php';

let currentTags = [];
let activeTagFilter = null;

// ─── MODALS ───

function abrirModal(id = null) {
    document.getElementById('modal').classList.add('active');
    document.getElementById('modal-title').textContent = id ? 'Editar Recuerdo' : 'Nuevo Recuerdo';
    if (!id) {
        document.getElementById('recuerdo-id').value = '';
        document.getElementById('recuerdo-titulo').value = '';
        document.getElementById('recuerdo-contenido').value = '';
        document.getElementById('recuerdo-categoria').value = 'general';
        document.getElementById('recuerdo-categoria-otra').value = '';
        document.getElementById('recuerdo-categoria-otra').style.display = 'none';
        currentTags = [];
        renderTagChips();
    }
}

function cerrarModal() {
    document.getElementById('modal').classList.remove('active');
}

function openPasswordModal() {
    document.getElementById('modal-password').classList.add('active');
    document.getElementById('password-msg').style.display = 'none';
    document.getElementById('pwd-current').value = '';
    document.getElementById('pwd-new').value = '';
    document.getElementById('pwd-confirm').value = '';
    document.getElementById('pwd-current').focus();
}

function closePasswordModal() {
    document.getElementById('modal-password').classList.remove('active');
}

// ─── PASSWORD ───

function showMsg(id, text, type) {
    const el = document.getElementById(id);
    el.textContent = text;
    el.className = 'modal-msg ' + type;
    el.style.display = 'block';
}

function showToast(text, type) {
    const el = document.getElementById('toast');
    el.textContent = text;
    el.className = 'toast ' + type + ' show';
    setTimeout(() => el.className = 'toast', 3000);
}

async function changePassword() {
    const current = document.getElementById('pwd-current').value;
    const newPwd = document.getElementById('pwd-new').value;
    const confirm = document.getElementById('pwd-confirm').value;

    if (!current) { showMsg('password-msg', 'Ingresá tu contraseña actual', 'error'); return; }
    if (newPwd.length < 4) { showMsg('password-msg', 'Mínimo 4 caracteres', 'error'); return; }
    if (newPwd !== confirm) { showMsg('password-msg', 'Las contraseñas no coinciden', 'error'); return; }

    try {
        const resp = await fetch(AUTH_API, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'change_password', current_password: current, new_password: newPwd})
        });
        const data = await resp.json();

        if (data.success) {
            showToast('✅ Contraseña cambiada correctamente', 'success');
            setTimeout(() => closePasswordModal(), 1500);
        } else {
            showMsg('password-msg', data.error || 'Error', 'error');
        }
    } catch (e) {
        showMsg('password-msg', 'Error de conexión', 'error');
    }
}

// ─── TAGS ───

function handleTagInput(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const val = e.target.value.trim();
        if (val && !currentTags.includes(val)) {
            currentTags.push(val);
            renderTagChips();
        }
        e.target.value = '';
    } else if (e.key === 'Backspace' && !e.target.value && currentTags.length) {
        currentTags.pop();
        renderTagChips();
    }
}

function renderTagChips() {
    const container = document.getElementById('tag-chips');
    container.innerHTML = currentTags.map((t, i) =>
        `<span class="tag-chip">${escapeHtml(t)} <span class="remove" onclick="removeTag(${i})">×</span></span>`
    ).join('');
}

function removeTag(i) {
    currentTags.splice(i, 1);
    renderTagChips();
}

function filterByTag(tag, btn) {
    // Toggle
    if (activeTagFilter === tag) {
        activeTagFilter = null;
        document.querySelectorAll('.filtro-tag-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('filtros-tags').innerHTML = '';
    } else {
        activeTagFilter = tag;
        document.querySelectorAll('.filtro-tag-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
    }
    cargarRecuerdos();
}

// ─── DATA ───

async function cargarRecuerdos() {
    const search = encodeURIComponent(document.getElementById('search').value);
    const cat = document.querySelector('.filtro-btn.active')?.dataset.cat || 'todas';
    let url = `${API}?action=list&search=${search}&categoria=${encodeURIComponent(cat)}`;
    if (activeTagFilter) {
        url += `&tag=${encodeURIComponent(activeTagFilter)}`;
    }
    try {
        const resp = await fetch(url);
        if (!resp.ok) {
            if (resp.status === 401) { window.location.href = '/mementos/login.php'; return; }
            throw new Error('HTTP ' + resp.status);
        }
        const data = await resp.json();
        renderRecuerdos(data);
        extractAndRenderTags(data);
    } catch (e) { console.error(e); }
}

function extractAndRenderTags(items) {
    const tagSet = new Set();
    items.forEach(r => {
        if (r.tags) r.tags.split(',').forEach(t => { const tt = t.trim(); if (tt) tagSet.add(tt); });
    });

    const container = document.getElementById('filtros-tags');
    if (!tagSet.size) { container.innerHTML = ''; return; }

    const tags = Array.from(tagSet).sort();
    container.innerHTML = '<span class="filtros-tags-label">🏷️ Tags:</span>' + tags.map(t =>
        `<button class="filtro-tag-btn${activeTagFilter === t ? ' active' : ''}" onclick="filterByTag('${escapeJs(t)}',this)">${escapeHtml(t)}</button>`
    ).join('');
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
            ${r.tags ? `<div class="card-tags">${r.tags.split(',').map(t => {
                const tt = t.trim();
                return tt ? `<span class="tag" onclick="setTagFilter('${escapeJs(tt)}')">#${escapeHtml(tt)}</span>` : '';
            }).join('')}</div>` : ''}
            <div class="card-actions">
                <button class="btn btn-sm btn-primary" onclick="editarRecuerdo(${r.id})">Editar</button>
                <button class="btn btn-sm btn-danger" onclick="eliminarRecuerdo(${r.id})">Eliminar</button>
            </div>
        </div>
    `).join('');
}

function escapeJs(s) {
    return s.replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"');
}

function setTagFilter(tag) {
    // Si el tag ya tiene botón de filtro, usarlo
    const btns = document.querySelectorAll('.filtro-tag-btn');
    for (const b of btns) {
        if (b.textContent === tag) {
            filterByTag(tag, b);
            return;
        }
    }
    // Si no, activar filtro directo
    activeTagFilter = tag;
    cargarRecuerdos();
}

// ─── HELPERS ───

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

function toggleCategoriaOtra() {
    const input = document.getElementById('recuerdo-categoria-otra');
    input.style.display = document.getElementById('recuerdo-categoria').value === '__otra__' ? 'block' : 'none';
    if (input.style.display === 'block') input.focus();
}

async function guardarRecuerdo() {
    const id = document.getElementById('recuerdo-id').value;
    const catSelect = document.getElementById('recuerdo-categoria').value;
    const catOtra = document.getElementById('recuerdo-categoria-otra').value.trim();
    const categoria = (catSelect === '__otra__' && catOtra) ? catOtra : catSelect;

    const payload = {
        action: id ? 'update' : 'create',
        id: id || undefined,
        titulo: document.getElementById('recuerdo-titulo').value,
        contenido: document.getElementById('recuerdo-contenido').value,
        categoria: categoria || 'general',
        tags: currentTags.join(', '),
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
            document.getElementById('recuerdo-id').value = r.data.id;

            // Tags
            currentTags = r.data.tags ? r.data.tags.split(',').map(t => t.trim()).filter(t => t) : [];
            renderTagChips();

            const cat = r.data.categoria || 'general';
            const sel = document.getElementById('recuerdo-categoria');
            let found = false;
            for (let i = 0; i < sel.options.length; i++) {
                if (sel.options[i].value === cat) { sel.selectedIndex = i; found = true; break; }
            }
            if (!found) {
                sel.value = '__otra__';
                document.getElementById('recuerdo-categoria-otra').value = cat;
                document.getElementById('recuerdo-categoria-otra').style.display = 'block';
            }
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

async function doLogout() {
    try {
        await fetch(AUTH_API, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'logout'})
        });
    } catch (e) {}
    window.location.href = '/mementos/login.php';
}

cargarRecuerdos();
</script>
</body>
</html>
