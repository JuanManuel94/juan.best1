<?php
  head($data);
  modal("productsModal",$data);
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
    <li class="breadcrumb-item"><a href="javascript:window.history.back();"><?= $data['previous_page'] ?></a></li>
    <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="panel panel-default panel-products">
    <div class="panel-heading">
        <h4 class="panel-title">Lista de productos</h4>
        <div class="panel-heading-btn">
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-reload" onclick="refresh_table()"><i class="fas fa-sync-alt"></i></a>
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
                          <div class="col-lg-2 col-12">
                              <div class="form-group">
                                  <label>Seleccionar</label>
                                 <select class="form-control log_filter" onchange="$('.search_filter').val('').focus();table.search('').columns().search( '' ).draw();">
                                      <option value="0">Codigo</option>
                                      <option value="1">Producto</option>
                                      <option value="2">Precio</option>
                                      <option value="3">Stock</option>
                                      <option value="4">Presentación</option>
                                      <option value="5">Categoria</option>
                                      <option value="6">Proveedor</option>
                                      <option value="7">Estado</option>
                                  </select>
                                </div>
                          </div>
                          <div class="col-lg-4 col-12">
                              <div class="form-group">
                                  <label>Criterio</label>
                                  <input class="form-control search_filter" type="text" autocomplete="off" onkeyup="filter($('.log_filter').val(),this.value)" placeholder="Escribre el criterio..">
                                </div>
                          </div>
                          <div class="col-lg-4 col-12">
                              <div class="form-group">
                                <label class="text-white width-full">.</label>
                                  <button class="btn btn-success" onclick="$('.search_filter').val('');table.search( '' ).columns().search( '' ).draw();"><i class="fas fa-sync-alt"></i></button>
                              </div>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
            <div id="list-btns-exportable" style="display: none;">
              <?php if($_SESSION['userData']['profileid'] == ADMINISTRATOR){ ?>
              <?php if($_SESSION['permits_module']['r']){ ?>
              <div class="btn-group">
                <button type="button" class="btn btn-white" data-toggle="tooltip" data-original-title="Importar productos" onclick="modal_import();"><i class="fas fa-upload"></i></button>
              </div>
              <?php } ?>
              <div class="btn-group">
                <button type="button" class="btn btn-white" data-toggle="tooltip" data-original-title="Exportar productos" onclick="exports();"><i class="far fa-file-excel f-s-14"></i></button>
              </div>
              <?php } ?>
            </div>
            <div id="list-btns-tools" style="display: none;">
              <div class="options-group btn-group m-r-5">
                <?php if($_SESSION['permits_module']['r']){ ?>
                <button type="button" class="btn btn-white" onclick="modal()"><i class="fas fa-plus mr-1"></i>Nuevo</button>
                <?php } ?>
                <button type="button" class="btn btn-white" data-toggle="collapse" href="#collapseview" onclick="table.search('').columns().search('').draw()"><i class="fas fa-filter mr-1"></i>Filtro</button>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 col-12">
                <div class="table-responsive">
                    <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed" data-order='[[ 1, "asc" ]]' style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Codigo</th>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Presentación</th>
                                <th>Categoria</th>
                                <th>Proveedor</th>
                                <th>Modelo</th>
                                <th>Marca</th>
                                <th>Serie</th>
                                <th>Mac</th>
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
