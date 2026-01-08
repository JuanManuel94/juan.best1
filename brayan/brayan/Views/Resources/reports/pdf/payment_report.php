<?php
	$start = date("d/m/Y",strtotime($data['start']));
	$end = date("d/m/Y",strtotime($data['end']));
	$payments = $data['data'];
	$logo = 'Assets/uploads/business/'.$_SESSION['businessData']['logotyope'];
 ?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Lista de cobros</title>
	<style>
		table{
			width: 100%;
		}
		table td, table th{
			font-size: 12px;
		}
		h4{
			margin-bottom: 0px;
		}
		.text-center{
			text-align: center;
		}
		.text-right{
			text-align: right;
		}
    .text-left{
			text-align: left;
		}
		.wd33{
			width: 33.33%;
		}
		.wd10{
			width: 10%;
		}
		.wd15{
			width: 15%;
		}
    .wd20{
			width: 20%;
		}
		.wd40{
			width: 40%;
		}
    .wd45{
			width: 45%;
		}
		.wd55{
			width: 55%;
		}
    .wd80{
			width: 80%;
		}
		.tbl-detalle{
			border-collapse: collapse;
		}
		.tbl-detalle thead th{
			padding: 5px;
			background-color: #2D3036;
			color: #FFF;
		}
		.tbl-detalle tbody td{
			border-bottom: 1px solid #CCC;
			padding: 5px;
		}
		.tbl-detalle tfoot td{
			padding: 5px;
		}
	</style>
</head>
<body>
	<table class="tbl-hader">
		<tbody>
			<tr>
				<td class="wd80">
          <h2><strong>COBRANZA REALIZADA ENTRE EL <?= $start ?> Y <?= $end ?> </strong></h2>
				</td>
				<td class="text-center wd20">
          <?php if(file_exists($logo)): ?>
            <img src="<?= base_url()."/".$logo ?>" width="200px">
          <?php else: ?>
            <img src="<?= base_style().'/images/logotypes/superwisp.png' ?>" width="200px">
          <?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
	<br>
	<table class="tbl-detalle">
		<thead>
			<tr>
        <th class="text-center">COD</th>
				<th class="text-left">CLIENTE</th>
				<th class="text-centert">Nº FACT.</th>
				<th class="text-center">FECHA</th>
				<th class="text-center">TOTAL FACT.</th>
        <th class="text-center">PAGADO</th>
        <th class="text-center">USUARIO</th>
        <th class="text-center">ESTADO</th>
			</tr>
		</thead>
		<tbody>
      <?php if(!empty($payments)): ?>
      <?php foreach ($payments AS $row) : ?>
      <?php
        if($row['state'] == 1){
            $state = 'RECIBIDO';
        }else if($row['state'] == 2){
            $state = 'CANCELADO';
        }
      ?>
        <tr>
          <td align="center"><?= $row["internal_code"]?></td>
          <td align="left"><?= $row["client"] ?></td>
          <td align="center"><?= str_pad($row['correlative'],7,"0", STR_PAD_LEFT) ?></td>
          <td align="center"><?= date("d/m/Y H:i", strtotime($row['payment_date'])) ?></td>
          <td align="center"><?= $_SESSION['businessData']['symbol'].format_money($row['bill_total']) ?></td>
          <td align="center"><?= $_SESSION['businessData']['symbol'].format_money($row['amount_paid']) ?></td>
          <td align="center"><?= $row["user"] ?></td>
          <td align="center"><?= $state ?></td>
        </tr>
      <?php endforeach ?>
      <?php else: ?>
        <tr>
            <td class="text-center" colspan="7">NO HAY REGISTROS.</td>
        </tr>
      <?php endif; ?>
		</tbody>
	</table>
</body>
</html>
