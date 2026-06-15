<?php
if(!defined('access') or !access) die();
?>
<ul class="navbar-nav me-auto">
    <?php templateBuildNavbarItems(); ?>
</ul>
<ul class="navbar-nav ms-auto align-items-center">
    <?php if(isLoggedIn()) { ?>
    <?php if(canAccessAdminCP($_SESSION['username'])) { ?>
    <li class="nav-item"><a href="<?php echo __BASE_URL__; ?>admincp/" class="nav-link auth-nav-link">Admin CP</a></li>
    <?php } ?>
    <li class="nav-item"><a href="<?php echo __BASE_URL__; ?>usercp" class="nav-link auth-nav-link"><?php echo lang('module_titles_txt_3'); ?></a></li>
    <li class="nav-item"><a href="<?php echo __BASE_URL__; ?>logout" class="nav-link auth-nav-link"><?php echo lang('menu_txt_6'); ?></a></li>
    <?php } else { ?>
    <li class="nav-item"><a href="<?php echo __BASE_URL__; ?>login" class="nav-link auth-nav-link"><?php echo lang('menu_txt_4'); ?></a></li>
    <li class="nav-item"><a href="<?php echo __BASE_URL__; ?>register" class="nav-link auth-nav-link"><?php echo lang('menu_txt_3'); ?></a></li>
    <?php } ?>
</ul>
