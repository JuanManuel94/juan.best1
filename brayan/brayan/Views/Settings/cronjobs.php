<?php

head($data);

function timeAgo($timestamp)
{
    // Convert the timestamp to a DateTime object
    $date = new DateTime("@$timestamp");
    $now = new DateTime();

    // Calculate the difference
    $interval = $now->diff($date);

    // Define the time intervals in Spanish
    if ($interval->y >= 1) {
        return 'Hace ' . $interval->format('%y año%') . ($interval->y > 1 ? 's' : '');
    }
    if ($interval->m >= 1) {
        return 'Hace ' . $interval->format('%m mes%') . ($interval->m > 1 ? 'es' : '');
    }
    if ($interval->d >= 1) {
        if ($interval->d == 1) {
            return 'Hace ayer';
        }
        return 'Hace ' . $interval->format('%d día%') . ($interval->d > 1 ? 's' : '');
    }
    if ($interval->h >= 1) {
        return 'Hace ' . $interval->format('%h hora%') . ($interval->h > 1 ? 's' : '');
    }
    if ($interval->i >= 1) {
        return 'Hace ' . $interval->format('%i minuto%') . ($interval->i > 1 ? 's' : '');
    }
    if ($interval->s >= 0) {
        return 'Hace unos segundos';
    }
}

?>
<style>
    .panel .nav-tabs li a,
    .panel .nav-tabs li a:hover {
        color: #3D3D3D;
        text-decoration: none !important;
    }

    .panel .nav-tabs li.active a {
        color: #00AEAE;
        text-decoration: none !important;
    }
</style>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
    <li class="breadcrumb-item"><a href="javascript:window.history.back();"><?= $data['previous_page'] ?></a></li>
    <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>

<?php
// Example timestamp variable (replace this with your actual timestamp)
$lastServiceCallTimestamp = $data['core']->lastrun; // or any timestamp value you have

// Get the current timestamp
$currentTimestamp = time();

// Check if the timestamp is 0 or older than 5 minutes
if ($lastServiceCallTimestamp == 0 || ($currentTimestamp - $lastServiceCallTimestamp) > 300) {
    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>¡Alerta!</strong> Detectamos que la tarea principal no esta siendo ejecutada. Favor agregar la siguiente tarea programada (CronJob) al panel de control:<br><br>
                * * * * * wget -q -O - https://tudominio.com/cronjob/run >/dev/null 2>&1
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
              </div>';
} else {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                La tarea principal se esta ejecutando correctamente.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
              </div>';
}
?>

