<div id="modal-ticket" class="modal fade p-0" role="dialog" style="display: none;">
  <form autocomplete="off" name="transactions_ticket" id="transactions_ticket">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-uppercase" id="text-ticket"></h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" id="idticket" name="idticket" value="">
            <input type="hidden" id="idticketclient" name="idclient">
            <div class="col-md-8 form-group">
              <label class="control-label">Cliente</label>
              <input type="text" class="form-control" id="client_ticket" readonly>
            </div>
            <div class="col-md-4 form-group">
              <label class="control-label">Fecha de atención <span class="text-danger">*</span></label>
              <div class="input-group input-daterange">
                <input type="text" class="form-control" name="attention_date" id="attention_date"
                  data-parsley-required="true">
                <div class="input-group-append">
                  <span class="input-group-text"><i class="far fa-calendar"></i></span>
                </div>
              </div>
            </div>
            <div class="col-md-6 form-group">
              <label class="control-label">Asunto</label>
              <select class="form-control" name="listAffairs" id="listAffairs" style="width:100%;"></select>
            </div>
            <div class="col-md-2 form-group">
              <label class="control-label">Prioridad</label>
              <select class="form-control" name="listPriority" id="listPriority" style="width:100%;">
                <option value="1">BAJA</option>
                <option value="2">MEDIA</option>
                <option value="3">ALTA</option>
                <option value="4">URGENTE</option>
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label class="control-label">Asginado</label>
              <select class="form-control" name="listTechnical" id="listTechnical" style="width:100%;"></select>
            </div>
            <div class="col-md-12 form-group">
              <label class="control-label">Descripción</label>
              <textarea class="form-control text-uppercase" name="description" id="description"
                placeholder="INGRESE DESCRIPCIÓN" rows="6"
                style="min-height: 50px; overflow: hidden; overflow-wrap: break-word; height: 80px;"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-white" data-dismiss="modal"></i>Cerrar</button>
          <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i><span
              id="text-button-ticket"></span></button>
        </div>
      </div>
    </div>
  </form>
</div>
<div id="modal-internet" class="modal fade p-0" role="dialog" style="display: none;">
  <form autocomplete="off" name="transactions_internet" id="transactions_internet">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-uppercase" id="text-internet"></h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" id="idinternet" name="idservice" value="">
            <input type="hidden" id="idconin" name="idcontract" value="">
            <div class="col-md-9 form-group">
              <label for="listInternet" class="control-label">Plan de Internet</label>
              <select class="form-control" name="listService" id="listInternet" style="width:100%;"></select>
            </div>
            <div class="col-md-3 form-group">
              <label for="price_internet" class="control-label">Precio de Plan</label>
              <input type="text" class="form-control" name="price" id="price_internet"
                onkeypress="return numbers(event)" placeholder="0.00" data-parsley-required="true" readonly>
            </div>
            <div class="col-md-6 form-group hide-descent">
              <label for="descent" class="control-label">Velocida de bajada</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text form-icon"><i class="fa fa-cloud-download-alt"></i></span>
                </div>
                <input type="text" class="form-control" name="descent" id="descent" onkeypress="return numbers(event)"
                  placeholder="0" readonly>
              </div>
            </div>
            <div class="col-md-6 form-group hide-rise">
              <label for="rise" class="control-label">Velocida de subida</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text form-icon"><i class="fa fa-cloud-upload-alt"></i></span>
                </div>
                <input type="text" class="form-control" name="rise" id="rise" onkeypress="return numbers(event)"
                  placeholder="0" readonly>
              </div>
            </div>
            <div class="col-md-12 form-group">
              <label for="details_internet" class="control-label">Detalles</label>
              <input type="text" class="form-control" id="details_internet" name="details"
                onkeypress="return numbersandletters(event)" placeholder="INTERNET BANDA ANCHA 4Mbps/2Mbps" readonly>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-white" data-dismiss="modal"></i>Cerrar</button>
          <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i><span
              id="text-button-internet"></span></button>
        </div>
      </div>
    </div>
  </form>
