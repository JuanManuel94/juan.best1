<?php
    class Advice extends Controllers{
        public function __construct(){
            parent::__construct();
  			    session_start();
			if(empty($_SESSION['login'])){
				header('Location: '.base_url().'/login');
				die();
			}
			consent_permission(EMAIL);
        }
        public function advice(){
            if(empty($_SESSION['permits_module']['v'])){
                header("Location:".base_url().'/dashboard');
            }
            $data['page_name'] = "Correos";
            $data['page_title'] = "Gestión de correos";
            $data['home_page'] = "Dashboard";
            $data['previous_page'] = "Clientes";
            $data['actual_page'] = "Correos";
            $data['page_functions_js'] = "advice.js";
            $this->views->getView($this,"advice",$data);
        }
        public function list_records(){
            if($_SESSION['permits_module']['v']){
                $n = 1;
                $data = $this->model->list_records();
                for($i=0; $i < count($data); $i++){
                    $resend = '';
                    $data[$i]['n'] = $n++;
                    $data[$i]['client'] = $data[$i]['names']." ".$data[$i]['surnames'];
                    if($_SESSION['permits_module']['r']){
                      $resend = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Reenviar correo" onclick="resend(\''.encrypt($data[$i]['billid']).'\',\''.encrypt($data[$i]['id']).'\')"><i class="far fa-share-square"></i></a>';
                      $resend_2 = '<a href="javascript:;" class="dropdown-item" onclick="resend(\''.encrypt($data[$i]['billid']).'\',\''.encrypt($data[$i]['id']).'\')"><i class="far fa-share-square mr-1"></i>Reenviar correo</a>';
                    }else{
                      $resend = '';
                      $resend_2 = '';
                    }
                    $options = '<div class="hidden-sm hidden-xs action-buttons">'.$resend.'</div>';
                    $options .='<div class="hidden-md hidden-lg"><div class="dropdown">
                    <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
                      '.$resend_2.'
                    </div>
                    </div></div>';
                    $data[$i]['options'] = $options;
                }
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function resend(string $params){
            if(empty($params)){
                header("Location:".base_url()."/advice");
            }else{
                $arrParams = explode(",",$params);
                $idbill = decrypt($arrParams[0]);
                $idbill = intval($idbill);
                $idemail = decrypt($arrParams[1]);
                $idemail = intval($idemail);
                $sender = $_SESSION['businessData']['email'];
                $consult_email = $this->model->select_record($idemail);
                $datetime = date("Y-m-d H:i:s");
                if(!empty($consult_email)){
                    if($idbill > 0){
                        $data = $this->model->data_pdf($idbill);
                        if(!empty($data)){
                            if(empty($data['bill']['email'])){
                                $answer = array('status' => 'not_exist', 'msg' => 'El cliente no tiene correo electronico.');
                            }else{
                                $correlative = str_pad($data['bill']['correlative'],7,"0", STR_PAD_LEFT);
                                $voucher = $data['bill']['serie'] .'-'.$correlative;
                                $email = array(
                                    'logo' => $_SESSION['businessData']['logo_email'],//logo empresa
                                    'name_sender' => $_SESSION['businessData']['business_name'],//nombre remitente
                                    'sender' => $sender,//remitente
                                    'password' => $_SESSION['businessData']['password'],//contraseña
                                    'mobile' => $_SESSION['businessData']['mobile'],//celular
                                    'address' => $_SESSION['businessData']['address'],//celular
                                    'host' => $_SESSION['businessData']['server_host'],//host
                                    'port' => $_SESSION['businessData']['port'],//puerto
                                    'addressee' => $data['bill']['email'],//destinatario
                                    'name_addressee' => $data['bill']['names']." ".$data['bill']['surnames'],//nombre destinatario
                                    'affair' =>  $consult_email['affair'],//asunto
                                    'add_pdf' => $consult_email['files'],//si mandara un pdf
                                    'type_pdf' => $consult_email['type_file'],//tipo de pdf ticket o a4
                                    'data' => $data,//pdf
                                    'state' =>  $consult_email['affair'],//estado de la factura
                                    'voucher' =>  $data['bill']['voucher'],//comprobante
                                    'invoice' =>  $voucher,//codigo de factura
                                    'transaction' =>  $data['bill']['internal_code'],//codigo de factura
                                    'sub_invoice' =>  $data['bill']['subtotal'],//total de factura
                                    'dis_invoice' =>  $data['bill']['discount'],//total de factura
                                    'total_invoice' =>  $data['bill']['total'],//total de factura
                                    'issue' =>  $data['bill']['date_issue'],// fecha de emision
                                    'expiration' =>  $data['bill']['expiration_date'],// fecha de vencimiento
                                    'money_plural' =>  $_SESSION['businessData']['money_plural'],// moneda en plural
                                    'money' =>  $_SESSION['businessData']['money']// moneda en singular
                                );
                                $result = sendMail($email,$consult_email['template_email']);
                                if($result === true){
                                    $state_email = 1;
                                    $request = $this->model->register_email($consult_email['clientid'],$idbill, strtoupper($consult_email['affair']),$sender,$consult_email['type_file'],$consult_email['type_file'],$consult_email['template_email'],$datetime,$state_email);
                                    if($request == 'success'){
                                        $answer = array('status' => 'success', 'msg' => "Se reenvio el correo correctamente.");
                                    }else{
                                        $answer = array('status' => 'error', 'msg' => "Hubo un error,reenvié el correo nuevamente.");
                                    }
                                }else{
                                    $state_email = 2;
                                    $this->model->register_email($consult_email['clientid'],$idbill, strtoupper($consult_email['affair']),$sender,$consult_email['type_file'],$consult_email['type_file'],$consult_email['template_email'],$datetime,$state_email);
                                    $answer = array('status' => 'error', 'msg' => "Hubo un error,reenvié el correo nuevamente.");
                                }
                            }
                        }else{
                            $answer = array('status' => 'error', 'msg' => 'La información buscada no existe.');
                        }
                    }
                }else{
                    $answer = array('status' => 'error', 'msg' => 'La información buscada no existe.');
                }
                echo json_encode($answer,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
    }
