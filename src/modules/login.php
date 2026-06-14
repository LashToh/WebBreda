<?php
if(isLoggedIn()) redirect();

try {

    if(!mconfig('active')) throw new Exception(lang('error_47',true));

    // Login Process
    if(isset($_POST['webengineLogin_submit'])) {
        try {
            $userLogin = new login();
            $userLogin->validateLogin($_POST['webengineLogin_user'], $_POST['webengineLogin_pwd']);
        } catch (Exception $ex) {
            echo '<div class="auth-error text-center mb-3">'.$ex->getMessage().'</div>';
        }
    }
?>

<div class="auth-page-wrapper">

    <div class="auth-container">
        <div class="auth-box">

            <h4 class="auth-title text-white">
                <?php echo lang('module_titles_txt_2',true); ?>
            </h4>

            <div class="auth-divider"></div>

            <!-- VOLVER -->
            <div class="text-center mb-3">
                <a href="<?php echo __BASE_URL__; ?>" class="auth-back-link">
                    <img src="<?php echo __PATH_TEMPLATE_IMG__; ?>arrow-left-1.jpg" class="auth-back-arrow">
                    VOLVER AL INICIO
                </a>
            </div>

            <!-- FORM -->
            <form action="" method="post" id="standalone-login-form">

                <!-- USER -->
                <div class="mb-3 text-start">
                    <label class="text-white mb-1" style="font-size:14px;">
                        <?php echo lang('login_txt_1',true); ?>
                    </label>
                    <input type="text" class="form-control auth-input"
                        id="webengineLogin1"
                        name="webengineLogin_user"
                        required>
                </div>

                <!-- PASSWORD -->
                <div class="mb-4 text-start">
                    <label class="text-white mb-1 d-flex justify-content-between" style="font-size:14px;">
                        <span><?php echo lang('login_txt_2',true); ?></span>
                        <a href="<?php echo __BASE_URL__; ?>forgotpassword/" class="link-blue" style="font-size:12px;">
                            <?php echo lang('login_txt_4',true); ?>
                        </a>
                    </label>
                    <input type="password" class="form-control auth-input"
                        id="webengineLogin2"
                        name="webengineLogin_pwd"
                        required>
                </div>

                <!-- RECAPTCHA (opcional) -->
                <?php if(config('recaptcha_enable')) { ?>
                    <div class="mb-4 mt-4 d-flex justify-content-center">
                        <div class="g-recaptcha"
                             data-sitekey="<?php echo config('recaptcha_site_key'); ?>"
                             data-callback="loginRecaptchaOk"
                             data-expired-callback="loginRecaptchaExpired">
                        </div>
                    </div>
                    <script src="https://www.google.com/recaptcha/api.js"></script>
                <?php } ?>

                <!-- BUTTON -->
                <button type="submit"
                        name="webengineLogin_submit"
                        value="submit"
                        class="btn auth-btn-blue w-100 py-2 fw-bold text-uppercase"
                        <?php echo config('recaptcha_enable') ? 'disabled' : ''; ?>>
                    <?php echo lang('login_txt_3',true); ?>
                </button>

                <!-- REGISTER -->
                <div class="text-center mt-3" style="font-size:14px;">
                    <span class="text-white-50">¿AÚN NO TIENES CUENTA?</span><br>
                    <a href="<?php echo __BASE_URL__; ?>register" class="text-decoration-none fw-bold link-blue">
                        REGÍSTRATE AHORA
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
function loginRecaptchaOk() {
    document.querySelector('#standalone-login-form button[name="webengineLogin_submit"]').disabled = false;
}
function loginRecaptchaExpired() {
    document.querySelector('#standalone-login-form button[name="webengineLogin_submit"]').disabled = true;
}
</script>

<?php

} catch(Exception $ex) {
    echo '<div class="alert alert-danger text-center">'.$ex->getMessage().'</div>';
}
?>