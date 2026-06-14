<?php
echo '<div class="premium-cartel-square">';
echo '<h3 class="char-card-name mb-4 text-center" style="font-size:24px;letter-spacing:2px;color:#e8a34f;text-transform:uppercase;">
<i class="fas fa-download me-2"></i> '.lang('module_titles_txt_8',true).'
</h3>';

try {

    if(!mconfig('active')) throw new Exception(lang('error_47',true));

    $downloadCLIENTS = [];
    $downloadPATCHES = [];
    $downloadTOOLS   = [];

    $downloadsCACHE = loadCache('downloads.cache');

    if(is_array($downloadsCACHE)) {
        foreach($downloadsCACHE as $d) {
            switch($d['download_type']) {
                case 1: $downloadCLIENTS[] = $d; break;
                case 2: $downloadPATCHES[] = $d; break;
                case 3: $downloadTOOLS[]   = $d; break;
            }
        }
    }

    /* =========================
       🎮 CLIENT DOWNLOADS
    ========================== */
    if(mconfig('show_client_downloads') && count($downloadCLIENTS)) {

        echo '<div class="premium-cartel-deep mb-4 p-4">';
        echo '<h4 class="char-card-name mb-4" style="color:#e8a34f;letter-spacing:1px;">
        <i class="fas fa-desktop me-2"></i> '.lang('downloads_txt_6',true).'
        </h4>';

        echo '<div class="table-responsive">';
        echo '<table class="table table-dark table-hover mb-0"><tbody>';

        foreach($downloadCLIENTS as $d) {
            echo '<tr>';
            echo '<td style="width:60%;"> 
                    <span class="fw-bold text-white">'.$d['download_title'].'</span><br>
                    <small class="text-white-50">'.$d['download_description'].'</small>
                  </td>';

            echo '<td class="text-center" style="width:20%;">
                    <span class="premium-info-badge">'.round($d['download_size'],2).' '.lang('downloads_txt_4',true).'</span>
                  </td>';

            echo '<td class="text-center" style="width:20%;">
                    <a href="'.$d['download_link'].'" target="_blank" class="btn-premium-gold" style="min-width:120px;">
                        '.lang('downloads_txt_5',true).'
                    </a>
                  </td>';
            echo '</tr>';
        }

        echo '</tbody></table></div></div>';
    }

    /* =========================
       📦 PATCH DOWNLOADS
    ========================== */
    if(mconfig('show_patch_downloads') && count($downloadPATCHES)) {

        echo '<div class="premium-cartel-deep mb-4 p-4">';
        echo '<h4 class="char-card-name mb-4" style="color:#e8a34f;letter-spacing:1px;">
        <i class="fas fa-file-archive me-2"></i> '.lang('downloads_txt_7',true).'
        </h4>';

        echo '<div class="table-responsive">';
        echo '<table class="table table-dark table-hover mb-0"><tbody>';

        foreach($downloadPATCHES as $d) {
            echo '<tr>';
            echo '<td style="width:60%;"> 
                    <span class="fw-bold text-white">'.$d['download_title'].'</span><br>
                    <small class="text-white-50">'.$d['download_description'].'</small>
                  </td>';

            echo '<td class="text-center" style="width:20%;">
                    <span class="premium-info-badge">'.round($d['download_size'],2).' '.lang('downloads_txt_4',true).'</span>
                  </td>';

            echo '<td class="text-center" style="width:20%;">
                    <a href="'.$d['download_link'].'" target="_blank" class="btn-premium-gold" style="min-width:120px;">
                        '.lang('downloads_txt_5',true).'
                    </a>
                  </td>';
            echo '</tr>';
        }

        echo '</tbody></table></div></div>';
    }

    /* =========================
       🛠 TOOLS DOWNLOADS
    ========================== */
    if(mconfig('show_tool_downloads') && count($downloadTOOLS)) {

        echo '<div class="premium-cartel-deep mb-4 p-4">';
        echo '<h4 class="char-card-name mb-4" style="color:#e8a34f;letter-spacing:1px;">
        <i class="fas fa-tools me-2"></i> '.lang('downloads_txt_8',true).'
        </h4>';

        echo '<div class="table-responsive">';
        echo '<table class="table table-dark table-hover mb-0"><tbody>';

        foreach($downloadTOOLS as $d) {
            echo '<tr>';
            echo '<td style="width:60%;"> 
                    <span class="fw-bold text-white">'.$d['download_title'].'</span><br>
                    <small class="text-white-50">'.$d['download_description'].'</small>
                  </td>';

            echo '<td class="text-center" style="width:20%;">
                    <span class="premium-info-badge">'.round($d['download_size'],2).' '.lang('downloads_txt_4',true).'</span>
                  </td>';

            echo '<td class="text-center" style="width:20%;">
                    <a href="'.$d['download_link'].'" target="_blank" class="btn-premium-gold" style="min-width:120px;">
                        '.lang('downloads_txt_5',true).'
                    </a>
                  </td>';
            echo '</tr>';
        }

        echo '</tbody></table></div></div>';
    }

} catch(Exception $ex) {
    echo '<div class="alert alert-danger text-center">'.$ex->getMessage().'</div>';
}

echo '</div>';
?>

