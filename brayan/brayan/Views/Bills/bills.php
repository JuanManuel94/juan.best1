<?php
head($data);
modal("billsModal", $data);
?>
<style>
  #list tbody tr td:nth-child(14):has(div a.promise-on)~td:nth-child(9) {
    background-color: #ff0000 !important;
  }
</style>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item"><a href="javascript:window.history.back();"><?= $data['previous_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="panel panel-default panel-bills">
  <div class="panel-heading">
    <h4 class="panel-title">Lista de facturas</h4>
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
                  <label>Estado</label>
                  <select class="form-control" id="listStates" style="width: 100%;">
                    <option value="0">TODAS</option>
                    <option value="1">PAGADAS</option>
                    <option value="2">PENDIENTES</option>
                    <option value="3">VENCIDAS</option>
                    <option value="4">ANULADAS</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-2 col-12">
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
        <?php if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) { ?>
          <?php if ($_SESSION['permits_module']['r']) { ?>
            <div class="btn-group">
              <!--<button type="button" class="btn btn-white" data-toggle="tooltip" data-original-title="Importar facturas" onclick="modal_import();"><i class="fas fa-upload"></i></button>-->
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-white" data-toggle="tooltip"
                data-original-title="Generar facturas masivas" onclick="modal_debtOpening();"><i
                  class="far fa-calendar-plus f-s-14"></i></button>
            </div>
          <?php } ?>
          <div class="btn-group">
            <button type="button" class="btn btn-white" data-toggle="tooltip" data-original-title="Exportar facturas"
              onclick="exports();"><i class="far fa-file-excel f-s-14"></i></button>
          </div>
        <?php } ?>
      </div>
      <div id="list-btns-tools" style="display: none;">
        <div class="options-group btn-group m-r-5">
          <?php if ($_SESSION['userData']['profileid'] != TECHNICAL || $_SESSION['userData']['profileid'] != CHARGES) { ?>
            <?php if ($_SESSION['permits_module']['r']) { ?>
              <button type="button" class="btn btn-white" onclick="bill_free();"><i class="fas fa-plus mr-1"></i>Factura
                libre</button>
              <button type="button" class="btn btn-white" onclick="bill_services();"><i class="fas fa-plus mr-1"></i>Factura
                servicio</button>
            <?php } ?>
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
                <th>Nº Factura</th>
                <th>Mes Fact.</th>
                <th>Cliente</th>
                <th>Zona</th>
                <th>F.Emision</th>
                <th>F.Vencimiento</th>
                <th style="max-width: 60px !important; width: 60px;">Total</th>
                <th style="max-width: 70px !important; width: 70px;">Pendiente</th>
                <th style="max-width: 60px !important; width: 60px;">Subtotal</th>
                <th style="max-width: 70px !important; width: 70px;">Descuento</th>
                <th>Tipo</th>
                <th>F.Pago</th>
                <th>Forma pago</th>
                <th>Metodo</th>
                <th>Observación</th>
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
<div id="modal-promise" class="modal fade p-0" role="dialog" style="display: none;">
  <form autocomplete="off" name="transactions_promise" id="transactions_promise">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-uppercase" id="text-promise"></h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="idbillpromise" name="idbill" value="">
          <input type="hidden" id="idclientpromise" name="idclient" value="">
          <div class="row">
            <div id="promiseEnabled" class="alert alert-info" style="width: 100%; padding: 0 10px;">Hay una promesa de
              pago habilitada.</div>
            <div class="col-md-12 form-group">
              <label class="control-label">Fecha</label>
              <div class="input-group">
                <input type="date" class="form-control" name="date_promise" id="date_promise">
                <div class="input-group-append">
                  <span class="input-group-text">
                    <i class="far fa-calendar"></i>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-12 form-group">
              <label for="comment" class="control-label">Comentario</label>
              <textarea class="form-control text-uppercase" name="comment" id="comment_promise" rows="6"
                placeholder="INGRESE COMENTARIO"
                style="min-height: 20px; overflow: hidden; overflow-wrap: break-word; height: 80px;"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"></i>Cerrar</button>
          <!-- <button type="button" class="btn btn-danger" onclick="remove_promise()"></i>Quitar promesa</button> -->
          <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i><span
              id="text-button-payment"></span></button>
        </div>
      </div>
    </div>
  </form>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.5/sweetalert2.all.min.js"
  integrity="sha512-m4zOGknNg3h+mK09EizkXi9Nf7B3zwsN9ow+YkYIPZoA6iX2vSzLezg4FnW0Q6Z1CPaJdwgUFQ3WSAUC4E/5Hg=="
  crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
  var blockRes = false;
  var search = "<?= isset($_GET['search']) ? $_GET['search'] : null ?>";
  var act_pay = "<?= isset($_GET['pay']) ? "true" : "false" ?>";

  $(function () {

  });

  $('#transactions_promise').submit(function (event) {
    event.preventDefault();
    var valid = true;

    if (!blockRes) {
      blockRes = true;

      if (valid) {
        var data = {};

        data.billid = $("#idbillpromise").val();
        data.clientid = $("#idclientpromise").val();
        data.promise_date = $("#date_promise").val();
        data.promise_comment = $("#comment_promise").val();

        $.post('<?= base_url(); ?>/bills/set_promise', data).done(function (data) {
          var res = JSON.parse(data);
          if (res.result == "success") {
            $("#modal-promise").modal("hide");
            Swal.fire({
              title: "Guardado!",
              icon: "success"
            });
          } else {
            Swal.fire({
              title: res.message,
              icon: "error"
            });
          }
        });

      }
    }

  });

  function remove_promise() {
    Swal.fire({
      title: "¿Esta seguro?",
      text: "Esta a punto de quitar esta promesa de pago",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Continuar"
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Por favor espere...',
          allowOutsideClick: false,
          showConfirmButton: false,
          onBeforeOpen: () => {
            Swal.showLoading();
          }
        });
        var data = {};

        data.billid = $("#idbillpromise").val();
        data.clientid = $("#idclientpromise").val();
        $.post('<?= base_url(); ?>/bills/unset_promise', data).done(function (data) {
          var res = JSON.parse(data);
          if (res.result == "success") {
            window.location.reload();
          }
        });
      }
    });
  }
</script>
<?php footer($data); ?>