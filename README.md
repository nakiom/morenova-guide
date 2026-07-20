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

## Instalación

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configurar MySQL en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=morenova
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
APP_URL=http://morenova.local
APP_LOCALE=es
```

```bash
php artisan migrate --seed
php artisan storage:link
npm run build
```

## Usuarios de seed

| Rol | Email | Contraseña |
|-----|-------|------------|
| Admin | `admin@morenova.com` | `password` |
| Centro CCU | `maurommeo@gmail.com` | `ccu123` |
| Resto de usuarios CRM | sus emails | `password` |

## Datos sembrados

Los seeders cargan el estado actual de producción local:

- 2 categorías + imágenes en `database/seeders/assets/`
- 7 patologías (FSD, SUI, Vulvodinia, GSM, ED, CPPS/CP, PD)
- Criterios, pasos, checklists, seguimientos y áreas desde `database/seeders/data/*.json`
- 3 centros (CCU, CUIN, Urolit), 6 usuarios y 3 equipos

Para reinstalar contenido clínico/CRM sin tocar migraciones:

```bash
php artisan db:seed
```

## Estructura relevante

- `database/seeders/data/` — fixtures JSON del contenido clínico
- `database/seeders/assets/` — imágenes de categorías y patologías
- `resources/documentation/` — protocolos fuente (fem/men)
- `lang/` — traducciones de UI

## Desarrollo

```bash
npm run dev
# otra terminal
php artisan serve
```

## Licencia

Proyecto privado — DirexGroup.
