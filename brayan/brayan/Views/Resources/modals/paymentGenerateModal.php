<div id="bill-modal-action" class="modal fade p-0" role="dialog" style="display: none;">
  <form autocomplete="off" name="transactions" id="bill-transactions">
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
                Fecha de Inicio <span class="text-danger">*</span>
              </label>
              <input type="date" disabled class="form-control text-uppercase" id="fecha" name="fecha"
                placeholder="INGRESE FECHA" data-parsley-required="true">
            </div>

            <div class="col-md-12 form-group">
              <label class="control-label text-uppercase">
                Comprobante <span class="text-danger">*</span>
              </label>
              <select class="form-control" name="listVouchers" id="vouchersserv" style="width:100%;"></select>
            </div>

            <div class="col-md-12 form-group">
              <label class="control-label text-uppercase">
                Serie <span class="text-danger">*</span>
              </label>
              <select class="form-control" name="listSerie" id="serieserv" style="width:100%;"></select>
            </div>

            <div class="col-md-12 form-group">
              <label class="control-label text-uppercase">
                Observación
              </label>
              <textarea class="form-control text-uppercase" name="observacion"
                placeholder="INGRESE OBSERVACIÓN"></textarea>
            </div>

            <div class="col-md-12 form-group">
              <label class="control-label text-uppercase">
                Número de Meses <span class="text-danger">*</span>
              </label>
              <input type="number" step="1" class="form-control text-uppercase" name="months" id="months"
                onkeypress="return numbersandletters(event)" placeholder="INGRESE NUMERO DE MESES"
                data-parsley-required="true">
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
</div>