</div>
<div id="modal-personalized" class="modal fade p-0" role="dialog" style="display: none;">
  <form autocomplete="off" name="transactions_personalized" id="transactions_personalized">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-uppercase" id="text-personalized"></h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" id="idpersonalized" name="idservice" value="">
            <input type="hidden" id="idconper" name="idcontract" value="">
            <div class="col-md-9 form-group">
              <label for="listPersonalized" class="control-label">Plan</label>
              <select class="form-control" name="listService" id="listPersonalized" style="width:100%;"></select>
            </div>
            <div class="col-md-3 form-group">
              <label for="price_personalized" class="control-label">Precio de Plan</label>
              <input type="text" class="form-control" name="price" id="price_personalized"
                onkeypress="return numbers(event)" placeholder="0.00" data-parsley-required="true" readonly>
            </div>
            <div class="col-md-12 form-group">
              <label for="details_personalized" class="control-label">Detalles</label>
              <input type="text" class="form-control" id="details_personalized" name="details"
                onkeypress="return numbersandletters(event)" placeholder="INTERNET BANDA ANCHA 4Mbps/2Mbps" readonly>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-white" data-dismiss="modal"></i>Cerrar</button>
          <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i><span
              id="text-button-personalized"></span></button>
        </div>
      </div>
    </div>
  </form>
