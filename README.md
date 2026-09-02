# php-docker-starter-apache-mysql

Plantilla de GitHub para levantar un proyecto PHP plano (sin framework) dockerizado, con **Apache + mod_php + MySQL 8**, en cualquier maquina, con un solo comando. Pensada para practicar, resolver ejercicios o arrancar un proyecto chico sin instalar PHP, Composer ni MySQL en el host.

## Requisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (o Docker Engine + Compose plugin) corriendo.
- [GitHub CLI](https://cli.github.com/) (`gh`) para crear proyectos nuevos desde la terminal. Alternativa: boton **"Use this template"** en GitHub.

## Crear un proyecto nuevo desde este template

```bash
gh repo create mi-proyecto --template TU_USUARIO/php-docker-starter-apache-mysql --private --clone
cd mi-proyecto
make install
```

Al terminar, la app esta en **http://localhost:8080** y Adminer en **http://localhost:8081**.

## Arquitectura

Un solo contenedor de aplicacion (`php:8.4-apache`, mod_php) mas MySQL y Adminer:

| Servicio | Imagen | Rol |
|---|---|---|
| `app` | build propio, `php:8.4-apache` | Apache y PHP en el mismo proceso. Publica `APP_PORT` (default 8080). |
| `mysql` | `mysql:8` | Base de datos, volumen persistente `mysql-data` + healthcheck. `app` espera a que este *healthy* antes de arrancar. |
| `adminer` | `adminer` | Cliente web de MySQL, publica `ADMINER_PORT` (default 8081). |

`./src` se monta como bind mount en `app`: `src/public` es el docroot, `src/app` es codigo privado fuera del docroot.

## Comandos disponibles (Makefile)

| Comando | Que hace |
|---|---|
| `make install` | Instala dependencias de Composer si hace falta, levanta los tres contenedores. Correlo una sola vez por proyecto. |
| `make up` | Levanta los contenedores en segundo plano. |
| `make down` | Apaga los contenedores. **No borra datos**: `mysql-data` es un volumen con nombre. |
| `make restart` | Reinicia los contenedores sin rebuildear. |
| `make shell` | Abre una terminal `bash` dentro del contenedor `app`. |
| `make db-shell` | Abre el cliente `mysql` conectado a la base del proyecto. |
| `make logs` | Sigue los logs de todos los servicios. |
| `make db-import FILE=dump.sql` | Importa un dump SQL a la base del proyecto. |
| `make fresh` | Borra el volumen de MySQL y vuelve a levantar todo de cero. Pide confirmacion explicita. |

## Configuracion (`.env` en la raiz, no en `src/`)

Las credenciales de MySQL y los puertos se controlan con variables de entorno que lee `docker-compose.yml`, no con un `.env` dentro de `src/`. Para cambiarlas, creá un `.env` en la raiz del repo:

```bash
APP_PORT=8080
ADMINER_PORT=8081
DB_DATABASE=app
DB_USERNAME=app
DB_PASSWORD=secret
```

`src/app/db.php` lee estas mismas variables con `getenv()` dentro del contenedor `app` (docker-compose las inyecta como `environment:`), no hace falta editar codigo para cambiarlas.

## Arrancar un proyecto real

1. `gh repo create mi-proyecto --template TU_USUARIO/php-docker-starter-apache-mysql --private --clone && cd mi-proyecto`
2. Reemplazar `docker/mysql/init/01-schema.sql` por el schema real del proyecto (o agregar mas archivos `.sql` numerados) y borrar/editar `src/public/index.php` (es una demo descartable).
3. `make install` — levanta todo y, si hay `src/composer.json`, instala las dependencias.
