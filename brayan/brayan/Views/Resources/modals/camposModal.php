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
                    <label class="control-label">
                      Modulo <span class="text-danger">*</span>
                    </label>
                    <select class="form-control"
                     name="tablaId" 
                     id="tablaId"  
                     data-parsley-required="true">
                    </select>
                  </div>

                  <div class="col-md-12 form-group" id="campo-container">
                    <label class="control-label">
                      Campo <span class="text-danger">*</span>
                    </label>
                    <select class="form-control"
                     name="campoId" 
                     id="campoId">
                    </select>
                  </div>
                </div>

                <div class="row" id="campo-body">
                  <div class="col-md-12 form-group" id="input-nombre">
                    <label class="control-label">
                      Nombre <span class="text-danger">*</span>
                    </label>
                    <input 
                      type="text" 
                      class="form-control text-uppercase" 
                      name="nombre"
                      id="nombre"
                      onkeypress="return numbersandletters(event)" 
                      placeholder="INGRESE EL NOMBRE DEL CAMPO" 
                      data-parsley-required="true"
                    >
                    <input type="hidden" name="campo" id="campo" />
                  </div>

                  <div class="col-md-12 form-group">
                    <label class="control-label">
                      Tipo Campo <span class="text-danger">*</span>
                    </label>
                    <select class="form-control"
                    name="tipo" 
                    id="tipo"  
                    data-parsley-required="true">
                    <option value="varchar">Varchar</option>
                    <option value="int">Int</option>
                    <option value="decimal">Decimal</option>
                    </select>
                  </div>

                  <div class="col-md-12 form-group">
                    <label class="control-label">
                      Obligatorio <span class="text-danger">*</span>
                    </label>
                    <select class="form-control"
                    name="obligatorio" 
                    id="obligatorio"
                    data-parsley-required="true">
                    <option value="1">SI</option>
                    <option value="0">NO</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal"></i>Cerrar</button>
                  <button type="button" id="btn-delete" class="btn btn-red">
                    <i class="fas fa-trash mr-2"></i>Eliminar
                  </button>
                  <button type="submit" class="btn btn-blue" id="btn-save" disabled>
                    <i class="fas fa-save mr-2"></i><span id="text-button"></span>
                  </button>
              </div>
          </div>
      </div>
    </form>
</div>
