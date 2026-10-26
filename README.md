# CRM FOR

Aplicación CodeIgniter con administración de usuarios, roles y permisos.
Esta versión funciona con **PHP 8.0.3** (MAMP) y Composer 2. Usa el PHP de MAMP tanto
en Apache como en la terminal. `git pull` no actualiza PHP.

## Recibir actualizaciones

Desde la carpeta del proyecto:

```sh
git status
git pull --ff-only origin main
composer install
composer check-platform-reqs
```

Si hay cambios locales, consérvalos antes de actualizar. No uses `reset --hard`
para resolver un conflicto sin revisar esos cambios. El archivo `.env` local
contiene tu conexión a MAMP y no se debe reemplazar con el del entorno cloud.

## Arrancar con MAMP PHP 8.0.3 en macOS

En la misma terminal donde está el proyecto:

```sh
export PATH="/Applications/MAMP/bin/php/php8.0.3/bin:$PATH"
php -v
composer install
composer check-platform-reqs
php spark serve
```

Conserva la conexión local de `.env` y mantén MySQL de MAMP encendido.
El lockfile se resuelve contra PHP 8.0.3 para conservar la compatibilidad; la
aplicación y las pruebas usan CodeIgniter 4.4 y PHPUnit 9.6. Este lockfile está
destinado al entorno local con PHP 8.0.3; PHP 8.3/8.4 necesita otra resolución de
dependencias y no es el objetivo de esta entrega.

## Actualizar la base desde phpMyAdmin

1. Exporta un respaldo de la base local.
2. Selecciona esa misma base en phpMyAdmin.
3. En **Importar**, selecciona `database/updates/2026-10-26_roles_accesos.sql`.
4. Inicia sesión con tu administrador y abre **Configuración → Roles y accesos**.

El SQL agrega las tablas y asignaciones iniciales sin reemplazar los registros del
CRM. Puede repetirse sin restablecer los permisos que ya se hayan configurado.
También registra la migración para que `php spark access:install` no la repita.
Los detalles de permisos y la alternativa por terminal están en [docs/accesos.md](docs/accesos.md).

## Pruebas

```sh
composer test
```

La suite usa SQLite en memoria. Requisitos y cobertura opcional en
[tests/README.md](tests/README.md).
