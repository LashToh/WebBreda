# MercadoPago Donations - Plugin para WebEngine CMS 1.2.7

Plugin de acreditación automática de Monedas/Coins y VIP mediante Mercado Pago
(Checkout Pro + Webhooks), adaptado a la estructura y clases nativas de
WebEngine 1.2.7 (`CreditSystem`, `Plugins`, etc).

---

## Contenido del ZIP

```
includes/plugins/mercadopago/
    loader.php          -> se carga automáticamente en cada request (clase MercadoPago)
    api.php             -> webhook/IPN de Mercado Pago
    plugin.xml          -> manifiesto para el importador de plugins del AdminCP
    vendor/             -> SDK oficial de Mercado Pago (dx-php) + dependencias

modules/
    donation.php        -> agrega el botón/card de MercadoPago a la página de donaciones
    donation/
        mercadopago.php -> página de selección de paquetes (Coins / VIP)

admincp/modules/
    mercadopago.php        -> configuración del plugin (Access Token, URLs, etc)
    mercadopago_packs.php  -> administrador de paquetes de Coins y VIP

includes/config/
    mp_packs_coin.json      -> paquetes de monedas (editable desde AdminCP)
    mp_packs_vip.json        -> paquetes de VIP (editable desde AdminCP)
    modules/mercadopago.xml -> configuración del módulo (token, urls, etc)

sql/
    mysql_install.sql   -> tabla de transacciones (MySQL)
    mssql_install.sql   -> tabla de transacciones (MSSQL)
```

---

## Instalación

### 1. Base de datos
Ejecutá `sql/mysql_install.sql` (o `mssql_install.sql` según tu motor) sobre la
base **Me_MuOnline** (la base de WebEngine). Si tu instalación usa un prefijo
de tablas (`WE_PREFIX`), reemplazá `{TABLE_PREFIX}` por ese prefijo (en la
mayoría de las instalaciones está vacío).

Asegurate también de que exista la tabla `WEBENGINE_PLUGINS` (viene incluida
en el core de WebEngine 1.2.7; si tu instalación es muy vieja, usá
`install/sql/WEBENGINE_PLUGINS.txt` del core).

### 2. Subir archivos
Subí las carpetas tal cual están en el ZIP a la raíz de tu WebEngine:

- `includes/plugins/mercadopago/` (con `loader.php`, `api.php`, `plugin.xml`, `vendor/`)
- `modules/donation.php` (sobrescribe el archivo existente — si ya lo
  personalizaste, simplemente agregá la card de MercadoPago manualmente,
  mirá el archivo como referencia)
- `modules/donation/mercadopago.php`
- `admincp/modules/mercadopago.php`
- `admincp/modules/mercadopago_packs.php`
- `includes/config/mp_packs_coin.json`
- `includes/config/mp_packs_vip.json`
- `includes/config/modules/mercadopago.xml`

### 3. Instalar el plugin desde el AdminCP
1. Entrá a **AdminCP -> Plugins**.
2. Importá el archivo `includes/plugins/mercadopago/plugin.xml`.
3. Esto registra el plugin en la base de datos y genera el `plugins.cache`,
   por lo que `loader.php` (la clase `MercadoPago` y el SDK) se cargará
   automáticamente en cada request.

### 4. Agregar accesos al menú del AdminCP (manual, una sola vez)
Editá `admincp/index.php` y agregá estas dos líneas dentro del array
`"Credits"` del `$admincpSidebar` (junto a `latestpaypal`):

```php
"mercadopago" => "MercadoPago Settings",
"mercadopago_packs" => "MercadoPago Packs",
```

### 5. Configurar el plugin
1. Entrá a **AdminCP -> Credits -> MercadoPago Settings**.
2. Pegá tu **Access Token** (Producción o Test) desde
   https://www.mercadopago.com.ar/developers/panel/credentials
3. Configurá la **Return URL** (por defecto `usercp/myaccount`).
4. Configurá la **IPN Notify URL**:
   ```
   https://tudominio.com/includes/plugins/mercadopago/api.php
   ```
   y agregala también como Webhook en el panel de Mercado Pago
   (Developers -> Tu aplicación -> Webhooks -> URL de producción/prueba,
   evento `payments`).
5. Entrá a **MercadoPago Packs** y configurá tus paquetes de Coins/VIP
   (precio, cantidad, moneda/configuración de créditos a usar).

### 6. Probar
- Iniciá sesión en la web, entrá a **Donations -> MercadoPago**.
- Vas a ver los paquetes configurados, cada uno con su botón "Donar ahora"
  que lleva al Checkout Pro de Mercado Pago.
- Al aprobarse el pago, Mercado Pago llama al webhook (`api.php`), que:
  - identifica al usuario por su username (guardado en la descripción del item),
  - acredita las Coins usando `CreditSystem` (igual que el sistema nativo de
    votos/donaciones de WebEngine), o activa el VIP actualizando
    `AccountLevel`/`AccountExpireDate` en `MEMB_INFO`,
  - guarda un registro en `WEBENGINE_MERCADOPAGO_TRANSACTIONS`.

---

## Notas técnicas

- El plugin reutiliza el `CreditSystem` nativo de WebEngine, por lo que las
  Coins se acreditan en la moneda/tabla que ya tengas configurada en
  **AdminCP -> Credit Configurations** (sea `MEMB_INFO`, `MEMB_STAT`, una
  tabla custom, etc).
- `loader.php` solo define la clase `MercadoPago` y carga el SDK — no hace
  ninguna consulta pesada en cada request.
- El formato de descripción usado en los items de Mercado Pago es
  `username|cantidad|TIPO|configId`, donde `TIPO` es `COINS` o `VIP`. Esto es
  lo que el webhook lee para saber qué acreditar.
- Si en el futuro Mercado Pago discontinúa el SDK `dx-php` (v1, ya en modo
  legacy), se puede reemplazar `vendor/` por el SDK nuevo (`mercadopago/sdk-php`)
  sin tener que tocar `modules/donation/mercadopago.php` más que en la forma
  de crear la preferencia (la API pública de Checkout Pro es la misma).
