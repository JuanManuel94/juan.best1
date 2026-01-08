<?php
head($data);
$currencys = $data['options']['currencys'];
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item"><a href="javascript:window.history.back();"><?= $data['previous_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="row" data-sortable="false">
  <div class="col-sm-12" data-sortable="false">
    <div class="panel panel-inverse panel-with-tabs" data-sortable="false">
      <div class="panel-heading p-0">
        <div class="tab-overflow nav-ajax" style="width: 100%">
          <ul class="nav nav-tabs nav-tabs-inverse">
            <li class="nav-item"><a href="#general-tab" data-toggle="tab" class="nav-link active"><i
                  class="fa fa-fw fa-lg fa-question-circle mr-1"></i><span class="d-none d-lg-inline">General</span></a>
            </li>
            <li class="nav-item"><a href="#invoice-tab" data-toggle="tab" class="nav-link"><i
                  class="fas fa-fw fa-lg fa-file-alt mr-1"></i><span class="d-none d-lg-inline">Facturación</span></a>
            </li>
            <li class="nav-item"><a href="#api-tab" data-toggle="tab" class="nav-link"><i
                  class="fas fa-fw fa-lg fa-cubes mr-1"></i><span class="d-none d-lg-inline">Apis</span></a></li>
          </ul>
        </div>
      </div>
      <div class="panel-body tab-content">
        <div class="tab-pane fade active show" id="general-tab">
          <div id="accordion" class="accordion">
            <div class="card">
              <div class="card-header pointer-cursor d-flex align-items-center collapsed" data-toggle="collapse"
                data-target="#collapseOne" aria-expanded="true">
                <i class="fa fa-building fa-fw mr-2"></i>Datos de la empresa
              </div>
              <div id="collapseOne" class="collapse show" data-parent="#accordion" style="">
                <div class="card-body">
                  <form autocomplete="off" name="transactions_general" id="transactions_general"
                    class="row row-space-30">
                    <div class="col-xl-12">
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Razon Social <span
                            class="text-danger">*</span></label>
                        <div class="col-md-8">
                          <input type="text" class="form-control text-uppercase" name="business_name" id="business_name"
                            onkeypress="return letters(event)" value="<?= $_SESSION['businessData']['business_name'] ?>"
                            data-parsley-required="true">
                        </div>
                      </div>
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Nombre Comercial</label>
                        <div class="col-md-8">
                          <input type="text" class="form-control text-uppercase" name="tradename" id="tradename"
                            onkeypress="return numbersandletters(event)"
                            value="<?= $_SESSION['businessData']['tradename'] ?>">
                        </div>
                      </div>
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">N° Ruc <span
                            class="text-danger">*</span></label>
                        <div class="col-md-8">
                          <input type="text" class="form-control" name="document" id="document"
                            onkeypress="return numbers(event)" value="<?= $_SESSION['businessData']['ruc'] ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-md-3 text-lg-right col-form-label">Celulares <span
                            class="text-danger">*</span></label>
                        <div class="col-md-8">
                          <input type="text" class="form-control m-b-10" name="mobile" id="mobile"
                            onkeypress="return numbers(event)" maxlength="10" data-parsley-required="true"
                            value="<?= $_SESSION['businessData']['mobile'] ?>">
                          <input type="text" class="form-control" name="mobileReference" id="mobileReference"
                            onkeypress="return numbers(event)" maxlength="10"
                            value="<?= $_SESSION['businessData']['mobile_refrence'] ?>">
                          <small class="text-success text-uppercase m-b-10">Número telefonico opcional</small>
                        </div>
                      </div>
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Dirección</label>
                        <div class="col-md-8">
                          <input type="text" class="form-control text-uppercase" name="address" id="address"
                            value="<?= $_SESSION['businessData']['address'] ?>">
                        </div>
                      </div>
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Mes de Corte</label>
                        <div class="col-md-8">
                          <select name="month_corte" class="form-control text-uppercase">
                            <?php for ($i = 1; $i < 6; $i++): ?>
                              <option value="<?= $i ?>" <?= $_SESSION['businessData']['month_corte'] == $i ? "selected" : "" ?>>
                                <?= $i ?>
                              </option>
                            <?php endfor ?>
                          </select>
                        </div>
                      </div>
                      <div class="form-group row justify-content-center">
                        <button type="submit" class="btn btn-blue">
                          <i class="fas fa-save mr-2"></i>Guardar Cambios
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-header pointer-cursor d-flex align-items-center" data-toggle="collapse"
                data-target="#collapseTwo" aria-expanded="false">
                <i class="fa fa-cogs fa-fw mr-2"></i>Configuración basica
              </div>
              <div id="collapseTwo" class="collapse" data-parent="#accordion" style="">
                <div class="card-body">
                  <form autocomplete="off" name="transactions_basic" id="transactions_basic" class="row row-space-30">
                    <div class="col-xl-12">
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Eslogan</label>
                        <div class="col-md-8">
                          <input type="text" class="form-control text-uppercase" name="slogan" id="slogan"
                            onkeypress="return letters(event)" value="<?= $_SESSION['businessData']['slogan'] ?>">
                        </div>
                      </div>
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Departamento</label>
                        <div class="col-md-8">
                          <input type="text" class="form-control text-uppercase" name="department" id="department"
                            value="<?= $_SESSION['businessData']['department'] ?>">
                        </div>
                      </div>
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Provincia</label>
                        <div class="col-md-8">
                          <input type="text" class="form-control text-uppercase" name="province" id="province"
                            value="<?= $_SESSION['businessData']['province'] ?>">
                        </div>
                      </div>
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Distrito</label>
                        <div class="col-md-8">
                          <input type="text" class="form-control text-uppercase" name="district" id="district"
                            value="<?= $_SESSION['businessData']['district'] ?>">
                        </div>
                      </div>
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Ubigeo</label>
                        <div class="col-md-8">
                          <input type="text" class="form-control" name="ubigeo" id="ubigeo"
                            onkeypress="return numbers(event)" value="<?= $_SESSION['businessData']['ubigeo'] ?>">
                          <small class="text-success text-uppercase m-b-10">Código de ubicación</small>
                        </div>
                      </div>
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Código de pais</label>
                        <div class="col-md-8">
                          <select class="form-control" id="listCountry" name="listCountry">
                            <?= countrySelector($_SESSION['businessData']['country_code']) ?>
                          </select>
                        </div>
                      </div>
                      <div class="form-group row justify-content-center">
                        <button type="submit" class="btn btn-blue">
                          <i class="fas fa-save mr-2"></i>Guardar Cambios
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-header pointer-cursor d-flex align-items-center collapsed" data-toggle="collapse"
                data-target="#collapseThree">
                <i class="fa fa-file-image fa-fw mr-2"></i>Logo de inicio de sesión
              </div>
              <div id="collapseThree" class="collapse" data-parent="#accordion">
                <div class="card-body">
                  <form name="transactions_logo" id="transactions_logo" class="row">
                    <?php
                    if (!empty($_SESSION['businessData']['logo_login'])) {
                      if ($_SESSION['businessData']['logo_login'] == "superwisp_white.png") {
                        $logolog = base_style() . '/images/logotypes/' . $_SESSION['businessData']['logo_login'];
                      } else {
                        $logolog_url = base_style() . '/uploads/business/' . $_SESSION['businessData']['logo_login'];
                        if (@getimagesize($logolog_url)) {
                          $logolog = base_style() . '/uploads/business/' . $_SESSION['businessData']['logo_login'];
                        } else {
                          $logolog = base_style() . '/images/logotypes/superwisp_white.png';
                        }
                      }
                    } else {
                      $logolog = base_style() . '/images/logotypes/superwisp_white.png';
                    }
                    ?>
                    <input type="hidden" id="logo-actual" name="logo-actual"
                      value="<?= $_SESSION['businessData']['logo_login'] ?>">
                    <div class="col-md-12 col-sm-12 col-12 text-center">
                      <div class="image">
                        <div class="cont-image">
                          <label for="logo"></label>
                          <div class="prev-image">
                            <img class="img-responsive" id="image-logo" src="<?= $logolog ?>">
                          </div>
                        </div>
                        <div class="upload-image">
                          <input type="file" name="logo" id="logo">
                        </div>
                      </div>
                      <small class="text-success text-uppercase m-b-10">Max. 210 KB</small>
                    </div>
                    <div class="col-md-12 col-sm-12 col-12 text-center mt-2">
                      <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-2"></i>Subir
                        logo</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-header pointer-cursor d-flex align-items-center collapsed" data-toggle="collapse"
                data-target="#collapseFour">
                <i class="fab fa-fly fa-fw mr-2"></i>Favicon
              </div>
              <div id="collapseFour" class="collapse" data-parent="#accordion">
                <div class="card-body">
                  <form name="transactions_favicon" id="transactions_favicon" class="row">
                    <?php
                    if (!empty($_SESSION['businessData']['favicon'])) {
                      if ($_SESSION['businessData']['favicon'] == "favicon.png") {
                        $favicon = base_style() . '/images/logotypes/' . $_SESSION['businessData']['favicon'];
                        ;
                      } else {
                        $favicon_url = base_style() . '/uploads/business/' . $_SESSION['businessData']['favicon'];
                        if (@getimagesize($favicon_url)) {
                          $favicon = base_style() . '/uploads/business/' . $_SESSION['businessData']['favicon'];
                        } else {
                          $favicon = base_style() . '/images/logotypes/favicon.png';
                        }
                      }
                    } else {
                      $favicon = base_style() . '/images/logotypes/favicon.png';
                    }
                    ?>
                    <input type="hidden" id="fa-actual" name="fa-actual"
                      value="<?= $_SESSION['businessData']['favicon'] ?>">
                    <div class="col-md-12 col-sm-12 col-12 text-center">
                      <div class="favicon">
                        <div class="cont-favicon">
                          <label for="favicon"></label>
                          <div class="prev-favicon">
                            <img class="img-responsive" id="image-favicon" src="<?= $favicon ?>">
                          </div>
                        </div>
                        <div class="upload-image">
                          <input type="file" name="favicon" id="favicon">
                        </div>
                      </div>
                      <small class="text-success text-uppercase m-b-10">Max. 160 KB</small>
                    </div>
                    <div class="col-md-12 col-sm-12 col-12 text-center mt-2">
                      <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-2"></i>Subir
                        favicon</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-header pointer-cursor d-flex align-items-center collapsed" data-toggle="collapse"
                data-target="#collapseFive">
                <i class="fas fa-image fa-fw mr-2"></i>Fondo de inicio de sesión
              </div>
              <div id="collapseFive" class="collapse" data-parent="#accordion">
                <div class="card-body">
                  <form name="transactions_background" id="transactions_background" class="row">
                    <div class="col-6 col-md-4 col-lg-2">
                      <span class="bmd-form-group is-filled">
                        <div class="radio radio-css radio-inline">
                          <input id="select_1" type="radio" name="background" value="bg-1.jpeg" <?php if ($_SESSION['businessData']['background'] == "bg-1.jpeg") {
                            echo 'checked';
                          } ?>>
                          <label for="select_1" class="cursor-pointer">
                            <img src="<?= base_style() ?>/images/background/bg-1.jpeg"
                              class="img-fluid img-avatar-form">
                          </label>
                        </div>
                      </span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                      <span class="bmd-form-group is-filled">
                        <div class="radio radio-css radio-inline">
                          <input id="select_2" type="radio" name="background" value="bg-2.jpeg" <?php if ($_SESSION['businessData']['background'] == "bg-2.jpeg") {
                            echo 'checked';
                          } ?>>
                          <label for="select_2" class="cursor-pointer">
                            <img src="<?= base_style() ?>/images/background/bg-2.jpeg"
                              class="img-fluid img-avatar-form">
                          </label>
                        </div>
                      </span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                      <span class="bmd-form-group is-filled">
                        <div class="radio radio-css radio-inline">
                          <input id="select_3" type="radio" name="background" value="bg-3.jpeg" <?php if ($_SESSION['businessData']['background'] == "bg-3.jpeg") {
                            echo 'checked';
                          } ?>>
                          <label for="select_3" class="cursor-pointer">
                            <img src="<?= base_style() ?>/images/background/bg-3.jpeg"
                              class="img-fluid img-avatar-form">
                          </label>
                        </div>
                      </span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                      <span class="bmd-form-group is-filled">
                        <div class="radio radio-css radio-inline">
                          <input id="select_4" type="radio" name="background" value="bg-4.jpeg" <?php if ($_SESSION['businessData']['background'] == "bg-4.jpeg") {
                            echo 'checked';
                          } ?>>
                          <label for="select_4" class="cursor-pointer">
                            <img src="<?= base_style() ?>/images/background/bg-4.jpeg"
                              class="img-fluid img-avatar-form">
                          </label>
                        </div>
                      </span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                      <span class="bmd-form-group is-filled">
                        <div class="radio radio-css radio-inline">
                          <input id="select_5" type="radio" name="background" value="bg-5.jpeg" <?php if ($_SESSION['businessData']['background'] == "bg-5.jpeg") {
                            echo 'checked';
                          } ?>>
                          <label for="select_5" class="cursor-pointer">
                            <img src="<?= base_style() ?>/images/background/bg-5.jpeg"
                              class="img-fluid img-avatar-form">
                          </label>
                        </div>
                      </span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                      <span class="bmd-form-group is-filled">
                        <div class="radio radio-css radio-inline">
                          <input id="select_6" type="radio" name="background" value="bg-6.jpeg" <?php if ($_SESSION['businessData']['background'] == "bg-6.jpeg") {
                            echo 'checked';
                          } ?>>
                          <label for="select_6" class="cursor-pointer">
                            <img src="<?= base_style() ?>/images/background/bg-6.jpeg"
                              class="img-fluid img-avatar-form">
                          </label>
                        </div>
                      </span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                      <span class="bmd-form-group is-filled">
                        <div class="radio radio-css radio-inline">
                          <input id="select_7" type="radio" name="background" value="bg-7.jpeg" <?php if ($_SESSION['businessData']['background'] == "bg-7.jpeg") {
                            echo 'checked';
                          } ?>>
                          <label for="select_7" class="cursor-pointer">
                            <img src="<?= base_style() ?>/images/background/bg-7.jpeg"
                              class="img-fluid img-avatar-form">
                          </label>
                        </div>
                      </span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                      <span class="bmd-form-group is-filled">
                        <div class="radio radio-css radio-inline">
                          <input id="select_8" type="radio" name="background" value="bg-8.jpeg" <?php if ($_SESSION['businessData']['background'] == "bg-8.jpeg") {
                            echo 'checked';
                          } ?>>
                          <label for="select_8" class="cursor-pointer">
                            <img src="<?= base_style() ?>/images/background/bg-8.jpeg"
                              class="img-fluid img-avatar-form">
                          </label>
                        </div>
                      </span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                      <span class="bmd-form-group is-filled">
                        <div class="radio radio-css radio-inline">
                          <input id="select_9" type="radio" name="background" value="bg-9.jpeg" <?php if ($_SESSION['businessData']['background'] == "bg-9.jpeg") {
                            echo 'checked';
                          } ?>>
                          <label for="select_9" class="cursor-pointer">
                            <img src="<?= base_style() ?>/images/background/bg-9.jpeg"
                              class="img-fluid img-avatar-form">
                          </label>
                        </div>
                      </span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                      <span class="bmd-form-group is-filled">
                        <div class="radio radio-css radio-inline">
                          <input id="select_10" type="radio" name="background" value="bg-10.jpeg" <?php if ($_SESSION['businessData']['background'] == "bg-10.jpeg") {
                            echo 'checked';
                          } ?>>
                          <label for="select_10" class="cursor-pointer">
                            <img src="<?= base_style() ?>/images/background/bg-10.jpeg"
                              class="img-fluid img-avatar-form">
                          </label>
                        </div>
                      </span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                      <span class="bmd-form-group is-filled">
                        <div class="radio radio-css radio-inline">
                          <input id="select_11" type="radio" name="background" value="bg-11.jpeg" <?php if ($_SESSION['businessData']['background'] == "bg-11.jpeg") {
                            echo 'checked';
                          } ?>>
                          <label for="select_11" class="cursor-pointer">
                            <img src="<?= base_style() ?>/images/background/bg-11.jpeg"
                              class="img-fluid img-avatar-form">
                          </label>
                        </div>
                      </span>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                      <span class="bmd-form-group is-filled">
                        <div class="radio radio-css radio-inline">
                          <input id="select_12" type="radio" name="background" value="bg-12.jpeg" <?php if ($_SESSION['businessData']['background'] == "bg-12.jpeg") {
                            echo 'checked';
                          } ?>>
                          <label for="select_12" class="cursor-pointer">
                            <img src="<?= base_style() ?>/images/background/bg-12.jpeg"
                              class="img-fluid img-avatar-form">
                          </label>
                        </div>
                      </span>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12 text-center mt-2">
                      <button type="submit" class="btn btn-blue">
                        <i class="fas fa-save mr-2"></i>Guardar Cambios
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="invoice-tab">
          <div id="accordionTwo" class="accordion">
            <div class="card">
              <div class="card-header pointer-cursor d-flex align-items-center collapsed" data-toggle="collapse"
                data-target="#collapseSix">
                <i class="fa fa-cogs fa-fw mr-2"></i>Configuración de facturación
              </div>
              <div id="collapseSix" class="collapse show" data-parent="#accordionTwo">
                <div class="card-body">
                  <form autocomplete="off" name="transactions_invoice" id="transactions_invoice"
                    class="row row-space-30">
                    <div class="col-xl-12">
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Moneda</label>
                        <div class="col-md-8">
                          <select class="form-control" id="listCurrency" name="listCurrency">
                            <?php
                            foreach ($currencys as $currency) {
                              ?>
                              <option value="<?= $currency['id'] ?>"
                                <?= (($currency['id'] == $_SESSION['businessData']['currencyid']) ? "selected" : "") ?>>
                                <?= $currency['currency_iso'] . ' - ' . $currency['currency_name'] . ' - ' . $currency['symbol'] ?>
                              </option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Formato de impresión</label>
                        <div class="col-md-8">
                          <select class="form-control" id="listPrinters" name="listPrinters">
                            <option value="ticket" <?= (($_SESSION['businessData']['print_format'] == "ticket") ? "selected" : "") ?>>Ticket</option>
                            <option value="a4" <?= (($_SESSION['businessData']['print_format'] == "a4") ? "selected" : "") ?>>A4</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Texto del pie de pagina</label>
                        <div class="col-md-8">
                          <textarea class="form-control" id="footer_text" name="footer_text">
                                                <?= $_SESSION['businessData']['footer_text'] ?>
                                              </textarea>
                        </div>
                      </div>
                      <div class="form-group row justify-content-center">
                        <button type="submit" class="btn btn-blue">
                          <i class="fas fa-save mr-2"></i>Guardar Cambios
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-header pointer-cursor d-flex align-items-center collapsed" data-toggle="collapse"
                data-target="#collapseSeven">
                <i class="fa fa-file-image fa-fw mr-2"></i>Logo factura
              </div>
              <div id="collapseSeven" class="collapse" data-parent="#accordionTwo">
                <div class="card-body">
                  <form name="transactions_logofac" id="transactions_logofac" class="row">
                    <?php
                    if (!empty($_SESSION['businessData']['logotyope'])) {
                      if ($_SESSION['businessData']['logotyope'] == "superwisp.png") {
                        $logofac = base_style() . '/images/logotypes/' . $_SESSION['businessData']['logotyope'];
                      } else {
                        $logofac_url = base_style() . '/uploads/business/' . $_SESSION['businessData']['logotyope'];
                        if (@getimagesize($logofac_url)) {
                          $logofac = base_style() . '/uploads/business/' . $_SESSION['businessData']['logotyope'];
                        } else {
                          $logofac = base_style() . '/images/logotypes/superwisp.png';
                        }
                      }
                    } else {
                      $logofac = base_style() . '/images/logotypes/superwisp.png';
                    }
                    ?>
                    <input type="hidden" id="logfac-actual" name="logfac-actual"
                      value="<?= $_SESSION['businessData']['logotyope'] ?>">
                    <div class="col-md-12 col-sm-12 col-12 text-center">
                      <div class="image">
                        <div class="cont-image">
                          <label for="logo-fac"></label>
                          <div class="prev-image">
                            <img class="img-responsive" id="image-logofac" src="<?= $logofac ?>">
                          </div>
                        </div>
                        <div class="upload-image">
                          <input type="file" name="logo-fac" id="logo-fac">
                        </div>
                      </div>
                      <small class="text-success text-uppercase m-b-10">Max. 210 KB</small>
                    </div>
                    <div class="col-md-12 col-sm-12 col-12 text-center mt-2">
                      <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-2"></i>Subir
                        logo</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="api-tab">
          <div id="accordionTree" class="accordion">
            <div class="card">
              <div class="card-header pointer-cursor d-flex align-items-center" data-toggle="collapse"
                data-target="#collapseEigth" aria-expanded="false">
                <img src="<?= base_style() ?>/images/default/googlemaps.png" class="image-apis mr-1">Google Maps
              </div>
              <div id="collapseEigth" class="collapse" data-parent="#accordionTree" style="">
                <div class="card-body">
                  <form autocomplete="off" name="transactions_google" id="transactions_google" class="row row-space-30">
                    <div class="col-xl-12">
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Clave API google maps</label>
                        <div class="col-md-8">
                          <input type="text" class="form-control"
                            value="<?= $_SESSION['businessData']['google_apikey'] ?>" name="google_apikey"
                            id="google_apikey">
                          <small>Para obtener su Clave API Google visite: <a
                              href="https://console.cloud.google.com/freetrial?hl=es&amp;page=0" target="_blank">Obtén
                              una clave para API para JavaScript</a> </small>
                        </div>
                      </div>
                      <div class="form-group row justify-content-center">
                        <button type="submit" class="btn btn-blue">
                          <i class="fas fa-save mr-2"></i>Guardar Cambios
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-header pointer-cursor d-flex align-items-center" data-toggle="collapse"
                data-target="#collapseNine" aria-expanded="false">
                <img src="<?= base_style() ?>/images/default/reniec.png" class="image-apis mr-1">API Reniec
              </div>
              <div id="collapseNine" class="collapse" data-parent="#accordionTree" style="">
                <div class="card-body">
                  <form autocomplete="off" name="transactions_reniec" id="transactions_reniec" class="row row-space-30">
                    <div class="col-xl-12">
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">TOKEN API</label>
                        <div class="col-md-8">
                          <input type="text" class="form-control"
                            value="<?= $_SESSION['businessData']['reniec_apikey'] ?>" name="reniec_apikey"
                            id="reniec_apikey">
                          <small>Para obtener su Token visite: <a href="https://factiliza.com" target="_blank">Obtén tu
                              token</a></small>
                        </div>
                      </div>
                      <div class="form-group row justify-content-center">
                        <button type="submit" class="btn btn-blue">
                          <i class="fas fa-save mr-2"></i>Guardar Cambios
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-header pointer-cursor d-flex align-items-center" data-toggle="collapse"
                data-target="#collapseTwelve" aria-expanded="false">
                <img src="<?= base_style() ?>/images/default/phpmailer.jpg" class="image-apis mr-1">Configuracion de
                correo enviador
              </div>
              <div id="collapseTwelve" class="collapse" data-parent="#accordionTree" style="">
                <div class="card-body">
                  <form autocomplete="off" name="transactions_email" id="transactions_email" class="row row-space-30">
                    <div class="col-xl-12">
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Correo</label>
                        <div class="col-md-8">
                          <input type="text" class="form-control" onkeypress="return mail(event)"
                            value="<?= $_SESSION['businessData']['email'] ?>" name="email" id="email">
                        </div>
                      </div>
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Contraseña</label>
                        <div class="col-md-8">
                          <input type="password" class="form-control"
                            value="<?= $_SESSION['businessData']['password'] ?>" name="password" id="password">
                        </div>
                      </div>
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Host/Servidor</label>
                        <div class="col-md-8">
                          <input type="text" class="form-control"
                            value="<?= $_SESSION['businessData']['server_host'] ?>" name="server_host" id="server_host">
                        </div>
                      </div>
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Puerto</label>
                        <div class="col-md-8">
                          <select class="form-control" name="port" id="port">
                            <option value="465" <?php if ($_SESSION['businessData']['port'] == 465) {
                              echo 'checked';
                            } ?>>465</option>
                            <option value="587" <?php if ($_SESSION['businessData']['port'] == 587) {
                              echo 'checked';
                            } ?>>587</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-md-3 text-lg-right col-form-label">Logo correo</label>
                        <div class="col-md-8">
                          <input type="text" class="form-control" value="<?= $_SESSION['businessData']['logo_email'] ?>"
                            name="logo_email" id="logo_email">
                        </div>
                      </div>
                      <div class="form-group row justify-content-center">
                        <button type="submit" class="btn btn-blue">
                          <i class="fas fa-save mr-2"></i>Guardar Cambios
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-header pointer-cursor d-flex align-items-center" data-toggle="collapse"
                data-target="#collapseThirteen" aria-expanded="false">
                <img src="<?= base_style() ?>/images/default/whatsapp.png" class="image-apis mr-1">Whatsapp
              </div>
              <div id="collapseThirteen" class="collapse" data-parent="#accordionTree" style="">
                <div class="card-body">
                  <form autocomplete="off" name="transactions_whatsapp" id="transactions_whatsapp"
                    class="row row-space-30">
                    <div class="col-xl-12">
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">Whatsapp API</label>
                        <div class="col-md-8">
                          <input type="text" class="form-control"
                            value="<?= $_SESSION['businessData']['whatsapp_api'] ?>" name="whatsapp_api"
                            id="whatsapp_api">
                        </div>
                      </div>
                    </div>
                    <div class="col-xl-12">
                      <div class="form-group row m-b-10">
                        <label class="col-md-3 text-lg-right col-form-label">TOKEN</label>
                        <div class="col-md-8">
                          <input type="text" class="form-control"
                            value="<?= $_SESSION['businessData']['whatsapp_key'] ?>" name="whatsapp_key"
                            id="whatsapp_key">
                          <small>Para obtener su Token visite: <a href="https://app.then.net.pe/login"
                              target="_blank">Obtén tu token</a></small>-
                          <small>Para adquirir accesso contacte: <a
                              href="https://api.whatsapp.com/send?phone=+51999220735&text=Hola%20deseo%20obtener%20un%20*TOKEN*%20de%20API%20WhatsApp,%20para%20usar%20en%20la%20plataforma%20*SISTEMA%20WISP*"
                              target="_blank">WhatsApp</a></small>
                        </div>
                      </div>
                      <div class="form-group row justify-content-center">
                        <button type="submit" class="btn btn-blue">
                          <i class="fas fa-save mr-2"></i>Guardar Cambios
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>