</div>
<div id="modal-free" class="modal fade p-0" role="dialog" style="display: none;">
  <form autocomplete="off" name="transactions_free" id="transactions_free">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-uppercase" id="text-free"></h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body row">
          <input type="hidden" id="idfree" name="idbill" value="">
          <input type="hidden" id="idclifree" name="idclient" value="">
          <input type="hidden" name="typebill" value="1">
          <div class="col-md-6 form-group">
            <label class="control-label">Cliente</label>
            <input type="text" class="form-control" id="client_free" readonly>
          </div>
          <div class="col-md-3 form-group">
            <label class="control-label">Fecha de emisión</label>
            <div class="input-group">
              <input type="text" class="form-control" name="issue" id="freeissue">
              <div class="input-group-append">
                <span class="input-group-text"><i class="far fa-calendar"></i></span>
              </div>
            </div>
          </div>
          <div class="col-md-3 form-group">
            <label class="control-label">Fecha de vencimiento</label>
            <div class="input-group">
              <input type="text" class="form-control" name="expiration" id="freeexpiration">
              <div class="input-group-append">
                <span class="input-group-text"><i class="far fa-calendar"></i></span>
              </div>
            </div>
          </div>
          <div class="col-md-2 form-group">
            <label class="control-label">Metodo de pago</label>
            <select class="form-control" name="listMethod" id="listMethod" style="width:100%;">
              <option value="1">CONTADO</option>
              <option value="2">CREDITO</option>
            </select>
          </div>
          <div class="col-md-2 form-group">
            <label class="control-label">Comprobante</label>
            <select class="form-control" name="listVouchers" id="vouchersfree" style="width:100%;"></select>
          </div>
          <div class="col-md-2 form-group">
            <label class="control-label">Serie</label>
            <select class="form-control" name="listSerie" id="seriefree" style="width:100%;"></select>
          </div>
          <div class="col-md-6 form-group">
            <label class="control-label">Observación</label>
            <input type="text" class="form-control text-uppercase" name="observation" id="obfree"
              placeholder="INGRESE OBSERVACIÓN">
          </div>
          <div class="col-md-12 form-group mb-3">
            <label class="control-label">Buscar producto</label>
            <div class="search-input">
              <input type="text" id="search_products" name="search_products" placeholder="DESCRIBA EL PRODUCTO.">
              <div id="box-search" class="autocom-box"></div>
              <div class="icon"><i class="fas fa-search"></i></div>
            </div>
          </div>
          <div class="invoice">
            <div class="invoice-content">
              <div class="table-responsive">
                <table class="table table-bordered table-invoice" id="table-free">
                  <thead>
                    <tr>
                      <th style="border-color:#2d353c" class="text-uppercase f-s-12" width="80px">Codigo</th>
                      <th style="border-color:#2d353c" class="text-uppercase f-s-12 text-left" width="60%">Descripción
                      </th>
                      <th style="border-color:#2d353c" class="text-uppercase f-s-12" width="80px">Precio</th>
                      <th style="border-color:#2d353c" class="text-uppercase f-s-12" width="80px">Cantidad</th>
                      <th style="border-color:#2d353c" class="text-uppercase f-s-12" width="80px">Total</th>
                      <th style="border-color:#2d353c" class="text-uppercase f-s-12" width="40px"></th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                  <tfoot>
                    <tr>
                      <td colspan="4" class="text-uppercase f-s-12 text-right"><strong>Descuento</strong></td>
                      <td colspan="2"><input type="text" class="form-control" value="0.00" id="discountfree"
                          name="discount"></td>
                    </tr>
                  </tfoot>
                </table>
                <div class="pull-left" style="margin-bottom:10px;">
                  <button type="button" onclick="addItem('libre')" class="btn btn-default"><i class="fas fa-plus"></i>
                    Agregar Línea</button>
                </div>
              </div>
              <div class="invoice-price">
                <div class="invoice-price-left">
                  <div class="invoice-price-row">
                    <div class="sub-price">
                      <small>SUBTOTAL</small>
                      <span id="text-sub-f"><?= $_SESSION['businessData']['symbol'] ?>0.00</span>
                      <input type="hidden" id="subtotalfree" name="subtotal">
                    </div>
                    <div class="sub-price"><i class="fas fa-minus"></i></div>
                    <div class="sub-price">
                      <small>DESCUENTO</small>
                      <span id="text-dis-f"><?= $_SESSION['businessData']['symbol'] ?>0.00</span>
                    </div>
                  </div>
                </div>
                <div class="invoice-price-right">
                  <small>TOTAL</small>
                  <span id="text-total-f"><?= $_SESSION['businessData']['symbol'] ?>0.00</span>
                  <input type="hidden" id="totalfree" name="total">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-white" data-dismiss="modal"></i>Cerrar</button>
          <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i><span
              id="text-button-free"></span></button>
        </div>
      </div>
    </div>
  </form>
