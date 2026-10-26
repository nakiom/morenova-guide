#!/usr/bin/env bash
# Regenera enlaces simbólicos de Laravel y corrige dueño/permisos.
# Uso:
#   ./deploy/fix-permissions.sh
#   ./deploy/fix-permissions.sh --user http --group http
#   ./deploy/fix-permissions.sh --dry-run

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

DRY_RUN=0
OWNER=""
GROUP=""

while [[ $# -gt 0 ]]; do
  case "$1" in
    --user|-u) OWNER="${2:-}"; shift 2 ;;
    --group|-g) GROUP="${2:-}"; shift 2 ;;
    --dry-run) DRY_RUN=1; shift ;;
    -h|--help)
      cat <<EOF
Uso: $(basename "$0") [--user USUARIO] [--group GRUPO] [--dry-run]

Regenera public/storage → storage/app/public y aplica dueño/permisos
a storage/, bootstrap/cache/ y public/storage.
EOF
      exit 0
      ;;
    *)
      echo "Opción desconocida: $1" >&2
      exit 1
      ;;
  esac
done

detect_web_user() {
  if id -u http &>/dev/null; then
    echo http
  elif id -u www-data &>/dev/null; then
    echo www-data
  elif id -u apache &>/dev/null; then
    echo apache
  elif id -u nginx &>/dev/null; then
    echo nginx
  else
    echo "$(id -un)"
  fi
}

detect_web_group() {
  local user="$1"
  if getent group "$user" &>/dev/null; then
    echo "$user"
  else
    id -gn "$user" 2>/dev/null || id -gn
  fi
}

run() {
  if [[ "$DRY_RUN" -eq 1 ]]; then
    echo "  [dry-run] $*"
  else
    "$@"
  fi
}

DEFAULT_OWNER="$(detect_web_user)"
DEFAULT_GROUP="$(detect_web_group "$DEFAULT_OWNER")"

if [[ -z "$OWNER" ]]; then
  read -r -p "Usuario del servidor web [$DEFAULT_OWNER]: " OWNER
  OWNER="${OWNER:-$DEFAULT_OWNER}"
fi

if [[ -z "$GROUP" ]]; then
  read -r -p "Grupo del servidor web [$DEFAULT_GROUP]: " GROUP
  GROUP="${GROUP:-$DEFAULT_GROUP}"
fi

if ! id -u "$OWNER" &>/dev/null; then
  echo "Error: el usuario '$OWNER' no existe." >&2
  exit 1
fi

if ! getent group "$GROUP" &>/dev/null; then
  echo "Error: el grupo '$GROUP' no existe." >&2
  exit 1
fi

echo
echo "==> Proyecto: $ROOT"
echo "==> Dueño:    $OWNER:$GROUP"
[[ "$DRY_RUN" -eq 1 ]] && echo "==> Modo dry-run (no se aplican cambios)"
echo

echo "==> Creando directorios de Laravel…"
DIRS=(
  storage/app
  storage/app/public
  storage/app/private
  storage/framework
  storage/framework/cache
  storage/framework/cache/data
  storage/framework/sessions
  storage/framework/views
  storage/framework/testing
  storage/logs
  bootstrap/cache
)
for d in "${DIRS[@]}"; do
  run mkdir -p "$ROOT/$d"
done

echo "==> Regenerando enlace simbólico public/storage…"
if [[ -L "$ROOT/public/storage" || -e "$ROOT/public/storage" ]]; then
  run rm -rf "$ROOT/public/storage"
fi

if [[ -x "$ROOT/artisan" ]] && command -v php &>/dev/null; then
  if [[ "$DRY_RUN" -eq 1 ]]; then
    echo "  [dry-run] php artisan storage:link"
  else
    php artisan storage:link --force 2>/dev/null || php artisan storage:link
  fi
else
  run ln -sfn "$ROOT/storage/app/public" "$ROOT/public/storage"
fi

# Asegurar que el link apunte al path absoluto correcto
TARGET="$(readlink -f "$ROOT/storage/app/public" 2>/dev/null || true)"
CURRENT="$(readlink -f "$ROOT/public/storage" 2>/dev/null || true)"
if [[ -n "$TARGET" && "$CURRENT" != "$TARGET" ]]; then
  echo "  Corrigiendo destino del symlink…"
  run rm -rf "$ROOT/public/storage"
  run ln -sfn "$ROOT/storage/app/public" "$ROOT/public/storage"
fi

echo "==> Aplicando dueño $OWNER:$GROUP…"
# Solo lo que el web necesita escribir; no tocar vendor/node_modules enteros.
run chown -R "$OWNER:$GROUP" \
  "$ROOT/storage" \
  "$ROOT/bootstrap/cache" \
  "$ROOT/public/storage"

# El resto del proyecto: dueño del deploy (usuario que corre el script) + grupo web
DEPLOY_USER="$(id -un)"
if [[ "$DEPLOY_USER" != "$OWNER" ]]; then
  echo "==> Ajustando grupo web en código (sin cambiar dueño de archivos de deploy)…"
  run chgrp -R "$GROUP" \
    "$ROOT/storage" \
    "$ROOT/bootstrap/cache" \
    "$ROOT/public" \
    2>/dev/null || true
fi

echo "==> Aplicando permisos…"
# Directorios escribibles
run find "$ROOT/storage" "$ROOT/bootstrap/cache" -type d -exec chmod 2775 {} +
# Archivos escribibles
run find "$ROOT/storage" "$ROOT/bootstrap/cache" -type f -exec chmod 664 {} +
# public: lectura para el servidor
run find "$ROOT/public" -type d -exec chmod 775 {} +
run find "$ROOT/public" -type f -exec chmod 664 {} +
# artisan ejecutable
[[ -f "$ROOT/artisan" ]] && run chmod 775 "$ROOT/artisan"

# ACL opcional: el usuario web siempre puede escribir aunque el dueño sea otro
if command -v setfacl &>/dev/null; then
  echo "==> Aplicando ACL para $OWNER (si el FS lo permite)…"
  run setfacl -R -m "u:${OWNER}:rwx" "$ROOT/storage" "$ROOT/bootstrap/cache" 2>/dev/null || true
  run setfacl -R -d -m "u:${OWNER}:rwx" "$ROOT/storage" "$ROOT/bootstrap/cache" 2>/dev/null || true
fi

echo "==> Limpiando caches de Laravel…"
if [[ -x "$ROOT/artisan" ]] && command -v php &>/dev/null && [[ "$DRY_RUN" -eq 0 ]]; then
  php artisan optimize:clear 2>/dev/null || true
fi

echo
echo "Listo."
echo "  Enlace: $(ls -ld "$ROOT/public/storage" 2>/dev/null || echo 'no existe')"
echo "  storage/: $(ls -ld "$ROOT/storage" | awk '{print $1,$3,$4}')"
echo
echo "Si Apache/nginx sigue sin escribir, verificá SELinux/AppArmor o el DocumentRoot en public/."
