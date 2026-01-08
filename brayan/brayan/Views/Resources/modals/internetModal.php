<style>
    .toggle-label {
        font-weight: bold;
        margin-left: 10px;
    }

    .toggle-switch {
        display: inline-block;
        width: 40px;
        height: 20px;
        background-color: #ccc;
        border-radius: 10px;
        position: relative;
        cursor: pointer;
    }

    .toggle-switch::before {
        content: '';
        display: block;
        width: 20px;
        height: 20px;
        background-color: #fff;
        border-radius: 50%;
        position: absolute;
        top: 0;
        left: 0;
        transition: left 0.2s ease-in-out;
    }

    .toggle-switch.active::before {
        left: 20px;
    }

    .toggle-switch.active {
        background-color: #19ba14;
    }

    .toggle-label {
        vertical-align: super;
    }
</style>
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
                        <input type="hidden" id="idservices" name="idservices" value="">
                        <div class="col-md-9 form-group">
                            <label for="service" class="control-label">Nombre de Plan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="service" id="service"
                                onkeypress="return numbersandletters(event)" placeholder="Plan Premiun 4Mbps"
                                data-parsley-required="true">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="price" class="control-label">Precio de Plan <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="price" id="price" min="0" step="0.1"
                                onkeypress="return decimal(event)"
                                pattern="^(0*[1-9][0-9]*(\.[0-9]+)?|0+\.[0-9]*[1-9][0-9]*)$" placeholder="0.00"
                                data-parsley-required="true">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="rise" class="control-label">Máx. Subida <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control" name="rise" id="rise"
                                    onkeypress="return decimal(event)" placeholder="0">
                                <div class="input-group-prepend">
                                    <select class="form-control select-plans" name="rise_select" id="rise_select">
                                        <option value="MBPS" selected>MBPS</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="descent" class="control-label">Máx. Bajada <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control" name="descent" id="descent"
                                    onkeypress="return numbers(event)" placeholder="0">
                                <div class="input-group-prepend">
                                    <select class="form-control select-plans" name="descent_select" id="descent_select">
                                        <option value="MBPS" selected>MBPS</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="details" class="control-label">Detalles</label>
                            <input type="text" class="form-control text-uppercase" id="details" name="details"
                                onkeypress="return numbersandletters(event)"
                                placeholder="Internet banda ancha 4Mbps/2Mbps">
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="details" class="control-label">Profile(Mikrotik)</label>
                            <input type="text" class="form-control" id="profile" name="profile"
                                onkeypress="return numbersandletters(event)">
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="listStatus" class="control-label">Estado</label>
                            <select class="form-control" name="listStatus" id="listStatus">
                                <option value="1">ACTIVO</option>
                                <option value="2">DESACTIVADO</option>
                            </select>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="routersList" class="control-label">Routers</label><br>
                            <label>
                                <input type="checkbox" id="selectAll" class="toggle-input d-none">
                                <span class="toggle-label">Seleccione los Routers</span>
                                <span class="toggle-switch"></span>
                            </label>
                            <br>
                            <select id="routersList" name="routersList[]" class="form-control text-uppercase" multiple>
                                <?php
                                foreach ($data['routers'] as $k => $z) {
                                    $z = (object) $z;
                                    ?>
                                    <option value="<?= $z->id ?>"><?= $z->name ?></option>
                                    <?php
                                    $first = false;
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal"></i>Cerrar</button>
                    <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i><span
                            id="text-button"></span></button>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function () {
        $('#selectAll').on('change', function () {
            if ($(this).is(':checked')) {
                $('#routersList').hide();
                $('#routersList option').prop('selected', false);
                $(this).next('.toggle-label').text('Todos los routers');
            } else {
                $('#routersList').show();
                $(this).next('.toggle-label').text('Seleccione los routers');
            }
        });

        $(".toggle-input").change(function () {
            $(this).siblings(".toggle-switch").toggleClass("active", $(this).prop("checked"));
        });
    });
</script>