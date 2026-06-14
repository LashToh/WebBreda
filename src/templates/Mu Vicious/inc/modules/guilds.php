<?php
try {

    loadModuleConfigs('rankings');

    if(!mconfig('rankings_enable_guilds') || !mconfig('active')) {
        echo '<div class="swiper-slide"><div class="alert alert-warning text-center">Ranking no disponible.</div></div>';
        return;
    }

    $ranking_data = LoadCacheData('rankings_guilds.cache');

    if(!is_array($ranking_data) || count($ranking_data) <= 1) {
        echo '<div class="swiper-slide"><div class="alert alert-warning text-center">No hay datos de guilds.</div></div>';
        return;
    }

    echo '<div class="swiper-slide">';
    echo '<div class="rankings-card fade-in-up" style="background-image: url(' . __PATH_TEMPLATE_IMG__ . 'fon-guilds.jpg)">';

    echo '<h4 class="text-center page-title mb-4">
            <i class="fas fa-shield-alt me-2" style="color: #e8a34f;"></i> Top Guilds
          </h4>';

    echo '<table class="rankings-table">';
    echo '<thead>
            <tr>
                <th>#</th>
                <th>Guild</th>
                <th>Score</th>
            </tr>
          </thead>';
    echo '<tbody>';

    $i = 0;
    $limit = 5;

    foreach($ranking_data as $rdata) {

        if($i == 0) { $i++; continue; }
        if($i > $limit) break;

        $guildName  = $rdata[0] ?? 'Unknown';
        $guildScore = $rdata[2] ?? 0;

        $multiplier = mconfig('guild_score_formula') == 1 
            ? 1 
            : mconfig('guild_score_multiplier');

        $finalScore = number_format(floor($guildScore * $multiplier));

        // 🏆 clases top
        $rankClass = '';
        if($i == 1) $rankClass = 'rank-1';
        elseif($i == 2) $rankClass = 'rank-2';
        elseif($i == 3) $rankClass = 'rank-3';

        echo '<tr class="'.$rankClass.'">';

        // 🥇 medallas top 3
        if($i <= 3) {
            echo '<td class="rankings-table-place"><i class="fas fa-medal"></i></td>';
        } else {
            echo '<td class="rankings-table-place">'.$i.'</td>';
        }

        echo '<td class="text-start">'.guildProfile($guildName).'</td>';
        echo '<td class="fw-bold">'.$finalScore.'</td>';
        echo '</tr>';

        $i++;
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>'; // card
    echo '</div>'; // swiper

} catch(Exception $e) {

    echo '<div class="swiper-slide">';
    echo '<div class="alert alert-danger text-center">';
    echo 'Error al cargar ranking de Guilds.';
    echo '</div>';
    echo '</div>';

}
?>