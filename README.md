# WebBreda

Personalizaciones de [WebEngine CMS](https://webenginecms.org/) (v1.2.7) para
**MU Breda Season 6**: template adaptado, módulos personalizados y plugins de
donaciones automáticas con Mercado Pago y Stripe.

Este repositorio **no** incluye el core de WebEngine CMS. El core se descarga
automáticamente al construir el contenedor; en `./src` solo viven los archivos
que agregamos/sobrescribimos sobre una instalación de WebEngine 1.2.7.

## Requisitos

- [Docker](https://docs.docker.com/get-docker/) y Docker Compose
- [`just`](https://github.com/casey/just)

No hace falta instalar nada más localmente: PHP, las extensiones, el empaquetado
del build, etc. corren todos dentro de los contenedores.

## Puesta en marcha

```bash
cp .env.example .env   # ajustá credenciales/puertos si hace falta
just dev               # construye, inicializa la base y levanta todo
```

- Sitio: <http://localhost:8080>
- AdminCP: <http://localhost:8080/admincp/>
- Login de prueba (viene en el backup): **mspro / mspro** (es el admin web).

La primera vez, `just dev` restaura `db/muonline.bak`, crea las tablas de
WebEngine y aplica el SQL de los plugins. Las veces siguientes detecta que la
base ya existe y arranca directo.

### Hot reload

`./src` se monta dentro del contenedor con symlinks, así que **editar un archivo
existente se ve al instante**. Si **agregás o borrás** archivos en `./src`,
corré `just sync` (o reiniciá `just dev`) para regenerar los enlaces.

## Comandos (`just`)

| Comando            | Qué hace                                                            |
| ------------------ | ------------------------------------------------------------------- |
| `just dev`         | Construye, inicializa la base (si hace falta) y levanta db + web.   |
| `just init`        | Restaura el backup e instala las tablas de WebEngine (idempotente). |
| `just ngrok`       | Abre un túnel ngrok al contenedor web (para probar webhooks).       |
| `just sync`        | Re-enlaza `./src` en el contenedor (tras agregar/borrar archivos).  |
| `just build-full`  | ZIP completo de `./src` (incluye imágenes/video) para deploy FTP.   |
| `just build-patch` | ZIP liviano (solo código/config, sin imágenes ni video).            |
| `just db`          | Abre una consola SQL contra la base.                                |
| `just logs`        | Sigue los logs de los contenedores.                                 |
| `just down`        | Detiene los contenedores (conserva los datos).                      |
| `just reset`       | Detiene y **borra** los volúmenes (base + webroot).                 |

## Probar pagos (ngrok)

Los webhooks de Mercado Pago y Stripe necesitan una URL pública:

1. Cargá tu `NGROK_AUTHTOKEN` en `.env` (lo sacás de
   <https://dashboard.ngrok.com>).
2. Con el token cargado, `just dev` **levanta el túnel automáticamente** y
   muestra la URL pública al final (también la ves con `just info` o, si querés
   arrancarlo aparte, con `just ngrok`). El panel queda en
   <http://localhost:4040>.
3. Configurá esa URL como webhook en el panel del proveedor y en el AdminCP del
   plugin correspondiente.

## Deploy

```bash
just build-full    # dist/webbreda-overlay-full.zip  — todo (incluye imágenes/video)
just build-patch   # dist/webbreda-overlay-patch.zip — solo código/config (liviano)
```

Ambos ZIP tienen la misma estructura de carpetas que la raíz de WebEngine
(`modules/`, `includes/`, `templates/`, `admincp/`). Usá **full** la primera vez
(o cuando cambian assets) y **patch** para subir rápido cambios de código cuando
las imágenes/videos ya están en el servidor. Subilo por FTP a tu instalación de
WebEngine 1.2.7 y descomprimilo en la raíz, respetando las rutas. Después:

1. Activá el template **"Mu Breda"** desde AdminCP -> Website Settings.
2. Seguí los README de cada plugin para Mercado Pago y Stripe.
3. Aplicá el SQL de `db/sql/` que corresponda a tu base.

## Estructura del repo

```
src/                              -> Nuestras personalizaciones (overlay del webroot)
    templates/Mu Breda/             - Template del sitio
    modules/                        - Módulos (info, donation, login, register, downloads)
    includes/plugins/mercadopago/   - Plugin de Mercado Pago (Checkout Pro + Webhook)
    includes/plugins/stripe/        - Plugin de Stripe (Checkout + Webhook)
    includes/config/                - Paquetes de Coins/VIP y settings de los plugins
    admincp/modules/                - Paneles de administración de los plugins

db/
    muonline.bak                    - Backup de la base (juego) que restaura `just init`
    sql/                            - Scripts SQL de las tablas de los plugins

docker/web/                       -> Imagen PHP/Apache + instalador headless de WebEngine
scripts/db-init.sh                -> Restore + instalación de tablas (lo usa `just init`)
docker-compose.yml, justfile      -> Orquestación local
```

## Notas

- La base usa **una sola** base de datos (modo `SQL_USE_2_DB=false`): las tablas
  `WEBENGINE_*` conviven con las del juego dentro de `MuOnline`.
- El driver de SQL Server es `pdo_dblib` (FreeTDS), `SQL_PDO_DRIVER=1`.
- Para ver errores PHP durante el desarrollo, poné `"error_reporting": true` en
  `includes/config/webengine.json` (se regenera desde `docker/web/webengine.json.tpl`).
- El sidebar de "Eventos" del home consume `api/events.php` (parte del core).
