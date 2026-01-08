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
                        <input type="hidden" id="iduser" name="iduser" value="">
                        <div class="col-md-6 form-group">
                            <label for="listTypes" class="control-label">Tipo doc.</label>
                            <select class="form-control" name="listTypes" id="listTypes"></select>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="document" class="control-label">Número doc. <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="document" id="document" onkeypress="return numbers(event)" maxlength="8">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-white btn-search" onclick="search_document();">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="names" class="control-label">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="names" id="names" onkeypress="return letters(event)" placeholder="INGRESE NOMBRE" data-parsley-required="true">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="surnames" class="control-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="surnames" id="surnames" onkeypress="return letters(event)" placeholder="INGRESE APELLIDOS" data-parsley-required="true">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="mobile" class="control-label">Celular <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="mobile" id="mobile" onkeypress="return numbers(event)" maxlength="10" placeholder="999999999" data-parsley-required="true">
                        </div>
                        <div class="col-md-8 form-group">
                            <label for="listProfiles" class="control-label">Perfil</label>
                            <select class="form-control" name="listProfiles" id="listProfiles"></select>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="email" class="control-label">Correo</label>
                            <input type="text" class="form-control" name="email" id="email" onkeypress="return mail(event)" placeholder="EXAMPLE@EXAMPLE.COM">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="username" class="control-label">Usuario <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-lowercase" name="username" id="username" placeholder="INGRESE USUARIO" data-parsley-required="true">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="password" class="control-label">Contraseña</label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="**********">
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
