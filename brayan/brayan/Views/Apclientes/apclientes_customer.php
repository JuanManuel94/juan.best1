<?php
  head($data);
  modal("apClientesModal", $data);
?>
<!-- INICIO TITULO -->
<input type="hidden" id="apId" value="<?= $data['apId'] ?>">
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
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-reload" onclick="refresh_table()"><i class="fas fa-sync-alt"></i></a>
        </div>
    </div>
    <div class="panel-body border-panel">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-12">
                <div class="table-responsive">
                    <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed" data-order='[[ 1, "asc" ]]' style="width: 100%;">
                      <thead>
                        <tr>
                          <th style="max-width: 20px !important; width: 20px;">Id</th>
                          <th>Cliente</th>
                          <th>Dni/Ruc</th>
                          <th style="text-align: center">Celular</th>
                          <th style="text-align: center">AP</th>
                          <th style="text-align: center">Dirección IP</th>
                          <th style="text-align: center">Coordenadas</th>
                          <th>Dirección</th>
                          <th>Referencia</th>
                          <th>Estado</th>
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
