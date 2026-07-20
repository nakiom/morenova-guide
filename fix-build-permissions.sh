#!/bin/bash
# Script para corregir permisos del directorio public/build

echo "Corrigiendo permisos de public/build..."
sudo chown -R $(whoami):$(whoami) public/build/
sudo chmod -R 755 public/build/
echo "✅ Permisos corregidos. Ahora puedes ejecutar: npm run build"
