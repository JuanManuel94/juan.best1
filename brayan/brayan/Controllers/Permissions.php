<?php
	class Permissions extends Controllers{
		public function __construct(){
			parent::__construct();
		}
        public function assign_permissions(){
            if($_POST){
                $idprofile = intval($_POST['idprofile']);
				$modules = $_POST['modules'];
				$this->model->remove($idprofile);
                foreach($modules as $module){
                    $idmodule = $module['idmodule'];
					$r = empty($module['r']) ? 0 : 1;
					$a = empty($module['a']) ? 0 : 1;
					$e = empty($module['e']) ? 0 : 1;
					$v = empty($module['v']) ? 0 : 1;
					$asnwer = $this->model->assign_permissions($idprofile,$idmodule,$r,$a,$e,$v);
                }
            	if($asnwer == 'success'){
                    $response = array('status' => true, 'msg' => 'Los permisos se asignaron con exito.');
                }else{
                    $response = array("status" => false, "msg" => 'No se pudieron asignar los permisos.');
                }
                echo json_encode($response,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
    }
