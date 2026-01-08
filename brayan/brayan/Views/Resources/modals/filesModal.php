<div id="modal-action-files" class="modal fade p-0" role="dialog" style="display: none;">
  <div class="modal-dialog modal-dialog-centered modal-file">
      <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title text-uppercase" id="file-text-title"></h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-12">
                <input type="hidden" id="f_object_id">
                <input type="hidden" id="f_tabla">
                <input type="file" class="form-control" id="upload-file" onchange="uploadFile()">
              </div>
              <div class="col-12 mt-3">
                <h5>Lista de archivos</h5>
                <div class="table-responsive">
                  <table id="list-files" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed w-100">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>NOMBRE</th>
                        <th>PESO</th>
                        <th class="all" data-orderable="false" style="max-width: 40px !important; width: 40px;"></th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
          </div>
      </div>
  </div>
</div>
