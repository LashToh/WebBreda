<?php
/**
 * WebEngine CMS - Downloads Modal Version
 */

try {

    loadModuleConfigs('downloads'); // ← ESTA LINEA FALTABA

    if(!mconfig('active')) {
        throw new Exception(lang('error_47', true));
    }

    $downloadCLIENTS = array();
    $downloadPATCHES = array();
    $downloadTOOLS   = array();

$downloadsCACHE = loadCache('downloads.cache');

if(is_array($downloadsCACHE)) {
    foreach($downloadsCACHE as $tempDownloadsData) {

        $type = (int)$tempDownloadsData['download_type'];

        switch($type) {
            case 1:
                $downloadCLIENTS[] = $tempDownloadsData;
            break;
            case 2:
                $downloadPATCHES[] = $tempDownloadsData;
            break;
            case 3:
                $downloadTOOLS[] = $tempDownloadsData;
            break;
        }
    }
}
    echo '<div class="container-fluid">';
    echo '<div class="row g-4">';

    $hasDownloads = false;

    function renderDownloadCard($download) {
        echo '
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card-download h-100 text-center p-4">
                <img src="'.__PATH_TEMPLATE_IMG__.'carpet.png" class="img-fluid mb-3" alt="">
                <h5>'.$download['download_title'].'</h5>
                <p class="small">
                    '.$download['download_description'].'<br>
                    <strong>'.round($download['download_size'],2).' '.lang('downloads_txt_4',true).'</strong>
                </p>
                <a href="'.$download['download_link'].'" target="_blank" class="btn btn-light w-100 mt-auto">
                    '.lang('downloads_txt_5',true).'
                </a>
            </div>
        </div>';
    }

    // CLIENTES
    if(mconfig('show_client_downloads') && !empty($downloadCLIENTS)) {
        foreach($downloadCLIENTS as $download) {
            renderDownloadCard($download);
            $hasDownloads = true;
        }
    }

    // PATCHES
    if(mconfig('show_patch_downloads') && !empty($downloadPATCHES)) {
        foreach($downloadPATCHES as $download) {
            renderDownloadCard($download);
            $hasDownloads = true;
        }
    }

    // TOOLS
    if(mconfig('show_tool_downloads') && !empty($downloadTOOLS)) {
        foreach($downloadTOOLS as $download) {
            renderDownloadCard($download);
            $hasDownloads = true;
        }
    }

    echo '</div>';
    echo '</div>';

    // ⚠️ Si no hay descargas
    if(!$hasDownloads) {
        echo '
        <div class="alert alert-warning text-center mt-4">
            No hay descargas disponibles en este momento.
        </div>';
    }

} catch(Exception $ex) {
    echo '
    <div class="alert alert-danger text-center">
        '.$ex->getMessage().'
    </div>';
}
?>