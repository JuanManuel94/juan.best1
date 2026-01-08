<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php
            if(!empty($_SESSION['businessData']['favicon'])){
                if($_SESSION['businessData']['favicon'] == "favicon.png"){
                    $favicon = base_style().'/images/logotypes/'.$_SESSION['businessData']['favicon'];;
                }else{
                    $favicon_url = base_style().'/uploads/business/'.$_SESSION['businessData']['favicon'];
                    if(@getimagesize($favicon_url)){
                        $favicon = base_style().'/uploads/business/'.$_SESSION['businessData']['favicon'];
                    }else{
                        $favicon = base_style().'/images/logotypes/favicon.png';
                    }
                }
            }else{
                $favicon = base_style().'/images/logotypes/favicon.png';
            }
        ?>
        
  <link rel="icon" type="image/x-icon" href="<?= base_style() . '/images/logotypes/favicon.png' ?>">
  <link rel="stylesheet" href="<?= base_style() ?>/css/default/app.min.css">
  <link rel="stylesheet" href="<?= base_style() ?>/css/datatables.min.css" />
  <link rel="stylesheet" href="<?= base_style() ?>/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= base_style() ?>/css/superwisp.css">
  <link rel="stylesheet" href="<?= base_style() ?>/css/jquery-confirm.min.css">
  <link rel="stylesheet" href="<?= base_style() ?>/css/gritter.css">
  <link rel="stylesheet" href="<?= base_style() ?>/bookstores/simple-line-icons/css/simple-line-icons.css">
  <link rel="stylesheet" href="<?= base_style() ?>/bookstores/ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?= base_style() ?>/bookstores/gritter/css/jquery.gritter.css" />
  <link rel="stylesheet" href="<?= base_style() ?>/bookstores/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?= base_style() ?>/bookstores/smartwizard/css/smart_wizard.css">
  <link rel="stylesheet" href="<?= base_style() ?>/bookstores/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" href="<?= base_style() ?>/bookstores/lightbox/css/lightbox.css">
  <link rel="stylesheet" href="<?= base_style() ?>/css/custom.css">
  <title>CONSULTA TU RECIBO</title>
</head>

