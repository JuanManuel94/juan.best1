<?php
head($data);
modal("paymentsModal", $data);
?>
<div class="panel panel-default">
  <div class="panel-heading">
    <h4 class="panel-title">Registrar pago</h4>
    <div class="panel-heading-btn">
      <a href="javascript:window.history.back();" class="btn btn-xs btn-icon btn-circle btn-iconpanel"><i
          class="fas fa-reply"></i></a>
    </div>
  </div>
  <div class="panel-body border-panel">
    <div class="row">
      <div class="col-xl-4 search_payment">
        <label class="label_payment">BUSCAR CLIENTE</label>
      </div>
      <div class="col-xl-5 m-t-10 m-b-10">
        <div class="search-input">
          <input type="text" id="search_client" placeholder="NOMBRE O DNI/RUC">
          <div id="box-search" class="autocom-box"></div>
          <div class="icon"><i class="fas fa-search"></i></div>
        </div>
      </div>
      <div class="col-xl-12 m-t-10" id="pending_invoices" style="display: none;" data-sortable="false"></div>
    </div>
  </div>
</div>
<style>
  .bg-debt {
    background-color: #f8d7da;
    color: #721c24;
  }
</style>
<!-- FIN TITULO -->
<script src="<?= base_style() ?>/js/form/methodPaymentForm.js?v=<?= time(); ?>"></script>
<?php footer($data); ?>