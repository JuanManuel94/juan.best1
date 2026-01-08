<?php
head($data);

$LABEL_TYPES = [];

$LABEL_TYPES[1] = '<span style="color:#4da1ff;border: 1px solid #4da1ff;padding: 2px 5px;border-radius: 5px;text-transform: uppercase;font-size: 9.5px;font-weight: 700;">LIBRE</span>';
$LABEL_TYPES[2] = '<span style="color:#F59C1A;border: 1px solid #F59C1A;padding: 2px 5px;border-radius: 5px;text-transform: uppercase;font-size: 9.5px;font-weight: 700;">SERVICIOS</span>';

$months = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
?>
<!-- INICIO TITULO -->
<style>
  .bt-a {
    width: 20px;
    display: inline-block;
  }

  .panel .nav-tabs li a,
  .panel .nav-tabs li a:hover {
    color: #3D3D3D;
    text-decoration: none !important;
  }

  .panel .nav-tabs li.active a {
    color: #00AEAE;
    text-decoration: none !important;
  }

  #routerLogs {
    overflow-x: hidden;
    overflow-y: scroll;
    max-height: 400px;
  }

  #routerLogTable {
    background: black;
    color: #CCC;
  }

  #routerLogTable tr {
    padding-left: 10px;
  }

  #routerLogTable tr.log-error td {
    color: #FAA !important;
  }

  #routerLogTable td {
    white-space: nowrap;
    border: none !important;
  }

  .chart-container {
    width: 100%;
  }

  #routerGraphs.tab-pane {
    text-align: center;
  }

  #routerGraphs select {
    display: inline-block;
  }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="panel panel-default panel-routers">
  <div class="panel-heading">
    <h4 class="panel-title">Promesas de pago</h4>
    <div class="panel-heading-btn">
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-expand"><i class="fa fa-expand"></i></a>
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-iconpanel" data-click="panel-reload" onclick="refresh_table()"><i class="fas fa-sync-alt"></i></a>
    </div>
  </div>
  <div class="panel-body border-panel">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-12">
        <div class="table-responsive">
          <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed" data-order='[[ 1, "asc" ]]' style="width: 100%;">
            <thead>
              <tr>
                <th>Nº Factura</th>
                <th>Mes Fact.</th>
                <th>Cliente</th>
                <th>F.Emision</th>
                <th>F.Vencimiento</th>
                <th>Total</th>
                <th>Pendiente</th>
                <th>Tipo</th>
                <th>Método</th>
                <th>Promesa hecha</th>
                <th>Fecha a pagar</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php

              foreach ($data['records'] as $k => $r) {
                $r = (object) $r;
              ?>

                <tr>
                  <td>
                      <i class="fa fa-file"></i> <?= str_pad($r->correlative, 7, '0', STR_PAD_LEFT) ?><br>
                  </td>
                  <td><?= strtoupper($months[date('n', strtotime($r->date_issue)) - 1] . ', ' . date('Y', strtotime($r->date_issue))) ?></small></td>
                  <td><?= $r->client ?></td>
                  <td><?= $r->date_issue ?></td>
                  <td><?= $r->expiration_date ?></td>
                  <td><?= $r->total ?></td>
                  <td><?= $r->remaining_amount ?></td>
                  <td><?= $LABEL_TYPES[$r->type] ?></td>
                  <td><?= $r->sales_method == 1 ? "CONTADO" : "CREDITO" ?></td>
                  <td><?= $r->promise_set_date ?></td>
                  <td><?= $r->promise_date ?></td>
                  <td><a href="<?=base_url() ?>/bills?search=<?= str_pad($r->correlative, 7, '0', STR_PAD_LEFT) ?>&pay" class="btn btn-success text-white"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFoAAABaCAYAAAA4qEECAAAACXBIWXMAAAsTAAALEwEAmpwYAAAFhElEQVR4nO2dW4hWVRSAP6fSvzC6WGDUQ1lDlpnOlHazGqOHGgh6aIh66PISQWRFhUWXGX3oTiZB00gU9BR2UdAQCYJqym5klE45o5VElOPoVFPa6Myc2LAG5Gf2P3vvc8785+y9P1gwoP9a7jXn7HXZa/9CJBKJRCKRSMSOGcBSYCWwDugB9gOHgGH5ebv82QqgRT4TMeQiYA3wJ5BYyiDQBTSbGguRZmCzg3N1sglYWO9FFYljgdXASIZOHpfDwCqgQuCcC3yXg4Or5VugkUBZBPRPgZPHZR9wOYFxCTBk6ahpGl1jFjqUzcUEtF0MODyRWThayd4QtpEKsNXx1c/K0YnEBRWEvWW1pUN2Aw8DZ9fQeR7wJPCHpe4X8ThPHjF0gnpKn7NMy2YCr1imfgvwkM0WTrg/hZ1HLOy8j4dldWIo72Rgb53Fm9OER6yxcPQFGh0qU3gV+Bz4CngNmKf5u+db2FM6vWCGRYNop0bHaZri5iCwRPOZHkObqgs4HQ9YavF0bdTouLXGZ9Tef9IEstHC7tV4wEqLBb+t0dEkWUKSk3TgAestFvxxDT3XAp/k1OV7Dw/osVjwAeC4SfSpbaEVeBx4F/g1A0dvwwMGLBe9zMHGHMmff3F0tOp/lJ5hy0WrDtt8R1uqknzaof/xHwE6OpGU68YUNh8N0dEDKfbObuAOyaNtmGZ5crM3tGCYTFLMvCXdvCsNiowHQwuG6zNydLX8K2X4qRq7LaGldytycvSRlaGuyDHV0Y4HtFgs+LoJSukzJgmovRq711jYvQpPmkqDhgu+XaNjWY2UTZd3P2BxOu5FUwkZ0zJZtKr0dFwq7VZVpn8pgVG9ATq6DW124tkxVmIgoxk14lsN7Y35ODa2yXDxfcDsFHbmAnsMbW3AQxZatDp3SxC15SaLAulwilK/8KyyyATGU7ebgVk1dKqn/05gi6Xu5/GYigwc2jhkfO8mwwGab0KYMG2U3oKtc3TY6umfZCDHKxY7DDnqsNExJFOsQbGoDmO7lxEojY57tsueHMx2oaMiA4d5nHIrnS+UIfAdLVWWKqF/BP4B/pL+bafkug0Z2Vogs3AuWUS1jEkxMr8Ms3IvGY7C7gDukWnOLGiSMa39jvtwZ9HLatWOXC5Pq8tTNCiv6ZkZ/XumS/uyQxrz26TqGxZRP38vjagOw1OXuqGm3tvkNctqjxwVfWrgJWgaZDCwyyGXtZWtwF2+X2OoZp68Yq5DJmlkD/AMcDoBkBRAhoG1cv3NW5KCydfAbZJCekVSUPlJsp2T8YSk4DIkAVpdZys1SUlkFPgAuKHGJc5Ck5RQdgD3GcxMF4qkxNIv+3ge+fhZMurbJ1nRTpmucraVeCAfZuRsVbRdLxXsqMbWZ662Ek9keQoHq8zmIXlqc7tElHgi2x3WfjHwutyRsbHVF7KjDxiutyKzfF+ksHUwZEf3GlwcetbxpL1afgjZ0e2a4NYqt2V1wc1FHgvV0Z9WZQKz5KrFrhxsfeR6kJCUVEYkKLUf4WQ1qvCG7KFZ21M6n0pzWlNvhyUFlzFp4ao9PhX1XkhSYFHfAXJFWgdHR1Mzg2nLunll8xv+G3hZzhZnSlOnWb4S4rcCPIFpRaV+9wLHZOlgG0fvkhJX3YbS0SAn3BsyGnaZShmWr4Q7MQ8Hmzi6W16hoyx1niOHrlM5rFjXQGfCRBfN3wQuzOiKW5s07JOCyZYsA50J44Z/l67UKTnZmStPuctIV+EDnQkqhbklrwAwAccDd0/RWO6UBboyDE12yUX5Uge6snCCjIi5DlLWPdCVkSXioENlCnRlZrbk7j+XIdD5QIMUQmtrfLfdPvmlxP/YJiPmSKuyV9qW6lrHE7LHRyKRSCQSiUQikUiEGvwPJ6oNw6kqt/8AAAAASUVORK5CYII=" height="20px" width="20px" style="filter: brightness(0) invert(1);" /> Registrar pago</button></td>
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
<!-- FIN TITULO -->
<?php footer($data); ?>