<body>
  <div id="loading"><span class="loading-spinner"></span></div>
  <div class="bg-primary" style="height: 20px; background: #eceff1 !important;"></div>

  <div class="py-2 text-center" style="background: #eceff1;">
    <?php
    if (!empty($_SESSION['businessData']['logotyope'])) {
      if ($_SESSION['businessData']['logotyope'] == "superwisp.png") {
        $logo = base_style() . '/images/logotypes/' . $_SESSION['businessData']['logotyope'];
      } else {
        $logofac_url = base_style() . '/uploads/business/' . $_SESSION['businessData']['logotyope'];
        if (@getimagesize($logofac_url)) {
          $logo = base_style() . '/uploads/business/' . $_SESSION['businessData']['logotyope'];
        } else {
          $logo = base_style() . '/images/logotypes/superwisp.png';
        }
      }
    } else {
      $logo = base_style() . '/images/logotypes/superwisp.png';
    }
    ?>
    <img src="<?= $logo ?>" id="mainlogo" style="max-width:250px; height:auto">
    <h3 class="text-center mt-2 text-dark">Consulta de Recibos</h3>
  </div>
  <div class="mt-5 container">
    <div class="d-flex justify-content-center">
      <input type="hidden" id="clientId">
      <input type="hidden" id="mobile">
      <input type="hidden" id="contry">
      <form class="col-md-4 col-lg-4" id="consulta">
        <div class="form-group mb-3">
          <label>Consulta por:</label>
          <select class="form-control" name="type" id="ftype">
            <option value="document">N° DNI/RUC</option>
            <option value="phone">N° Celular</option>
          </select>
        </div>

        <div class="form-group mb-3">
          <input name="value" id="fvalue" class="form-control" placeholder="Ingrese el valor"
            data-parsley-required="true" />
        </div>

        <div class="text-center" id="content-query">
          <button class="btn btn-dark" id="btn-save" disabled>
            Consultar
          </button>
        </div>

        <div class="text-center" id="content-info" style="display: none;">
          <div class="btn btn-dark" id="btn-new-query">
            Nueva consulta
          </div>
          <div class="btn btn-warning" id="btn-open-ticket">
            Nuevo ticket
          </div>
        </div>
      </form>
    </div>
    <div id="result" style="display: none;">
      <div class="d-flex justify-content-center mt-5">
        <div class="col-md-12">
          <h5 id="text-result"></h5>
          <div class="table-responsive">
            <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed"
              data-order='[[ 0, "desc" ]]' style="width: 100%;">
              <thead>
                <tr>
                  <th>Nº Factura</th>
                  <th>Mes Fact.</th>
                  <th>Emitido</th>
                  <th>Vencimiento</th>
                  <th style="max-width: 60px !important; width: 60px;">Total</th>
                  <th style="max-width: 70px !important; width: 70px;">Pendiente</th>
                  <th>Fecha pago</th>
                  <th>Forma pago</th>
                  <th>Observación</th>
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

  <div id="modal-voucher" class="modal fade p-0" role="dialog" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-uppercase" id="text-title-voucher"></h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="idbillvoucher">
          <input type="hidden" id="country_code">
          <input type="hidden" id="msg">
          <div class="row">
            <div class="col text-center font-weight-bold mt-3">
              <button type="button" class="btn btn-lg btn-dark" id="btn-a4"><i
                  class="fa fa-file-alt fa-5x"></i></button>
              <p>PDF A4</p>
            </div>
            <div class="col text-center font-weight-bold mt-3">
              <button type="button" class="btn btn-lg btn-dark" id="btn-ticket"><i
                  class="fa fa-receipt fa-5x"></i></button>
              <p>PDF TICKET</p>
            </div>
            <div class="col text-center font-weight-bold mt-3">
              <button type="button" class="btn btn-lg btn-dark" id="btn-print_ticket"><i
                  class="ion ion-ios-print fa-5x"></i></button>
              <p>IMPRIMIR</p>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-12">
              <div class="input-group m-b-10">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="text_country"></span>
                </div>
                <input type="text" id="bill_number_client" class="form-control" disabled
                  onkeypress="return numbers(event)" placeholder="999999999" maxlength="25">
                <div class="input-group-append">
                  <button type="button" class="btn btn-whatsapp" id="btn-msg">
                    <i class="fab fa-whatsapp fa-lg mr-1"></i>Enviar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"></i>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ticket -->
  <?= modal("consultaTicketModal") ?>

  <!-- keys -->
  <input type="hidden" id="whatsapp_key_value" value="<?= $_SESSION['businessData']['whatsapp_key'] ?>" />
  <input type="hidden" id="whatsapp_api_value" value="<?= $_SESSION['businessData']['whatsapp_api'] ?>" />
  <!-- moneda -->
  <input type="hidden" value="<?= $_SESSION['businessData']['symbol'] ?>" id="moneda_simbol">

  <!-- scripts -->
  <script> const base_url = "<?= base_url(); ?>"; </script>
  <script src="<?= base_style() ?>/js/app.min.js"></script>
  <script src="<?= base_style() ?>/js/moment.min.js"></script>
  <script src="<?= base_style() ?>/js/functions.js?v=<?= time(); ?>"></script>
  <script src="<?= base_style() ?>/js/utils.min.js"></script>
  <script src="<?= base_style() ?>/js/initial.min.js"></script>
  <script src="<?= base_style() ?>/js/theme/default.min.js"></script>
  <script src="<?= base_style() ?>/js/jquery-confirm.min.js"></script>
  <script src="<?= base_style() ?>/js/jquery.bootstrap-touchspin.min.js"></script>
  <script src="<?= base_style() ?>/js/datatables.min.js"></script>
  <script src="<?= base_style() ?>/bookstores/jszip/jszip.min.js"></script>
  <script src="<?= base_style() ?>/bookstores/pdfmake/pdfmake.min.js"></script>
  <script src="<?= base_style() ?>/bookstores/pdfmake/vfs_fonts.js"></script>
  <script src="<?= base_style() ?>/bookstores/tinymce/tinymce.min.js"></script>
  <script src="<?= base_style() ?>/bookstores/select2/js/select2.min.js"></script>
  <script src="<?= base_style() ?>/bookstores/parsleyjs/parsley.js"></script>
  <script src="<?= base_style() ?>/bookstores/smartwizard/js/jquery.smartWizard.js"></script>
  <script src="<?= base_style() ?>/bookstores/gritter/js/jquery.gritter.min.js"></script>
  <script src="<?= base_style() ?>/bookstores/jquery.maskedinput/jquery.maskedinput.js"></script>
  <script src="<?= base_style() ?>/bookstores/chartjs/js/chart.min.js"></script>
  <script src="<?= base_style() ?>/bookstores/lightbox/js/lightbox.min.js"></script>
  <script src="<?= base_style() ?>/bookstores/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
  <script src="<?= base_style() ?>/bookstores/axios/axios.min.js"></script>
  <script src="<?= base_style() ?>/js/datatable-helper.js?v=<?= time(); ?>"></script>
  <script src="<?= base_style() ?>/js/whatsapp.js?v=<?= time(); ?>""></script>
  <script src=" <?= base_style() ?>/js/functions/consultas.js?v=<?= time(); ?>"></script>
</body>

</html>