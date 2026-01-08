<?php
head($data);
modal("clientsModal", $data);
$documents = $data['documents'];
$contract = $data['contract_information']['contract'];
$client = $data['contract_information']['client'];
$debt = $data['contract_information']['current_debt'];
$bill = $data['contract_information']['bill'];
$pending = $data['contract_information']['pending'];
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/customers"><?= $data['previous_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header f-s-18">
  <img data-name="<?= $data['page_title'] ?>" id="image-user" style="border-radius: 100%">
  <?= $data['page_title'] ?>
</h1>
<div class="row" data-sortable="false">
  <div class="col-sm-12" data-sortable="false">
    <div class="panel panel-inverse panel-with-tabs" data-sortable="false">
      <div class="panel-heading p-0">
        <div class="tab-overflow nav-ajax" style="width: 100%">
          <ul class="nav nav-tabs nav-tabs-inverse">
            <li class="nav-item"><a href="#client-tab" data-toggle="tab" class="nav-link active" data-view="abstract"><i
                  class="fa fa-fw fa-lg fa-info-circle mr-1"></i><span class="d-none d-lg-inline">Resumen</span></a>
            </li>
            <li class="nav-item"><a href="#services-tab" data-toggle="tab" class="nav-link" data-view="services"><i
                  class="far fa-fw fa-lg fa-calendar-alt mr-1"></i><span class="d-none d-lg-inline">Planes</span></a>
            </li>
            <li class="nav-item"><a href="#billing-tab" data-toggle="tab" class="nav-link" data-view="billing"><i
                  class="fa fa-fw fa-lg fa-money-bill-alt mr-1"></i><span
                  class="d-none d-lg-inline">Facturación</span></a></li>
            <li class="nav-item"><a href="#tickets-tab" data-toggle="tab" class="nav-link" data-view="tickets"><i
                  class="far fa-fw fa-lg fa-life-ring mr-1"></i><span class="d-none d-lg-inline">Tickets</span></a></li>
            <li class="nav-item"><a href="#gallery-tab" data-toggle="tab" class="nav-link" data-view="gallery"><i
                  class="fa fa-fw fa-lg fa-image mr-1"></i><span class="d-none d-lg-inline">Galeria</span></a></li>
            <li class="nav-item"><a href="#network-tab" data-toggle="tab" class="nav-link" data-view="network"><i
                  class="fa fa-fw fa-lg fa-network-wired mr-1"></i><span class="d-none d-lg-inline">Red</span></a></li>
          </ul>
        </div>
      </div>
      <div class="panel-body tab-content">
        <div class="tab-pane fade active show" id="client-tab">
          <form autocomplete="off" name="transactions_client" id="transactions_client">
            <div class="row row-space-30">
              <div class="col-xl-7" style="border-right: 1px solid #e2e7ec !important;">
                <div class="mb-3 text-inverse f-w-600 f-s-13"><i class="fa fa-angle-double-right mr-2"></i>DATOS DEL
                  CLIENTE</div>
                <input type="hidden" id="idclients" name="idclient" value="<?= encrypt($client['id']) ?>">
                <div class="form-group row m-b-10">
                  <label class="col-md-3 text-lg-right col-form-label">Tipo doc.</label>
                  <div class="col-md-4">
                    <select class="form-control" name="listTypes" id="listTypes" style="width:100%;">
                      <?php foreach ($documents as $document) { ?>
                        <option value="<?= $document['id'] ?>" <?= (($document['id'] == $client['documentid']) ? "selected" : "") ?>><?= $document['document'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row m-b-10">
                  <label class="col-md-3 text-lg-right col-form-label">Número doc.</label>
                  <div class="col-md-4">
                    <div class="input-group">
                      <input type="text" class="form-control" name="document" id="document"
                        onkeypress="return numbers(event)"
                        maxlength="<?= (($client['documentid'] == 2) ? "8" : "11") ?>"
                        value="<?= $client['document'] ?>">
                      <div class="input-group-append">
                        <button type="button" class="btn btn-white btn-search" onclick="search_document();">
                          <i class="fa fa-search"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-3 text-lg-right col-form-label">Nombres</label>
                  <div class="col-md-9">
                    <input type="text" class="form-control text-uppercase" name="names" id="names"
                      onkeypress="return letters(event)" placeholder="INGRESE NOMBRE" value="<?= $client['names'] ?>"
                      data-parsley-group="step-1" data-parsley-required="true">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-3 text-lg-right col-form-label">Apellidos</label>
                  <div class="col-md-9">
                    <input type="text" class="form-control text-uppercase" name="surnames" id="surnames"
                      onkeypress="return letters(event)" placeholder="INGRESE APELLIDOS"
                      value="<?= $client['surnames'] ?>" data-parsley-group="step-1" data-parsley-required="true">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-3 text-lg-right col-form-label">Celulares</label>
                  <div class="col-md-9">
                    <input type="text" class="form-control m-b-10" name="mobile" id="mobile"
                      onkeypress="return numbers(event)" placeholder="999999999" maxlength="10"
                      value="<?= $client['mobile'] ?>" data-parsley-group="step-1" data-parsley-required="true">
                    <input type="text" class="form-control" name="mobileOp" id="mobileOp"
                      onkeypress="return numbers(event)" placeholder="999999999" maxlength="10"
                      value="<?= $client['mobile_optional'] ?>">
                    <small class="text-success text-uppercase m-b-10">Número telefonico opcional</small>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-3 text-lg-right col-form-label">Correo</label>
                  <div class="col-md-9">
                    <input type="text" class="form-control" name="email" id="email" onkeypress="return mail(event)"
                      placeholder="EXAMPLE@EXAMPLE.com" value="<?= $client['email'] ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-3 col-form-label text-right">Coordenadas
                    <button type="button" class="btn btn-icono btn-xs" data-toggle="tooltip" data-placement="top"
                      data-original-title="Abrir google maps" onclick="open_map()">
                      <i class="fas fa-map-marker-alt"></i>
                    </button>
                    <button type="button" class="btn btn-icono btn-xs btn-coordinates d-none" data-toggle="tooltip"
                      data-placement="top" data-original-title="Obtener Ubicación Actual"
                      data-loading-text="<i class='fas fa-spinner fa-spin'></i>" onclick="current_location()">
                      <i class="fas fa-map-marker-alt"></i>
                    </button>
                  </label>
                  <div class="col-md-9 row">
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="latitud" id="latitud" placeholder="14.254454545"
                        value="<?= $client['latitud'] ?>">
                      <small class="text-success text-uppercase m-b-10">Latitud</small>
                    </div>
                    <div class="col-md-6">
                      <input type="text" class="form-control" name="longitud" id="longitud" placeholder="-17.44587488"
                        value="<?= $client['longitud'] ?>">
                      <small class="text-success text-uppercase m-b-10">Longitud</small>
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-3 col-form-label text-right">Dirección</label>
                  <div class="col-md-9">
                    <input type="text" class="form-control text-uppercase" name="address" id="address"
                      onkeypress="return numbersandletters(event)" placeholder="INGRESE DOMICILIO"
                      value="<?= $client['address'] ?>" data-parsley-group="step-1">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-3 text-lg-right col-form-label">Referencia</label>
                  <div class="col-md-9">
                    <input type="text" class="form-control text-uppercase" name="reference" id="reference"
                      onkeypress="return numbersandletters(event)" placeholder="REFERENCIA DEL DOMICILIO (OPCIONAL)"
                      value="<?= $client['reference'] ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-3 text-lg-right col-form-label">Zona
                    <span class="text-danger">*</span></label>
                  <div class="col-md-8">
                    <select class="form-control text-center" name="zonaid" id="zona">
                      <option value="" class="text-center" <?= empty($client['zonaid']) ? 'selected' : '' ?>>Seleccionar
                        zona</option>
                      <?php
                      $conexion = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
                      $query = "SELECT * FROM zonas WHERE state = 1";
                      $resultados = mysqli_query($conexion, $query);

                      while ($fila = mysqli_fetch_assoc($resultados)) {
                        $selected = ($fila['id'] == $client['zonaid']) ? 'selected' : '';
                        echo '<option value="' . $fila['id'] . '" ' . $selected . ' class="text-center">' . $fila['nombre_zona'] . '</option>';
                      }

                      mysqli_free_result($resultados);
                      mysqli_close($conexion);
                      ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-3 text-lg-right col-form-label">Nota</label>
                  <div class="col-md-9">
                    <input type="text" class="form-control text-uppercase" name="note" id="note"
                      placeholder="NOTA DEL SERVICIO (OPCIONAL)" value="<?= $client['note'] ?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-md-3 text-lg-right col-form-label">Estado</label>
                  <div class="col-md-9">
                    <div class="form-control dv-state-user" style="border: none;">
                      <?php
                      if ($contract['state'] == 1) {
                        echo '<span class="label label-orange">INSTALACIÓN</span>';
                      } else if ($contract['state'] == 2) {
                        echo '<span class="label label-green">ACTIVO</span>';
                      } else if ($contract['state'] == 3) {
                        echo '<span class="label label-primary">SUSPENDIDO</span>';
                      } else if ($contract['state'] == 4) {
                        echo '<span class="label label-dark">CANCELADO</span>';
                      } else if ($contract['state'] == 5) {
                        echo '<span class="label label-indigo">GRATIS</span>';
                      }
                      ?>
                    </div>
                  </div>
                </div>
                <div class="form-group row justify-content-center">
                  <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i>Guardar Cambios</button>
                </div>
              </div>
              <div class="col-xl-5">
                <div class="mb-3 text-inverse f-w-600 f-s-13">
                  <i class="fa fa-angle-double-right mr-2"></i>AVISOS
                </div>
                <?php
                if ($contract['state'] == 5) {
                  $balance = $_SESSION['businessData']['symbol'] . " " . format_money($debt) . "</strong>";
                  $expiration = "00/00/0000";
                  $cutoff = "00/00/0000";
                  $create = "Desactivado";
                } else {
                  $balance = $_SESSION['businessData']['symbol'] . " " . format_money($debt) . "</strong>";
                  if ($pending >= 1) {
                    $days_grace = str_pad($contract['days_grace'], 2, "0", STR_PAD_LEFT);
                    $expiration = date("d/m/Y", strtotime($bill['expiration_date']));
                    $day = date("Y-m-d", strtotime($bill['expiration_date']));
                    $cutoff = date("d/m/Y", strtotime($day . " + " . $days_grace . " days"));
                    $create = ($contract['create_invoice'] == 0) ? "Desactivado" : date("d/m/Y", strtotime($day . "-" . $contract['create_invoice'] . " days"));
                  } else {
                    $payday = str_pad($contract['payday'], 2, "0", STR_PAD_LEFT);
                    $date_exp = date("Y-m-" . $payday);
                    $expiration = date("d/m/Y", strtotime($date_exp . " + 1 month"));
                    $day = date("Y-m-d", strtotime(date("Y-m-" . $payday) . "+ 1 month"));
                    $cutoff = date("d/m/Y", strtotime($day . " + " . $contract['days_grace'] . " days"));
                    $create = ($contract['create_invoice'] == 0) ? "Desactivado" : date("d/m/Y", strtotime($day . "-" . $contract['create_invoice'] . " days"));
                  }
                }
                ?>
                <div class="row">
                  <div class="col-md-6">
                    <div class="widget-list widget-list-rounded m-b-5" data-id="widget">
                      <div class="widget-list-item bg-success">
                        <div class="widget-list-media icon p-5">
                          <i class="fa fa-calendar-alt f-s-30 f-w-700 text-white"></i>
                        </div>
                        <div class="widget-list-content p-5">
                          <h4 class="widget-list-title text-white f-w-700"><?= $expiration ?></h4>
                          <p class="widget-list-desc text-white text-uppercase">Dia de pago</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="widget-list widget-list-rounded m-b-5" data-id="widget">
                      <div class="widget-list-item bg-indigo">
                        <div class="widget-list-media icon p-5"><i class="fa fa-envelope f-s-30 f-w-700 text-white"></i>
                        </div>
                        <div class="widget-list-content p-5">
                          <h4 class="widget-list-title text-white f-w-700"><?= $create ?></h4>
                          <p class="widget-list-desc text-white text-uppercase">Crear & Enviar</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="widget-list widget-list-rounded m-b-5" data-id="widget">
                      <div class="widget-list-item bg-danger">
                        <div class="widget-list-media icon p-5"><i
                            class="fa fa-calendar-times f-s-30 f-w-700 text-white"></i></div>
                        <div class="widget-list-content p-5">
                          <h4 class="widget-list-title text-white f-w-700"><?= $cutoff ?></h4>
                          <p class="widget-list-desc text-white text-uppercase">Dia de corte</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="widget-list widget-list-rounded m-b-5" data-id="widget">
                      <div class="widget-list-item bg-warning">
                        <div class="widget-list-media icon p-5"><i
                            class="fa fa-dollar-sign f-s-30 f-w-700 text-white"></i></div>
                        <div class="widget-list-content p-5">
                          <h4 class="widget-list-title text-white f-w-700"><?= $balance ?></h4>
                          <p class="widget-list-desc text-white text-uppercase">Deuda Actual</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div id="netBlocked" class="widget-list widget-list-rounded m-b-5" data-id="widget">
                      <div class="widget-list-item bg-dark" style="cursor: pointer;">
                        <div class="widget-list-media icon p-5"><i
                            class="fa fa-refresh icon-refresh f-s-30 f-w-700 text-white"></i></div>
                        <div class="widget-list-content p-5">
                          <h4 class="widget-list-title text-white f-w-700">Cargando...</h4>
                          <p class="widget-list-desc text-white text-uppercase">Estado de Red</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <?php
                  if ($contract['discount'] == 1) {
                    if ($contract['remaining_discount'] <= 0) {
                      $state_dis = "<strong> Descuento no vigente</strong>";
                    } else {
                      $state_dis = "<strong>Descuento vigente</strong>";
                    }
                    if ($contract['months_discount'] == 1) {
                      $month_dis = $contract['months_discount'] . " mes";
                    } else {
                      $month_dis = $contract['months_discount'] . " meses";
                    }
                    ?>
                    <div class="col-md-6">
                      <div class="widget-list widget-list-rounded m-b-5" data-id="widget">
                        <div class="widget-list-item bg-blue">
                          <div class="widget-list-media icon p-5"><i class="fa fa-percent f-s-30 f-w-700 text-white"></i>
                          </div>
                          <div class="widget-list-content p-5">
                            <h4 class="widget-list-title text-white f-w-700">
                              <?= $_SESSION['businessData']['symbol'] . " " . format_money($contract['discount_price']) ?>
                              por <?= $month_dis ?>
                            </h4>
                            <p class="widget-list-desc text-white text-uppercase"><?= $state_dis ?></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="tab-pane fade" id="services-tab">
          <input type="hidden" id="idcontract" value="<?= encrypt($contract['id']) ?>">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title"><b>Planes de Internet</b></h4>
              <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-expand"><i
                    class="fa fa-expand"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-reload"
                  onclick="refresh_internet()"><i class="fas fa-sync-alt"></i></a>
              </div>
            </div>
            <div class="panel-body border-panel">
              <div class="row">
                <div id="list-internet-btns-tools">
                  <div class="options-group btn-group m-r-5">
                    <?php if ($_SESSION['permits_module']['r']) { ?>
                      <button type="button" class="btn btn-white" onclick="add_internet()"><i
                          class="fas fa-plus mr-1"></i>Nuevo</button>
                    <?php } ?>
                  </div>
                </div>
                <div class="col-md-12 col-sm-12 col-12">
                  <div class="table-responsive">
                    <table id="list-internet"
                      class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed"
                      data-order='[[ 1, "asc" ]]' style="width: 100%;">
                      <thead>
                        <tr>
                          <th style="max-width: 60px !important; width: 60px;">Codigo</th>
                          <th>Plan</th>
                          <th>Costo</th>
                          <th>Máx. Subida</th>
                          <th>Máx. Bajada</th>
                          <th>Fecha ingreso</th>
                          <th>Estado</th>
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
          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title"><b>Planes Personalizados</b></h4>
              <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-expand"><i
                    class="fa fa-expand"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-reload"
                  onclick="refresh_personalized()"><i class="fas fa-sync-alt"></i></a>
              </div>
            </div>
            <div class="panel-body border-panel">
              <div class="row">
                <div id="list-personalized-btns-tools">
                  <div class="options-group btn-group m-r-5">
                    <?php if ($_SESSION['permits_module']['r']) { ?>
                      <button type="button" class="btn btn-white" onclick="add_personalized()"><i
                          class="fas fa-plus mr-1"></i>Nuevo</button>
                    <?php } ?>
                  </div>
                </div>
                <div class="col-md-12 col-sm-12 col-12">
                  <div class="table-responsive">
                    <table id="list-personalized"
                      class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed"
                      data-order='[[ 1, "asc" ]]' style="width: 100%;">
                      <thead>
                        <tr>
                          <th style="max-width: 60px !important; width: 60px;">Codigo</th>
                          <th>Plan</th>
                          <th>Costo</th>
                          <th>Fecha ingreso</th>
                          <th>Estado</th>
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
        </div>
        <div class="tab-pane fade" id="billing-tab">
          <div class="tabcontent">
            <ul class="tabgris nav nav-tabs nav-ajax">
              <li class="nav-items">
                <a href="#nav-bills" data-toggle="tab" data-view="bills" class="nav-link active show">
                  <span class="d-sm-block d-none"><i class="fas fa-file-alt mr-2"></i>Facturas</span>
                  <span class="d-sm-none"><i class="fas fa-file-alt fa-lg"></i></span>
                </a>
              </li>
              <li class="nav-items">
                <a href="#nav-transactions" data-toggle="tab" data-view="transactions" class="nav-link">
                  <span class="d-sm-block d-none"><i class="fa fa-bars mr-2"></i>Transacciones</span>
                  <span class="d-sm-none"><i class="fa fa-bars fa-lg"></i></span>
                </a>
              </li>
              <li class="nav-items">
                <a href="#nav-configurations" data-toggle="tab" data-view="settings" class="nav-link">
                  <span class="d-sm-block d-none"><i class="fas fa-cogs mr-2"></i>Configuración</span>
                  <span class="d-sm-none"><i class="fas fa-cogs fa-lg"></i></span>
                </a>
              </li>
            </ul>
            <div class="tab-content mb-0">
              <div class="tab-pane active show" id="nav-bills">
                <div class="row">
                  <div id="list-bills-btns-tools">
                    <div class="options-group btn-group m-r-5">
                      <?php if ($_SESSION['permits_module']['r']) { ?>
                        <?php if ($contract['state'] == 5) { ?>
                          <button type="button" class="btn btn-white" onclick="bill_free();"><i
                              class="fas fa-plus mr-1"></i>Factura libre</button>
                        <?php } else { ?>
                          <button type="button" class="btn btn-white" onclick="bill_free();"><i
                              class="fas fa-plus mr-1"></i>Factura libre</button>
                          <button type="button" class="btn btn-white" onclick="bill_services();"><i
                              class="fas fa-plus mr-1"></i>Factura servicio</button>
                        <?php } ?>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="col-md-12 col-sm-12 col-12">
                    <div class="table-responsive">
                      <table id="list-bills"
                        class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed"
                        data-order='[[ 0, "desc" ]]' style="width: 100%;">
                        <thead>
                          <tr>
                            <th>Nº Factura</th>
                            <th>Mes Fact.</th>
                            <th>Emitido</th>
                            <th>Vencimiento</th>
                            <th style="max-width: 60px !important; width: 60px;">Total</th>
                            <th style="max-width: 70px !important; width: 70px;">Pendiente</th>
                            <th>Tipo</th>
                            <th>Fecha pago</th>
                            <th>Forma pago</th>
                            <th>Metodo</th>
                            <th>Observación</th>
                            <th class="all">Estado</th>
                            <th class="all" data-orderable="false" style="max-width: 40px !important; width: 40px;">
                            </th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="nav-transactions">
                <div class="row">
                  <div class="col-md-12 col-sm-12 col-12">
                    <div class="table-responsive">
                      <table id="list-transactions"
                        class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed"
                        data-order='[[ 0, "desc" ]]' style="width: 100%;">
                        <thead>
                          <tr>
                            <th>Codigo</th>
                            <th>Nº Fact.</th>
                            <th>Fecha</th>
                            <th style="max-width: 60px !important; width: 60px;">Pagado</th>
                            <th>Forma pago</th>
                            <th>Usuario</th>
                            <th>Comentario</th>
                            <th class="all">Estado</th>
                            <th class="all" data-orderable="false" style="max-width: 40px !important; width: 40px;">
                            </th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="nav-configurations">
                <form autocomplete="off" name="transactions_contract" id="transactions_contract" class="row">
                  <input type="hidden" name="idcontract" value="<?= encrypt($contract['id']) ?>">
                  <div class="col-lg-12" data-sortable="false">
                    <div class="panel panel-default" data-sortable="false">
                      <div class="panel-heading">
                        <h4 class="panel-title"><b><i class="far fa-file mr-1"></i>Facturación</b></h4>
                      </div>
                      <div class="panel-body border-panel">
                        <div class="form-group row m-b-10">
                          <label class="col-md-4 text-lg-right col-form-label">Estado</label>
                          <div class="col-md-6">
                            <select class="form-control" name="listPlan" id="listPlan">
                              <?php
                              if ($contract['state'] == 1) {
                                echo '<option value="1" selected>Instalación</option>
                                  <option value="2">Activo</option>
                                  <option value="3">Suspendido</option>
                                  <option value="4">Cancelado</option>
                                  <option value="5">Gratis</option>';
                              }
                              if ($contract['state'] == 2) {
                                echo '<option value="1">Instalación</option>
                                  <option value="2" selected>Activo</option>
                                  <option value="3">Suspendido</option>
                                  <option value="4">Cancelado</option>
                                  <option value="5">Gratis</option>';
                              }
                              if ($contract['state'] == 3) {
                                echo '<option value="1">Instalación</option>
                                  <option value="2">Activo</option>
                                  <option value="3" selected>Suspendido</option>
                                  <option value="4">Cancelado</option>
                                  <option value="5">Gratis</option>';
                              }
                              if ($contract['state'] == 4) {
                                echo ' <option value="1">Instalación</option>
                                  <option value="2">Activo</option>
                                  <option value="3">Suspendido</option>
                                  <option value="4" selected>Cancelado</option>
                                  <option value="5">Gratis</option>';
                              }
                              if ($contract['state'] == 5) {
                                echo '<option value="1">Instalación</option>
                                  <option value="2">Activo</option>
                                  <option value="3">Suspendido</option>
                                  <option value="4">Cancelado</option>
                                  <option value="5" selected>Gratis</option>';
                              }
                              ?>
                            </select>
                          </div>
                        </div>

                        <div class="form-group row m-b-10 cont-day">
                          <label class="col-md-4 text-lg-right col-form-label">Modalidad</label>
                          <div class="col-md-6">
                            <select class="form-control" name="modalidad" id="modalidad">
                              <option value="POSTPAGO" <?= $contract['modalidad'] == 'POSTPAGO' ? 'selected' : '' ?>>
                                Post-Pago
                              </option>
                              <option value="PREPAGO" <?= $contract['modalidad'] == 'PREPAGO' ? 'selected' : '' ?>>
                                Pre-Pago
                              </option>
                            </select>
                          </div>
                        </div>

                        <div class="form-group row m-b-10 cont-day">
                          <label class="col-md-4 text-lg-right col-form-label">Dia de pago</label>
                          <div class="col-md-6">
                            <select class="form-control" name="listPayday" id="listPayday">
                              <?php
                              for ($i = 1; $i < 28 + 1; $i++) {
                                if ($i == $contract['payday']) {
                                  echo '<option value="' . $i . '" selected>' . $i . '</option>';
                                } else {
                                  echo '<option value="' . $i . '" >' . $i . '</option>';
                                }
                              }
                              ?>
                            </select>
                          </div>
                        </div>

                        <!--<div class="form-group row m-b-10 cont-create">
                          <label class="col-md-4 text-lg-right col-form-label">Crear factura</label>
                          <div class="col-md-6">
                            <select class="form-control" name="listInvoice" id="listInvoice">
                              <?php
                              for ($i = 0; $i < 25 + 1; $i++) {
                                if ($i == $contract['create_invoice']) {
                                  if ($contract['create_invoice'] == 0) {
                                    echo '<option value="0" selected>Desactivado</option>';
                                  } else if ($contract['create_invoice'] == 1) {
                                    echo '<option value="' . $i . '" selected>' . $i . ' Día antes</option>';
                                  } else {
                                    echo '<option value="' . $i . '" selected>' . $i . ' Días antes</option>';
                                  }
                                } else {
                                  if ($i == 0) {
                                    echo '<option value="0">Desactivado</option>';
                                  } else if ($i == 1) {
                                    echo '<option value="' . $i . '">' . $i . ' Día antes</option>';
                                  } else {
                                    echo '<option value="' . $i . '">' . $i . ' Días antes</option>';
                                  }
                                }
                              }
                              ?>
                            </select>
                          </div>
                        </div>-->
                        <div class="form-group row m-b-10 cont-gracia">
                          <label class="col-md-4 text-lg-right col-form-label">Dias de gracia</label>
                          <div class="col-md-6">
                            <select class="form-control" name="listDaysGrace" id="listDaysGrace">
                              <?php
                              for ($i = 0; $i < 25 + 1; $i++) {
                                if ($i == $contract['days_grace']) {
                                  if ($contract['days_grace'] == 1) {
                                    echo '<option value="' . $i . '" selected>' . $i . ' Día</option>';
                                  } else {
                                    echo '<option value="' . $i . '" selected>' . $i . ' Días</option>';
                                  }
                                } else {
                                  if ($i == 1) {
                                    echo '<option value="' . $i . '">' . $i . ' Día</option>';
                                  } else {
                                    echo '<option value="' . $i . '">' . $i . ' Días</option>';
                                  }
                                }
                              }
                              ?>
                            </select>
                            <small class="text-success text-uppercase">Días tolerancia para aplicar corte</small>
                          </div>
                        </div>
                        <div class="form-group row m-b-10 cont-chk">
                          <label class="col-md-4 text-lg-right col-form-label"></label>
                          <div class="col-md-6">
                            <div class="checkbox checkbox-css pt-0">
                              <input type="checkbox" id="chkDiscount" name="chkDiscount" value="1" <?php if ($contract['discount'] == 1) {
                                echo 'checked';
                              } ?>>
                              <label for="chkDiscount" class="cursor-pointer m-0">Agregar descuento</label>
                            </div>
                            <small class="text-success text-uppercase">Solo aplica a facturas de servicios</small>
                          </div>
                        </div>
                        <div class="form-group row m-b-10 cont-dis">
                          <label class="col-md-4 text-lg-right col-form-label">Descuento</label>
                          <div class="col-md-6">
                            <input type="number" class="form-control" name="discount" id="discount" min="0" step="0.1"
                              onkeypress="return numbers(event)" placeholder="0.00"
                              value="<?= $contract['discount_price'] ?>">
                          </div>
                        </div>
                        <div class="form-group row m-b-10 cont-month">
                          <label class="col-md-4 text-lg-right col-form-label">Meses de descuento</label>
                          <div class="col-md-6">
                            <select class="form-control" name="listMonthDis" id="listMonthDis">
                              <?php
                              for ($i = 1; $i < 12 + 1; $i++) {
                                if ($i == $contract['months_discount']) {
                                  if ($contract['months_discount'] == 0) {
                                    echo '<option value="' . $i . '" selected>' . $i . ' Mes</option>';
                                  } elseif ($contract['months_discount'] == 1) {
                                    echo '<option value="' . $i . '" selected>' . $i . ' Mes</option>';
                                  } else {
                                    echo '<option value="' . $i . '" selected>' . $i . ' Meses</option>';
                                  }
                                } else {
                                  if ($i == 1) {
                                    echo '<option value="' . $i . '" >' . $i . ' Mes</option>';
                                  } else {
                                    echo '<option value="' . $i . '" >' . $i . ' Meses</option>';
                                  }
                                }
                              }
                              ?>
                            </select>
                          </div>
                        </div>
                        <div class="form-group row justify-content-center">
                          <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i>Guardar
                            Cambios</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="tickets-tab">
          <div class="row">
            <div id="list-ticket-btns-tools">
              <div class="options-group btn-group m-r-5">
                <?php if ($_SESSION['permits_module']['r']) { ?>
                  <button type="button" class="btn btn-white" onclick="add_ticket()"><i
                      class="fas fa-plus mr-1"></i>Nuevo</button>
                <?php } ?>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 col-12">
              <div class="table-responsive">
                <table id="list-ticket" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed"
                  data-order='[[ 0, "asc" ]]' style="width: 100%;">
                  <thead>
                    <tr>
                      <th style="max-width: 20px !important; width: 20px;">Id</th>
                      <th>Asunto</th>
                      <th>F.programada</th>
                      <th>F.apertura</th>
                      <th>F.cierre</th>
                      <th>Prioridad</th>
                      <th>Tecnico</th>
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
        <div class="tab-pane fade" id="gallery-tab">
          <div class="row">
            <div class="col-md-12 col-md-push-5 col-sm-12 p-sm">
              <fieldset>
                <legend class="f-w-600 f-s-13">
                  <i class="fa fa-angle-double-right mr-2"></i>AGREGAR FOTO
                  <?php if ($_SESSION['permits_module']['r']) { ?>
                    <button type="button" class="btn-add btn btn-info btn-xs"><i
                        class="fas fa-camera-retro f-s-20"></i></button>
                  <?php } ?>
                </legend>
              </fieldset>
            </div>
            <div class="col-md-12 col-sm-12 col-12">
              <hr class="m-t-0 m-b-10">
            </div>
            <div class="col-md-12 col-sm-12 col-12">
              <div id="gallery" class="row"></div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="network-tab">
          <div class="row">
            <div class="col-md-12 col-md-push-5 col-sm-12 p-sm">
              <div class="mb-3 text-inverse f-w-600 f-s-13"><i class="fa fa-angle-double-right mr-2"></i>DATOS DE RED
              </div>
              <input type="hidden" id="idnetclient" name="idclient" value="<?= encrypt($client['id']) ?>">

              <div id="network_mount"></div>

              <pre id="clientData" style="display: none;"><?= json_encode($client) ?></pre>
              <input type="hidden" id="clientNetPassword"
                value="<?= decrypt_aes($client['net_password'], SECRET_IV) ?>" />

              <div class="form-group row justify-content-center">
                <a href="#!" class="btn btn-blue" onclick="saveNet()"><i class="fas fa-save mr-2"></i>Guardar
                  Cambios</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="network_ip_mount"></div>

<div class="modal fade" id="Tools" tabindex="-1" role="dialog" aria-labelledby="Tools-label">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="Tools-label">Herramientas</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- FIN TITULO -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  var client_data = JSON.parse('<?= json_encode($client) ?>');
  var blockSaveNetRes = false;

  function saveNet() {
    var valid = true;

    if (!blockSaveNetRes) {
      blockSaveNetRes = true;

      $('#network-tab').find(".form-control:visible").each(function () {
        if ($(this).val().length == 0) {
          $(this).focus();
          valid = false;
        }
      });

      if (valid) {
        var data = {};

        data.clientid = $("#idnetclient").val();
        data.net_router = $("#netRouter").val();
        data.net_name = $("#netName").val();
        data.net_password = $("#netPassword").val();
        data.net_ip = $("#netIP").val();
        data.net_localaddress = $("#netLocalAddress").val();
        data.nap_cliente_id = getNapClientValue().val();
        data.ap_cliente_id = getApClientValue().val();

        Swal.fire({
          title: 'Por favor espere...',
          allowOutsideClick: false,
          showConfirmButton: false,
          onBeforeOpen: () => {
            Swal.showLoading();
          }
        });

        $.post('<?= base_url(); ?>/Customers/modify_network', data).done(function (data) {
          var res = JSON.parse(data);
          if (res.result == "success") {
            Swal.fire({
              title: "Guardado!",
              icon: "success"
            });
          } else {
            blockRes = false;
            Swal.fire({
              icon: 'error',
              title: 'No se pudo conectar',
              text: 'Revisa la información de conexión del Router.',
            });
          }
          blockSaveNetRes = false;
        });
      }
    }
  }
  const BASE_URL = "<?= base_url() ?>";


</script>
<?php footer($data); ?>