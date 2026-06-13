<?php
echo '<div class="premium-cartel-square">';

// ===== HERO HEADER =====
echo '<div class="info-hero">';
echo '<h1><i class="fas fa-info-circle me-2"></i>'.'Información del Servidor'.'</h1>';
echo '<p>'; config('server_name'); echo ' &mdash; Season '; config('server_info_season'); echo '</p>';
echo '</div>';

// ===== KEY STATS =====
echo '<div class="row g-3 mb-2">';

echo '<div class="col-6 col-md-3">
        <div class="info-stat-card">
            <i class="fas fa-layer-group"></i>
            <span class="info-stat-label">Season</span>
            <span class="info-stat-value">'.config('server_info_season', true).'</span>
        </div>
      </div>';

echo '<div class="col-6 col-md-3">
        <div class="info-stat-card">
            <i class="fas fa-bolt"></i>
            <span class="info-stat-label">Experience</span>
            <span class="info-stat-value">'.config('server_info_exp', true).'</span>
        </div>
      </div>';

echo '<div class="col-6 col-md-3">
        <div class="info-stat-card">
            <i class="fas fa-star"></i>
            <span class="info-stat-label">Master EXP</span>
            <span class="info-stat-value">'.config('server_info_masterexp', true).'</span>
        </div>
      </div>';

echo '<div class="col-6 col-md-3">
        <div class="info-stat-card">
            <i class="fas fa-gem"></i>
            <span class="info-stat-label">Drop Rate</span>
            <span class="info-stat-value">'.config('server_info_drop', true).'</span>
        </div>
      </div>';

echo '</div>';

// ===== CHAOS MACHINE / PARTY BONUS / COMMANDS =====
echo '<div class="info-section-title"><i class="fas fa-flask"></i> Chaos Machine &amp; Party Bonus</div>';

echo '<div class="row g-3 mb-2">';

echo '<div class="col-md-6">
        <div class="info-list-card">
            <div class="info-list-row"><span><i class="fas fa-clover"></i>Item Luck</span><span class="info-value">x% / x%</span></div>
            <div class="info-list-row"><span><i class="fas fa-arrow-up"></i>Items +10 a +12</span><span class="info-value">x% (+Luck)</span></div>
            <div class="info-list-row"><span><i class="fas fa-arrow-up"></i>Items +13 a +15</span><span class="info-value">x% (+Luck)</span></div>
            <div class="info-list-row"><span><i class="fas fa-feather-alt"></i>Wings LV1</span><span class="info-value">x%</span></div>
            <div class="info-list-row"><span><i class="fas fa-feather-alt"></i>Wings LV2/3</span><span class="info-value">x%</span></div>
        </div>
      </div>';

echo '<div class="col-md-6">
        <div class="info-list-card">
            <div class="info-list-row"><span><i class="fas fa-user"></i>2 Players</span><span class="info-value">x% / x%</span></div>
            <div class="info-list-row"><span><i class="fas fa-user-friends"></i>3 Players</span><span class="info-value">x% / x%</span></div>
            <div class="info-list-row"><span><i class="fas fa-users"></i>4 Players</span><span class="info-value">x% / x%</span></div>
            <div class="info-list-row"><span><i class="fas fa-users-cog"></i>5 Players</span><span class="info-value">x% / x%</span></div>
        </div>
      </div>';

echo '</div>';

// ===== COMMANDS =====
echo '<div class="info-section-title"><i class="fas fa-terminal"></i> Comandos</div>';

echo '<div class="row g-3 mb-2">';

echo '<div class="col-md-6">
        <div class="info-list-card">
            <div class="info-list-row"><span><i class="fas fa-redo"></i>/reset</span><span class="info-value">Reset de personaje</span></div>
            <div class="info-list-row"><span><i class="fas fa-comments"></i>/post</span><span class="info-value">Chat Global</span></div>
        </div>
      </div>';

echo '<div class="col-md-6">
        <div class="info-list-card">
            <div class="info-list-row"><span><i class="fas fa-shield-alt"></i>/clearpk</span><span class="info-value">Limpiar PK</span></div>
            <div class="info-list-row"><span><i class="fas fa-plus-circle"></i>/add</span><span class="info-value">Agregar Stats</span></div>
        </div>
      </div>';

echo '</div>';

// ===== TRAILER =====
echo '<div class="info-section-title"><i class="fas fa-video"></i> '.'Server Trailer'.'</div>';

echo '<div class="ratio ratio-16x9 info-trailer">
        <iframe src="https://www.youtube.com/embed/H5QQDvgU-hE?controls=0" allowfullscreen></iframe>
      </div>';

echo '</div>';
?>