</div>
<div id="modal-facser" class="modal fade p-0" role="dialog" style="display: none;">
  <form autocomplete="off" name="transactions_facser" id="transactions_facser">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-uppercase" id="text-facser"></h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body row">
          <input type="hidden" id="idfacser" name="idbill" value="">
          <input type="hidden" id="idcliser" name="idclient" value="">
          <input type="hidden" id="billed_month" name="billed_month" value="">
          <input type="hidden" name="typebill" value="2">
          <input type="hidden" name="listMethod" id="listMethod" value="2">
          <div class="col-md-6 form-group">
            <label class="control-label">Cliente</label>
            <input type="text" class="form-control" id="client_serv" readonly>
          </div>
          <div id="cont-iss-serv" class="col-md-3 form-group">
            <label class="control-label">Emisión</label>
            <div class="input-group">
              <input type="text" class="form-control" name="issue" id="servissue">
              <div class="input-group-append">
                <span class="input-group-text"><i class="far fa-calendar"></i></span>
              </div>
            </div>
          </div>
          <div id="cont-exp-serv" class="col-md-3 form-group">
            <label class="control-label">Vencimiento</label>
            <div class="input-group">
              <input type="text" class="form-control" name="expiration" id="servexpiration">
              <div class="input-group-append">
                <span class="input-group-text"><i class="far fa-calendar"></i></span>
              </div>
            </div>
          </div>
          <div id="cont-state-serv" class="col-md-2 form-group" style="display:none;">
            <label class="control-label">Estado</label>
            <select class="form-control" name="listStatus" id="statusserv" style="width:100%;">
              <option value="1">PAGADO</option>
              <option value="2">PENDIENTE</option>
              <option value="3">VENCIDO</option>
              <option value="4">ANULADO</option>
            </select>
          </div>
          <div class="col-md-2 form-group">
            <label class="control-label">Comprobante</label>
            <select class="form-control" name="listVouchers" id="vouchersserv" style="width:100%;"></select>
          </div>
          <div class="col-md-2 form-group">
            <label class="control-label">Serie</label>
            <select class="form-control" name="listSerie" id="serieserv" style="width:100%;"></select>
          </div>
          <div class="col-md-8 form-group mb-3">
            <label class="control-label">Observación</label>
            <input type="text" class="form-control text-uppercase" name="observation" id="observ"
              placeholder="INGRESE OBSERVACIÓN">
          </div>
          <div class="invoice">
            <div class="invoice-content">
              <div class="table-responsive">
                <table id="table-plans" class="table table-bordered table-invoice">
                  <thead>
                    <tr>
                      <th style="border-color:#2d353c" class="table-header text-uppercase f-s-12 text-left" width="60%">
                        Descripción</th>
                      <th style="border-color:#2d353c" class="table-header text-uppercase f-s-12" width="80px">Precio
                      </th>
                      <th style="border-color:#2d353c" class="table-header text-uppercase f-s-12" width="80px">Cantidad
                      </th>
                      <th style="border-color:#2d353c" class="table-header text-uppercase f-s-12" width="60px">Total
                      </th>
                      <th style="border-color:#2d353c" class="text-uppercase f-s-12" width="40px"></th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                  <tfoot>
                    <tr>
                      <td colspan="3" class="text-uppercase f-s-12 text-right"><strong>Descuento</strong></td>
                      <td colspan="2"><input type="text" class="form-control" id="discountserv" name="discount"
                          value="0.00"></td>
                    </tr>
                  </tfoot>
                </table>
                <div class="pull-left" style="margin-bottom:10px;">
                  <button type="button" onclick="addItem('service')" class="btn btn-default"><i class="fas fa-plus"></i>
                    Agregar Línea</button>
                </div>
              </div>
              <div class="invoice-price">
                <div class="invoice-price-left">
                  <div class="invoice-price-row">
                    <div class="sub-price">
                      <small>SUBTOTAL</small>
                      <span id="text-sub"></span>
                      <input type="hidden" id="subtotalserv" name="subtotal">
                    </div>
                    <div class="sub-price">
                      <i class="fas fa-minus"></i>
                    </div>
                    <div class="sub-price">
                      <small>DESCUENTO</small>
                      <span id="text-dis"></span>
                    </div>
                  </div>
                </div>
                <div class="invoice-price-right">
                  <small>TOTAL</small>
                  <span id="text-total"></span>
                  <input type="hidden" id="totalserv" name="total">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-white" data-dismiss="modal"></i>Cerrar</button>
          <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i><span
              id="text-button-facser"></span></button>
        </div>
      </div>
    </div>
  </form>
