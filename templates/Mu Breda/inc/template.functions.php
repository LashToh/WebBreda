<?php

function templateCastleSiegeWidget() {

    $castleSiege = new CastleSiege();
    if(!$castleSiege->showWidget()) return;

    $siegeData = $castleSiege->siegeData();
    if(!is_array($siegeData)) return;
    if(!is_array($siegeData['castle_data'])) return;

    // =========================
    // OWNER DATA
    // =========================
    if($siegeData['castle_data'][_CLMN_MCD_OCCUPY_] == 1) {

        $guildNameRaw = $siegeData['castle_data'][_CLMN_MCD_GUILD_OWNER_];
        $guildOwner   = guildProfile($guildNameRaw);
        $guildMaster  = playerProfile($siegeData['castle_owner_alliance'][0][_CLMN_GUILD_MASTER_]);
        $guildLogo    = returnGuildLogo(
            $siegeData['castle_owner_alliance'][0][_CLMN_GUILD_LOGO_], 
            120
        );

    } else {

        $guildOwner  = '<span class="text-muted">Sin dueño</span>';
        $guildMaster = '-';
        $guildLogo   = returnGuildLogo(
            '1111111111111111111111111114411111144111111111111111111111111111',
            120
        );

    }

    // =========================
    // STATUS
    // =========================
    $currentStage = $siegeData['current_stage']['title'];
    $countdown    = $siegeData['warfare_stage_countdown'];

    $statusBadge = '<span class="badge bg-secondary">'.$currentStage.'</span>';

    if(stripos($currentStage, 'battle') !== false || stripos($currentStage, 'war') !== false) {
        $statusBadge = '<span class="badge bg-success">'.$currentStage.'</span>';
    }

    // =========================
    // OUTPUT TU DISEÑO
    // =========================
    ?>

    <div class="castles-main my-5 fade-in-up">
        <div class="castle-card p-4 rounded-4 shadow-lg">
            <div class="row align-items-center">

                <!-- Logo -->
                <div class="col-lg-3 text-center mb-4 mb-lg-0">
                    <?php echo $guildLogo; ?>
                </div>

                <!-- Info -->
                <div class="col-lg-6">

                    <h3 class="castle-title mb-3">Castle Siege</h3>

                    <div class="castle-info mb-2">
                        <strong>Guild Dueña:</strong>
                        <span class="text-info"><?php echo $guildOwner; ?></span>
                    </div>

                    <div class="castle-info mb-2">
                        <strong>Guild Master:</strong>
                        <span class="text-info"><?php echo $guildMaster; ?></span>
                    </div>

                    <div class="castle-info mb-2">
                        <strong>Estado:</strong>
                        <?php echo $statusBadge; ?>
                    </div>

                    <div class="castle-info">
                        <strong>Próxima Batalla:</strong>
                        <span class="text-warning"><?php echo $countdown; ?></span>
                    </div>

                </div>

                <!-- Countdown visual -->
                <div class="col-lg-3 text-center mt-4 mt-lg-0">

                    <h6 class="mb-3 text-uppercase">Tiempo Restante</h6>

                    <div class="countdown-box">
                        <?php echo $countdown; ?>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <?php
}



function templateBuildNavbarItems() {
	$cfg = loadConfig('navbar');
	if(!is_array($cfg)) return;

	usort($cfg, function($a, $b) {
		return ($a['order'] ?? 0) <=> ($b['order'] ?? 0);
	});

	foreach($cfg as $element) {
		if(!is_array($element)) continue;

		// active
		if(!$element['active']) continue;

		// visibility
		if($element['visibility'] == 'guest') if(isLoggedIn()) continue;
		if($element['visibility'] == 'user') if(!isLoggedIn()) continue;

		// type
		$link = ($element['type'] == 'internal' ? __BASE_URL__ . $element['link'] : $element['link']);

		// title
		$title = (check_value(lang($element['phrase'], true)) ? lang($element['phrase'], true) : 'Unk_phrase');

		// print
		$target = $element['newtab'] ? ' target="_blank"' : '';
		echo '<li class="nav-item"><a href="'.$link.'" class="nav-link"'.$target.'>'.$title.'</a></li>';
	}
}

function templateBuildUsercpItems() {
	$cfg = loadConfig('usercp');
	if(!is_array($cfg)) return;

	usort($cfg, function($a, $b) {
		return ($a['order'] ?? 0) <=> ($b['order'] ?? 0);
	});

	foreach($cfg as $element) {
		if(!is_array($element)) continue;

		// active
		if(!$element['active']) continue;

		// visibility
		if($element['visibility'] == 'guest') if(isLoggedIn()) continue;
		if($element['visibility'] == 'user') if(!isLoggedIn()) continue;

		// type
		$link = ($element['type'] == 'internal' ? __BASE_URL__ . $element['link'] : $element['link']);

		// title
		$title = (check_value(lang($element['phrase'], true)) ? lang($element['phrase'], true) : 'Unk_phrase');

		// icon
		$icon = (check_value($element['icon']) ? __PATH_TEMPLATE_IMG__ . 'icons/' . $element['icon'] : __PATH_TEMPLATE_IMG__ . 'icons/usercp_default.png');

		// print
		$target = $element['newtab'] ? ' target="_blank"' : '';
		echo '<a href="'.$link.'" class="list-group-item list-group-item-action d-flex align-items-center gap-2"'.$target.'>';
		echo '<img src="'.$icon.'" width="20" height="20" alt="">';
		echo '<span>'.$title.'</span>';
		echo '</a>';
	}
}
