<?php
  head($data);
  $client = $data['client'];
?>
<div class="map">
    <input type="hidden" id="my_lat">
    <input type="hidden" id="my_lng">
    <input type="hidden" id="lat_client" value="<?= $client['latitud'] ?>">
    <input type="hidden" id="lng_client" value="<?= $client['longitud'] ?>">
    <input type="hidden" id="name_client" value="<?= $client['names']." ".$client['surnames'] ?>">
    <input type="hidden" id="address_client" value="<?= $client['address'] ?>">
    <div id="customer_location" class="height-full width-full"></div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?> 
