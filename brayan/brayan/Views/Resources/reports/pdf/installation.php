<?php
    $facility = $data['facility'];
    $tools = $data['tools'];
    $services = $data['services'];
    $technical = $data['technical'];
    $logo = 'Assets/uploads/business/'.$_SESSION['businessData']['logotyope'];
    $code = str_pad($facility['id'],7,"0", STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>INS-<?= $code ?></title>
    <link rel="stylesheet" href="<?= base_style() ?>/css/style_installation.css">
    <style media="screen">
      hr{
        border: 0;
        border-top: 1px solid #eee;
        margin: 2px 0;
      }
      .hr{
        border-top: 1px dashed #000;
      }
    </style>
	</head>
	<body>
		<table class="tbl-logo">
			<tbody>
				<tr>
					<td class="wd33">
            <?php if(file_exists($logo)): ?>
                <img src="<?= base_url()."/".$logo ?>" style="max-height:65px;">
            <?php else: ?>
                <img src="<?= base_style().'/images/logotypes/superwisp.png' ?>" style="max-height:65px;">
            <?php endif; ?>
					</td>
					<td class="text-right wd33">
						<p>No. Instalación: <strong><?= $code ?></strong><br>
							Fecha Imp.: <?= date("d/m/Y") ?>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
        <table>
            <tr>
                <td class="text-center titulo"><strong>HOJA DE INSTALACIÓN</strong></td>
            </tr>
        </table>
        <table class="tbl-cliente">
            <thead class="table-head">
                <tr>
                    <th colspan="2" class="text-left">DATOS DEL CLIENTE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="150"><strong>NOMBRES Y APELLIDOS</strong></td>
                    <td class="text-left"><?= $facility['client'] ?></td>
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
        <table class="tbl-servicio">
            <thead class="table-head">
                <tr>
                    <th colspan="2" class="text-left">CARACTERÍSTICAS DE LA INSTALACIÓN</th>
                </tr>
            </thead>
            <tbody>
              <tr>
                <td width="150"><strong>TÉCNICO</strong></td>
                <td class="text-left"> <?= is_array($technical) ? $technical['names']." ".$technical['surnames'] : '' ?></td>
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
        <table class="tbl-servicio">
            <thead class="table-head">
                <tr>
                    <th class="text-left">PLAN</th>
                </tr>
            </thead>
            <tbody>
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
        <table class="tbl-producto">
            <thead class="table-head">
            <tr>
                    <th  class="text-left">PRODUCTO</th>
                    <th  class="text-center">SERIE</th>
                    <th  class="text-center">MAC</th>
                    <th  class="text-center">CONDICIÓN</th>
                    <th  class="text-center">PRECIO</th>
                    <th  class="text-center">CANTIDAD</th>
                    <th  class="text-center">IMPORTE</th>
                </tr>
            </thead>
            <tbody>
              <?php if(!empty($tools)): ?>
                <?php foreach ($tools AS $tool) : ?>
                    <tr>
                          <td class="text-left"><?= $tool['product'] ?></td>
                          <td class="text-left"><?= $tool['serie'] ?></td>
                          <td class="text-left"><?= $tool['mac'] ?></td>
                          <td class="text-center"><?= $tool['product_condition'] ?></td>
                          <td class="text-center"><?= $_SESSION['businessData']['symbol'].format_money($tool['price']) ?></td>
                          <td class="text-center"><?= $tool['quantity'] ?></td>
                          <td class="text-center"><?= $_SESSION['businessData']['symbol'].format_money($tool['total']) ?></td>
                      </tr>
                <?php endforeach ?>
              <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">NO HAY PRODUCTOS</td>
                    </tr>
              <?php endif; ?>
            </tbody>
        </table>
        <table class="tbl-observacion">
            <thead class="table-head">
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
            <table>
            <tr>
                <td style="border: 1px solid #fff;padding-left:0px;"><strong>INFORMACIÓN ADICIONAL</strong></td>
            </tr>
        </table>
        
        <table class="tbl-observacion">
            <tbody>
                <tr>
                    <td style="border: 1px solid #fff;padding-left:0px;" class="text-left">•	Todo reporte por falla de su servicio deberá ser comunicado al departamento de soporte técnico o a nuestra oficina para solucionarlo</td>      
                </tr>
                <tr>
                    <td style="border: 1px solid #fff;padding-left:0px;" class="text-left">•	La reconexión por falta de pago NO TIENE NINGUN COSTO ADICIONAL, la reactivación se realiza en un lapso de 30 minutos luego de registrar el pago</td>      
                </tr>
                <tr>
                    <td style="border: 1px solid #fff;padding-left:0px;" class="text-left">•	La suspensión por más de 30 días, por falta de pago, dará lugar a la cancelación del servicio</td>      
                </tr>
                <tr>
                    <td style="border: 1px solid #fff;padding-left:0px;" class="text-left">•	Al cancelar su servicio, suspendemos su servicio y en caso los equipos fueran prestados por contrato, retiramos los equipos que fueron entregados.</td>      
                </tr>
            </tbody>
        </table>
        <br><br><br><br>
        <table width="100%">
            <tbody>
                <tr>
                    <td style="border:0;" class="text-center"> <hr class="hr"> FIRMA CLIENTE</td>
                    <td style="border:0;" class="text-center"> <hr class="hr"> FIRMA TECNICO</td>
                </tr>
            </tbody>
        </table>
	</body>
</html>

