# Guías de métodos de pago — MU Breda

Guías en español para configurar los tres métodos de donación del sitio, tanto
en **modo prueba (sandbox)** para testear en local como en **producción**.

| Método       | Guía                               | Tipo                    |
| ------------ | ---------------------------------- | ----------------------- |
| PayPal       | [`paypal.md`](paypal.md)           | Core de WebEngine (IPN) |
| Stripe       | [`stripe.md`](stripe.md)           | Plugin (Checkout)       |
| Mercado Pago | [`mercadopago.md`](mercadopago.md) | Plugin (Checkout Pro)   |

> Todas las páginas de esta carpeta están en español a propósito (son la
> documentación operativa del servidor). El código y la UI del sitio siguen su
> propio idioma.

---

## Conceptos comunes (leer primero)

Los tres métodos comparten la misma lógica dentro de WebEngine:

- **Paquetes (packs)**: definís paquetes de **Coins** y de **VIP** con su precio
  y cantidad. PayPal usa una **tasa de conversión** (créditos por unidad de
  moneda); Stripe y Mercado Pago usan **packs** editables desde el AdminCP.
- **CreditSystem**: al aprobarse un pago, los métodos acreditan las Coins
  usando el sistema de créditos nativo de WebEngine. Para que esto funcione
  tenés que tener al menos una **Credit Configuration** creada (ver abajo).
- **Webhook / IPN**: el proveedor de pago llama a una URL de tu sitio para
  avisar que el pago se aprobó. **Esa llamada la hace el servidor del
  proveedor**, no el navegador, así que la URL tiene que ser **pública**. En
  local eso se resuelve con **ngrok** (ver abajo).
- **VIP**: si el pack es de VIP, se actualiza `AccountLevel` /
  `AccountExpireDate` en `MEMB_INFO` en vez de acreditar Coins.

### Endpoints de notificación (webhook / IPN)

| Método       | URL (relativa a la raíz del sitio)      | Evento a escuchar            |
| ------------ | --------------------------------------- | ---------------------------- |
| PayPal       | `/api/paypal.php`                       | IPN (automático)             |
| Stripe       | `/includes/plugins/stripe/api.php`      | `checkout.session.completed` |
| Mercado Pago | `/includes/plugins/mercadopago/api.php` | `payments`                   |

---

## Antes de empezar (requisitos en el AdminCP)

Estos pasos valen para los tres métodos. Hacelos una sola vez:

1. **Crear una Credit Configuration**
   AdminCP → **Credits → Credit Configurations** → creá una config (define en
   qué tabla/columna se guardan las Coins, p. ej. `MEMB_INFO`). Sin esto, los
   pagos se aprueban pero **no acreditan**.

2. **Activar los plugins de Stripe y Mercado Pago** (no aplica a PayPal, que es
   core)
   AdminCP → **Plugins** → **Importar** y subí:
   - `includes/plugins/stripe/plugin.xml`
   - `includes/plugins/mercadopago/plugin.xml`

   Esto registra cada plugin y regenera el `plugins.cache`, de modo que sus
   `loader.php` se carguen en cada request. En una base recién restaurada con
   `just init` los plugins **no** vienen importados todavía.

3. **Activar el template y el módulo de donaciones**
   El template **"Mu Breda"** ya viene activo. La página de donaciones
   (`/donation`) muestra las tres opciones (PayPal / Mercado Pago / Stripe).

---

## Probar en local (sandbox + ngrok)

El entorno local corre en `http://localhost:8080` (ver el `README.md` de la
raíz). Para que los webhooks/IPN lleguen a tu máquina necesitás una URL
pública:

1. Cargá tu `NGROK_AUTHTOKEN` en `.env`.
2. `just dev` levanta el túnel automáticamente y muestra la **URL pública**
   (también con `just info`). Ejemplo: `https://3a1b-2c3d.ngrok-free.app`.
3. En la configuración de cada método (y en el panel del proveedor) usá esa
   URL pública para el webhook/IPN, por ejemplo:
   ```
   https://3a1b-2c3d.ngrok-free.app/includes/plugins/stripe/api.php
   ```

> **Redirección vs webhook**: las URLs de **retorno/éxito** (a las que vuelve el
> navegador del usuario) pueden apuntar a `http://localhost:8080/...`. Las URLs
> de **webhook/IPN** (que llama el proveedor) **siempre** tienen que ser la URL
> pública de ngrok.

Cada guía tiene su sección **"Modo prueba (sandbox)"** con las credenciales de
test, tarjetas de prueba y usuarios de test correspondientes.

---

## Pasar a producción

En cada guía, la sección **"Producción"** explica qué credenciales reales usar
y qué URLs configurar (tu dominio real con HTTPS, p. ej.
`https://mubreda.com/...`). Reglas generales:

- Reemplazá credenciales de **test** por las de **producción**.
- Reemplazá la URL de ngrok por tu **dominio real con HTTPS**.
- Verificá que el dominio tenga **certificado SSL válido** (los webhooks de los
  tres proveedores requieren HTTPS).
