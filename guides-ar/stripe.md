# Stripe — Guía de configuración

Stripe está implementado como **plugin** (`includes/plugins/stripe/`). Usa
**Stripe Checkout** (redirección) y crea la Checkout Session **on-demand** al
hacer click en "Donar ahora". Al aprobarse el pago, Stripe llama al webhook
`/includes/plugins/stripe/api.php` y el plugin acredita las Coins / VIP.

> Antes de empezar, leé los **requisitos comunes** en el
> [`README.md`](README.md) (Credit Configuration, importar el plugin, ngrok).
> El plugin de Stripe **tiene que estar importado** en AdminCP → Plugins.

> **Importante**: Stripe no opera con cuentas radicadas en Argentina. Se usa
> normalmente para cobros en **USD/EUR**. Para cobros locales en pesos, usá
> Mercado Pago.

---

## Dónde se configura

- **Ajustes**: AdminCP → **Credits → Stripe Settings**
  (o el archivo `src/includes/config/modules/stripe.xml`).
- **Paquetes**: AdminCP → **Credits → Stripe Packs**.
- **Endpoint webhook**: `/includes/plugins/stripe/api.php`.

### Campos de configuración

| Campo             | Qué es                                                                |
| ----------------- | --------------------------------------------------------------------- |
| `active`          | `1` para habilitar Stripe en la página de donaciones.                 |
| `coins_status`    | `1` para habilitar packs de Coins.                                    |
| `vip_status`      | `1` para habilitar packs de VIP.                                      |
| `secret_key`      | Clave secreta de Stripe (`sk_test_…` / `sk_live_…`).                  |
| `publishable_key` | Clave pública (`pk_test_…` / `pk_live_…`). Reservada para uso futuro. |
| `webhook_secret`  | Signing secret del webhook (`whsec_…`).                               |
| `currency`        | Moneda (`usd`, `eur`, …).                                             |
| `success_url`     | A dónde vuelve el usuario tras pagar OK.                              |
| `cancel_url`      | A dónde vuelve si cancela.                                            |

---

## Modo prueba (sandbox / test mode)

Stripe no tiene un "sandbox" aparte: usás el **modo Test** de tu propia cuenta
(las claves `…_test_…`).

### 1. Obtener las claves de test

1. Entrá a <https://dashboard.stripe.com/> y activá el **toggle "Test mode"**
   (arriba a la derecha).
2. Andá a **Developers → API keys** y copiá:
   - **Secret key** → `sk_test_…`
   - **Publishable key** → `pk_test_…`

### 2. Configurar el webhook de test

Tenés dos opciones para que los webhooks lleguen a tu local:

**Opción A — Webhook por ngrok (igual que en prod)**

1. Levantá el entorno con `just dev` (con `NGROK_AUTHTOKEN` cargado).
2. En el dashboard (Test mode) → **Developers → Webhooks → Add endpoint**:
   - **Endpoint URL**: `https://TU-URL-NGROK/includes/plugins/stripe/api.php`
   - **Evento**: `checkout.session.completed`
3. Copiá el **Signing secret** (`whsec_…`) que te muestra Stripe.

**Opción B — Stripe CLI (forwarding local, sin ngrok)**

```bash
stripe login
stripe listen --forward-to http://localhost:8080/includes/plugins/stripe/api.php
```

El CLI te imprime un `whsec_…` temporal; usá ese como `webhook_secret`.

### 3. Configurar el sitio (local)

En **AdminCP → Credits → Stripe Settings**:

- `secret_key` = tu `sk_test_…`
- `webhook_secret` = el `whsec_…` del paso anterior
- `currency` = `usd`
- `success_url` = `http://localhost:8080/usercp/myaccount`
- `cancel_url` = `http://localhost:8080/donation/stripe`

Después entrá a **Stripe Packs** y creá al menos un pack de Coins (precio,
cantidad y la Credit Configuration a usar).

### 4. Probar con tarjetas de test

1. Iniciá sesión, entrá a **Donations → Stripe**, elegí un pack y "Donar ahora".
2. En el Checkout de Stripe usá una **tarjeta de prueba**:
   - Nº: `4242 4242 4242 4242`
   - Vencimiento: cualquier fecha futura · CVC: cualquiera · CP: cualquiera
3. Al aprobarse, Stripe llama al webhook (`api.php`), que verifica la firma con
   `webhook_secret`, identifica al usuario por el `metadata` de la sesión y
   acredita Coins/VIP. Verificá el registro en `WEBENGINE_STRIPE_TRANSACTIONS`.

---

## Producción

1. En el dashboard, **desactivá Test mode** y copiá las claves **live**:
   - **Secret key** → `sk_live_…`
2. **Developers → Webhooks → Add endpoint** (en modo live):
   - **Endpoint URL**: `https://TU-DOMINIO/includes/plugins/stripe/api.php`
   - **Evento**: `checkout.session.completed`
   - Copiá el nuevo **Signing secret** (`whsec_…`) de producción.
3. En **AdminCP → Credits → Stripe Settings**:
   - `secret_key` = `sk_live_…`
   - `webhook_secret` = el `whsec_…` **de producción**
   - `success_url` = `https://TU-DOMINIO/usercp/myaccount`
   - `cancel_url` = `https://TU-DOMINIO/donation/stripe`

### Checklist de paso a producción

- [ ] `secret_key` con la clave **live** (`sk_live_…`)
- [ ] Webhook live creado apuntando a tu dominio real
- [ ] `webhook_secret` de producción cargado en el AdminCP
- [ ] `success_url` / `cancel_url` con tu dominio real (HTTPS)
- [ ] Un pago de prueba real acredita Coins y queda en
      `WEBENGINE_STRIPE_TRANSACTIONS`
