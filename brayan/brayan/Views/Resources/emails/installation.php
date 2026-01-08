<?php
    $facility = $information['data']['facility'];
    $tools = $information['data']['tools'];
    $services = $information['data']['services'];
    $technical = $information['data']['technical'];
    $code = str_pad($facility['id'],7,"0", STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>INS-<?= $code ?></title>
		<style type="text/css">
			p{
				font-family: arial;letter-spacing: 1px;color: #7f7f7f;font-size: 12px;
			}
      hr{
        border: 0;
        border-top: 1px solid #eee;
        margin: 2px 0;
      }
      .hr{
        border-top: 1px dashed #000;
      }
			h4{font-family: arial; margin: 0;}
			table{width: 100%; max-width: 600px; margin: 10px auto; border: 1px solid #bdbdbd; border-spacing: 0;border-collapse: collapse;}
			table tr td, table tr th{padding: 5px 10px;font-family: arial; font-size: 12px;border: 1px solid #CCC;}

			.table-active{background-image: linear-gradient(to bottom,#f5f5f5 0,#e8e8e8 100%);}
            .titulo{
                font-size: 18px;
                border: 1px solid #fff;
            }
			.text-center{text-align: center;}
			.text-right{text-align: right;}
			.text-left{text-align: left;}

			@media screen and (max-width: 470px) {
				.logo{width: 90px;}
				p, table tr td, table tr th{font-size: 9px;}
			}
		</style>
	</head>
	<body>
		<div>
			<table>
				<tr>
					<td width="50%" style="border: 1px solid #fff;">
						<img class="logo" src="<?= $information['logo'] ?>" style="max-height:65px;" alt="Logo">
					</td>
					<td width="50%" style="border: 1px solid #fff;">
						<div class="text-right">
							<p>
								No. Instalación: <strong><?= $code ?></strong><br>
	              Fecha: <?= date("d/m/Y") ?>
							</p>
						</div>
					</td>
				</tr>
			</table>
            <table>
                <tr>
                    <td class="text-center titulo"><strong>HOJA DE INSTALACIÓN</strong></td>
                </tr>
            </table>
            <table>
			  	<thead class="table-active">
				    <tr>
				      	<th colspan="2" class="text-left">DATOS DEL CLIENTE</th>
				    </tr>
			  	</thead>
			  	<tbody>
					<tr>
						<td width="150"><strong>NOMBRES Y APELLIDOS</strong></td>
		  		  <td class="text-Lleft"><?= $facility['client'] ?></td>
					</tr>
					<tr>
						<td><strong>N° DNI</strong></td>
						<td class="text-left"><?= $facility['document'] ?></td>
					</tr>
					<tr>
            <?php
                $mobile = '';
                if(!empty($facility['mobile'])){
                  $mobile .= $facility['mobile'];
                }
                if(!empty($facility['mobile_optional'])){
                  $mobile .= '-'.$facility['mobile_optional'];
                }
             ?>
						<td><strong>CELULARES</strong></td>
						<td class="text-left"><?= $mobile ?></td>
					</tr>
					<tr>
            <?php
              $address = '';
              if(!empty($facility['address'])){
                $address .= $facility['address'];
              }
              if(!empty($facility['reference'])){
                $address .= ' <strong>REF.</strong> '.$facility['reference'];
              }
             ?>
						<td><strong>DIRECCIÓN</strong></td>
						<td class="text-left"><?= $address ?></td>
					</tr>
			  	</tbody>
			</table>
      <table>
          <thead class="table-active">
            <tr>
              <th colspan="2" class="text-left">CARACTERÍSTICAS DE LA INSTALACIÓN</th>
            </tr>
          </thead>
          <tbody id="detalleOrden">
            <tr>
              <td width="150"><strong>TÉCNICO</strong></td>
              <td class="text-left"><?= $technical['names']." ".$technical['surnames'] ?></td>
            </tr>
            <tr>
              <td><strong>FECHA Y HORA</strong></td>
              <td class="text-left"><?= date("d/m/Y h:i a",strtotime($facility['attention_date'])) ?></td>
            </tr>
            <tr>
              <td><strong>COSTO DE INSTALACIÓN</strong></td>
              <td class="text-left"><?= $_SESSION['businessData']['symbol'].format_money($facility['cost']) ?></td>
            </tr>
          </tbody>
      </table>
			<table>
				<tr>
					<td style="border: 1px solid #fff;padding-left:0px;"><strong>SERVICIOS CONTRATADOS</strong></td>
				</tr>
			</table>
			<table>
			  	<thead class="table-active">
			    	<tr>
			     	 	<th class="text-left">PLAN</th>
			    	</tr>
			  	</thead>
			  	<tbody id="detalleOrden">
            <?php if(!empty($services)): ?>
              <?php foreach ($services AS $service) : ?>
                  <tr>
                      <td class="text-left"><?= $service['service'] ?></td>
                  </tr>
              <?php endforeach ?>
            <?php else: ?>
                  <tr>
                      <td class="text-center">NO HAY SERVICIOS</td>
                  </tr>
            <?php endif; ?>
			  	</tbody>
			</table>
			<table>
				<tr>
					<td style="border: 1px solid #fff;padding-left:0px;"><strong>PRODUCTOS INSTALADOS</strong></td>
				</tr>
			</table>
			<table>
			 	<thead class="table-active">
					<tr>
					    <th class="text-left">PRODUCTO</th>
              <th class="text-center">CONDICIÓN</th>
					    <th class="text-center">PRECIO</th>
					    <th class="text-center">CANTIDAD</th>
					    <th class="text-center">IMPORTE</th>
					</tr>
			  	</thead>
			  	<tbody id="detalleOrden">
            <?php if(!empty($tools)): ?>
              <?php foreach ($tools AS $tool) : ?>
                    <tr>
                        <td class="text-left"><?= $tool['product'] ?></td>
                        <td class="text-center"><?= $tool['product_condition'] ?></td>
                        <td class="text-center"><?= $_SESSION['businessData']['symbol'].format_money($tool['price']) ?></td>
                        <td class="text-center"><?= $tool['quantity'] ?></td>
                        <td class="text-center"><?= $_SESSION['businessData']['symbol'].format_money($tool['total']) ?></td>
                    </tr>
              <?php endforeach ?>
            <?php else: ?>
                  <tr>
                      <td colspan="5" class="text-center">NO HAY PRODUCTOS</td>
                  </tr>
            <?php endif; ?>
			 	</tbody>
			</table>
			<table>
				<thead class="table-active">
					<tr>
						<th class="text-left">DETALLES</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="text-left"><?= $facility['detail'] ?></td>
					</tr>
				</tbody>
			</table>
			<table width="100%">
				<tbody>
					<tr>
						<td style="margin-top:-20px;border: 1px solid #fff;color: #999;font-size: 12px;">Este es un email automático, si tienes cualquier tipo de duda ponte en contacto con nosotros a través de nuestro servicio de atención al cliente al <?= $information['mobile'] ?>, por favor no respondas a este mensaje.</td>
					</tr>
				</tbody>
			</table>
		</div>
	</body>
</html>