</div>
<div id="modal-payment" class="modal fade p-0" role="dialog" style="display: none;">
  <form autocomplete="off" name="transactions_payments" id="transactions_payments">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-uppercase" id="text-payment"></h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="idpayment" name="idpayment" value="">
          <input type="hidden" id="idbillpayment" name="idbill" value="">
          <input type="hidden" id="idbillclient" name="idclient" value="">
          <div class="row">
            <div class="col-md-12 form-group mb-0">
              <label for="listTypePay" class="control-label mb-0">Cliente</label>
              <p class="text-name-client">
                <strong id="client_name"></strong>
              </p>
            </div>
            <div class="col-md-12 form-group">
              <label for="listTypePay" class="control-label">Forma de pago</label>
              <select class="form-control" name="listTypePay" id="listTypePay" style="width:100%;"></select>
            </div>
            <div class="col-md-12 form-group">
              <label for="ticket_number" class="control-label">Número de boleta</label>
              <input type="text" class="form-control" name="ticket_number" id="ticket_number" style="width:100%;">
            </div>
            <div class="col-md-12 form-group">
              <label for="reference_number" class="control-label">Número de referencia</label>
              <input type="text" class="form-control" name="reference_number" id="reference_number" style="width:100%;">
            </div>
            <div class="col-md-12 form-group">
              <label for="total_payment" class="control-label">Fecha</label>
              <div class="input-group">
                <input type="text" class="form-control" name="date_time" id="date_time">
                <div class="input-group-append">
                  <span class="input-group-text"><i class="far fa-calendar"></i></span>
                </div>
              </div>
            </div>
            <div class="col-md-12 form-group">
              <label for="total_payment" class="control-label">Total</label>
              <input type="text" class="form-control" name="total_payment" id="total_payment"
                onkeypress="return decimal(event)" placeholder="0.00" data-parsley-required="true">
              <div id="text-alert"></div>
            </div>
            <div class="col-md-12 form-group">
              <label for="comment" class="control-label">Comentario</label>
              <textarea class="form-control text-uppercase" name="comment" id="comment" placeholder="INGRESE COMENTARIO"
                rows="6"
                style="min-height: 20px; overflow: hidden; overflow-wrap: break-word; height: 80px;"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"></i>Cerrar</button>
          <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i><span
              id="text-button-payment"></span></button>
        </div>
      </div>
    </div>
  </form>
</div>
<div id="modal-view" class="modal fade p-0" role="dialog" style="display: none;">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-uppercase" id="text-view"></h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body row">
        <div class="col-sm-6">
          <strong class="f-s-15" id="view-client"></strong><br>
          <b id="view-typedoc"></b> <span id="view-doc"></span><br>
          <span id="view-address"></span><br>
          <b>Cel.</b> <span id="view-mobile"></span>
        </div>
        <div class="col-sm-6 text-right">
          <small id="view-issue"></small> <br>
          <small id="view-expiration"></small><br>
          <small>condición: <b id="view-method"></b></small><br>
          <b id="view-state"></b>
        </div>
        <div class="col-sm-12 mt-3">
          <b>OBSERVACIÓN:</b><br><span id="view-observation"></span>
        </div>
        <div class="invoice mt-3">
          <div class="invoice-content">
            <div class="table-responsive">
              <table class="table table-bordered table-invoice" id="view-table">
                <thead>
                  <tr>
                    <th style="border-color:#2d353c" class="text-left text-uppercase f-s-12" width="60%">Descripción
                    </th>
                    <th style="border-color:#2d353c" class="text-uppercase f-s-12" width="80px">Precio</th>
                    <th style="border-color:#2d353c" class="text-uppercase f-s-12" width="80px">Cantidad</th>
                    <th style="border-color:#2d353c" class="text-uppercase f-s-12" width="80px">Total</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <h6 class="mb-0">PAGOS REALIZADOS</h6>
            <div class="table-responsive">
              <table class="table table-bordered table-invoice" id="view-table-payments">
                <thead>
                  <tr>
                    <th style="border-color:#2d353c" class="text-uppercase f-s-12">Codigo</th>
                    <th style="border-color:#2d353c" class="text-uppercase f-s-12">Fecha</th>
                    <th style="border-color:#2d353c" class="text-uppercase f-s-12">Monto</th>
                    <th style="border-color:#2d353c" class="text-left text-uppercase f-s-12">Forma pago</th>
                    <th style="border-color:#2d353c" class="text-left text-uppercase f-s-12">Usuario</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <div class="invoice-price">
              <div class="invoice-price-left">
                <div class="invoice-price-row">
                  <div class="sub-price">
                    <small>SUBTOTAL</small>
                    <span id="view-sub"></span>
                  </div>
                  <div class="sub-price"><i class="fas fa-minus"></i></div>
                  <div class="sub-price">
                    <small>DESCUENTO</small>
                    <span id="view-dis"></span>
                  </div>
                </div>
              </div>
              <div class="invoice-price-right">
                <small>TOTAL</small>
                <span id="view-total"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php modal("mapModal"); ?>
