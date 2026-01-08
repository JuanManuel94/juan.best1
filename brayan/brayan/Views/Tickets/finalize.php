<?php
  head($data);
  $ticket = $data['information'];
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item"><a href="javascript:window.history.back();"><?= $data['previous_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="panel panel-default panel-tickets">
  <div class="panel-heading">
    <h4 class="panel-title">Finalizar ticket</h4>
    <div class="panel-heading-btn">
      <a href="javascript:window.history.back();" class="btn btn-xs btn-icon btn-circle btn-iconpanel"><i class="fas fa-reply"></i></a>
    </div>
  </div>
  <form autocomplete="off" name="transactions" id="transactions">
    <div class="panel-body border-panel">
      <div class="row">
        <input type="hidden" id="idticket" name="idticket" value="<?= encrypt($ticket['id']) ?>">
        <input type="hidden" id="idclient" name="idclient" value="<?= encrypt($ticket['clientid']) ?>">
        <div class="col-md-12 form-group mb-2">
          <style media="screen">
            .radio.radio-css.radio-inline + .radio-inline {
            margin-left: 0;
            }
          </style>
          <label class="control-label mb-0 f-w-600"><i class="fa fa-angle-double-right mr-2"></i>TERMINAR EL PROCESO EN:</label>
          <br>
          <div class="radio radio-css radio-inline mr-4">
            <input type="radio" name="radio_option" id="radio_yes" value="1" checked>
            <label for="radio_yes" class="cursor-pointer f-s-14">RESUELTO</label>
          </div>
          <div class="radio radio-css radio-inline mr-4">
            <input type="radio" name="radio_option" id="radio_not" value="2">
            <label for="radio_not" class="cursor-pointer f-s-14">NO RESUELTO</label>
          </div>
        </div>
        <div class="col-md-12 form-group">
          <label class="control-label f-w-600" id="text-observation"><i class="fa fa-angle-double-right mr-2"></i>DESCRIBE EL PROCESO REALIZADO:</label>
          <textarea class="form-control text-uppercase" name="observation" id="observation" rows="6" style="min-height: 50px; overflow: hidden; overflow-wrap: break-word; height: 80px;" data-parsley-required="true" placeholder="Ingrese su descripcion"></textarea>
        </div>
        <div class="col-md-12 col-md-push-5 col-sm-12 p-sm">
          <fieldset>
            <legend class="f-w-600 f-s-13">
              <i class="fa fa-angle-double-right mr-2"></i>AGREGAR FOTO
              <button type="button" class="btn-add btn btn-info btn-xs"><i class="fas fa-camera-retro f-s-20"></i></button>
            </legend>
            <p class="m-b-20 text-uppercase f-s-10">
              <strong class="mr-1">NOTA:</strong>Imagenes en formato PNG, JPG, JPEG.
            </p>
          </fieldset>
        </div>
        <input type="hidden" id="counter" value="0">
        <div class="col-md-12 col-md-push-5 col-sm-12 p-sm"><hr class="m-t-0 m-b-10"></div>
        <div class="col-md-12 col-md-push-5 col-sm-12 p-sm">
          <div id="gallery" class="row"></div>
        </div>
        <div class="col-md-12 col-md-push-5 col-sm-12 p-sm">
          <div class="form-group row justify-content-center">
            <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i>Guardar Cambios</button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>
