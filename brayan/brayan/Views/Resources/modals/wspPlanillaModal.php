<div id="modal-action" class="modal fade p-0" role="dialog" style="display: none;">
  <form autocomplete="off" name="transactions" id="transactions">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-uppercase" id="text-title"></h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 form-group">
              <label class="control-label text-uppercase">
                Titulo <span class="text-danger">*</span>
              </label>
              <input type="text" class="form-control" id="titulo" name="titulo"
                onkeypress="return numbersandletters(event)" placeholder="INGRESE TITULO" data-parsley-required="true">
            </div>

            <div class="col-md-12 form-group">
              <label class="control-label text-uppercase">
                Contenido <span class="text-danger">*</span>
              </label>
              <textarea type="text" rows="8" class="form-control" name="contenido" id="contenido"
                placeholder="INGRESE CONTENIDO" data-parsley-required="true"></textarea>
            </div>

            <div class="col-md-12 form-group">
              <div>
                <b>Variables</b>
              </div>
              <div class="py-2">
                <ul>
                  <li>{names}: Nombres</li>
                  <li>{surnames}: Apellidos</li>
                  <li>{cliente}: Nombre Completo</li>
                  <li>{document}: N° de Identidad</li>
                  <li>{mobile}: N° Telefonico</li>
                  <li>{mobiledos}: mobile opcional</li>
                  <li>{note}: nota</li>
                  <li>{email}: Correo</li>
                  <li>{address}: Dirección</li>
                  <li>{latitud}: Latitud</li>
                  <li>{longitud}: Longitud</li>
                  <li>{reference}: Referencia</li>
                  <li>{net_ip}: IP</li>
                  <li>{business_name}: Nombre de la empresa</li>
                  <li>{debt_total_list}: Lista de todas las deuda del cliente</li>
                  <li>
                    {debt_total_month_count}: Total de meses de todas las deuda del cliente
                  </li>
                  <li class="list_debts" style="display: none">{debt_list}: Lista de deudas seleccionadas</li>
                  <li class="list_debts" style="display: none">{debt_amount}: Total de deudas seleccionadas</li>
                  <li class="list_debts" style="display: none">{debt_months}: Meses de deudas seleccionadas</li>
                  <li class="info_ticket" style="display: none">{ticket_num}: Número de Ticket</li>
                  <li class="info_payment_massive" style="display: none">{list_payments}: Lista basica de pago</li>
                  <li class="info_payment_total" style="display: none">{payment_total}: Total pagado</li>
                  <li class="info_payment_month" style="display: none">
                    {payment_months}: Lista de meses pagados separado por
                    ","</li>
                  <li class="info_payment" style="display: none">{payment_links}: Links de boleta</li>
                  <li class="info_payment" style="display: none">{payment_num}: Número de recibo de pago</li>
                  <li class="info_payment" style="display: none">{payment_pending}: Saldo pendiente de pago</li>

                  <li class="info_promise" style="display: none">{promise_date}: Fecha de Promesa</li>
                  <li class="info_promise" style="display: none">{promise_comment}: Comentario de Promesa</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"></i>Cerrar</button>
          <button type="submit" class="btn btn-blue">
            <i class="fas fa-save mr-2"></i><span id="text-button"></span>
          </button>
        </div>
      </div>
    </div>
  </form>
  <?php modal("mapModal"); ?>
</div>