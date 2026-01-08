<?php head($data); ?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
    <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="row d-flex justify-content-center">
    <?php if (!empty($_SESSION['permits'][BUSINESS]['v'])) { ?>
        <a href="<?= base_url() ?>/settings/general" class="settings btn btn-lg btn-white">
            <i class="fas fa-cog fa-2x"></i><br>General
        </a>
    <?php } ?>
    <?php if (!empty($_SESSION['permits'][BUSINESS]['v'])) { ?>
        <a href="<?= base_url() ?>/settings/database" class="settings btn btn-lg btn-white">
            <i class="fas fa-database fa-2x"></i><br>Base de datos
        </a>
    <?php } ?>
    <?php if (!empty($_SESSION['permits'][USERS]['v'])) { ?>
        <a href="<?= base_url() ?>/users" class="settings btn btn-lg btn-white">
            <i class="fas fa-users fa-2x"></i><br>Usuarios
        </a>
    <?php } ?>
    <?php if (!empty($_SESSION['permits'][USERS]['v'])) { ?>
        <a href="<?= base_url() ?>/profiles" class="settings btn btn-lg btn-white">
            <i class="fa fa-lock fa-2x"></i><br>Roles
        </a>
    <?php } ?>
    <?php if (!empty($_SESSION['permits'][CURRENCYS]['v'])) { ?>
        <a href="<?= base_url() ?>/currencys" class="settings btn btn-lg btn-white">
            <i class="fas fa-money-bill-alt fa-2x"></i><br>Divisas
        </a>
    <?php } ?>
    <?php if (!empty($_SESSION['permits'][RUNWAY]['v'])) { ?>
        <a href="<?= base_url() ?>/runway" class="settings btn btn-lg btn-white">
            <i class="fas fa-hand-holding-usd fa-2x"></i><br>Formas de pago
        </a>
    <?php } ?>
    <?php if (!empty($_SESSION['permits'][VOUCHERS]['v'])) { ?>
        <a href="<?= base_url() ?>/vouchers" class="settings btn btn-lg btn-white">
            <i class="far fa-file-alt fa-2x"></i><br>Comprobantes
        </a>
    <?php } ?>
    <?php if (!empty($_SESSION['permits'][UNITS]['v'])) { ?>
        <a href="<?= base_url() ?>/unit" class="settings btn btn-lg btn-white">
            <i class="fa fa-box fa-2x"></i><br>Unidades de medida
        </a>
    <?php } ?>
    <?php if (!empty($_SESSION['permits'][INCIDENTS]['v'])) { ?>
        <a href="<?= base_url() ?>/incidents" class="settings btn btn-lg btn-white">
            <i class="fa fa-wrench fa-2x"></i><br>Incidencias
        </a>
    <?php } ?>
    <?php if (!empty($_SESSION['permits'][BUSINESS]['v'])) { ?>
        <a href="<?= base_url() ?>/settings/cronjobs" class="settings btn btn-lg btn-white">
            <i class="fa fa-tasks fa-2x"></i><br>Cron Jobs
        </a>
    <?php } ?>
    <?php if (!empty($_SESSION['permits'][INCIDENTS]['v'])) { ?>
        <a href="<?= base_url() ?>/runway2" class="settings btn btn-lg btn-white">
            <i class="fa fa-map fa-2x"></i><br>Zonas
        </a>
    <?php } ?>
    <?php if (!empty($_SESSION['permits'][INCIDENTS]['v'])) { ?>
        <a href="<?= base_url() ?>/campos" class="settings btn btn-lg btn-white">
            <i class="fa fa-list fa-2x"></i><br>Campo Personalizados
        </a>
    <?php } ?>
    <?php if (!empty($_SESSION['permits'][WHATSAPP]['v'])) { ?>
        <a href="<?= base_url() ?>/campania/plantilla" class="settings btn btn-lg btn-white">
            <i class="fa fa-paper-plane fa-2x"></i><br> Plantillas de WSP
        </a>
    <?php } ?>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>