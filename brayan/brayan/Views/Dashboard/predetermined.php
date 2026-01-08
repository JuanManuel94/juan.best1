<?php head($data); ?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="row">
<?php if($_SESSION['userData']['profileid'] == TECHNICAL){ ?>
  <div class="col-xl-3 col-md-6">
    <div class="widget widget-stats bg-orange">
      <div class="stats-icon stats-icon-lg"><i class="ion-ios-cellular fa-fw"></i></div>
      <div class="stats-info">
        <h4 class="text-uppercase">Instalaciones Pendientes</h4>
        <p><?= $data['pending_installations'] ?></p>
      </div>
      <div class="stats-link">
        <a href="<?= base_url() ?>/installations">Ver todos <i class="fa fa-arrow-alt-circle-right"></i></a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="widget widget-stats bg-dark">
      <div class="stats-icon stats-icon-lg"><i class="ion-ios-help-buoy fa-fw"></i></div>
      <div class="stats-info">
        <h4 class="text-uppercase">Tickets Vencidos</h4>
        <p><?= $data['pending_tickets'] ?></p>
      </div>
      <div class="stats-link">
        <a href="<?= base_url() ?>/tickets/expired">Ver todos <i class="fa fa-arrow-alt-circle-right"></i></a>
      </div>
    </div>
  </div>
<?php } ?>
  <div class="col-xl-12">
    <?php if($_SESSION['userData']['profileid'] != TECHNICAL && $_SESSION['userData']['profileid'] != CHARGES){ ?>
    <h1 class="text-uppercase">.:.Bienvenido al sistema.:.</h1>
    <?php } ?>
    <?php if(!empty($_SESSION['permits'][PAYMENTS]['v'])){ ?>
    <div class="panel panel-inverse" data-sortable="false">
      <div class="panel-heading">
        <h4 class="panel-title">Pagos realizados</h4>
        <div class="panel-heading-btn">
          <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-expand"><i class="fa fa-expand"></i></a>
          <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-reload" onclick="refresh_table()"><i class="fas fa-sync-alt"></i></a>
        </div>
      </div>
      <div class="panel-body border-panel">
        <div class="row">
          <div class="col-md-12 col-sm-12 col-12">
            <div class="table-responsive">
              <table id="list-payments" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed" style="width: 100%;">
                <thead>
                  <tr>
                    <th>Codigo</th>
                    <th>Cliente</th>
                    <th>Nº Fact.</th>
                    <th>Fecha</th>
                    <th>Total factura</th>
                    <th style="max-width: 60px !important; width: 60px;">Pagado</th>
                    <th>Forma pago</th>
                    <th>Comentario</th>
                    <th class="all">Estado</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
  </div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>
