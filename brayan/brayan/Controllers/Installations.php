<?php
require 'Libraries/resize/vendor/autoload.php';
require 'Libraries/dompdf/vendor/autoload.php';
use Verot\Upload\Upload;
use Dompdf\Dompdf;
    class Installations extends Controllers{
        public function __construct(){
          parent::__construct();
    			session_start();
    			if(empty($_SESSION['login'])){
    				header('Location: '.base_url().'/login');
    				die();
    			}
			    consent_permission(INSTALLATIONS);
        }
        public function installations(){
            if(empty($_SESSION['permits_module']['v'])){
                header("Location:".base_url().'/dashboard');
            }
            $data['page_name'] = "Instalaciones";
            $data['page_title'] = "Gestión de Instalaciones";
            $data['home_page'] = "Dashboard";
            $data['previous_page'] = "Clientes";
            $data['actual_page'] = "Instalaciones";
            $data['page_functions_js'] = "installations.js";
            $this->views->getView($this,"installations",$data);
        }
        public function tools(string $idfacility){
           if(empty($_SESSION['permits_module']['r'])){
             header("Location:".base_url().'/installations');
           }
           $idfacility = decrypt($idfacility);
           $idfacility = intval($idfacility);
           if($idfacility > 0){
               $information = $this->model->select_record($idfacility);
               if(empty($information)){
                 header("Location:".base_url().'/installations');
               }else{
                 if($information['state'] == 5){
                   header("Location:".base_url().'/installations');
                 }else{
                   $data['page_name'] = "Materiales de Instalación";
                   $data['page_title'] = "<b>Materiales Instalación<small> (".ucwords(strtolower($information['names']." ".$information['surnames'])).")</small></b>";
                   $data['home_page'] = "Dashboard";
                   $data['previous_page'] = "Instalaciones";
                   $data['actual_page'] = "Materiales";
                   $data['page_functions_js'] = "tools.js";
                   $data['information'] = $information;
                   $this->views->getView($this,"tools",$data);
                 }
               }
           }else{
             header("Location:".base_url().'/installations');
           }
           die();
        }
        public function attend(string $idfacility){
            if(empty($_SESSION['permits_module']['a'])){
                header("Location:".base_url().'/installations');
            }
            $idfacility = decrypt($idfacility);
            $idfacility = intval($idfacility);
            if($idfacility > 0){
              $information = $this->model->select_record($idfacility);
              if(empty($information)){
                header("Location:".base_url().'/installations');
              }else{
                if($information['state'] == 1 || $information['state'] == 5){
                  header("Location:".base_url().'/installations');
                }else if($information['state'] == 2 || $information['state'] == 3 || $information['state'] == 4){
                  $data['page_name'] = "Proceso de Instalación";
                  $data['page_title'] = $information['names']." ".$information['surnames'];
                  $data['home_page'] = "Dashboard";
                  $data['previous_page'] = "Instalaciones";
                  $data['actual_page'] = "Proceso de instalación";
                  $data['page_functions_js'] = "attend.js";
                  $data['information'] = $information;
                  if($_SESSION['userData']['profileid'] == ADMINISTRATOR){
                    if($information['state'] == 2 || $information['state'] == 4){
                      $datetime = date("Y-m-d H:i:s");
                      $this->model->open_facility($idfacility,$datetime,3);
                      $this->model->reassign_technical($idfacility,$_SESSION['idUser']);
                    }else{
                      $this->model->reassign_technical($idfacility,$_SESSION['idUser']);
                    }
                    $this->views->getView($this,"attend",$data);
                  }else if($_SESSION['userData']['profileid'] == TECHNICAL){
                    if($information['technical'] == $_SESSION['idUser']){
                      if($information['state'] == 2 || $information['state'] == 4){
                        $datetime = date("Y-m-d H:i:s");
                        $this->model->open_facility($idfacility,$datetime,3);
                        $this->model->reassign_technical($idfacility,$_SESSION['idUser']);
                      }
                      $this->views->getView($this,"attend",$data);
                    }else if($information['technical'] == 0){
                      if($information['state'] == 2 || $information['state'] == 4){
                        $datetime = date("Y-m-d H:i:s");
                        $this->model->open_facility($idfacility,$datetime,3);
                        $this->model->reassign_technical($idfacility,$_SESSION['idUser']);
                      }
                      $this->views->getView($this,"attend",$data);
                    }else{
                      header("Location:".base_url().'/installations');
                    }
                  }else{
                    header("Location:".base_url().'/installations');
                  }
                }else{
                  header("Location:".base_url().'/installations');
                }
              }
            }else{
              header("Location:".base_url().'/installations');
            }
            die();
        }
        public function location(string $idclient){
          if(empty($_SESSION['permits_module']['v'])){
              header("Location:".base_url().'/dashboard');
          }
          $idclient = decrypt($idclient);
          $idclient = intval($idclient);
          if($idclient > 0){
            $data['page_name'] = "Ubicación del cliente";
            $data['client'] = $this->model->select_client($idclient);
            $data['page_functions_js'] = "customer_location.js";
            $this->views->getView($this,"location",$data);
          }else{
            header("Location:".base_url()."/dashboard");
          }
          die();
        }
        public function list_records(string $params){
            if($_SESSION['permits_module']['v']){
                $n = 1;
                if(!empty($params)){
                    $arrParams = explode("-",$params);
                    $state = intval($arrParams[0]);
                }else{
                    $state = 0;
                }
                $data = $this->model->list_records($state);
                for($i=0; $i < count($data); $i++){
                    $data[$i]['profile_user'] = $_SESSION['userData']['profileid'];
                    /* ID CONTRATO */
                    $contract = $this->model->select_contract($data[$i]['clientid']);
                    if(empty($contract)){
                      $data[$i]['encrypt_contract'] = "";
                    }else{
                      $data[$i]['encrypt_contract'] = encrypt($contract['id']);
                    }
                    /* DURACION DE TICKET */
                    if($data[$i]['attention_date'] == "0000-00-00 00:00:00" && $data[$i]['closing_date'] == "0000-00-00 00:00:00"){
                      $data[$i]['duration'] = "";
                    }else if(isset($data[$i]['opening_date']) && $data[$i]['closing_date'] == "0000-00-00 00:00:00"){
                      $data[$i]['duration'] = "";
                    }else{
                      $data[$i]['duration'] = ticket_duration($data[$i]['opening_date'],$data[$i]['closing_date']);
                    }
                    /* CELULARES */
                    $mobiles = '';
                    if(!empty($data[$i]['mobile'])){
                        $mobiles .= '<a href="javascript:;" onclick="modal_tools(\''.$_SESSION['businessData']['country_code'].'\',\''.$data[$i]['mobile'].'\',\''.$data[$i]['client'].'\')"><i class="fa fa-mobile mr-1"></i>'.$data[$i]['mobile'].'</a>';
                    }
                    if(!empty($data[$i]['mobile_optional'])){
                        $mobiles .= '<br>';
                        $mobiles .= '<a href="javascript:;" onclick="modal_tools(\''.$_SESSION['businessData']['country_code'].'\',\''.$data[$i]['mobile_optional'].'\',\''.$data[$i]['client'].'\')"><i class="fa fa-mobile mr-1"></i>'.$data[$i]['mobile_optional'].'</a>';
                    }
                    $data[$i]['total'] = $_SESSION['businessData']['symbol'].' '.format_money($data[$i]['cost']);
                    $data[$i]['cellphones'] = $mobiles;
                    $data[$i]['n'] = $n++;
                    $data[$i]['assigned'] = ($data[$i]['technical'] == 0) ? "LIBRE" : $this->model->see_technical($data[$i]['technical']);
                    if($data[$i]['latitud'] && $data[$i]['longitud']){
                      $reference = '<a href="'.base_url().'/installations/location/'.encrypt($data[$i]['clientid']).'">'.$data[$i]['reference'].'</a>';
                      $address = '<a href="'.base_url().'/installations/location/'.encrypt($data[$i]['clientid']).'">'.$data[$i]['address'].'</a>';
                    }else {
                        $reference = $data[$i]['reference'];
                        $address = $data[$i]['address'];
                    }
                    $data[$i]['reference'] = $reference;
                    $data[$i]['address'] = $address;
                    if($_SESSION['permits_module']['a']){
                      if($_SESSION['userData']['profileid'] == ADMINISTRATOR){
                        if($data[$i]['state'] == 2 || $data[$i]['state'] == 4){
                          $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-pencil-alt"></i></a>';
                          $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
                          $attend = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Atender instalación" onclick="attend(\''.encrypt($data[$i]['id']).'\')"><i class="fas fa-check-circle"></i></a>';
                          $attend_2 = '<a href="javascript:;" class="dropdown-item" onclick="attend(\''.encrypt($data[$i]['id']).'\')"><i class="fas fa-check-circle mr-1"></i>Atender instalación</a>';
                        }else if($data[$i]['state'] == 3){
                          $update = '';
                          $update_2 = '';
                          $attend = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Cerrar instalación" onclick="attend(\''.encrypt($data[$i]['id']).'\')"><i class="fas fa-check-circle"></i></a>';
                          $attend_2 = '<a href="javascript:;" class="dropdown-item" onclick="attend(\''.encrypt($data[$i]['id']).'\')"><i class="fas fa-check-circle mr-1"></i>Cerrar instalación</a>';
                        }else{
                          $update = '';
                          $update_2 = '';
                          $attend = '';
                          $attend_2 = '';
                        }
                      }else if($_SESSION['userData']['profileid'] == TECHNICAL){
                        if($data[$i]['state'] == 2 || $data[$i]['state'] == 4){
                          $update = '';
                          $update_2 = '';
                          if($data[$i]['technical'] == 0){
                            $attend = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Atender instalación" onclick="attend(\''.encrypt($data[$i]['id']).'\')"><i class="fas fa-check-circle"></i></a>';
                            $attend_2 = '<a href="javascript:;" class="dropdown-item" onclick="attend(\''.encrypt($data[$i]['id']).'\')"><i class="fas fa-check-circle mr-1"></i>Atender instalación</a>';
                          }else if($data[$i]['technical'] == $_SESSION['idUser']){
                            $attend = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Atender instalación" onclick="attend(\''.encrypt($data[$i]['id']).'\')"><i class="fas fa-check-circle"></i></a>';
                            $attend_2 = '<a href="javascript:;" class="dropdown-item" onclick="attend(\''.encrypt($data[$i]['id']).'\')"><i class="fas fa-check-circle mr-1"></i>Atender instalación</a>';
                          }else{
                            $attend = '';
                            $attend_2 = '';
                          }
                        }else if($data[$i]['state'] == 3){
                          if($data[$i]['technical'] == $_SESSION['idUser']){
                            $update = '';
                            $update_2 = '';
                            $attend = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Cerrar instalación" onclick="attend(\''.encrypt($data[$i]['id']).'\')"><i class="fas fa-check-circle"></i></a>';
                            $attend_2 = '<a href="javascript:;" class="dropdown-item" onclick="attend(\''.encrypt($data[$i]['id']).'\')"><i class="fas fa-check-circle mr-1"></i>Cerrar instalación</a>';
                          }else{
                            $update = '';
                            $update_2 = '';
                            $attend = '';
                            $attend_2 = '';
                          }
                        }else{
                          $update = '';
                          $update_2 = '';
                          $attend = '';
                          $attend_2 = '';
                        }
                      }else{
                        if($data[$i]['state'] == 2 || $data[$i]['state'] == 4){
                          $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-pencil-alt"></i></a>';
                          $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
                          $attend = '';
                          $attend_2 = '';
                        }else{
                          $update = '';
                          $update_2 = '';
                          $attend = '';
                          $attend_2 = '';
                        }
                      }
                    }else{
                      $update = '';
                      $update_2 = '';
                      $attend = '';
                      $attend_2 = '';
                    }
                    if($_SESSION['permits_module']['r']){
                      if($_SESSION['userData']['profileid'] == ADMINISTRATOR){
                        if($data[$i]['state'] == 5){
                          $tools = '';
                          $tools_2 = '';
                        }else{
                          $tools = '<a href="javascript:;" class="purple" data-toggle="tooltip" data-original-title="Agregar materiales" onclick="tools(\''.encrypt($data[$i]['id']).'\')"><i class="fas fa-toolbox"></i></a>';
                          $tools_2 = '<a href="javascript:;" class="dropdown-item" onclick="tools(\''.encrypt($data[$i]['id']).'\')"><i class="fas fa-toolbox mr-1"></i>Agregar materiales</a>';
                        }
                      }else if($_SESSION['userData']['profileid'] == TECHNICAL){
                        if($data[$i]['state'] == 2 || $data[$i]['state'] == 4){
                          $tools = '<a href="javascript:;" class="purple" data-toggle="tooltip" data-original-title="Agregar materiales" onclick="tools(\''.encrypt($data[$i]['id']).'\')"><i class="fas fa-toolbox"></i></a>';
                          $tools_2 = '<a href="javascript:;" class="dropdown-item" onclick="tools(\''.encrypt($data[$i]['id']).'\')"><i class="fas fa-toolbox mr-1"></i>Agregar materiales</a>';
                        }else{
                          $tools = '';
                          $tools_2 = '';
                        }
                      }else{
                        $tools = '';
                        $tools_2 = '';
                      }
                    }else{
                      $tools = '';
                      $tools_2 = '';
                    }
                    if($_SESSION['permits_module']['v']){
                      $view = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Ver instalación" onclick="view(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-eye"></i></a>';
                      $view_2 = '<a href="javascript:;" class="dropdown-item" onclick="view(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-eye mr-1"></i>Ver instalación</a>';
                      
                      $print = '<a href="javascript:;" class="black printer" data-toggle="tooltip" data-original-title="Hoja de instalación" onclick="installation_sheet(\''.encrypt($data[$i]['id']).'\')"><i class="far fa-file-pdf"></i></a>';
                      $print_2 = '<a href="javascript:;" class="dropdown-item" onclick="installation_sheet(\''.encrypt($data[$i]['id']).'\')"><i class="far fa-file-pdf mr-1"></i>Hoja de instalación</a>';
                  
                      if($data[$i]['state'] == 1){
                          $email = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Enviar por correo" onclick="send_email(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-share-square mr-1"></i></a>';
                          $email_2 = '<a href="javascript:;" class="dropdown-item" onclick="send_email(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-share-square mr-1"></i>Enviar por correo</a>';
                      } else {
                          $email = '';
                          $email_2 = '';
                      }
                  } else {
                      $email = '';
                      $email_2 = '';
                  }
                  
                    if($_SESSION['permits_module']['e']){
                      if($_SESSION['userData']['profileid'] == ADMINISTRATOR){
                        if($data[$i]['state'] == 2 || $data[$i]['state'] == 4){
                          $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Cancelar" onclick="cancel(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-ban"></i></a>';
                          $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-ban mr-1"></i>Cancelar</a>';
                        }else{
                          $cancel = '';
                          $cancel_2 = '';
                        }
                      }else{
                        if($data[$i]['state'] == 2 || $data[$i]['state'] == 4){
                          if($data[$i]['technical'] == 0){
                            $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Cancelar" onclick="cancel(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-ban"></i></a>';
                            $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-ban mr-1"></i>Cancelar</a>';
                          }else{
                            $cancel = '';
                            $cancel_2 = '';
                          }
                        }else{
                          $cancel = '';
                          $cancel_2 = '';
                        }
                      }
                    }else{
                      $cancel = '';
                      $cancel_2 = '';
                    }
                    $options = '<div class="hidden-sm hidden-xs action-buttons">'.$view.$update.$attend.$tools.$print.$cancel.$email.'</div>';
                    $options .='<div class="hidden-md hidden-lg"><div class="dropdown">
                    <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
                      '.$view_2.$update_2.$attend_2.$tools_2.$print_2.$cancel_2.$email_2.'
                    </div>
                    </div></div>';
                    $data[$i]['options'] = $options;
                }
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function select_record(string $idfacility){
            if($_SESSION['permits_module']['v']){
                $idfacility = decrypt($idfacility);
                $idfacility = intval($idfacility);
                if($idfacility > 0){
                    $data = $this->model->select_record($idfacility);
                    if(empty($data)){
                      $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
                    }else{
                      $data['encrypt_id'] = encrypt($data['id']);
                      $data['encrypt_client'] = encrypt($data['clientid']);
                      $data['encrypt_technical'] = ($data['technical'] == 0) ? 0 : encrypt($data['technical']);
                      $answer = array('status' => 'success', 'data' => $data);
                    }
                }else{
                  $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
                }
                echo json_encode($answer,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function select_client(string $idclient){
            if($_SESSION['permits_module']['v']){
                $idclient = decrypt($idclient);
                $idclient = intval($idclient);
                if($idclient > 0){
                    $data = $this->model->select_client($idclient);
                    if(empty($data)){
                        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
                    }else{
                        $data['country_code'] = $_SESSION['businessData']['country_code'];
                        $answer = array('status' => 'success', 'data' => $data);
                    }
                    echo json_encode($answer,JSON_UNESCAPED_UNICODE);
                }
            }
            die();
        }
        public function action(){
            if($_POST){
                if(empty($_POST['listClients']) || empty($_POST['insDate'])){
                  $response = array("status" => 'errror', "msg" => 'Campos señalados son obligatorios.');
                }else{
                    $id = decrypt($_POST['idfacility']);
                    $id =  intval($id);
                    $client = decrypt($_POST['listClients']);
                    $client =  intval($client);
                    $user = intval($_SESSION['idUser']);
                    $dateFacility = DateTime::createFromFormat('d/m/Y H:i', $_POST['insDate']);
                    $attention = $dateFacility->format('Y-m-d H:i:s');
                    $datetime = date("Y-m-d H:i:s");
                    //Desencryptar idtecnico
                    if($_POST['listTechnical'] == "0"){
                      $technical = 0;
                    }else{
                      $technical = decrypt($_POST['listTechnical']);
                      $technical =  intval($technical);
                    }
                    $price = empty($_POST['instPrice']) ? 0 : strClean($_POST['instPrice']);
                    $detail = strtoupper(strClean($_POST['detail']));

                    if($id == 0){
                      $option = 1;
                      if($_SESSION['permits_module']['r']){
                        $request = $this->model->create($client,$user,$technical,$attention,$price,$detail,$datetime);
                      }
                    }else{
                      $option = 2;
                      if($_SESSION['permits_module']['a']){
                        $request = $this->model->modify($id,$technical,$attention,$price,$detail);
                      }
                    }
                    if($request == "success"){
                      if($option == 1){
                        $response = array('status' => 'success', 'msg' => 'Se ha registrado la instalación exitosamente.');
                      }else{
                        $response = array('status' => 'success', 'msg' => 'Se ha actualizado la instalación exitosamente.');
                      }
                    }else if($request == 'exists'){
                      $response = array('status' => 'exists', 'msg' => 'El cliente ya tiene una instalación.');
                    }else{
                      $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operación, intentelo nuevamente.');
                    }
                }
                echo json_encode($response,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function complete_installation(){
          if($_POST){
            if(empty($_POST['idfacility']) || empty($_POST['idclient']) || empty($_POST['latitud']) || empty($_POST['longitud'])){
              $response = array("status" => 'errror', "msg" => 'Campos señalados son obligatorios.');
            }else{
              $iduser = intval($_SESSION['idUser']);
              $facility = decrypt($_POST['idfacility']);
              $facility =  intval($facility);
              $client = decrypt($_POST['idclient']);
              $client =  intval($client);
              $latitude = strClean($_POST['latitud']);
              $longitude = strClean($_POST['longitud']);
              $radio_option = strClean($_POST['radio_option']);
              $observation = strtoupper(strClean($_POST['observation']));
              $red_type = strClean($_POST['red_type']);
              $closing_date = date("Y-m-d H:i:s");
              if($_SESSION['permits_module']['a']){
                $consult = $this->model->select_record($facility);
                $opening_date = $consult['opening_date'];
                $attention_date = $consult['attention_date'];
                $technical = $consult['technical'];
                $state_facility = $consult['state'];
                $state = ($radio_option == 1) ? 1 : 2;
                if($state_facility == 1){
                  $response = array("status" => 'info', "msg" => 'La instalación ya fue completada.');
                }else if($state_facility == 3){
                  if($_SESSION['userData']['profileid'] == ADMINISTRATOR){
                    $request = $this->model->complete_installation($facility,$iduser,$opening_date,$closing_date,$observation,$state,$red_type);
                    if($request == "success"){
                      if($radio_option == 1){
                        // PASA A ESTO ACTIVO EL CLIENTE ENCASO NO SEA GRATIS
                        $consult_contract = $this->model->select_contract($client);
                        if($consult_contract['state'] !== 5){
                          $this->model->modify_contract($consult_contract['id']);
                        }
                        //CULMINO INSTLACION
                        $this->model->close_facility($facility,$closing_date,1);
                        //ASIGNO TECNICO
                        $this->model->reassign_technical($facility,$iduser);
                        //AGREGO COORDENADAS AL CLIENTE
                        $this->model->modify_client($client,$latitude,$longitude);
                        //GENERAR FACTURA DE CLIENTE
                        $voucher = 1;
                        /* FECHA DE EMISION */
                        $issue = date("Y-m-d");
                        //FACTURA INSTALACION
                        $serie = $this->model->returnSerie($voucher);
                        $total = $this->model->returnCodeBill();
                        if($total == 0){
                          $code = "V00001";
                        }else{
                          $max = $this->model->generateCodeBill();
                          $code = "V".substr((substr($max,1)+100001),1);
                        }
                        $num_corre = $this->model->returnCorrelative($voucher,$serie);
                        if(empty($num_corre)){
                          $correlative = 1;
                        }else{
                          $correlative =  $this->model->returnUsed($voucher,$serie);
                        }
                        $type = 1;
                        $discount = 0;
                        $subtotal = 0;
                        $materials = $this->model->list_materials($facility);
                        for ($i=0; $i < COUNT($materials) ; $i++) {
                          $subtotal = $subtotal + $materials[$i]['total'];
                        }
                        $cost_facility = $consult['cost'];
                        $total = $subtotal + $cost_facility;
                        if($cost_facility >= 1 && $subtotal >= 1){
                          $request_bill = $this->model->create_bill($iduser,$client,$voucher,$serie,$code,$correlative,$issue,$issue,"0000-00-00",$total,0,$total,$type);
                          if($request_bill == "success"){
                            $this->model->modify_available($voucher,$serie);
                            $idbill = $this->model->returnBill();
                            $this->model->create_datailBill($idbill,$type,0,"SERVICIO DE INSTALACIÓN",1,$cost_facility,$cost_facility);
                            for ($i=0; $i < COUNT($materials) ; $i++) {
                              $idproduct = $materials[$i]['productid'];
                              $product = $materials[$i]['product'];
                              $quantity = $materials[$i]['quantity'];
                              $price_pro = $materials[$i]['price'];
                              $total_pro = $materials[$i]['total'];
                              $this->model->create_datailBill($idbill,$type,$idproduct,$product,$quantity,$price_pro,$total_pro);
                            }
                          }
                        }else if($cost_facility >= 1 && $subtotal == 0){
                          $request_bill = $this->model->create_bill($iduser,$client,$voucher,$serie,$code,$correlative,$issue,$issue,"0000-00-00",$total,0,$total,$type);
                          if($request_bill == "success"){
                            $this->model->modify_available($voucher,$serie);
                            $idbill = $this->model->returnBill();
                            $this->model->create_datailBill($idbill,$type,0,"SERVICIO DE INSTALACIÓN",1,$cost_facility,$cost_facility);
                          }
                        }else if($cost_facility == 0 && $subtotal >= 1){
                          $request_bill = $this->model->create_bill($iduser,$client,$voucher,$serie,$code,$correlative,$issue,$issue,"0000-00-00",$total,0,$total,$type);
                          if($request_bill == "success"){
                            $this->model->modify_available($voucher,$serie);
                            $idbill = $this->model->returnBill();
                            for ($i=0; $i < COUNT($materials) ; $i++) {
                              $idproduct = $materials[$i]['productid'];
                              $product = $materials[$i]['product'];
                              $quantity = $materials[$i]['quantity'];
                              $price_pro = $materials[$i]['price'];
                              $total_pro = $materials[$i]['total'];
                              $this->model->create_datailBill($idbill,$type,$idproduct,$product,$quantity,$price_pro,$total_pro);
                            }
                          }
                        }
                        if(isset($request_bill)){
                          if($request_bill == "success"){
                            //FACTURA MENSUALIDAD
                            $serie_month = $this->model->returnSerie($voucher);
                            $total_month = $this->model->returnCodeBill();
                            $type_month = 2;//FAC. SERVICIO
                            if($total_month == 0){
                              $code_month = "V00001";
                            }else{
                              $max_month = $this->model->generateCodeBill();
                              $code_month = "V".substr((substr($max_month,1)+100001),1);
                            }
                            $num_corre_month = $this->model->returnCorrelative($voucher,$serie_month);
                            if(empty($num_corre_month)){
                              $correlative_month = 1;
                            }else{
                              $correlative_month =  $this->model->returnUsed($voucher,$serie_month);
                            }
                            if($consult_contract['discount'] == 1){
                              $discount_month = $consult_contract['discount_price'];
                            }else{
                              $discount_month = 0;
                            }
                            $cost_service = 0;
                            /* DIA DE PAGO */
                            $payday = str_pad($consult_contract['payday'], 2, "0", STR_PAD_LEFT);
                            /* FECHA DE VENCIMIENTO DE LA FACTURA */
                            $date_payday = date("Y-m-".$payday);
                            $expiration = date("Y-m-d",strtotime($date_payday." + 1 month"));
                            /* DESGLOSAR FECHA DE CONTRATO PARA OBTENER EL MES */
                            $month_contract = date("m",strtotime($consult_contract['contract_date']));
                            /* DESGLOSAR FECHA DE CONTRATO PARA OBTENER EL AÑO */
                            $year_contract = date("Y",strtotime($consult_contract['contract_date']));
                            /* FECHA DE INSTALACION */
                            $date_facility = new DateTime($attention_date);
                            /* OBTENER TOTAL DE DIAS DEL MES */
                            $total_day = cal_days_in_month(CAL_GREGORIAN,$month_contract,$year_contract);
                            /* ULTIMO DIA DEL MES EN FORMATO FECHA */
                            $date_lastday = $year_contract."-".$month_contract."-".$total_day;
                            $last_day = new DateTime($date_lastday);
                            /* OBETENR LOS DIAS DE DIERENCIA ENTRE EL CONTRATO Y ULTIMO DIA DEL MES */
                            $diff = $last_day->diff($date_facility);
                            $used_days = ($diff->invert == 1) ? $diff->days : $diff->days;
                            /* OBTENER MES EN LETRAS */
                            $months = months();
                            $month = $months[date('n',strtotime($date_payday))-1];
                            //SERVICIOS DEL CLIENTE
                            $services = $this->model->contract_services($consult_contract['id']);
                            for($p=0; $p < count($services); $p++){
                              $cost_day_t = $services[$p]['price'] / $total_day;
                              $price_prorrateado_t = $cost_day_t * $used_days;
                              $cost_service = $cost_service + round($price_prorrateado_t);
                            }
                            $total_month = $cost_service - $discount_month;
                            $request_bill_month = $this->model->create_bill($iduser,$client,$voucher,$serie_month,$code_month,$correlative_month,$issue,$expiration,$date_payday,$cost_service,$discount_month,$total_month,$type_month);
                            if($request_bill_month == "success"){
                              $this->model->modify_available($voucher,$serie_month);
                              $idbill = $this->model->returnBill();
                              for ($p=0; $p < COUNT($services) ; $p++) {
                                $idservice = $services[$p]['serviceid'];
                                $service = "SERVICIO DE ".$services[$p]['service'].", MES DE ".strtoupper($month)." PRORRATEADO";
                                $cost_day = $services[$p]['price'] / $total_day;
                                $price_prorrateado = $cost_day * $used_days;
                                $price_service = round($price_prorrateado);
                                $this->model->create_datailBill($idbill,$type_month,$idservice,$service,1,$price_service,$price_service);
                              }
                            }
                          }
                        }
                      }else{
                        $this->model->open_facility($facility,"0000-00-00 00:00:00",4);
                        $this->model->reassign_technical($facility,0);
                      }
                      $response = array('status' => 'success', 'msg' => 'La instalación se completo exitosamente.');
                    }else{
                      $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operación, intentelo nuevamente.');
                    }
                  }else if($_SESSION['userData']['profileid'] == TECHNICAL){
                    if($technical == $iduser){
                      $request = $this->model->complete_installation($facility,$iduser,$opening_date,$closing_date,$observation,$state,$red_type);
                      if($request == "success"){
                        if($radio_option == 1){
                          // ACTIVO EL CLIENTE ENCASO NO SEA GRATIS
                          $consult_contract = $this->model->select_contract($client);
                          if($consult_contract['state'] !== 5){
                            $this->model->modify_contract($consult_contract['id']);
                          }
                          //CULMINO INSTLACION
                          $this->model->close_facility($facility,$closing_date,1);
                          //ASIGNO TECNICO
                          $this->model->reassign_technical($facility,$iduser);
                          //AGREGO COORDENADAS AL CLIENTE
                          $this->model->modify_client($client,$latitude,$longitude);
                          //GENERAR FACTURA DE CLIENTE
                          $voucher = 1;
                          /* FECHA DE EMISION */
                          $issue = date("Y-m-d");
                          //FACTURA INSTALACION
                          $serie = $this->model->returnSerie($voucher);
                          $total = $this->model->returnCodeBill();
                          if($total == 0){
                            $code = "V00001";
                          }else{
                            $max = $this->model->generateCodeBill();
                            $code = "V".substr((substr($max,1)+100001),1);
                          }
                          $num_corre = $this->model->returnCorrelative($voucher,$serie);
                          if(empty($num_corre)){
                            $correlative = 1;
                          }else{
                            $correlative =  $this->model->returnUsed($voucher,$serie);
                          }
                          $type = 1;
                          $discount = 0;
                          $subtotal = 0;
                          $materials = $this->model->list_materials($facility);
                          for ($i=0; $i < COUNT($materials) ; $i++) {
                            $subtotal = $subtotal + $materials[$i]['total'];
                          }
                          $cost_facility = $consult['cost'];
                          $total = $subtotal + $cost_facility;
                          if($cost_facility >= 1 && $subtotal >= 1){
                            $request_bill = $this->model->create_bill($iduser,$client,$voucher,$serie,$code,$correlative,$issue,$issue,"0000-00-00",$total,0,$total,$type);
                            if($request_bill == "success"){
                              $this->model->modify_available($voucher,$serie);
                              $idbill = $this->model->returnBill();
                              $this->model->create_datailBill($idbill,$type,0,"SERVICIO DE INSTALACIÓN",1,$cost_facility,$cost_facility);
                              for ($i=0; $i < COUNT($materials) ; $i++) {
                                $idproduct = $materials[$i]['productid'];
                                $product = $materials[$i]['product'];
                                $quantity = $materials[$i]['quantity'];
                                $price_pro = $materials[$i]['price'];
                                $total_pro = $materials[$i]['total'];
                                $this->model->create_datailBill($idbill,$type,$idproduct,$product,$quantity,$price_pro,$total_pro);
                              }
                            }
                          }else if($cost_facility >= 1 && $subtotal == 0){
                            $request_bill = $this->model->create_bill($iduser,$client,$voucher,$serie,$code,$correlative,$issue,$issue,"0000-00-00",$total,0,$total,$type);
                            if($request_bill == "success"){
                              $this->model->modify_available($voucher,$serie);
                              $idbill = $this->model->returnBill();
                              $this->model->create_datailBill($idbill,$type,0,"SERVICIO DE INSTALACIÓN",1,$cost_facility,$cost_facility);
                            }
                          }else if($cost_facility == 0 && $subtotal >= 1){
                            $request_bill = $this->model->create_bill($iduser,$client,$voucher,$serie,$code,$correlative,$issue,$issue,"0000-00-00",$total,0,$total,$type);
                            if($request_bill == "success"){
                              $this->model->modify_available($voucher,$serie);
                              $idbill = $this->model->returnBill();
                              for ($i=0; $i < COUNT($materials) ; $i++) {
                                $idproduct = $materials[$i]['productid'];
                                $product = $materials[$i]['product'];
                                $quantity = $materials[$i]['quantity'];
                                $price_pro = $materials[$i]['price'];
                                $total_pro = $materials[$i]['total'];
                                $this->model->create_datailBill($idbill,$type,$idproduct,$product,$quantity,$price_pro,$total_pro);
                              }
                            }
                          }
                          if($request_bill == "success"){
                            //FACTURA MENSUALIDAD
                            $serie_month = $this->model->returnSerie($voucher);
                            $total_month = $this->model->returnCodeBill();
                            $type_month = 2;//FAC. SERVICIO
                            if($total_month == 0){
                              $code_month = "V00001";
                            }else{
                              $max_month = $this->model->generateCodeBill();
                              $code_month = "V".substr((substr($max_month,1)+100001),1);
                            }
                            $num_corre_month = $this->model->returnCorrelative($voucher,$serie_month);
                            if(empty($num_corre_month)){
                              $correlative_month = 1;
                            }else{
                              $correlative_month =  $this->model->returnUsed($voucher,$serie_month);
                            }
                            if($consult_contract['discount'] == 1){
                              $discount_month = $consult_contract['discount_price'];
                            }else{
                              $discount_month = 0;
                            }
                            $cost_service = 0;
                            /* DIA DE PAGO */
                            $payday = str_pad($consult_contract['payday'], 2, "0", STR_PAD_LEFT);
                            /* FECHA DE VENCIMIENTO DE LA FACTURA */
                            $date_payday = date("Y-m-".$payday);
                            $expiration = date("Y-m-d",strtotime($date_payday." + 1 month"));
                            /* DESGLOSAR FECHA DE CONTRATO PARA OBTENER EL MES */
                            $month_contract = date("m",strtotime($consult_contract['contract_date']));
                            /* DESGLOSAR FECHA DE CONTRATO PARA OBTENER EL AÑO */
                            $year_contract = date("Y",strtotime($consult_contract['contract_date']));
                            /* FECHA DE INSTALACION */
                            $date_facility = new DateTime($attention_date);
                            /* OBTENER TOTAL DE DIAS DEL MES */
                            $total_day = cal_days_in_month(CAL_GREGORIAN,$month_contract,$year_contract);
                            /* ULTIMO DIA DEL MES EN FORMATO FECHA */
                            $date_lastday = $year_contract."-".$month_contract."-".$total_day;
                            $last_day = new DateTime($date_lastday);
                            /* OBETENR LOS DIAS DE DIERENCIA ENTRE EL CONTRATO Y ULTIMO DIA DEL MES */
                            $diff = $last_day->diff($date_facility);
                            $used_days = ($diff->invert == 1) ? $diff->days : $diff->days;

                            /* OBTENER MES EN LETRAS */
                            $months = months();
                            $month = $months[date('n',strtotime($date_payday))-1];
                            //SERVICIOS DEL CLIENTE
                            $services = $this->model->contract_services($consult_contract['id']);
                            for($p=0; $p < count($services); $p++){
                              $cost_day_t = $services[$p]['price'] / $total_day;
                              $price_prorrateado_t = $cost_day_t * $used_days;
                              $cost_service = $cost_service + round($price_prorrateado_t);
                            }
                            $total_month = $cost_service - $discount_month;
                            $request_bill_month = $this->model->create_bill($iduser,$client,$voucher,$serie_month,$code_month,$correlative_month,$issue,$expiration,$date_payday,$cost_service,$discount_month,$total_month,$type_month);
                            if($request_bill_month == "success"){
                              $this->model->modify_available($voucher,$serie_month);
                              $idbill = $this->model->returnBill();
                              for ($p=0; $p < COUNT($services) ; $p++) {
                                $idservice = $services[$p]['serviceid'];
                                $service = "SERVICIO DE ".$services[$p]['service'].", MES DE ".strtoupper($month)." PRORRATEADO";
                                $cost_day = $services[$p]['price'] / $total_day;
                                $price_prorrateado = $cost_day * $used_days;
                                $price_service = round($price_prorrateado);
                                $this->model->create_datailBill($idbill,$type_month,$idservice,$service,1,$price_service,$price_service);
                              }
                            }
                          }
                        }else{
                          $this->model->open_facility($facility,"0000-00-00 00:00:00",4);
                          $this->model->reassign_technical($facility,0);
                        }
                        $response = array('status' => 'success', 'msg' => 'La instalación se completo exitosamente.');
                      }else{
                        $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operación, intentelo nuevamente.');
                      }
                    }else{
                      $response = array("status" => 'info', "msg" => 'La instalación esta asignado a otro técnico.');
                    }
                  }else{
                    $response = array("status" => 'info', "msg" => 'Usted no tiene permiso para cerra la instalación.');
                  }
                }else if($state_ticket == 2 || $state_ticket == 4 || $state_ticket == 5 ){
                  $response = array("status" => 'error', "msg" => 'La instalación debe estar en estado en proceso para poder completar la operación.');
                }
              }
            }
            echo json_encode($response,JSON_UNESCAPED_UNICODE);
          }
          die();
        }
        public function view_pdf(string $idfacility){
          if($_SESSION['permits_module']['v']){
            $idfacility = decrypt($idfacility);
            $idfacility = intval($idfacility);
            if(is_numeric($idfacility)){
              $data = $this->model->view_installation($idfacility);
              if(empty($data)){
        				echo "Información no ha sido encontrada";
        			}else{
                ob_end_clean();
                $html = redirect_pdf("Resources/reports/pdf/installation",$data);
                $orientation = 'portrait';
                $customPaper = 'A4';

                $dompdf = new Dompdf();

                $options = $dompdf->getOptions();
                $options->set(array('isRemoteEnabled' => true));
                $dompdf->setOptions($options);

                $dompdf->loadHtml($html);

                $dompdf->setPaper($customPaper,$orientation);
                $dompdf->render();
                $code = str_pad($data['facility']['id'],7,"0", STR_PAD_LEFT);
                $dompdf->stream('INS-'.$code.'.pdf',array("Attachment" => false));
        			}
            }else{
        			echo "Información no valida";
        		}
          }else{
    				header('Location: '.base_url().'/login');
    				die();
    			}
        }
        public function view_installation(string $idfacility){
          if($_SESSION['permits_module']['v']){
              $idfacility = decrypt($idfacility);
              $idfacility = intval($idfacility);
              if($idfacility > 0){
                  $data = $this->model->view_installation($idfacility);
                  if(empty($data)){
                    $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
                  }else{
                    $data['facility']['code'] = str_pad($data['facility']['id'],7,"0", STR_PAD_LEFT);
                    for($i=0; $i < count($data['images']); $i++){
                      $data['images'][$i]['url_image'] = base_style().'/uploads/gallery/'.$data['images'][$i]['image'];
                    }
                    $answer = array('status' => 'success', 'data' => $data);
                  }
              }else{
                $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
              }
            echo json_encode($answer,JSON_UNESCAPED_UNICODE);
          }
          die();
        }
        public function cancel(){
          if($_SESSION['permits_module']['e']){
            if($_POST){
              $idfacility = decrypt($_POST['idfacility']);
              $idfacility =  intval($idfacility);
              $request = $this->model->cancel($idfacility);
              if($request){
                $arrResponse = array('status' => 'success', 'msg' => 'La instalación ha sido cancelada.');
              }else{
                $arrResponse = array('status' => 'error', 'msg' => 'Error no se pudo realizar esta operación.');
              }
              echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
          }
          die();
        }
        public function list_clients(){
          $html = "";
          $data = $this->model->list_clients();
          if(count($data) > 0){
            $html = '<option value="">SELECCIONAR</option>';
            for($i=0; $i < count($data); $i++) {
              $html .= '<option value="'.encrypt($data[$i]['clientid']).'">'.$data[$i]['names'].' '.$data[$i]['surnames'].'</option>';
            }
          }
          echo $html;
          die();
        }
        public function list_technical(){
            $html = "";
            $data = $this->model->list_technical();
            if(count($data) > 0){
                $html = '<option value="0">LIBRE</option>';
                for($i=0; $i < count($data); $i++) {
                    $html .= '<option value="'.encrypt($data[$i]['id']).'">'.$data[$i]['names'].' '.$data[$i]['surnames'].'</option>';
                }
            }
            echo $html;
            die();
        }
        public function list_materials(string $idfacility){
            if($_SESSION['permits_module']['v']){
                $n = 1;
                $idfacility = decrypt($idfacility);
                $idfacility = intval($idfacility);
                $data = $this->model->list_materials($idfacility);
                for($i=0; $i < count($data); $i++){
                    $delete = '';
                    $data[$i]['n'] = $n++;
                    $data[$i]['price'] = $_SESSION['businessData']['symbol'].format_money($data[$i]['price']);
                    $data[$i]['total'] = $_SESSION['businessData']['symbol'].format_money($data[$i]['total']);
                    if($_SESSION['permits_module']['e']){
                         $delete = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Eliminar" onclick="remove_material(\''.encrypt($data[$i]['id']).'\')"><i class="far fa-trash-alt"></i></a>';
                         $delete_2 = '<a href="javascript:;" class="dropdown-item" onclick="remove_material(\''.encrypt($data[$i]['id']).'\')"><i class="far fa-trash-alt mr-1"></i>Eliminar</a>';
                    }else{
                         $delete = '<a href="javascript:;" class="blue drop" disabled><i class="far fa-trash-alt"></i></a>';
                         $delete_2 = '<a href="javascript:;" class="dropdown-item drop" disabled><i class="far fa-trash-alt mr-1"></i>Eliminar</a>';
                    }
                    $options = '<div class="hidden-sm hidden-xs action-buttons">'.$delete.'</div>';
                    $options .='<div class="hidden-md hidden-lg"><div class="dropdown">
                    <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
                      '.$delete_2.'
                    </div>
                    </div></div>';
                    $data[$i]['options'] = $options;
                }
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function select_product(string $idproduct){
            if($_SESSION['permits_module']['v']){
                $idproduct = decrypt($idproduct);
                $idproduct = intval($idproduct);
                if($idproduct > 0){
                    $data = $this->model->select_product($idproduct);
                    if(empty($data)){
                        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
                    }else{
                        $answer = array('status' => 'success', 'data' => $data);
                    }
                    echo json_encode($answer,JSON_UNESCAPED_UNICODE);
                }
            }
            die();
        }
        public function register_material(){
            if($_POST){
                if(empty($_POST['idfacility']) || empty($_POST['listProduct'])){
                  $response = array("status" => 'errror', "msg" => 'Campos señalados son obligatorios.');
                }else{
                    $facility = decrypt($_POST['idfacility']);
                    $facility = intval($facility);
                    $product = decrypt($_POST['listProduct']);
                    $product = intval($product);
                    $conditions = strClean($_POST['listConditions']);
                    $serie = strClean($_POST['serie']);
                    $mac = strClean($_POST['mac']);
                    $consult_ins = $this->model->select_record($facility);
                    if($conditions == "PRESTAMO"){
                        $quantity = 1;
                        $price = 0;
                        $total = 0;
                        $description = "PRESTAMO DE PRODUCTO (INSTALACIÓN DE SERVICIO) - ".$consult_ins['names']." ".$consult_ins['surnames'];
                    }
                    if($conditions == "VENTA"){
                        $quantity = intval(strClean($_POST['quantity']));
                        $price = strClean($_POST['price']);
                        $total = strClean($_POST['total']);
                        $description = "VENTA DE PRODUCTO (INSTALACIÓN DE SERVICIO) - ".$consult_ins['names']." ".$consult_ins['surnames'];
                    }
                    if($_SESSION['permits_module']['r']){
                        $request = $this->model->create_material($facility,$product,$quantity,$price,$total,$conditions,$serie,$mac);
                        if($request == "success"){
                            $datetime = date("Y-m-d H:i:s");
                            $consult = $this->model->consult_loan_facility($facility,$product);
                            if(!empty($consult)){
                              $this->model->create_departures($product,$datetime,$description,$quantity,0,0);
                            }else {
                              $this->model->create_departures($product,$datetime,$description,$quantity,$price,$total);
                            }
                            $this->model->subtract_stock($product,$quantity);
                        }
                    }
                    if($request == "success"){
                        $response = array('status' => 'success', 'msg' => 'El producto se registro correctamente.');
                    }else{
                        $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operaciòn, intentelo nuevamente.');
                    }
                }
                echo json_encode($response,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function remove_material(){
            if($_POST){
                if($_SESSION['permits_module']['e']){
                    $idtools = decrypt($_POST['idtools']);
                    $idtools =  intval($idtools);
                    $consult = $this->model->select_tools($idtools);
                    if(!empty($consult)){
                        $facility = $consult['facilityid'];
                        $product = $consult['productid'];
                        $quantity = $consult['quantity'];
                        $price = $consult['price'];
                        $total = $consult['total'];
                        $datetime = date("Y-m-d H:i:s");
                        $consult_ins = $this->model->select_record($facility);
                        $description = "DEVOLUCIÓN DE PRODUCTO A ALMACEN (INSTALACIÓN DE SERVICIO) - ".$consult_ins['names']." ".$consult_ins['surnames'];
                        $this->model->create_incomes($product,$datetime,$description,$quantity,$price,$total);
                        $this->model->increase_stock($product,$quantity);
                        $request = $this->model->remove_material($idtools);
                        if($request == 'success'){
                          $response = array('status' => 'success', 'msg' => 'Producto retirado correctamente, el stock ya se encuentra disponible en almacén.');
                        }else{
                          $response = array('status' => 'error', 'msg' => 'Error no se pudo eliminar.');
                        }
                    }
                    echo json_encode($response,JSON_UNESCAPED_UNICODE);
                }
            }
            die();
        }
        public function register_image(){
            if($_POST){
                if(empty($_POST['idclient']) || empty($_POST['idfacility'])){
                    $response = array('status' => 'error', 'msg' => 'Error de datos.');
                }else{
                    /* VARIABLES PARA EL REGISTRO A LA BD*/
                    $idclient = decrypt($_POST['idclient']);
                    $idclient = intval($idclient);
                    $iduser = intval($_SESSION['idUser']);
                    $type_image = 1;//instalacion
                    $idfacility = decrypt($_POST['idfacility']);
                    $idfacility = intval($idfacility);
                    $datetime = date("Y-m-d H:i:s");
                    $user = $_SESSION['userData']['names']." ".$_SESSION['userData']['surnames'];
                    /* IMAGEN DESDE EL FORMULARIO */
                    $photo = $_FILES['photo'];
                    $name = $photo['name'];
                    /* EXTENCION DE IMAGEN */
                    $ext = explode(".", $name);
                    /* OBTENER NOMBRE DEL CLIENTE */
                    $consult_client = $this->model->select_client($idclient);
                    $name_client = $consult_client['names']." ".$consult_client['surnames'];
                    $formatted_name = strtolower(clear_cadena($name_client));
					          $formatted_name = str_replace(" ","_",$formatted_name);
                    /* RUTA Y NOMBRE DE LA NUEVA IMAGEN */
                    $image = $formatted_name.'_'.md5(round(microtime(true))).'.'.end($ext);
                    $image_file = $formatted_name.'_'.md5(round(microtime(true)));
                    $save_path = 'Assets/uploads/gallery/';
                    $url_image = base_style().'/uploads/gallery/'.$formatted_name.'_'.md5(round(microtime(true))).'.'.end($ext);
                    if($_SESSION['permits_module']['r']){
                      $request = $this->model->register_image($idclient,$iduser,$type_image,$idfacility,$datetime,$image);
                    }
                    if($request == "success"){
                      if(isset($photo)){
                        $up = new Upload($photo);
                        if($up->uploaded){
                          $taken = date("d/m/Y h:i A");
                          $up->file_new_name_body = $image_file;
                          $up->image_resize = true;
                          $up->image_x = 600;
                          $up->image_ratio_y = true;
                          $up->image_unsharp = true;
                          $up->image_text = $user."\n".$taken;
                          $up->image_text_alignment = 'R';
                          $up->image_text_font = 35;
                          $up->image_text_position = 'BR';
                          $up->image_text_padding_y = 5;
                          $up->image_text_x = -10;
                        	$up->Process($save_path);
                          if($up->processed){
                            $up->clean();
                          }
                        }
                      }
                      $response = array('status' => 'success', 'image' => $image, 'url_image' => $url_image, 'msg' => 'Imagen agregada correctamente.');
                    }else{
                      $response = array('status' => 'error', 'msg' => 'No se pudo completar esta operación.');
                    }
                }
                echo json_encode($response,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function remove_image(){
            if($_POST){
                if(empty($_POST['idfacility']) || empty($_POST['file'])){
                    $response = array("status" => 'error', "msg" => 'Datos incorrectos.');
                }else{
                    $idfacility = decrypt($_POST['idfacility']);
                    $idfacility =  intval($idfacility);
                    $image  = strClean($_POST['file']);
                    $request = $this->model->remove_image($idfacility,$image);
                    if($request == "success"){
                        $delete =  delete_image('gallery',$image);
                        $response = array('status' => 'success', 'msg' => 'Imagen eliminada de la galeria.');
                    }else{
                        $response = array('status' => 'error', 'msg' => 'Error al eliminar.');
                    }
                }
                echo json_encode($response,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function show_images(string $idfacility){
            if($_SESSION['permits_module']['v']){
                $idfacility = decrypt($idfacility);
                $idfacility =  intval($idfacility);
                if($idfacility > 0){
                    $data = $this->model->show_images($idfacility);
                    if(empty($data)){
                      $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
                    }else{
                        for ($i=0; $i < count($data); $i++) {
                            $data[$i]['url_image'] = base_style().'/uploads/gallery/'.$data[$i]['image'];
                        }
                        $answer = array('status' => 'success', 'data' => $data);
                    }
                }else{
                  $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
                }
                echo json_encode($answer,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function number_images(string $idfacility){
          if($_SESSION['permits_module']['v']){
            $idfacility = decrypt($idfacility);
            $idfacility =  intval($idfacility);
            if($idfacility > 0){
              $data = $this->model->number_images($idfacility);
              if(empty($data)){
                $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
              }else{
                $answer = array('status' => 'success', 'data' => $data);
              }
            }else{
              $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
            }
            echo json_encode($answer,JSON_UNESCAPED_UNICODE);
          }
          die();
        }
        public function send_email(string $idfacility){
            $idfacility = decrypt($idfacility);
            $idfacility = intval($idfacility);
            if($idfacility > 0){
                $data = $this->model->view_installation($idfacility);
                if(!empty($data)){
                    if(empty($data['facility']['email'])){
                        $answer = array('status' => 'not_exist', 'msg' => 'El cliente no tiene correo electronico.');
                    }else{
                        $email = array(
                            'logo' => $_SESSION['businessData']['logo_email'],//logo empresa
                            'name_sender' => $_SESSION['businessData']['business_name'],//nombre remitente
                            'sender' => $_SESSION['businessData']['email'],//remitente
                            'password' => $_SESSION['businessData']['password'],//contraseña
                            'mobile' => $_SESSION['businessData']['mobile'],//celular
                            'address' => $_SESSION['businessData']['address'],//celular
                            'host' => $_SESSION['businessData']['server_host'],//host
                            'port' => $_SESSION['businessData']['port'],//puerto
                            'addressee' => $data['facility']['email'],//destinatario
                            'name_addressee' => $data['facility']['client'],//nombre destinatario
                            'affair' => 'HOJA DE INSTALACIÓN',//asunto
                            'data' => $data,//data del email
                        );
                        $result = sendMail($email,"installation");
                        if($result === true){
                            $answer = array('status' => 'success', 'msg' => "El correo se envio correctamente.");
                        }else{
                            $answer = array('status' => 'error', 'msg' => "Hubo un error,reenvié el correo nuevamente.");
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
