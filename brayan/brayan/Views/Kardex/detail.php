<?php
    head($data);
    $product = $data['kardex_detail']['product'];
    $income = $data['kardex_detail']['income'];
    $input_amount = $income['input_amount'];
    $total_input = $income['total_input'];
    $total_income = $input_amount * $total_input;

    $departure = $data['kardex_detail']['departure'];
    $output_quantity = $departure['output_quantity'];
    $total_output = $departure['total_output'];
    $total_departure = $output_quantity * $total_output;

    $current_inventory = $input_amount - $output_quantity;
    $current_cost = $total_input - $total_output;
    $total_balance = $total_income - $total_departure;

    $inventary = empty($data['kardex_detail']['inventary']) ? 0 : $data['kardex_detail']['inventary'];
    $detail = $data['kardex_detail']['detail'];
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
    <li class="breadcrumb-item"><a href="javascript:window.history.back();"><?= $data['previous_page'] ?></a></li>
    <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="row">
    <div class="col-xl-4 col-md-6">
		<div class="widget widget-stats bg-info">
			<div class="stats-title text-uppercase f-w-700 text-white f-s-16">
                Entradas
            </div>
			<div class="stats-info">
                <div class="stats-icon stats-icon-lg"><i class="icon-handbag fa-fw"></i></div>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">
                        Cantidad de entradas
                        <span class="f-w-700"><?= $input_amount ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">
                        Costo de entradas
                        <span class="f-w-700"><?= $_SESSION['businessData']['symbol']." ".format_money($total_input) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">
                        Total de entradas
                        <span class="f-w-700"><?= $_SESSION['businessData']['symbol']." ".format_money($total_income) ?></span>
                    </li>
                </ul>
			</div>
		</div>
	</div>
    <div class="col-xl-4 col-md-6">
		<div class="widget widget-stats bg-danger">
			<div class="stats-title text-uppercase f-w-700 text-white f-s-16">
                Salidas
            </div>
			<div class="stats-info">
                <div class="stats-icon stats-icon-lg"><i class="icon-basket-loaded fa-fw"></i></div>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">
                        Cantidad de salidas
                        <span class="f-w-700"><?= $output_quantity ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">
                        Costo de salidas
                        <span class="f-w-700"><?= $_SESSION['businessData']['symbol']." ".format_money($total_output) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">
                        Total de salidas
                        <span class="f-w-700"><?= $_SESSION['businessData']['symbol']." ".format_money($total_departure) ?></span>
                    </li>
                </ul>
			</div>
		</div>
	</div>
    <div class="col-xl-4 col-md-6">
        <div class="widget widget-stats bg-success">
            <div class="stats-title text-uppercase f-w-700 text-white f-s-16">
                Saldos
            </div>
            <div class="stats-info">
                <div class="stats-icon stats-icon-lg"><i class="icon-social-dropbox fa-fw"></i></div>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">
                        Inventario inicial
                        <span class="f-w-700"><?= $inventary ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">
                        Cantidad de saldo
                        <span class="f-w-700"><?= $current_inventory ?></span>
                    </li>
                    <!--<li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">
                        Costo de saldo
                        <span class="f-w-700"><?= $_SESSION['businessData']['symbol']." ".format_money($current_cost) ?></span>
                    </li>-->
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">
                        Total saldo
                        <span class="f-w-700"><?= $_SESSION['businessData']['symbol']." ".format_money($total_balance) ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-12" data-sortable="false">
        <div class="panel panel-default" data-sortable="false">
            <div class="panel-heading">
                <h4 class="panel-title">Lista de movimientos</h4>
            </div>
            <div class="panel-body border-panel">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-12">
                      <div class="table-responsive">
                          <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed" data-order='[[ 1, "asc" ]]' style="width: 100%;">
                              <thead>
                                  <tr>
                                      <th class="text-center">#</th>
                                      <th class="text-center">Fecha</th>
                                      <th class="text-center">Tipo</th>
                                      <th>Descripción</th>
                                      <th class="text-center">Unidades</th>
                                      <th class="text-center">Precio</th>
                                      <th class="text-center">Total</th>
                                  </tr>
                              </thead>
                              <tbody>
                              <?php $n = 1 ?>
                              <?php for($i=0; $i < count($detail); $i++){ ?>
                                  <tr>
                                      <td class="text-center"><?= $n++ ?></td>
                                      <td class="text-center"><?= date("d/m/Y H:i",strtotime($detail[$i]['date'])) ?></td>
                                      <td class="text-center"><?= $detail[$i]['type'] ?></td>
                                      <td><?= $detail[$i]['description'] ?></td>
                                      <td class="text-center"><?= $detail[$i]['quantity'] ?></td>
                                      <td class="text-center"><?= $_SESSION['businessData']['symbol'].format_money($detail[$i]['price']) ?></td>
                                      <td class="text-center"><?= $_SESSION['businessData']['symbol'].format_money($detail[$i]['total']) ?></td>
                                  </tr>
                              <?php } ?>
                              </tbody>
                          </table>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>
<script>
    table_configuration('#list','Lista de operaciones');
    table = $('#list').DataTable({
        "idDataTables": "1"
    });
</script>
