<?php
head($data);
modal("otrosPagosModal", $data);
?>
<pre id="columns" style="display: none;"><?php echo json_encode($data['columns']) ?></pre>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item"><a href="javascript:window.history.back();"><?= $data['previous_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="panel panel-default panel-runway2">
  <div class="panel-heading">
    <h4 class="panel-title"><?= $data['page_title'] ?></h4>
    <div class="panel-heading-btn">
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-expand"><i
          class="fa fa-expand"></i></a>
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-reload"
        onclick="refresh_table()"><i class="fas fa-sync-alt"></i></a>
    </div>
  </div>
  <div class="panel-body border-panel">
    <div class="row">
      <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-info">
          <div class="stats-icon stats-icon-lg"><i class="fa fa-globe fa-fw"></i></div>
          <div class="stats-info">
            <h4 class="text-uppercase text-center">TOTAL INGRESOS HOY</h4>
            <p class="text-center" id="ingreso-today"></p>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-info">
          <div class="stats-icon stats-icon-lg"><i class="fa fa-globe fa-fw"></i></div>
          <div class="stats-info">
            <h4 class="text-uppercase text-center">TOTAL INGRESOS</h4>
            <p class="text-center" id="ingreso-total"></p>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-red">
          <div class="stats-icon stats-icon-lg"><i class="fa fa-globe fa-fw"></i></div>
          <div class="stats-info">
            <h4 class="text-uppercase text-center">TOTAL EGRESOS HOY</h4>
            <p class="text-center" id="egreso-today"></p>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-red">
          <div class="stats-icon stats-icon-lg"><i class="fa fa-globe fa-fw"></i></div>
          <div class="stats-info">
            <h4 class="text-uppercase text-center">TOTAL EGRESOS</h4>
            <p class="text-center" id="egreso-total"></p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-body border-panel">
    <div class="row">
      <div id="list-btns-tools" style="display: none;">
        <div class="row ml-2">
          <?php if ($_SESSION['permits_module']['r']) { ?>
            <button type="button" class="btn btn-white col-lg-3 col-md-3 col-sm-12 col-12 mb-1" onclick="openCreate()">
              <i class="fas fa-plus mr-1"></i>Nuevo
            </button>
          <?php } ?>
          <input type="date" name="dateStart" id="dateStart" class="form-control col-lg-3 col-md-4 col-sm-5 col-5 mb-1"
            onchange="checkDate(this)">
          <div class="col-lg-2 col-md-2 col-sm-2 col-2 d-flex align-items-center text-center mb-1"
            style="background: rgba(0,0,0,0.1)">
            Hasta
          </div>
          <input type="date" name="dateOver" id="dateOver" class="form-control col-lg-3 col-md-4 col-sm-5 col-5 mb-1"
            onchange="checkDate(this)">
        </div>
      </div>
      <div class="col-md-12 col-sm-12 col-12">
        <div class="table-responsive">
          <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed"
            data-order='[[ 1, "asc" ]]' style="width: 100%;">
            <thead>
              <tr>
                <th>ID</th>
                <th>FECHA</th>
                <th>MONTO</th>
                <th>TIPO</th>
                <th>OPERADOR</th>
                <th>DESCRIPCIÓN</th>
                <th>ESTADO</th>
                <?php foreach ($data['columns'] as $column) { ?>
                  <th><?php echo $column['nombre'] ?></th>
                <?php } ?>
                <th class="all" data-orderable="false" style="max-width: 40px !important; width: 40px;"></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>