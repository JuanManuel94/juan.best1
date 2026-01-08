<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Restablecer contraseña</title>
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
                    										<td style="vertical-align: top;padding: 0px;">Para restablecer su contraseña, haga clic en el enlace a continuación.</td>
                    									</tr>
                                                        <br>
														<tr>
															<td style="vertical-align: top;padding: 0px;text-justify">
                                                                <a href="<?= $information['url_recovery']; ?>" target="_blank">Restablecer su contraseña</a>
															</td>
														</tr>
                                                        <br>
                                                        <tr>
                                                            <td style="vertical-align: top;padding: 0px;">
                                                                Si tiene problemas, intente copiar y pegar la siguiente URL en su navegador:<br><?= $information['url_recovery']; ?>
                                                            </td>
                                                        </tr>
                                                        <br>
                                                        <tr>
                                                            <td style="vertical-align: top;padding: 0px;">
                                                                <strong>ATENTAMENTE</strong><br><?= $information['name_sender']; ?>
                                                            </td>
                                                        </tr>
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
