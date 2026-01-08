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
                        <input type="hidden" id="idrunway" name="idrunway" value="">
                        <div class="col-md-12 form-group">
                            <label for="printing" class="control-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="runway" id="runway" onkeypress="return numbersandletters(event)" placeholder="INGRESE NOMBRE" data-parsley-required="true">
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
