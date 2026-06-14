# Mercado Pago — Guía de configuración

Mercado Pago está implementado como **plugin** (`includes/plugins/mercadopago/`,
con el SDK oficial `dx-php` incluido en `vendor/`). Usa **Checkout Pro**: el
usuario elige un pack y va al checkout de Mercado Pago. Al aprobarse el pago,
Mercado Pago llama al webhook `/includes/plugins/mercadopago/api.php` y el
plugin acredita las Coins / VIP.

> Antes de empezar, leé los **requisitos comunes** en el
> [`README.md`](README.md) (Credit Configuration, importar el plugin, ngrok).
> El plugin de Mercado Pago **tiene que estar importado** en AdminCP → Plugins.

> Es el método recomendado para cobrar en **pesos (ARS)** a usuarios de
> Argentina.

---

## Dónde se configura

- **Ajustes**: AdminCP → **Credits → MercadoPago Settings**
  (o el archivo `src/includes/config/modules/mercadopago.xml`).
- **Paquetes**: AdminCP → **Credits → MercadoPago Packs**.
- **Endpoint webhook**: `/includes/plugins/mercadopago/api.php`.

### Campos de configuración

| Campo                        | Qué es                                                      |
| ---------------------------- | ----------------------------------------------------------- |
| `active`                     | `1` para habilitar Mercado Pago en la página de donaciones. |
| `coins_status`               | `1` para habilitar packs de Coins.                          |
| `vip_status`                 | `1` para habilitar packs de VIP.                            |
| `access_token`               | Access Token de Mercado Pago (test o producción).           |
| `mercadopago_return_url`     | A dónde vuelve el navegador tras pagar.                     |
| `mercadopago_api_return_url` | URL del webhook: termina en `…/mercadopago/api.php`.        |

---

## Modo prueba (sandbox)

Mercado Pago prueba con **credenciales de TEST** + **usuarios de prueba**
(vendedor y comprador) que se crean desde el panel de desarrolladores.

### 1. Obtener las credenciales de test

1. Entrá a <https://www.mercadopago.com.ar/developers/panel/app> y creá (o
   abrí) tu aplicación.
2. En **Credenciales de prueba**, copiá el **Access Token** de test
   (empieza con `TEST-…` o es un `APP_USR-…` marcado como de prueba).

### 2. Crear usuarios de prueba

1. En el panel de desarrolladores → **Cuentas de prueba** (Test users), creá
   dos usuarios:
   - **Vendedor** (con cuyas credenciales de test configurás el sitio);
   - **Comprador** (con el que vas a pagar en la prueba).
2. Para pagar vas a usar las **tarjetas de prueba** de Mercado Pago
   (<https://www.mercadopago.com.ar/developers/es/docs/checkout-pro/additional-content/your-integrations/test/cards>),
   p. ej. Mastercard `5031 7557 3453 0604`, con un titular de prueba como
   `APRO` para forzar un pago **aprobado**.

### 3. Configurar el sitio (local)

En **AdminCP → Credits → MercadoPago Settings**:

- `access_token` = tu Access Token de **test**
- `mercadopago_return_url` = `http://localhost:8080/usercp/myaccount`
- `mercadopago_api_return_url` =
  `https://TU-URL-NGROK/includes/plugins/mercadopago/api.php`
  (la URL pública que muestra `just dev` / `just info`)

Después entrá a **MercadoPago Packs** y creá al menos un pack de Coins (precio
en ARS, cantidad y la Credit Configuration a usar).

### 4. Registrar el webhook en el panel

1. Levantá el entorno con `just dev` (con `NGROK_AUTHTOKEN` cargado).
2. En el panel de tu aplicación → **Webhooks** → configurá la **URL de
   prueba**:
   ```
   https://TU-URL-NGROK/includes/plugins/mercadopago/api.php
   ```
   y suscribí el evento **`payments`**.

### 5. Probar

1. Iniciá sesión, entrá a **Donations → Mercado Pago**, elegí un pack y "Donar
   ahora".
2. Pagá con el **usuario comprador de prueba** y una **tarjeta de prueba**
   (titular `APRO` para aprobar).
3. Al aprobarse, Mercado Pago llama al webhook (`api.php`), que identifica al
   usuario por la descripción del item (`username|cantidad|TIPO|configId`) y
   acredita Coins/VIP. Verificá el registro en
   `WEBENGINE_MERCADOPAGO_TRANSACTIONS`.

---

## Producción

1. En el panel de tu aplicación → **Credenciales de producción**, copiá el
   **Access Token** de producción (`APP_USR-…`).
2. En **AdminCP → Credits → MercadoPago Settings**:
   - `access_token` = el Access Token **de producción**
   - `mercadopago_return_url` = `https://TU-DOMINIO/usercp/myaccount`
   - `mercadopago_api_return_url` =
     `https://TU-DOMINIO/includes/plugins/mercadopago/api.php`
3. En el panel → **Webhooks**, configurá la **URL de producción** con tu
   dominio real y el evento `payments`.
4. Tu dominio tiene que estar en **HTTPS** con certificado válido.

### Checklist de paso a producción

- [ ] `access_token` con la credencial de **producción** (`APP_USR-…`)
- [ ] `mercadopago_api_return_url` con tu dominio real (HTTPS)
- [ ] Webhook de producción configurado con el evento `payments`
- [ ] Packs con precios reales en ARS
- [ ] Un pago real de prueba acredita Coins y queda en
      `WEBENGINE_MERCADOPAGO_TRANSACTIONS`
