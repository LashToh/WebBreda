<?php
if(isLoggedIn()) redirect();

echo '<div class="auth-page-wrapper">';

try {
	
	if(!mconfig('active')) throw new Exception(lang('error_17',true));
	
	// Register Process
	if(isset($_POST['webengineRegister_submit'])) {
		try {
			$Account = new Account();
			
			if(mconfig('register_enable_recaptcha')) {
				if(!@include_once(__PATH_CLASSES__ . 'recaptcha/autoload.php')) throw new Exception(lang('error_60'));
				$recaptcha = new \ReCaptcha\ReCaptcha(mconfig('register_recaptcha_secret_key'));
				
				$resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
				if(!$resp->isSuccess()) {
					throw new Exception(lang('error_18',true));
				}
			}
			
			$Account->registerAccount(
				$_POST['webengineRegister_user'],
				$_POST['webengineRegister_pwd'],
				$_POST['webengineRegister_pwdc'],
				$_POST['webengineRegister_email']
			);
			
		} catch (Exception $ex) {
			message('error', $ex->getMessage());
		}
	}

	echo '<div class="auth-container">';
		echo '<div class="auth-box">';
			
			echo '<h4 class="auth-title text-white">REGISTRO</h4>';
			echo '<div class="auth-divider"></div>';

			echo '<div class="text-center mb-3">';
				echo '<a href="'.__BASE_URL__.'" class="auth-back-link">';
					echo '<img src="'.__PATH_TEMPLATE_IMG__.'arrow-left-1.jpg" class="auth-back-arrow"> VOLVER AL INICIO';
				echo '</a>';
			echo '</div>';

			echo '<form action="" method="post" id="standalone-register-form">';

				// USERNAME
				echo '<div class="mb-3 text-start">';
					echo '<label class="text-white mb-1" style="font-size:14px;">USUARIO</label>';
					echo '<input type="text" class="form-control auth-input" id="webengineRegistration1" name="webengineRegister_user" minlength="'.config('username_min_len',true).'" maxlength="'.config('username_max_len',true).'" required>';
					echo '<span id="username-status" style="font-size:13px; display:block; margin-top:5px;"></span>';
				echo '</div>';

				// EMAIL
				echo '<div class="mb-3 text-start">';
					echo '<label class="text-white mb-1" style="font-size:14px;">CORREO ELECTRÓNICO</label>';
					echo '<input type="email" class="form-control auth-input" id="webengineRegistration4" name="webengineRegister_email" required>';
					echo '<span id="email-status" style="font-size:13px; display:block; margin-top:5px;"></span>';
				echo '</div>';

				// PASSWORD
				echo '<div class="mb-3 text-start">';
					echo '<label class="text-white mb-1" style="font-size:14px;">CONTRASEÑA</label>';
					echo '<input type="password" class="form-control auth-input" id="webengineRegistration2" name="webengineRegister_pwd" minlength="'.config('password_min_len',true).'" maxlength="'.config('password_max_len',true).'" required>';
				echo '</div>';

				// CONFIRM PASSWORD
				echo '<div class="mb-4 text-start">';
					echo '<label class="text-white mb-1" style="font-size:14px;">CONFIRMAR CONTRASEÑA</label>';
					echo '<input type="password" class="form-control auth-input" id="webengineRegistration3" name="webengineRegister_pwdc" required>';
					echo '<span id="password-status" style="font-size:13px; display:block; margin-top:5px;"></span>';
				echo '</div>';

				// RECAPTCHA
				if(mconfig('register_enable_recaptcha')) {
					echo '<div class="mb-4 d-flex justify-content-center">';
						echo '<div class="g-recaptcha" data-sitekey="'.mconfig('register_recaptcha_site_key').'"></div>';
					echo '</div>';
					echo '<script src="https://www.google.com/recaptcha/api.js"></script>';
				}

				// TOS
				echo '<div class="mb-3 text-center text-white" style="font-size:13px;">';
					echo 'Al registrarte aceptas nuestros términos<br>';
					echo '<a href="'.__BASE_URL__.'tos" target="_blank">Terms of Service</a>';
				echo '</div>';

				// SUBMIT
				echo '<button type="submit" name="webengineRegister_submit" value="submit" class="btn auth-btn-blue w-100 py-2 fw-bold text-uppercase">';
					echo 'CREAR CUENTA';
				echo '</button>';

				echo '<div class="text-center mt-3" style="font-size:14px;">';
					echo '<a href="'.__BASE_URL__.'login" class="text-decoration-none fw-bold link-blue">';
						echo 'VOLVER A INICIAR SESIÓN';
					echo '</a>';
				echo '</div>';

			echo '</form>';

		echo '</div>';
	echo '</div>';

} catch(Exception $ex) {
	message('error', $ex->getMessage());
}

echo '</div>';
?>