<div id="modal-import" class="modal fade p-0" role="dialog" style="display: none;">
  <form autocomplete="off" name="transactions_import" id="transactions_import">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-uppercase" id="text-title-import"></h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 form-group">
              <p class="m-0">Antes de iniciar la importación generar una copia de seguridad en caso revertir algún
                cambio no deseado, en caso de incluir clientes repetidos estos seran excluidos de la importación.
                <a href="<?= base_style() ?>/resources/clients.xlsx">Descargar Plantilla</a>
              </p>
            </div>
            <div class="col-md-12 form-group">
              <div class="input-group">
                <input type="text" class="form-control" name="text-file" id="text-file" readonly>
                <div class="input-group-append">
                  <label class="btn btn-default cursor-pointer" for="import_clients">
                    <input type="file" id="import_clients" name="import_clients" accept=".xls, .xlsx"
                      style="display:none">
                    <i class="fas fa-folder-open"></i>
                  </label>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <p class="m-0"><strong>Nota:</strong> Tener en cuenta que el servicio y el técnico deben estar registrado
                previamente.</p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"></i>Cerrar</button>
          <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i><span
              id="text-button-import"></span></button>
        </div>
      </div>
    </div>
  </form>
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
            <button type="button" class="btn btn-lg btn-dark" id="btn-a4"><i class="fa fa-file-alt fa-5x"></i></button>
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
              <input type="text" id="bill_number_client" class="form-control" onkeypress="return numbers(event)"
                placeholder="999999999" maxlength="25">
              <div class="input-group-append">
                <button type="button" class="btn btn-whatsapp" id="btn-msg"><i
                    class="fab fa-whatsapp fa-lg mr-1"></i>Enviar</button>
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
<div id="modal-tools" class="modal fade p-0" role="dialog" style="display: none;">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-uppercase" id="text-title-tools"></h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="tools_country">
        <input type="hidden" id="tools_number">
        <div class="row">
          <div class="col text-center font-weight-bold mt-3">
            <button type="button" class="btn btn-lg btn-primary" id="btn-sms"><i
                class="fas fa-comment-alt fa-5x"></i></button>
            <p>SMS</p>
          </div>
          <div class="col text-center font-weight-bold mt-3">
            <button type="button" class="btn btn-lg btn-info" id="btn-tocall"><i
                class="fa fa-phone-alt fa-5x"></i></button>
            <p>Llamar</p>
          </div>
          <div class="col text-center font-weight-bold mt-3">
            <button type="button" class="btn btn-lg btn-whatsapp" id="btn-whatsapp"><i
                class="fab fa-whatsapp fa-5x"></i></button>
            <p>WhatsApp</p>
          </div>
          <div class="col text-center font-weight-bold mt-3">
            <button type="button" class="btn btn-lg btn-whatsapp" id="btn-whatsapp-massive"><i
                class="fab fa-whatsapp fa-5x"></i></button>
            <input type="hidden" id="message-whatsapp-massive">
            <p>WSP Deuda Total</p>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"></i>Cerrar</button>
      </div>
    </div>
  </div>
