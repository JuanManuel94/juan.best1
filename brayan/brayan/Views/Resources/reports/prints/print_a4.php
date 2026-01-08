<?php
	$business = $data['business'];
	$bill = $data['bill'];
	$detail = $data['detail'];
	$payments = $data['payments'];
	$correlative = str_pad($bill['correlative'],7,"0", STR_PAD_LEFT);
	$logo = 'Assets/uploads/business/'.$business['logotyope'];
	$comprobante = $bill['serie'] . '|' . $correlative;
	$name_qr = $business['ruc']. '|' . $bill['voucher'] . '|' . $comprobante . '|0.00|' . $bill["total"] . '|' . $bill['date_issue'] . '|2|' . $bill['document'] . '|';
	$route_qr = 'Assets/uploads/qr/'.$bill['serie'] .'-'.$correlative.'.png';
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
				<?php
						if(!empty($_SESSION['businessData']['favicon'])){
								if($_SESSION['businessData']['favicon'] == "favicon.png"){
										$favicon = base_style().'/images/logotypes/'.$_SESSION['businessData']['favicon'];;
								}else{
										$favicon_url = base_style().'/uploads/business/'.$_SESSION['businessData']['favicon'];
										if(@getimagesize($favicon_url)){
												$favicon = base_style().'/uploads/business/'.$_SESSION['businessData']['favicon'];
										}else{
												$favicon = base_style().'/images/logotypes/favicon.png';
										}
								}
						}else{
								$favicon = base_style().'/images/logotypes/favicon.png';
						}
				?>
				<!-- ================== INICIO ICONO ================== -->
				<link rel="icon" type="image/x-icon" href="<?= $favicon ?>">
        <title><?= $bill['serie'].'-'.$correlative ?></title>
    </head>
   <body>
		 <table class="cabecera1">
				 <tr>
						 <th>
								 <?php if(file_exists($logo)): ?>
										 <img src="<?= base_url()."/".$logo ?>" width="270px">
								 <?php else: ?>
										 <img src="<?= base_style().'/images/logotypes/superwisp.png' ?>" width="270px">
								 <?php endif; ?>
						 </th>
				 </tr>
				 <tr>
						 <th><span style="font-size:12px;"><?= $business['slogan'] ?></span></th>
				 </tr>
				 <tr>
						 <?php
								 $mobiles_business = '';
								 if(!empty($business['mobile'])){
									 $mobiles_business .= $business['mobile'];
								 }
								 if(!empty($business['mobile_refrence'])){
									 $mobiles_business .= ' - ';
									 $mobiles_business .= $business['mobile_refrence'];
								 }
							?>
						 <th>Cel. <?= $mobiles_business ?></th>
				 </tr>
				 <tr>
						 <th><?= $business['address'] ?></th>
				 </tr>
		 </table><br>
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
		 <table border="0">
				 <tr>
						 <td>
								 <table class="cabecera3">
										 <tr>
												 <td colspan="2">&nbsp;</td>
										 </tr>
										 <tr>
												 <th width="25%">CLIENTE</th>
												 <td>:&nbsp;&nbsp;<?= $bill['names']." ".$bill['surnames'] ?></td>
										 </tr>
										 <tr>
											 <th>DNI</th>
											 <td>:&nbsp;&nbsp;<?= $bill['document'] ?></td>
										 </tr>
										 <tr>
											 <th>CELULAR</th>
											 <td>:&nbsp;&nbsp;<?= $bill['mobile'] ?></td>
										 </tr>
										 <tr>
											 <th>DIRECCIÓN</th>
											 <td>:&nbsp;&nbsp;<?= $bill['address'] ?></td>
										 </tr>
										 <tr>
											 <td colspan="2">&nbsp;</td>
										 </tr>
								 </table>
						 </td>
						 <td>
								 <table class="cabecera3">
										 <tr>
												 <td colspan="2">&nbsp;</td>
										 </tr>
										 <tr>
												 <th>FECHA EMISIÓN</th>
												 <td>:&nbsp;&nbsp;<?= date("d/m/Y",strtotime($bill['date_issue'])) ?></td>
										 </tr>
										 <tr>
												 <th>FECHA DE VENC.</th>
												 <td>:&nbsp;&nbsp;<?= date("d/m/Y",strtotime($bill['expiration_date'])) ?></td>
										 </tr>
										 <tr>
												 <th>MONEDA</th>
												 <td>:&nbsp;&nbsp;<?= $business['money_plural'] ?></td>
										 </tr>
										 <tr>
												 <th>ESTADO</th>
												 <td>:&nbsp;&nbsp;<?= $state ?></td>
										 </tr>
										 <tr>
												 <td colspan="2">&nbsp;</td>
										 </tr>
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
                          <td align="center"><?= $row["price"]?></td>
                          <td align="center"><?= $row["total"]?></td>
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
							 <table class="cabecera3">
								 <?php if(!empty($payments)){ ?>
								 <tr>
									 <td> <strong>PAGOS</strong></td>
								 </tr>
								 <?php foreach ($payments as $payment) : ?>
									 <tr><td><?= $payment['payment_type'] ?> - <?= date("d/m/Y H:i",strtotime($payment['payment_date'])) ?> - <?= $business['symbol'].format_money($payment['amount_paid']) ?></td></tr>
								 <?php endforeach ?>
								 <?php } ?>
								 <tr><?= $business['footer_text'] ?></tr>
							 </table>
					 </td>
					 <?php if($bill['voucherid'] == 2 || $bill['voucherid'] == 3): ?>
					 <td width="30%">
							 <table class="cabecera3">
								 <tr>
									<td align="center"><img src="<?= generate_qr_image($name_qr,$route_qr,5,"H",3); ?>" alt="" width="135px"></td>
								 </tr>
							 </table>
					 </td>
					 <?php endif; ?>
				 </tr>
		 </table>
    <script src="<?= base_style() ?>/js/app.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            window.print();
        });
    </script>
    </body>
</html>
