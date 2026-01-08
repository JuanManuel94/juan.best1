<?php
	$business = $data['business'];
	$bill = $data['bill'];
	$detail = $data['detail'];
	$payments = $data['payments'];
	$correlative = str_pad($bill['correlative'],7,"0", STR_PAD_LEFT);
	$logo = 'Assets/uploads/business/'.$business['logotyope'];
	$comprobante = $bill['serie'] . '|' . $correlative;
	$name_qr = $business['ruc']. '|' . $bill['voucher'] . '|' . $comprobante . '|0.00|' . $bill["total"] . '|' . $bill['date_issue'] . '|2|' . $bill['document'] . '|';
	if($bill['state'] == 1){
		$state = "PAGADO";
	}else if($bill['state'] == 2){
		$state = "PENDIENTE";
	}else if($bill['state'] == 3){
		$state = "VENCIDO";
	}else if($bill['state'] == 4){
		$state = "ANULADO";
	}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="<?= base_style() ?>/css/style_a4.css">
        <title><?= $bill['serie'].'-'.$correlative ?></title>
    </head>
   <body>
       <table width="100%" style="border-collapse: collapse; margin-bottom: 20px;">
  <tr>
    <!-- Logo -->
    <td width="5%" style="padding-right: 10px; vertical-align: top;">
      <?php if(file_exists($logo)): ?>
        <img src="<?= base_url()."/".$logo ?>" width="120px">
      <?php else: ?>
        <img src="<?= base_style().'/images/logotypes/superwisp.png' ?>" width="90px">
      <?php endif; ?>
    </td>

    <!-- Línea vertical negra, más corta y centrada verticalmente -->
<td width="1%" style="padding: 0;">
  <div style="
    height: 80px;
    margin: auto;
    border-left: 2px solid #000;
    display: flex;
    align-items: center;
  "></div>
</td>

    <!-- Información de empresa -->
    <td width="48%" style="padding-left: 8px; padding-top: 20px; vertical-align: top;">
      <div style="font-weight: bold; font-size: 12px;"><?= $business['name'] ?></div>
      <div><?= $business['address'] ?></div>
      <?php
        $mobiles_business = '';
        if (!empty($business['mobile'])) $mobiles_business .= $business['mobile'];
        if (!empty($business['mobile_refrence'])) $mobiles_business .= ' / ' . $business['mobile_refrence'];
      ?>
      <div>Teléfonos: <?= $mobiles_business ?></div>
      <div>E - mail: <?= $business['email'] ?></div>
      <div><?= str_replace("https://", "www.", base_url()) ?></div>
    </td>

</table>
       <br>
        <table class="cabecera2">
            <tr>
                <td>RUC <?= $business['ruc'] ?></td>
            </tr>
            <tr>
                <td><?= $bill['voucher'] ?></td>
            </tr>
            <tr>
                <td><?= $bill['serie']."-".$correlative ?></td>
            </tr>
        </table>
       <table border="0" width="100%">
  <tr valign="top">
    <!-- Columna izquierda -->
    <td width="50%">
      <table class="cabecera3" width="100%">
        <tr><td colspan="2" height="5"></td></tr>

        <tr>
          <th width="25%" align="left">CLIENTE</th>
          <td>: <?= $bill['names'] . " " . $bill['surnames'] ?></td>
        </tr>
        <tr>
          <th align="left"><?= $bill['type_doc'] ?></th>
          <td>: <?= $bill['document'] ?></td>
        </tr>
        <tr>
          <th align="left">CELULAR</th>
          <td>: <?= $bill['mobile'] ?></td>
        </tr>
        <tr>
          <th align="left">DIRECCI&Oacute;N</th>
          <td>: <?= $bill['address'] ?></td>
        </tr>

        <tr><td colspan="2" height="5"></td></tr>
      </table>
    </td>

    <!-- Columna derecha -->
    <td width="50%">
      <table class="cabecera3" width="100%">
        <tr><td colspan="2" height="5"></td></tr>

        <tr>
          <th width="45%" align="left">FECHA EMISI&Oacute;N</th>
          <td>: <?= date("d/m/Y", strtotime($bill['date_issue'])) ?></td>
        </tr>
        <tr>
          <th align="left">FECHA DE VENC.</th>
          <td>: <?= date("d/m/Y", strtotime($bill['expiration_date'])) ?></td>
        </tr>
        <tr>
          <th align="left">MONEDA</th>
          <td>: <?= $business['money_plural'] ?></td>
        </tr>
        <tr>
          <th align="left">ESTADO</th>
          <td>: <?= $state ?></td>
        </tr>

        <tr><td colspan="2" height="5"></td></tr>
      </table>
    </td>
  </tr>
