# Matriz de Desempeño y Resultados Full Skills

La pantalla es una propuesta visual de solo lectura. Sus cifras y estados son
ficticios; todavía no se calculan a partir de los registros del CRM.

## Habilitar el acceso

En la base del CRM, ejecuta `database/updates/2026-11-18_full_skills_access.sql`.
El script asigna las tres cuentas indicadas si existen, están activas y no han
sido eliminadas. Al terminar, muestra los usuarios asignados; comprueba que
aparezcan los tres. Si falta alguno, revisa su cuenta y vuelve a ejecutar el
script. No modifica las asignaciones existentes al repetirse.

La autorización comprueba el ID y el correo de cada cuenta; otros usuarios,
incluidos los administradores, no acceden. Si la tabla aún no existe, nadie
puede acceder. Si cambia el correo o la cuenta de alguna de las tres personas,
la asignación y la comprobación de correo deben revisarse expresamente.

La pantalla se encuentra en **Estrategia → Matriz Full Skills** para las
cuentas autorizadas. No requiere acceso a otros módulos ni cambia sus permisos.
