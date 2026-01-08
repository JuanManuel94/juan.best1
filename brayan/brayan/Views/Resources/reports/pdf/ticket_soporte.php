<?php
	$correlative = str_pad($data['id'],7,"0", STR_PAD_LEFT);
	$logo = 'Assets/uploads/business/'.$_SESSION['businessData']['logotyope'];
	$name_qr = $data['id']. '|' . $data['names'] . '|' . $data['surnames'] . '|';
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
		<title><?= 'TCK-'.$correlative ?></title>
	</head>
	<body>
		<div class="row">
			<table width="100%">
				<tbody>
					<tr style="padding: 0px;">
						<td style="padding: 0px; width: 100%;">
						<p style="font-size: 9px; text-align: center;">
							<?php if(file_exists($logo)): ?>
									<img src="<?= base_url()."/".$logo ?>" style="width: 170px; height: 50px;">
							<?php else: ?>
									<img src="<?= base_style().'/images/logotypes/superwisp.png' ?>" style="width: 170px; height: 50px;">
							<?php endif; ?>
						</p>
						<p style="font-size: 9px; text-align: center;">
							<span style="font-family:arial,helvetica,sans-serif;">
								<span style="font-size:9px;"><?= $_SESSION['businessData']['slogan'] ?></span>
							</span>
						</p>
						<p style="font-size: 9px; text-align: center;">
							<span style="font-family:arial,helvetica,sans-serif;">
								<span style="font-size:13px;"><strong>RUC: </strong><strong><?= $_SESSION['businessData']['ruc'] ?></strong> </span>
							</span>
						</p>
						<p style="font-size: 9px; text-align: center;">
							<span style="font-family:arial,helvetica,sans-serif;">
								<?php
									$mobiles_business = '';
									if(!empty($_SESSION['businessData']['mobile'])){
										$mobiles_business .= $_SESSION['businessData']['mobile'];
									}
									if(!empty($_SESSION['businessData']['mobile_refrence'])){
										$mobiles_business .= ' - ';
										$mobiles_business .= $_SESSION['businessData']['mobile_refrence'];
									}
								 ?>
								<span style="font-size:11px;">CEL. <?= $mobiles_business ?></span>
							</span>
						</p>
						<p style="font-size: 9px; text-align: center;">
							<span style="font-family:arial,helvetica,sans-serif;">
								<span style="font-size:11px;"><?= $_SESSION['businessData']['address'] ?></span>
							</span>
						</p>
						<p style="font-size: 9px; text-align: center;">
							<span style="font-family:arial,helvetica,sans-serif;">
									<span><strong style="font-size:15px;">TICKET DE ATENCIÓN</strong><br><strong style="font-size:25px;"><?= $correlative ?></strong></span>
							</span>
						</p>
						<br>
						<p style="font-size:9px;">
							<span style="font-family:arial,helvetica,sans-serif;">
								<span style="font-size:11px;"><strong>CLIENTE: </strong><?= $data['names']." ".$data['surnames'] ?></span>
							</span>
						</p>
						<p style="font-size:9px;">
							<span style="font-family:arial,helvetica,sans-serif;">
								<span style="font-size:11px;"><strong>FECHA ATENCIÓN: </strong><?= date("d/m/Y",strtotime($data['attention_date'])) ?></span>
							 </span>
						 </p>
						 <p style="font-size:9px;">
							<span style="font-family:arial,helvetica,sans-serif;">
								<span style="font-size:11px;"><strong>HORA ATENCIÓN: </strong><?= date("H:i",strtotime($data['attention_date'])) ?></span>
							 </span>
						 </p>
						 <p style="font-size:9px;">
							<span style="font-family:arial,helvetica,sans-serif;">
								<span style="font-size:11px;"><strong>ASUNTO: </strong><?= $data['incident'] ?></span>
							 </span>
						 </p>
						</td>
					</tr>
				</tbody>
			</table>
			<br>
			<table width="100%">
				<tbody>
					<tr>
						<td style="padding: 0px; width: 100%;">
							<div class="contenedor-qr">
								<img src="<?= generate_qr($name_qr,5,"H",3); ?>">
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</body>
</html>
