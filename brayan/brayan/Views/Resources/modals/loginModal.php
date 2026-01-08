<div id="modal-reset" class="modal fade p-0" role="dialog" style="display: none;">
    <form autocomplete="off" name="transactions_reset" id="transactions_reset">
        <div class="modal-dialog">
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
                            <label for="email" class="control-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="email" onkeypress="return mail(event)" data-parsley-required="true">
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="submit" class="btn btn-success"><span id="text-button"></span></button>
                </div>
            </div>
        </div>
    </form>
</div>
