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
                <div class="modal-body" style="padding: 0 15px">
                  <style type="text/css">
                      .products-tab li a.nav-link.active {
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
                      .products-tab li a.nav-link, .customtab li a.nav-link {
                          border: 0px;
                          padding: 15px 20px;
                          color: #54667a;
                      }
                      .nav-tabs {
                          background: #FFF;
                      }
                  </style>
                  <ul class="nav nav-tabs products-tab nav-products" role="tablist">
                      <li class="nav-item"> <a class="nav-link active show" data-toggle="tab" href="#data-general" role="tab" aria-expanded="true" aria-selected="false">General</a></li>
                      <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#data-attributes" role="tab" aria-expanded="false" aria-selected="true">Atributos</a></li>
                  </ul>
                  <div class="tab-content mb-0" style="padding: 10px 0">
                    <div class="tab-pane active show" id="data-general" role="tabpanel" aria-expanded="true">
                      <div class="row">
                        <input type="hidden" id="idproduct" name="idproduct" value="">
                        <input type="hidden" id="current_photo" name="current_photo" value="">
                        <div class="col-md-12 form-group">
                            <label for="product" class="control-label">Producto <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="product" id="product" placeholder="INGRESE PRODUCTO"  onkeypress="return numbersandletters(event)" data-parsley-required="true">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="barcode" class="control-label">Codigo de barras</label>
                            <input type="text" class="form-control" name="barcode" id="barcode" placeholder="1081039110920000" onkeypress="return numbers(event)" maxlength="13">
                        </div>
                        <div class="col-md-3 form-group">
                          <label class="control-label">Modelo</label>
                          <input type="text" class="form-control text-uppercase" name="model" id="model" placeholder="ATX-517" onkeypress="return numbersandletters(event)">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="purchase_price" class="control-label">Precio de compra <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="purchase_price" id="purchase_price" min="0" step="0.1" onkeypress="return decimal(event)" placeholder="0.00" pattern="^(0*[1-9][0-9]*(\.[0-9]+)?|0+\.[0-9]*[1-9][0-9]*)$" data-parsley-required="true">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="sale_price" class="control-label">Precio de venta <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="sale_price" id="sale_price" min="0" step="0.1" onkeypress="return decimal(event)" placeholder="0.00" pattern="^(0*[1-9][0-9]*(\.[0-9]+)?|0+\.[0-9]*[1-9][0-9]*)$" data-parsley-required="true">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="stock" class="control-label">Stock <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="stock" id="stock" onkeypress="return numbers(event)" placeholder="0" data-parsley-required="true">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="stock_alert" class="control-label">Stock minimo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="stock_alert" id="stock_alert" onkeypress="return numbers(event)" placeholder="0" data-parsley-required="true">
                        </div>
                        <div class="col-md-6 form-group">
                          <label class="control-label">Descripción</label>
                          <input type="text" class="form-control text-uppercase" name="description" id="description" placeholder="DESCRIPCIÓN">
                        </div>
                        <div class="col-md-12 form-group">
                          <div class="checkbox checkbox-css">
                            <input type="checkbox" id="extra" name="extra">
                            <!--<label for="extra" class="cursor-pointer">Agregar información extra</label>-->
                          </div>
                        </div>
                        <div id="cont-serie" class="col-md-3 form-group" style="display:none">
                          <label class="control-label">Nº Serie</label>
                          <input type="text" class="form-control text-uppercase" name="serie" id="serie" placeholder="7505127807" onkeypress="return numbers(event)" maxlength="10">
                        </div>
                        <div id="cont-mac" class="col-md-3 form-group" style="display:none">
                          <label class="control-label">Mac (opcional)</label>
                          <input type="text" class="form-control text-uppercase" name="mac" id="mac" placeholder="00:00:00:00:00:00">
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane" id="data-attributes" role="tabpanel" aria-expanded="true">
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                            <label class="control-label">Imágen <span class="text-danger"></span></label>
                            <label for="image" class="avatar-uploader">
                              <div tabindex="0" class="el-upload el-upload--text">
                                <img id="img_product" src="<?= base_style() ?>/images/default/no_image.jpg" class="avatar">
                                <input id="image" type="file" name="image_product" class="el-upload__input">
                              </div>
                            </label>
                          </div>
                        </div>
                        <div class="col-md-9">
                          <div class="row">
                            <div class="col-md-6 form-group">
                              <label for="listUnits" class="control-label">Unidad de medida</label>
                              <select class="form-control" name="listUnits" id="listUnits"></select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="listProviders" class="control-label">Proveedor <span class="text-danger">*</span></label>
                                <select class="form-control" name="listProviders" id="listProviders"></select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="listCategories" class="control-label">Categoria <span class="text-danger">*</span></label>
                                <select class="form-control" name="listCategories" id="listCategories"></select>
                            </div>
                            <div class="col-md-6 form-group">
                              <label class="control-label">Marca</label>
                              <input type="text" class="form-control text-uppercase" name="brand" id="brand" placeholder="TP-LINK" onkeypress="return numbersandletters(event)">
                            </div>
                          </div>
                        </div>
                      </div>
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
                            en caso de incluir productos repetidos estos seran excluidos de la importación.
                            <a href="<?= base_style() ?>/resources/products.xlsx">Descargar Plantilla</a></p>
                        </div>
                        <div class="col-md-12 form-group">
                          <div class="input-group">
                              <input type="text" class="form-control" name="text-file" id="text-file" readonly>
                              <div class="input-group-append">
                                <label class="btn btn-default cursor-pointer" for="import_products">
                                  <input type="file" id="import_products" name="import_products" accept=".xls, .xlsx" style="display:none">
                                  <i class="fas fa-folder-open"></i>
                                  </label>
                              </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                            <p class="m-0"><strong>Nota:</strong> Tener en cuenta que las categoria,unidad de medida y proveedor deben estar registrado previamente.</p>
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