</table>

        <table class="cabecera5">
            <thead class="encabeza">
                <tr>
                    <th align="center">CANT.</th>
                    <th align="left">DESCRIPCION</th>
                    <th align="center">P/U</th>
                    <th align="center">IMPORTE</th>
                </tr>
            </thead>
          <tbody class="zebra">
              <?php if(!empty($detail)): ?>
              <?php foreach ($detail AS $row) : ?>
                      <tr>
                          <td align="center"><?= $row["quantity"]?></td>
                          <td align="left"><?= $row["description"] ?></td>
                          <td align="center"><?= format_money($row["price"])?></td>
                          <td align="center"><?= format_money($row["total"])?></td>
                      </tr>
              <?php endforeach ?>
              <?php else: ?>
                      <tr>
                          <td class="text-center" colspan="4">NO HAY REGISTROS.</td>
                      </tr>
              <?php endif; ?>
              </tbody>
                <tr>
                    <th align="right" colspan="3">SUBTOTAL</th>
                    <th align="center"><?= $business['symbol'].format_money($bill["subtotal"]) ?></th>
                </tr>
                <tr>
                    <th align="right" colspan="3">DESCUENTO</th>
                    <th align="center"><?= $business['symbol'].format_money($bill["discount"]) ?></th>
                </tr>
                <tr>
                    <th align="right" colspan="3">TOTAL</th>
                    <th align="center"><?= $business['symbol'].format_money($bill["total"]) ?></th>
                </tr>
        </table>
        <table class="cabecera6" style="margin-bottom:7px;">
          <tr>
            <td><span>IMPORTE EN LETRAS:</span> <?= numbers_letters($bill['total'],$business['money'],$business['money_plural']) ?></td>
          </tr>
        </table>
        <table border="0">
						<tr>
							<td>
                <?php if(isset($bill['promise_date'])){?>
                  <hr class="hr">
                  <p style="font-size:9px;">
                    <span style="font-size:11px;">
                      <span style="font-family:arial,helvetica,sans-serif;">
                        <strong>FECHA DE COMPROMISO: </strong> <?= date_format(date_create($bill['promise_date']), "Y/m/d") ?>
                      </span>
                    </span>
                  </p>
                <?php }?>
                <?php if(!empty($payments)){ ?>
                  <tr>
                    <td> <strong>PAGOS:</strong></td>
                  </tr>
                  <?php foreach ($payments as $payment) : ?>
                    <tr><td><?= $payment['payment_type'] ?> - <?= date("d/m/Y H:i",strtotime($payment['payment_date'])) ?> - <?= $business['symbol'].format_money($payment['amount_paid']) ?></td></tr>
                  <?php endforeach ?>
                  <?php } ?>
                  
                <br>
                <table class="cabecera3">
                  
                  <tr><?= $business['footer_text'] ?></tr>
                </table>
							</td>
							<?php if($bill['voucherid'] == 2 || $bill['voucherid'] == 3): ?>
							<td width="30%">
							  	<table class="cabecera3">
							    	<tr>
							     	 <td align="center"><img src="<?= generate_qr($name_qr,5,"H",3); ?>" width="135px"></td>
							    	</tr>
							  	</table>
							</td>
							<?php endif; ?>
						</tr>
				</table>
    </body>
</html>
