<?php
    require 'Libraries/dompdf/vendor/autoload.php';
    use Dompdf\Dompdf;
    class Invoice extends Controllers{
        public function __construct(){
            parent::__construct();
        }
        public function document(string $params){
            if(!empty($params)){
                 $arrParams = explode(",",$params);
                 $idbill = decrypt($arrParams[0]);
                 $idbill = intval($idbill);
                 $blade_type = strClean($arrParams[1]);
                 if(empty($blade_type)){
                   $type = empty($_SESSION['businessData']['print_format']) ? 'ticket': $_SESSION['businessData']['print_format'];
                 }else{
                   $type = strtolower($blade_type);
                 }
                 if(is_numeric($idbill)){
                     $data = $this->model->document($idbill);
                     if(empty($data)){
                        echo "Información no ha sido encontrada";
                     }else{
                       ob_end_clean();
                       if($type == 'a4'){
                          $html = redirect_pdf("Resources/reports/pdf/invoice_a4",$data);
                          $customPaper = 'A4';
                       }else{
                          $html = redirect_pdf("Resources/reports/pdf/invoice_ticket",$data);
                          $customPaper = array(0,0,204,700);
                       }
                       $dompdf = new Dompdf();

                       $options = $dompdf->getOptions();
                       $options->set(array('isRemoteEnabled' => true));
                       $dompdf->setOptions($options);

                       $dompdf->loadHtml($html);
                       $orientation = 'portrait';

                       $dompdf->setPaper($customPaper,$orientation);
                       $dompdf->render();
                       $correlative = str_pad($data['bill']['correlative'],5,"0", STR_PAD_LEFT);
                       $voucher = $data['bill']['serie'] .'-'.$correlative;
                       $dompdf->stream($voucher.'.pdf',array("Attachment" => true));
                     }
                 }else{
                       echo "Información no valida";
                 }
             }else{
               echo "Información no ha sido encontrada";
             }
        }
    }
