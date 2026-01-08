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
              <label class="control-label text-uppercase">
                Nombre <span class="text-danger">*</span>
              </label>
              <input type="text" class="form-control" id="nombre" name="nombre"
                onkeypress="return numbersandletters(event)" placeholder="INGRESE NOMBRE" data-parsley-required="true">
            </div>

            <div class="col-md-12 form-group">
              <label class="control-label text-uppercase">
                zona <span class="text-danger">*</span>
              </label>
              <select class="form-control text-left" name="zonaId" id="zonaId">
                <?php foreach ($data['zonas'] as $zona): ?>
                  <option value="<?= $zona['id'] ?>" class="text-center">
                    <?= $zona['nombre_zona'] ?>
                  </option>
                <?php endforeach ?>
              </select>
            </div>

            <div class="col-md-6 form-group">
              <label class="control-label text-uppercase">
                Color de tubo <span class="text-danger">*</span>
              </label>
              <input type="color" name="color_tubo" class="form-control" id="color_tubo">
            </div>

            <div class="col-md-6 form-group">
              <label class="control-label text-uppercase">
                Color de hilo <span class="text-danger">*</span>
              </label>
              <input type="color" name="color_hilo" class="form-control" id="color_hilo">
            </div>

            <div class="col-md-12 form-group">
              <label class="control-label text-uppercase">
                Tipo <span class="text-danger">*</span>
              </label>
              <select name="tipo" class="form-control" id="tipo">
                <option value="nap">CAJA NAP</option>
                <option value="mufa">MUFA</option>
              </select>
            </div>

            <div class="col-md-12 form-group">
              <label class="control-label text-uppercase">
                Coordenadas <span class="text-danger">*</span>
              </label>
              <div class="row pl-2 pr-2">
                <button type="button" class="btn btn-icono col-2" data-toggle="tooltip" data-placement="top"
                  data-original-title="Abrir google maps" onclick="open_map()">
                  <i class="fas fa-map-marker-alt"></i>
                </button>
                <input type="text" class="form-control col-10" id="coordenadas" disabled
                  onkeypress="return numbersandletters(event)" placeholder="Lat, Lng" data-parsley-required="true">
                <input type="hidden" class="form-control" id="latitud" name="latitud">
                <input type="hidden" class="form-control" id="longitud" name="longitud">
              </div>
            </div>

            <div class="col-md-12 form-group">
              <label class="control-label text-uppercase">
                Ubicación <span class="text-danger">*</span>
              </label>
              <input type="text" class="form-control" name="ubicacion" id="ubicacion"
                onkeypress="return numbersandletters(event)" placeholder="INGRESE UBICACIÓN"
                data-parsley-required="true">
            </div>

            <div class="col-md-12 form-group" id="container-puertos">
              <label class="control-label text-uppercase">
                Puertos <span class="text-danger">*</span>
              </label>
              <input type="number" min="1" class="form-control" name="puertos" id="puertos"
                onkeypress="return numbersandletters(event)" placeholder="INGRESE PUERTOS" data-parsley-required="true">
            </div>

            <div class="col-md-12 form-group">
              <label class="control-label text-uppercase">
                Detalles <span class="text-danger">*</span>
              </label>
              <input type="text" class="form-control" name="detalles" id="detalles"
                onkeypress="return numbersandletters(event)" placeholder="INGRESE DETALLES"
                data-parsley-required="true">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"></i>Cerrar</button>
          <button type="submit" class="btn btn-blue">
            <i class="fas fa-save mr-2"></i><span id="text-button"></span>
          </button>
        </div>
      </div>
    </div>
  </form>
  <?php modal("mapModal"); ?>
</div>