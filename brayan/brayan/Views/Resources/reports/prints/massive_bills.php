<?php
  $business = $data['business'];
  $bills = $data['bills'];
	$logo = 'Assets/uploads/business/'.$business['logotyope'];
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<link rel="stylesheet" href="<?= base_style() ?>/css/style_ticket.css">
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
		<title>Comprobante</title>
    <style media="print" type="text/css">
				@page {
						size: 72mm 246mm portrait;
						margin: 0.5cm;
				}
		</style>
	</head>
	<body>
    <?php foreach ($bills AS $bill) : ?>
    <?php
      $details = $bill['details'];
      $payments = $bill['payments'];
      $atm = $bill['atm'];
    ?>
    <div class="row">
      <table width="100%">
				<tbody>
					<tr style="padding: 0px;">
						<td style="padding: 0px; width: 100%;">
						<p style="font-size: 9px; text-align: center;">
							<?php if (file_exists($logo)): ?>
									<img src="<?= base_url()."/".$logo ?>" style="width: 170px; height: 50px;">
							<?php else: ?>
									<img src="<?= base_style().'/images/logotypes/superwisp.png' ?>" style="width: 170px; height: 50px;">
							<?php endif; ?>
						</p>
						<p style="font-size: 9px; text-align: center;">
							<span style="font-family:arial,helvetica,sans-serif;">
								<span style="font-size:9px;"><?= $business['slogan'] ?></span>
							</span>
						</p>
						<p style="font-size: 9px; text-align: center;">
							<span style="font-family:arial,helvetica,sans-serif;">
								<span style="font-size:13px;"><strong>RUC: </strong><strong><?= $business['ruc'] ?></strong> </span>
							</span>
						</p>
						<p style="font-size: 9px; text-align: center;">
							<span style="font-family:arial,helvetica,sans-serif;">
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
								<span style="font-size:11px;">CEL. <?= $mobiles_business ?></span>
							</span>
						</p>
						<p style="font-size: 9px; text-align: center;">
							<span style="font-family:arial,helvetica,sans-serif;">
								<span style="font-size:11px;"><?= $business['address'] ?></span>
							</span>
						</p>
            <?php
            	$correlative = str_pad($bill['correlative'],7,"0", STR_PAD_LEFT);
              $comprobante = $bill['serie'] . '|' . $correlative;
              $name_qr = $business['ruc']. '|' . $bill['voucher'] . '|' . $comprobante . '|0.00|' . $bill["total"] . '|' . $bill['date_issue'] . '|2|' . $bill['document'] . '|';
              $route_qr = 'Assets/uploads/qr/'.$bill['serie'] .'-'.$correlative.'.png';
            ?>
            <p style="font-size: 9px; text-align: center;">
              <span style="font-family:arial,helvetica,sans-serif;">
                  <span style="font-size:15px;"><strong><?= $bill['voucher'] ?> </strong><strong><?= $bill['serie']."-".$correlative ?></strong></span>
              </span>
            </p><hr class="hr">
						<p style="font-size:9px;">
							<span style="font-family:arial,helvetica,sans-serif;">
								<span style="font-size:11px;"><strong>CLIE: </strong><?= $bill['names']." ".$bill['surnames'] ?></span>
							</span>
						</p>
						<p style="font-size:9px;">
							<span style="font-family:arial,helvetica,sans-serif;">
								<span style="font-size:11px;"><strong>DIRE: </strong><?= $bill['address'] ?></span>
							</span>
						</p>
						<p style="font-size:9px;">
							<span style="font-family:arial,helvetica,sans-serif;">
								<?php
									$mobiles = '';
									if(!empty($bill['mobile'])){
										$mobiles .= $bill['mobile'];
									}
									if(!empty($bill['mobile_optional'])){
										$mobiles .= ' - ';
										$mobiles .= $bill['mobile_optional'];
									}
								 ?>
								<span style="font-size:11px;"><strong><?= $bill['type_doc'] ?>: </strong><?= $bill['document'] ?> <strong>CEL: </strong><?= $mobiles ?></span>
							</span>
						</p>
						<hr class="hr">
						<p style="font-size:9px;">
							<span style="font-family:arial,helvetica,sans-serif;">
								<span style="font-size:11px;"><strong>F.EMISIÓN: </strong><?= date("d/m/Y",strtotime($bill['date_issue'])) ?></span>
							 </span>
						 </p>
             <?php
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
						 <p style="font-size:9px;">
							<span style="font-family:arial,helvetica,sans-serif;">
								<span style="font-size:11px;"><strong>ESTADO: </strong><?= $state ?></span>
							 </span>
						 </p>
						 <hr class="hr">
						</td>
					</tr>
				</tbody>
			</table>
      <table class="tabla">
				<tr>
					<?php if($bill['type'] == 1): ?>
						<td><span style="font-family:arial,helvetica,sans-serif;font-size:11px;"><b>DESCRIPCIÓN</b></span></td>
						<td class="center"><span style="font-family:arial,helvetica,sans-serif;font-size:11px;"><b>CANT.</b></span></td>
						<td class="center"><span style="font-family:arial,helvetica,sans-serif;font-size:11px;"><b>P.UNI</b></span></td>
						<td class="center"><span style="font-family:arial,helvetica,sans-serif;font-size:11px;"><b>IMP.</b></span></td>
					<?php else: ?>
						<td><span style="font-family:arial,helvetica,sans-serif;font-size:11px;"><b>DESCRIPCIÓN</b></span></td>
						<td class="center"><span style="font-family:arial,helvetica,sans-serif;font-size:11px;"><b>CANT.</b></span></td>
						<td class="center"><span style="font-family:arial,helvetica,sans-serif;font-size:11px;"><b>P.UNI</b></span></td>
					<?php endif; ?>
				</tr>
				<tbody>
					<?php if(!empty($details)): ?>
					<?php foreach ($details AS $row) : ?>
						<?php if($bill['type'] == 1): ?>
							<tr>
								<td><span style="font-family:arial,helvetica,sans-serif;font-size:11px;"><?= $row["description"]?></span></td>
								<td class="center"><span style="font-family:arial,helvetica,sans-serif;font-size:11px;"><?= $row["quantity"]?></span></td>
								<td class="center"><span style="font-family:arial,helvetica,sans-serif;font-size:11px;"><?= $row["price"]?></span></td>
								<td class="center"><span style="font-family:arial,helvetica,sans-serif;font-size:11px;"><?= $row["total"]?></span></td>
							</tr>
						<?php else: ?>
							<tr>
								<td><span style="font-family:arial,helvetica,sans-serif;font-size:11px;"><?= $row["description"]?></span></td>
								<td class="center"><span style="font-family:arial,helvetica,sans-serif;font-size:11px;"><?= $row["quantity"]?></span></td>
								<td class="center"><span style="font-family:arial,helvetica,sans-serif;font-size:11px;"><?= $row["price"]?></span></td>
							</tr>
						<?php endif; ?>
					<?php endforeach ?>
					<?php else: ?>
						<?php if($bill['type'] == 1): ?>
							<tr style="font-family:arial,helvetica,sans-serif;">
								<td class="center" colspan="4">NO HAY REGISTROS.</td>
							</tr>
						<?php else: ?>
							<tr style="font-family:arial,helvetica,sans-serif;">
								<td class="center" colspan="3">NO HAY REGISTROS.</td>
							</tr>
						<?php endif; ?>
              		<?php endif; ?>
				</tbody>
			</table>
      <table width="100%">
				<tbody>
					<tr>
						<td colspan="2"><hr class="hr"></td>
					</tr>
					<tr style="font-family:arial,helvetica,sans-serif;">
						<td class="right" style="font-size:10px;"><strong>SUBTOTAL</strong></td>
						<td class="center" style="font-size:11px;"> <?= $business['symbol'].format_money($bill["subtotal"]) ?></td>
					</tr>
					<tr style="font-family:arial,helvetica,sans-serif;">
						<td class="right" style="font-size:10px;"><strong>DESCUENTO</strong></td>
						<td class="center" style="font-size:11px;"> <?= $business['symbol'].format_money($bill["discount"]) ?></td>
					</tr>
					<tr style="font-family:arial,helvetica,sans-serif;">
						<td class="right" style="font-size:10px;"><strong>TOTAL</strong></td>
						<td class="center" style="font-size:11px;"> <?= $business['symbol'].format_money($bill["total"]) ?></td>
					</tr>
				</tbody>
			</table>
			<table width="100%">
				<tbody>
					<tr>
						<td style="padding: 0px; width: 100%;">
							<hr class="hr">
							<p style="font-size:9px;">
								<span style="font-size:11px;">
									<span style="font-family:arial,helvetica,sans-serif;">
										<strong>SON: </strong><?= numbers_letters($bill['total'],$business['money'],$business['money_plural']) ?>
									</span>
								</span>
							</p>
							<?php
								$user = empty($atm['user']) ? "" : $atm['user'];
							?>
							<p style="font-size:9px;">
								<span style="font-size:11px;">
									<span style="font-family:arial,helvetica,sans-serif;">
										<strong>CAJERO: </strong><?= $user ?>
									</span>
								</span>
							</p>
							<hr class="hr">
							<?php if(!empty($payments)){ ?>
								<p style="font-size:9px;">
									<span style="font-family:arial,helvetica,sans-serif;">
										<span style="font-size:11px;"><strong>PAGOS:</strong></span>
									 </span>
								 </p>
							<?php foreach ($payments as $payment) : ?>
								<p style="font-size:9px;">
									<span style="font-family:arial,helvetica,sans-serif;">
										<span style="font-size:10px;"><?= $payment['payment_type'] ?> - <?= date("d/m/Y H:i",strtotime($payment['payment_date'])) ?> - <?= $business['symbol'].format_money($payment['amount_paid']) ?></span>
									 </span>
								 </p>
							<?php endforeach ?>
							<hr class="hr">
							<?php } ?>
							<?php if($bill['voucherid'] == 2 || $bill['voucherid'] == 3): ?>
								<br>
								<div class="contenedor-qr">
									<img src="<?= generate_qr_image($name_qr,$route_qr,5,"H",3); ?>">
								</div>
							<?php endif; ?>
							<?= $business['footer_text'] ?>
						</td>
					</tr>
				</tbody>
			</table>
    </div>
    <?php if($bill !== end($bills)) : ?>
    <div style="page-break-after:always;"></div>
    <?php endif; ?>
    <?php endforeach ?>
    <script src="<?= base_style() ?>/js/app.min.js"></script>
		<script type="text/javascript">
		    $(document).ready(function() {
		        window.print();
		    });
		</script>
  </body>
</html>
