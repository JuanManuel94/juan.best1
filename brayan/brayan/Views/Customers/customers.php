<?php
head($data);
modal("clientsModal", $data);
?>
<!-- INICIO TITULO -->
<pre id="hideColumns" style="display: none;"><?= json_encode($data['hideColumns']) ?></pre>
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
  <input type="hidden" id="simple" value="<?= $data['simple'] ?>">
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="panel panel-default panel-clients">
  <div class="panel-heading">
    <h4 class="panel-title">Lista de clientes</h4>
    <div class="panel-heading-btn">
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-expand"><i
          class="fa fa-expand"></i></a>
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-reload"
        onclick="refresh_table()"><i class="fas fa-sync-alt"></i></a>
    </div>
  </div>
  <div class="panel-body border-panel">
    <div class="row">
      <div id="list-btns-exportable" style="display: none;">
        <?php if ($_SESSION['permits_module']['r'] && !$data['simple']) { ?>
          <div class="btn-group">
            <!--<button type="button" class="btn btn-white" data-toggle="tooltip" data-original-title="Importar clientes"
              onclick="modal_import();">
              <i class="fas fa-upload"></i>
            </button>-->
          </div>
        <?php } ?>
        <?php if (!$data['simple']): ?>
          <div class="btn-group">
            <button type="button" class="btn btn-white" data-toggle="tooltip" data-original-title="Exportar clientes"
              onclick="exports();">
              <i class="far fa-file-excel f-s-14"></i>
            </button>
          </div>
        <?php endif ?>
      </div>
      <div id="list-btns-tools" style="display: none;">
        <div class="options-group btn-group m-r-5">
          <?php if ($_SESSION['permits_module']['r'] && !$data['simple']): ?>
            <button type="button" class="btn btn-white" onclick="add()">
              <i class="fas fa-plus mr-1"></i>Nuevo
            </button>
          <?php endif ?>
          <div class="row">
            <?php if ($data['filters']): ?>
              <div class="col-12 col-md-5 col-lg-5 mb-1">
                <select class="form-control" id="orderDeuda" onchange="changeOrderDeuda()">
                  <option value="DESC">DEUDA DE MAYOR A MENOR</option>
                  <option value="ASC" selected>DEUDA DE MENOR A MAYOR</option>
                </select>
              </div>
              <div class="col-5 col-md-2 col-lg-2 mb-1">
                <select class="form-control ml-1" id="filterStart" onchange="changeFilterDays('filterStart')">
                  <option value="DESC">01</option>
                </select>
              </div>
              <div class="col-1 mb-1 text-center pt-2">
                AL
              </div>
              <div class="col-5 col-md-2 col-lg-2 mb-1">
                <select class="form-control ml-1 mr-1" id="filterOver" onchange="changeFilterDays('filterOver')">
                  <option value="DESC">01</option>
                </select>
              </div>
            <?php endif ?>
            <div class="col-12 col-md-2 col-lg-2 mb-1">
              <select class="form-control" id="filter_states" name="tblestado" onchange="filter_states();"
                style="width: 130px">
                <option value="0" selected>TODOS</option>
                <option value="1">INSTALACIÓN</option>
                <option value="2">ACTIVOS</option>
                <option value="3">SUSPENDIDOS</option>
                <option value="4">CANCELADOS</option>
                <option value="5">GRATIS</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-12 col-sm-12 col-12">
        <div class="table-responsive">
          <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed"
            data-order='[[ 1, "asc" ]]' style="width: 100%;">
            <thead>
              <tr>
                <?php if (!$data['simple']): ?>
                  <th style="max-width: 20px !important; width: 20px;">Id</th>
                <?php endif ?>
                <th>Cliente</th>
                <?php if (!$data['simple']): ?>
                  <th>Dni/Ruc</th>
                <?php endif ?>
                <th style="text-align: center">Celular</th>
                <?php if (!$data['simple']): ?>
                  <th style="text-align: center">Dirección IP</th>
                <?php endif ?>
                <?php if (!in_array("coordenadas", $data['hideColumns'])): ?>
                  <th style="text-align: center">Coordenadas</th>
                <?php endif; ?>
                <th>Deuda actual</th>
                <th>Dia pago</th>
                <?php if (!$data['simple']): ?>
                  <th>Ultimo pago</th>
                  <th>Proximo pago</th>
                <?php endif ?>
                <th>Plan</th>
                <?php if (!$data['simple']): ?>
                  <th>F.Suspendido</th>
                  <th>F.Cancelado</th>
                <?php endif ?>
                <?php if (!in_array("direccion", $data['hideColumns'])): ?>
                  <th>Dirección</th>
                <?php endif; ?>
                <?php if (!$data['simple']): ?>
                  <th>Referencia</th>
                <?php endif ?>
                <th>Estado</th>
                <?php if (!$data['simple']): ?>
                  <th class="all" data-orderable="false" style="max-width: 40px !important; width: 40px;"></th>
                <?php endif ?>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

        <!-- info -->
        <div id="info_background"></div>
      </div>
    </div>
  </div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>