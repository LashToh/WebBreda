<?php

if (!defined('access') or !access)
    die();

$serverInfoCache = LoadCacheData('server_info.cache');
if (is_array($serverInfoCache)) {
    $srvInfo = explode("|", $serverInfoCache[1][0]);
}

$maxOnline = config('maximum_online', true);
$onlinePlayers = check_value($srvInfo[3]) ? $srvInfo[3] : 0;
$onlinePlayersPercent = check_value($maxOnline) ? $onlinePlayers * 100 / $maxOnline : 0;
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
    <meta name="author" content="Template by Mulanesa" />
    <meta name="description" content="<?php config('website_meta_keywords'); ?>" />
    <meta name="keywords" content="<?php config('website_meta_keywords'); ?>" />
    <link rel="shortcut icon" href="<?php echo __PATH_TEMPLATE__; ?>favicon.ico" />
    <!--Favicon-->
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <!--CSS-->
    <link href="<?php echo __PATH_TEMPLATE_CSS__; ?>style.css" rel="stylesheet">
    <link href="<?php echo __PATH_TEMPLATE_CSS__; ?>mobile.css" rel="stylesheet">
    <link href="<?php echo __PATH_TEMPLATE_CSS__; ?>oversize.css" rel="stylesheet">
    <link href="<?php echo __PATH_TEMPLATE_CSS__; ?>style-vertical.css" rel="stylesheet">
    <!--Agregados-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!--Fonts-->
    <link href="https://api.fontshare.com/v2/css?f[]=gambarino@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script>
        var baseUrl = '<?php echo __BASE_URL__; ?>';
    </script>
</head>

