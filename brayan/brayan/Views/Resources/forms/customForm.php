<?php 
  $client = $data['client'];
?>

<div class="row">
  <pre style="display: none;" id="dataClient"><?= isset($client) ? json_encode($client) : "{}" ?></pre>
  <input type="hidden" id="idclients" name="idclient" value="<?= isset($client['id']) ? encrypt($client['id']) : null?>">
  <div class="col-xl-8 offset-xl-2">
    <div class="form-group row">
      <label class="col-md-3 text-lg-right col-form-label">Tipo doc.</label>
      <div class="col-md-7">
        <input type="hidden" id="documentData">
        <input type="hidden" id="currentDocumentId" value="<?= isset($client) ? $client['documentid'] : "" ?>">
        <select class="form-control" name="listTypes" id="listTypes" style="width:100%;"></select>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 text-lg-right col-form-label">Número doc. <span class="text-danger">*</span></label>
      <div class="col-md-7">
        <div class="input-group">
          <input type="text" 
            class="form-control" 
            name="document" 
            id="document" 
            onkeypress="return numbers(event)" 
            placeholder="99999999"
            data-parsley-group="step-1" 
            value="<?= isset($client) ? $client['document'] : null ?>">
          <div class="input-group-append">
            <button type="button" class="btn btn-white btn-search" onclick="search_document();">
              <i class="fa fa-search"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 text-lg-right col-form-label">Nombres <span class="text-danger">*</span></label>
      <div class="col-md-9">
        <input type="text" 
          class="form-control text-uppercase"
          name="names" 
          id="names" 
          onkeypress="return letters(event)" 
          placeholder="INGRESE NOMBRE" 
          value="<?= isset($client) ? $client['names'] : null ?>" 
          data-parsley-group="step-1" 
          data-parsley-required="true"
        >
      </div>
    </div>

    <div class="form-group row">
      <label class="col-md-3 text-lg-right col-form-label">Apellidos <span class="text-danger">*</span></label>
      <div class="col-md-9">
        <input type="text" 
          class="form-control text-uppercase" 
          name="surnames" 
          id="surnames" 
          onkeypress="return letters(event)" 
          placeholder="INGRESE APELLIDOS" 
          value="<?= isset($client) ? $client['surnames'] : null ?>" 
          data-parsley-group="step-1" 
          data-parsley-required="true"
        >
      </div>
    </div>

    <div class="form-group row">
      <label class="col-md-3 text-lg-right col-form-label">Celular <span class="text-danger">*</span></label>
      <div class="col-md-9">
        <input type="text" 
          class="form-control m-b-10" 
          name="mobile" 
          id="mobile" 
          onkeypress="return numbers(event)" 
          placeholder="999999999" 
          maxlength="20" 
          value="<?= isset($client) ? $client['mobile'] : null ?>" 
          data-parsley-group="step-1" 
          data-parsley-required="true"
        >   
      </div>
    </div>
    
   <div class="form-group row">
      <label class="col-md-3 text-lg-right col-form-label">RouterOS <span class="text-danger">*</span></label>
      <div class="col-md-8"> 
        <input type="hidden" id="mikrotik_default" value="<?= $client['mikrotik'] ?>">
        <select value="<?= isset($client) ? $client['mikrotik'] : null ?>"  class="form-control text-center" name="mikrotik" id="mikrotik-select">
        </select>
      </div>
    </div>

<div class="form-group row">
  <label class="col-md-3 text-lg-right col-form-label">Zona <span class="text-danger">*</span></label>
  <div class="col-md-8">
    <select class="form-control text-center" name="zona" id="zona">
      <option value="" class="text-center" <?= empty($client['zona']) ? 'selected' : '' ?>>Seleccionar zona</option>
      <?php
        $conexion = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $query = "SELECT * FROM zonas WHERE state = 1";
        $resultados = mysqli_query($conexion, $query);

        while ($fila = mysqli_fetch_assoc($resultados)) {
          $selected = ($fila['nombre_zona'] == $client['zona']) ? 'selected' : '';
          echo '<option value="' . $fila['nombre_zona'] . '" ' . $selected . ' class="text-center">' . $fila['nombre_zona'] . '</option>';
        }

        mysqli_free_result($resultados);
        mysqli_close($conexion);
      ?>
    </select>
  </div>
