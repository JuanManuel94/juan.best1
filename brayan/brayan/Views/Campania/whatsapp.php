<?php
head($data);
?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item"><a href="javascript:window.history.back();"><?= $data['previous_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="panel panel-default panel-runway2">
  <div class="panel-heading">
    <h4 class="panel-title"><?= $data['page_title'] ?></h4>
  </div>
  <form class="row" id="form-wsp" style="min-height: 700px; max-height: 100vh;">
    <div class="col-3 wsp_contacts">
      <div class="pl-2 pt-2 pb-2">
        <select class="form-control" id="select-state" onchange="changeState()">
          <option value="">SELECIONAR</option>
          <option value="0">TODOS</option>
          <option value="1">INSTALACION</option>
          <option value="2">ACTIVOS</option>
          <option value="3">SUSPENDIDOS</option>
          <option value="4">CANCELADOS</option>
          <option value="5">GRATIS</option>
        </select>
        <div class="mt-2 border-top" id="wsp-contactos"></div>
      </div>
    </div>
    <div class="col-9 border border-left">
      <div class="row flex-column justify-content-between h-100 w-110">
        <textarea class="col-11 wsp_content w-100"
          autofocus="on"
          id="message"></textarea>
        <div class="col-1 wsp_control w-100">
          <label class="wsp_control_file w-100 pl-2 cursor-pointer">
            <span id="file-text">Subir archivo</span>
            <input type="file" name="file" id="file" style="display: none">
          </label>
          <button id="wsp_btn" class="wsp_control_send" disabled>
            <i class="fas fa-paper-plane"></i>
          </button>
        </div>
      </div>
    </div>
  </form>
</div>
<div class="panel panel-default panel-runway2">
  <div class="panel-heading">
    <h4 class="panel-title">Variables de mensaje</h4>
  </div>
  <div class="py-2">
    <ul>
      <li>{names}: Nombres</li>
      <li>{surnames}: Apellidos</li>
      <li>{cliente}: Nombre Completo</li>
      <li>{document}: N° de Identidad</li>
      <li>{mobile}: N° Telefonico</li>
      <li>{mobiledos}: mobile opcional</li>
      <li>{note}: nota</li>
      <li>{email}: Correo</li>
      <li>{address}: Dirección</li>
      <li>{latitud}: Latitud</li>
      <li>{longitud}: Longitud</li>
      <li>{reference}: referencia</li>
      <li>{net_ip}: ip</li>
    </ul>
  </div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>