# PayPal — Guía de configuración

PayPal es parte del **core de WebEngine** (no es un plugin). Usa el flujo
clásico de **PayPal IPN**: el usuario paga en PayPal y PayPal le avisa a tu
sitio mediante una notificación al endpoint `/api/paypal.php`.

> Antes de empezar, leé los **requisitos comunes** en el
> [`README.md`](README.md) de esta carpeta (Credit Configuration, ngrok, etc.).

---

## Dónde se configura

- **Ajustes**: AdminCP → **Settings → Modules Manager** → módulo
  **Donation (PayPal)** → editar configuración.
  Alternativa: editar directamente
  `src/includes/config/modules/donation.paypal.xml`.
- **Registro de transacciones**: AdminCP → **Credits → PayPal Donations**.
- **Endpoint IPN**: `/api/paypal.php` (ya viene en el core).

### Campos de configuración

| Campo                    | Qué es                                                     |
| ------------------------ | ---------------------------------------------------------- |
| `active`                 | `1` para habilitar PayPal en la página de donaciones.      |
| `paypal_enable_sandbox`  | `1` = modo prueba (sandbox), `0` = producción.             |
| `paypal_email`           | Email de la cuenta **vendedora** que recibe los pagos.     |
| `paypal_title`           | Descripción que ve el usuario en PayPal.                   |
| `paypal_currency`        | Moneda (`USD`, `EUR`, etc.).                               |
| `paypal_return_url`      | A dónde vuelve el navegador del usuario tras pagar.        |
| `paypal_notify_url`      | URL del IPN: termina en `/api/paypal.php`.                 |
| `paypal_conversion_rate` | Cuántas Coins se acreditan por **1 unidad** de la moneda.  |
| `credit_config`          | ID de la Credit Configuration a usar para acreditar Coins. |

> PayPal **no usa packs**: el monto es libre y las Coins se calculan con
> `paypal_conversion_rate`. Ej.: con tasa `100` y moneda USD, una donación de
> USD 5 acredita 500 Coins.

---

## Modo prueba (sandbox)

Sirve para probar el flujo completo en local sin dinero real.

### 1. Crear cuentas de sandbox

1. Entrá a <https://developer.paypal.com/> e iniciá sesión.
2. Andá a **Testing Tools → Sandbox Accounts**.
3. Vas a tener (o podés crear) dos cuentas de prueba:
   - una **Business** (vendedor) — su email es el que va en `paypal_email`;
   - una **Personal** (comprador) — la usás para pagar en la prueba.

### 2. Configurar el sitio (local)

En **Modules Manager → Donation (PayPal)** (o en `donation.paypal.xml`):

- `active` = `1`
- `paypal_enable_sandbox` = `1`
- `paypal_email` = email de tu cuenta **Business de sandbox**
- `paypal_currency` = `USD`
- `paypal_conversion_rate` = `100` (o el que quieras)
- `credit_config` = el ID de tu Credit Configuration
- `paypal_return_url` = `http://localhost:8080/usercp/myaccount`
- `paypal_notify_url` = `https://TU-URL-NGROK/api/paypal.php`
  (la URL pública que muestra `just dev` / `just info`)

### 3. Probar

1. Levantá el entorno con `just dev` (con `NGROK_AUTHTOKEN` cargado para tener
   URL pública).
2. Iniciá sesión en `http://localhost:8080`, entrá a **Donations → PayPal**.
3. Pagá usando la cuenta **Personal de sandbox**.
4. PayPal manda el IPN a `…/api/paypal.php`. Verificá:
   - que las Coins se acreditaron en la cuenta del usuario;
   - que aparece la transacción en **Credits → PayPal Donations**.

> Con `paypal_enable_sandbox = 1`, el core valida el IPN contra el entorno
> sandbox de PayPal (`useSandbox()`). En producción debe estar en `0` o las
> notificaciones reales fallan la verificación.

---

## Producción

1. **Modules Manager → Donation (PayPal)**:
   - `paypal_enable_sandbox` = `0`
   - `paypal_email` = el email **real** de tu cuenta de PayPal de negocios
   - `paypal_return_url` = `https://TU-DOMINIO/usercp/myaccount`
   - `paypal_notify_url` = `https://TU-DOMINIO/api/paypal.php`
   - `paypal_currency`, `paypal_conversion_rate`, `credit_config` según
     corresponda.
2. En tu cuenta real de PayPal, asegurate de tener **IPN habilitado**
   (Account Settings → Notifications → Instant Payment Notifications) apuntando
   a `https://TU-DOMINIO/api/paypal.php`.
3. Tu dominio tiene que estar en **HTTPS** con certificado válido.

### Checklist de paso a producción

- [ ] `paypal_enable_sandbox` = `0`
- [ ] `paypal_email` con el email real del vendedor
- [ ] `paypal_notify_url` con tu dominio real (HTTPS)
- [ ] IPN habilitado en la cuenta real de PayPal
- [ ] Una donación real de prueba acredita Coins y queda registrada