</div>

    
    

    <div class="form-group row">
      <label class="col-md-3 text-lg-right col-form-label">Opciones <span class="text-danger">*</span></label>
      <div class="col-md-6">
        <select class="form-control text-center" onchange="changeOpcion()" name="opcion" id="opcion" style="width:100%;">
            <option <?= isset($client) && $client['opcion'] === "NINGUNO" ? "selected" : "" ?> value="NINGUNO">NINGUNO</option>
            <option <?= isset($client) && $client['opcion'] === "WISP" ? "selected" : "" ?> value="WISP">Simple Queues</option>
            <option <?= isset($client) && $client['opcion'] === "ISP" ? "selected" : "" ?> value="ISP">ISP</option>
            <option <?= isset($client) && $client['opcion'] === "PPOE" ? "selected" : "" ?> value="PPOE">PPOE</option>
        </select>
      </div>
    </div>

    <div class="form-group row" id="content-is_ip">
      <div class="col-md-3"></div>
      <div class="col-md-6">
        Agregar dirección IP al cliente
        <input type="checkbox" 
          class="m-b-10" 
          id="is_ip" 
          <?= isset($client) && $client['mobile_optional'] ? 'checked' : '' ?>
          data-parsley-group="step-1"
          onchange="onCheckIp()"
        />   
      </div>
    </div>

    <div class="form-group row" id="content-is_firewall">
      <label class="col-md-3 text-lg-right col-form-label">
        Corte Firewall
      </label>
      <div class="col-md-6">
        <input type="checkbox" 
          class="m-b-10 mt-2" 
          name="corte_firewall"
          id="corte_firewall" 
          <?= isset($client) && $client['corte_firewall'] ? 'checked' : '' ?>
          data-parsley-group="step-1"
        />  
      </div>
    </div>

    <div class="form-group row" id="content-ip">
      <label class="col-md-3 text-lg-right col-form-label">Dirección IP <span class="text-danger" id="label-ip">*</span></label>
      <div class="col-md-6">
        <div class="search-input" id="container-search-ip">
          <input id="mobileOpCurrent" type="hidden" value="<?= isset($client) ? $client['mobile_optional'] : null ?>"/>
          <input 
            type="text" 
            id="mobileOp" 
            name="mobileOp"
            placeholder="BUSCAR IP DISPONIBLE" 
            onkeyup="search_ip()"
            value="<?= isset($client) ? $client['mobile_optional'] : "" ?>"
          >
          
          <ul id="box-search-ip" class="autocom-box"></ul>
          <div class="icon"><i class="fas fa-search"></i></div>
        </div>
      </div>
    </div>

    <div class="form-group row" id="content-ap_cliente_id">
      <label class="col-md-3 text-lg-right col-form-label">AP Cliente <span class="text-danger" id="label-ap_cliente_id">*</span></label>
      <div class="col-md-6">
        <div class="search-input" id="container-search-apcliente">
          <?php
            $apClienteId = isset($client['ap_cliente_id']) ? $client['ap_cliente_id'] : '';
            $apClienteNombre = isset($client['ap_cliente_nombre']) ? $client['ap_cliente_nombre'] : '';
          ?>
          <input type="hidden"
            name="ap_cliente_id" 
            id="ap_cliente_value" 
            value="<?= $apClienteId ?>"
          />
          <input type="text" 
            id="ap_cliente_id" 
            placeholder="BUSCAR AP CLIENTE"
            value="<?= $apClienteNombre ?>"
          />
          <ul id="box-search-apcliente" class="autocom-box"></ul>
          <div class="icon"><i class="fas fa-search"></i></div>
        </div>
      </div>
    </div>   
    
    <div class="form-group row" id="content-nap_cliente_id">
      <label class="col-md-3 text-lg-right col-form-label">Caja Nap <span class="text-danger" id="label-nap_cliente_id">*</span></label>
      <div class="col-md-6">
        <div class="search-input" id="container-search-nap">
          <?php
            $napClienteId = isset($client['nap_cliente_id']) ? $client['nap_cliente_id'] : '';
            $napClienteNombre = isset($client['nap_cliente_nombre']) ? $client['nap_cliente_nombre'] : '';
          ?>
          <input type="hidden"
            name="nap_cliente_id" 
            id="nap_cliente_id" 
            value="<?= $napClienteId ?>"
          />
          <input type="text" 
            id="nap_cliente_nombre"
            placeholder="BUSCAR CAJA NAP"
            value="<?= $napClienteNombre ?>"
            onkeyup="search_nap_cliente()"
          />
          <ul id="box-search-nap" class="autocom-box"></ul>
          <div class="icon"><i class="fas fa-search"></i></div>
        </div>
      </div>
    </div>   

    <div class="form-group row" id="content-ppoe_usuario">
      <label class="col-md-3 text-lg-right col-form-label">PPOE Usuario <span class="text-danger" id="label-ppoe_usuario">*</span></label>
      <div class="col-md-6">
        <input type="text" 
          class="form-control" 
          name="ppoe_usuario" 
          id="ppoe_usuario" 
          placeholder="INGRESE USUARIO" 
          value="<?= isset($client) ? $client['ppoe_usuario'] : null ?>" 
          data-parsley-group="step-1"
        />
      </div>
    </div>

    <div class="form-group row" id="content-ppoe_password">
      <label class="col-md-3 text-lg-right col-form-label">PPOE Password <span class="text-danger" id="label-ppoe_password">*</span></label>
      <div class="col-md-6">
        <input type="text" 
          class="form-control" 
          name="ppoe_password" 
          id="ppoe_password" 
          placeholder="INGRESE PASSWORD" 
          value="<?= isset($client) ? $client['ppoe_password'] : null ?>" 
          data-parsley-group="step-1"
        />
      </div>
    </div>

    <div class="form-group row m-b-10">
      <label class="col-md-3 text-lg-right col-form-label">Correo</label>
      <div class="col-md-9">
        <input type="text" 
          class="form-control" 
          name="email" 
          id="email" 
          onkeypress="return mail(event)" 
          placeholder="EXAMPLE@EXAMPLE.com" 
          value="<?= isset($client) ? $client['email'] : "" ?>"
        >
        <small id="emailError" class="form-text text-danger d-none">El correo debe contener un símbolo "@"</small>
      </div>
    </div>
      
    <script>
      document.getElementById('email').addEventListener('input', function() {
      var emailInput = document.getElementById('email');
      var emailError = document.getElementById('emailError');

      if (emailInput.value.includes('@')) {
        emailError.classList.add('d-none');
      } else {
        emailError.classList.remove('d-none');
      }
      });
    </script>
    <div class="form-group row m-b-10">
      <label class="col-md-3 text-lg-right col-form-label">Dirección <span class="text-danger">*</span></label>
      <div class="col-md-9">
        <input type="text" 
          class="form-control text-uppercase" 
          name="address" 
          id="address" 
          onkeypress="return numbersandletters(event)" 
          placeholder="INGRESE DOMICILIO" 
          value="<?= isset($client) ? $client['address'] : null ?>" 
          data-parsley-group="step-1" 
          data-parsley-required="true"
        >
      </div>
    </div>
    <div class="form-group row m-b-10">
      <label class="col-md-3 text-lg-right col-form-label">Referencia</label>
      <div class="col-md-9">
        <input type="text" 
          class="form-control text-uppercase" 
          name="reference" 
          id="reference" 
          onkeypress="return numbersandletters(event)" 
          placeholder="REFERENCIA DEL DOMICILIO (OPCIONAL)" 
          value="<?= isset($client) ? $client['reference'] : '' ?>"
        >
      </div>
    </div>
  </div>
</div>
<script src="<?= base_style() ?>/js/form/customForm.js"></script>