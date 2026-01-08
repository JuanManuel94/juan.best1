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
                        <input type="hidden" id="idvoucher" name="idvoucher" value="">
                        <div class="col-md-12 form-group">
                            <label for="voucher" class="control-label">Nombre de comprobante <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="voucher" id="voucher" onkeypress="return letters(event)" placeholder="INGRESE COMPROBANTE" data-parsley-required="true">
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
<div id="modal-view" class="modal fade p-0" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-uppercase" id="text-view"></h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="list_series" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="title-table text-center">Fecha</th>
                                        <th class="title-table text-center">Serie</th>
                                        <th class="title-table text-center">Disponible</th>
                                        <th class="title-table text-center">Utilizados</th>
                                    </tr>
                                </thead>
                                <tbody id="listSeries"></tbody>
                            </table>
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
<div id="modal-serie" class="modal fade p-0" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <form autocomplete="off" name="transactions_serie" id="transactions_serie">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-uppercase" id="text-serie"></h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="idserie" name="idserie" value="">
                        <input type="hidden" id="idvouchers" name="idvouchers" value="">
                        <div class="col-md-6 form-group">
                            <label for="date" class="control-label">Fecha <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="date" id="date" data-parsley-required="true">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="serie" class="control-label">Serie <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="serie" id="serie" placeholder="F001" onkeypress="return numbersandletters(event)" maxlength="4" data-parsley-required="true">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="fromc" class="control-label">Del</label>
                            <input type="number" class="form-control" name="fromc" id="fromc" min="1" placeholder="1" onkeypress="return numbers(event)" value="1">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="until" class="control-label">Al</label>
                            <input type="number" class="form-control" name="until" id="until" min="1" placeholder="2000" onkeypress="return numbers(event)" value="1">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="available" class="control-label">Disponible</label>
                            <input type="text" class="form-control" name="available" id="available" onkeypress="return numbers(event)" value="0">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="used" class="control-label">Utilizados</label>
                            <input type="text" class="form-control" name="used" id="used" onkeypress="return numbers(event)" disabled>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"></i>Cerrar</button>
                    <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i><span id="text-series"></span></button>
                </div>
            </div>
        </div>
    </form>
</div>
