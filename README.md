# Mementos

Sistema de recuerdos y memorias personales.

## Stack

- **PHP 8.3** (vanilla)
- **SQLite** (PDO)
- **JavaScript** (vanilla, fetch API)
- **CSS** (dark theme)

## Acceso

- Desarrollo: `http://192.168.100.214:8081/mementos`

## Estructura

```
mementos/
├── index.php      # Frontend (UI + JS)
├── api.php        # API REST (CRUD)
└── mementos.db    # Base de datos SQLite
```

## Características

- CRUD completo de recuerdos
- Categorías y tags
- Búsqueda en tiempo real
- Filtro por categoría
- Dark theme
- Responsive mobile

## Base de datos

Tabla `recuerdos`:
- `id` - INTEGER PK
- `titulo` - TEXT
- `contenido` - TEXT
- `categoria` - TEXT (default: 'general')
- `tags` - TEXT (separados por coma)
- `creado` - DATETIME
- `modificado` - DATETIME
