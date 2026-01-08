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
                        <input type="hidden" id="idprovider" name="idprovider" value="">
                        <div class="col-md-12 form-group">
                            <label for="provider" class="control-label">Razon Social <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="provider" id="provider" onkeypress="return letters(event)" placeholder="RAZON SOCIAL O NOMBRE" data-parsley-required="true">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="listTypes" class="control-label">Tipo doc.</label>
                            <select class="form-control" name="listTypes" id="listTypes">
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="document" class="control-label">Número doc. <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="document" id="document" onkeypress="return numbers(event)" maxlength="20">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-white btn-search" onclick="search_document();">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="mobile" class="control-label">Celular</label>
                            <input type="text" class="form-control" name="mobile" id="mobile" onkeypress="return numbers(event)" placeholder="999999999" maxlength="10">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="email" class="control-label">Correo</label>
                            <input type="text" class="form-control" name="email" id="email" onkeypress="return mail(event)" placeholder="EXAMPLE@EXAMPLE.COM">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="address" class="control-label">Dirreción</label>
                            <input type="text" class="form-control text-uppercase" name="address" id="address" onkeypress="return numbersandletters(event)" placeholder="INGRESE DIRECCIÓN">
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
<div id="modal-import" class="modal fade p-0" role="dialog" style="display: none;">
    <form autocomplete="off" name="transactions_import" id="transactions_import">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-uppercase" id="text-title-import"></h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <p class="m-0">Antes de iniciar la importación generar una copia de seguridad en caso revertir algún cambio no deseado,
                            en caso de incluir proveedores repetidos estos seran excluidos de la importación.
                            <a href="<?= base_style() ?>/resources/providers.xlsx">Descargar Plantilla</a></p>
                        </div>
                        <div class="col-md-12 form-group">
                          <div class="input-group">
                              <input type="text" class="form-control" name="text-file" id="text-file" readonly>
                              <div class="input-group-append">
                                <label class="btn btn-default cursor-pointer" for="import_providers">
                                  <input type="file" id="import_providers" name="import_providers" accept=".xls, .xlsx" style="display:none">
                                  <i class="fas fa-folder-open"></i>
                                  </label>
                              </div>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"></i>Cerrar</button>
                    <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i><span id="text-button-import"></span></button>
                </div>
            </div>
        </div>
    </form>
</div>
