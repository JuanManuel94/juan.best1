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
                        <input type="hidden" id="idfacility" name="idfacility" value="">
                        <div class="col-md-12 form-group">
                            <label for="listClients" class="control-label">Cliente <span class="text-danger">*</span></label>
                            <select class="form-control" name="listClients" id="listClients" style="width:100%;"></select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="listTechnical" class="control-label">Técnico asignado</label>
                            <select class="form-control" name="listTechnical" id="listTechnical" style="width:100%" data-parsley-required="true">
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="insDate" class="control-label">Fecha atención</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="insDate" id="insDate">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="instPrice" class="control-label">Costo Ins.</label>
                            <input type="number" class="form-control" name="instPrice" id="instPrice" min="0" step="0.1" onkeypress="return numbers(event)" placeholder="0.00">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="detail" class="control-label">Detalle</label>
                            <textarea class="form-control text-uppercase" name="detail" id="detail" placeholder="INGRESE DETALLE DE INSTALACIÓN" rows="6" style="min-height: 50px; overflow: hidden; overflow-wrap: break-word; height: 80px;"></textarea>
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
<div id="modal-tools" class="modal fade p-0" role="dialog" style="display: none;">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header">
              <h6 class="modal-title text-uppercase" id="text-title-tools"></h6>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
              <input type="hidden" id="tools_country">
              <input type="hidden" id="tools_number">
              <div class="row">
                  <div class="col text-center font-weight-bold mt-3">
                    <button type="button" class="btn btn-lg btn-primary" id="btn-sms">
                      <i class="fas fa-comment-alt fa-5x"></i>
                    </button>
                     <p>SMS</p>
                   </div>
                   <div class="col text-center font-weight-bold mt-3">
                     <button type="button" class="btn btn-lg btn-info" id="btn-tocall">
                       <i class="fa fa-phone-alt fa-5x"></i>
                     </button>
                      <p>Llamar</p>
                    </div>
                   <div class="col text-center font-weight-bold mt-3">
                     <button type="button" class="btn btn-lg btn-whatsapp" id="btn-whatsapp">
                       <i class="fab fa-whatsapp fa-5x"></i>
                     </button>
                      <p>WhatsApp</p>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal"></i>Cerrar</button>
          </div>
      </div>
  </div>
</div>
<div id="modal-view" class="modal fade p-0" role="dialog" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-uppercase" id="text-view"></h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
              <ul class="media-list underline width-full ">
                <li class="media media-sm clearfix">
                  <a href="javascript:;" class="pull-left">
                    <img id="view-image-client" class="media-object rounded-corner">
                  </a>
                  <div class="media-body">
                    <div id="view-client" class="email-from text-inverse f-s-14 f-w-600 m-b-1"></div>
                    <div id="view-celdoc" class="m-b-1"></div>
                    <div id="view-address" class="email-to"></div>
                  </div>
                  <div class="widget-list-action text-right">
                    <div class="m-b-1"><small id="view-created"></small></div>
                    <div class="m-b-1"><small id="view-visit"></small></div>
                    <div class="m-b-1"><b id="view-state"></b></div>
                  </div>
                </li>
      				</ul>
              <style type="text/css">
                  .clients-tab li a.nav-link.active, {
                      border-bottom: 2px solid #00acac !important;
                      color: #00acac;
                  }
                  .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
                      color: #495057;
                      background-color: #fff;
                      border-color: #ddd #ddd #fff;
                  }
                  .nav-tabs .nav-link {
                      border: 1px solid transparent;
                      border-top-left-radius: .25rem;
                      border-top-right-radius: .25rem;
                  }
                  .nav-link {
                      display: block;
                      padding: .5rem 1rem;
                  }
                  .clients-tab li a.nav-link, .customtab li a.nav-link {
                      border: 0px;
                      padding: 15px 20px;
                      color: #54667a;
                  }
                  .nav-tabs {
                      background: #FFF;
                  }
              </style>
              <ul class="nav nav-tabs clients-tab nav-clients" role="tablist">
                  <li class="nav-item"> <a class="nav-link active show" data-toggle="tab" href="#data-comments" role="tab" aria-expanded="true" aria-selected="false">Respuestas<span class="label label-success ml-2" id="view-comments"></span></a></li>
                  <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#data-images" role="tab" aria-expanded="false" aria-selected="true">Imagenes<span class="label label-success ml-2" id="view-images"></span></a></li>
              </ul>
              <div class="tab-content mb-0" style="padding: 10px 0">
                <div class="tab-pane active show" id="data-comments" role="tabpanel" aria-expanded="true">
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="panel panel-white post mb-0">
                        <div class="post-heading">
                          <div class="pull-left image">
                            <img class="img-circle avatar" id="view-user-post">
                          </div>
                          <div class="pull-left meta">
                            <div class="title h5"id="view-user"></div>
                            <h6 class="time text-orange"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></h6>
                          </div>
                        </div>
                        <div class="post-description pt-0">
                          <p id="view-services" class="mb-0 f-w-700"></p>
                          <p id="view-description" class="mb-0"></p>
                        </div>
                        <div class="post-footer pt-0 pb-0">
                          <ul class="comments-list" id="post-comment"></ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane" id="data-images" role="tabpanel" aria-expanded="true">
                  <div class="gallery" id="containerImages"></div>
                </div>
              </div>
            </div>
        </div>
    </div>
</div>
