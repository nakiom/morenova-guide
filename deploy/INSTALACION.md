# Guía de instalación — MoreNova en subdominio

Dominio objetivo: **https://morenova.dematech.com.ar**

Esta guía asume un servidor Linux con Apache (o nginx + PHP-FPM), PHP 8.2+, Composer, MySQL/MariaDB y Node.js 18+.

---

## 1. DNS

Creá un registro **A** (o CNAME) apuntando a la IP del servidor:

```
morenova.dematech.com.ar  →  <IP_DEL_SERVIDOR>
```

Esperá la propagación DNS antes de pedir el certificado SSL.

---

## 2. Código en el servidor

```bash
# Ejemplo
cd /srv/http
git clone <repo> morenova
cd morenova
```

O subí el proyecto por rsync/scp a `/srv/http/morenova`.

El **DocumentRoot** debe ser la carpeta `public/`, no la raíz del repo.

---

## 3. Dependencias

```bash
cd /srv/http/morenova
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
npm ci
npm run build
```

---

## 4. Variables de entorno (`.env`)

Editá `.env` con al menos:

```env
APP_NAME=MoreNova
APP_ENV=production
APP_DEBUG=false
APP_URL=https://morenova.dematech.com.ar
FORCE_HTTPS=true

APP_TIMEZONE=America/Argentina/Buenos_Aires
APP_LOCALE=es
APP_FALLBACK_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=morenova
DB_USERNAME=morenova
DB_PASSWORD=<secreto>

SESSION_DRIVER=database
SESSION_LIFETIME=480
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

MAIL_MAILER=mailgun
MAIL_FROM_ADDRESS=noreply@tu-dominio-mailgun
MAIL_FROM_NAME="MoreNova"
MAILGUN_DOMAIN=tu-dominio.mailgun.org
MAILGUN_SECRET=<api-key>
MAILGUN_ENDPOINT=api.mailgun.net
```

Notas:

- `SESSION_DOMAIN=null` → cookie solo para este subdominio (recomendado).
- `FORCE_HTTPS=true` y `SESSION_SECURE_COOKIE=true` requieren HTTPS real.
- Sin SSL todavía: usá `http://…`, `FORCE_HTTPS=false` y `SESSION_SECURE_COOKIE=false`.

---

## 5. Base de datos

```bash
mysql -u root -p -e "CREATE DATABASE morenova CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p -e "CREATE USER 'morenova'@'localhost' IDENTIFIED BY '<secreto>';"
mysql -u root -p -e "GRANT ALL ON morenova.* TO 'morenova'@'localhost'; FLUSH PRIVILEGES;"

cd /srv/http/morenova
php artisan migrate --force
php artisan db:seed --force   # opcional: datos iniciales
```

---

## 6. Enlaces simbólicos y permisos

El script pide **usuario** y **grupo** del servidor web (en Arch suele ser `http`; en Debian/Ubuntu `www-data`).

```bash
cd /srv/http/morenova
chmod +x deploy/fix-permissions.sh
./deploy/fix-permissions.sh
# o sin prompt:
./deploy/fix-permissions.sh --user http --group http
```

Qué hace:

1. Crea `storage/` y `bootstrap/cache/` si faltan
2. Regenera `public/storage` → `storage/app/public`
3. Asigna dueño/grupo y permisos 2775/664 en carpetas escribibles
4. Aplica ACL al usuario web si `setfacl` está disponible
5. Limpia caches de Laravel

Probá en seco:

```bash
./deploy/fix-permissions.sh --dry-run --user http --group http
```

---

## 7. Apache

Plantilla lista en `deploy/apache-morenova.conf`.

```bash
sudo cp deploy/apache-morenova.conf /etc/httpd/conf/extra/morenova.conf
# Asegurate de que httpd.conf incluya:
# Include conf/extra/morenova.conf
# o agregá el VirtualHost en httpd-vhosts.conf

sudo apachectl configtest
sudo systemctl reload httpd
```

Puntos clave del vhost:

- `ServerName morenova.dematech.com.ar`
- `DocumentRoot "/srv/http/morenova/public"`
- `AllowOverride All` (para el `.htaccess` de Laravel)

### HTTPS (Let's Encrypt)

```bash
sudo certbot --apache -d morenova.dematech.com.ar
```

Si el SSL termina en un proxy (Cloudflare, panel dematech), el vhost puede quedar en HTTP interno; Laravel ya confía en proxies (`TrustProxies`) y fuerza `https` con `FORCE_HTTPS`.

---

## 8. Nginx (alternativa)

```nginx
server {
    listen 80;
    server_name morenova.dematech.com.ar;
    root /srv/http/morenova/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php-fpm/php-fpm.sock;  # ajustar socket
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Luego SSL con certbot o el proxy del hosting.

---

## 9. Caches de producción

```bash
cd /srv/http/morenova
php artisan config:cache
php artisan route:cache
php artisan view:cache
# opcional: cola
# php artisan queue:work --daemon   # o systemd unit
```

Tras cambiar `.env`:

```bash
php artisan optimize:clear
php artisan config:cache
```

---

## 10. Verificación rápida

| Check | Cómo |
|-------|------|
| Home | Abrí `https://morenova.dematech.com.ar` |
| Health | `https://morenova.dematech.com.ar/up` |
| Login admin | `admin@morenova.com` / (clave del seed o la que hayas seteado) |
| Storage | Subí logo de centro; debe verse en `/storage/...` |
| Mails | Envío de cuestionario; el link debe ser `https://morenova.dematech.com.ar/q/...` |

---

## 11. Problemas frecuentes

**419 Page Expired / sesión no persiste**  
- `APP_URL` debe coincidir con el host real (https + subdominio)  
- `SESSION_SECURE_COOKIE=true` solo con HTTPS  
- No uses `SESSION_DOMAIN=.dematech.com.ar` salvo que quieras compartir cookie entre subdominios

**Permission denied en `storage/`**  
```bash
./deploy/fix-permissions.sh --user http --group http
```

**CSS/JS viejos**  
```bash
npm run build
php artisan view:clear
```

**Enlace `/storage` roto**  
```bash
./deploy/fix-permissions.sh
ls -la public/storage   # debe apuntar a .../storage/app/public
```

**Imágenes en mails**  
Los logos se incrustan (CID). Los links del cuestionario usan `APP_URL`: tiene que ser el subdominio público.

---

## Cron — recordatorios de citas

El sistema envía emails de recordatorio de visita con el comando `visits:send-reminders` (programado todos los días a las 08:00).

Agregá en el crontab del usuario del servidor web:

```bash
* * * * * cd /srv/http/morenova && php artisan schedule:run >> /dev/null 2>&1
```

Para probar manualmente:

```bash
php artisan visits:send-reminders
```

---

## Resumen de comandos (instalación limpia)

```bash
cd /srv/http/morenova
composer install --no-dev --optimize-autoloader
cp .env.example .env && nano .env          # APP_URL, DB, Mailgun, FORCE_HTTPS
php artisan key:generate
php artisan migrate --force
npm ci && npm run build
chmod +x deploy/fix-permissions.sh
./deploy/fix-permissions.sh --user http --group http
sudo cp deploy/apache-morenova.conf /etc/httpd/conf/extra/morenova.conf
sudo apachectl configtest && sudo systemctl reload httpd
sudo certbot --apache -d morenova.dematech.com.ar
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Listo: el sistema debería responder en **https://morenova.dematech.com.ar**.
