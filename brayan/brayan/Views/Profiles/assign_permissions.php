<?php
  head($data);
	$permissions = $data['permissions'];
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
    <li class="breadcrumb-item"><a href="javascript:window.history.back();"><?= $data['previous_page'] ?></a></li>
    <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="row">
    <div class="col-lg-8">
        <form autocomplete="off" name="transactions" id="transactions">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Permisos</h4>
                    <div class="panel-heading-btn">
                        <a href="javascript:window.history.back();" class="btn btn-xs btn-icon btn-circle btn-iconpanel"><i class="fas fa-reply"></i></a>
                    </div>
                </div>
                <div class="panel-body border-panel">
                    <input type="hidden" id="idprofile" name="idprofile" value="<?= $permissions['idprofile']; ?>" required>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-12">
                            <div class="container-options">
                                <div class="options-group btn-group m-0">
                                    <button type="submit" class="btn btn-white"><i class="fas fa-save mr-2"></i>Guardar Cambios</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-12">
                            <div class="table-responsive">
                                <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th class="title-table">Modulo</th>
                                            <th class="title-table text-center">Agregar</th>
                                            <th class="title-table text-center">Modificar</th>
                                            <th class="title-table text-center">Eliminar</th>
                                            <th class="title-table text-center">Visualizar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
    			                        $modules = $permissions['modules'];
    			                        for ($i=0; $i < count($modules); $i++) {
    			                            $permissions = $modules[$i]['permissions'];
    			                            $rCheck = $permissions['r'] == 1 ? " checked " : "";
    			                            $aCheck = $permissions['a'] == 1 ? " checked " : "";
    			                            $eCheck = $permissions['e'] == 1 ? " checked " : "";
    			                            $vCheck = $permissions['v'] == 1 ? " checked " : "";
    			                            $idmodule = $modules[$i]['id'];
    			                    ?>
    									<tr>
    										<td class="text-uppercase">
                                                <?= $modules[$i]['module']; ?>
                                                <input type="hidden" name="modules[<?= $i; ?>][idmodule]" value="<?= $idmodule ?>" required>
                                            </td>
    										<td class="text-center">
                                                <input type="checkbox" class="checkbox-select" name="modules[<?= $i; ?>][r]" <?= $rCheck ?>>
                                            </td>
    										<td class="text-center">
                                                <input type="checkbox" class="checkbox-select" name="modules[<?= $i; ?>][a]" <?= $aCheck ?>>
                                            </td>
    										<td class="text-center">
                                                <input type="checkbox" class="checkbox-select" name="modules[<?= $i; ?>][e]" <?= $eCheck ?>>
                                            </td>
    										<td class="text-center">
                                                <input type="checkbox" class="checkbox-select" name="modules[<?= $i; ?>][v]" <?= $vCheck ?>>
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
        </form>
    </div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>
<script type="text/javascript">
    let table;
    table = $('#list').DataTable({
        //"select": true,
        "aProcessing":true,
        "aServerSide":true,
        'buttons': [],
        "responsive": true,
        "ordering": false,
        "searching": false,
        "paging": false,
        "info": false,
        "bDestroy": true,
        "iDisplayLength": concurrence,
        "order":[[0,"desc"]]
    });
</script>
