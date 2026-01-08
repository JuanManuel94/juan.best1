<?php
    class Profiles extends Controllers{
        public function __construct(){
            parent::__construct();
			session_start();
			if(empty($_SESSION['login'])){
				header('Location: '.base_url().'/login');
				die();
			}
			consent_permission(USERS);
        }
        public function profiles(){
            if(empty($_SESSION['permits_module']['v'])){
                header("Location:".base_url().'/dashboard');
            }
            $data['page_name'] = "Perfiles";
            $data['page_title'] = "Gestión de Perfiles";
            $data['home_page'] = "Dashboard";
            $data['previous_page'] = "Ajustes";
            $data['actual_page'] = "Perfiles";
            $data['page_functions_js'] = "profiles.js";
            $this->views->getView($this,"profiles",$data);
        }
        public function assign_permissions(string $idprofile){
            if(empty($_SESSION['permits_module']['v'])){
                header("Location:".base_url().'/dashboard');
            }
            $idprofile = decrypt($idprofile);
            $idprofile = intval($idprofile);
            $profile = $this->model->select_record($idprofile);
            $data['page_name'] = "Permisos";
            $data['page_title'] = $profile['profile'];
            $data['home_page'] = "Dashboard";
            $data['previous_page'] = "Perfiles";
            $data['actual_page'] = "Asignar permiso";
            $data['page_functions_js'] = "assign_permissions.js";
            $data['permissions'] = $this->list_permissions($idprofile);
            $this->views->getView($this,"assign_permissions",$data);
        }
        public function list_permissions(int $idprofile){
            if($idprofile > 0){
                $modules = $this->model->list_modules();
                $select_permissions = $this->model->select_permissions($idprofile);
                $permissions = array('r' => 0, 'a' => 0, 'e' => 0, 'v' => 0);
                $permissions_charge = array('idprofile' => $idprofile);
                if(empty($select_permissions)){
                    for ($i=0; $i < count($modules) ; $i++) {
                        $modules[$i]['permissions'] = $permissions;
                    }
                }else{
                    for ($i=0; $i < count($modules); $i++) {
                        $permissions = array('r' => 0, 'a' => 0, 'e' => 0, 'v' => 0);
                        if(isset($select_permissions[$i])){
                            $permissions = array('r' => $select_permissions[$i]['r'],'a' => $select_permissions[$i]['a'],'e' => $select_permissions[$i]['e'],'v' => $select_permissions[$i]['v']);
                        }
                        $modules[$i]['permissions'] = $permissions;
                    }
                }
                $permissions_charge['modules'] = $modules;
                return $permissions_charge;
            }
            die();
        }
        public function list_records(){
            if($_SESSION['permits_module']['v']){
                $n = 1;
	            $data = $this->model->list_records();
	            for($i=0; $i < count($data); $i++){
                    $data[$i]['n'] = $n++;
                    $data[$i]['associates'] = '<span class="badge label-warning f-s-12">'.$this->model->associates($data[$i]['id']).'</span>';
                    if($_SESSION['permits_module']['a']){
                        $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-pencil-alt"></i></a>';
                        $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
                        if($data[$i]['state'] == 1){
                            $permits = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Asignar permisos" onclick="permits(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-id-card"></i></a>';
                            $permits_2 = '<a href="javascript:;" class="dropdown-item" onclick="permits(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-id-card mr-1"></i>Asignar permisos</a>';
                        }else{
                            $permits = '';
                            $permits_2 = '';
                        }
                    }else{
                        $update = '';
                        $update_2 = '';
                        $permits = '';
                        $permits_2 = '';
                    }
                    if($_SESSION['permits_module']['e']){
                        if($data[$i]['id'] ==1 || $data[$i]['id'] ==2 || $data[$i]['id'] ==3){
                          $delete = '';
                          $delete_2 = '';
                        }else{
                          $delete = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Eliminar" onclick="remove(\''.encrypt($data[$i]['id']).'\')"><i class="far fa-trash-alt"></i></a>';
                          $delete_2 = '<a href="javascript:;" class="dropdown-item" onclick="remove(\''.encrypt($data[$i]['id']).'\')"><i class="far fa-trash-alt mr-1"></i>Eliminar</a>';
                        }
                    }else{
                        $delete = '';
                        $delete_2 = '';
          					}
                    $options = '<div class="hidden-sm hidden-xs action-buttons">'.$update.$delete.$permits.'</div>';
                    $options .='<div class="hidden-md hidden-lg"><div class="dropdown">
                    <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
                      '.$update_2.$delete_2.$permits_2.'
                    </div>
                    </div></div>';
                    $data[$i]['options'] = $options;
                }
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function select_record(string $idprofile){
            if($_SESSION['permits_module']['v']){
                $idprofile = decrypt($idprofile);
                $idprofile = intval($idprofile);
                if($idprofile > 0){
                    $data = $this->model->select_record($idprofile);
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
        public function list_profiles(){
            $html = "";
            $arrData = $this->model->list_profiles();
            if(count($arrData) > 0){
                for($i=0; $i < count($arrData); $i++) {
                    if($arrData[$i]['state'] == 1 ){
                        $html .= '<option value="'.encrypt($arrData[$i]['id']).'">'.$arrData[$i]['profile'].'</option>';
                    }
                }
            }
            echo $html;
            die();
        }
        public function action(){
            if($_POST){
                if(empty($_POST['profile'])){
                    $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios');
                }else{
                  $id = decrypt($_POST['idprofile']);
                  $id = intval($id);
                    $profile = strtoupper(strClean($_POST['profile']));
                    $description = strtoupper(strClean($_POST['description']));
                    $datetime = date("Y-m-d H:i:s");
                    $state = intval(strClean($_POST['listStatus']));
                    if($id == 0){
                        $option = 1;
                        if($_SESSION['permits_module']['r']){
                            $request = $this->model->create($profile,$description,$datetime,$state);
                        }
                    }else{
                        $option = 2;
                        if($_SESSION['permits_module']['a']){
                            $request = $this->model->modify($id,$profile,$description,$state);
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
        public function remove(){
            if($_POST){
                if($_SESSION['permits_module']['e']){
                    $idprofile = decrypt($_POST['idprofile']);
                    $idprofile =  intval($idprofile);
                    $request = $this->model->remove($idprofile);
                    if($request == 'success'){
                      $response = array('status' => 'success', 'msg' => 'El registro se ha eliminado.');
                    }else if($request == 'exists'){
						$arrResponse = array('status' => 'exist', 'msg' => 'Perfil en uso, imposible eliminar.');
					}else{
                      $response = array('status' => 'error', 'msg' => 'Error no se pudo eliminar.');
                    }
                    echo json_encode($response,JSON_UNESCAPED_UNICODE);
                }
            }
            die();
        }
    }
