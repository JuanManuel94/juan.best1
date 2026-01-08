<?php
  head($data);
  modal("ticketModal",$data);
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/tickets"><?= $data['previous_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="panel panel-warning panel-pendings">
    <div class="panel-heading">
        <h4 class="panel-title">Lista de tickets</h4>
        <div class="panel-heading-btn">
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-reload" onclick="refresh_table()"><i class="fas fa-sync-alt"></i></a>
        </div>
    </div>
    <div class="panel-body border-panel">
        <div class="row">
        <div id="list-btns-tools" style="display: none;">
              <div class="options-group btn-group m-r-5">
                <?php if($_SESSION['permits_module']['r']){ ?>
                <button type="button" class="btn btn-white" onclick="modal()"><i class="fas fa-plus mr-1"></i>Nuevo</button>
                <?php } ?>
                <select class="form-control" id="filter_tickets" name="tblestado" onchange="filter_tickets();" style="width: 130px">
                  <option value="0" selected>TODOS TICKETS</option>
                  <option value="1">MIS TICKETS</option>
                </select>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 col-12">
                <div class="table-responsive">
                    <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed" data-order='[[ 1, "asc" ]]' style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="max-width: 20px !important; width: 20px;">Id</th>
                                <th>Cliente</th>
                                <th>Dni/Ruc</th>
                                <th>Celular</th>
                                <th>Gps</th>
                                <th>F.programada</th>
                                <th>F.Apertura</th>
                                <th>Prioridad</th>
                                <th>Asunto</th>
                                <th>Tecnico</th>
                                <th>Creado por</th>
                                <th>Dirección</th>
                                <th>Referencia</th>
                                <th>Creado</th>
                                <th class="all">Estado</th>
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
