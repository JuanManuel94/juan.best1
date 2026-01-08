<?php
head($data);
modal("internetModal", $data);
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
    <li class="breadcrumb-item"><a href="<?= base_url() ?>/services/internet"><?= $data['previous_page'] ?></a></li>
    <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="panel panel-default panel-internet">
    <div class="panel-heading">
        <h4 class="panel-title">Todos los planes</h4>
        <div class="panel-heading-btn">
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-expand"><i
                    class="fa fa-expand"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-reload"
                onclick="refresh_table()"><i class="fas fa-sync-alt"></i></a>
        </div>
    </div>
    <div class="panel-body border-panel">
        <div class="row">
            <div id="list-btns-tools" style="display: none;">
                <div class="options-group btn-group m-r-5">
                    <?php if ($_SESSION['permits_module']['r']) { ?>
                        <button type="button" class="btn btn-white" onclick="modal()"><i
                                class="fas fa-plus mr-1"></i>Nuevo</button>
                    <?php } ?>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-12">
                <div class="table-responsive">
                    <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed"
                        data-order='[[ 1, "asc" ]]' style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Clientes</th>
                                <th>Máx. Subida</th>
                                <th>Máx. Bajada</th>
                                <th>Detalles</th>
                                <th class="all">Estado</th>
                                <th class="all" data-orderable="false" style="max-width: 40px !important; width: 40px;">
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FIN TITULO -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function update_routers(id) {
        var data = {};
        data.id = id;
        Swal.fire({
            title: "¿Esta seguro?",
            text: "Esta a punto de refrescar la velocidad de todos los clientes de este plan.",
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
                $.post('<?= base_url(); ?>/internet/update_router_plans', data).done(function (data) {
                    if (data.success) {
                        Swal.fire({
                            title: data.message,
                            icon: "success"
                        });
                    } else {
                        Swal.fire({
                            title: data.message,
                            icon: "error"
                        });
                    }
                });
            }
        });
    }
</script>
<?php footer($data); ?>