<?php
try {

    loadModuleConfigs('rankings');

    if(!mconfig('rankings_enable_level') || !mconfig('active')) {
        echo '<div class="swiper-slide"><div class="alert alert-warning text-center">Ranking no disponible.</div></div>';
        return;
    }

    $ranking_data = LoadCacheData('rankings_level.cache');

    if(!is_array($ranking_data) || count($ranking_data) <= 1) {
        echo '<div class="swiper-slide"><div class="alert alert-warning text-center">No hay datos de ranking.</div></div>';
        return;
    }

    echo '<div class="swiper-slide">';
    echo '<div class="rankings-card fade-in-up" style="background-image: url(' . __PATH_TEMPLATE_IMG__ . 'fon-level.jpeg)">';

    echo '<h4 class="text-center page-title mb-4">
            <i class="fas fa-trophy me-2" style="color: #e8a34f;"></i> Top Level
          </h4>';

    echo '<table class="rankings-table">';
    echo '<thead>
            <tr>
                <th>#</th>
                <th>Character</th>
                <th>Level</th>
            </tr>
          </thead>';
    echo '<tbody>';

    $i = 0;
    $limit = 5;

    foreach($ranking_data as $rdata) {

        if($i == 0) { $i++; continue; }
        if($i > $limit) break;

        $character = $rdata[0] ?? 'Unknown';
        $level     = isset($rdata[2]) ? number_format($rdata[2]) : 0;

        // 🏆 CLASES PARA TOP 1,2,3
        $rankClass = '';
        if($i == 1) $rankClass = 'rank-1';
        elseif($i == 2) $rankClass = 'rank-2';
        elseif($i == 3) $rankClass = 'rank-3';

        echo '<tr class="'.$rankClass.'">';

        // 🥇 ICONOS TOP 3
        if($i <= 3) {
            echo '<td class="rankings-table-place"><i class="fas fa-medal"></i></td>';
        } else {
            echo '<td class="rankings-table-place">'.$i.'</td>';
        }

        echo '<td class="text-start">'.playerProfile($character).'</td>';
        echo '<td class="fw-bold">'.$level.'</td>';
        echo '</tr>';

        $i++;
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>'; // rankings-card
    echo '</div>'; // swiper-slide

} catch(Exception $e) {

    echo '<div class="swiper-slide">';
    echo '<div class="alert alert-danger text-center">';
    echo 'Error al cargar ranking de Level.';
    echo '</div>';
    echo '</div>';

}
?>