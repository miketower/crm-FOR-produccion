# Roles y accesos

Los administradores pueden abrir **Configuración → Roles y accesos** para:

- Crear un rol y definir las pantallas que puede consultar o modificar.
- Cambiar los permisos de los roles existentes.
- Asignar uno o varios roles a cada usuario y su canal comercial, cuando aplique.

**Equipo / Usuarios → Nuevo usuario** permite seleccionar los roles antes de guardar
y muestra un resumen de sus accesos. Los permisos de varios roles se suman.
Un rol sin permisos deja al usuario sin módulos; su perfil y cierre de sesión siguen
accesibles. Los cambios se consultan de nuevo en cada solicitud, sin cerrar sesión.

`Consultar` incluye abrir pantallas y descargar información. `Modificar` incluye
registrar, editar y eliminar en el módulo, e implica también consultar. El permiso
se aplica en el servidor a las rutas, incluidas las peticiones AJAX. Ocultar un
menú no es el mecanismo de seguridad. Los botones internos de las pantallas
existentes pueden seguir apareciendo, pero el servidor rechaza las acciones sin
permiso con HTTP 403.

Solo el rol protegido `admin` puede crear usuarios y administrar accesos. No se
puede editar ese rol, quitarse uno mismo de él, ni eliminar al último administrador
activo. Los formularios administrativos requieren CSRF. Las cuentas inactivas o
eliminadas pierden el acceso aunque tengan una sesión abierta.

## Actualización desde GitHub y phpMyAdmin

Los cambios de código se reciben con `git pull --ff-only origin main` y las
versiones exactas de las dependencias con `composer install`. PHP debe ser 8.3 o
8.4 tanto en MAMP como en la terminal: Git no cambia la versión de PHP instalada.
La configuración `.env` local se conserva y no debe copiarse desde el entorno cloud.

Para actualizar la base sin ejecutar comandos PHP, selecciona la base local del
CRM en phpMyAdmin y usa **Importar** con
`database/updates/2026-10-26_roles_accesos.sql`. El script está destinado a MySQL
5.7+ o MariaDB, con los nombres de tablas sin prefijo del esquema original.
Exporta un respaldo antes de importarlo. Conserva los usuarios y registros del
CRM, convierte sus asignaciones iniciales y registra la misma migración que
`access:install`. Si ya está instalada, conserva los permisos configurados.

El SQL y el comando de instalación son alternativas; basta con ejecutar uno.
El SQL actualiza la base, pero no resuelve una versión de PHP incompatible.

## Instalación alternativa con la terminal

Desde la raíz, con la conexión de desarrollo configurada:

```sh
composer install
php spark access:install
```

`access:install` ejecuta **solo** la nueva migración de accesos y registra su
historial. Es repetible: si ya está instalada no revierte ni vuelve a sembrar los
roles. Evita ejecutar `migrate` indiscriminadamente sobre el SQL importado: ese
archivo ya contiene las tablas de las migraciones históricas, pero no sus filas
de historial.

La migración agrega `access_roles`, `access_permissions`,
`access_role_permissions`, `access_user_roles` y `users.commercial_channel`.
Convierte una sola vez las excepciones conocidas por correo a asignaciones de
roles y canales. Los perfiles iniciales conservan las combinaciones del menú
anterior; conviene revisarlos antes de desplegar cambios en producción.
El código de autorización y el menú ya no utilizan direcciones de correo.
Las cuentas agregadas después mediante SQL deben recibir roles explícitamente.

Los permisos especiales de alcance conservan los filtros existentes de los
listados de perfilamiento, carga semanal y operaciones. Este cambio no sustituye
una revisión de autorización por registro en todos los controladores existentes.
Los canales comerciales ahora se guardan por usuario en lugar de deducirse del
correo. Sin canal ni permiso para todos los canales, se deniega la carga semanal.

La pantalla TV de vacantes y su feed JSON permanecen públicos, como estaban antes.

## Desarrollo y pruebas

`app/Config/Access.php` define módulos, permisos y la clasificación de las acciones
ruteadas. `Authorization` resuelve roles, menú y pantalla de inicio. `AccessFilter`
protege las rutas antes de ejecutar los controladores; una acción sin clasificación
no recibe acceso por defecto. Para agregar un módulo hay que añadir su definición,
clasificar sus acciones y crear los permisos de base de datos con una migración.

```sh
composer test
```

La suite prueba las pantallas administrativas, creación de usuarios con roles,
conversión de accesos anteriores, combinación y revocación de permisos,
rechazo de escrituras/administración sin permiso, CSRF y protección del administrador.
Las pruebas usan SQLite en memoria; no modifican la base del CRM.
