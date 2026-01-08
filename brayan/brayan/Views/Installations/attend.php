<?php
  head($data);
  $information = $data['information'];
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
    <li class="breadcrumb-item"><a href="<?= base_url() ?>/installations"><?= $data['previous_page'] ?></a></li>
    <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header f-s-18">
    <img data-name="<?= $data['page_title'] ?>" id="image-user" style="border-radius: 100%">
    <?= $data['page_title'] ?>
</h1>
<form autocomplete="off" name="transactions" id="transactions" class="form-control-with-bg">
    <div id="wizard">
        <ul>
            <li>
                <a href="#step-1">
                    <span class="number">1</span>
                    <span class="info">
                        Información del cliente
                        <small>Nombres,apellidos,dni,etc.</small>
                    </span>
                </a>
            </li>
            <li>
                <a href="#step-2">
                    <span class="number">2</span>
                    <span class="info">
                        Ubicación
                        <small>Coordenadas del domicilio.</small>
                    </span>
                </a>
            </li>
            <li>
                <a href="#step-3">
                    <span class="number">3</span>
                    <span class="info">
                         Evidencias
                         <small>Fotos y observaciones.</small>
                    </span>
                </a>
            </li>
        </ul>
        <div>
            <div id="step-1">
                <fieldset>
                  <div class="row row-space-30">
                      <div class="col-xl-9">
                          <div class="mb-3 text-inverse f-w-600 f-s-13"><i class="fa fa-angle-double-right mr-2"></i>DATOS DEL CLIENTE</div>
                          <div class="form-group row m-b-10">
                              <label class="col-md-3 text-lg-right col-form-label">N° Dni</label>
                              <div class="col-md-4">
                                  <input type="text" class="form-control" value="<?= $information['document'] ?>" readonly>
                              </div>
                          </div>
                          <div class="form-group row">
                              <label class="col-md-3 text-lg-right col-form-label">Nombres</label>
                              <div class="col-md-9">
                                  <input type="text" class="form-control text-uppercase" value="<?= $information['names'] ?>" readonly>
                              </div>
                          </div>
                          <div class="form-group row">
                              <label class="col-md-3 text-lg-right col-form-label">Apellidos</label>
                              <div class="col-md-9">
                                  <input type="text" class="form-control text-uppercase" value="<?= $information['surnames'] ?>" readonly>
                              </div>
                          </div>
                          <div class="form-group row">
                              <label class="col-md-3 text-lg-right col-form-label">Celulares</label>
                              <div class="col-md-9">
                                  <input type="text" class="form-control m-b-10" value="<?= $information['mobile'] ?>" readonly>
                                  <input type="text" class="form-control" value="<?= $information['mobile_optional'] ?>" readonly>
                                  <small class="text-success text-uppercase m-b-10">Número telefonico opcional</small>
                              </div>
                          </div>
                          <div class="form-group row">
                              <label class="col-md-3 text-lg-right col-form-label">Correo</label>
                              <div class="col-md-9">
                                  <input type="text" class="form-control" value="<?= $information['email'] ?>" readonly>
                              </div>
                          </div>
                          <div class="form-group row">
                              <label class="col-md-3 col-form-label text-right">Dirección </label>
                              <div class="col-md-9">
                                  <input type="text" class="form-control text-uppercase" value="<?= $information['address'] ?>" readonly>
                              </div>
                          </div>
                          <div class="form-group row">
                              <label class="col-md-3 text-lg-right col-form-label">Referencia</label>
                              <div class="col-md-9">
                                  <input type="text" class="form-control text-uppercase" value="<?= $information['reference'] ?>" readonly>
                              </div>
                          </div>
                          <div class="form-group row">
                              <label class="col-md-3 text-lg-right col-form-label">Tipo de red</label>
                              <div class="col-md-9">
                                  <select class="form-control text-uppercase" name="red_type"> 
                                  <option value="">Selecionar</option>
                                  <option value="1">INALÁMBRICA</option>
                                  <option value="2">FIBRA ÓPTICA</option>
                                </select>
                              </div>
                          </div>
            
                      </div>
                  </div>
                </fieldset>
            </div>
            <div id="step-2" class="p-0">
              <fieldset>
                <input type="hidden" id="idfacility" name="idfacility" value="<?= encrypt($information['id']) ?>">
                <input type="hidden" id="idclient" name="idclient" value="<?= encrypt($information['clientid']) ?>">
                <div class="row">
                  <input type="hidden" class="latitud form-control" name="latitud" id="latitud">
                  <input type="hidden" class="longitud form-control" name="longitud" id="longitud">
                  <div class="col-md-12 col-md-push-5 col-sm-12 p-sm">
                    <div id="map-canvas" style="width: 100%;height: 500px;position: relative;"></div>
                  </div>
                </div>
              </fieldset>
            </div>
            <div id="step-3">
              <fieldset>
                <div class="row">
                  <div class="col-md-12 form-group">
                    <style media="screen">
                      .radio.radio-css.radio-inline + .radio-inline {
                      margin-left: 0;
                      }
                    </style>
                    <label class="control-label mb-0 f-w-600"><i class="fa fa-angle-double-right mr-2"></i>TERMINAR EL PROCESO EN:</label>
                    <br>
                    <div class="radio radio-css radio-inline mr-4">
                      <input type="radio" name="radio_option" id="radio_yes" value="1" checked>
                      <label for="radio_yes" class="cursor-pointer f-s-14">COMPLETADO</label>
                    </div>
                    <div class="radio radio-css radio-inline">
                      <input type="radio" name="radio_option" id="radio_not" value="2">
                      <label for="radio_not" class="cursor-pointer f-s-14">NO COMPLETADO</label>
                    </div>
                  </div>
                  <div class="col-md-12 form-group">
                    <label for="observation" class="control-label f-w-600" id="text-observation">DESCRIBE EL PROCESO REALIZADO:</label>
                    <textarea class="form-control text-uppercase" name="observation" id="observation" rows="6" style="min-height: 50px; overflow: hidden; overflow-wrap: break-word; height: 80px;" data-parsley-group="step-3" placeholder="Ingrese observación"></textarea>
                  </div>
                  <div class="col-md-12 col-md-push-5 col-sm-12 p-sm">
                    <fieldset>
                        <legend class="f-w-600 f-s-13">
                           <i class="fa fa-angle-double-right mr-2"></i>AGREGAR FOTO
                           <?php if($_SESSION['permits_module']['r']){ ?>
                             <button type="button" class="btn-add btn btn-info btn-xs"><i class="fas fa-camera-retro" style="font-size:20px"></i></button>
                           <?php } ?>
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
                </div>
              </fieldset>
            </div>
        </div>
    </div>
</form>
<!-- FIN TITULO -->
<?php footer($data); ?>
