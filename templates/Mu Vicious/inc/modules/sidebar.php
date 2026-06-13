<?php
if(!defined('access') or !access) die();
?>
<style>
.mv-sidebar .card {
    background: rgba(0,0,0,0.55);
    border: 1px solid rgba(232,163,79,0.35);
    border-radius: 10px;
    margin-bottom: 20px;
    color: #fff;
}
.mv-sidebar .card-header {
    background: rgba(232,163,79,0.15);
    border-bottom: 1px solid rgba(232,163,79,0.35);
    color: #e8a34f;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 14px;
    font-weight: 700;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.mv-sidebar .card-header a {
    color: #fff;
}
.mv-sidebar .list-group-item {
    background: transparent;
    border-color: rgba(255,255,255,0.08);
    color: #ddd;
}
.mv-sidebar .list-group-item:hover {
    background: rgba(232,163,79,0.15);
    color: #fff;
}
.mv-sidebar table td {
    color: #ddd;
    border-color: rgba(255,255,255,0.08);
}
.mv-sidebar .form-control {
    background: rgba(255,255,255,0.05);
    border-color: rgba(255,255,255,0.15);
    color: #fff;
}
</style>

<div class="mv-sidebar">

    <?php if(!isLoggedIn()) { ?>
    <div class="card">
        <div class="card-header">
            <span><?php echo lang('module_titles_txt_2'); ?></span>
            <a href="<?php echo __BASE_URL__; ?>forgotpassword" class="small"><?php echo lang('login_txt_4'); ?></a>
        </div>
        <div class="card-body">
            <form action="<?php echo __BASE_URL__; ?>login" method="post">
                <div class="mb-2">
                    <input type="text" class="form-control" id="loginBox1" name="webengineLogin_user" placeholder="<?php echo lang('login_txt_1'); ?>" required>
                </div>
                <div class="mb-2">
                    <input type="password" class="form-control" id="loginBox2" name="webengineLogin_pwd" placeholder="<?php echo lang('login_txt_2'); ?>" required>
                </div>
                <button type="submit" name="webengineLogin_submit" value="submit" class="btn btn-warning w-100"><?php echo lang('login_txt_3'); ?></button>
            </form>
            <a href="<?php echo __BASE_URL__; ?>register" class="btn btn-outline-light w-100 mt-2"><?php echo lang('menu_txt_3'); ?></a>
        </div>
    </div>
    <?php } ?>

    <?php if(isLoggedIn()) { ?>
    <div class="card">
        <div class="card-header">
            <span><?php echo lang('usercp_menu_title'); ?></span>
            <a href="<?php echo __BASE_URL__; ?>logout" class="small"><?php echo lang('login_txt_6'); ?></a>
        </div>
        <div class="list-group list-group-flush">
            <?php templateBuildUsercpItems(); ?>
        </div>
    </div>
    <?php } ?>

    <?php if(isset($srvInfo) && is_array($srvInfo)) { ?>
    <div class="card">
        <div class="card-header">
            <span><?php echo lang('sidebar_srvinfo_txt_1'); ?></span>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <?php if(check_value(config('server_info_season', true))) { ?>
                <tr><td><?php echo lang('sidebar_srvinfo_txt_6'); ?></td><td><?php config('server_info_season', true); ?></td></tr>
                <?php } ?>
                <?php if(check_value(config('server_info_exp', true))) { ?>
                <tr><td><?php echo lang('sidebar_srvinfo_txt_7'); ?></td><td><?php config('server_info_exp', true); ?></td></tr>
                <?php } ?>
                <?php if(check_value(config('server_info_masterexp', true))) { ?>
                <tr><td><?php echo lang('sidebar_srvinfo_txt_8'); ?></td><td><?php config('server_info_masterexp', true); ?></td></tr>
                <?php } ?>
                <?php if(check_value(config('server_info_drop', true))) { ?>
                <tr><td><?php echo lang('sidebar_srvinfo_txt_9'); ?></td><td><?php config('server_info_drop', true); ?></td></tr>
                <?php } ?>
                <tr><td><?php echo lang('sidebar_srvinfo_txt_2'); ?></td><td style="font-weight:bold;"><?php echo number_format($srvInfo[0]); ?></td></tr>
                <tr><td><?php echo lang('sidebar_srvinfo_txt_3'); ?></td><td style="font-weight:bold;"><?php echo number_format($srvInfo[1]); ?></td></tr>
                <tr><td><?php echo lang('sidebar_srvinfo_txt_4'); ?></td><td style="font-weight:bold;"><?php echo number_format($srvInfo[2]); ?></td></tr>
                <?php if(check_value(config('maximum_online', true))) { ?>
                <tr><td><?php echo lang('sidebar_srvinfo_txt_5'); ?></td><td style="color:#5dd35d;font-weight:bold;"><?php echo number_format($onlinePlayers); ?></td></tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <?php } ?>

    <div class="card">
        <div class="card-body">
            <?php templateCastleSiegeWidget(); ?>
        </div>
    </div>

</div>
