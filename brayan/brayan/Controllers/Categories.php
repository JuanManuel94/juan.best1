<?php
    require 'Libraries/spreadsheet/vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\IOFactory;
    class Categories extends Controllers{
        public function __construct(){
            parent::__construct();
  			    session_start();
			if(empty($_SESSION['login'])){
				header('Location: '.base_url().'/login');
				die();
			}
			consent_permission(CATEGORIES);
        }
        public function categories(){
            if(empty($_SESSION['permits_module']['v'])){
                header("Location:".base_url().'/dashboard');
            }
            $data['page_name'] = "Categorias";
            $data['page_title'] = "Gestión de Categorias";
            $data['home_page'] = "Dashboard";
            $data['previous_page'] = "Inventario";
            $data['actual_page'] = "Categorias";
            $data['page_functions_js'] = "categories.js";
            $this->views->getView($this,"categories",$data);
        }
        public function list_records(){
            if($_SESSION['permits_module']['v']){
                $n = 1;
                $data = $this->model->list_records();
                for($i=0; $i < count($data); $i++){
                    $update = '';
                    $delete = '';
                    $data[$i]['n'] = $n++;
                    $data[$i]['associates'] = '<span class="badge label-warning f-s-12">'.$this->model->associates($data[$i]['id']).'</span>';
                    if($_SESSION['permits_module']['a']){
                        $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-pencil-alt"></i></a>';
                        $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update(\''.encrypt($data[$i]['id']).'\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
                    }else{
                        $update = '';
                        $update_2 = '';
                    }
                    if($_SESSION['permits_module']['e']){
                        $delete = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Eliminar" onclick="remove(\''.encrypt($data[$i]['id']).'\')"><i class="far fa-trash-alt"></i></a>';
                        $delete_2 = '<a href="javascript:;" class="dropdown-item" onclick="remove(\''.encrypt($data[$i]['id']).'\')"><i class="far fa-trash-alt mr-1"></i>Eliminar</a>';
                    }else{
                        $delete = '';
                        $delete_2 = '';
                    }
                    $options = '<div class="hidden-sm hidden-xs action-buttons">'.$update.$delete.'</div>';
                    $options .='<div class="hidden-md hidden-lg"><div class="dropdown">
                    <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
                      '.$update_2.$delete_2.'
                    </div>
                    </div></div>';
                    $data[$i]['options'] = $options;
                }
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function select_record(string $idcategory){
            if($_SESSION['permits_module']['v']){
                $idcategory = decrypt($idcategory);
                $idcategory = intval($idcategory);
                if($idcategory > 0){
                    $data = $this->model->select_record($idcategory);
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
        public function list_categories(){
            $html = "";
            $arrData = $this->model->list_categories();
            if(count($arrData) > 0){
                for($i=0; $i < count($arrData); $i++) {
                    if($arrData[$i]['state'] == 1 ){
                        $html .= '<option value="'.encrypt($arrData[$i]['id']).'">'.$arrData[$i]['category'].'</option>';
                    }
                }
            }
            echo $html;
            die();
        }
        public function action(){
            if($_POST){
                if(empty($_POST['category'])){
                  $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios.');
                }else{
                  $id = decrypt($_POST['idcategory']);
                  $id = intval($id);
                    $category = strtoupper(strClean($_POST['category']));
                    $description = strtoupper(strClean($_POST['description']));
                    $state = intval(strClean($_POST['listStatus']));
                    $datetime = date("Y-m-d H:i:s");
                    if($id == 0){
                        $option = 1;
                        if($_SESSION['permits_module']['r']){
                            $request = $this->model->create($category,$description,$datetime,$state);
                        }
                    }else{
                        $option = 2;
                        if($_SESSION['permits_module']['a']){
                            $request = $this->model->modify($id,$category,$description,$state);
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
                    $idcategory = decrypt($_POST['idcategory']);
                    $idcategory =  intval($idcategory);
                    $request = $this->model->remove($idcategory);
                    if($request == 'success'){
                      $response = array('status' => 'success', 'msg' => 'El registro se ha eliminado.');
                    }else if($request == 'exists'){
                        $response = array('status' => 'exists', 'msg' => 'La categoria esta en uso, imposible eliminar');
                    }else{
                      $response = array('status' => 'error', 'msg' => 'Error no se pudo eliminar.');
                    }
                    echo json_encode($response,JSON_UNESCAPED_UNICODE);
                }
            }
            die();
        }
        public function import(){
            /* Variable Post */
            $file = $_FILES["import_categories"]["tmp_name"];
            /* Cargamos el archivo */
            $document = IOFactory::load($file);
            /* Hoja Productos*/
            $category_sheet = $document->getSheetByName("Categorias");
            $row_categories = $category_sheet->getHighestDataRow();
            /* Ciclo registrar productos */
            $total_categories = 0;
            for($i=2; $i <= $row_categories; $i++){
                $category = strtoupper(strClean($category_sheet->getCell("A".$i)));
                $description = strtoupper(strClean($category_sheet->getCell("B".$i)));
                $datetime = date("Y-m-d H:i:s");

                $request = $this->model->import($category,$description,$datetime);

                $total_categories = $total_categories + $request;
            }
            if($total_categories >= 1){
              $response = array('status' => 'success', 'msg' => 'La importación se realizo exitosamente.');
            }else if($total_categories == 0){
                $response = array('status' => 'warning', 'msg' => 'No se pudo importar, revise el excel en caso que realizaste mal rellenado.');
            }else{
              $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operaciòn, intentelo nuevamente');
            }
            echo json_encode($response,JSON_UNESCAPED_UNICODE);
            die();
        }
    }
