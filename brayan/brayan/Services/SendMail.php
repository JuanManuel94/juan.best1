<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;

class SendMail {

  public static function message($information, $template) {
    $mail = new PHPMailer(true);
    ob_start();
    require("Views/Resources/emails/{$template}.php");
    $data_template = ob_get_clean();

    /* Valores del remitente */
    $name_sender    = $information['name_sender'];
    $sender         = $information['sender'];
    $password       = $information['password'];

    /* Valores del servidor */
    $host           = $information['host'];
    $port           = $information['port'];

    if($port == 465) $serverhost = PHPMailer::ENCRYPTION_SMTPS;

    if($port == 587) $serverhost = PHPMailer::ENCRYPTION_STARTTLS;

    /* Asunto */
    $affair         = $information['affair'];

    /* Valores del destinatario */
    $addressee      = $information['addressee'];
    $name_addressee = $information['name_addressee'];

    if (!empty($information['add_pdf']) && $information['add_pdf'] == true) {
      /* Data del pdf */
      $data           = $information['data'];

      /* Valores del pdf */
      $state          = $information['state'];
      $invoice        = $information['invoice'];
      $total_invoice  = $information['total_invoice'];
      $issue          = $information['issue'];

      /* Nombre del pdf */
      $pdf_name = $invoice.'.pdf';

      /* fichero */
      $file_name = 'Assets/uploads/pdf/'.$pdf_name;

      /* tipo de pdf formato ticket o A4 */
      if($information['type_pdf'] == 'ticket'){
          $orientation = 'portrait';
          $customPaper = [0, 0, 204, 700];
          $pdf_template = 'invoice_ticket';
      }else{
          $orientation = 'portrait';
          $customPaper = 'A4';
          $pdf_template = 'invoice_a4';
      }

      /* plantilla pdf */
      $pdf = redirect_pdf("Resources/reports/pdf/".$pdf_template,$data);

      /* Instacion a la libreria dompdf */
      $dompdf = new Dompdf();

      /* Para que se muestren las imagenes */
      $options = $dompdf->getOptions();
      $options->set(array('isRemoteEnabled' => true));
      $dompdf->setOptions($options);

      /* creo el pdf */
      $dompdf->loadHtml($pdf);
      $dompdf->setPaper($customPaper,$orientation);
      $dompdf->render();
      $file = $dompdf->output();

      /* lo guardo en assets para ser enviado */
      file_put_contents($file_name,$file);
    }
        
    try {
      $mail->SMTPDebug  = 0;
      $mail->isSMTP();
      $mail->Host       = $host;
      $mail->SMTPAuth   = true;
      $mail->Username   = $sender;
      $mail->Password   = $password;
      $mail->SMTPSecure = $serverhost;
      $mail->Port       = $port;

      $mail->setFrom($sender,$name_sender);
      $mail->addAddress($addressee);

      $mail->CharSet = 'UTF-8';

      $mail->isHTML(true);

      if(!empty($information['add_pdf'])){
          if($information['add_pdf'] == true){
              $mail->AddAttachment($file_name);
          }
      }

      $mail->Subject = $affair;
      $mail->Body    = $data_template;

      if(!$mail->send()) throw new Exception("{$mail->ErrorInfo}:{$addressee}");

      if (isset($information['add_pdf']) && $information['add_pdf'] == true) {
        if(file_exists($file_name)) {
          unlink($file_name);
        }
      }
          
      return [
        "status" => true,
        "message" => "Envio exitoso"
      ];
    } catch (Exception $e) {
      return [
        "status" => false,
        "message" => $e->getMessage()
      ];
    }
  }
}