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
                        <input type="hidden" id="idfacility" name="idfacility" value="">
                        <div class="col-md-12 form-group">
                            <label for="listProduct" class="control-label">Producto</label>
                            <select class="form-control" name="listProduct" id="listProduct"></select>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="listProduct" class="control-label">Serie <b class="text-danger">*</b></label>
                            <input type="text" class="form-control" name="serie" id="serie">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="listProduct" class="control-label">Mac <b class="text-danger">*</b></label>
                            <input type="text" class="form-control" name="mac" id="mac">
                        </div>
                        
                        <div class="col-md-12 form-group">
                            <label for="listConditions" class="control-label">Condición</label>
                            <select class="form-control" name="listConditions" id="listConditions">
                                <option value="PRESTAMO">PRESTAMO</option>
                                <option value="VENTA">VENTA</option>
                            </select>
                        </div>
                        <div id="divquantity" class="col-md-4 form-group" style="display:none">
                            <label for="quantity" class="control-label">Cantidad</label>
                            <input type="number" class="form-control" name="quantity" id="quantity" min="1" step="1" onkeypress="return numbers(event)" placeholder="0.00" value="1">
                            <input type="hidden" id="current_stock" value="">
                        </div>
                        <div id="divprice" class="col-md-4 form-group" style="display:none">
                            <label for="price" class="control-label">Precio</label>
                            <input type="text" class="form-control" name="price" id="price" min="0" step="0.1" onkeypress="return decimal(event)" placeholder="0.00" value="0.00" readonly>
                        </div>
                        <div id="divtotal" class="col-md-4 form-group" style="display:none">
                            <label for="total" class="control-label">Total</label>
                            <input type="text" class="form-control" name="total" id="total" min="0" step="0.1" onkeypress="return decimal(event)" placeholder="0.00" value="0.00" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"></i>Cerrar</button>
                    <button type="submit" class="btn btn-blue" id="btn-saved"><i class="fas fa-save mr-2"></i><span id="text-button"></span></button>
                </div>
            </div>
        </div>
    </form>
</div>
