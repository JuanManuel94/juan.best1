<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Notificaion</title>
	</head>
    <body>
    	<div style="background-color: #fff;-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none;-webkit-text-size-adjust: none;width: 100% !important;height: 100%;line-height: 1.6;margin: 0; padding: 0;font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;box-sizing: border-box;font-size: 14px;">
    		<table class="body-wrap" style="background-color: #fff;width: 100%;">
    			<tbody>
    				<tr>
    					<td>&nbsp;</td>
    					<td style="display: block !important; max-width: 600px !important;margin: 0 auto !important;clear: both !important;" width="600">
    						<div style="max-width: 600px;margin: 0 auto;display: block;padding: 20px;">
    							<table cellpadding="0" cellspacing="0" style=" background: #fff;border: 1px solid #e9e9e9;border-radius: 3px;" width="100%">
    								<tbody>
    									<tr>
    										<td style="vertical-align: middle;font-size: 16px;color: #fff;font-weight: 500;padding: 15px;border-radius: 3px 3px 0 0;border-bottom: 1px solid #e9e9e9;text-align:center;">
    									        <img alt="Logo" src="<?= $information['logo']; ?>" style="max-height:65px;">
    										</td>
    									</tr>
    						            <tr>
    							            <td style="vertical-align: top;padding: 25px;">
                    							<table cellpadding="0" cellspacing="0" width="100%">
                    								<tbody>
                    									<tr>
                    										<td style="vertical-align: top;padding: 0px;">Hola&nbsp;,<strong><?= $information['name_addressee'] ?>.</strong></td>
                    									</tr>
														<?php if($information['state'] == "CONSTANCIA DE PAGO"):
															$money = ($information['total_invoice'] >= 2) ? $information['money_plural'] : $information['money'];
														?>
															<tr>
																<td style="vertical-align: top;padding: 0 0 10px;">
																	<p><span style="font-size:12px;">Tiene un recibo de pago con <strong><?= $information['voucher'] ?></strong> N° <strong><?= $information['invoice'] ?></strong>, registrado el dia <strong><?= date_letters($information['issue']) ?></strong>, teniendo un total de <strong><?= $_SESSION['businessData']['symbol'].format_money($information['total_invoice']) ?></strong> <?= ucwords(strtolower($money)) ?>.</span></p>

																	<p style="line-height: 8px;"><span style="font-size:12px;"><strong>DETALLES DE LA OPERACIÓN</strong></span></p>
																	<p style="line-height: 8px;"><span style="font-size:12px;"><strong>Nº Transacción:</strong> <?= $information['transaction'] ?></span></p>
																	<p style="line-height: 8px;"><span style="font-size:12px;"><strong>Fecha de Emisión:</strong> <?= date("d/m/Y",strtotime($information['issue'])) ?></span></p>
																	<p style="line-height: 8px;"><span style="font-size:12px;"><strong>Fecha de Vencimiento:</strong> <?= date("d/m/Y",strtotime($information['expiration'])) ?></span></p>
																	<p style="line-height: 8px;"><span style="font-size:12px;"><strong>Subtotal:</strong> <?= $_SESSION['businessData']['symbol'].format_money($information['sub_invoice']) ?></span></p>
																	<p style="line-height: 8px;"><span style="font-size:12px;"><strong>Descuento:</strong> <?= $_SESSION['businessData']['symbol'].format_money($information['dis_invoice']) ?></span></p>
																	<p style="line-height: 8px;"><span style="font-size:12px;"><strong>Total:</strong> <?= $_SESSION['businessData']['symbol'].format_money($information['total_invoice']) ?></span></p>
																</td>
															</tr>
									                    <?php endif; ?>
														<?php if($information['state'] == "FACTURA PENDIENTE DE PAGO"):
															$money = ($information['total_invoice'] >= 2) ? $information['money_plural'] : $information['money'];
														?>
															<tr>
																<td style="vertical-align: top;padding: 0 0 10px;">
																	<p><span style="font-size:12px;">Para comunicarle que tiene un(a) <strong><?= $information['voucher'] ?></strong> N° <strong><?= $information['invoice'] ?></strong>, en estado pendiente de pago y vencera el dia <strong><?= date_letters($information['expiration']) ?></strong> con un total de <strong><?= $_SESSION['businessData']['symbol'].format_money($information['total_invoice']) ?></strong> <?= ucwords(strtolower($money)) ?>, si tiene alguna inquietud, por favor comunicarse al número <?= $information['mobile'] ?>.</span></p>
																	<p><span style="font-size:12px;"><strong>Lugares de pago:</strong></span></p>
																	<ul>
																		<li><span style="font-size:12px;"><strong>Agente Kasnet:&nbsp;</strong>Banco de la Nación,Bbva,Bcp y Interbank.</span></li>
																		<li><span style="font-size:12px;"><strong>Transferencia o Deposito:&nbsp;</strong>Recordamos enviar captura o foto del vaucher de pago al correo <strong><?= $information['sender'] ?></strong> o al whatsapp <strong><?= $information['mobile'] ?></strong>.</span></li>
																		<li><span style="font-size:12px;"><strong>Oficina central:&nbsp;</strong>Realizar el pago al contado en nuestra oficina en <?= ucwords(strtolower($information['address'])) ?>.</span></li>
																	</ul>
																</td>
															</tr>
														<?php endif; ?>
														<?php if($information['state'] == "FACTURA VENCIDA CON PENDIENTE DE PAGO"):
															$money = ($information['total_invoice'] >= 2) ? $information['money_plural'] : $information['money'];
														?>
															<tr>
																<td style="vertical-align: top;padding: 0 0 10px;">
																	<p><span style="font-size:12px;">Estimado cliente, queremos informale que según nuestro registros tiene un(a) <strong><?= $information['voucher'] ?></strong> N° <strong><?= $information['invoice'] ?></strong>, que vencio el dia <strong><?= date_letters($information['expiration']) ?></strong> con un saldo de <strong><?= $_SESSION['businessData']['symbol'].format_money($information['total_invoice']) ?></strong> <?= ucwords(strtolower($money)) ?>.</span></p>

																	<p><span style="font-size:12px;">Si este valor ya ha sido cancelado, por favor hacerlo saber al correo <strong><?= $information['sender'] ?> o comunicarse al <strong><?= $information['mobile'] ?></strong>.</span></p>
																</td>
															</tr>
														<?php endif; ?>
    								                </tbody>
    							                </table>
    							            </td>
    						            </tr>
    					            </tbody>
    				            </table>
                				<div style="width: 100%;clear: both;color: #999;padding: 20px;">
                    				<table width="100%">
                    					<tbody>
                    						<tr>
                    							<td class="aligncenter" style="vertical-align: top; padding: 0 -100px -20px; font-size: 12px;">Este es un email automático, si tienes cualquier tipo de duda ponte en contacto con nosotros a través de nuestro servicio de atención al cliente al <?= $information['mobile'] ?>, por favor no respondas a este mensaje.</td>
                    						</tr>
                    					</tbody>
                    				</table>
                				</div>
    				        </div>
    				    </td>
    				    <td>&nbsp;</td>
    			    </tr>
    		    </tbody>
    	    </table>
    	</div>
    </body>
</html>
