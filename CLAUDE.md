# php-docker-starter-apache-mysql

- Stack de un solo contenedor: `php:8.4-apache` con mod_php (no PHP-FPM, no Nginx separado). Para un proyecto plano sin framework no hace falta el desacople que sí tiene sentido en `laravel-docker-starter-ngxMsql`.
- A diferencia de los starters de Laravel, `src/` **esta versionado** (no gitignoreado): no hay `composer create-project` que lo regenere, el codigo PHP plano es el proyecto en si.
- Docroot en `src/public` (lo que expone Apache); codigo privado (helpers, conexion a DB) va en `src/app`, fuera del docroot.
- Las credenciales de MySQL se inyectan como `environment:` del servicio `app` en `docker-compose.yml`, no en un `.env` dentro de `src/`. `src/app/db.php` las lee con `getenv()` (no `$_ENV`, que depende de `variables_order` en `php.ini`).
- El schema de `docker/mysql/init/01-schema.sql` solo corre en el primer arranque del volumen `mysql-data` (comportamiento estandar de `docker-entrypoint-initdb.d`). Para reaplicarlo hace falta recrear el volumen: `make fresh`.
- Todo comando (composer, php, lo que sea) corre via `docker compose exec app ...` o `make shell`. No hay PHP instalado en el host a proposito.
- El Dockerfile **no agrega `USER`**: Apache necesita arrancar como root para poder bindear el puerto 80, y el propio proceso maestro baja los workers a `www-data` solo. Los build args `UID`/`GID` remapean `www-data` (via `usermod`/`groupmod`) en vez de crear un usuario nuevo, porque `www-data` ya existe en la imagen base.
