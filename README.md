# CRM FOR

Aplicación CodeIgniter con administración de usuarios, roles y permisos.
Esta versión requiere **PHP 8.3 o 8.4** y Composer 2. La versión de PHP de MAMP
(Apache) y la de la terminal deben coincidir. `git pull` no actualiza PHP.

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
