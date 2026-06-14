<?php

try {
	
	if(!mconfig('active')) throw new Exception(lang('error_47',true));

	echo '<div class="premium-cartel-square">
    <h3 class="char-card-name mb-5 text-center"
        style="font-size: 24px; letter-spacing: 2px; color: #e8a34f; text-transform: uppercase;"><i
            class="fas fa-donate me-2"></i> '.lang('module_titles_txt_11',true).'</h3>
    <div class="container-fluid p-0">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-4 col-md-6"><a href="'.__BASE_URL__.'donation/paypal/"
                    class="text-decoration-none h-100 d-block">
                    <div class="card-donations p-5"><img src="'.__PATH_TEMPLATE_IMG__.'paypal.png"
                            alt="PayPal" class="img-fluid mb-4" style="max-width:200px;"><span class="btn-premium-gold"
                            style="pointer-events: none; width: 100%; font-size: 16px; padding: 15px;">Donate with
                            PayPal</span></div>
                </a></div>
            <div class="col-lg-4 col-md-6"><a href="'.__BASE_URL__.'donation/mercadopago/"
                    class="text-decoration-none h-100 d-block">
                    <div class="card-donations p-5"><img src="'.__PATH_TEMPLATE_IMG__.'mercadopago.png"
                            alt="MercadoPago" class="img-fluid mb-4" style="max-width:200px;"><span class="btn-premium-gold"
                            style="pointer-events: none; width: 100%; font-size: 16px; padding: 15px;">Donar con
                            Mercado Pago</span></div>
                </a></div>
            <div class="col-lg-4 col-md-6"><a href="'.__BASE_URL__.'donation/stripe/"
                    class="text-decoration-none h-100 d-block">
                    <div class="card-donations p-5"><img src="'.__PATH_TEMPLATE_IMG__.'stripelogo.svg"
                            alt="Stripe" class="img-fluid mb-4" style="max-width:200px;"><span class="btn-premium-gold"
                            style="pointer-events: none; width: 100%; font-size: 16px; padding: 15px;">Donar con
                            Stripe</span></div>
                </a></div>
        </div>
    </div>
</div>';
	
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}
