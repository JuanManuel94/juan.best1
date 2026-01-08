<div id="modal-view" class="modal fade p-0" role="dialog" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-uppercase" id="text-view"></h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
                                        <th style="border-color:#2d353c" class="text-left text-uppercase f-s-12" width="60%">Descripción</th>
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
                                      <div class="sub-price">
                                          <i class="fas fa-minus"></i>
                                      </div>
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
<div id="modal-payment" class="modal fade p-0" role="dialog" style="display: none;">
    <form autocomplete="off" name="transactions_payments" id="transactions_payments">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-uppercase" id="text-payment"></h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="idbill" name="idbill" value="">
                    <input type="hidden" id="idpayment" name="idpayment" value="">
                    <input type="hidden" id="idclient" name="idclient" value="">
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
                            <?php
                                $datetime = date("d/m/Y G:i");
                            ?>
                            <label for="total_payment" class="control-label">Fecha</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="date_time" id="date_time" value="<?= $datetime ?>">
                                <div class="input-group-append">
                                      <span class="input-group-text">
                                            <i class="far fa-calendar"></i>
                                      </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="operation_number" class="control-label">N° Operacion</label>
                            <input type="text" class="form-control" name="operation_number" id="operation_number" onkeypress="return numbers(event)" placeholder="0000000001" maxlength="10">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="total_payment" class="control-label">Total</label>
                            <input type="text" class="form-control" name="total_payment" id="total_payment" onkeypress="return decimal(event)" placeholder="0.00" data-parsley-required="true">
                            <div id="text-alert"></div>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="comment" class="control-label">Comentario</label>
                            <textarea class="form-control text-uppercase" name="comment" id="comment" rows="6" placeholder="INGRESE COMENTARIO" style="min-height: 20px; overflow: hidden; overflow-wrap: break-word; height: 80px;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"></i>Cerrar</button>
                    <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i><span id="text-button-payment"></span></button>
                </div>
            </div>
        </div>
    </form>
</div>
<div id="modal-massive-voucher" class="modal fade p-0" role="dialog" style="display: none;">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header">
              <h6 class="modal-title text-uppercase" id="text-title-massive-voucher"></h6>
              <button type="button" class="close btn-close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
              <input type="hidden" id="massive_country_code">
              <input type="hidden" id="massive_client">
              <input type="hidden" id="massive_current_paid">
              <div class="row">
                  <div class="col text-center font-weight-bold mt-3">
                     <button type="button" class="btn btn-lg btn-dark" id="btn-massive-ticket">
                       <i class="fa fa-receipt fa-5x"></i>
                     </button>
                      <p>PDF TICKET</p>
                  </div>
                  <div class="col text-center font-weight-bold mt-3">
                    <button type="button" class="btn btn-lg btn-dark" id="btn-massive-print_ticket">
                      <i class="ion ion-ios-print fa-5x"></i>
                    </button>
                     <p>IMPRIMIR</p>
                  </div>
              </div>
              <div class="row mt-3">
                <div class="col-md-12">
                  <div class="input-group m-b-10">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="massive_text_country"></span>
                    </div>
                    <input type="text" id="massive_bill_number_client" class="form-control" onkeypress="return numbers(event)" placeholder="999999999" maxlength="25">
                    <div class="input-group-append">
                      <button type="button" class="btn btn-whatsapp" id="btn-massive-msg">
                          <i class="fab fa-whatsapp fa-lg mr-1"></i>Enviar
                      </button>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-default btn-close" data-dismiss="modal"></i>Cerrar</button>
          </div>
      </div>
  </div>
</div>
