<?php
try {

    $News = new News();
    $cachedNews = loadCache('news.cache');

    // Si el cache no existe o no es válido
    if(!is_array($cachedNews) || empty($cachedNews)) {
        echo '<div class="alert alert-warning text-center">';
        echo 'No hay noticias disponibles en este momento.';
        echo '</div>';
        return;
    }

    $images = [
        __PATH_TEMPLATE_IMG__ . 'news1.jpg',
        __PATH_TEMPLATE_IMG__ . 'news2.jpg',
        __PATH_TEMPLATE_IMG__ . 'news3.jpg',
        __PATH_TEMPLATE_IMG__ . 'news4.jpg',
    ];

    $i = 0;
    $rendered = 0;

    echo '<div class="swiper newsSwiper my-5">';
    echo '<div class="swiper-wrapper">';

    foreach($cachedNews as $newsArticle) {

        if($i >= 4) break;

        if(!isset($newsArticle['news_id'])) continue;

        $News->setId($newsArticle['news_id']);

        $news_id     = $newsArticle['news_id'];
        $news_title  = isset($newsArticle['news_title']) ? base64_decode($newsArticle['news_title']) : 'Sin título';
        $news_author = isset($newsArticle['news_author']) ? $newsArticle['news_author'] : 'Admin';
        $news_date   = isset($newsArticle['news_date']) ? date("d M Y", $newsArticle['news_date']) : '';
        $news_url    = __BASE_URL__.'news/'.$news_id.'/';

        // Evita HTML roto del sistema viejo
        $news_short = strip_tags($News->LoadCachedNews(true));
        $news_short = substr($news_short, 0, 150) . '...';

        $bgImage = isset($images[$i]) ? $images[$i] : $images[0];

$animationClass = ($i < 2) ? 'fade-in-left' : 'fade-in-right';

echo '<div class="swiper-slide '.$animationClass.'">';
    echo '<div class="news-card">';
        echo '<div class="news-bg" style="background-image:url(\''.$bgImage.'\');"></div>';
        echo '<div class="news-overlay">';
            echo '<h5 class="news-title">'.$news_title.'</h5>';
            echo '<p class="news-desc">'.$news_short.' <a href="'.$news_url.'">(Leer más)</a></p>';
            echo '<div class="news-meta">';
                echo '<span>'.$news_date.'</span><br>';
                echo '<small>Publicado por: '.$news_author.'</small>';
            echo '</div>';
        echo '</div>';
    echo '</div>';
echo '</div>';

        $i++;
        $rendered++;
    }

    echo '</div>';

    if($rendered > 0) {
        echo '<div class="swiper-button-next"></div>';
        echo '<div class="swiper-button-prev"></div>';
    }

    echo '</div>';

    // Si no se renderizó ninguna noticia válida
    if($rendered === 0) {
        echo '<div class="alert alert-warning text-center">';
        echo 'No se pudieron cargar noticias.';
        echo '</div>';
    }

} catch(Exception $e) {

    echo '<div class="alert alert-danger text-center">';
    echo 'Error al cargar el módulo de noticias.';
    echo '</div>';

}