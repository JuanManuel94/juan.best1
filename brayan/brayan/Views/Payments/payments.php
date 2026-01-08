<?php
head($data);
modal("paymentsModal", $data);
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item"><a href="javascript:window.history.back();"><?= $data['previous_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="panel panel-default panel-payments">
  <div class="panel-heading">
    <h4 class="panel-title">Lista de pagos</h4>
    <div class="panel-heading-btn">
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-expand"
        data-original-title="" title="" data-init="true"><i class="fas fa-expand"></i></a>
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-reload"
        onclick="refresh_table()" data-original-title="" title="" data-init="true"><i class="fas fa-sync-alt"></i></a>
    </div>
  </div>
  <div class="panel-body border-panel">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-12">
        <div id="collapseview" class="box box-solid box-inverse collapse">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fas fa-filter mr-1"></i>Filtro Avanzado</h3>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label>Fecha</label>
                  <div class="input-group input-daterange">
                    <input type="text" class="form-control" id="start">
                    <span class="input-group-addon">al</span>
                    <input type="text" class="form-control" id="end">
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-12">
                <div class="form-group">
                  <label>Tipo pago</label>
                  <select class="form-control" id="listPayments" style="width:100%;"></select>
                </div>
              </div>
              <div class="col-lg-2 col-12">
                <div class="form-group">
                  <label>Usuario</label>
                  <select class="form-control" id="listUsers" style="width:100%;"></select>
                </div>
              </div>
              <div class="col-lg-2 col-12">
                <div class="form-group">
                  <label>Estado</label>
                  <select class="form-control" id="listStates" style="width:100%;">
                    <option value="0">TODOS</option>
                    <option value="1">RECIBIDAS</option>
                    <option value="2">NO RECIBIDAS</option>
                    <option value="3">ANULADAS</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-2 col-12">
                <div class="form-group">
                  <label>Zona</label>
                  <select class="form-control" id="listZona" style="width:100%;">
                    <option value="">TODOS</option>
                    <?php foreach ($data['zonas'] as $zona): ?>
                      <option value="<?= $zona['id'] ?>">
                        <?= $zona['nombre_zona'] ?>
                      </option>
                    <?php endforeach ?>
                  </select>
                </div>
              </div>
              <div class="col-lg-1 col-12">
                <div class="form-group">
                  <label class="text-white width-full">.</label>
                  <button type="button" class="btn btn-success" id="btn-search"> <i class="fa fa-search"></i> </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="list-btns-exportable" style="display: none;">
        <?php if ($_SESSION['idUser'] == 1) { ?>
          <div class="btn-group">
            <button type="button" class="btn btn-white" id="btn-select-all-list_wrapper" data-toggle="tooltip"
              data-original-title="Seleccionar todo">
              <i class="far fa-check-square f-s-14"></i>
              <span class="badge label-warning f-s-10 list_amount_selected">0</span>
            </button>
          </div>
          <div class="btn-group">
            <button type="button" class="btn btn-white" id="btn-unselect-all-list_wrapper" data-toggle="tooltip"
              data-original-title="Limpiar selección">
              <i class="far fa-square f-s-14"></i>
            </button>
          </div>
          <div class="btn-group">
            <button type="button" class="btn btn-white" onclick="cancel_massive()" data-toggle="tooltip"
              data-original-title="Cancelar pagos">
              <i class="fa fa-ban f-s-14"></i>
            </button>
          </div>
        <?php } ?>
      </div>
      <div id="list-btns-tools" style="display: none;">
        <div class="options-group btn-group m-r-5">
          <?php if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) { ?>
            <button type="button" class="btn btn-white" onclick="export_pdf()"><i
                class="far fa-file-pdf f-s-14 mr-1"></i>Exportar pdf</button>
            <button type="button" class="btn btn-white" onclick="export_excel()"><i
                class="far fa-file-excel f-s-14 mr-1"></i>Exportar excel</button>
          <?php } ?>
          <button type="button" class="btn btn-white" data-toggle="collapse" href="#collapseview"><i
              class="fas fa-filter mr-1"></i>Filtro</button>
        </div>
      </div>
      <div class="col-md-12 col-sm-12 col-12">
        <div class="table-responsive">
          <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed"
            style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 20px;"></th>
                <th>Codigo</th>
                <th>Cliente</th>
                <th>Zona</th>
                <th>Nº Fact.</th>
                <th>Fecha</th>
                <th>Total factura</th>
                <th style="max-width: 60px !important; width: 60px;">Pagado</th>
                <th>Forma pago</th>
                <th>Usuario</th>
                <th>Comentario</th>
                <th class="all">Estado</th>
                <th class="all" data-orderable="false" style="max-width: 40px !important; width: 40px;"></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
          <div class="col-xl-12 p-0 m-t-20 invoice_summary" style="margin: 0 auto;"></div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>