<?php
/**
 * WebEngine CMS
 * https://webenginecms.org/
 * 
 * @version 1.2.2
 * @author Lautaro Angelico <http://lautaroangelico.com/>
 * @copyright (c) 2013-2020 Lautaro Angelico, All Rights Reserved
 * 
 * Licensed under the MIT license
 * http://opensource.org/licenses/MIT
 */
if (!defined('access') or !access)
    die();

$serverInfoCache = LoadCacheData('server_info.cache');
if (is_array($serverInfoCache)) {
    $srvInfo = explode("|", $serverInfoCache[1][0]);
}

$maxOnline = config('maximum_online', true);
$onlinePlayers = check_value($srvInfo[3]) ? $srvInfo[3] : 0;
$onlinePlayersPercent = check_value($maxOnline) ? $onlinePlayers * 100 / $maxOnline : 0;
$currentPage = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
$isRankPage = str_starts_with(strtolower($currentPage), 'rankings');
if(!isset($_REQUEST['subpage'])) {
    $_REQUEST['subpage'] = '';
}
if($currentPage == 'usercp' && !check_value($_REQUEST['subpage'])) {
    $_REQUEST['subpage'] = 'myaccount';
}
$isUsercpPage = ($currentPage == 'usercp');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>
        <?php $handler->websiteTitle(); ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="WebEngine <?php echo __WEBENGINE_VERSION__; ?>" />
    <meta name="author" content="Lautaro Angelico" />
    <meta name="description" content="<?php config('website_meta_keywords'); ?>" />
    <meta name="keywords" content="<?php config('website_meta_keywords'); ?>" />
    <link rel="shortcut icon" href="<?php echo __PATH_TEMPLATE__; ?>favicon.ico" />
    <!--Favicon-->
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <!--CSS-->
    <link href="<?php echo __PATH_TEMPLATE_CSS__; ?>profiles.css" rel="stylesheet">
    <link href="<?php echo __PATH_TEMPLATE_CSS__; ?>override.css" rel="stylesheet">
    <link href="<?php echo __PATH_TEMPLATE_CSS__; ?>castle-siege.css" rel="stylesheet">
    <link href="<?php echo __PATH_TEMPLATE_CSS__; ?>style.css" rel="stylesheet">
    <link href="<?php echo __PATH_TEMPLATE_CSS__; ?>mobile.css" rel="stylesheet">
    <link href="<?php echo __PATH_TEMPLATE_CSS__; ?>oversize.css" rel="stylesheet">
    <link href="<?php echo __PATH_TEMPLATE_CSS__; ?>style-vertical.css" rel="stylesheet">
    <!--Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script>
        var baseUrl = '<?php echo __BASE_URL__; ?>';

    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="internal-page">



        <nav class="fixed-top navbar navbar-expand-lg">
            <div class="container">
                <a href="" class="navbar-brand">
                    <img src="<?php echo __PATH_TEMPLATE_IMG__; ?>logomini.png" alt="Logo" width="55px">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <?php include(__PATH_TEMPLATE_ROOT__ . 'inc/modules/navbar.php'); ?>
                </div>
            </div>
        </nav>



    <main>
        <section class="internal-content-wrapper">
            <div class="container py-5 mt-5">
                <?php if($_REQUEST['page'] == 'usercp' && check_value($_REQUEST['subpage'])) { ?>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="<?= ($isRankPage || $isUsercpPage) ? 'premium-cartel-square usercp-content' : '' ?>">
                            <?php $handler->loadModule($_REQUEST['page'], $_REQUEST['subpage']); ?>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <?php include(__PATH_TEMPLATE_ROOT__ . 'inc/modules/sidebar.php'); ?>
                    </div>
                </div>
                <?php } else { ?>
                <div class="<?= ($isRankPage || $isUsercpPage) ? 'premium-cartel-square usercp-content' : '' ?>">
                    <?php $handler->loadModule($_REQUEST['page'], $_REQUEST['subpage']); ?>
                </div>
                <?php } ?>
            </div>
        </section>
    </main>






    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
        integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/gsap.min.js"></script>
    <script>
        window.TEMPLATE = {
            img: "<?= __PATH_TEMPLATE_IMG__; ?>"
        };
    </script>
    <script src="<?php echo __PATH_TEMPLATE_JS__; ?>main.js?=2" defer></script>
    <script src="<?php echo __PATH_TEMPLATE_JS__; ?>events.js" defer></script>




</body>


</html>