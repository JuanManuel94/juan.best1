<?php
head($data);
modal("billsModal", $data);
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item"><a href="javascript:window.history.back();"><?= $data['previous_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="panel panel-default panel-pendings">
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
      <div id="list-btns-exportable" style="display: none;">
        <?php if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) { ?>
          <div class="btn-group" id="btn-export">
            <button type="button" class="btn btn-white" data-toggle="tooltip" data-original-title="Exportar facturas"
              onclick="exports();"><i class="far fa-file-excel f-s-14"></i></button>
            <button type="button" id="whatsapp-massive" class="btn btn-white" data-toggle="tooltip"
              data-original-title="Envio masivo">
              ENVIO
            </button>
            <select class="form-control" name="deuda_mensual" id="deuda_mensual">
              <option value="">TODOS</option>
              <option value="1">2 MESES &#211; MAS</option>
              <option value="2">3 MESES &#211; MAS</option>
              <option value="3">4 MESES &#211; MAS</option>
              <option value="4">5 MESES &#211; MAS</option>
              <option value="5">6 MESES &#211; MAS</option>
              <option value="6">7 MESES &#211; MAS</option>
              <option value="7">8 MESES &#211; MAS</option>
              <option value="8">9 MESES &#211; MAS</option>
              <option value="9">10 MESES &#211; MAS</option>
            </select>

          </div>
        <?php } ?>
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
                <th>F.Emision</th>
                <th>F.Vencimiento</th>
                <th style="max-width: 60px !important; width: 60px;">Total</th>
                <th style="max-width: 70px !important; width: 70px;">Pendiente</th>
                <th style="max-width: 60px !important; width: 60px;">Subtotal</th>
                <th style="max-width: 70px !important; width: 70px;">Descuento</th>
                <th>Tipo</th>
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
            <div id="promiseEnabled" class="alert alert-info" style="width: 100%; padding: 0 10px;">HAY UNA PROMESA DE
              PAGO ACTIVA.</div>
            <div class="col-md-12 form-group">
              <label class="control-label">Fecha</label>
              <style>
                #date_promise::-webkit-calendar-picker-indicator {
                  font-size: 1.5rem;
                  /* Tamaño del icono */
                  cursor: pointer;
                  padding: 5px;
                }

                #date_promise {
                  height: 45px;
                }
              </style>
              <div class="input-group">
                <input type="date" class="form-control" name="date_promise" id="date_promise">
                <div class="input-group-append">

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
          <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i> Guardar<span
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
  $('#transactions_promise').submit(function (event) {
    event.preventDefault();


    var valid = true;

    if (valid) {
      var data = {};

      // disabled
      $("#text-button-payment").attr("disabled", true);
      $("#date_promise").attr("disabled", true);
      $("#date_promise").attr("disabled", true);

      data.billid = $("#idbillpromise").val();
      data.clientid = $("#idclientpromise").val();
      data.promise_date = $("#date_promise").val();
      data.promise_comment = $("#comment_promise").val();

      $.post('<?= base_url(); ?>/bills/set_promise', data).done(function (data) {
        $("#text-button-payment").attr("disabled", false);
        $("#date_promise").attr("disabled", false);
        $("#date_promise").attr("disabled", false);

        if (data.result == "success") {
          $("#modal-promise").modal("hide");
          Swal.fire({
            title: "Guardado!",
            icon: "success"
          });
        } else {
          Swal.fire({
            title: data.message,
            icon: "error"
          });
        }
      });

    }

  });

  function make_promise(idbill) {
    $('[data-toggle="tooltip"]').tooltip("hide");
    $("#transactions_promise").parsley().reset();

    var request = window.XMLHttpRequest ?
      new XMLHttpRequest() :
      new ActiveXObject("Microsoft.XMLHTTP");
    var ajaxUrl = base_url + "/bills/select_record/" + idbill;
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        var objData = JSON.parse(request.responseText);
        if (objData.status == "success") {
          var transactions_payments = document.querySelector(
            "#transactions_promise"
          );
          transactions_payments.reset();
          document.querySelector("#text-promise").innerHTML =
            "Agregar Promesa de Pago al " +
            objData.data.voucher +
            " Nº " +
            objData.data.invoice;
          document.querySelector("#idbillpromise").value =
            objData.data.encrypt_bill;
          document.querySelector("#idclientpromise").value =
            objData.data.encrypt_client;
          document.querySelector("#date_promise").value =
            objData.data.promise_date ??
            moment().add(1, "days").format("YYYY-MM-DD");
          document.querySelector("#comment_promise").value =
            objData.data.promise_comment ?? "";

          if (objData.data.promise_enabled == 1) {
            $("#promiseEnabled").show();
          } else {
            $("#promiseEnabled").hide();
          }
          $("#date_promise").attr(
            "min",
            moment().add(1, "days").format("YYYY-MM-DD")
          );
          $("#date_promise").attr(
            "max",
            moment().add(30, "days").format("YYYY-MM-DD")
          );
          list_runway();
          $("#modal-promise").modal("show");
        } else {
          alert_msg("error", objData.msg);
        }
      }
    };
  }

  function remove_promise() {
    Swal.fire({
      title: "�0�7Esta seguro?",
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