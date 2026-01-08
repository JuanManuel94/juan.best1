<div id="ticket-modal-validation" class="modal fade p-0" role="dialog" style="display: none;">
    <form autocomplete="off" name="ticket-modal-form" id="ticket-modal-form">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-uppercase" id="text-title">Validación</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning" role="alert">
                        Por favor, ingresa el código de validación enviado a tu número por WhatsApp.
                    </div>
                  <div class="row">
                    <input type="hidden" id="idticket" name="idticket" value="">
                    <div class="col-md-12 form-group">
                        <label class="control-label">CÓDIGO<span class="text-danger">*</span></label>
                        <input type="text" id="code" name="code" class="form-control" required>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                      Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary">
                      <i class="fas fa-save mr-2"></i>
                      <span id="text-button">Validar</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="ticket-modal" class="modal fade p-0" role="dialog" style="display: none;">
    <form autocomplete="off" name="ticket-form" id="ticket-form">
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
                        <input type="hidden" id="idticket" name="idticket" value="">
                        <div class="col-md-6 form-group">
                            <label class="control-label">Asunto <span class="text-danger">*</span></label>
                            <select class="form-control" name="listAffairs" id="listAffairs" style="width:100%;"></select>
                        </div>

                        <div class="col-md-4 form-group">
                            <label class="control-label">Fecha de atención <span class="text-danger">*</span></label>
                            <div class="input-group input-daterange">
                                <input type="text" class="form-control" name="attention_date" id="attention_date" data-parsley-required="true">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                  
                        <div class="col-md-2 form-group">
                            <label class="control-label">Prioridad</label>
                            <select class="form-control" name="listPriority" id="listPriority" style="width:100%;">
                                <option value="1">BAJA</option>
                                <option value="2">MEDIA</option>
                                <option value="3">ALTA</option>
                                <option value="4">URGENTE</option>
                            </select>
                        </div>
                        <div class="col-md-12 form-group">
                            <label class="control-label">Descripción</label>
                            <textarea class="form-control text-uppercase" name="description" id="description" placeholder="INGRESE DESCRIPCIÓN" rows="6" style="min-height: 50px; overflow: hidden; overflow-wrap: break-word; height: 80px;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                      Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary">
                      <i class="fas fa-save mr-2"></i>
                      <span id="text-button">Guardar ticket</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>