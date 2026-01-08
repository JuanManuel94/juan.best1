<?php
require 'Libraries/resize/vendor/autoload.php';
require 'Libraries/dompdf/vendor/autoload.php';
use Verot\Upload\Upload;
use Dompdf\Dompdf;
class Tickets extends Controllers
{
  public function __construct()
  {
    parent::__construct();
    session_start();
    if (empty($_SESSION['login'])) {
      header('Location: ' . base_url() . '/login');
      die();
    }
    consent_permission(TICKETS);
  }
  public function current()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['page_name'] = "Tickets del dia";
    $data['page_title'] = "Tickets del dia";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Tickets";
    $data['actual_page'] = "Hoy";
    $data['page_functions_js'] = "tickets_current.js";
    $this->views->getView($this, "current", $data);
  }
  public function expired()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['page_name'] = "Tickets vencidos";
    $data['page_title'] = "Tickets Vencidos";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Tickets";
    $data['actual_page'] = "Vencidos";
    $data['page_functions_js'] = "tickets_expired.js";
    $this->views->getView($this, "expired", $data);
  }
  public function resolved()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['page_name'] = "Tickets resueltos";
    $data['page_title'] = "Tickets resueltos";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Tickets";
    $data['actual_page'] = "Resueltos";
    $data['page_functions_js'] = "tickets_resolved.js";
    $this->views->getView($this, "resolved", $data);
  }
  public function tickets()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['page_name'] = "Tickets";
    $data['page_title'] = "Gestión de Tickets";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Tickets";
    $data['actual_page'] = "Lista";
    $data['page_functions_js'] = "tickets.js";
    $this->views->getView($this, "tickets", $data);
  }
  public function finalize(string $idticket)
  {
    if (empty($_SESSION['permits_module']['a'])) {
      header("Location:" . base_url() . '/tickets');
    }
    $idticket = decrypt($idticket);
    $idticket = intval($idticket);
    if ($idticket > 0) {
      $information = $this->model->select_record($idticket);
      if (empty($information)) {
        header("Location:" . base_url() . '/tickets');
      } else {
        if ($information['state'] == 6) {
          header("Location:" . base_url() . '/tickets');
        } else if ($information['state'] == 1 || $information['state'] == 2 || $information['state'] == 3 || $information['state'] == 4 || $information['state'] == 5) {
          $data['page_name'] = "Finalizar ticket";
          $data['page_name'] = "Finalizar ticket";
          $data['page_title'] = "<b>Ticket #" . str_pad($information['id'], 7, "0", STR_PAD_LEFT) . "<small> (" . ucwords(strtolower($information['names'] . " " . $information['surnames'])) . ")</small></b>";
          $data['home_page'] = "Dashboard";
          $data['previous_page'] = "Tickets";
          $data['actual_page'] = "Finalizar ticket";
          $data['page_functions_js'] = "finalize.js";
          $data['information'] = $information;
          if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) {
            if ($information['state'] == 2 || $information['state'] == 4 || $information['state'] == 5) {
              $datetime = date("Y-m-d H:i:s");
              $this->model->open_ticket($idticket, $datetime, 3);
              $this->model->reassign_technical($idticket, $_SESSION['idUser']);
            } else if ($information['state'] == 1) {
              $datetime = date("Y-m-d H:i:s");
              $this->model->open_ticket($idticket, $datetime, 3);
              $this->model->reassign_technical($idticket, $_SESSION['idUser']);
            } else {
              $this->model->reassign_technical($idticket, $_SESSION['idUser']);
            }
            $this->views->getView($this, "finalize", $data);
          } else if ($_SESSION['userData']['profileid'] == TECHNICAL) {
            if ($information['state'] == 2 || $information['state'] == 4 || $information['state'] == 5) {
              $datetime = date("Y-m-d H:i:s");
              $this->model->open_ticket($idticket, $datetime, 3);
              $this->model->reassign_technical($idticket, $_SESSION['idUser']);
            } else if ($information['state'] == 1) {
              header("Location:" . base_url() . '/tickets');
            }
            $this->views->getView($this, "finalize", $data);
          } else {
            header("Location:" . base_url() . '/tickets');
          }
        } else {
          header("Location:" . base_url() . '/tickets');
        }
      }
    } else {
      header("Location:" . base_url() . '/tickets');
    }
    die();
  }
  public function client_location(string $idclient)
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $idclient = decrypt($idclient);
    $idclient = intval($idclient);
    if ($idclient > 0) {
      $data['page_name'] = "Ubicación del cliente";
      $data['client'] = $this->model->select_client($idclient);
      $data['page_functions_js'] = "customer_location.js";
      $this->views->getView($this, "location", $data);
    } else {
      header("Location:" . base_url() . "/dashboard");
    }
    die();
  }
  public function view_pdf(string $idticket)
  {
    if ($_SESSION['permits_module']['v']) {
      if (empty($idticket)) {
        header("Location:" . base_url() . "/tickets");
      } else {
        $idticket = decrypt($idticket);
        $idticket = intval($idticket);
        if (is_numeric($idticket)) {
          $data = $this->model->select_record($idticket);
          if (empty($data)) {
            echo "Información no ha sido encontrada";
          } else {
            ob_end_clean();
            $html = redirect_pdf("Resources/reports/pdf/ticket_soporte", $data);
            $customPaper = array(0, 0, 204, 400);
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->set(array('isRemoteEnabled' => true));
            $dompdf->setOptions($options);
            $dompdf->loadHtml($html);
            $orientation = 'portrait';
            $dompdf->setPaper($customPaper, $orientation);
            $dompdf->render();
            $ticket = 'TCK-' . str_pad($data['id'], 7, "0", STR_PAD_LEFT);
            $dompdf->stream($ticket . '.pdf', array("Attachment" => false));
          }
        } else {
          echo "Información no valida";
        }
      }
    } else {
      header('Location: ' . base_url() . '/login');
      die();
    }
  }
  public function view_ticket(string $idticket)
  {
    if ($_SESSION['permits_module']['v']) {
      $idticket = decrypt($idticket);
      $idticket = intval($idticket);
      if ($idticket > 0) {
        $data = $this->model->view_ticket($idticket);
        if (empty($data)) {
          $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
        } else {
          $data['ticket']['code'] = $data['ticket']['id'];
          for ($i = 0; $i < count($data['images']); $i++) {
            $data['images'][$i]['url_image'] = base_style() . '/uploads/gallery/' . $data['images'][$i]['image'];
          }
          $answer = array('status' => 'success', 'data' => $data);
        }
      } else {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      }
      echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function list_current()
  {
    if ($_SESSION['permits_module']['v']) {
      $ticket = intVal($_GET['ticket']);
      $current = date('Y-m-d');
      if ($ticket == 0) {
        $user = 0;
      } else {
        $user = intval($_SESSION['idUser']);
      }
      $data = $this->model->list_current($user, $current);
      for ($i = 0; $i < count($data); $i++) {
        if ($data[$i]['state'] == 2) {
          $currentDate = date("Y-m-d");
          $expirationDate = date("Y-m-d", strtotime($data[$i]['attention_date']));
          if ($currentDate !== $expirationDate) {
            $current_date = new DateTime("now");
            $expiration = new DateTime($data[$i]['attention_date']);
            $diff = $expiration->diff($current_date);
            $days = $diff->invert;
            if ($days <= 0) {
              $this->model->modify_state($data[$i]['id'], 5);
            }
          }
        }
        /* ID CLIENTE */
        $data[$i]['encrypt_client'] = encrypt($data[$i]['clientid']);
        /* ID CONTRATO */
        $contract = $this->model->select_contract($data[$i]['clientid']);
        if (empty($contract)) {
          $data[$i]['encrypt_contract'] = "";
        } else {
          $data[$i]['encrypt_contract'] = encrypt($contract['id']);
        }
        /* CELULARES */
        $mobiles = '';
        if (!empty($data[$i]['mobile'])) {
          $mobiles .= '<a href="javascript:;" onclick="modal_tools(\'' . $_SESSION['businessData']['country_code'] . '\',\'' . $data[$i]['mobile'] . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-mobile mr-1"></i>' . $data[$i]['mobile'] . '</a>';
        }
        if (!empty($data[$i]['mobile_optional'])) {
          $mobiles .= '<br>';
          $mobiles .= '<a href="javascript:;" onclick="modal_tools(\'' . $_SESSION['businessData']['country_code'] . '\',\'' . $data[$i]['mobile_optional'] . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-mobile mr-1"></i>' . $data[$i]['mobile_optional'] . '</a>';
        }
        $data[$i]['cellphones'] = $mobiles;
        /* COORDENADAS */
        if (!empty($data[$i]['latitud']) || !empty($data[$i]['longitud'])) {
          $coordinates = round_out($data[$i]['latitud'], 5) . ', ' . round_out($data[$i]['longitud'], 5);
        } else {
          $coordinates = '';
        }
        $data[$i]['coordinates'] = $coordinates;
        $data[$i]['assigned'] = ($data[$i]['technical'] == 0) ? "LIBRE" : $this->model->see_technical($data[$i]['technical']);
        if ($_SESSION['permits_module']['v']) {
          $view = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Ver ticket" onclick="view(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-eye"></i></a>';
          $view_2 = '<a href="javascript:;" class="dropdown-item" onclick="view(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-eye mr-1"></i>Ver ticket</a>';
          $options_print = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Opciones" onclick="options_print(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-sun"></i></a>';
          $options_print_2 = '<a href="javascript:;" class="dropdown-item" onclick="options_print(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-sun mr-1"></i>Opciones</a>';
        } else {
          $view = '';
          $view_2 = '';
          $options_print = '';
          $options_print_2 = '';
        }
        if ($_SESSION['permits_module']['a']) {
          if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) {
            $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
            $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
            $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Atender ticket" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
            $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Atender ticket</a>';
          } else if ($_SESSION['userData']['profileid'] == TECHNICAL) {
            if ($data[$i]['state'] == 3) {
              if ($data[$i]['technical'] == $_SESSION['idUser']) {
                $update = '';
                $update_2 = '';
                $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Cerrar ticket" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
                $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Cerrar ticket</a>';
              } else {
                $update = '';
                $update_2 = '';
                $finalize = '';
                $finalize_2 = '';
              }
            } else {
              $update = '';
              $update_2 = '';
              $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Atender ticket" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
              $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Atender ticket</a>';
            }
          } else {
            $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
            $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
            $finalize = '';
            $finalize_2 = '';
          }
        } else {
          $update = '';
          $update_2 = '';
          $finalize = '';
          $finalize_2 = '';
        }
        if ($_SESSION['permits_module']['e']) {
          $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Cancelar" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban"></i></a>';
          $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban mr-1"></i>Cancelar</a>';
        } else {
          $cancel = '';
          $cancel_2 = '';
        }
        $options = '<div class="hidden-sm hidden-xs action-buttons">' . $view . $finalize . $options_print . $update . $cancel . '</div>';
        $options .= '<div class="hidden-md hidden-lg"><div class="dropdown">
            <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-ellipsis-v"></i>
            </button>
            <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
              ' . $view_2 . $finalize_2 . $options_print_2 . $update_2 . $cancel_2 . '
            </div>
            </div></div>';
        $data[$i]['options'] = $options;
      }
      echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function list_expired()
  {
    if ($_SESSION['permits_module']['v']) {
      $ticket = intVal($_GET['ticket']);
      $affair = intVal($_GET['affair']);
      $state = intVal($_GET['state']);
      if ($ticket == 0) {
        $user = 0;
      } else {
        $user = intval($_SESSION['idUser']);
      }
      $data = $this->model->list_expired($user, $affair, $state);
      for ($i = 0; $i < count($data); $i++) {
        /* ID CLIENTE */
        $data[$i]['encrypt_client'] = encrypt($data[$i]['clientid']);
        /* ID CONTRATO */
        $contract = $this->model->select_contract($data[$i]['clientid']);
        if (empty($contract)) {
          $data[$i]['encrypt_contract'] = "";
        } else {
          $data[$i]['encrypt_contract'] = encrypt($contract['id']);
        }
        /* CELULARES */
        $mobiles = '';
        if (!empty($data[$i]['mobile'])) {
          $mobiles .= '<a href="javascript:;" onclick="modal_tools(\'' . $_SESSION['businessData']['country_code'] . '\',\'' . $data[$i]['mobile'] . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-mobile mr-1"></i>' . $data[$i]['mobile'] . '</a>';
        }
        if (!empty($data[$i]['mobile_optional'])) {
          $mobiles .= '<br>';
          $mobiles .= '<a href="javascript:;" onclick="modal_tools(\'' . $_SESSION['businessData']['country_code'] . '\',\'' . $data[$i]['mobile_optional'] . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-mobile mr-1"></i>' . $data[$i]['mobile_optional'] . '</a>';
        }
        $data[$i]['cellphones'] = $mobiles;
        /* COORDENADAS */
        if (!empty($data[$i]['latitud']) || !empty($data[$i]['longitud'])) {
          $coordinates = round_out($data[$i]['latitud'], 5) . ', ' . round_out($data[$i]['longitud'], 5);
        } else {
          $coordinates = '';
        }
        $data[$i]['coordinates'] = $coordinates;
        $data[$i]['assigned'] = ($data[$i]['technical'] == 0) ? "LIBRE" : $this->model->see_technical($data[$i]['technical']);
        if ($_SESSION['permits_module']['v']) {
          $view = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Ver ticket" onclick="view(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-eye"></i></a>';
          $view_2 = '<a href="javascript:;" class="dropdown-item" onclick="view(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-eye mr-1"></i>Ver ticket</a>';
          $options_print = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Opciones" onclick="options_print(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-sun"></i></a>';
          $options_print_2 = '<a href="javascript:;" class="dropdown-item" onclick="options_print(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-sun mr-1"></i>Opciones</a>';
        } else {
          $view = '';
          $view_2 = '';
          $options_print = '';
          $options_print_2 = '';
        }
        if ($_SESSION['permits_module']['a']) {
          if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) {
            $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
            $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
            $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Atender ticket" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
            $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Atender ticket</a>';
          } else if ($_SESSION['userData']['profileid'] == TECHNICAL) {
            if ($data[$i]['state'] == 3) {
              if ($data[$i]['technical'] == $_SESSION['idUser']) {
                $update = '';
                $update_2 = '';
                $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Cerrar ticket" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
                $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Cerrar ticket</a>';
              } else {
                $update = '';
                $update_2 = '';
                $finalize = '';
                $finalize_2 = '';
              }
            } else {
              $update = '';
              $update_2 = '';
              $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Atender ticket" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
              $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Atender ticket</a>';
            }
          } else {
            $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
            $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
            $finalize = '';
            $finalize_2 = '';
          }
        } else {
          $update = '';
          $update_2 = '';
          $finalize = '';
          $finalize_2 = '';
        }
        if ($_SESSION['permits_module']['e']) {
          $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Cancelar" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban"></i></a>';
          $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban mr-1"></i>Cancelar</a>';
        } else {
          $cancel = '';
          $cancel_2 = '';
        }
        $options = '<div class="hidden-sm hidden-xs action-buttons">' . $view . $finalize . $options_print . $update . $cancel . '</div>';
        $options .= '<div class="hidden-md hidden-lg"><div class="dropdown">
              <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-ellipsis-v"></i>
              </button>
              <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
                ' . $view_2 . $finalize_2 . $options_print_2 . $update_2 . $cancel_2 . '
              </div>
              </div></div>';
        $data[$i]['options'] = $options;
      }
      echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function list_resolved()
  {
    if ($_SESSION['permits_module']['v']) {
      if (empty($_GET['closing'])) {
        $closing = date("Y-m-d");
      } else {
        $dateStart = DateTime::createFromFormat('d/m/Y', $_GET['closing']);
        $closing = $dateStart->format('Y-m-d');
      }
      $data = $this->model->list_resolved($closing);
      for ($i = 0; $i < count($data); $i++) {
        if ($data[$i]['state'] == 2) {
          $currentDate = date("Y-m-d");
          $expirationDate = date("Y-m-d", strtotime($data[$i]['attention_date']));
          if ($currentDate !== $expirationDate) {
            $current_date = new DateTime("now");
            $expiration = new DateTime($data[$i]['attention_date']);
            $diff = $expiration->diff($current_date);
            $days = $diff->invert;
            if ($days <= 0) {
              $this->model->modify_state($data[$i]['id'], 5);
            }
          }
        }
        /* ID CLIENTE */
        $data[$i]['encrypt_client'] = encrypt($data[$i]['clientid']);
        /* ID CONTRATO */
        $contract = $this->model->select_contract($data[$i]['clientid']);
        if (empty($contract)) {
          $data[$i]['encrypt_contract'] = "";
        } else {
          $data[$i]['encrypt_contract'] = encrypt($contract['id']);
        }
        /* DURACION DE TICKET */
        if ($data[$i]['attention_date'] == "0000-00-00 00:00:00" && $data[$i]['closing_date'] == "0000-00-00 00:00:00") {
          $data[$i]['duration'] = "";
        } else if (isset($data[$i]['opening_date']) && $data[$i]['closing_date'] == "0000-00-00 00:00:00") {
          $data[$i]['duration'] = "";
        } else {
          $data[$i]['duration'] = ticket_duration($data[$i]['opening_date'], $data[$i]['closing_date']);
        }
        /* CELULARES */
        $mobiles = '';
        if (!empty($data[$i]['mobile'])) {
          $mobiles .= '<a href="javascript:;" onclick="modal_tools(\'' . $_SESSION['businessData']['country_code'] . '\',\'' . $data[$i]['mobile'] . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-mobile mr-1"></i>' . $data[$i]['mobile'] . '</a>';
        }
        if (!empty($data[$i]['mobile_optional'])) {
          $mobiles .= '<br>';
          $mobiles .= '<a href="javascript:;" onclick="modal_tools(\'' . $_SESSION['businessData']['country_code'] . '\',\'' . $data[$i]['mobile_optional'] . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-mobile mr-1"></i>' . $data[$i]['mobile_optional'] . '</a>';
        }
        $data[$i]['cellphones'] = $mobiles;
        /* COORDENADAS */
        if (!empty($data[$i]['latitud']) || !empty($data[$i]['longitud'])) {
          $coordinates = round_out($data[$i]['latitud'], 5) . ', ' . round_out($data[$i]['longitud'], 5);
        } else {
          $coordinates = '';
        }
        $data[$i]['coordinates'] = $coordinates;
        $data[$i]['assigned'] = ($data[$i]['technical'] == 0) ? "LIBRE" : $this->model->see_technical($data[$i]['technical']);
        if ($_SESSION['permits_module']['v']) {
          $view = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Ver ticket" onclick="view(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-eye"></i></a>';
          $view_2 = '<a href="javascript:;" class="dropdown-item" onclick="view(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-eye mr-1"></i>Ver ticket</a>';
          $options_print = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Opciones" onclick="options_print(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-sun"></i></a>';
          $options_print_2 = '<a href="javascript:;" class="dropdown-item" onclick="options_print(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-sun mr-1"></i>Opciones</a>';
        } else {
          $view = '';
          $view_2 = '';
          $options_print = '';
          $options_print_2 = '';
        }
        if ($_SESSION['permits_module']['a']) {
          if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) {
            if ($data[$i]['state'] == 2 || $data[$i]['state'] == 4 || $data[$i]['state'] == 5) {
              $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
              $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
              $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Atender ticket" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
              $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Atender ticket</a>';
            } else if ($data[$i]['state'] == 1) {
              $update = '';
              $update_2 = '';
              $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Reaperturar ticket" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
              $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Reaperturar ticket</a>';
            } else if ($data[$i]['state'] == 3) {
              $update = '';
              $update_2 = '';
              $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Cerrar ticket" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
              $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Cerrar ticket</a>';
            } else {
              $update = '';
              $update_2 = '';
              $finalize = '';
              $finalize_2 = '';
            }
          } else {
            $update = '';
            $update_2 = '';
            $finalize = '';
            $finalize_2 = '';
          }
        } else {
          $update = '';
          $update_2 = '';
          $finalize = '';
          $finalize_2 = '';
        }
        if ($_SESSION['permits_module']['e']) {
          if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) {
            if ($data[$i]['state'] == 2 || $data[$i]['state'] == 4 || $data[$i]['state'] == 5) {
              $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Cancelar" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban"></i></a>';
              $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban mr-1"></i>Cancelar</a>';
            } else {
              $cancel = '';
              $cancel_2 = '';
            }
          } else {
            if ($data[$i]['state'] == 2 || $data[$i]['state'] == 4 || $data[$i]['state'] == 5) {
              if ($data[$i]['technical'] == 0) {
                $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Cancelar" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban"></i></a>';
                $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban mr-1"></i>Cancelar</a>';
              } else {
                $cancel = '';
                $cancel_2 = '';
              }
            } else {
              $cancel = '';
              $cancel_2 = '';
            }
          }
        } else {
          $cancel = '';
          $cancel_2 = '';
        }
        $options = '<div class="hidden-sm hidden-xs action-buttons">' . $view . $finalize . $options_print . $update . $cancel . '</div>';
        $options .= '<div class="hidden-md hidden-lg"><div class="dropdown">
                <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
                  ' . $view_2 . $finalize_2 . $options_print_2 . $update_2 . $cancel_2 . '
                </div>
                </div></div>';
        $data[$i]['options'] = $options;
      }
      echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function list_records()
  {
    if ($_SESSION['permits_module']['v']) {
      $state = intVal($_GET['state']);
      $user = intVal($_GET['user']);
      $affair = intVal($_GET['affair']);
      $data = $this->model->list_records($state, $user, $affair);
      for ($i = 0; $i < count($data); $i++) {
        if ($data[$i]['state'] == 2) {
          $currentDate = date("Y-m-d");
          $expirationDate = date("Y-m-d", strtotime($data[$i]['attention_date']));
          if ($currentDate !== $expirationDate) {
            $current_date = new DateTime("now");
            $expiration = new DateTime($data[$i]['attention_date']);
            $diff = $expiration->diff($current_date);
            $days = $diff->invert;
            if ($days <= 0) {
              $this->model->modify_state($data[$i]['id'], 5);
            }
          }
        }
        /* ID CLIENTE */
        $data[$i]['encrypt_client'] = encrypt($data[$i]['clientid']);
        /* ID CONTRATO */
        $contract = $this->model->select_contract($data[$i]['clientid']);
        if (empty($contract)) {
          $data[$i]['encrypt_contract'] = "";
        } else {
          $data[$i]['encrypt_contract'] = encrypt($contract['id']);
        }
        /* DURACION DE TICKET */
        if ($data[$i]['attention_date'] == "0000-00-00 00:00:00" && $data[$i]['closing_date'] == "0000-00-00 00:00:00") {
          $data[$i]['duration'] = "";
        } else if (isset($data[$i]['opening_date']) && $data[$i]['closing_date'] == "0000-00-00 00:00:00") {
          $data[$i]['duration'] = "";
        } else {
          $data[$i]['duration'] = ticket_duration($data[$i]['opening_date'], $data[$i]['closing_date']);
        }
        /* CELULARES */
        $mobiles = '';
        if (!empty($data[$i]['mobile'])) {
          $mobiles .= '<a href="javascript:;" onclick="modal_tools(\'' . $_SESSION['businessData']['country_code'] . '\',\'' . $data[$i]['mobile'] . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-mobile mr-1"></i>' . $data[$i]['mobile'] . '</a>';
        }
        if (!empty($data[$i]['mobile_optional'])) {
          $mobiles .= '<br>';
          $mobiles .= '<a href="javascript:;" onclick="modal_tools(\'' . $_SESSION['businessData']['country_code'] . '\',\'' . $data[$i]['mobile_optional'] . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-mobile mr-1"></i>' . $data[$i]['mobile_optional'] . '</a>';
        }
        $data[$i]['cellphones'] = $mobiles;
        /* COORDENADAS */
        if (!empty($data[$i]['latitud']) || !empty($data[$i]['longitud'])) {
          $coordinates = round_out($data[$i]['latitud'], 5) . ', ' . round_out($data[$i]['longitud'], 5);
        } else {
          $coordinates = '';
        }
        $data[$i]['coordinates'] = $coordinates;
        $data[$i]['assigned'] = ($data[$i]['technical'] == 0) ? "LIBRE" : $this->model->see_technical($data[$i]['technical']);
        if ($_SESSION['permits_module']['v']) {
          $view = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Ver ticket" onclick="view(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-eye"></i></a>';
          $view_2 = '<a href="javascript:;" class="dropdown-item" onclick="view(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-eye mr-1"></i>Ver ticket</a>';
          $options_print = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Opciones" onclick="options_print(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-sun"></i></a>';
          $options_print_2 = '<a href="javascript:;" class="dropdown-item" onclick="options_print(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-sun mr-1"></i>Opciones</a>';
        } else {
          $view = '';
          $view_2 = '';
          $options_print = '';
          $options_print_2 = '';
        }
        if ($_SESSION['permits_module']['a']) {
          if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) {
            if ($data[$i]['state'] == 2 || $data[$i]['state'] == 4 || $data[$i]['state'] == 5) {
              $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
              $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
              $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Atender ticket" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
              $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Atender ticket</a>';
            } else if ($data[$i]['state'] == 1) {
              $update = '';
              $update_2 = '';
              $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Reaperturar ticket" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
              $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Reaperturar ticket</a>';
            } else if ($data[$i]['state'] == 3) {
              $update = '';
              $update_2 = '';
              $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Cerrar ticket" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
              $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Cerrar ticket</a>';
            } else {
              $update = '';
              $update_2 = '';
              $finalize = '';
              $finalize_2 = '';
            }
          } else if ($_SESSION['userData']['profileid'] == TECHNICAL) {
            if ($data[$i]['state'] == 2 || $data[$i]['state'] == 4 || $data[$i]['state'] == 5) {
              $update = '';
              $update_2 = '';
              $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Atender ticket" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
              $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Atender ticket</a>';
            } else if ($data[$i]['state'] == 3) {
              if ($data[$i]['technical'] == $_SESSION['idUser']) {
                $update = '';
                $update_2 = '';
                $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Cerrar ticket" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
                $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Cerrar ticket</a>';
              } else {
                $update = '';
                $update_2 = '';
                $finalize = '';
                $finalize_2 = '';
              }
            } else {
              $update = '';
              $update_2 = '';
              $finalize = '';
              $finalize_2 = '';
            }
          } else {
            if ($data[$i]['state'] == 2 || $data[$i]['state'] == 4 || $data[$i]['state'] == 5) {
              $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
              $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
              $finalize = '';
              $finalize_2 = '';
            } else {
              $update = '';
              $update_2 = '';
              $finalize = '';
              $finalize_2 = '';
            }
          }
        } else {
          $update = '';
          $update_2 = '';
          $finalize = '';
          $finalize_2 = '';
        }
        if ($_SESSION['permits_module']['e']) {
          if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) {
            if ($data[$i]['state'] == 2 || $data[$i]['state'] == 4 || $data[$i]['state'] == 5) {
              $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Cancelar" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban"></i></a>';
              $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban mr-1"></i>Cancelar</a>';
            } else {
              $cancel = '';
              $cancel_2 = '';
            }
          } else {
            if ($data[$i]['state'] == 2 || $data[$i]['state'] == 4 || $data[$i]['state'] == 5) {
              if ($data[$i]['technical'] == 0) {
                $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Cancelar" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban"></i></a>';
                $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban mr-1"></i>Cancelar</a>';
              } else {
                $cancel = '';
                $cancel_2 = '';
              }
            } else {
              $cancel = '';
              $cancel_2 = '';
            }
          }
        } else {
          $cancel = '';
          $cancel_2 = '';
        }
        $options = '<div class="hidden-sm hidden-xs action-buttons">' . $view . $finalize . $options_print . $update . $cancel . '</div>';
        $options .= '<div class="hidden-md hidden-lg"><div class="dropdown">
                <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
                  ' . $view_2 . $finalize_2 . $options_print_2 . $update_2 . $cancel_2 . '
                </div>
                </div></div>';
        $data[$i]['options'] = $options;
      }
      echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function action()
  {
    if ($_POST) {
      if (empty($_POST['attention_date']) || empty($_POST['listAffairs']) || empty($_POST['listClients'])) {
        $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios.');
      } else {
        //Desencryptar idticket
        $id = decrypt($_POST['idticket']);
        $id = intval($id);
        $user = intval($_SESSION['idUser']);
        //Desencryptar idcliente
        $client = decrypt($_POST['listClients']);
        $client = intval($client);
        //Desencryptar idtecnico
        if ($_POST['listTechnical'] == "0") {
          $technical = 0;
        } else {
          $technical = decrypt($_POST['listTechnical']);
          $technical = intval($technical);
        }
        //Desencryptar idasunto
        $incidents = decrypt($_POST['listAffairs']);
        $incidents = intval($incidents);
        $description = strtoupper(strClean($_POST['description']));
        $priority = intval($_POST['listPriority']);
        $dateTicket = DateTime::createFromFormat('d/m/Y H:i', $_POST['attention_date']);
        $attention = $dateTicket->format('Y-m-d H:i:s');
        $datetime = date("Y-m-d H:i:s");
        if ($id == 0) {
          $option = 1;
          if ($_SESSION['permits_module']['r']) {
            $request = $this->model->create($user, $client, $technical, $incidents, $description, $priority, $attention, $datetime);
          }
        } else {
          $option = 2;
          if ($_SESSION['permits_module']['a']) {
            $request = $this->model->modify($id, $client, $technical, $incidents, $description, $priority, $attention);
          }
        }
        if ($request == "success") {
          if ($option == 1) {

            $ticketId = $this->model->returnTicket();
            $consult_client = (Object) $this->model->find_client($client);
            $messageWsp = new PlantillaWspInfoService($consult_client, (Object) $_SESSION['businessData']);
            $messageWsp->setTicketId($ticketId);

            $num_ticket = str_pad($ticketId, 7, "0", STR_PAD_LEFT);

            $response = array(
              'status' => 'success',
              'msg' => 'Se ha registrado el ticket #' . $num_ticket . ' exitosamente.',
              'modal' => true,
              'code' => $num_ticket,
              'encrypt' => encrypt($this->model->returnTicket()),
              'country_code' => $_SESSION['businessData']['country_code'],
              'business' => $_SESSION['businessData']['business_name'],
              'mobile' => $consult_client->mobile,
              'client' => $consult_client->names . " " . $consult_client->surnames,
              "message_wsp" => $messageWsp->execute("SUPPORT_TECNICO")
            );
          } else {
            $response = array('status' => 'success', 'msg' => 'Se ha actualizado el registro exitosamente.', 'modal' => false);
          }
        } else if ($request == 'exists') {
          $response = array('status' => 'error', 'msg' => 'Hay un ticket programado, ingrese otra fecha.');
        } else {
          $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operaciòn, intentelo nuevamente.');
        }
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function register_image()
  {
    if ($_POST) {
      if (empty($_POST['idclient']) || empty($_POST['idticket'])) {
        $response = array('status' => "error", 'msg' => 'El cliente es necesario para esta operación.');
      } else {
        /* VARIABLES PARA EL REGISTRO A LA BD*/
        $idclient = decrypt($_POST['idclient']);
        $idclient = intval($idclient);
        $iduser = intval($_SESSION['idUser']);
        $type_image = 2;//ticket
        $idticket = decrypt($_POST['idticket']);
        $idticket = intval($idticket);
        $datetime = date("Y-m-d H:i:s");
        $user = $_SESSION['userData']['names'] . " " . $_SESSION['userData']['surnames'];
        /* IMAGEN DESDE EL FORMULARIO */
        $photo = $_FILES['photo'];
        $name = $photo['name'];
        /* EXTENCION DE IMAGEN */
        $ext = explode(".", $name);
        /* OBTENER NOMBRE DEL CLIENTE */
        $consult_client = $this->model->select_client($idclient);
        $name_client = $consult_client['names'] . " " . $consult_client['surnames'];
        $formatted_name = strtolower(clear_cadena($name_client));
        $formatted_name = str_replace(" ", "_", $formatted_name);
        /* RUTA Y NOMBRE DE LA NUEVA IMAGEN */
        $image = $formatted_name . '_' . md5(round(microtime(true))) . '.' . end($ext);
        $image_file = $formatted_name . '_' . md5(round(microtime(true)));
        $save_path = 'Assets/uploads/gallery/';
        $url_image = base_style() . '/uploads/gallery/' . $formatted_name . '_' . md5(round(microtime(true))) . '.' . end($ext);
        /* REGISTRAR Y GUARDAR IMAGEN */
        if ($_SESSION['permits_module']['r']) {
          $request = $this->model->register_image($idclient, $iduser, $type_image, $idticket, $datetime, $image);
        }
        if ($request == "success") {
          if (isset($photo)) {
            $up = new Upload($photo);
            if ($up->uploaded) {
              $taken = date("d/m/Y h:i A");
              $up->file_new_name_body = $image_file;
              $up->image_resize = true;
              $up->image_x = 600;
              $up->image_ratio_y = true;
              $up->image_unsharp = true;
              $up->image_text = $user . "\n" . $taken;
              $up->image_text_alignment = 'R';
              $up->image_text_font = 35;
              $up->image_text_position = 'BR';
              $up->image_text_padding_y = 5;
              $up->image_text_x = -10;
              $up->Process($save_path);
              if ($up->processed) {
                $up->clean();
              }
            }
          }
          $response = array('status' => 'success', 'image' => $image, 'url_image' => $url_image, 'msg' => 'Imagen agregada correctamente.');
        } else {
          $response = array('status' => 'error', 'msg' => 'No se pudo completar esta operación.');
        }
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function remove_image()
  {
    if ($_POST) {
      if (empty($_POST['idticket']) || empty($_POST['file'])) {
        $response = array("status" => 'error', "msg" => 'Datos incorrectos.');
      } else {
        $idticket = decrypt($_POST['idticket']);
        $idticket = intval($idticket);
        $image = strClean($_POST['file']);
        $request = $this->model->remove_image($idticket, $image);
        if ($request == "success") {
          $delete = delete_image('gallery', $image);
          $response = array('status' => 'success', 'msg' => 'Imagen eliminada de la galeria.');
        } else {
          $response = array('status' => 'error', 'msg' => 'Error al eliminar.');
        }
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function show_images(string $idticket)
  {
    if ($_SESSION['permits_module']['v']) {
      $idticket = decrypt($idticket);
      $idticket = intval($idticket);
      if ($idticket > 0) {
        $data = $this->model->show_images($idticket);
        if (empty($data)) {
          $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada x.');
        } else {
          for ($i = 0; $i < count($data); $i++) {
            $data[$i]['url_image'] = base_style() . '/uploads/gallery/' . $data[$i]['image'];
          }
          $answer = array('status' => 'success', 'data' => $data);
        }
      } else {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      }
      echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function number_images(string $idticket)
  {
    if ($_SESSION['permits_module']['v']) {
      $idticket = decrypt($idticket);
      $idticket = intval($idticket);
      if ($idticket > 0) {
        $data = $this->model->number_images($idticket);
        if (empty($data)) {
          $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
        } else {
          $answer = array('status' => 'success', 'data' => $data);
        }
      } else {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      }
      echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function complete_ticket()
  {
    if ($_POST) {
      if (empty($_POST['idticket']) || empty($_POST['observation'])) {
        $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios.');
      } else {
        $iduser = intval($_SESSION['idUser']);
        $idticket = decrypt($_POST['idticket']);
        $idticket = intval($idticket);
        $radio_option = strClean($_POST['radio_option']);
        $observation = strtoupper(strClean($_POST['observation']));
        $closing_date = date("Y-m-d H:i:s");
        if ($_SESSION['permits_module']['a']) {
          $consult = $this->model->select_record($idticket);
          $opening_date = $consult['opening_date'];
          $technical = $consult['technical'];
          $state_ticket = $consult['state'];
          $state = ($radio_option == 1) ? 1 : 2;
          if ($state_ticket == 1) {
            $response = array("status" => 'info', "msg" => 'El ticket ya fue resuelto.');
          } else if ($state_ticket == 3) {
            if ($_SESSION['userData']['profileid'] == ADMINISTRATOR || $_SESSION['userData']['profileid'] == TECHNICAL) {
              $request = $this->model->complete_ticket($idticket, $iduser, $opening_date, $closing_date, $observation, $state);
              if ($request == "success") {
                if ($radio_option == 1) {
                  $this->model->close_ticket($idticket, $closing_date, 1);
                  $this->model->reassign_technical($idticket, $iduser);
                } else if ($radio_option == 2) {
                  $this->model->open_ticket($idticket, "0000-00-00 00:00:00", 4);
                  $this->model->close_ticket($idticket, "0000-00-00 00:00:00", 4);
                  $this->model->reassign_technical($idticket, 0);
                }
                $response = array('status' => 'success', 'msg' => 'El ticket se completo exitosamente.');
              } else {
                $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operación, intentelo nuevamente.');
              }
            } else {
              $response = array("status" => 'info', "msg" => 'Usted no tiene permiso para cerra el ticket.');
            }
          } else if ($state_ticket == 2 || $state_ticket == 4 || $state_ticket == 5 || $state_ticket == 6) {
            $response = array("status" => 'error', "msg" => 'El ticket debe estar en estado en proceso para poder completar la operación.');
          }
        }
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function select_record(string $idticket)
  {
    if ($_SESSION['permits_module']['v']) {
      $idticket = decrypt($idticket);
      $idticket = intval($idticket);
      if ($idticket > 0) {
        $data = $this->model->select_record($idticket);
        if (empty($data)) {
          $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
        } else {
          $client = $this->model->find_client($data['clientid']);
          $messageWsp = new PlantillaWspInfoService((object) $client, (object) $_SESSION['businessData']);
          $messageWsp->setTicketId($idticket);
          $data['encrypt'] = encrypt($data['id']);
          $data['encrypt_client'] = encrypt($data['clientid']);
          $data['encrypt_incident'] = encrypt($data['incidentsid']);
          $data['encrypt_technical'] = ($data['technical'] == 0) ? 0 : encrypt($data['technical']);
          $data['code'] = $data['id'];
          $data['country_code'] = $_SESSION['businessData']['country_code'];
          $data['business'] = $_SESSION['businessData']['business_name'];
          $data['mobile'] = $data['mobile'];
          $data['client'] = $data['names'] . " " . $data['surnames'];
          $data["message_wsp"] = $messageWsp->execute("SUPPORT_TECNICO");
          $answer = array('status' => 'success', 'data' => $data);
        }
      } else {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      }
      echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function list_clients()
  {
    $html = "";
    $data = $this->model->list_clients();
    if (count($data) > 0) {
      $html = '<option value="">SELECCIONAR</option>';
      for ($i = 0; $i < count($data); $i++) {
        $html .= '<option value="' . encrypt($data[$i]['clientid']) . '">' . $data[$i]['names'] . ' ' . $data[$i]['surnames'] . '</option>';
      }
    }
    echo $html;
    die();
  }
  public function list_technical()
  {
    $html = "";
    $data = $this->model->list_technical();
    if (count($data) > 0) {
      $html = '<option value="0">LIBRE</option>';
      for ($i = 0; $i < count($data); $i++) {
        $html .= '<option value="' . encrypt($data[$i]['id']) . '">' . $data[$i]['names'] . ' ' . $data[$i]['surnames'] . '</option>';
      }
    }
    echo $html;
    die();
  }
  public function filter_technical()
  {
    $html = "";
    $data = $this->model->list_technical();
    if (count($data) > 0) {
      $html = '<option value="0">TODOS</option>';
      for ($i = 0; $i < count($data); $i++) {
        $html .= '<option value="' . $data[$i]['id'] . '">' . $data[$i]['names'] . ' ' . $data[$i]['surnames'] . '</option>';
      }
    }
    echo $html;
    die();
  }
  public function cancel()
  {
    if ($_SESSION['permits_module']['e']) {
      if ($_POST) {
        $idticket = decrypt($_POST['idticket']);
        $idticket = intval($idticket);
        $request = $this->model->cancel($idticket);
        if ($request) {
          $arrResponse = array('status' => 'success', 'msg' => 'El ticket ha sido cancelado.');
        } else {
          $arrResponse = array('status' => 'error', 'msg' => 'Error no se pudo realizar esta operación.');
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
      }
    }
    die();
  }
}
