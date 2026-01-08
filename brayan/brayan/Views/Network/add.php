<?php head($data); ?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
    <li class="breadcrumb-item"><a href="<?= base_url() ?>/customers"><?= $data['previous_page'] ?></a></li>
    <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
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
                        Facturación y Servicio
                        <small>Servicio,dia de pago,costo</small>
                    </span>
                </a>
            </li>
            <li>
                <a href="#step-3">
                    <span class="number">3</span>
                    <span class="info">
                        Instalación
                        <small>Fecha,hora,tecnico asignado</small>
                    </span>
                </a>
            </li>
        </ul>
        <div>
            <div id="step-1">
                <fieldset>
                    <div class="row">
                        <div class="col-xl-8 offset-xl-2">
                            <div class="form-group row">
                                <label class="col-md-3 text-lg-right col-form-label">Tipo doc.</label>
                                <div class="col-md-4">
                                  <select class="form-control" name="listTypes" id="listTypes" style="width:100%;">
                                  </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 text-lg-right col-form-label">Número doc. <span
                                        class="text-danger"></span></label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="document" id="document"
                                            onkeypress="return numbers(event)" maxlength="8">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-white btn-search "
                                                onclick="search_document();">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 text-lg-right col-form-label">Nombres <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control text-uppercase" name="names" id="names"
                                        onkeypress="return letters(event)" placeholder="INGRESE NOMBRE"
                                        data-parsley-group="step-1" data-parsley-required="true">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 text-lg-right col-form-label">Apellidos <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control text-uppercase" name="surnames" id="surnames"
                                        onkeypress="return letters(event)" placeholder="INGRESE APELLIDOS"
                                        data-parsley-group="step-1" data-parsley-required="true">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 text-lg-right col-form-label">Celulares <span class="text-danger">*</span></label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control m-b-10" name="mobile" id="mobile" onkeypress="return numbers(event)" placeholder="999999999" maxlength="10" data-parsley-group="step-1" data-parsley-required="true">
                                    <input type="text" class="form-control" name="mobileOp" id="mobileOp" onkeypress="return numbers(event)" placeholder="999999999" maxlength="10">
                                    <small class="text-success text-uppercase m-b-10">Número telefonico opcional</small>
                                </div>
                            </div>
                            <div class="form-group row m-b-10">
                                <label class="col-md-3 text-lg-right col-form-label">Correo</label>
                                <div class="col-md-9">
                                    <input type="email" class="form-control" name="email" id="email" onkeypress="return mail(event)" placeholder="EXAMPLE@EXAMPLE.com">
                                </div>
                            </div>
                            <div class="form-group row m-b-10">
                                <label class="col-md-3 text-lg-right col-form-label">Dirección </label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control text-uppercase" name="address" id="address"
                                        onkeypress="return numbersandletters(event)" placeholder="INGRESE DOMICILIO"
                                        data-parsley-group="step-1" >
                                </div>
                            </div>
                            <div class="form-group row m-b-10">
                                <label class="col-md-3 text-lg-right col-form-label">Referencia</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control text-uppercase" name="reference" id="reference" onkeypress="return numbersandletters(event)" placeholder="REFERENCIA DEL DOMICILIO (OPCIONAL)">
                                </div>
                            </div>
                            <div class="form-group row m-b-10">
                                <label class="col-md-3 text-lg-right col-form-label">Nota</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control text-uppercase" name="note" id="note"  placeholder="NOTA DEL SERVICIO (OPCIONAL)">
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div id="step-2">
                <fieldset>
                    <div class="row">
                        <div class="col-lg-6" data-sortable="false">
                            <div class="panel panel-default" data-sortable="false">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <b><i class="far fa-file mr-1"></i>Facturación</b>
                                    </h4>
                                </div>
                                <div class="panel-body border-panel">
                                    <div class="form-group row m-b-10">
                                        <label class="col-md-5 text-lg-right col-form-label">Estado de servicio</label>
                                        <div class="col-md-6">
                                            <select class="form-control" name="listPlan" id="listPlan">
                                                <option value="1">Facturado</option>
                                                <option value="2">Gratis</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row m-b-10 cont-day">
                                        <label class="col-md-5 text-lg-right col-form-label">Dia de Pago</label>
                                        <div class="col-md-6">
                                            <select class="form-control" name="listPayday" id="listPayday">
                                                <?php
                                                    for($i = 1;$i < 28+1; $i++){
                                                        echo '<option value="'.$i.'" >'.$i.'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row m-b-10 cont-create">
                                        <label class="col-md-5 text-lg-right col-form-label">Crear Factura</label>
                                        <div class="col-md-6">
                                            <select class="form-control" name="listInvoice" id="listInvoice">
                                                <?php
                                                    for($i = 0;$i < 25+1; $i++){
                                                        if($i == 0){
                                                            echo '<option value="0">Desactivado</option>';
                                                        }else if($i == 1){
                                                            echo '<option value="'.$i.'">'.$i.' Día antes</option>';
                                                        }else{
                                                            echo '<option value="'.$i.'">'.$i.' Días antes</option>';
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row m-b-10 cont-gracia">
                                        <label class="col-md-5 text-lg-right col-form-label">Dias de Gracia</label>
                                        <div class="col-md-6">
                                            <select class="form-control" name="listDaysGrace" id="listDaysGrace">
                                                <?php
                                                    for($i = 0;$i < 25+1; $i++){
                                                        if($i == 1){
                                                            echo '<option value="'.$i.'">'.$i.' Día</option>';
                                                        }else{
                                                            if($i == 5){
                                                                echo '<option value="'.$i.'" selected>'.$i.' Días</option>';
                                                            }else{
                                                                echo '<option value="'.$i.'">'.$i.' Días</option>';
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </select>
                                            <small class="text-success text-uppercase">Días tolerancia para aplicar corte</small>
                                        </div>
                                    </div>
                                    <div class="form-group row m-b-10 cont-chk">
                                        <label class="col-md-5 text-lg-right col-form-label"></label>
                                        <div class="col-md-6">
                                          <div class="checkbox checkbox-css pt-0">
                                            <input type="checkbox" id="chkDiscount" name="chkDiscount" value="1" onchange="showDiscount()">
                                            <label for="chkDiscount" class="cursor-pointer m-0">Agregar descuento</label>
                                          </div>
                                          <small class="text-success text-uppercase">Solo aplica a facturas de servicios</small>
                                        </div>
                                    </div>
                                    <div class="form-group row m-b-10 cont-dis">
                                        <label class="col-md-5 text-lg-right col-form-label">Descuento</label>
                                        <div class="col-md-6">
                                            <input type="number" class="form-control" name="discount" id="discount" min="0" step="0.1" onkeypress="return numbers(event)" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="form-group row m-b-10 cont-month">
                                        <label class="col-md-5 text-lg-right col-form-label">Meses de descuento</label>
                                        <div class="col-md-6">
                                            <select class="form-control" name="listMonthDis" id="listMonthDis">
                                                <?php
                                                    for($i = 1;$i < 12+1; $i++){
                                                        if($i == 1){
                                                            echo '<option value="'.$i.'" >'.$i.' Mes</option>';
                                                        }else{
                                                            echo '<option value="'.$i.'" >'.$i.' Meses</option>';
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6" data-sortable="false">
                            <div class="panel panel-default" data-sortable="false">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <b><i class="far fa-calendar-alt mr-1"></i>Planes</b>
                                    </h4>
                                </div>
                                <div class="panel-body border-panel">
                                    <input type="hidden" id="idservice" name="idservice" value="">
                                    <div class="form-row">
                                       <div class="form-group col-md-12">
                                           <div class="search-input">
                                               <input type="text" id="search_service" name="search_service" placeholder="BUSCAR PLANES..">
                                               <div id="box-search" class="autocom-box">
                                               </div>
                                               <div class="icon"><i class="fas fa-search"></i></div>
                                           </div>
                                           <small class="text-success text-uppercase">Busqueda por nombre y descripción</small>
                                       </div>
                                    </div>
                                    <div class="form-row mt-2">
                                       <div class="form-group col-md-12">
                                           <label for="service">Perfil del plan <span class="text-danger">*</span></label>
                                           <input type="text" class="form-control" id="service" name="service" placeholder="INTERNET BANDA ANCHA 4Mbps/2Mbps" readonly  data-parsley-group="step-2" data-parsley-required="true">
                                       </div>
                                       <div class="form-group col-md-10">
                                           <label for="detail-service">Descripción</label>
                                           <input type="text" class="form-control" id="detail-service" name="detail-service" placeholder="INTERNET BANDA ANCHA 4Mbps/2Mbps" readonly>
                                       </div>
                                       <div class="form-group col-md-2">
                                           <label for="price-service">Costo</label>
                                           <input type="text" class="form-control" id="price-service" name="price-service" placeholder="0.00" readonly>
                                       </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div id="step-3">
                <fieldset>
                    <div class="row">
                        <div class="col-xl-8 offset-xl-2">
                            <div class="form-group row m-b-10">
                                <label class="col-md-3 text-lg-right col-form-label">Fecha de atención <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="insDate" id="insDate">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row m-b-10">
                                <label class="col-md-3 text-lg-right col-form-label">Costo de instalación <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="number" class="form-control" name="instPrice" id="instPrice" min="0" step="0.1" onkeypress="return numbers(event)" placeholder="0.00" data-parsley-group="step-3" data-parsley-required="true">
                                </div>
                            </div>
                            <div class="form-group row m-b-10">
                                <label class="col-md-3 text-lg-right col-form-label">Técnico asignado</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="listTechnical" id="listTechnical" style="width: 100%;" data-parsley-group="step-3" data-parsley-required="true">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row m-b-10">
                                <label class="col-md-3 text-lg-right col-form-label">Detalle de instalación</label>
                                <div class="col-md-8">
                                    <textarea class="form-control text-uppercase" name="detail" id="detail" placeholder="INGRESE DETALLE DE INSTALACIÓN" rows="6" style="min-height: 50px; overflow: hidden; overflow-wrap: break-word; height: 80px;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</form>
<!-- FIN TITULO -->
<?php footer($data); ?>
