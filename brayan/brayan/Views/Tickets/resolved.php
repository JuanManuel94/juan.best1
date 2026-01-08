<?php
  head($data);
  modal("ticketModal",$data);
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item"><a href="javascript:window.history.back();"><?= $data['previous_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="panel panel-success panel-tickets">
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
              <div class="options-group btn-group">
                <?php if($_SESSION['permits_module']['r']){ ?>
                <button type="button" class="btn btn-white" onclick="modal()"><i class="fas fa-plus mr-1"></i>Nuevo</button>
                <?php } ?>
              </div>
            </div>
            <div id="list-btns-filter" style="display: none;">
               <div class="options-group btn-group">
                <input class="form-control text-center" type="text" id="closing_date" style="max-width: 100px;">
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
                            <th>F.Cierre</th>
                            <th>Duración</th>
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
