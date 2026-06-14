# Stripe Donations - Plugin para WebEngine CMS 1.2.7

Plugin de acreditación automática de Monedas/Coins y VIP mediante **Stripe
Checkout** (modo "payment", redirect) + **Webhooks**, adaptado a la
estructura y clases nativas de WebEngine 1.2.7 (`CreditSystem`, `Plugins`,
etc).

A diferencia del SDK oficial de Stripe (que requiere Composer), este plugin
habla directamente con la API REST de Stripe vía cURL — no tiene
dependencias externas, es liviano y fácil de mantener.

---

## Contenido del ZIP

```
includes/plugins/stripe/
    loader.php          -> se carga automáticamente en cada request (clase Stripe)
    api.php             -> webhook de Stripe (checkout.session.completed)
    plugin.xml          -> manifiesto para el importador de plugins del AdminCP

modules/
    donation.php           -> agrega el botón/card de Stripe a la página de donaciones
    donation/
        stripe.php          -> página de selección de paquetes (Coins / VIP)
        stripe_checkout.php -> crea la Checkout Session y redirige a Stripe (on-demand)

admincp/modules/
    stripe.php        -> configuración del plugin (Secret Key, Webhook Secret, etc)
    stripe_packs.php  -> administrador de paquetes de Coins y VIP

includes/config/
    sp_packs_coin.json    -> paquetes de monedas (editable desde AdminCP)
    sp_packs_vip.json     -> paquetes de VIP (editable desde AdminCP)
    modules/stripe.xml    -> configuración del módulo (keys, urls, moneda, etc)

sql/
    stripe_mysql_install.sql -> tabla de transacciones (MySQL)
    stripe_mssql_install.sql -> tabla de transacciones (MSSQL)
```

---

## Instalación

### 1. Base de datos
Ejecutá `sql/stripe_mysql_install.sql` (o `stripe_mssql_install.sql` según tu motor) sobre
la base **Me_MuOnline** (la base de WebEngine). Si tu instalación usa un
prefijo de tablas (`WE_PREFIX`), reemplazá `{TABLE_PREFIX}` por ese prefijo
(en la mayoría de las instalaciones está vacío).

### 2. Subir archivos
Subí las carpetas tal cual están en el ZIP a la raíz de tu WebEngine:

- `includes/plugins/stripe/`
- `modules/donation.php` (sobrescribe el archivo existente — si ya tenés el
  plugin de MercadoPago instalado, mergeá la card de Stripe manualmente
  mirando este archivo como referencia, en vez de sobrescribir)
- `modules/donation/stripe.php`
- `modules/donation/stripe_checkout.php`
- `admincp/modules/stripe.php`
- `admincp/modules/stripe_packs.php`
- `includes/config/sp_packs_coin.json`
- `includes/config/sp_packs_vip.json`
- `includes/config/modules/stripe.xml`

### 3. Instalar el plugin desde el AdminCP
1. Entrá a **AdminCP -> Plugins**.
2. Importá el archivo `includes/plugins/stripe/plugin.xml`.
3. Esto registra el plugin y genera el `plugins.cache`, por lo que
   `loader.php` (la clase `Stripe`) se cargará automáticamente en cada
   request.

### 4. Agregar accesos al menú del AdminCP (manual, una sola vez)
Editá `admincp/index.php` y agregá estas dos líneas dentro del array
`"Credits"` del `$admincpSidebar`:

```php
"stripe" => "Stripe Settings",
"stripe_packs" => "Stripe Packs",
```

### 5. Configurar Stripe
1. Entrá a https://dashboard.stripe.com/apikeys y copiá tu **Secret Key**
   (`sk_test_...` para pruebas, `sk_live_...` para producción).
2. Entrá a **AdminCP -> Credits -> Stripe Settings** y pegá la Secret Key,
   configurá la moneda (`usd`, `eur`, etc — Stripe no opera con cuentas
   radicadas en Argentina, así que normalmente se usa USD/EUR), y las URLs
   de éxito/cancelación.
3. Entrá a https://dashboard.stripe.com/webhooks -> **Add endpoint**:
   - URL: `https://tudominio.com/includes/plugins/stripe/api.php`
   - Evento a escuchar: `checkout.session.completed`
   - Copiá el **Signing secret** (`whsec_...`) que te muestra Stripe y
     pegalo en **Stripe Settings -> Webhook Signing Secret**.
4. Entrá a **Stripe Packs** y configurá tus paquetes de Coins/VIP (precio en
   la moneda configurada, cantidad, y la configuración de créditos a usar).

### 6. Probar
- Iniciá sesión en la web, entrá a **Donations -> Stripe**.
- Vas a ver los paquetes configurados. Al hacer click en "Donar ahora", el
  CMS crea una Checkout Session de Stripe en el momento y te redirige al
  checkout alojado por Stripe.
- Al aprobarse el pago, Stripe llama al webhook (`api.php`), que:
  - verifica la firma del webhook con el Webhook Signing Secret,
  - identifica al usuario por su username (guardado en `metadata` de la
    sesión),
  - acredita las Coins usando `CreditSystem` (igual que el sistema nativo de
    votos/donaciones de WebEngine), o activa el VIP actualizando
    `AccountLevel`/`AccountExpireDate` en `MEMB_INFO`,
  - guarda un registro en `WEBENGINE_STRIPE_TRANSACTIONS`.

---

## Notas técnicas

- A diferencia del flujo de MercadoPago (que pre-genera una preferencia por
  cada pack en cada carga de página), este plugin crea la Checkout Session
  **on-demand** cuando el usuario hace click en "Donar ahora"
  (`modules/donation/stripe_checkout.php`), evitando llamadas innecesarias a
  la API en cada visita a la página de donaciones.
- El plugin reutiliza el `CreditSystem` nativo de WebEngine, por lo que las
  Coins se acreditan en la moneda/tabla que ya tengas configurada en
  **AdminCP -> Credit Configurations**.
- La verificación de la firma del webhook (`Stripe-Signature`) está
  implementada manualmente con `hash_hmac('sha256', ...)`, siguiendo el
  algoritmo documentado por Stripe — no requiere el SDK oficial.
- El formato de `metadata` usado en la Checkout Session es
  `username`, `amount`, `type` (`COINS` o `VIP`) y `configId`. Esto es lo
  que el webhook lee para saber qué acreditar.
- Si más adelante preferís usar Stripe Elements embebido (en vez de
  redirección a Checkout), se puede usar la `publishable_key` ya
  contemplada en la configuración para crear un PaymentIntent + formulario
  embebido sin tocar el resto de la arquitectura del plugin.
