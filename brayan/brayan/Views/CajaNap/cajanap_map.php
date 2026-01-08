<?php
head($data);
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/customers"><?= $data['previous_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="panel panel-white">
  <div class="panel-body border-panel no-padding">
    <div id="customer_map" class="width-full height-lg" style="position: relative; overflow: hidden;"></div>
  </div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>