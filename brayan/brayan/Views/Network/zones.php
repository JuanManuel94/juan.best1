<?php
head($data);

$mode_label = [];
$mode_label["1"] = "Simple Queues";
$mode_label["2"] = "PPPoE";
?>
<!-- INICIO TITULO -->
<style>
    .bt-a {
        width: 20px;
        display: inline-block;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
    <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="panel panel-default panel-zonas">
    <div class="panel-heading">
        <h4 class="panel-title">Lista de zonas</h4>
        <div class="panel-heading-btn">
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-reload" onclick="refresh_table()"><i class="fas fa-sync-alt"></i></a>
        </div>
    </div>
    <div class="panel-body border-panel">
        <div class="row">
            <div id="list-btns-tools">
                <div class="options-group btn-group m-r-5">

                    <button type="button" class="btn btn-white" onclick="add()"><i class="fas fa-plus mr-1"></i>Nueva</button>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-12">
                <div class="table-responsive">
                    <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed" data-order='[[ 1, "asc" ]]' style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="max-width: 20px !important; width: 20px;">ID</th>
                                <th>Nombre</th>
                                <th>Routers</th>
                                <th class="all" data-orderable="false"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            foreach ($data['records'] as $k => $r) {
                                $r = (object) $r;
                            ?>

                                <tr>
                                    <td style="max-width: 20px !important; width: 20px;"><?= $r->id ?></td>
                                    <td><a href="#!"><?= $r->name ?></a><small style="display: block;" class="text-warning"><?= $mode_label[$r->mode] ?></small></td>
                                    <td><span class="badge"><?= $r->routers ?></span></td>
                                    <td class="all" data-orderable="false">
                                        <a href="#!" class="bt-a" onclick="edit(<?= $r->id ?>)"><i class="fa fa-edit"></i></a>
                                        <a href="#!" class="bt-a" onclick="remove(<?= $r->id ?>)"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>

                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modal-add" class="modal fade p-0" role="dialog" style="display: none;">
    <form autocomplete="off" name="addZoneForm" id="addZoneForm">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-uppercase" id="text-ticket">Agregar router</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label class="control-label">Nombre*</label>
                            <input type="text" class="form-control" id="zoneAddDescription" maxlength="60">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="control-label">Modo*</label>
                            <select class="form-control" id="zoneAddMode" name="zoneAddMode">
                                <option value="" disabled>-SELECCIONE UN MODO-</option>
                                <option value="1" selected>Simple Queues</option>
                                <option value="2">PPPoE</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal"></i>Cerrar</button>
                    <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i><span id="text-button-ticket"></span></button>
                </div>
            </div>
        </div>
    </form>
</div>
<div id="modal-edit" class="modal fade p-0" role="dialog" style="display: none;">
    <form autocomplete="off" name="editZoneForm" id="editZoneForm">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-uppercase" id="text-ticket">Agregar router</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" class="form-control" id="zoneEditID" maxlength="60">
                        <div class="col-md-12 form-group">
                            <label class="control-label">Name*</label>
                            <input type="text" class="form-control" id="zoneEditDescription" maxlength="60">
                        </div>

                        <div class="col-md-4 form-group">
                            <label class="control-label">Modo*</label>
                            <select class="form-control" id="zoneEditMode" name="zoneEditMode">
                                <option value="" disabled>-SELECCIONE UN MODO-</option>
                                <option value="1" selected>Simple Queues</option>
                                <option value="2">PPPoE</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal"></i>Cerrar</button>
                    <button type="submit" class="btn btn-blue"><i class="fas fa-save mr-2"></i><span id="text-button-ticket"></span></button>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- FIN TITULO -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.5/sweetalert2.all.min.js" integrity="sha512-m4zOGknNg3h+mK09EizkXi9Nf7B3zwsN9ow+YkYIPZoA6iX2vSzLezg4FnW0Q6Z1CPaJdwgUFQ3WSAUC4E/5Hg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(function() {
        var blockRes = false;
        $('#addZoneForm').submit(function(event) {
            event.preventDefault();
            var valid = true;

            if (!blockRes) {
                blockRes = true;

                $('#addZoneForm').find(".form-control").each(function() {
                    if ($(this).val().length == 0) {
                        $(this).focus();
                        valid = false;
                    }
                });

                if (valid) {
                    var data = {};

                    data.name = $("#zoneAddDescription").val();
                    data.mode = $("#zoneAddMode").val();

                    $.post('<?= base_url(); ?>/network/add_zone', data).done(function(data) {
                        var res = JSON.parse(data);
                        if (res.result == "success") {
                            window.location.reload();
                        }
                    });

                }
            }

        });
        $('#editZoneForm').submit(function(event) {
            event.preventDefault();
            var valid = true;

            if (!blockRes) {
                blockRes = true;

                $('#editZoneForm').find(".form-control").each(function() {
                    if ($(this).val().length == 0) {
                        $(this).focus();
                        valid = false;
                    }
                });

                if (valid) {
                    var data = {};

                    data.id = $("#zoneEditID").val();
                    data.name = $("#zoneEditDescription").val();
                    data.ip = $("#zoneEditIP").val();
                    data.port = $("#zoneEditPort").val();
                    data.username = $("#zoneEditUser").val();
                    data.password = $("#zoneEditPass").val();

                    $.post('<?= base_url(); ?>/network/edit_zone', data).done(function(data) {
                        var res = JSON.parse(data);
                        if (res.result == "success") {
                            window.location.reload();
                        }
                    });

                }
            }

        });
        $(".toggle-password").click(function() {
            if ($(this).siblings("input").attr("type") == "text") {
                $(this).siblings("input").attr("type", "password");
                $(this).find("i").removeClass("icon-eye-slash-open").addClass("icon-eye-open");
            } else {
                $(this).siblings("input").attr("type", "text");
                $(this).find("i").addClass("icon-eye-slash-open").removeClass("icon-eye-open");
            }
        })
        $("#filter_states").change(function() {
            switch ($(this).val()) {
                case "0":
                    $(".status-con").parent().show();
                    $(".status-dis").parent().show();
                    $(".status-unk").parent().show();
                    break;
                case "1":
                    $(".status-con").parent().show();
                    $(".status-dis").parent().hide();
                    $(".status-unk").parent().hide();
                    break;
                case "2":
                    $(".status-con").parent().hide();
                    $(".status-dis").parent().show();
                    $(".status-unk").parent().hide();
                    break;
                case "3":
                    $(".status-con").parent().hide();
                    $(".status-dis").parent().hide();
                    $(".status-unk").parent().show();
                    break;

                default:
                    break;
            }
        });
    });

    function add() {
        $('#modal-add').modal('show');
    }

    function edit(id) {
        var data = {};
        data.id = id;
        $.post('<?= base_url(); ?>/network/get_edit_zone', data).done(function(data) {
            var res = JSON.parse(data);
            console.log(res.data);
            $("#zoneEditID").val(id);
            $("#zoneEditDescription").val(res.data.name);
            $("#zoneEditMode").val(res.data.mode);
            $('#modal-edit').modal('show');
        });
    }

    function remove(id) {
        var data = {};
        data.id = id;
        Swal.fire({
            title: "¿Esta seguro?",
            text: "Esta a punto de eliminar una zona",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Continuar"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Por favor espere...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.post('<?= base_url(); ?>/network/remove_zone', data).done(function(data) {
                    var res = JSON.parse(data);
                    if (res.result == "success") {
                        window.location.reload();
                    } else {
                        Swal.fire({
                            title: "No se pudo eliminar",
                            text: "Hay routers asignados a esta zona.",
                            icon: "error"
                        });
                    }
                });
            }
        });

    }
</script>
<?php footer($data); ?>