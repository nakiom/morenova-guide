# Sistema de Guía Rápida Morenova

Guía clínica MoreNova (Laravel 12, Bootstrap 5, MySQL) con CRM de centros, usuarios y equipos.

## Características

- Categorías Women's / Men's Health con fondos e íconos
- Patologías, protocolos, criterios, checklists, seguimientos y áreas de aplicación
- Multiidioma: es / en / pt / zh
- CRM admin: centros, usuarios, equipos
- Sesiones clínicas y cuestionarios
- Panel de administración

## Requisitos

- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js y npm

## Instalación rápida (local)

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
# Configurar DB en .env
php artisan migrate --seed
php artisan storage:link
npm run build
```

Para desarrollo local: `APP_URL=http://morenova.local` y `FORCE_HTTPS=false`.

## Producción (subdominio)

Guía completa: **[deploy/INSTALACION.md](deploy/INSTALACION.md)**

Script de enlaces y permisos:

```bash
chmod +x deploy/fix-permissions.sh
./deploy/fix-permissions.sh
# o: ./deploy/fix-permissions.sh --user http --group http
```

Apache: `deploy/apache-morenova.conf`

## Usuarios de seed

| Rol | Email | Contraseña |
|-----|-------|------------|
| Admin | `admin@morenova.com` | `password` |
| Centro CCU | `maurommeo@gmail.com` | `ccu123` |
| Resto de usuarios CRM | sus emails | `password` |

## Datos sembrados

Los seeders cargan el estado actual de producción local:

- Categorías + imágenes en `database/seeders/assets/`
- Patologías, criterios, pasos, checklists, seguimientos y áreas desde `database/seeders/data/*.json`
- Centros, usuarios y equipos CRM

```bash
php artisan db:seed
```

## Estructura relevante

- `deploy/` — instalación, Apache, script de permisos
- `database/seeders/data/` — fixtures JSON del contenido clínico
- `lang/` — traducciones de UI

## Desarrollo

```bash
npm run dev
# otra terminal
php artisan serve
```

## Licencia

Proyecto privado — DirexGroup.