<body>

    <div id="contenidoWeb">
        <nav class="fixed-top navbar navbar-expand-lg">
            <div class="container">
                <a href="" class="navbar-brand">
                    <img src="<?php echo __PATH_TEMPLATE_IMG__; ?>nabo.png" alt="Logo" width="55px">
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
        <nav class="section-nav"></nav>

        <div class="rrss-icon-premium">
            <a href="#" target="_blank" class="rs-whatsapp" title="WhatsApp">
                <i class="fa-brands fa-square-whatsapp"></i>
            </a>

            <a href="#" target="_blank" class="rs-facebook" title="Facebook">
                <i class="fa-brands fa-square-facebook"></i>
            </a>

            <a href="#" target="_blank" class="rs-instagram" title="Instagram">
                <i class="fa-brands fa-square-instagram"></i>
            </a>


            <a href="#" target="_blank" class="rs-tiktok" title="TikTok">
                <i class="fa-brands fa-tiktok"></i>
            </a>



            <a href="#" target="_blank" class="rs-discord" title="Discord">
                <i class="fa-brands fa-discord"></i>
            </a>
        </div>


        <main class="container-snap content-wrapper">

            <!--Principal Section-->
            <video autoplay muted loop id="videoBg">
                <source src="<?php echo __PATH_TEMPLATE_IMG__; ?>hero.webm" type="video/webm">
                Your browser does not support HTML5 video.
            </video>
            <section class="panel main-section is-active" id="inicio">
                <div class="panel-content text-center">
                    <div class="fade-in-down">
                        <img src="<?php echo __PATH_TEMPLATE_IMG__; ?>nabo.png" alt="Logotipo" class="img-fluid"
                            width="75%" style="filter: drop-shadow(2px 4px 6px #000); opacity: 1 !important;">
                        <h1 class="text-main-section">Tu nueva aventura en <?php config('server_name'); ?></h1>
                        <div class="stats-container mb-2">
                        </div>
                    </div>
                </div>

                <div class="server-info-glass server-info-hero">
                    <div class="server-info-glass-inner">
                        <div class="server-info-row">
                            <span class="server-info-label">Online Users:</span>
                            <span
                                class="server-info-value server-info-online"><?php echo number_format($onlinePlayers); ?></span>
                        </div>
                        <div class="webengine-online-bar">
                            <div class="webengine-online-bar-progress"
                                style="width:<?php echo number_format($onlinePlayers); ?>%;"></div>
                        </div>
                        <div class="server-info-row">
                            <span class="server-info-label">Server Time:</span>
                            <span class="server-info-value"><time id="tServerTime">17:52:34</time> <span
                                    id="tServerDate" class="server-info-date">Tue Mar 31</span></span>
                        </div>
                        <div class="server-info-row">
                            <span class="server-info-label">Your Time:</span>
                            <span class="server-info-value"><time id="tLocalTime">14:52:34</time> <span id="tLocalDate"
                                    class="server-info-date">Tue Mar 31</span></span>
                        </div>
                    </div>
                </div>

                <a href="https://discord.gg/79gRQsdeXA" target="_blank" rel="noopener" class="discord-hero-widget"
                    id="discordHeroWidget" title="Únete a nuestro Discord">
                    <div class="discord-hero-inner">
                        <div class="discord-hero-header">
                            <span class="discord-hero-icon-wrap"><i class="fab fa-discord discord-hero-icon"></i></span>
                            <div class="discord-hero-titles">
                                <span class="discord-hero-title">Discord</span>
                                <span class="discord-hero-cta">Únete →</span>
                            </div>
                        </div>
                        <div class="discord-hero-online">
                            <span class="discord-hero-count" id="discordOnlineCount">4</span>
                            <span class="discord-hero-label">en línea ahora</span>
                        </div>
                        <div class="discord-hero-avatars" id="discordAvatars">

                            <img class="discord-hero-avatar"
                                src="https://cdn.discordapp.com/widget-avatars/aoK09Esmp6asK1R1bv-Yg108lpoSj0veGebF4i2TLOQ/3pWeQ7Km9qrDrbyU1h14DKmSp0KgN0gfo_dJSW3WcPXPsaR4PLyv0lcqbMBg_qQvDX8LzbPYSfdLItIV8XrJecqazWU8A56M06yI34XfIGFqWpF_OV-1I9Hokg2EdhBtdExxkypSHiPIQA"
                                alt="KenaBot" title="KenaBot" loading="lazy"><img class="discord-hero-avatar"
                                src="https://cdn.discordapp.com/widget-avatars/eKWQiG4BIEi_FMrHbhMO1t-eFtwdPE8Ae1DIlb7Aa6U/MciaUR_7HRQnCXP5IWPe2RuaLUwkH4Np8Hw89ec-abZgh8Zk2FPinIDmnXQKhaGDUVFCVKTSJxL4UM0xVm-gbfsRoBXMBVZz8s6wLs00haeP3qMAZR5n_rGF5eGdrKnv70w9nWLQer9oiQ"
                                alt="Koya" title="Koya" loading="lazy"><img class="discord-hero-avatar"
                                src="https://cdn.discordapp.com/widget-avatars/VbTh49R0fwWqqi5QkWsjVR6xwNzL3cIFNGIln9GNyNA/UotWPnMWIc4nUV3EC35wNinAli5dVz0shNXcEtjsA1kDVWWsnBLMpCHDoWr3xUS4D0JUwsHJPjrUBK7Fuzbi4iYwa_2Nj6LelzJQ35Yf9kfH57v3Pbd6yhf0XD9VOM3Mops6g-F6zipvHw"
                                alt="Megito" title="Megito" loading="lazy"><img class="discord-hero-avatar"
                                src="https://cdn.discordapp.com/widget-avatars/Ro2vsXGI-4LUizgVWmOc0EKOA_a4XToUSBI7qz4wRsI/gnH_rcp5y2DAhci3TCeFDNyZzuhNY5KAeD01iGAwuzzeUqBuTWditPbmaBMrr41_hUiyXT0hgxhJJ4fHCM-spOHoTr71nqM28iE2Uy8fshCAGsCTsYqafimWHsPCQYniroJgen5tJU3vBg"
                                alt="Uzox" title="Uzox" loading="lazy">
                        </div>
                    </div>
                </a>




            </section>

            <!-- Noticias Slider -->

            <section class="panel noticias" id="noticias">
                <div class="panel-content container my-5">
                    <h2 class="text-center fade-in-up section-premium-title">Noticias</h2>
                    <?php include(__PATH_TEMPLATE_ROOT__ . 'inc/modules/news.php'); ?>
                </div>
            </section>

            <!-- Ranking Slider -->

            <section class="panel rankings" id="rankings">
                <div class="panel-content container my-5">
                    <h2 class="text-center fade-in-up mb-4 section-premium-title">Rankings</h2>

                    <!-- Swiper -->
                    <div class="rankingsSwiper swiper swiper-initialized swiper-horizontal swiper-backface-hidden">
                        <div class="swiper-wrapper">
                            <?php include(__PATH_TEMPLATE_ROOT__ . 'inc/modules/level.php'); ?>

                            <?php include(__PATH_TEMPLATE_ROOT__ . 'inc/modules/grandresets.php'); ?>

                            <?php include(__PATH_TEMPLATE_ROOT__ . 'inc/modules/killers.php'); ?>

                            <?php include(__PATH_TEMPLATE_ROOT__ . 'inc/modules/guilds.php'); ?>


                        </div>

                        <!-- Navigation -->
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>

                        <!-- Pagination -->
                        <div class="swiper-pagination mt-4"></div>
                    </div>




                </div>
            </section>


        </main>

        <!--Footer-->
        <footer class="fixed-bottom bottom-0 w-100 py-3 fade-in-up" style="z-index: 999;">
            <div class="mx-5 footer-main">
                <span class="text-white-50 fw-light text-uppercase" style="line-height: 1;">© 2026
                    <?php config('server_name'); ?></span>
            </div>
        </footer>

    </div>

    <aside id="events-sidebar" class="events-sidebar collapsed">
        <div class="events-sidebar-toggle" id="eventsSidebarToggle">
            <i class="fas fa-calendar-alt"></i>
            <span>Eventos</span>
            <i class="fas fa-chevron-right toggle-icon"></i>
        </div>
        <div class="events-sidebar-body">
            <h3 class="events-sidebar-title"><i class="fas fa-calendar-alt me-2"></i>Horario de Eventos</h3>
            <div class="events-loading-container" style="text-align:center; padding:30px 0;">
                <i class="fa-solid fa-spinner fa-spin" style="font-size: 32px; color: #ffaf43;"></i>
                <span style="display:block; margin-top:10px; color:#e7c195;">Cargando eventos...</span>
            </div>
            <table class="table table-condensed events-table" style="display: none; color: #ccc; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th class="text-center">Tiempo restante</th>
                        <th class="text-end">Próximo</th>
                    </tr>
                </thead>
                <tbody id="events-list"></tbody>
            </table>
        </div>
    </aside>

    <script>
        function loadDiscordWidget() {
            const GUILD_ID = "ID DE TU SERVIDOR DE DISCORD";
            const API_URL = `https://discord.com/api/guilds/${GUILD_ID}/widget.json`;

            fetch(API_URL)
                .then(res => res.json())
                .then(data => {
                    document.getElementById("discordOnlineCount").textContent = data.presence_count || 0;

                    const avatarsEl = document.getElementById("discordAvatars");
                    avatarsEl.innerHTML = "";

                    data.members.slice(0, 8).forEach(member => {
                        const img = document.createElement("img");
                        img.className = "discord-hero-avatar";
                        img.src = member.avatar_url;
                        img.alt = member.username;
                        img.title = member.username;
                        avatarsEl.appendChild(img);
                    });
                });
        }

        loadDiscordWidget();
        setInterval(loadDiscordWidget, 30000); // cada 30s

        
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>
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
    <script src="<?php echo __PATH_TEMPLATE_JS__; ?>main.js?=3" defer></script>

    <script src="<?php echo __PATH_TEMPLATE_JS__; ?>events.js" defer></script>



    <script>
        $(document).ready(function () {
            var $sidebar = $('#events-sidebar');
            var $toggle = $('#eventsSidebarToggle');
            var apiCandidates = [
                '<?php echo __BASE_URL__; ?>api/events.php',
                (window.location.origin + '/api/events.php').replace(/([^:]\/)\/+/g, '$1'),
                '/api/events.php'
            ];
            var apiUrls = [];
            $.each(apiCandidates, function (_, url) {
                if ($.inArray(url, apiUrls) === -1) apiUrls.push(url);
            });
            var eventTimer;

            $toggle.on('click', function () {
                $sidebar.toggleClass('collapsed');
            });

            function renderEvents(data) {
                var html = '';
                $.each(data, function (key, event) {
                    var timeLeft = event.timeleft;
                    var timeLeftString = formatTimeLeft(timeLeft);
                    html += '<tr>';
                    html += '<td class="event-name">' + event.event + '</td>';
                    html += '<td class="text-center event-time-left" data-seconds="' + timeLeft + '">' + timeLeftString + '</td>';
                    html += '<td class="text-end event-next">' + event.nextF + '</td>';
                    html += '</tr>';
                });
                $('#events-list').html(html);
                $('.events-loading-container').hide();
                $('.events-table').show();
                startTimer();
            }

            function loadEvents(attemptIndex) {
                var currentAttempt = typeof attemptIndex === 'number' ? attemptIndex : 0;
                $.ajax({
                    url: apiUrls[currentAttempt],
                    dataType: 'json',
                    cache: false,
                    timeout: 5000,
                    success: function (data) {
                        renderEvents(data);
                    },
                    error: function (xhr, status) {
                        if ((status === 'timeout' || status === 'error') && currentAttempt < (apiUrls.length - 1)) {
                            loadEvents(currentAttempt + 1);
                            return;
                        }
                        $('.events-loading-container').html('<span style="color:#ff6b6b">No se pudieron cargar los eventos.</span>');
                    }
                });
            }

            function formatTimeLeft(seconds) {
                if (seconds <= 0) return "Open / In Progress";
                var h = Math.floor(seconds / 3600);
                var m = Math.floor((seconds % 3600) / 60);
                var s = seconds % 60;
                return (h < 10 ? "0" + h : h) + ":" + (m < 10 ? "0" + m : m) + ":" + (s < 10 ? "0" + s : s);
            }

            function startTimer() {
                if (eventTimer) clearInterval(eventTimer);
                eventTimer = setInterval(function () {
                    $('.event-time-left').each(function () {
                        var $el = $(this);
                        var seconds = parseInt($el.attr('data-seconds'));
                        if (seconds > 0) {
                            seconds--;
                            $el.attr('data-seconds', seconds);
                            $el.text(formatTimeLeft(seconds));
                        } else {
                            $el.text("Open / In Progress");
                        }
                    });
                }, 1000);
            }

            loadEvents(0);
            setInterval(function () { loadEvents(0); }, 600000);
        });
    </script>



</body>

</html>