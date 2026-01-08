<?php
    class Vouchers extends Controllers{
        public function __construct(){
            parent::__construct();
			session_start();
			if(empty($_SESSION['login'])){
				header('Location: '.base_url().'/login');
				die();
			}
			consent_permission(VOUCHERS);
        }
        public function vouchers(){
            if(empty($_SESSION['permits_module']['v'])){
                header("Location:".base_url().'/dashboard');
            }
            $data['page_name'] = "Comprobantes";
            $data['page_title'] = "Gestión de Comprobantes";
            $data['home_page'] = "Dashboard";
            $data['previous_page'] = "Ajustes";
            $data['actual_page'] = "Comprobantes";
            $data['page_functions_js'] = "vouchers.js";
            $this->views->getView($this,"vouchers",$data);
        }
        public function list_records(){
            if($_SESSION['permits_module']['v']){
                $n = 1;
                $data = $this->model->list_records();
                for($i=0; $i < count($data); $i++){
                    $data[$i]['n'] = $n++;
                    $available = $this->model->return_serie($data[$i]['id']);
                    $data[$i]['date'] = date("d/m/Y", strtotime($data[$i]['registration_date']));
                    if($available == 0){
                        if($_SESSION['permits_module']['r']){
                            $series = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Agregar serie" onclick="add_serie(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-plus-square"></i></a>';
                            $series_2 = '<a href="javascript:;" class="dropdown-item" onclick="add_serie(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-plus-square mr-1"></i>Agregar serie</a>';
                        }else{
                            $series = '';
                            $series_2 = '';
                        }
                    }else{
                        if($_SESSION['permits_module']['a']){
                            $series = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Editar serie" onclick="edit_serie(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-minus-square"></i></a>';
                            $series_2 = '<a href="javascript:;" class="dropdown-item" onclick="edit_serie(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-minus-square mr-1"></i>Editar serie</a>';
                        }else{
                            $series = '';
                            $series_2 = '';
                        }
                    }
                    if($_SESSION['permits_module']['a']){
                        $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-pencil-alt"></i></a>';
                        $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
                    }else{
                        $update = '';
                        $update_2 = '';
                    }
                    if($_SESSION['permits_module']['e']){
                        if($data[$i]['id'] !=1 && $data[$i]['id'] !=2 && $data[$i]['id'] !=3){
                          $delete = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Eliminar" onclick="remove(\''.encrypt($data[$i]['id']).'\')"><i class="far fa-trash-alt"></i></a>';
                          $delete_2 = '<a href="javascript:;" class="dropdown-item" onclick="remove(\''.encrypt($data[$i]['id']).'\')"><i class="far fa-trash-alt mr-1"></i>Eliminar</a>';
                        }else{
                          $delete = '';
                          $delete_2 = '';
                        }
                    }else{
                      $delete = '';
                      $delete_2 = '';
                    }
                    if($_SESSION['permits_module']['v']){
                        $view = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Ver '.strtolower($data[$i]['voucher']).'" onclick="view(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-file-alt"></i></a>';
                        $view_2 = '<a href="javascript:;" class="dropdown-item" onclick="view(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-file-alt mr-1"></i>Ver '.strtolower($data[$i]['voucher']).'</a>';
                    }else{
                        $view = '';
                        $view_2 = '';
                    }
                    $options = '<div class="hidden-sm hidden-xs action-buttons">'.$update.$delete.$series.$view.'</div>';
                    $options .='<div class="hidden-md hidden-lg"><div class="dropdown">
                    <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
                      '.$update_2.$delete_2.$series_2.$view_2.'
                    </div>
                    </div></div>';
                    $data[$i]['options'] = $options;
                }
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function select_record(string $idvoucher){
            if($_SESSION['permits_module']['v']){
                $idvoucher = decrypt($idvoucher);
                $idvoucher = intval($idvoucher);
                if($idvoucher > 0){
                    $data = $this->model->select_record($idvoucher);
                    if(empty($data)){
                        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
                    }else{
                      $data['encrypt_id'] = encrypt($data['id']);
                        $answer = array('status' => 'success', 'data' => $data);
                    }
                }else{
                  $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
                }
                echo json_encode($answer,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function select_serie(string $idvoucher){
    			if($_SESSION['permits_module']['v']){
    				$idvoucher = decrypt($idvoucher);
    				$idvoucher = intval($idvoucher);
    				if($idvoucher > 0){
    					$serie = $this->model->return_id($idvoucher);
    					$data = $this->model->select_serie($serie);
    					if(empty($data)){
    						$response = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
    					}else{
                $data['encrypt_id'] = encrypt($data['id']);
                $data['encrypt_voucher'] = encrypt($data['voucherid']);
    						$response = array('status' => 'success', 'data' => $data);
    					}
    					echo json_encode($response,JSON_UNESCAPED_UNICODE);
    				}
    			}
    			die();
    		}
        public function list_vouchers(){
            $html = "";
            $arrData = $this->model->list_vouchers();
            if(count($arrData) > 0){
                for($i=0; $i < count($arrData); $i++) {
                    if($arrData[$i]['state'] == 1 ){
                        $exists = $this->model->serial_existence($arrData[$i]['id']);
                        if($exists != 0){
                          $total = $this->model->return_series($arrData[$i]['id']);
                          if($total >= 1){
                            $html .= '<option value="'.encrypt($arrData[$i]['id']).'">'.$arrData[$i]['voucher'].'</option>';
                          }
                        }
                    }
                }
            }
            echo $html;
            die();
        }
        public function series_vocuhers(string $voucher){
            $html = "";
            $voucher = decrypt($voucher);
            $voucher = intval($voucher);
            $arrData = $this->model->series_vocuhers($voucher);
            if(count($arrData) > 0){
                for($i=0; $i < count($arrData); $i++) {
                    $html .= '<option value="'.encrypt($arrData[$i]['id']).'">'.$arrData[$i]['serie'].'</option>';
                }
            }
            echo $html;
            die();
        }
        public function action(){
            if($_POST){
                if(empty($_POST['voucher'])){
                    $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios.');
                }else{
                    $id = decrypt($_POST['idvoucher']);
                    $id = intval($id);
                    $voucher = strtoupper(strClean($_POST['voucher']));
                    $datetime = date("Y-m-d H:i:s");
                    $state = intval(strClean($_POST['listStatus']));
                    if($id == 0){
                        $option = 1;
                        if($_SESSION['permits_module']['r']){
                            $request = $this->model->create($voucher,$datetime,$state);
                        }
                    }else{
                        $option = 2;
                        if($_SESSION['permits_module']['a']){
                            $request = $this->model->modify($id,$voucher,$state);
                        }
                    }
                    if($request == "success"){
                        if($option == 1){
                            $response = array('status' => 'success', 'msg' => 'Se ha registrado exitosamente.');
                        }else{
                            $response = array('status' => 'success', 'msg' => 'Se ha actualizado el registro exitosamente.');
                        }
                    }else if($request == 'exists'){
                        $response = array('status' => 'error', 'msg' => '¡Atención! El registro ya existe, ingrese otro.');
                    }else{
                        $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operaciòn, intentelo nuevamente.');
                    }
                }
                echo json_encode($response,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function action_serie(){
            if($_POST){
                if(empty($_POST['date']) || empty($_POST['serie'])){
                    $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios.');
                }else{
                  $id = decrypt($_POST['idserie']);
                  $id = intval($id);
          $idvouchers = decrypt($_POST['idvouchers']);
          $idvouchers = intval($idvouchers);
          $dateIni = DateTime::createFromFormat('d/m/Y', $_POST['date']);
          $date = $dateIni->format('Y-m-d');
					$serie = strtoupper(strClean($_POST['serie']));
					$fromc = intval($_POST['fromc']);
					$until = intval($_POST['until']);
					$available = intval($_POST['available']);
                    if($id == 0){
                        $option = 1;
                        if($_SESSION['permits_module']['r']){
                            $request = $this->model->add_serie($date,$serie,$fromc,$until,$idvouchers,$available);
                        }
                    }else{
                        $option = 2;
                        if($_SESSION['permits_module']['a']){
                            $request = $this->model->edit_serie($id,$date,$serie,$fromc,$until,$idvouchers,$available);
                        }
                    }
                    if($request == "success"){
                        if($option == 1){
                            $response = array('status' => 'success', 'msg' => 'Se ha asignado la serie exitosamente.');
                        }else{
                            $response = array('status' => 'success', 'msg' => 'Se ha actualizado la serie exitosamente.');
                        }
                    }else if($request == 'exists'){
                        $response = array('status' => 'error', 'msg' => '¡Atención! La serie ya existe, ingrese otro.');
                    }else{
                        $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operaciòn, intentelo nuevamente.');
                    }
                }
                echo json_encode($response,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function list_series(string $idvoucher){
            $html_out = "";
            $idvoucher = decrypt($idvoucher);
            $idvoucher = intval($idvoucher);
            $data = $this->model->list_series($idvoucher);
            if(!empty($data)){
                for ($i=0; $i < count($data); $i++) {
                    $utilizados = $data[$i]['until'] - $data[$i]['available'];
                    $view = $utilizados ? '<span class="badge label-warning f-s-12">'.$utilizados.'</span>':'<span class="badge associates badge-danger">'.$utilizados.'</span>';
                    $html_out .= '
                        <tr>
                            <th class="text-center">'.date("d/m/Y", strtotime($data[$i]['date'])).'</th>
                            <th class="text-center">'.$data[$i]['serie'].'</th>
                            <th class="text-center">'.$data[$i]['available'].'</th>
                            <th class="text-center">'.$view.'</th>
                        </tr>
                    ';
                }
            }else{
                $html_out .= '
                <tr>
                	<td colspan="4" class="text-center">No hay series registradas.</td>
                </tr>';
            }
            echo $html_out;
            die();
        }
        public function remove(){
            if($_POST){
                if($_SESSION['permits_module']['e']){
                    $idvoucher = decrypt($_POST['idvoucher']);
                    $idvoucher =  intval($idvoucher);
                    $request = $this->model->remove($idvoucher);
                    if($request == 'success'){
                      $response = array('status' => 'success', 'msg' => 'El registro se ha eliminado.');
                    }else if($request == 'exists'){
						$response = array('status' => 'exists', 'msg' => 'El comprobante esta en uso, imposible eliminar');
					}else{
                      $response = array('status' => 'error', 'msg' => 'Error no se pudo eliminar.');
                    }
                    echo json_encode($response,JSON_UNESCAPED_UNICODE);
                }
            }
            die();
        }
    }
