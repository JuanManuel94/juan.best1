<?php
require 'Libraries/resize/vendor/autoload.php';

use Verot\Upload\Upload;

class Business extends Controllers
{
  public function __construct()
  {
    parent::__construct();
    session_start();
    if (empty($_SESSION['login'])) {
      header('Location: ' . base_url() . '/login');
      die();
    }
    consent_permission(BUSINESS);
  }
  public function update_general()
  {
    if ($_POST) {
      if (empty($_POST['business_name']) || empty($_POST['document'])) {
        $arrResponse = array("status" => 'error', "msg" => 'Campos señalados son obligatorios.');
      } else {
        $id = $_SESSION['businessData']['id'];
        $document = strClean($_POST['document']);
        $business_name = strtoupper(strClean($_POST['business_name']));
        $tradename = strtoupper(strClean($_POST['tradename']));
        $mobile = strClean($_POST['mobile']);
        $reference = strClean($_POST['mobileReference']);
        $address = strtoupper(strClean($_POST['address']));
        $month_corte = $_POST['month_corte'];
        if ($_SESSION['permits_module']['a']) {
          $request = $this->model->update_general($id, $document, $business_name, $tradename, $mobile, $reference, $address, $month_corte);
        }
        if ($request) {
          business_session();
          $response = array('status' => 'success', 'msg' => 'Se guardaron los cambios exitosamente.');
        } else {
          $response = array("status" => 'error', "msg" => 'No es posible actualizar los datos.');
        }
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function update_basic()
  {
    if ($_POST) {
      $id = $_SESSION['businessData']['id'];
      $slogan = strtoupper(strClean($_POST['slogan']));
      $department = strtoupper(strClean($_POST['department']));
      $province = strtoupper(strClean($_POST['province']));
      $district = strtoupper(strClean($_POST['district']));
      $ubigeo = strClean($_POST['ubigeo']);
      $country = strClean($_POST['listCountry']);
      if ($_SESSION['permits_module']['a']) {
        $request = $this->model->update_basic($id, $slogan, $department, $province, $district, $ubigeo, $country);
      }
      if ($request) {
        business_session();
        $response = array('status' => 'success', 'msg' => 'Se guardaron los cambios exitosamente.');
      } else {
        $response = array("status" => 'error', "msg" => 'No es posible actualizar los datos.');
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function update_invoice()
  {
    if ($_POST) {
      $id = $_SESSION['businessData']['id'];
      $footer = $_POST['footer_text'];
      $printers = strClean($_POST['listPrinters']);
      $currency = intval(strClean($_POST['listCurrency']));
      if ($_SESSION['permits_module']['a']) {
        $request = $this->model->update_invoice($id, $footer, $currency, $printers);
      }
      if ($request) {
        business_session();
        $response = array('status' => 'success', 'msg' => 'Se guardaron los cambios exitosamente.');
      } else {
        $response = array("status" => 'error', "msg" => 'No es posible actualizar los datos.');
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function main_logo()
  {
    if ($_POST) {
      $id = $_SESSION['businessData']['id'];
      $photo = $_FILES['logo-fac'];
      $name = $photo['name'];
      $ext = explode(".", $name);
      $image = 'logo_' . md5(round(microtime(true))) . '.' . end($ext);
      $image_file = 'logo_' . md5(round(microtime(true)));
      $save_path = 'Assets/uploads/business/';
      /* REGISTRAR Y GUARDAR IMAGEN */
      if ($_SESSION['permits_module']['a']) {
        $request = $this->model->main_logo($id, $image);
      }
      if ($request == "success") {
        business_session();
        if (isset($photo)) {
          $up = new Upload($photo);
          if ($up->uploaded) {
            $up->file_new_name_body = $image_file;
            $up->image_resize = true;
            $up->image_x = 400;
            $up->image_ratio_y = true;
            $up->Process($save_path);
            if ($up->processed) {
              $up->clean();
            }
          }
        }
        if ($name != '' && $_POST['logfac-actual'] != 'superwisp.png') {
          delete_image('business', $_POST['logfac-actual']);
        }
        $response = array('status' => 'success', 'msg' => 'El logo se ha actualizado correctamente.');
      } else {
        $response = array('status' => 'error', 'msg' => 'No se pudo completar esta operación.');
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function login_logo()
  {
    if ($_POST) {
      $id = $_SESSION['businessData']['id'];
      $photo = $_FILES['logo'];
      $name = $photo['name'];
      $ext = explode(".", $name);
      $image = 'login_' . md5(round(microtime(true))) . '.' . end($ext);
      $image_file = 'login_' . md5(round(microtime(true)));
      $save_path = 'Assets/uploads/business/';
      /* REGISTRAR Y GUARDAR IMAGEN */
      if ($_SESSION['permits_module']['a']) {
        $request = $this->model->login_logo($id, $image);
      }
      if ($request == "success") {
        business_session();
        if (isset($photo)) {
          $up = new Upload($photo);
          if ($up->uploaded) {
            $up->file_new_name_body = $image_file;
            $up->image_resize = true;
            $up->image_x = 400;
            $up->image_ratio_y = true;
            $up->Process($save_path);
            if ($up->processed) {
              $up->clean();
            }
          }
        }
        if ($name != '' && $_POST['logo-actual'] != 'superwisp_white.png') {
          delete_image('business', $_POST['logo-actual']);
        }
        $response = array('status' => 'success', 'msg' => 'El logo se ha actualizado correctamente.');
      } else {
        $response = array('status' => 'error', 'msg' => 'No se pudo completar esta operación.');
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function favicon()
  {
    if ($_POST) {
      $id = $_SESSION['businessData']['id'];
      $photo = $_FILES['favicon'];
      $name = $photo['name'];
      $ext = explode(".", $name);
      $image = 'favicon_' . md5(round(microtime(true))) . '.' . end($ext);
      $image_file = 'favicon_' . md5(round(microtime(true)));
      $save_path = 'Assets/uploads/business/';
      /* REGISTRAR Y GUARDAR IMAGEN */
      if ($_SESSION['permits_module']['a']) {
        $request = $this->model->favicon($id, $image);
      }
      if ($request == "success") {
        business_session();
        if (isset($photo)) {
          $up = new Upload($photo);
          if ($up->uploaded) {
            $up->file_new_name_body = $image_file;
            $up->image_resize = true;
            $up->image_x = 32;
            $up->image_ratio_y = true;
            $up->Process($save_path);
            if ($up->processed) {
              $up->clean();
            }
          }
        }
        if ($name != '' && $_POST['fa-actual'] != 'favicon.png') {
          delete_image('business', $_POST['fa-actual']);
        }
        $response = array('status' => 'success', 'msg' => 'El favicon se ha actualizado correctamente.');
      } else {
        $response = array('status' => 'error', 'msg' => 'No se pudo completar esta operación.');
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function background()
  {
    if ($_POST) {
      $id = $_SESSION['businessData']['id'];
      $background = $_POST['background'];
      if ($_SESSION['permits_module']['a']) {
        $request = $this->model->background($id, $background);
      }
      if ($request) {
        business_session();
        $response = array('status' => 'success', 'msg' => 'Se guardaron los cambios exitosamente.');
      } else {
        $response = array("status" => 'error', "msg" => 'No es posible actualizar los datos.');
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function google()
  {
    if ($_POST) {
      $id = $_SESSION['businessData']['id'];
      $api = strClean($_POST['google_apikey']);
      if ($_SESSION['permits_module']['a']) {
        $request = $this->model->update_google($id, $api);
      }
      if ($request) {
        business_session();
        $response = array('status' => 'success', 'msg' => 'Se guardaron los cambios exitosamente.');
      } else {
        $response = array("status" => 'error', "msg" => 'No es posible actualizar los datos.');
      }

      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function reniec()
  {
    if ($_POST) {
      $id = $_SESSION['businessData']['id'];
      $api = strClean($_POST['reniec_apikey']);
      if ($_SESSION['permits_module']['a']) {
        $request = $this->model->update_reniec($id, $api);
      }
      if ($request) {
        business_session();
        $response = array('status' => 'success', 'msg' => 'Se guardaron los cambios exitosamente.');
      } else {
        $response = array("status" => 'error', "msg" => 'No es posible actualizar los datos.');
      }

      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function email()
  {
    if ($_POST) {
      $id = $_SESSION['businessData']['id'];
      $email = strClean($_POST['email']);
      $password = strClean($_POST['password']);
      $server_host = strClean($_POST['server_host']);
      $port = strClean($_POST['port']);
      $logo_email = strClean($_POST['logo_email']);
      if ($_SESSION['permits_module']['a']) {
        $request = $this->model->update_email($id, $email, $password, $server_host, $port, $logo_email);
      }
      if ($request) {
        business_session();
        $response = array('status' => 'success', 'msg' => 'Se guardaron los cambios exitosamente.');
      } else {
        $response = array("status" => 'error', "msg" => 'No es posible actualizar los datos.');
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }

  public function whatsapp()
  {
    if ($_POST) {
      $id = $_SESSION['businessData']['id'];
      if ($_SESSION['permits_module']['a']) {
        $request = $this->model->update_whatsapp($id, [
          "whatsapp_api" => strClean($_POST['whatsapp_api']),
          "whatsapp_key" => strClean($_POST['whatsapp_key'])
        ]);
      }
      if ($request) {
        $this->json([
          'status' => 'success',
          'msg' => 'Se guardaron los cambios exitosamente.'
        ]);
        business_session();
      } else {
        $this->json([
          "status" => 'error',
          "msg" => 'No es posible actualizar los datos.'
        ]);
      }
    }
  }

  public function list_database()
  {
    if ($_SESSION['permits_module']['v']) {
      $n = 1;
      $data = $this->model->list_database();
      for ($i = 0; $i < count($data); $i++) {
        $download = '';
        $delete = '';
        $data[$i]['n'] = $n++;
        $url = base_style() . "/backups/" . $data[$i]['archive'];
        if ($_SESSION['permits_module']['v']) {
          $download = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Descargar" onclick="download_files(\'' . $url . '\',\'' . $data[$i]['archive'] . '\')"><i class="fa fa-cloud-download-alt"></i></a>';
          $download_2 = '<a href="javascript:;" class="dropdown-item" onclick="download_files(\'' . $url . '\',\'' . $data[$i]['archive'] . '\')"><i class="fa fa-cloud-download-alt mr-1"></i>Descargar</a>';
        } else {
          $download = '';
          $download_2 = '';
        }
        if ($_SESSION['permits_module']['e']) {
          $delete = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Eliminar" onclick="remove(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-trash-alt"></i></a>';
          $delete_2 = '<a href="javascript:;" class="dropdown-item" onclick="remove(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-trash-alt mr-1"></i>Eliminar</a>';
        } else {
          $delete = '';
          $delete_2 = '';
        }
        $options = '<div class="hidden-sm hidden-xs action-buttons">' . $download . $delete . '</div>';
        $options .= '<div class="hidden-md hidden-lg"><div class="dropdown">
                    <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
                      ' . $download_2 . $delete_2 . '
                    </div>
                    </div></div>';
        $data[$i]['options'] = $options;
      }
      echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function create_backup()
  {
    if ($_SESSION['permits_module']['r']) {
      $request = $this->model->create_backup();
      if ($request == 'success') {
        $arrResponse = array('status' => 'success', 'msg' => 'Copia de seguridad realizada con éxito');
      } else if ($request == 'exists') {
        $arrResponse = array('status' => 'exists', 'msg' => 'Copia de seguridad del diá, ya fue creada.');
      } else {
        $arrResponse = array("status" => 'error', "msg" => 'Ocurrio un error inesperado al crear la copia de seguridad');
      }
    } else {
      $arrResponse = array("status" => 'error', "msg" => 'No tienes permisos para realizar esta acción');
    }
    echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    die();
  }
  public function remove()
  {
    if ($_POST) {
      if ($_SESSION['permits_module']['e']) {
        $idbackup = decrypt($_POST['idbackup']);
        $idbackup = intval($idbackup);
        $request = $this->model->remove($idbackup);
        if ($request == 'success') {
          $response = array('status' => 'success', 'msg' => 'Copia de seguridad eliminada correctamente.');
        } else if ($request == 'exists') {
          $response = array('status' => 'exists', 'msg' => 'Copia de seguridad, imposible eliminar');
        } else {
          $response = array('status' => 'error', 'msg' => 'Error no se pudo eliminar.');
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
      }
    }
    die();
  }
}
