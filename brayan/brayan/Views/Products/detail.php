<?php
    head($data);
    $product = $data['detail']['product'];
    $detail = $data['detail']['a'];
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
    <li class="breadcrumb-item"><a href="javascript:window.history.back();"><?= $data['previous_page'] ?></a></li>
    <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="card card-solid">
  <div class="card-body">
    <div class="row">
      <div class="col-12 col-sm-4">
        <h3 class="d-inline-block d-sm-none"><?= $product['product'] ?></h3>
        <div class="col-12">
          <?php
            if(!empty($product['image'])){
              if($product['image'] == "no_image.jpg"){
                  $image = base_style().'/images/default/no_image.jpg';
              }else{
                  $url = base_style().'/uploads/products/'.$product['image'];
                  if(@getimagesize($url)){
                    $image = $url;
                  }else{
                    $image = base_style().'/images/default/no_image.jpg';
                  }
              }
            }else{
              $image = base_style().'/images/default/no_image.jpg';
            }
          ?>
          <img src="<?= $image ?>" class="product-image" alt="Product Image">
        </div>
      </div>
      <div class="col-12 col-sm-8">
        <h3 class="my-3"><?= $product['product'] ?></h3>
        <p><?= $product['description'] ?></p>
        <hr>
        <div class="row">
          <div class="col-12 col-sm-6">
            <h4>Especificaciones </h4>
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <p><?= "<b>Categoria:</b> ".$product['category']."<br><b>Modelo:</b> ".$product['model']."<br><b>Marca:</b> ".$product['brand']."<br><b>Nº Serie:</b> ".$product['serial_number']."<br><b>Nº Mac:</b> ".$product['mac'] ?></p>
            </div>
          </div>
          <div class="col-12 col-sm-6">
            <h4>Información extra</h4>
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <p><?= "<b>Proveedor:</b> ".$product['provider']."<br><b>Unidad de medida:</b> ".$product['united']."<br><b>Stock:</b> ".$product['stock']."<br><b>Alterta stock:</b> ".$product['stock_alert'] ?></p>
            </div>
          </div>
        </div>

        <div class="bg-success text-white py-2 px-3 mt-4">
          <h4 class="mb-0">
            Precio venta: <?= $_SESSION['businessData']['symbol'].format_money($product['sale_price']) ?>
          </h4>
          <h4 class="mt-0">
            Precio compra: <?= $_SESSION['businessData']['symbol'].format_money($product['purchase_price']) ?>
          </h4>
        </div>
      </div>
    </div>
    <div class="mt-4">
      <style type="text/css">
          .clients-tab li a.nav-link.active, .customtab li a.nav-link.active {
              border-bottom: 2px solid #009efb;
              color: #009efb;
          }
          .clients-tab li a.nav-link.active, .customtab li a.nav-link.active {
              border-bottom: 2px solid #009efb !important;
              color: #009efb;
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
          .clients-tab li a.nav-link, .customtab li a.nav-link {
              border: 0px;
              padding: 15px 20px;
              color: #54667a;
          }
          .nav-tabs {
              background: #FFF;
          }
      </style>
      <ul class="nav nav-tabs clients-tab nav-clients" role="tablist">
        <li class="nav-item"> <a class="nav-link active show" data-toggle="tab" href="#data-latest_sales" role="tab" aria-expanded="true" aria-selected="false">Últimas movimientos</a></li>
      </ul>
      <div class="tab-content" style="padding: 10px 0">
        <div class="tab-pane active show" id="data-latest_sales" role="tabpanel" aria-expanded="true">
          <div class="row">
            <div class="col-md-12 col-sm-12 col-12">
              <div class="table-responsive">
                <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed" style="width:100%">
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
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>
<script>
    table_configuration('#list','Lista de operaciones');
    table = $('#list').DataTable({
      "idDataTables": "1"
    });
</script>
