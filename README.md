# 🕰️ Mementos

Sistema de recuerdos y memorias personales. Simple, sin dependencias, listo para usar.

## Stack

- **PHP 8.3+** (vanilla, sin frameworks)
- **SQLite** (PDO)
- **JavaScript** (vanilla, fetch API)
- **CSS** (dark theme, responsive)

## Características

- CRUD completo de recuerdos
- Categorías y tags
- Búsqueda en tiempo real
- Filtro por categoría
- Dark theme
- Responsive mobile
- Sin dependencias externas
- Instalación en 1 minuto

## Instalación

```bash
# Clonar
git clone https://github.com/EduardoCohen/mementos.git
cd mementos

# Iniciar servidor PHP
php -S localhost:8080

# Abrir en el navegador
# http://localhost:8080
```

O si ya tenés un servidor PHP corriendo, simplemente copiá la carpeta al document root.

## Estructura

```
mementos/
├── index.php      # Frontend (UI + JS)
├── api.php        # API REST (CRUD)
├── mementos.db    # Base de datos SQLite (se crea automáticamente)
└── README.md
```

## API

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `api.php?action=list` | Listar recuerdos |
| GET | `api.php?action=list&search=texto` | Buscar recuerdos |
| GET | `api.php?action=list&categoria=cat` | Filtrar por categoría |
| GET | `api.php?action=get&id=1` | Obtener un recuerdo |
| POST | `api.php` | Crear/actualizar/eliminar |

### POST actions

```json
// Crear
{ "action": "create", "titulo": "...", "contenido": "...", "categoria": "general", "tags": "tag1,tag2" }

// Actualizar
{ "action": "update", "id": 1, "titulo": "...", "contenido": "...", "categoria": "general", "tags": "tag1,tag2" }

// Eliminar
{ "action": "delete", "id": 1 }
```

## Base de datos

Tabla `recuerdos`:
- `id` - INTEGER PK AUTOINCREMENT
- `titulo` - TEXT NOT NULL
- `contenido` - TEXT
- `categoria` - TEXT (default: 'general')
- `tags` - TEXT (separados por coma)
- `creado` - DATETIME
- `modificado` - DATETIME

Tabla `config`:
- `clave` - TEXT PRIMARY KEY
- `valor` - TEXT

## Licencia

MIT