</div>
<div id="modal-message" class="modal fade p-0" role="dialog" style="display: none;">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-uppercase" id="text-title-message"></h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="idpdfticket">
        <input type="hidden" id="message_country_code">
        <input type="hidden" id="message_msg">
        <div class="row">
          <div class="col text-center font-weight-bold mt-3">
            <button type="button" class="btn btn-lg btn-dark" id="btn-ticket-message"><i
                class="fa fa-receipt fa-5x"></i></button>
            <p>TICKET SOPORTE</p>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-12">
            <div class="input-group m-b-10">
              <div class="input-group-prepend">
                <span class="input-group-text" id="message_text_country"></span>
              </div>
              <input type="text" id="message_number_client" class="form-control" onkeypress="return numbers(event)"
                placeholder="999999999" maxlength="25">
              <div class="input-group-append">
                <button type="button" class="btn btn-whatsapp" id="btn-msg-message"><i
                    class="fab fa-whatsapp fa-lg mr-1"></i>Enviar</button>
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
<div id="modal-finalize" class="modal fade p-0" role="dialog" style="display: none;">
  <form autocomplete="off" name="transactions_finalize" id="transactions_finalize">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-uppercase" id="text-finalize"></h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" id="idticketfinalize" name="idticket" value="">
            <div class="col-md-12 form-group">
              <label class="control-label mb-0 f-w-600"><i class="fa fa-angle-double-right mr-2"></i>TERMINAR EL PROCESO
                EN:</label>
              <br>
              <div class="radio radio-css radio-inline mr-4">
                <input type="radio" name="radio_option" id="radio_yes" value="1" checked>
                <label for="radio_yes" class="cursor-pointer f-s-14">RESUELTO</label>
              </div>
              <div class="radio radio-css radio-inline">
                <input type="radio" name="radio_option" id="radio_not" value="2">
                <label for="radio_not" class="cursor-pointer f-s-14">NO RESUELTO</label>
              </div>
            </div>
            <div class="col-md-12 form-group">
              <label for="observation" class="control-label f-w-600" id="text-observation">DESCRIBE EL PROCESO
                REALIZADO:</label>
              <textarea class="form-control text-uppercase" name="observation" id="obserfinalize" rows="6"
                style="min-height: 50px; overflow: hidden; overflow-wrap: break-word; height: 80px;"
                data-parsley-required="true" placeholder="Ingrese su descripcion"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-white" data-dismiss="modal"></i>Cerrar</button>
          <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i><span
              id="text-button-finalize"></span></button>
        </div>
      </div>
    </div>
  </form>
</div>
<div id="modal-view-ticket" class="modal fade p-0" role="dialog" style="display: none;">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-uppercase" id="text-view-ticket"></h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <ul class="media-list underline width-full">
          <li class="media media-sm clearfix">
            <a href="javascript:;" class="pull-left">
              <img id="view-image-ticket" class="media-object rounded-corner">
            </a>
            <div class="media-body">
              <div id="view-client-ticket" class="email-from text-inverse f-s-14 f-w-600 m-b-1"></div>
              <div id="view-celdoc-ticket" class="m-b-1"></div>
              <div id="view-address-ticket" class="email-to"></div>
              <div id="view-priority-ticket" class="m-b-1"></div>
            </div>
            <div class="widget-list-action text-right">
              <div class="m-b-1"><small id="view-created-ticket"></small></div>
              <div class="m-b-1"><small id="view-visit-ticket"></small></div>
              <div class="m-b-1"><b id="view-state-ticket"></b></div>
            </div>
          </li>
        </ul>
        <div class="row">
          <div class="col-sm-12">
            <div class="panel panel-white post mb-0">
              <div class="post-heading">
                <div class="pull-left image">
                  <img class="img-circle avatar" id="view-user-post">
                </div>
                <div class="pull-left meta">
                  <div class="title h5" id="view-user"></div>
                  <h6 class="time text-orange" id="view-star"></h6>
                </div>
              </div>
              <div class="post-description pt-0">
                <p id="view-incident" class="mb-0 f-w-700"></p>
                <p id="view-description" class="mb-0"></p>
              </div>
              <div class="post-footer pt-0 pb-0">
                <ul class="comments-list" id="post-comment"></ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>