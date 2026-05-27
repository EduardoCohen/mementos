<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/SQLite-003B57?style=for-the-badge&logo=sqlite&logoColor=white" alt="SQLite">
  <img src="https://img.shields.io/badge/Vanilla_JS-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript">
</p>

<h1 align="center">🕰️ Mementos</h1>

<p align="center">
  <strong>Sistema de gestión de recuerdos y memorias personales</strong><br>
  Interfaz dark theme con tags, categorías, autenticación y filtros avanzados.<br>
  Sin dependencias externas — PHP built-in server + SQLite.
</p>

<br>

<p align="center">
  <img src="https://img.shields.io/badge/CRUD_recuerdos-✓-2ea043?style=flat-square">
  <img src="https://img.shields.io/badge/sistema_auth-✓-2ea043?style=flat-square">
  <img src="https://img.shields.io/badge/tags_interactivos-✓-2ea043?style=flat-square">
  <img src="https://img.shields.io/badge/filtros_múltiples-✓-2ea043?style=flat-square">
  <img src="https://img.shields.io/badge/búsqueda_tiempo_real-✓-2ea043?style=flat-square">
  <img src="https://img.shields.io/badge/dark_theme-✓-2ea043?style=flat-square">
  <img src="https://img.shields.io/badge/responsive-✓-2ea043?style=flat-square">
  <img src="https://img.shields.io/badge/zero_dependencies-✓-2ea043?style=flat-square">
</p>

---

## Características

- **CRUD completo** de recuerdos (título, contenido, categoría, tags)
- **Sistema de autenticación** con login/logout y sesiones PHP
- **Cambio de contraseña** desde el header (click en el nombre de usuario)
- **Tags interactivos**: chips visuales al editar, clickeables en las cards
- **Filtros múltiples**: por categoría + por tag + búsqueda libre (combinables)
- **Búsqueda en tiempo real** (títulos, contenido y tags)
- **Dark theme** responsive (mobile-first)
- **Toast notifications** para feedback de acciones

## Stack

| Componente | Tecnología |
|-----------|-----------|
| Backend | PHP 8.3+ (PDO) |
| Base de datos | SQLite 3 |
| Frontend | HTML5 + CSS3 + JavaScript Vanilla |
| Auth | bcrypt + sesiones PHP |

## Acceso

- Desarrollo: `http://192.168.100.214:8081/mementos`
- Samba: `\\192.168.100.214\desarrollo\mementos`
- GitHub: `https://github.com/EduardoCohen/memberos` (privado)

## Credenciales

| Usuario | Contraseña |
|---------|------------|
| `admin` | `mementos2026` |

> Para cambiar la contraseña: hacé click en el nombre de usuario en el header.

## Instalación

```bash
git clone https://github.com/EduardoCohen/mementos.git
cd mementos
php -S localhost:8080
# Abrir http://localhost:8080
```

## Estructura

```
mementos/
├── bootstrap.php      # Inicialización (DB + sesiones)
├── index.php          # Frontend (UI + JS) — protegido por auth
├── login.php          # Página de login
├── api.php            # API REST de recuerdos (CRUD)
├── api/
│   └── auth.php       # API de autenticación
├── classes/
│   └── Auth.php       # Clase de autenticación (bcrypt, sesiones)
├── mementos.db        # Base de datos SQLite (auto-creada)
├── .gitignore
└── README.md
```

## API

### Recuerdos (`api.php`)

Requiere autenticación (401 si no hay sesión).

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `api.php?action=list` | Listar recuerdos |
| GET | `api.php?action=list&search=texto` | Buscar |
| GET | `api.php?action=list&categoria=cat` | Filtrar por categoría |
| GET | `api.php?action=list&tag=tag` | Filtrar por tag |
| GET | `api.php?action=get&id=1` | Obtener un recuerdo |
| POST | `api.php` | Crear/actualizar/eliminar |

### Autenticación (`api/auth.php`)

| Método | Endpoint | Auth | Descripción |
|--------|----------|------|-------------|
| GET | `api/auth.php?action=check` | No | Verificar sesión |
| POST | `api/auth.php` login | No | Iniciar sesión |
| POST | `api/auth.php` logout | Sí | Cerrar sesión |
| POST | `api/auth.php` change_password | Sí | Cambiar contraseña |

## Changelog

### v1.4 (2026-05-27)
- ✅ Fix bug auth admin (hash corrupto → login fallaba)
- ✅ Fix bootstrap.php: auto-reparación de hash inválido
- ✅ Verificado flujo completo: login → list → CRUD

### v1.3 (2026-05-24)
- ✅ Tags con chips visuales, barra de filtros dinámica
- ✅ Tags clickeables en cards
- ✅ Username clickeable → modal cambio contraseña

### v1.2 (2026-05-24)
- ✅ Sistema de autenticación completo (login/logout/sesiones)
- ✅ Fix ruta API en JS: `/mementos/api.php` (absoluta)

### v1.0 (2026-05-23)
- ✅ Versión inicial

## Licencia

MIT

## Autor

<a href="https://eduardocohen.com" target="_blank" rel="noopener noreferrer">Eduardo Cohen</a> - 2026 — 🇦🇷 Posadas, Misiones