<div class="panel panel-default panel-database">
    <div class="panel-heading">
        <h4 class="panel-title">Todas las tareas (Cronjobs)</h4>
        <div class="panel-heading-btn">
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-reload" onclick="refresh_table()"><i class="fas fa-sync-alt"></i></a>
        </div>
    </div>
    <div class="panel-body border-panel">
        <div class="row">
            <div id="list-btns-tools" style="display: none;">
                <div class="options-group btn-group m-r-5">
                    <?php if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) { ?>
                        <?php if ($_SESSION['permits_module']['r']) { ?>
                            <!--<button type="button" class="btn btn-white" onclick="create_backup();"><i class="fas fa-bolt mr-1"></i>Crear copia</button>-->
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-12">
                <div class="table-responsive">
                    <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed" data-order='[[ 1, "asc" ]]' style="width: 100%;">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Descripción</th>
                                <th>Frecuencia</th>
                                <th>Parámetro</th>
                                <th>Último resultado</th>
                                <th>Última ejecución</th>
                                <th>Status</th>
                                <th class="all" data-orderable="false" style="max-width: 40px !important; width: 40px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($data['records'] as $k => $r) {

                            ?>
                                <tr>
                                    <td><?= $r->id ?></td>
                                    <td><?= $r->description ?></td>
                                    <td>Cada <?= $data['frequencies'][$r->frequency] ?></td>
                                    <td><?= !empty($r->parmx) ? str_replace("%x%", $r->parm, $r->parmx) . '  <i data-toggle="tooltip" data-original-title="' . $r->parmdesc . '" class="fa fa-info-circle"></i>' : "" ?></td>
                                    <td><?= !empty($r->lastresult) ? $r->lastresult : '-' ?></td>
                                    <td><?= ($r->lastrun != 0) ? timeAgo($r->lastrun) : '-' ?></td>
                                    <td><span class="label label-<?= $r->status == 1 ? 'success' : 'secondary' ?>"><?= $r->status == 1 ? 'Activado' : 'Desactivado' ?></span></td>
                                    <td>
                                        <a href="javascript:;" class="purple run-cronjob" data-toggle="tooltip" data-original-title="Ejecutar ahora" data-id="<?= $r->id ?>"><i class="fa fa-play"></i></a>
                                        <a href="javascript:;" class="<?= $r->status == 1 ? 'red stop-cronjob' : 'green play-cronjob' ?>" data-id="<?= $r->id ?>" data-toggle="tooltip" data-original-title="<?= $r->status == 1 ? 'Desactivar' : 'Activar' ?>" onclick=""><i class="fa fa-<?= $r->status == 1 ? 'power-off' : 'play-circle' ?>"></i></a>
                                        <a href="javascript:;" class="black cronjob-modal-1" data-toggle="tooltip" data-original-title="Histórico" data-id="<?= $r->id ?>"><i class="fa fa-list"></i></a>
                                        <a href="javascript:;" class="black cronjob-modal-2" data-toggle="tooltip" data-original-title="Cambiar frecuencia" data-id="<?= $r->id ?>" data-frequency="<?= $r->frequency ?>"><i class="fa fa-clock"></i></a>
                                        <a href="javascript:;" class="black cronjob-modal-3" data-toggle="tooltip" data-original-title="Ajustar parámetro" data-id="<?= $r->id ?>"><i class="fa fa-cogs"></i></a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modal-info" class="modal fade p-0" role="dialog" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-uppercase" id="text-ticket">Histórico de la tarea</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="panel panel-default">
                    <div class="panel-heading panel-heading-nav">
                        <ul class="nav nav-tabs">
                            <li role="presentation" class="active">
                                <a href="#cronHistory" aria-controls="cronHistory" role="tab" data-toggle="tab">Histórico</a>
                            </li>
                            <li role="presentation">
                                <a href="#cronFreq" aria-controls="cronFreq" role="tab" data-toggle="tab">Frecuencia</a>
                            </li>
                            <li role="presentation">
                                <a href="#cronParms" aria-controls="cronParms" role="tab" data-toggle="tab">Parámetros</a>
                            </li>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade in active" id="cronHistory">
                                <table id="cronjobHistoric" class="table">
                                    <thead>
                                        <tr>
                                            <th>
                                                Descripción
                                            </th>
                                            <th>
                                                Fecha
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="cronFreq">
                                <label for="frequency">Cambiar frecuencia de la tarea</label>
                                <div class="row">
                                    <div class="col-5">
                                        <select class="form-control" id="frequency" style="width: 100%">
                                            <option value="" selected disabled>Seleccione frecuencia</option>

                                            <?php foreach ($data['frequencies'] as $k => $v) { ?>
                                                <option value="<?= $k ?>"><?= $v ?></option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                    <div class="col-7">
                                        <a href="#!" class="btn btn-primary save-frequency" style="text-align: left;">Guardar</a>
                                    </div>
                                </div>

                                <br>

                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="cronParms">
                                <label for="frequency" style="font-weight: bold;font-size: 14px;">Ajustar parámetro de la tarea</label>
                                <p class="description"></p>
                                <div class="row">
                                    <div class="col-5">
                                        <input id="parm_value" type="text" class="form-control" placeholder="" maxlength="60">
                                    </div>
                                    <div class="col-7">
                                        <a href="#!" class="btn btn-primary save-parm" style="text-align: left;">Guardar</a>
                                    </div>
                                </div>

                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.5/sweetalert2.all.min.js" integrity="sha512-m4zOGknNg3h+mK09EizkXi9Nf7B3zwsN9ow+YkYIPZoA6iX2vSzLezg4FnW0Q6Z1CPaJdwgUFQ3WSAUC4E/5Hg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    var record_id = 0;

    $(".panel .nav-tabs li a").click(function() {
        $(".panel .nav-tabs li.active").removeClass("active");
        $(this).parent().addClass("active");
    });
    $(".cronjob-modal-1").click(function() {
        $('#modal-info').modal('show');

        record_id = $(this).data("id");

        var data = {};

        data.id = record_id;

        $("a[href='#cronHistory']").click();
        $("#cronjobHistoric tbody").html('<tr><td>Cargando...</td></tr>');

        $.post('<?= base_url(); ?>/settings/cronjob_history', data).done(function(data) {
            var res = JSON.parse(data);
            if (res.result == "success") {
                $("#cronjobHistoric tbody").html(res.html);
            }
        });

        $(".panel .nav-tabs li.active").removeClass("active");
        $("a[href='#cronHistory']").parent().addClass("active");
    });
    $(".cronjob-modal-2").click(function() {
        $('#modal-info').modal('show');

        record_id = $(this).data("id");

        $("#frequency").val($(this).data("frequency"));
        $("a[href='#cronFreq']").click();

        $(".panel .nav-tabs li.active").removeClass("active");
        $("a[href='#cronFreq']").parent().addClass("active");
    });
    $(".cronjob-modal-3").click(function() {
        $('#modal-info').modal('show');

        record_id = $(this).data("id");

        var data = {};

        data.id = record_id;

        $("#cronParms label").text("Cargando...");
        $("#cronParms .row").hide();

        $.post('<?= base_url(); ?>/settings/cronjob_get', data).done(function(data) {
            var res = JSON.parse(data);
            if (res.result == "success") {
                if (res.data.id == null || res.data.id == undefined || res.data.parmdesc == "") {
                    $("#cronParms label").text("Sin parámetros para esta taréa");
                } else {
                    $("#cronParms .row").show();
                    $("#cronParms label").text("Ajustar parámetro para esta taréa");
                    $("#cronParms p").text(res.data.parmdesc);
                    $("#cronParms #parm_value").attr("placeholder", res.data.parmdesc);
                    $("#cronParms #parm_value").val(res.data.parm);
                }

            }
        });

        $("a[href='#cronParms']").click();

        $(".panel .nav-tabs li.active").removeClass("active");
        $("a[href='#cronParms']").parent().addClass("active");
    });
    $(".save-frequency").click(function() {
        var data = {};

        data.id = record_id;
        data.frequency = $("#frequency").val();

        $.post('<?= base_url(); ?>/settings/cronjob_save_frequency', data).done(function(data) {
            var res = JSON.parse(data);
            if (res.result == "success") {
                window.location.reload();
            }
        });
    });
    $(".save-parm").click(function() {
        var data = {};

        data.id = record_id;
        data.parm = $("#parm_value").val();

        $.post('<?= base_url(); ?>/settings/cronjob_save_parm', data).done(function(data) {
            var res = JSON.parse(data);
            if (res.result == "success") {
                window.location.reload();
            }
        });
    });

    $(".play-cronjob").click(function() {
        var id = $(this).data("id");
        Swal.fire({
            title: "¿Esta seguro?",
            text: "Esta a punto de habilitar esta tarea",
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
                var data = {};

                data.id = id;
                data.status = 1;

                $.post('<?= base_url(); ?>/settings/cronjob_control', data).done(function(data) {
                    var res = JSON.parse(data);
                    if (res.result == "success") {
                        window.location.reload();
                    }
                });
            }
        });
    });
    $(".stop-cronjob").click(function() {
        var id = $(this).data("id");
        Swal.fire({
            title: "¿Esta seguro?",
            text: "Esta a punto de deshabilitar esta tarea",
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
                var data = {};

                data.id = id;
                data.status = 0;

                $.post('<?= base_url(); ?>/settings/cronjob_control', data).done(function(data) {
                    var res = JSON.parse(data);
                    if (res.result == "success") {
                        window.location.reload();
                    }
                });
            }
        });
    });
    $(".run-cronjob").click(function() {
        var id = $(this).data("id");
        Swal.fire({
            title: "¿Esta seguro?",
            text: "Esta a punto de ejecutar esta tarea",
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
                var data = {};

                data.id = id;
                data.status = 0;

                $.post('<?= base_url(); ?>/settings/cronjob_testrun', data).done(function(data) {
                    var res = JSON.parse(data);
                    if (res.result == "success") {
                        window.location.reload();
                    }
                });
            }
        });
    });
</script>
<!-- FIN TITULO -->
<?php footer($data); ?>