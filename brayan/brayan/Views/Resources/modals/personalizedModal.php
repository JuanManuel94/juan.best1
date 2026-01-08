<div id="modal-action" class="modal fade p-0" role="dialog" style="display: none;">
    <form autocomplete="off" name="transactions" id="transactions">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-uppercase" id="text-title"></h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="idservices" name="idservices" value="">
                        <div class="col-md-9 form-group">
                            <label for="service" class="control-label">Nombre de Plan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="service" id="service" onkeypress="return numbersandletters(event)" placeholder="Tv Básico" data-parsley-required="true">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="price" class="control-label">Precio de Plan <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="price" id="price" min="0" step="0.1" onkeypress="return numbers(event)" pattern="^(0*[1-9][0-9]*(\.[0-9]+)?|0+\.[0-9]*[1-9][0-9]*)$" placeholder="0.00" data-parsley-required="true">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="details" class="control-label">Detalles</label>
                            <input type="text" class="form-control text-uppercase" id="details" name="details" onkeypress="return numbersandletters(event)" placeholder="Full HD con 100 canales">
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
                    <button type="button" class="btn btn-white" data-dismiss="modal"></i>Cerrar</button>
                    <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i><span id="text-button"></span></button>
                </div>
            </div>
        </div>
    </form>
</div>
