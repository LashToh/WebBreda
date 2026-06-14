# WebBreda

Personalizaciones de [WebEngine CMS](https://webenginecms.org/) (v1.2.7) para
**MU Breda Season 6**: template adaptado, módulos personalizados y plugin de
donaciones automáticas con Mercado Pago.

Este repositorio **no** incluye el core de WebEngine CMS (hay que tenerlo
instalado por separado, según su propia licencia). Acá solo viven los
archivos que se agregan/sobrescriben sobre una instalación de WebEngine
1.2.7 ya funcionando.

## Estructura

```
templates/Mu Breda/        -> Template del sitio (adaptado a 1.2.7, navbar
                                 y sidebar dinámicos, sección de eventos,
                                 página de Info rediseñada, etc.)

modules/                      -> Módulos personalizados (overrides):
    info.php                     - página de información del servidor
    donation.php                 - selector de métodos de pago (PayPal / MercadoPago / Stripe)
    donation/mercadopago.php     - paquetes de Coins/VIP vía Mercado Pago
    donation/stripe.php          - paquetes de Coins/VIP vía Stripe
    donation/stripe_checkout.php - crea la Checkout Session de Stripe (on-demand)
    login.php, register.php, downloads.php

includes/plugins/mercadopago/ -> Plugin de Mercado Pago (Checkout Pro +
                                  Webhook), ver su propio README para la
                                  instalación.

includes/plugins/stripe/      -> Plugin de Stripe (Checkout + Webhook), ver
                                  su propio README para la instalación.

admincp/modules/               -> Paneles de administración de ambos plugins
                                  (configuración y gestión de paquetes).

includes/config/                -> Configuraciones (paquetes de Coins/VIP,
                                  settings de Mercado Pago y Stripe).

sql/                            -> Scripts SQL para las tablas de
                                  transacciones de cada plugin.
```

## Instalación / Deploy

1. Cloná este repo en tu servidor (o descargalo) y copiá/mergeá cada carpeta
   dentro de la raíz de tu instalación de WebEngine 1.2.7, respetando las
   mismas rutas (`templates/`, `modules/`, `includes/`, `admincp/`).
2. Activá el template **"Mu Breda"** desde AdminCP -> Website Settings.
3. Para el plugin de Mercado Pago, seguí las instrucciones en
   [`includes/plugins/mercadopago/README.md`](includes/plugins/mercadopago/README.md).
4. Para el plugin de Stripe, seguí las instrucciones en
   [`includes/plugins/stripe/README.md`](includes/plugins/stripe/README.md).

## Notas

- El logo del navbar/hero (`templates/Mu Breda/img/logo.png`) corresponde al
  branding de "MU Breda Season 6".
- El sidebar de "Eventos" en el home consume `api/events.php` (parte del core
  de WebEngine).
