<?php head($data); ?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="row">
  <div class="col-xl-3 col-md-6">
    <div class="widget widget-stats bg-blue">
      <div class="stats-icon stats-icon-lg"><i class="ion-ios-people fa-fw"></i></div>
      <div class="stats-info">
        <h4 class="text-uppercase">Clientes</h4>
        <p><?= $data['clients'] ?></p>
      </div>
      <div class="badge badge-sm badge-danger">Retirados: <b><?= $data['canceled_clients'] ?></b></div>
      <div class="badge badge-sm badge-warning">Suspendidos <b><?= $data['suspended_clients'] ?></b></div>
      <div class="badge badge-sm badge-light">Gratis <b><?= $data['gratis_clients'] ?></b></div>
      <div class="stats-progress progress" style="height: 5px;">
        <?php
        $percent_clients = round($data['clients'] / 1000 * 100);
        if ($percent_clients <= 100) {
          $val_client = $percent_clients;
        } else {
          $val_client = 100;
        }
        ?>
        <div class="progress-bar bar-w-2" style="width: <?= $val_client ?>;"></div>
      </div>
      <div class="stats-link">
        <a href="<?= base_url() ?>/customers">Ver clientes <i class="fa fa-arrow-alt-circle-right"></i></a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="widget widget-stats bg-danger">
      <div class="stats-icon stats-icon-lg"><i class="ion-ios-document fa-fw"></i></div>
      <div class="stats-info">
        <h4 class="text-uppercase">Facturas pendientes</h4>
        <p><?= $data['unpaid_bills'] ?></p>
      </div>
      <div>Facturas vencidas <b><?= $data['expired_bills'] ?></b></div>
      <div class="stats-progress progress" style="height: 5px;">
        <?php
        $percent_bills = round($data['unpaid_bills'] / 5000 * 100);
        if ($percent_bills <= 100) {
          $val_bill = $percent_bills;
        } else {
          $val_bill = 100;
        }
        ?>
        <div class="progress-bar bar-w-2" style="width: <?= $val_bill ?>%;"></div>
      </div>
      <div class="stats-link">
        <a href="<?= base_url() ?>/bills/pendings">Ver facturas <i class="fa fa-arrow-alt-circle-right"></i></a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="widget widget-stats bg-lime">
      <div class="stats-icon stats-icon-lg"><i class="ion-ios-rocket fa-fw"></i></div>
      <div class="stats-info">
        <h4 class="text-uppercase">Cobranza del día</h4>
        <p><?= $_SESSION['businessData']['symbol'] . " " . format_money($data['payments_day']) ?></p>
      </div>
      <div>Cobranza mensual
        <b><?= $_SESSION['businessData']['symbol'] . " " . format_money($data['payments_month']) ?></b>
      </div>
      <div class="stats-progress progress" style="height: 5px;">
        <?php
        $percent_payment = round($data['payments_day'] / 3000 * 100);
        if ($percent_payment <= 100) {
          $val_pay = $percent_payment;
        } else {
          $val_pay = 100;
        }
        ?>
        <div class="progress-bar bar-w-2" style="width: <?= $val_pay ?>%;"></div>
      </div>
      <div class="stats-link">
        <a href="<?= base_url() ?>/payments">Ver cobranzas <i class="fa fa-arrow-alt-circle-right"></i></a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="widget widget-stats bg-dark">
      <div class="stats-icon stats-icon-lg"><i class="ion-ios-help-buoy fa-fw"></i></div>
      <div class="stats-info">
        <h4 class="text-uppercase">Tickets</h4>
        <p><?= $data['tickets'] ?></p>
      </div>
      <div>ticket pendientes <b><?= $data['pending_tickets'] ?></b></div>
      <div class="stats-progress progress" style="height: 5px;">
        <?php
        $percent_tickets = round($data['tickets'] / 1000 * 100);
        if ($percent_tickets <= 100) {
          $val_tickets = $percent_tickets;
        } else {
          $val_tickets = 100;
        }
        ?>
        <div class="progress-bar bar-w-2" style="width: <?= $val_tickets ?>%;"></div>
      </div>
      <div class="stats-link">
        <a href="<?= base_url() ?>/tickets">Ver tickets <i class="fa fa-arrow-alt-circle-right"></i></a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="widget widget-stats bg-orange">
      <div class="stats-icon stats-icon-lg"><i class="ion-ios-cellular fa-fw"></i></div>
      <div class="stats-info">
        <h4 class="text-uppercase">Instalaciones</h4>
        <p><?= $data['installations'] ?></p>
      </div>
      <div>Instalaciones pendientes <b><?= $data['pending_installations'] ?></b></div>
      <div class="stats-progress progress" style="height: 5px;">
        <?php
        $percent_installations = round($data['installations'] / 1000 * 100);
        if ($percent_installations <= 100) {
          $val_installation = $percent_installations;
        } else {
          $val_installation = 100;
        }
        ?>
        <div class="progress-bar bar-w-2" style="width: <?= $val_installation ?>%;"></div>
      </div>
      <div class="stats-link">
        <a href="<?= base_url() ?>/installations">Ver instalaciones <i class="fa fa-arrow-alt-circle-right"></i></a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="widget widget-stats bg-teal">
      <div class="stats-icon stats-icon-lg"><i class="ion-ios-wifi fa-fw"></i></div>
      <div class="stats-info">
        <h4 class="text-uppercase">Planes</h4>
        <p><?= $data['plans'] ?></p>
      </div>
      <div>Planes de internet <b><?= $data['internet'] ?></b></div>
      <div class="stats-progress progress" style="height: 5px;">
        <?php
        $percent_plans = round($data['plans'] / 100 * 100);
        if ($percent_plans <= 100) {
          $val_plan = $percent_plans;
        } else {
          $val_plan = 100;
        }
        ?>
        <div class="progress-bar bar-w-2" style="width: <?= $val_plan ?>%;"></div>
      </div>
      <div class="stats-link">
        <a href="<?= base_url() ?>/plans/internet">Ver planes <i class="fa fa-arrow-alt-circle-right"></i></a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="widget widget-stats bg-indigo">
      <div class="stats-icon stats-icon-lg"><i class="ion-ios-archive fa-fw"></i></div>
      <div class="stats-info">
        <h4 class="text-uppercase">Productos</h4>
        <p><?= $data['products'] ?></p>
      </div>
      <div>Productos agotados <b><?= $data['stock_products'] ?></b></div>
      <div class="stats-progress progress" style="height: 5px;">
        <?php
        $percent_products = round($data['products'] / 1000 * 100);
        if ($percent_products <= 100) {
          $val_product = $percent_products;
        } else {
          $val_product = 100;
        }
        ?>
        <div class="progress-bar bar-w-2" style="width: <?= $val_product ?>%;"></div>
      </div>
      <div class="stats-link">
        <a href="<?= base_url() ?>/products">Ver productos <i class="fa fa-arrow-alt-circle-right"></i></a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="widget widget-stats bg-pink">
      <div class="stats-icon stats-icon-lg"><i class="ion-ios-person-add fa-fw"></i></div>
      <div class="stats-info">
        <h4 class="text-uppercase">Usuarios</h4>
        <p><?= $data['users'] ?></p>
      </div>
      <div>Usuarios inactivos <b><?= $data['inactive_users'] ?></b></div>
      <div class="stats-progress progress" style="height: 5px;">
        <?php
        $percent_users = round($data['users'] / 50 * 100);
        if ($percent_users <= 100) {
          $user = $percent_users;
        } else {
          $user = 100;
        }
        ?>
        <div class="progress-bar bar-w-2" style="width: <?= $user ?>%;"></div>
      </div>
      <div class="stats-link">
        <a href="<?= base_url() ?>/users">Ver usuarios <i class="fa fa-arrow-alt-circle-right"></i></a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div id="resumen_servicios" class="panel panel-inverse" data-sortable-id="home_resumen_servicios" data-init="true">
      <div class="panel-heading">
        <h4 class="panel-title">Resumen del sistema</h4>
      </div>
      <div class="list-group">
        <a href="<?= base_url() ?>/network/routers"
          class="list-group-item list-group-item-action list-group-item-inverse d-flex justify-content-between align-items-center text-ellipsis"
          style="padding-top: 11px !important;padding-bottom: 11px !important;">
          1. Routers Activos
          <span class="badge bg-teal f-s-10 r-1" style="font-size: 12px !important"><i
              class="fa fa-refresh icon-refresh"></i></span>
        </a>

        <a href="<?= base_url() ?>/network/routers"
          class="list-group-item list-group-item-action list-group-item-inverse d-flex justify-content-between align-items-center text-ellipsis"
          style="padding-top: 11px !important;padding-bottom: 11px !important;">
          2. Routers desconectados
          <span class="badge bg-red f-s-10 r-2" style="font-size: 12px !important"><i
              class="fa fa-refresh icon-refresh"></i></span>
        </a>

        <a href="<?= base_url() ?>/customers?state=2"
          class="list-group-item list-group-item-action list-group-item-inverse d-flex justify-content-between align-items-center text-ellipsis"
          style="padding-top: 11px !important;padding-bottom: 11px !important;">
          3. Clientes Activos
          <span class="badge bg-green f-s-10 r-3" style="font-size: 12px !important"><i
              class="fa fa-refresh icon-refresh"></i></span>
        </a>

        <a href="<?= base_url() ?>/customers?state=3"
          class="list-group-item list-group-item-action list-group-item-inverse d-flex justify-content-between align-items-center text-ellipsis"
          style="padding-top: 11px !important;padding-bottom: 11px !important;">
          4. Clientes suspendidos
          <span class="badge bg-pink f-s-10 r-4" style="font-size: 12px !important"><i
              class="fa fa-refresh icon-refresh"></i></span>
        </a>

        <a href="<?= base_url() ?>/plans/internet"
          class="list-group-item list-group-item-action list-group-item-inverse d-flex justify-content-between align-items-center text-ellipsis"
          style="padding-top: 11px !important;padding-bottom: 11px !important;">
          5. Planes Activos
          <span class="badge bg-info f-s-10 r-5" style="font-size: 12px !important"><i
              class="fa fa-refresh icon-refresh"></i></span>
        </a>

      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div id="resumen_servidor" class="panel panel-inverse" data-sortable-id="home_resumen_servidor" data-init="true">
      <div class="panel-heading">
        <h4 class="panel-title">Datos del Servidor</h4>
      </div>
      <div class="list-group">

        <a href="javascript:;"
          class="list-group-item list-group-item-action list-group-item-inverse d-flex justify-content-between align-items-center text-ellipsis"
          style="padding-top: 11px !important; padding-bottom: 11px !important;">
          <span><i class="fa fa-microchip"></i> CPU Cores</span>
          <span id="cpu_cores"><?= $cpu_cores ?></span>
        </a>

        <a href="javascript:;"
          class="list-group-item list-group-item-action list-group-item-inverse d-flex justify-content-between align-items-center text-ellipsis"
          style="padding-top: 11px !important; padding-bottom: 11px !important;">
          <span><i class="fa fa-tachometer-alt"></i> Carga Promedio (1,5,15 min)</span>
          <span id="load_avg"><?= $load_avg_str ?></span>
        </a>

        <a href="javascript:;"
          class="list-group-item list-group-item-action list-group-item-inverse d-flex justify-content-between align-items-center text-ellipsis"
          style="padding-top: 11px !important; padding-bottom: 11px !important;">
          <span><i class="fa fa-microchip"></i> Uso de CPU</span>
          <div class="progress" style="width: 100px; height: 20px; margin-left: auto;">
            <div id="cpu_usage_bar" class="progress-bar bg-warning" style="width: <?= $cpu_usage ?>%;">
              <?= $cpu_usage ?>
            </div>
          </div>
          <span id="cpu_free"><?= $cpu_free ?>% libre</span>
        </a>
        <a href="javascript:;"
          class="list-group-item list-group-item-action list-group-item-inverse d-flex justify-content-between align-items-center text-ellipsis"
          style="padding-top: 11px !important; padding-bottom: 11px !important;">
          <span><i class="fa fa-memory"></i> Memoria</span>
          <div class="progress" style="width: 100px; height: 10px; margin-left: auto;">
            <div id="mem_usage_bar" class="progress-bar bg-danger" style="width: <?= $men_usage ?>%;">
            </div>
          </div>
          <span id="mem_info"><?= $men_usage ?>
            (<?= $men_usage ?>)
          </span>
        </a>

        <a href="javascript:;"
          class="list-group-item list-group-item-action list-group-item-inverse d-flex justify-content-between align-items-center text-ellipsis"
          style="padding-top: 11px !important; padding-bottom: 11px !important;">
          <span><i class="fa fa-hdd"></i> Disco</span>
          <div class="progress" style="width: 100px; height: 10px; margin-left: auto;">
            <div id="disk_usage_bar" class="progress-bar bg-info" style="width: <?= $disk_used_percentage ?>%;"></div>
          </div>
          <span id="disk_info"><?= round($disk_free, 2) ?> GB libres / <?= round($disk_total, 2) ?> GB</span>
        </a>
      </div>
    </div>
  </div>

  <script>
    function actualizarDatosServidor() {
      fetch(window.location.href, { cache: "no-store" }) // Evita cargar desde caché
        .then(response => response.text())
        .then(html => {
          let parser = new DOMParser();
          let doc = parser.parseFromString(html, 'text/html');

          // Actualizar CPU
          document.getElementById("cpu_cores").innerText = doc.getElementById("cpu_cores").innerText;
          document.getElementById("load_avg").innerText = doc.getElementById("load_avg").innerText;

          let cpuUsageBar = document.getElementById("cpu_usage_bar");
          let newCpuUsage = doc.getElementById("cpu_usage_bar").innerText.trim();

          if (newCpuUsage !== "Desconocido") {
            cpuUsageBar.style.width = newCpuUsage;
            cpuUsageBar.innerText = newCpuUsage;
            cpuUsageBar.classList.remove("d-none"); // Asegurar que no esté oculto
          } else {
            cpuUsageBar.classList.add("d-none"); // Ocultar si el valor es desconocido
          }

          document.getElementById("cpu_free").innerText = doc.getElementById("cpu_free").innerText;

          // Actualizar Memoria
          let memUsageBar = document.getElementById("mem_usage_bar");
          memUsageBar.style.width = doc.getElementById("mem_usage_bar").style.width;
          document.getElementById("mem_info").innerText = doc.getElementById("mem_info").innerText;

          // Actualizar Disco
          let diskUsageBar = document.getElementById("disk_usage_bar");
          diskUsageBar.style.width = doc.getElementById("disk_usage_bar").style.width;
          document.getElementById("disk_info").innerText = doc.getElementById("disk_info").innerText;
        })
        .catch(error => console.error("Error al actualizar datos:", error));
    }

    // Ejecutar la actualización cada 2 segundos (2000 ms)
    setInterval(actualizarDatosServidor, 2000);
  </script>

  <div class="col-xl-12">
    <div class="panel panel-default" data-sortable="false">
      <div class="panel-heading">
        <h4 class="panel-title">Transacciones de <span id="trans-month"><?= $data['monthly_payments']['month'] ?></span>
          del <span id="trans-year"><?= $data['monthly_payments']['year'] ?></span><br>
          <small class="f-s-12">Total de pagos <span
              id="trans-total"><?= $_SESSION['businessData']['symbol'] . format_money($data['monthly_payments']['total']) ?></span></small>
        </h4>
        <div class="panel-heading-btn">
          <div class="input-group">
            <input type="text" class="form-control" style="width:80px" id="search-trans" placeholder="mes-aÃ±o">
            <div class="input-group-append">
              <span class="input-group-text">
                <i class="fa fa-search"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body border-panel">
        <div class="row">
          <div class="col-md-12 col-sm-12 col-12">
            <canvas id="payments_month" style="height:350px; width:100%"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-12">
    <div class="panel panel-inverse" data-sortable-id="home_conectados" data-init="true">
      <div class="panel-heading">
        <h4 class="panel-title">Últimos conectados</h4>
      </div>
      <div class="panel-body text-center">
        <h5><b>TOTAL CONECTADOS PPPOE: <span id="total_conectados">0</span></b></h5>
      </div>
      <ul class="registered-users-list clearfix" id="home_ultimos_conectados"></ul>
      <div id="home_ultimos_conectados_no_data" class="panel-footer text-center">
        <b>Cargando...</b>
      </div>
      <div class="panel-footer text-center">
        <button id="btn_ver_todos" class="btn btn-primary" style="display: none;">Ver todos</button>
      </div>
    </div>
  </div>


  <div class="col-xl-12">
    <div class="widget widget-rounded">
      <div class="widget-header">
        <h4 class="widget-header-title">Top 6 tipo de pago de <span
            id="type-month"><?= $data['payments_type']['month'] ?></span> del <span
            id="type-year"><?= $data['payments_type']['year'] ?></span></h4>
      </div>
      <div class="vertical-box with-grid with-border-top">
        <div class="vertical-box-column widget-chart-content">
          <canvas id="payments_type" style="width:100%"></canvas>
        </div>
        <div class="vertical-box-column p-15" style="width: 30%;">
          <div class="widget-chart-info">
            <h4 class="widget-chart-info-title">Pagos Totales</h4>
            <p class="widget-chart-info-desc">Pagos registrados durante el mes.</p>
            <div id="transaction_summary"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-12">
    <div class="panel panel-default" data-sortable="false">
      <div class="panel-heading">
        <h4 class="panel-title">Facturas emitidas del <span id="serv-year"></span></h4>
        <div class="panel-heading-btn">
          <div class="input-group">
            <input type="text" class="form-control" style="width:80px" id="search-year" placeholder="aÃ±o">
            <div class="input-group-append">
              <span class="input-group-text">
                <i class="fa fa-search"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body border-panel">
        <div class="row">
          <div class="col-md-12 col-sm-12 col-12">
            <canvas id="libre_services" style="height:350px; width:100%"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-5">
    <div class="card border-0 bg-dark text-white mb-3">
      <div class="card-body">
        <div class="mb-3 text-grey">
          <b>
            <font style="vertical-align: inherit;">
              <font style="vertical-align: inherit;">TOP 10 PRODUCTOS MAS VENDIDOS</font>
            </font>
          </b>
        </div>
        <?php
        if (!empty($data['top_products'])) {
          foreach ($data['top_products'] as $product) {
            ?>
            <div class="d-flex align-items-center m-b-15">
              <?php
              if (!empty($product['image'])) {
                if ($product['image'] == "no_image.jpg") {
                  $image = base_style() . '/images/default/no_image.jpg';
                } else {
                  $url = base_style() . '/uploads/products/' . $product['image'];
                  if (@getimagesize($url)) {
                    $image = $url;
                  } else {
                    $image = base_style() . '/images/default/no_image.jpg';
                  }
                }
              } else {
                $image = base_style() . '/images/default/no_image.jpg';
              }
              ?>
              <div class="widget-img rounded-lg width-30 m-r-10 bg-white p-3">
                <div class="h-100 w-100"
                  style="background: url(<?= $image ?>) center no-repeat; background-size: auto 100%;"></div>
              </div>
              <div class="text-truncate">
                <div>
                  <font style="vertical-align: inherit;">
                    <font style="vertical-align: inherit;"><?= $product['product'] ?></font>
                  </font>
                </div>
                <div class="text-grey">
                  <font style="vertical-align: inherit;">
                    <font style="vertical-align: inherit;">
                      <?= $_SESSION['businessData']['symbol'] . format_money($product['total']) ?>
                    </font>
                  </font>
                </div>
              </div>
              <div class="ml-auto text-center">
                <div class="f-s-13">
                  <span data-animation="number" data-value="<?= $product['quantity'] ?>">
                    <font style="vertical-align: inherit;">
                      <font style="vertical-align: inherit;"><?= $product['quantity'] ?></font>
                    </font>
                  </span>
                </div>
                <div class="text-grey f-s-10">
                  <font style="vertical-align: inherit;">
                    <font style="vertical-align: inherit;">vendido</font>
                  </font>
                </div>
              </div>
            </div>
          <?php }
        } else {
          ?>
          <div class="d-flex align-items-center m-b-15">
            <div class="text-truncate">
              <div>
                <font style="vertical-align: inherit;">
                  <font style="vertical-align: inherit;">No hay registros</font>
                </font>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
  <div class="col-xl-7">
    <ul class="nav nav-tabs nav-tabs-inverse nav-justified nav-justified-mobile" data-sortable-id="index-2">
      <li class="nav-item"><a href="#last-payments" data-toggle="tab" class="nav-link active"><i
            class="icon-wallet fa-lg m-r-5"></i> <span class="d-none d-md-inline">Ultimos pagos</span></a></li>
      <li class="nav-item"><a href="#products-sellout" data-toggle="tab" class="nav-link"><i
            class="icon-basket fa-lg m-r-5"></i> <span class="d-none d-md-inline">Productos por agotarse</span></a></li>
    </ul>
    <div class="tab-content" data-sortable-id="index-3">
      <div class="tab-pane fade active show" id="last-payments">
        <div class="table-responsive m-b-0">
          <table class="table m-b-0 " style="font-size: 11px;">
            <thead>
              <tr>
                <th>Cliente</th>
                <th class="text-center" style="width: 80px">Cobrado</th>
                <th class="text-center" style="width: 100px">Usuario</th>
                <th style="width: 120px">Tiempo</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (!empty($data['last_payments'])) {
                foreach ($data['last_payments'] as $payments) {
                  ?>
                  <tr>
                    <td class="text-ellipsis">
                      <a href="<?= base_url() ?>/customers/view_client/<?= encrypt($payments['id']) ?>"
                        style="color:#333"><?= $payments['names'] . " " . $payments['surnames'] ?></a>
                    </td>
                    <td class="text-center">
                      <?= $_SESSION['businessData']['symbol'] . format_money($payments['amount_paid']) ?>
                    </td>
                    <td class="text-center"><?= $payments['username'] ?></td>
                    <td><?= time_elapsed($payments['payment_date']) ?></td>
                  </tr>
                <?php }
              } else {
                ?>
                <tr>
                  <td class="text-center" colspan="4">No hay registros</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="tab-pane fade" id="products-sellout">
        <div class="table-responsive m-b-0">
          <table class="table m-b-0 " style="font-size: 11px;">
            <thead>
              <tr>
                <th class="text-center" style="width: 80px">Codigo</th>
                <th>Producto</th>
                <th class="text-center" style="width: 100px">Stock</th>
                <th class="text-center" style="width: 100px">Precio</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (!empty($data['products_sellout'])) {
                foreach ($data['products_sellout'] as $sellout) {
                  ?>
                  <tr>
                    <td class="text-center"><?= $sellout['internal_code'] ?></td>
                    <td class="text-ellipsis">
                      <a href="<?= base_url() ?>/products/detail/<?= encrypt($sellout['id']) ?>"
                        style="color:#333"><?= $sellout['product'] ?></a>
                    </td>
                    <td class="text-center text-danger f-w-600 f-s-12"><?= $sellout['stock'] ?></td>
                    <td class="text-center">
                      <?= $_SESSION['businessData']['symbol'] . format_money($sellout['sale_price']) ?>
                    </td>
                  </tr>
                <?php }
              } else {
                ?>
                <tr>
                  <td id="acNoRecords" class="text-center" colspan="4">No hay registros</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
  $.post('<?= base_url(); ?>/dashboard/count_widget').done(function (data) {
    var res = JSON.parse(data);
    if (res.result == "success") {
      $($("#resumen_servicios .list-group a span")[0]).text(res.data.routers_connected);
      $($("#resumen_servicios .list-group a span")[1]).text(res.data.routers_disconnected);
      $($("#resumen_servicios .list-group a span")[2]).text(res.data.customers_active);
      $($("#resumen_servicios .list-group a span")[3]).text(res.data.customers_suspended);
      $($("#resumen_servicios .list-group a span")[4]).text(res.data.services_active);
      $($("#resumen_servicios .list-group a span")[5]).text(res.data.monitors_active);
      $($("#resumen_servicios .list-group a span")[6]).text(res.data.monitors_down);
    } else {

      $($("#resumen_servicios .list-group a span")[0]).text("-");
      $($("#resumen_servicios .list-group a span")[1]).text("-");
      $($("#resumen_servicios .list-group a span")[2]).text("-");
      $($("#resumen_servicios .list-group a span")[3]).text("-");
      $($("#resumen_servicios .list-group a span")[4]).text("-");
      $($("#resumen_servicios .list-group a span")[5]).text("-");
      $($("#resumen_servicios .list-group a span")[6]).text("-");
    }
  });
  function cargarUltimosConectados(limit) {
    $.post('<?= base_url(); ?>/dashboard/customers_connected_widget', { limit: limit })
      .done(function (data) {
        var res = JSON.parse(data);
        if (res.result == "success") {
          if (res.html !== "") {
            $("#home_ultimos_conectados_no_data").hide();
            $("#home_ultimos_conectados").html(res.html);
            $("#total_conectados").text(res.total); // Actualizar el total de conectados

            // Mostrar el botón "Ver todos" si el límite es 8
            if (limit === 8) {
              $("#btn_ver_todos").show();
            } else {
              $("#btn_ver_todos").hide();
            }
          } else {
            $("#home_ultimos_conectados_no_data").text("Sin datos...");
          }
        }
      });
  }

  // Cargar los primeros 8 conectados al cargar la página
  cargarUltimosConectados(8);

  // Al hacer clic en "Ver todos", cargar todos los conectados
  $("#btn_ver_todos").click(function () {
    cargarUltimosConectados(1000); // Aquí puedes poner un número alto como 1000 para cargar todos
  });


</script>
<!-- FIN TITULO -->
<?php footer($data); ?>