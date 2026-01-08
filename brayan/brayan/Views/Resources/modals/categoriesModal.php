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
                        <input type="hidden" id="idcategory" name="idcategory" value="">
                        <div class="col-md-12 form-group">
                            <label for="category" class="control-label">Categoria <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="category" id="category" placeholder="INGRESE CATEGORIA" onkeypress="return letters(event)" data-parsley-required="true">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="description" class="control-label">Descripción</label>
                            <textarea class="form-control text-uppercase" name="description" id="description" placeholder="INGRESE DESCRIPCIÓN" rows="6" style="min-height: 50px; overflow: hidden; overflow-wrap: break-word; height: 80px;"></textarea>
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
                            en caso de incluir categorias repetidas estos seran excluidos de la importación.
                            <a href="<?= base_style() ?>/resources/categories.xlsx">Descargar Plantilla</a></p>
                        </div>
                        <div class="col-md-12 form-group">
                          <div class="input-group">
                              <input type="text" class="form-control" name="text-file" id="text-file" readonly>
                              <div class="input-group-append">
                                <label class="btn btn-default cursor-pointer" for="import_categories">
                                  <input type="file" id="import_categories" name="import_categories" accept=".xls, .xlsx" style="display:none">
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
