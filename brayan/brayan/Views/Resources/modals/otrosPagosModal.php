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
                Tipo <span class="text-danger">*</span>
              </label>
              <input type="hidden" id="currentType">
              <select
                class="form-control text-uppercase"
                id="tipo"
                name="tipo"
                data-parsley-required="true"
                onChange="changeTipo()">
                <option value="INGRESO">Ingreso</option>
                <option value="EGRESO">Egreso</option>
              </select>
            </div>

            <div class="col-md-12 form-group">
              <label class="control-label text-uppercase">
                Fecha <span class="text-danger">*</span>
              </label>
              <input
                type="date"
                class="form-control text-uppercase"
                id="fecha"
                name="fecha"
                placeholder="INGRESE FECHA"
                data-parsley-required="true">
            </div>

            <div class="col-md-12 form-group">
              <label class="control-label text-uppercase">
                Descripción <span class="text-danger">*</span>
              </label>
              <textarea
                id="descripcion"
                name="descripcion"
                placeholder="INGRESE DESCRIPCION"
                class="form-control text-uppercase"
                data-parsley-required="true"></textarea>
            </div>

            <div class="col-md-12 form-group">
              <label class="control-label text-uppercase">
                Monto <span class="text-danger">*</span>
              </label>
              <input
                type="number"
                class="form-control text-uppercase"
                name="monto"
                id="monto"
                onkeypress="return numbersandletters(event)"
                placeholder="INGRESE MONTO"
                data-parsley-required="true">
            </div>

            <div class="col-md-12 form-group" id="state-container">
              <label class="control-label text-uppercase">
                Estado <span class="text-danger">*</span>
              </label>
              <select
                class="form-control text-uppercase"
                id="state"
                name="state"
                data-parsley-required="true">
                <option value="PENDIENTE">Pendiente</option>
                <option value="PAGADO">Pagado</option>
              </select>
            </div>

            <?php form("camposForm", $data) ?>
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