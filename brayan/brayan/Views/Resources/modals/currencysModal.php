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
                        <input type="hidden" id="idcurrency" name="idcurrency" value="">
                        <div class="col-md-12 form-group">
                            <label for="currency" class="control-label">Nombre de divisa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="currency" id="currency" placeholder="Nuevo Sol" onkeypress="return letters(event)" data-parsley-required="true">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="iso" class="control-label">ISO <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="iso" id="iso" placeholder="PEN" onkeypress="return letters(event)" maxlength="5" data-parsley-required="true">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="money" class="control-label">Moneda <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="money" id="money" placeholder="SOL" onkeypress="return letters(event)" data-parsley-required="true">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="money_plural" class="control-label">Moneda (Plural)<span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="money_plural" id="money_plural" placeholder="SOLES" onkeypress="return letters(event)" data-parsley-required="true">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="listLanguage" class="control-label">Lenguaje</label>
                            <select name="listLanguage" id="listLanguage" class="form-control">
                                <option value="EN">EN</option>
                                <option value="ES">ES</option>
                                <option value="FR">FR</option>
                                <option value="IT">IT</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="symbol" class="control-label">Simbolo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="symbol" id="symbol" placeholder="S/."  maxlength="5" data-parsley-required="true">
                        </div>
                        <div class="col-md-12 form-group">
                          <label for="listStatus" class="control-label">Estado</label>
                          <select class="form-control" name="listStatus" id="listStatus">
                              <option value="1">ACTIVO</option>
                              <option value="2">DESACTIVADO</option>
                          </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"></i>Cerrar</button>
                    <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i><span id="text-button"></span></button>
                </div>
            </div>
        </div>
    </form>
</div>
