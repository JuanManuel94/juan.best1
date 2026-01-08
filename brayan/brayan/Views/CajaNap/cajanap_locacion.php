<?php
head($data);
$nap = $data['nap'];
?>
<div class="map">
  <input type="hidden" id="latitud" value="<?= $nap['latitud'] ?>">
  <input type="hidden" id="longitud" value="<?= $nap['longitud'] ?>">
  <input type="hidden" id="nombre" value="<?= $nap['nombre'] ?>">
  <input type="hidden" id="puertos" value="<?= $nap['puertos'] ?>">
  <input type="hidden" id="detalles" value="<?= $nap['detalles'] ?>">
  <div id="nap_location" class="height-full width-full"></div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>