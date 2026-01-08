<?php
require 'Libraries/resize/vendor/autoload.php';
require 'Libraries/dompdf/vendor/autoload.php';
require 'Libraries/spreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Verot\Upload\Upload;
use Dompdf\Dompdf;

class Customers extends Controllers
{
  public function __construct()
  {
    parent::__construct();
    session_start();
    if (isset($_SESSION['consulta'])) {
      return;
    } else if (empty($_SESSION['login'])) {
      header('Location: ' . base_url() . '/login');
      die();
    } else {
      consent_permission(CLIENTS);
    }
  }
  /* VISTAS */
  public function customers()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['simple'] = false;
    $data['filters'] = false;
    $data['hideColumns'] = [];
    $data['page_name'] = "Clientes";
    $data['page_title'] = "Gestión de Clientes";
    $data['home_page'] = "Dashboard";
    $data['actual_page'] = "Clientes";
    $data['page_functions_js'] = "clients.js";
    $this->views->getView($this, "customers", $data);
  }
  public function add()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['page_name'] = "Nuevo cliente";
    $data['page_title'] = "Gestión de Clientes";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Clientes";
    $data['actual_page'] = "Nuevo cliente";
    $data['page_functions_js'] = "add_client.js";
    $this->views->getView($this, "add", $data);
  }
  public function view_client(string $idcontract)
  {
    if (empty($_SESSION['permits_module']['a'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $idcontract = decrypt($idcontract);
    $idcontract = intval($idcontract);
    if ($idcontract > 0) {
      $information = contract_information($idcontract);
      $data['page_name'] = "Actualizar cliente";
      $data['page_title'] = $information['client']['names'] . " " . $information['client']['surnames'];
      $data['home_page'] = "Dashboard";
      $data['previous_page'] = "Clientes";
      $data['actual_page'] = "Actualizar cliente";
      $data['page_functions_js'] = "view_client.js";
      if (empty($information)) {
        header("Location:" . base_url() . '/customers');
      } else {
        $data['contract_information'] = $information;
        $data['documents'] = $this->model->list_documents();
      }

      $routers_filter_q = sqlObject("SELECT s.routers FROM services s JOIN detail_contracts dc ON dc.serviceid = s.id AND dc.contractid = $idcontract");

      // GET PLAN ROUTERS

      $routers_filter = array_filter(explode(',', $routers_filter_q->routers));

      //GET ZONES

      $result = sql("SELECT r.*, z.name zone_name, z.mode zone_mode FROM network_routers r JOIN network_zones z ON z.id = r.zoneid");

      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

        if (in_array($row["id"], $routers_filter) || count($routers_filter) == 0) {
          $data['routers'][] = $row;
        }
      }

      $this->views->getView($this, "view", $data);
    } else {
      header("Location:" . base_url() . '/customers');
    }
    die();
  }

  public function customer_plan_routers(string $idservice)
  {
    if (empty($_SESSION['permits_module']['a'])) {
      header("Location:" . base_url() . '/dashboard');
    }

    if ($idservice) {
      $idservice = decrypt($idservice);
      $routers_filter_q = sqlObject("SELECT s.routers FROM services s WHERE s.id = $idservice");

      // GET PLAN ROUTERS

      $routers_filter = array_filter(explode(',', $routers_filter_q->routers));

      //GET ROUTERS
      $routers = array();

      $result = sql("SELECT r.*, z.name zone_name, z.mode zone_mode FROM network_routers r JOIN network_zones z ON z.id = r.zoneid");

      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

        if (in_array($row["id"], $routers_filter) || count($routers_filter) == 0) {
          $routers[] = $row;
        }
      }

      return $this->json($routers);
    }

    $data = $this->model->createQueryBuilder()
      ->from("network_routers", "r")
      ->innerJoin("network_zones z", "z.id = r.zoneid")
      ->select("r.*")
      ->addSelect("z.name", "zone_name")
      ->addSelect("z.mode", "zone_mode")
      ->getMany();

    return $this->json($data);
  }
  public function customer_map()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['page_name'] = "Mapa de clientes";
    $data['page_title'] = "Google Maps";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Clientes";
    $data['actual_page'] = "Mapa clientes";
    $data['page_functions_js'] = "customer_map.js";
    $this->views->getView($this, "customer_map", $data);
  }
  public function locations()
  {
    if ($_SESSION['permits_module']['v']) {
      $data = $this->model->locations();
      for ($i = 0; $i < count($data); $i++) {
        $images = $this->model->open_gallery($data[$i]['clientid']);
        $data[$i]['images'] = $images;
      }
      echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function customer_location(string $idclient)
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
  /* MODULO CLIENTES Y CONTRATOS */
  public function list_records(string $params)
  {
    if ($_SESSION['permits_module']['v']) {
      if (!empty($params)) {
        $arrParams = explode("-", $params);
        $state = intval($arrParams[0]);
      } else {
        $state = 0;
      }

      $filters = $_GET;
      $filters['state'] = $state;
      $data = $this->model->list_records($filters);

      for ($i = 0; $i < count($data); $i++) {
        /* VARIABLES */
        $notices = '';
        $mobiles = '';
        $mobiles_optional = '';
        $list_services = '';
        $data[$i]['permits_edit'] = $_SESSION['permits_module']['a'];
        $data[$i]['profile_user'] = $_SESSION['userData']['profileid'];
        /* ID CONTRATO ENCRYTADO */
        $data[$i]['encrypt'] = encrypt($data[$i]['id']);
        /* ID CLIENTE ENCRYTADO */
        $data[$i]['encrypt_client'] = encrypt($data[$i]['clientid']);
        /* LISTA DE SERVICOS */
        $services = $this->model->contract_services($data[$i]['id']);
        for ($p = 0; $p < count($services); $p++) {
          $list_services .= $services[$p]['service'] . "<br>";
        }
        $data[$i]['services'] = $list_services;
        /* CELULARES */
        if (!empty($data[$i]['mobile'])) {
          $mobiles .= '<a href="javascript:;" onclick="modal_tools(\'' . encrypt($data[$i]['clientid']) . '\',\'' . $data[$i]['mobile'] . '\')"><i class="fa fa-mobile mr-1"></i>' . $data[$i]['mobile'] . '</a>';
        }
        $data[$i]['cellphones'] = $mobiles;
        /* COORDENADAS */
        if (!empty($data[$i]['latitud']) || !empty($data[$i]['longitud'])) {
          $coordinates = round_out($data[$i]['latitud'], 5) . ', ' . round_out($data[$i]['longitud'], 5);
        } else {
          $coordinates = '';
        }
        $data[$i]['coordinates'] = $coordinates;
        /* DIA DE PAGO */
        $payday = str_pad($data[$i]['payday'], 2, "0", STR_PAD_LEFT);
        $data[$i]['payday'] = ($data[$i]['state'] == 5) ? 0 : $payday;
        /* ULTIMO PAGO */
        $lastpayment = $this->model->last_payment($data[$i]['clientid']);
        $data[$i]['last_payment'] = empty($lastpayment) ? "00/00/0000" : date("d/m/Y", strtotime($lastpayment));
        /* DEUDA ACTUAL */
        $balance = $this->model->outstanding_balance($data[$i]['clientid']);
        $slopes = $this->model->pending_payments($data[$i]['clientid']);
        if ($slopes >= 1) {
          $data[$i]['pending_payments'] = '<span class="badge associates badge-danger mr-1">' . $slopes . '</span>' . $_SESSION['businessData']['symbol'] . ' ' . format_money($balance);
        } else {
          $data[$i]['pending_payments'] = $_SESSION['businessData']['symbol'] . format_money($balance);
        }
        /* PROXIMO PAGO */
        $pendinService = $this->model->pending_services($data[$i]['clientid']);
        if ($pendinService >= 1) {
          $paydate = $this->model->next_payment($data[$i]['clientid']);
          $data[$i]['payment_date'] = ($paydate == '0000-00-00') ? '00/00/0000' : date("d/m/Y", strtotime($paydate . " + 1 month"));
        } else {
          $date_exp = date("Y-m-" . $payday);
          if ($data[$i]['state'] == 5) {
            $data[$i]['payment_date'] = '00/00/0000';
          } else {
            $data[$i]['payment_date'] = date("d/m/Y", strtotime($date_exp . " + 1 months"));
          }
        }
        /* BOTONES */
       if ($_SESSION['permits_module']['r']) {
        // Botón Ticket (todos los que tienen permiso 'r')
        $ticket = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Nuevo Ticket" onclick="ticket(\'' . encrypt($data[$i]['clientid']) . '\')"><i class="far fa-life-ring"></i></a>';
        $ticket_2 = '<a href="javascript:;" class="dropdown-item" onclick="ticket(\'' . encrypt($data[$i]['clientid']) . '\')"><i class="far fa-life-ring mr-1"></i>Ticket</a>';
      
        // Botón Eliminar (solo usuario con ID = 1)
        if ($_SESSION['idUser'] == 1) {
          $delete = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Eliminar" onclick="delete_client(\'' . encrypt($data[$i]['clientid']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fas fa-trash text-danger"></i></a>';
          $dlete2 = '<a href="javascript:;" class="dropdown-item text-danger" onclick="delete_client(\'' . encrypt($data[$i]['clientid']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fas fa-trash mr-1"></i>Eliminar</a>';
        } else {
          $delete = '';
          $dlete2 = '';
        }
      } else {
        $ticket = '';
        $ticket_2 = '';
        $delete = '';
        $dlete2 = '';
      }
      
        if ($_SESSION['permits_module']['e']) {
          if ($data[$i]['state'] == 1) {
            $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Cancelar" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-ban"></i></a>';
            $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-ban mr-1"></i>Cancelar</a>';
            $layoff = '';
            $layoff_2 = '';
            $activate = '';
            $activate_2 = '';
          } else if ($data[$i]['state'] == 2) {
            $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Cancelar" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-ban"></i></a>';
            $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-ban mr-1"></i>Cancelar</a>';
            $layoff = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Suspender" onclick="layoff(\'' . encrypt($data[$i]['id']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-adjust"></i></a>';
            $layoff_2 = '<a href="javascript:;" class="dropdown-item" onclick="layoff(\'' . encrypt($data[$i]['id']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-adjust mr-1"></i>Suspender</a>';
            $activate = '';
            $activate_2 = '';
          } else if ($data[$i]['state'] == 3) {
            $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Cancelar" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-ban"></i></a>';
            $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-ban mr-1"></i>Cancelar</a>';
            $layoff = '';
            $layoff_2 = '';
            $activate = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Activar" onclick="activate(\'' . encrypt($data[$i]['id']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-adjust"></i></a>';
            $activate_2 = '<a href="javascript:;" class="dropdown-item" onclick="activate(\'' . encrypt($data[$i]['id']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-adjust mr-1"></i>Activar</a>';
          } else if ($data[$i]['state'] == 4) {
            $cancel = '';
            $cancel_2 = '';
            $layoff = '';
            $layoff_2 = '';
            $activate = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Activar" onclick="activate(\'' . encrypt($data[$i]['id']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-adjust"></i></a>';
            $activate_2 = '<a href="javascript:;" class="dropdown-item" onclick="activate(\'' . encrypt($data[$i]['id']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-adjust mr-1"></i>Activar</a>';
          } else if ($data[$i]['state'] == 5) {
            $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Cancelar" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-ban"></i></a>';
            $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-ban mr-1"></i>Cancelar</a>';
            $layoff = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Suspender" onclick="layoff(\'' . encrypt($data[$i]['id']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-adjust"></i></a>';
            $layoff_2 = '<a href="javascript:;" class="dropdown-item" onclick="layoff(\'' . encrypt($data[$i]['id']) . '\',\'' . $data[$i]['client'] . '\')"><i class="fa fa-adjust mr-1"></i>Suspender</a>';
            $activate = '';
            $activate_2 = '';
          }
        } else {
          $cancel = '';
          $cancel_2 = '';
          $layoff = '';
          $layoff_2 = '';
          $activate = '';
          $activate_2 = '';
        }
        if ($_SESSION['permits_module']['a']) {
          if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) {
            $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="view(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
            $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="view(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
          } else {
            if ($data[$i]['state'] == 1) {
              $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="view(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
              $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="view(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
            } else if ($data[$i]['state'] == 2) {
              $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="view(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
              $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="view(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
            } else if ($data[$i]['state'] == 3) {
              $update = '';
              $update_2 = '';
            } else if ($data[$i]['state'] == 4) {
              $update = '';
              $update_2 = '';
            } else if ($data[$i]['state'] == 5) {
              $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="view(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
              $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="view(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
            }
          }
        } else {
          $update = '';
          $update_2 = '';
        }
        $options = '<div class="hidden-sm hidden-xs action-buttons">' . $cancel . $layoff . $activate . $update . $ticket . $delete . '</div>';
        $options .= '<div class="hidden-md hidden-lg"><div class="dropdown">
        <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-ellipsis-v"></i></button>
        <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
          ' . $cancel_2 . $layoff_2 . $activate_2 . $update_2 . $ticket_2 . '
        </div></div></div>';
        $data[$i]['options'] = $options;
      }
      $this->json($data);
    }
    die();
  }
  public function register_contract()
  {
    if ($_POST) {
      $type_document = intval(strClean($_POST['listTypes']));

      // Verificar si el tipo de documento NO es "Sin Documento" o ID 1, entonces el documento es obligatorio
      $document_required = !($type_document == 1 || $type_document == 'SIN DOCUMENTO');

      if (($document_required && empty($_POST['document'])) || empty($_POST['names']) || empty($_POST['surnames']) || empty($_POST['mobile']) || empty($_POST['address']) || empty($_POST['insDate'])) {
        $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios.');
      } else {
        $user = intval($_SESSION['idUser']);
        $names = strtoupper(strClean($_POST['names']));
        $surnames = strtoupper(strClean($_POST['surnames']));
        $type_document = intval(strClean($_POST['listTypes']));
        $document = strClean($_POST['document']);
        $mobile = strClean($_POST['mobile']);
        $mobileOp = strClean($_POST['mobileOp']);
        $zona = strClean($_POST['zonaid']);
        $email = strtolower(strClean($_POST['email']));
        $address = strClean($_POST['address']);
        $address = strtoupper(clear_cadena($address));
        $reference = strtoupper(strClean($_POST['reference']));
        $note = strtoupper(strClean($_POST['note']));
        $nap_cliente_id = strClean($_POST['nap_cliente_id']);
        $ap_cliente_id = strClean($_POST['ap_cliente_id']);
        $datetime = date("Y-m-d H:i:s");
        $modalidad = $_POST['modalidad'];

        if ($_POST['listPlan'] == 1) {
          $state = 1;
          $payday = intval($_POST['listPayday']);
          $invoice = intval(isset($_POST['listInvoice']) ? $_POST['listInvoice'] : 0);
          $daygrace = intval(isset($_POST['listDaysGrace']) ? $_POST['listDaysGrace'] : 0);
          if (isset($_POST['chkDiscount'])) {
            $discount = intval($_POST['chkDiscount']);
            $pricedisc = strClean($_POST['discount']);
            $monthdisc = intval($_POST['listMonthDis']);
          } else {
            $discount = 0;
            $pricedisc = 0;
            $monthdisc = 0;
          }
        } else {
          $state = 5;
          $payday = 0;
          $invoice = 0;
          $daygrace = 0;
          $discount = 0;
          $pricedisc = 0;
          $monthdisc = 0;
        }
        $service = decrypt($_POST['idservice']);
        $service = intval($service);
        $dateFacility = DateTime::createFromFormat('d/m/Y H:i', $_POST['insDate']);
        $insDate = $dateFacility->format('Y-m-d H:i:s');
        $price = empty($_POST['instPrice']) ? 0 : strClean($_POST['instPrice']);
        //Desencryptar idtecnico
        if ($_POST['listTechnical'] == "0") {
          $technical = 0;
        } else {
          $technical = decrypt($_POST['listTechnical']);
          $technical = intval($technical);
        }
        $detail = strClean($_POST['detail']);
        if ($_SESSION['permits_module']['r']) {
          $total = $this->model->returnCode();
          if ($total == 0) {
            $code = "CT00001";
          } else {
            $max = $this->model->generateCode();
            $code = "CT" . substr((substr($max, 2) + 100001), 1);
          }
          $existing_client = $this->model->existing_client($names, $surnames);
          if (empty($existing_client)) {
            $payload_client = [
              "names" => $names,
              "surnames" => $surnames,
              "documentid" => $type_document,
              "document" => $document,
              "mobile" => $mobile,
              "mobile_optional" => $mobileOp,
              "zonaid" => $zona,
              "email" => $email,
              "address" => $address,
              "reference" => $reference,
              "note" => $note,
              "nap_cliente_id" => $nap_cliente_id,
              "ap_cliente_id" => $ap_cliente_id,
            ];

            $request_client = $this->model->saveClient($payload_client);

            if ($request_client == 'success') {
              $idclient = $this->model->returnClient();
              $request = $this->model->create($user, $idclient, $code, $payday, $invoice, $daygrace, $discount, $pricedisc, $monthdisc, $datetime, $state, $modalidad);

              if ($request == "success") {
                $idcontract = $this->model->returnContract();
                $priceService = $this->model->returnPriceService($service);
                $request = $this->model->create_detail($idcontract, $service, $priceService, $datetime);
                $request_facility = $this->model->create_facility($idclient, $user, $technical, $insDate, $price, $detail, $datetime);
                if ($request_facility == "success") {
                  $this->addModifyNetAction = true;
                  $_POST['clientid'] = $idclient;

                  ///

                  $r = isset($_POST['netRouter']) ? sqlObject("SELECT r.*, z.mode mode FROM network_routers r JOIN network_zones z ON z.id = r.zoneid WHERE r.id = " . $_POST['netRouter']) : null;

                  if (!is_null($r->id)) {

                    $item = (object) array();

                    $clientid = $idclient;

                    if (!empty($_POST['netRouter'])) {
                      sqlUpdate("clients", "net_router", $_POST['netRouter'], $clientid);
                      sqlUpdate("clients", "net_name", $_POST['netName'] ?? '', $clientid);
                      sqlUpdate("clients", "net_password", encrypt_aes($_POST['netPassword'] ?? '', SECRET_IV), $clientid);
                      sqlUpdate("clients", "net_localaddress", $_POST['netLocalAddress'] ?? '', $clientid);
                      sqlUpdate("clients", "net_ip", $_POST['netIP'] ?? '', $clientid);
                    }
                    // WISP MANAGEMENT
                    $plan = sqlObject("SELECT s.* FROM `services` s JOIN contracts c ON c.clientid = $clientid JOIN detail_contracts cd ON cd.serviceid = s.id AND cd.contractid = c.id");
                    $profile = isset($plan->profile) ? $plan->profile : null;

                    // Formatear el ancho de banda con "M" para Mbps
                    $maxlimit = $plan->rise . "M/" . $plan->descent . "M";

                    $router = new Router($r->ip, $r->port, $r->username, decrypt_aes($r->password, SECRET_IV));
                    $sqr = $router->APIGetQueuesSimple($_POST['netIP']);

                    if (empty($profile) && $sqr->success && count($sqr->data) > 0) {
                      $sq = $sqr->data[0];
                      $address = $router->APIModifyQueuesSimple($sq->{".id"}, $_POST['netName'], $_POST['netIP'], $maxlimit);
                    } elseif (empty($profile)) {
                      $router->APIAddQueuesSimple($_POST['netName'], $_POST['netIP'], $maxlimit);
                    }

                    if ($r->mode == 2) {
                      $psr = $router->APIGetPPPSecret($_POST['netIP']);
                      if ($psr->success && count($psr->data) > 0) {
                        $ps = $psr->data[0];
                        $secret = $router->APIModifyPPPSecret(
                          $ps->{".id"},
                          $_POST['netName'],
                          $_POST['netIP'],
                          $_POST['netPassword'],
                          $_POST['netLocalAddress'],
                          $profile
                        );
                      } else {
                        $router->APIAddPPPSecret(
                          $_POST['netName'],
                          $_POST['netIP'],
                          $_POST['netPassword'],
                          $_POST['netLocalAddress'],
                          $profile
                        );
                      }
                    }
                  }

                  $response = array('status' => 'success', 'msg' => 'Se registro el cliente exitosamente.', 'id' => encrypt($idcontract));
                } else {
                  $response = array('status' => 'error', 'msg' => 'No se pudo realizar esta operaciòn, intentelo nuevamente.');
                }
              } else {
                $response = array('status' => 'error', 'msg' => 'No se pudo realizar esta operaciòn, intentelo nuevamente.');
              }
            } else {
              $response = array('status' => 'error', 'msg' => 'No se pudo realizar esta operaciòn, intentelo nuevamente.');
            }
          } else {
            $response = array('status' => 'exists', 'msg' => 'El cliente ya existe, ingrese otro.');
          }
        }
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function modify_contract()
  {
    if ($_POST) {
      if (empty($_POST['idcontract'])) {
        $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios.');
      } else {
        $id = decrypt($_POST['idcontract']);
        $id = intval($id);
        $payday = intval($_POST['listPayday']);
        $daygrace = intval($_POST['listDaysGrace']);
        $listPlan = intval($_POST['listPlan']);
        $modalidad = $_POST['modalidad'];

        if (isset($_POST['chkDiscount'])) {
          $discount = intval($_POST['chkDiscount']);
          $pricedisc = strClean($_POST['discount']);
          $monthdisc = intval($_POST['listMonthDis']);
        } else {
          $discount = 0;
          $pricedisc = 0;
          $monthdisc = 0;
        }

        if ($_SESSION['permits_module']['a']) {

          $this->model->createQueryRunner();
          $client = (object) $this->model->find_client_by_id($id);
          $contract = (object) $this->model->select_contract_by_id($id);

          $modifyState = $contract->state != $listPlan;

          try {
            $payload = [
              "days_grace" => $daygrace,
              "payday" => $payday,
              "discount" => $discount,
              "discount_price" => $pricedisc,
              "months_discount" => $monthdisc,
              "modalidad" => $modalidad,
              "state" => $listPlan
            ];

            $this->model->editContract($id, $payload);

            // validar conexión 
            if ($modifyState && $client->net_router && in_array($listPlan, [1, 2, 5])) {
              $service = new ClientActivedService((Object) $_SESSION['businessData']);
              $service->setCanTransaction(false);
              $service->setMysql($this->model);
              $service->setState($listPlan);
              $result = $service->execute($client->id);

              // validar activación
              if (!$result['success']) {
                return $this->json([
                  "status" => "error",
                  "msg" => $result["message"]
                ]);
              }
            } else if ($modifyState && $client->net_router && $listPlan == 3) {
              $service = new ClientSuspendService((Object) $_SESSION['businessData']);
              $service->setCanTransaction(false);
              $service->setMysql($this->model);
              $result = $service->execute($client->id);

              // validar suspended
              if (!$result['success']) {
                return $this->json([
                  "status" => "error",
                  "msg" => "No se pudo conectar al router"
                ]);
              }
            } else if ($modifyState && $client->net_router && $listPlan = 4) {
              $service = new ClientCancelledService((Object) $_SESSION['businessData']);
              $service->setCanTransaction(false);
              $service->setMysql($this->model);
              $result = $service->execute($client->id);

              // validar cancelled
              if (!$result['success']) {
                return $this->json([
                  "status" => "error",
                  "msg" => "No se pudo conectar al router"
                ]);
              }
            }

            $this->model->commit();
            $request = true;
          } catch (Exception $ex) {
            $this->model->rollback();
            $request = false;
          }
        }

        if ($request) {
          $response = array('status' => 'success', 'msg' => 'Se ha actualizado exitosamente.');
        } else {
          $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operaciòn, intentelo nuevamente.');
        }
      }

      return $this->json($response);
    }
    die();
  }
  public function modify_client()
  {

    if ($_POST) {
      $type_document = intval(strClean($_POST['listTypes']));

      // Verificar si el tipo de documento NO es "Sin Documento" o ID 1, entonces el documento es obligatorio
      $document_required = !($type_document == 1 || $type_document == 'SIN DOCUMENTO');

      if (
        empty($_POST['idclient']) ||
        ($document_required && empty($_POST['document'])) ||
        empty($_POST['names']) ||
        empty($_POST['surnames']) ||
        empty($_POST['mobile']) ||
        empty($_POST['address'])
      ) {

        $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios.');
      } else {
        $idclient = decrypt($_POST['idclient']);
        $idclient = intval($idclient);

        $payload = [
          "names" => strtoupper(strClean($_POST['names'])),
          "surnames" => strtoupper(strClean($_POST['surnames'])),
          "documentid" => intval(strClean($_POST['listTypes'])),
          "document" => strClean($_POST['document']),
          "mobile" => strClean($_POST['mobile']),
          "mobile_optional" => strClean($_POST['mobileOp']),
          "zonaid" => strClean($_POST['zonaid']),
          "email" => strtolower(strClean($_POST['email'])),
          "address" => strtoupper(clear_cadena(strClean($_POST['address']))),
          "reference" => strtoupper(strClean($_POST['reference'])),
          "latitud" => strClean($_POST['latitud']),
          "longitud" => strClean($_POST['longitud']),
          "note" => strClean($_POST['note']),
        ];

        if ($_SESSION['permits_module']['a']) {
          $request = $this->model->editClient($idclient, $payload);
          // $request = $this->model->modify_client($idclient, $names, $surnames, $type_document, $document, $mobile, $mobileOp, $email, $address, $reference, $note, $latitud, $longitud);
        }
        if ($request == "success") {
          $response = array('status' => 'success', 'msg' => 'Se ha actualizado el registro exitosamente.');
        } else if ($request == "exists") {
          $response = array('status' => 'exists', 'msg' => 'El cliente ya exsite.');
        } else {
          $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operaciòn, intentelo nuevamente.');
        }
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function search_document(string $params)
  {
    $arrParams = explode(",", $params);
    $type = $arrParams[0];
    $document = $arrParams[1];
    if ($type == 2) {
      $validate = strlen($document);
      if ($validate < 8) {
        $arrResponse = array('status' => 'info', 'msg' => 'El dni debe no debe tener menos de 8 digitos.');
      } else if ($validate > 8) {
        $arrResponse = array('status' => 'info', 'msg' => 'El dni debe no debe tener mas de 8 digitos.');
      } else {
        $answer = consult_document("dni", $document, $_SESSION['businessData']['reniec_apikey']);
        if (empty($answer['success'])) {
          $arrResponse = array('status' => 'error', 'msg' => 'No se encontraron resultados.');
        } else {
          $data = array(
            "names" => $answer['data']['nombres'],
            "surnames" => $answer['data']['apellido_paterno'] . " " . $answer['data']['apellido_materno'],
            "address" => $answer['data']['direccion']
          );
          $arrResponse = array('status' => 'success', 'data' => $data);
        }
      }
    }
    if ($type == 3) {
      $validate = strlen($document);
      if ($validate < 11) {
        $arrResponse = array('status' => 'info', 'msg' => 'El ruc debe no debe tener menos de 11 digitos.');
      } else if ($validate > 11) {
        $arrResponse = array('status' => 'info', 'msg' => 'El ruc debe no debe tener mas de 11 digitos.');
      } else {
        $answer = consult_document("ruc", $document, $_SESSION['businessData']['reniec_apikey']);
        if (empty($answer['success'])) {
          $arrResponse = array('status' => 'error', 'msg' => 'No se encontraron resultados.');
        } else {
          $data = array(
            "names" => $answer['data']['nombre_o_razon_social'],
            "surnames" => "-",
            "address" => $answer['data']['direccion']
          );
          $arrResponse = array('status' => 'success', 'data' => $data);
        }
      }
    }
    if ($type == 4) {
      $validate = strlen($document);
      if ($validate < 6) {
        $arrResponse = array('status' => 'info', 'msg' => 'El carnet de extranjeria no debe tener menos de 6 digitos.');
      } else if ($validate > 20) {
        $arrResponse = array('status' => 'info', 'msg' => 'El carnet de extranjeria no debe tener mas de 20 digitos.');
      } else {
        $answer = consult_document("cee", $document, $_SESSION['businessData']['reniec_apikey']);
        if (empty($answer['success'])) {
          $arrResponse = array('status' => 'error', 'msg' => 'No se encontraron resultados.');
        } else {
          $data = array(
            "names" => $answer['data']['nombres'],
            "surnames" => $answer['data']['apellido_paterno'] . " " . $answer['data']['apellido_materno'],
            "address" => $answer['data']['direccion']
          );
          $arrResponse = array('status' => 'success', 'data' => $data);
        }
      }
    }
    echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    die();
  }
  public function select_record(string $idclient)
  {
    if ($_SESSION['permits_module']['v']) {
      $idclient = decrypt($idclient);
      $idclient = intval($idclient);
      if ($idclient > 0) {
        $data = $this->model->select_record($idclient);
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
  public function select_client(string $idclient)
  {
    if ($_SESSION['permits_module']['v']) {
      $idclient = decrypt($idclient);
      $idclient = intval($idclient);
      if ($idclient > 0) {
        $data = $this->model->select_client($idclient);
        if (empty($data)) {
          $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
        } else {
          $consult = $this->model->select_contract($idclient);
          $data['country_code'] = $_SESSION['businessData']['country_code'];
          $data['business'] = $_SESSION['businessData']['business_name'];
          $data['encrypt_id'] = encrypt($data['id']);
          $list_services = "";
          $services = $this->model->contract_services($consult['id']);
          for ($p = 0; $p < count($services); $p++) {
            $list_services .= $services[$p]['service'] . " ";
          }
          $data['services'] = $list_services;
          $answer = array('status' => 'success', 'data' => $data);
        }
      } else {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      }
      echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    }
    die();
  }

  public function cancel()
  {
    if ($_SESSION['permits_module']['e']) {
      if ($_POST) {
        $idcontract = decrypt($_POST['idcontract']);
        $idcontract = intval($idcontract);
        $contract = $this->model->select_contract_by_id($idcontract);
        $service = new ClientCancelledService((Object) $_SESSION['businessData']);
        $request = $service->execute($contract['clientid']);
        if ($request['success']) {
          $response = array('status' => 'success', 'msg' => 'Se cancelo el servicio al cliente.');
        } else {
          $response = array('status' => 'error', 'msg' => $request['message']);
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
      }
    }
    die();
  }

  public function layoff()
  {
    if ($_SESSION['permits_module']['e']) {
      if ($_POST) {
        $idcontract = decrypt($_POST['idcontract']);
        $idcontract = intval($idcontract);
        $contract = $this->model->select_contract_by_id($idcontract);
        $service = new ClientSuspendService((Object) $_SESSION['businessData']);
        $request = $service->execute($contract['clientid']);

        if ($request['success']) {
          return $this->json([
            'status' => 'success',
            'msg' => 'Se suspendio el servicio al cliente.'
          ]);
        }

        return $this->json([
          'status' => 'error',
          'msg' => $request['message']
        ]);
      }
    }
    die();
  }

  public function activate()
  {
    if ($_SESSION['permits_module']['e']) {
      if ($_POST) {
        $idcontract = decrypt($_POST['idcontract']);
        $idcontract = intval($idcontract);
        $contract = $this->model->select_contract_by_id($idcontract);
        $service = new ClientActivedService((object) $_SESSION['businessData']);
        $request = $service->execute($contract['clientid']);
        if ($request['success']) {
          return $this->json([
            'status' => 'success',
            'msg' => 'Se activo el servicio al cliente.'
          ]);
        }

        return $this->json([
          'status' => 'error',
          'msg' => $request["message"]
        ]);
      }
    }
    die();
  }
  public function import()
  {
    /* Variable Post */
    $file = $_FILES["import_clients"]["tmp_name"];
    //$file = "Assets/clients.xlsx";
    /* Cargamos el archivo */
    $document = IOFactory::load($file);
    /* Hoja Productos*/
    $customer_sheet = $document->getSheetByName("Clientes");
    $row_clients = $customer_sheet->getHighestDataRow();
    /* Ciclo registrar productos */
    $total_clients = 0;
    for ($i = 2; $i <= $row_clients; $i++) {
      //Clientes
      $names = strtoupper(strClean($customer_sheet->getCell("A" . $i)));
      $surnames = strtoupper(strClean($customer_sheet->getCell("B" . $i)));
      $type_document = strClean($customer_sheet->getCell("C" . $i));
      $document = strClean($customer_sheet->getCell("D" . $i));
      $mobile = strClean($customer_sheet->getCell("E" . $i));
      $mobile_optional = strClean($customer_sheet->getCell("F" . $i));
      $email = strtolower(strClean($customer_sheet->getCell("G" . $i)));
      $address = strtoupper(strClean($customer_sheet->getCell("H" . $i)));
      $reference = strtoupper(strClean($customer_sheet->getCell("I" . $i)));
      $latitud = strClean($customer_sheet->getCell("J" . $i));
      $longitud = strClean($customer_sheet->getCell("K" . $i));
      //Contratos
      $business = intval($_SESSION['businessData']['id']);
      $user = intval($_SESSION['idUser']);
      $type_plan = strtoupper(strClean($customer_sheet->getCell("L" . $i)));
      $discount = strtoupper(strClean($customer_sheet->getCell("P" . $i)));
      //Servicios
      $service = strtoupper(strClean($customer_sheet->getCell("S" . $i)));
      $type_service = strClean($customer_sheet->getCell("T" . $i));
      $price = strClean($customer_sheet->getCell("U" . $i));
      //Instalacion
      $dateIns = DateTime::createFromFormat('d/m/Y H:i:s', $customer_sheet->getCell("V" . $i));
      $date_installation = $dateIns->format('Y-m-d H:i:s');
      $cost_installation = strClean($customer_sheet->getCell("W" . $i));
      $note = strClean($customer_sheet->getCell("X" . $i));
      $red_type = strClean($customer_sheet->getCell("Y" . $i));
      $ip = strClean($customer_sheet->getCell("Z" . $i));
      //Validar datos del contrato
      $month = 1;
      $datetime = date("Y-m-d H:i:s");
      if ($type_plan == "GRATIS") {
        $state = 5;
        $payday = 0;
        $create_invoice = 0;
        $days_grace = 0;
        $discount_value = 0;
        $discount_price = 0;
        $months_discount = 1;
      } else {
        if ($type_plan == "ACTIVO") {
          $state = 2;
        } else if ($type_plan == "SUSPENDIDO") {
          $state = 3;
        } else if ($type_plan == "CANCELADO") {
          $state = 4;
        } else {
          $state = 2;
        }
        $payday = strClean($customer_sheet->getCell("M" . $i));
        $create_invoice = strClean($customer_sheet->getCell("N" . $i));
        $days_grace = strClean($customer_sheet->getCell("O" . $i));
        if ($discount == "SI") {
          $discount_value = 1;
          $discount_price = strClean($customer_sheet->getCell("Q" . $i));
          if ($customer_sheet->getCell("R" . $i) == "0") {
            $months_discount = 1;
          } else {
            $months_discount = strClean($customer_sheet->getCell("R" . $i));
          }
        } else {
          $discount_value = 0;
          $discount_price = 0;
          $months_discount = 0;
        }
      }
      /* otro metodo */
      $existing_client = $this->model->existing_client($names, $surnames);
      if (empty($existing_client)) {
        $request_client = $this->model->import_client($names, $surnames, $type_document, $document, $mobile, $mobile_optional, $email, $address, $reference, $latitud, $longitud, $note);
        if ($request_client == "success") {
          $idclient = $this->model->returnClient();
          // SERVICIO
          $existing_service = $this->model->existing_service($service);
          if (empty($existing_service)) {
            $total_serv = $this->model->returnCodeService();
            if ($total_serv == 0) {
              $code_serv = "P00001";
            } else {
              $max_serv = $this->model->generateCodeService();
              $code_serv = "P" . substr((substr($max_serv, 1) + 100001), 1);
            }
            if ($type_service == "1") {
              $rise = "MBPS";
            }
            $request_service = $this->model->import_service($code_serv, $service, $type_service, $rise, $price, $datetime);
            if ($request_service == "success") {
              $idservice = $this->model->returnService();
            }
          } else {
            $idservice = $existing_service['id'];
          }
          // contratos
          $total = $this->model->returnCode();
          if ($total == 0) {
            $code = "CT00001";
          } else {
            $max = $this->model->generateCode();
            $code = "CT" . substr((substr($max, 2) + 100001), 1);
          }
          $request = $this->model->import($business, $user, $idclient, $code, $payday, $create_invoice, $days_grace, $discount_value, $discount_price, $months_discount, $datetime, $state);
          if ($request > 0) {
            $idcontract = $this->model->returnContract();
            $priceService = $this->model->returnPriceService($idservice);
            $this->model->create_detail($idcontract, $idservice, $priceService, $datetime);
            $request_facility = $this->model->import_facility($idclient, $user, $user, $date_installation, $cost_installation);
            if ($request_facility == "success") {
              $idfacility = $this->model->returnFacility();
              $this->model->import_detail_facility($idfacility, $user, $date_installation, $red_type, $ip);
            }
          }
          $total_clients = $total_clients + $request;
        } else {
          $total_clients = 0;
        }
      } else {
        $total_clients = 0;
      }
    }
    if ($total_clients >= 1) {
      $response = array('status' => 'success', 'msg' => 'La importación se realizo exitosamente.');
    } else if ($total_clients == 0) {
      $response = array('status' => 'warning', 'msg' => 'No se pudo importar, revise el excel en caso que realizaste mal rellenado.');
    } else {
      $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operaciòn, intentelo nuevamente.');
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    die();
  }
  public function export()
  {
    $spreadsheet = new SpreadSheet();
    $style_header = array(
      'font' => array(
        'name' => 'Calibri',
        'bold' => true,
        'color' => array(
          'rgb' => 'ffffff'
        ),
      ),
      'borders' => array(
        'outline' => array(
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
          'color' => array('rgb' => '2D3036'),
        ),
      ),
      'fill' => array(
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => array('rgb' => '2D3036'),
      ),
    );
    $spreadsheet->getActiveSheet()->getStyle('A1:S1')->applyFromArray($style_header);
    $center_cell = array(
      'alignment' => array(
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
      ),
    );
    $spreadsheet->getActiveSheet()->getStyle('A')->applyFromArray($center_cell);
    $spreadsheet->getActiveSheet()->getStyle('C:D')->applyFromArray($center_cell);
    $spreadsheet->getActiveSheet()->getStyle('I:J')->applyFromArray($center_cell);
    $spreadsheet->getActiveSheet()->getStyle('L:P')->applyFromArray($center_cell);
    $active_sheet = $spreadsheet->getActiveSheet();
    $active_sheet->setTitle("Clientes");
    $active_sheet->getColumnDimension('A')->setAutoSize(true);
    $active_sheet->setCellValue('A1', 'COD');
    $active_sheet->getColumnDimension('B')->setAutoSize(true);
    $active_sheet->setCellValue('B1', 'CLIENTE');
    $active_sheet->getColumnDimension('C')->setAutoSize(true);
    $active_sheet->setCellValue('C1', 'TIPO DOCUMENTO');
    $active_sheet->getColumnDimension('D')->setWidth(18);
    $active_sheet->setCellValue('D1', 'DOCUMENTO');
    $active_sheet->getColumnDimension('E')->setAutoSize(true);
    $active_sheet->setCellValue('E1', 'CELULARES');
    $active_sheet->getColumnDimension('F')->setAutoSize(true);
    $active_sheet->setCellValue('F1', 'CORREO');
    $active_sheet->getColumnDimension('G')->setAutoSize(true);
    $active_sheet->setCellValue('G1', 'DIRECCIÓN');
    $active_sheet->getColumnDimension('H')->setAutoSize(true);
    $active_sheet->setCellValue('H1', 'REFERENCIA');
    $active_sheet->getColumnDimension('I')->setAutoSize(true);
    $active_sheet->setCellValue('I1', 'LATITUD');
    $active_sheet->getColumnDimension('J')->setAutoSize(true);
    $active_sheet->setCellValue('J1', 'LONGITUD');
    $active_sheet->getColumnDimension('K')->setAutoSize(true);
    $active_sheet->setCellValue('K1', 'PLANES');
    $active_sheet->getColumnDimension('L')->setAutoSize(true);
    $active_sheet->setCellValue('L1', 'ULTIMO PAGO');
    $active_sheet->getColumnDimension('M')->setAutoSize(true);
    $active_sheet->setCellValue('M1', 'DIA PAGO');
    $active_sheet->getColumnDimension('N')->setAutoSize(true);
    $active_sheet->setCellValue('N1', 'DEUDA ACTUAL');
    $active_sheet->getColumnDimension('O')->setAutoSize(true);
    $active_sheet->setCellValue('O1', 'PROXIMO PAGO');
    $active_sheet->getColumnDimension('P')->setAutoSize(true);
    $active_sheet->setCellValue('P1', 'ESTADO');
    $active_sheet->setCellValue('Q1', 'NOTA');
    $active_sheet->setCellValue('R1', 'TIPO DE RED');
    $active_sheet->setCellValue('S1', 'IP');
    $data = $this->model->export();
    if (!empty($data)) {
      $i = 2;
      foreach ($data as $key => $value) {
        $mobile = '';
        if (!empty($value['mobile'])) {
          $mobile .= $value['mobile'];
        }
        if (!empty($value['mobile_optional'])) {
          $mobile .= '-';
          $mobile .= $value['mobile_optional'];
        }
        $serv = "";
        $services = $this->model->contract_services($value['id']);
        foreach ($services as $service) {
          $serv .= $service['service'] . ",";
        }
        $lastpayment = $this->model->last_payment($value['clientid']);
        $payday = str_pad($value['payday'], 2, "0", STR_PAD_LEFT);
        $balance = $this->model->outstanding_balance($value['clientid']);
        $slopes = $this->model->pending_payments($value['clientid']);
        if ($slopes == 1) {
          $pending_payments = $slopes . ' Factura ' . $_SESSION['businessData']['symbol'] . ' ' . format_money($balance);
        } else if ($slopes >= 1) {
          $pending_payments = $slopes . ' Facturas ' . $_SESSION['businessData']['symbol'] . ' ' . format_money($balance);
        } else {
          $pending_payments = '0 Facturas';
        }
        $pendinService = $this->model->pending_services($value['clientid']);
        if ($pendinService >= 1) {
          $paydate = $this->model->next_payment($value['clientid']);
          $payment_date = ($paydate == '0000-00-00') ? '00/00/0000' : date("d/m/Y", strtotime($paydate . " + 1 month"));
        } else {
          $date_exp = date("Y-m-" . $payday);
          if ($value['state'] == 5) {
            $payment_date = '00/00/0000';
          } else {
            $payment_date = date("d/m/Y", strtotime($date_exp . " + 1 months"));
          }
        }
        if ($value['state'] == 1) {
          $state = 'INSTALACIÓN';
        } else if ($value['state'] == 2) {
          $state = 'ACTIVO';
        } else if ($value['state'] == 3) {
          $state = 'SUSPENDIDO';
        } else if ($value['state'] == 4) {
          $state = 'CANCELADO';
        } else if ($value['state'] == 5) {
          $state = 'GRATIS';
        }
        $active_sheet->setCellValue('A' . $i, $value['internal_code']);
        $active_sheet->setCellValue('B' . $i, $value['names'] . " " . $value['surnames']);
        $active_sheet->setCellValue('C' . $i, $value['name_doc']);
        $active_sheet->setCellValue('D' . $i, $value['document']);
        $active_sheet->setCellValue('E' . $i, $mobile);
        $active_sheet->setCellValue('F' . $i, $value['email']);
        $active_sheet->setCellValue('G' . $i, $value['address']);
        $active_sheet->setCellValue('H' . $i, $value['reference']);
        $active_sheet->setCellValue('I' . $i, $value['latitud']);
        $active_sheet->setCellValue('J' . $i, $value['longitud']);
        $active_sheet->setCellValue('K' . $i, $serv);
        $active_sheet->setCellValue('L' . $i, empty($lastpayment) ? "00/00/0000" : date("d/m/Y", strtotime($lastpayment)));
        $active_sheet->setCellValue('M' . $i, ($value['state'] == 5) ? 0 : $payday);
        $active_sheet->setCellValue('N' . $i, $pending_payments);
        $active_sheet->setCellValue('O' . $i, $payment_date);
        $active_sheet->setCellValue('P' . $i, $state);
        $active_sheet->setCellValue('Q' . $i, $value['note']);
        if ($value['red_type'] == 2) {
          $red_type = "FIBRA ÓPTICA";
        } elseif ($value['red_type'] == 1) {
          $red_type = "INALÁMBRICA";
        } else {
          $red_type = "NO INSTALADO";
        }
        $active_sheet->setCellValue('R' . $i, $red_type);

        $ip_value = isset($value['net_ip']) ? $value['net_ip'] : 'SIN IP';
        $active_sheet->setCellValue('S' . $i, $ip_value);

        $i++;
      }
    }
    $title = 'Lista de clientes';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
    header('Cache-Control: max-age=0');
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
  }
  /* MODULO FACTURAS */
  public function list_bills(string $idclient)
  {
    if ($_SESSION['permits_module']['v']) {
      $idclient = decrypt($idclient);
      $idclient = intval($idclient);
      $data = $this->model->list_bills($idclient);
      for ($i = 0; $i < count($data); $i++) {
        /* ID FACTURA ENCRYTADO */
        $data[$i]['encrypt'] = encrypt($data[$i]['id']);
        $payments = $this->model->invoice_paid($data[$i]['id']);
        /* FACTURA DE SERVIOS O PRODUCTOS / OBTENER MES DE LA FACTURA*/
        if ($data[$i]['type'] == 1) {
          $month_letter = "";
        } else if ($data[$i]['type'] == 2) {
          $months = months();
          $month = date('n', strtotime($data[$i]['billed_month']));
          $year = date('Y', strtotime($data[$i]['billed_month']));
          $month_letter = strtoupper($months[intval($month) - 1]) . "," . $year;
        }
        /* COMPROBANTE */
        $correlative = str_pad($data[$i]['correlative'], 7, "0", STR_PAD_LEFT);
        $invoice = "#" . $correlative;
        $data[$i]['invoice'] = $correlative;
        $data[$i]['billing'] = $month_letter;
        /* FECHA DE EXPIRACION */
        $data[$i]['payment_date'] = empty($payments['payment_date']) ? "00/00/0000" : date("d/m/Y", strtotime($payments['payment_date']));
        $data[$i]['waytopay'] = empty($payments['payment_type']) ? "" : $payments['payment_type'];
        /* TOTALES PRO FACTURA */
        $data[$i]['count_total'] = $data[$i]['total'];
        $data[$i]['count_subtotal'] = empty($payments['amount_total']) ? format_money(0) : $payments['amount_total'];
        /* TOTAL PAGADO */
        $data[$i]['total'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['total']);
        $data[$i]['balance'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['remaining_amount']);
        $data[$i]['subtotal'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['subtotal']);
        $data[$i]['discount'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['discount']);
        /* ESTADO DE FACTURAS */
        if ($data[$i]['state'] == 1) {
          $state = "PAGADO";
          $data[$i]['count_state'] = "PAGADO";
        } else if ($data[$i]['state'] == 2) {
          /* VALIDAR SI LA FACTURA ESTA VENCIDA */
          $currentDate = date("Y-m-d");
          $expirationDate = $data[$i]['expiration_date'];
          if ($currentDate !== $expirationDate) {
            $current_date = new DateTime("now");
            $expiration = new DateTime($data[$i]['expiration_date']);
            $diff = $expiration->diff($current_date);
            $days = $diff->invert;
            if ($days <= 0) {
              $this->model->modify_state_bill($data[$i]['id'], 3);
            }
          }
          $state = "PENDIENTE";
          $data[$i]['count_state'] = "PENDIENTE";
        } else if ($data[$i]['state'] == 3) {
          $state = "VENCIDO";
          $data[$i]['count_state'] = "VENCIDO";
        } else if ($data[$i]['state'] == 4) {
          $state = 'ANULADO';
          $data[$i]['count_state'] = "ANULADO";
        }
        if ($_SESSION['permits_module']['v']) {
          $view = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Ver detalle" onclick="view_bill(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-eye"></i></a>';
          $view_2 = '<a href="javascript:;" class="dropdown-item" onclick="view_bill(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-eye mr-1"></i>Ver detalle</a>';
          $voucher = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Opciones" onclick="print_options(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-sun"></i></a>';
          $voucher_2 = '<a href="javascript:;" class="dropdown-item" onclick="print_options(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-sun mr-1"></i>Opciones</a>';
          if ($data[$i]['state'] != 4) {
            $email = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Enviar por correo" onclick="send_email(\'' . encrypt($data[$i]['id']) . '\',\'' . encrypt($data[$i]['clientid']) . '\',\'' . $state . '\')"><i class="fa fa-share-square"></i></a>';
            $email_2 = '<a href="javascript:;" class="dropdown-item" onclick="send_email(\'' . encrypt($data[$i]['id']) . '\',\'' . encrypt($data[$i]['clientid']) . '\',\'' . $state . '\')"><i class="fa fa-share-square mr-1"></i>Enviar por correo</a>';
          } else {
            $email = '';
            $email_2 = '';
          }
        } else {
          $view = '';
          $view_2 = '';
          $voucher = '';
          $voucher_2 = '';
          $email = '';
          $email_2 = '';
        }
        if ($_SESSION['permits_module']['a']) {
          if ($data[$i]['type'] == 1) {
            $edit = '';
            $edit_2 = '';
          } else {
            $edit = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update_invoice(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
            $edit_2 = '<a href="javascript:;" class="dropdown-item" onclick="update_invoice(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
          }
        } else {
          $edit = '';
          $edit_2 = '';
        }
        if ($_SESSION['permits_module']['r']) {
          if ($data[$i]['state'] == 2 || $data[$i]['state'] == 3) {
            $payment = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Agregar pago" onclick="make_payment(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-dollar-sign"></i></a>';
            $payment_2 = '<a href="javascript:;" class="dropdown-item" onclick="make_payment(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-dollar-sign mr-1"></i>Agregar pago</a>';
          } else {
            $payment = '';
            $payment_2 = '';
          }
        } else {
          $payment = '';
          $payment_2 = '';
        }
        if ($_SESSION['permits_module']['e']) {
          if ($data[$i]['state'] == 2 || $data[$i]['state'] == 3) {
            if ($data[$i]['amount_paid'] == 0) {
              $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Anular" onclick="cancel_invoice(\'' . encrypt($data[$i]['id']) . '\',\'' . $invoice . '\')"><i class="fa fa-ban"></i></a>';
              $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel_invoice(\'' . encrypt($data[$i]['id']) . '\',\'' . $invoice . '\')"><i class="fa fa-ban mr-1"></i>Anular</a>';
            } else {
              $cancel = '';
              $cancel_2 = '';
            }
          } else {
            $cancel = '';
            $cancel_2 = '';
          }
        } else {
          $cancel = '';
          $cancel_2 = '';
        }
        $options = '<div class="hidden-sm hidden-xs action-buttons">' . $view . $edit . $payment . $voucher . $cancel . $email . '</div>';
        $options .= '<div class="hidden-md hidden-lg"><div class="dropdown">
          <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-ellipsis-v"></i>
          </button>
          <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
            ' . $view_2 . $edit_2 . $payment_2 . $voucher_2 . $cancel_2 . $email_2 . '
          </div>
          </div></div>';
        $data[$i]['options'] = $options;
      }
      echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function action_bill()
  {
    if ($_POST) {
      if (empty($_POST['idclient']) || empty($_POST['listVouchers']) || empty($_POST['listSerie'])) {
        $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios');
      } else {
        $id = decrypt($_POST['idbill']);
        $id = intval($id);
        $user = intval(strClean($_SESSION['idUser']));
        $client = decrypt($_POST['idclient']);
        $client = intval($client);
        $voucher = decrypt($_POST['listVouchers']);
        $voucher = intval($voucher);
        $serie = decrypt($_POST['listSerie']);
        $serie = intval($serie);
        $dateIssue = DateTime::createFromFormat('d/m/Y', $_POST['issue']);
        $issue = $dateIssue->format('Y-m-d');
        $dateExpiration = DateTime::createFromFormat('d/m/Y', $_POST['expiration']);
        $expiration = $dateExpiration->format('Y-m-d');
        $subtotal = strClean($_POST['subtotal']);
        $discount = strClean($_POST['discount']);
        $total_bill = strClean($_POST['total']);
        $type = intval(strClean($_POST['typebill']));
        $method = intval(strClean($_POST['listMethod']));
        $observation = strtoupper(strClean($_POST['observation']));
        if ($id == 0) {
          $option = 1;
          if ($_SESSION['permits_module']['r']) {
            if ($type == 1) {
              $billed_month = "0000-00-00";
            } else {
              $dateBillMonth = DateTime::createFromFormat('d/m/Y', $_POST['issue']);
              $dateBillMonth->setDate((int) $dateBillMonth->format('Y'), (int) $dateBillMonth->format('m'), 1);
              $billed_month = $dateBillMonth->format('Y-m-d');
            }
            $total = $this->model->returnCodeBill();
            if ($total == 0) {
              $code = "V00001";
            } else {
              $max = $this->model->generateCodeBill();
              $code = "V" . substr((substr($max, 1) + 100001), 1);
            }
            $num_corre = $this->model->returnCorrelative($voucher, $serie);
            if (empty($num_corre)) {
              $correlative = 1;
            } else {
              $correlative = $this->model->returnUsed($voucher, $serie);
            }
            $request = $this->model->create_bill($user, $client, $voucher, $serie, $code, $correlative, $issue, $expiration, $billed_month, $subtotal, $discount, $total_bill, $type, $method, $observation);
            if ($type == 1) {
              if ($request == "success") {
                $idbill = $this->model->returnBill();
                $datetime = date("Y-m-d H:i:s");
                if ($method == 1) {
                  $row_payment = $this->model->returnCodePayment();
                  if ($row_payment == 0) {
                    $code_payment = "T00001";
                  } else {
                    $max_payment = $this->model->generateCodePayment();
                    $code_payment = "T" . substr((substr($max_payment, 1) + 100001), 1);
                  }
                  $request = $this->model->create_payment($idbill, $user, $client, $code_payment, 1, $datetime, "", "", $total_bill, $total_bill, 0, 1);
                  if ($request == "success") {
                    $this->model->modify_amounts($idbill, $total_bill, 0, 1);
                  }
                }
                $this->model->modify_available($voucher, $serie);
                $idserpro = empty($_POST['idproducto']) ? 0 : $_POST['idproducto'];
                ;
                $typepro = $_POST['tipo'];
                $description = $_POST['descripcion'];
                $quanty = $_POST['unidad'];
                $price = $_POST['costo'];
                $totald = $_POST['totales'];
                $description_departures = "VENTA DE PRODUCTO";
                if ($idserpro > 1) {
                  for ($i = 0; $i < count($idserpro); $i++) {
                    $this->model->detail_bill($idbill, $typepro[$i], $idserpro[$i], strtoupper($description[$i]), $quanty[$i], $price[$i], $totald[$i]);
                    if ($idserpro[$i] != 0) {
                      $this->model->create_departures($idbill, $idserpro[$i], $datetime, $description_departures, $quanty[$i], $price[$i], $totald[$i]);
                      $this->model->subtract_stock($idserpro[$i], $quanty[$i]);
                    }
                  }
                }
              }
            } else {
              if ($request == "success") {
                $consult_contract = $this->model->select_contract($client);
                if ($consult_contract['remaining_discount'] == 1) {
                  $this->model->discount_months($consult_contract['id']);
                  $this->model->modify_discount($consult_contract['id']);
                }
                if ($consult_contract['remaining_discount'] > 1) {
                  $this->model->discount_months($consult_contract['id']);
                }
                $idbill = $this->model->returnBill();
                $this->model->modify_available($voucher, $serie);
                $idserpro = empty($_POST['idproducto']) ? 0 : $_POST['idproducto'];
                ;
                $typepro = $_POST['tipo'];
                $description = $_POST['descripcion'];
                $quanty = $_POST['unidad'];
                $price = $_POST['costo'];
                $totald = $_POST['totales'];
                if ($idserpro > 1) {
                  for ($i = 0; $i < count($idserpro); $i++) {
                    $this->model->detail_bill($idbill, $typepro[$i], $idserpro[$i], strtoupper($description[$i]), $quanty[$i], $price[$i], $totald[$i]);
                  }
                }
              }
            }
          }
        } else {
          //editar solo factura de servicios
          $option = 2;
          if ($_SESSION['permits_module']['a']) {
            $state = intval(strClean($_POST['listStatus']));
            if ($type == 2) {
              if ($state == 1 || $state == 4) {
                $response = array('status' => 'warning', 'msg' => 'Para editar la factura esta tiene que estar en estado pendiente o vencido.');
              } else {
                $billed_month = date("Y-m-d", strtotime($expiration . " - 1 month"));
                $request = $this->model->modify_bill($id, $issue, $expiration, $billed_month, $subtotal, $discount, $total_bill, $observation, $state);
                if ($_POST['state_current'] == 1 && $state == 2 || $state == 3) {
                  $this->model->state_payments($id, 2);
                  $replacement_amounts = $this->model->replacement_amounts($id, 0, $total_bill);
                } else {
                  $voided_payments = $this->model->voided_payments($id);
                  if ($voided_payments >= 1) {
                    $total_paid = $this->model->total_paid($id);
                    if ($total_bill > $total_paid) {
                      if ($total_paid >= 1) {
                        $remaining_amount = $total_bill - $total_paid;
                        $modify_amounts = $this->model->remaining_bill($id, $remaining_amount);
                        if ($modify_amounts == "success") {
                          $last_payment = $this->model->last_paymentid($id);
                          $this->model->remaining_payments($last_payment, $remaining_amount);
                        }
                      } else if ($total_paid == 0) {
                        $modify_amounts = $this->model->remaining_bill($id, $total_bill);
                      }
                    } else if ($total_bill < $total_paid) {
                    } else if ($total_bill == $total_paid) {
                      if ($total_paid >= 1) {
                        $modify_amounts = $this->model->remaining_bill($id, 0);
                        if ($modify_amounts == "success") {
                          $last_payment = $this->model->last_paymentid($id);
                          $this->model->remaining_payments($last_payment, 0);
                        }
                      } else if ($total_paid == 0) {
                        $modify_amounts = $this->model->remaining_bill($id, $total_bill);
                      }
                    }
                  } else {
                    $modify_amounts = $this->model->remaining_bill($id, $total_bill);
                  }
                }
                $idserpro = empty($_POST['idproducto']) ? 0 : $_POST['idproducto'];
                ;
                $typepro = $_POST['tipo'];
                $description = $_POST['descripcion'];
                $quanty = $_POST['unidad'];
                $price = $_POST['costo'];
                $totald = $_POST['totales'];
                $this->model->remove_datail($id);
                if ($idserpro > 1) {
                  for ($i = 0; $i < count($idserpro); $i++) {
                    $this->model->detail_bill($id, $typepro[$i], $idserpro[$i], strtoupper($description[$i]), $quanty[$i], $price[$i], $totald[$i]);
                  }
                }
              }
            }
          }
        }
        if ($request == "success") {
          if ($option == 1) {
            $consult_bill = $this->model->select_bill($idbill);
            $consult_client = $this->model->select_client($client);
            if ($method == 1) {
              $response = array(
                'status' => 'success',
                'msg' => ucfirst(strtolower($consult_bill['voucher'])) . ' creada correctamente.',
                'modal' => true,
                'idbill' => encrypt($idbill),
                'voucher' => $consult_bill['voucher'],
                'serie' => $consult_bill['serie'] . "-" . str_pad($consult_bill['correlative'], 7, "0", STR_PAD_LEFT),
                'correlative' => str_pad($consult_bill['correlative'], 7, "0", STR_PAD_LEFT),
                'billed_month' => $consult_bill['billed_month'],
                'type' => $consult_bill['type'],
                'remaining_amount' => $consult_bill['remaining_amount'],
                'amount_paid' => $consult_bill['amount_paid'],
                'business_name' => $_SESSION['businessData']['business_name'],
                'symbol' => $_SESSION['businessData']['symbol'],
                'client' => $consult_client['names'] . " " . $consult_client['surnames'],
                'mobile' => $consult_client['mobile'],
                'country' => $_SESSION['businessData']['country_code']
              );
            } else {
              $response = array(
                'status' => 'success',
                'msg' => ucfirst(strtolower($consult_bill['voucher'])) . ' creada correctamente.',
                'modal' => false
              );
            }
          } else {
            $response = array('status' => 'success', 'msg' => 'Se ha actualizado el registro exitosamente.');
          }
        } else if ($request == 'exists') {
          $response = array('status' => 'exists', 'msg' => 'La factura ya existe, ingrese otro.');
        } else {
          $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operaciòn, intentelo nuevamente.');
        }
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function view_bill(string $idbill)
  {
    if ($_SESSION['permits_module']['v']) {
      $idbill = decrypt($idbill);
      $idbill = intval($idbill);
      if ($idbill > 0) {
        $data = $this->model->bill_voucher($idbill);
        if (empty($data)) {
          $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
        } else {
          $client = (object) $this->model->find_client($data['bill']['clientid']);
          $messageWsp = new PlantillaWspInfoService($client, (object) $_SESSION['businessData']);
          $messageWsp->setArrayBillId([$idbill]);

          $array_payments = [];
          foreach ($data['payments'] as $payment) {
            array_push($array_payments, $payment['id']);
          }

          $messageWsp->setArrayPaymentId($array_payments);
          $str_message = $messageWsp->execute("PAYMENT_PENDING");

          if ($data['bill']['state'] == 1) {
            $str_message = $messageWsp->execute("PAYMENT_CONFIRMED");
          }

          $data['bill']['encrypt_bill'] = encrypt($data['bill']['id']);
          $data['bill']['encrypt_client'] = encrypt($data['bill']['clientid']);
          $data['bill']['encrypt_voucher'] = encrypt($data['bill']['voucherid']);
          $data['bill']['encrypt_serie'] = encrypt($data['bill']['serieid']);
          $data['bill']['correlative'] = str_pad($data['bill']['correlative'], 7, "0", STR_PAD_LEFT);
          $data['bill']['serie'] = $data['bill']['serie'] . "-" . str_pad($data['bill']['correlative'], 7, "0", STR_PAD_LEFT);
          $data['bill']['client'] = $data['bill']['names'] . " " . $data['bill']['surnames'];
          $data['message_wsp'] = $str_message;
          $answer = array('status' => 'success', 'data' => $data);
        }
      } else {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      }

      return $this->json($answer);
    }
    die();
  }
  public function print_voucher(string $params)
  {
    if ($_SESSION['permits_module']['v']) {
      if (empty($params)) {
        header("Location:" . base_url() . "/customers/clients");
      } else {
        $arrParams = explode(",", $params);
        $idbill = decrypt($arrParams[0]);
        $idbill = intval($idbill);
        $type = empty($_SESSION['businessData']['print_format']) ? 'ticket' : $_SESSION['businessData']['print_format'];
        if (is_numeric($idbill)) {
          $data = $this->model->bill_voucher($idbill);
          if (empty($data)) {
            echo "Información no ha sido encontrada";
          } else {
            if ($type == 'a4') {
              redirect_file("Resources/reports/prints/print_a4", $data);
            } else {
              redirect_file("Resources/reports/prints/print_ticket", $data);
            }
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
  public function bill_voucher(string $params)
  {
    if ($_SESSION['permits_module']['v']) {
      if (empty($params)) {
        header("Location:" . base_url() . "/customers/clients");
      } else {
        $arrParams = explode(",", $params);
        $idbill = decrypt($arrParams[0]);
        $idbill = intval($idbill);
        $blade_type = strClean($arrParams[1]);
        if (empty($blade_type)) {
          $type = empty($_SESSION['businessData']['print_format']) ? 'ticket' : $_SESSION['businessData']['print_format'];
        } else {
          $type = strtolower($blade_type);
        }
        if (is_numeric($idbill)) {
          $data = $this->model->bill_voucher($idbill);
          if (empty($data)) {
            echo "Información no ha sido encontrada";
          } else {
            ob_end_clean();
            if ($type == 'a4') {
              $html = redirect_pdf("Resources/reports/pdf/invoice_a4", $data);
              $customPaper = 'A4';
            } else {
              $html = redirect_pdf("Resources/reports/pdf/invoice_ticket", $data);
              $customPaper = array(0, 0, 204, 700);
            }
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->set(array('isRemoteEnabled' => true));
            $dompdf->setOptions($options);
            $dompdf->loadHtml($html);
            $orientation = 'portrait';
            $dompdf->setPaper($customPaper, $orientation);
            $dompdf->render();
            $correlative = str_pad($data['bill']['correlative'], 7, "0", STR_PAD_LEFT);
            $voucher = $data['bill']['serie'] . '-' . $correlative;
            $dompdf->stream($voucher . '.pdf', array("Attachment" => false));
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
  public function send_email(string $params)
  {
    if (empty($params)) {
      header("Location:" . base_url() . "/customers/clients");
    } else {
      $arrParams = explode(",", $params);
      $idbill = decrypt($arrParams[0]);
      $idbill = intval($idbill);
      $idclient = decrypt($arrParams[1]);
      $idclient = intval($idclient);
      $state = strClean($arrParams[2]);
      if ($state == "PAGADO") {
        $affair = "CONSTANCIA DE PAGO";
      }
      if ($state == "PENDIENTE") {
        $affair = "FACTURA PENDIENTE DE PAGO";
      }
      if ($state == "VENCIDO") {
        $affair = "FACTURA VENCIDA CON PENDIENTE DE PAGO";
      }
      $datetime = date("Y-m-d H:i:s");
      if ($idbill > 0) {
        $data = $this->model->bill_voucher($idbill);
        if (!empty($data)) {
          if (empty($data['bill']['email'])) {
            $answer = array('status' => 'not_exist', 'msg' => 'El cliente no tiene correo electronico.');
          } else {
            $correlative = str_pad($data['bill']['correlative'], 7, "0", STR_PAD_LEFT);
            $voucher = $data['bill']['serie'] . '-' . $correlative;
            $email = array(
              'logo' => $_SESSION['businessData']['logo_email'], //logo empresa
              'name_sender' => $_SESSION['businessData']['business_name'], //nombre remitente
              'sender' => $_SESSION['businessData']['email'], //remitente
              'password' => $_SESSION['businessData']['password'], //contraseña
              'mobile' => $_SESSION['businessData']['mobile'], //celular
              'address' => $_SESSION['businessData']['address'], //celular
              'host' => $_SESSION['businessData']['server_host'], //host
              'port' => $_SESSION['businessData']['port'], //puerto
              'addressee' => $data['bill']['email'], //destinatario
              'name_addressee' => $data['bill']['names'] . " " . $data['bill']['surnames'], //nombre destinatario
              'affair' => $affair, //asunto
              'add_pdf' => true, //si mandara un pdf
              'type_pdf' => 'ticket', //tipo de pdf ticket o a4
              'data' => $data, //pdf
              'state' => $affair, //estado de la factura
              'voucher' => $data['bill']['voucher'], //comprobante
              'invoice' => $voucher, //codigo de factura
              'transaction' => $data['bill']['internal_code'], //codigo de factura
              'sub_invoice' => $data['bill']['subtotal'], //total de factura
              'dis_invoice' => $data['bill']['discount'], //total de factura
              'total_invoice' => $data['bill']['total'], //total de factura
              'issue' => $data['bill']['date_issue'], // fecha de emision
              'expiration' => $data['bill']['expiration_date'], // fecha de vencimiento
              'money_plural' => $_SESSION['businessData']['money_plural'], // moneda en plural
              'money' => $_SESSION['businessData']['money'] // moneda en singular
            );
            $result = sendMail($email, "notification");
            if ($result === true) {
              $state_email = 1;
              $request = $this->model->register_email($idclient, $idbill, $affair, $_SESSION['businessData']['email'], 'true', 'ticket', 'notification', $datetime, $state_email);
              if ($request == 'success') {
                $answer = array('status' => 'success', 'msg' => "El correo se envio correctamente.");
              } else {
                $answer = array('status' => 'error', 'msg' => "Hubo un error,reenvié el correo nuevamente.");
              }
            } else {
              $state_email = 2;
              $this->model->register_email($idclient, $idbill, $affair, $_SESSION['businessData']['email'], 'true', 'ticket', 'notification', $datetime, $state_email);
              $answer = array('status' => 'error', 'msg' => "Hubo un error,reenvié el correo nuevamente.");
            }
          }
        } else {
          $answer = array('status' => 'error', 'msg' => 'La información buscada no existe.');
        }
        echo json_encode($answer, JSON_UNESCAPED_UNICODE);
      }
    }
    die();
  }
  public function select_invoice(string $idclient)
  {
    if ($_SESSION['permits_module']['v']) {
      $idclient = decrypt($idclient);
      $idclient = intval($idclient);
      if ($idclient > 0) {
        $service = new BillInfoService();
        $data = $service->execute($idclient);
        if (empty($data)) {
          $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
        } else {
          $data['invoice']['idclient'] = $data['invoice']['clientId'];
          $data['invoice']['encrypt_client'] = encrypt($data['invoice']['clientId']);
          $data['invoice']['client'] = $data['invoice']['clientFullname'];
          $data['detail'] = $data['details'];
          $answer = array('status' => 'success', 'data' => $data);
        }
      } else {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      }
      $this->json($answer);
    }
  }
  public function select_bill(string $idbill)
  {
    if ($_SESSION['permits_module']['v']) {
      $idbill = decrypt($idbill);
      $idbill = intval($idbill);
      if ($idbill > 0) {
        $data = $this->model->select_bill($idbill);
        if (empty($data)) {
          $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
        } else {
          $data['encrypt_bill'] = encrypt($data['id']);
          $data['encrypt_client'] = encrypt($data['clientid']);
          $correlative = str_pad($data['correlative'], 7, "0", STR_PAD_LEFT);
          $data['invoice'] = $correlative;
          $answer = array('status' => 'success', 'data' => $data);
        }
      } else {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      }
      echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function cancel_bill()
  {
    if ($_SESSION['permits_module']['e']) {
      if ($_POST) {
        $idbill = decrypt($_POST['idbill']);
        $idbill = intval($idbill);
        $consult = $this->model->select_bill($idbill);
        $type = $consult['type'];
        $invoice = str_pad($consult['correlative'], 7, "0", STR_PAD_LEFT);
        $request = $this->model->cancel_bill($idbill);
        if ($request == 'success') {
          if ($type == 1) {
            $datetime = date("Y-m-d H:i:s");
            $description = "DEVOLUCIÓN DE PRODUCTO (MEDIANTE FACTURA N° " . $invoice . " ANULADA)";
            $departure = $this->model->consult_departure($idbill);
            for ($i = 0; $i < count($departure); $i++) {
              $product = $departure[$i]['productid'];
              $quantity = $departure[$i]['quantity_departures'];
              $price = $departure[$i]['unit_price'];
              $total = $departure[$i]['total_cost'];
              $this->model->increase_stock($product, $quantity);
              $this->model->create_incomes($product, $datetime, $description, $quantity, $price, $total);
            }
          }
          $response = array('status' => 'success', 'msg' => 'Factura N° ' . $invoice . ' anulada correctamente.');
        } else {
          $response = array('status' => 'error', 'msg' => 'Error no se pudo realizar esete proceso.');
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
      }
    }
    die();
  }
  /* MODULO PAGOS Y BALANCE */
  public function list_payments(string $idclient)
  {
    if ($_SESSION['permits_module']['v']) {
      $idclient = decrypt($idclient);
      $idclient = intval($idclient);
      $data = $this->model->list_payments($idclient);
      for ($i = 0; $i < count($data); $i++) {
        /* COMPROBANTE */
        $correlative = str_pad($data[$i]['correlative'], 7, "0", STR_PAD_LEFT);
        $data[$i]['invoice'] = $correlative;
        $data[$i]['amount'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['amount_paid']);
        /* ID FACTURA ENCRYTADO */
        $data[$i]['encrypt_bill'] = encrypt($data[$i]['billid']);
        if ($_SESSION['permits_module']['a']) {
          if ($data[$i]['state'] == 1) {
            $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update_payment(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
            $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update_payment(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
          } else {
            $update = '';
            $update_2 = '';
          }
        } else {
          $update = '';
          $update_2 = '';
        }
        if ($_SESSION['permits_module']['e']) {
          if ($data[$i]['state'] == 1) {
            $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Anular" onclick="cancel_payment(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban"></i></a>';
            $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel_payment(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban mr-1"></i>Anular</a>';
          } else {
            $cancel = '';
            $cancel_2 = '';
          }
        } else {
          $cancel = '';
          $cancel_2 = '';
        }
        $options = '<div class="hidden-sm hidden-xs action-buttons">' . $update . $cancel . '</div>';
        $options .= '<div class="hidden-md hidden-lg"><div class="dropdown">
          <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-ellipsis-v"></i>
          </button>
          <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
            ' . $update_2 . $cancel_2 . '
          </div>
          </div></div>';
        $data[$i]['options'] = $options;
      }
      echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function select_payment(string $idpayment)
  {
    if ($_SESSION['permits_module']['v']) {
      $idpayment = decrypt($idpayment);
      $idpayment = intval($idpayment);
      if ($idpayment > 0) {
        $data = $this->model->select_payment($idpayment);
        if (empty($data)) {
          $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
        } else {
          $correlative = str_pad($data['correlative'], 7, "0", STR_PAD_LEFT);
          $data['invoice'] = $correlative;
          $data['encrypt_id'] = encrypt($data['id']);
          $data['encrypt_bill'] = encrypt($data['billid']);
          $data['encrypt_client'] = encrypt($data['clientid']);
          $data['date'] = date("d/m/Y G:i", strtotime($data['payment_date']));
          $answer = array('status' => 'success', 'data' => $data);
        }
      } else {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      }
      echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function action_payment()
  {
    if (!$_POST) {
      return die();
    }

    if (empty($_POST['idclient']) || empty($_POST['idbill']) || empty($_POST['total_payment'])) {
      return $this->json([
        "status" => 'error',
        "msg" => 'Campos señalados son obligatorios'
      ]);
    }

    $ticket_number = $_POST['ticket_number'];
    $reference_number = $_POST['reference_number'];

    $exists = $this->model->checkTicketNumber($ticket_number);

    if ($exists) {
      return $this->json([
        "status" => 'error',
        "msg" => 'El número de boleta ya se encuentra registrado.'
      ]);
    }

    $id = decrypt($_POST['idpayment']);
    $id = intval($id);
    $billId = decrypt($_POST['idbill']);
    $userId = strClean($_SESSION['idUser']);
    $clientId = decrypt($_POST['idclient']);
    $clientId = intval($clientId);
    $client = $this->model->find_client($clientId);
    $date = DateTime::createFromFormat('d/m/Y H:i', $_POST['date_time']);
    $datetime = $date->format('Y-m-d H:i:s');
    $typepay = strClean($_POST['listTypePay']);
    $comment = strtoupper(strClean($_POST['comment']));
    $total_payment = (float) strClean($_POST['total_payment']);

    if ($id == 0) {
      $this->model->createQueryRunner();
      try {
        $business = (Object) $_SESSION['businessData'];
        $paymentBill = new PaymentBillService($business, $client, $userId, $typepay);
        $paymentBill->setMysql($this->model);
        $paymentBill->setDatetime($datetime);
        $paymentBill->setComment($comment);
        $paymentBill->execute($billId, $total_payment);
        $this->model->commit();
        return $this->json([
          "status" => "success",
          "msg" => "Los cambios fueron guardados correctamente.",
          "modal" => false
        ]);
      } catch (\Throwable $th) {
        $this->model->rollBack();
        return $this->json([
          "status" => "error",
          "msg" => "No se pudo realizar esta operación, intentelo nuevamente."
        ]);
      }
    } else {
      if ($_SESSION['permits_module']['a']) {
        $request = $this->model->modify_payment($id, $typepay, $datetime, $comment);
        if ($request == "success") {
          return $this->json([
            "status" => "success",
            "msg" => "",
            "modal" => false
          ]);
        } else {
          return $this->json([
            "status" => "error",
            "msg" => ""
          ]);
        }
      }
    }
  }

  public function cancel_payment()
  {
    if ($_POST) {
      if ($_SESSION['permits_module']['e']) {
        $idpayment = decrypt($_POST['idpayment']);
        $idpayment = intval($idpayment);
        $payment = $this->model->select_payment($idpayment);
        if (!empty($payment)) {
          $idbill = $payment['billid'];
          $amount_paid = $payment['amount_paid'];
          $bill = $this->model->select_bill($idbill);
          $remaining_amount = $bill['remaining_amount'];
          if ($remaining_amount == 0) {
            $this->model->subtract_amounts($idbill, $amount_paid, 2);
          } else if ($remaining_amount > 0) {
            $this->model->subtract_amounts($idbill, $amount_paid, 0);
          }
          $request = $this->model->cancel_payment($idpayment);
          if ($request == 'success') {
            $response = array('status' => 'success', 'msg' => 'Pago eliminado correctamente.');
          } else {
            $response = array('status' => 'error', 'msg' => 'Error no se pudo eliminar.');
          }
        } else {
          $response = array('status' => 'error', 'msg' => 'La transacción no exite.');
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
      }
    }
    die();
  }
  /* MODULO TICKETS */
  public function action_ticket()
  {
    if ($_POST) {
      if (empty($_POST['attention_date']) || empty($_POST['listAffairs']) || empty($_POST['idclient'])) {
        $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios.');
      } else {
        //Desencryptar idticket
        $id = decrypt($_POST['idticket']);
        $id = intval($id);
        $user = intval($_SESSION['idUser']);
        //Desencryptar idcliente
        $client = decrypt($_POST['idclient']);
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
            $request = $this->model->create_ticket($user, $client, $technical, $incidents, $description, $priority, $attention, $datetime);
          }
        } else {
          $option = 2;
          if ($_SESSION['permits_module']['a']) {
            $request = $this->model->modify_ticket($id, $client, $technical, $incidents, $description, $priority, $attention);
          }
        }
        if ($request == "success") {
          if ($option == 1) {
            $consult_client = $this->model->select_client($client);
            $num_ticket = str_pad($this->model->returnTicket(), 7, "0", STR_PAD_LEFT);
            $response = array(
              'status' => 'success',
              'msg' => 'Se ha registrado el ticket #' . $num_ticket . ' exitosamente.',
              'modal' => true,
              'code' => $num_ticket,
              'encrypt' => encrypt($this->model->returnTicket()),
              'country_code' => $_SESSION['businessData']['country_code'],
              'business' => $_SESSION['businessData']['business_name'],
              'mobile' => $consult_client['mobile'],
              'client' => $consult_client['names'] . " " . $consult_client['surnames']
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
          $consult = $this->model->select_ticket($idticket);
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
  public function list_ticket(string $idclient)
  {
    if ($_SESSION['permits_module']['v']) {
      $idclient = decrypt($idclient);
      $idclient = intval($idclient);
      $data = $this->model->list_ticket($idclient);
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
              $this->model->modify_state_ticket($data[$i]['id'], 5);
            }
          }
        }
        /* DURACION DE TICKET */
        if ($data[$i]['attention_date'] == "0000-00-00 00:00:00" && $data[$i]['closing_date'] == "0000-00-00 00:00:00") {
          $data[$i]['duration'] = "";
        } else if (isset($data[$i]['opening_date']) && $data[$i]['closing_date'] == "0000-00-00 00:00:00") {
          $data[$i]['duration'] = "";
        } else {
          $data[$i]['duration'] = ticket_duration($data[$i]['opening_date'], $data[$i]['closing_date']);
        }
        $data[$i]['assigned'] = ($data[$i]['technical'] == 0) ? "LIBRE" : $this->model->see_technical($data[$i]['technical']);
        if ($_SESSION['permits_module']['v']) {
          $view = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Ver ticket" onclick="view_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-eye"></i></a>';
          $view_2 = '<a href="javascript:;" class="dropdown-item" onclick="view_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-eye mr-1"></i>Ver ticket</a>';
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
              $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
              $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
              $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Atender ticket" onclick="finalize_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
              $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Atender ticket</a>';
            } else if ($data[$i]['state'] == 1) {
              $update = '';
              $update_2 = '';
              $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Reaperturar ticket" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
              $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Reaperturar ticket</a>';
            } else if ($data[$i]['state'] == 3) {
              $update = '';
              $update_2 = '';
              $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Cerrar ticket" onclick="finalize_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
              $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Cerrar ticket</a>';
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
              $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Atender ticket" onclick="finalize_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
              $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Atender ticket</a>';
            } else if ($data[$i]['state'] == 3) {
              if ($data[$i]['technical'] == $_SESSION['idUser']) {
                $update = '';
                $update_2 = '';
                $finalize = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Cerrar ticket" onclick="finalize_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle"></i></a>';
                $finalize_2 = '<a href="javascript:;" class="dropdown-item" onclick="finalize_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fas fa-check-circle mr-1"></i>Cerrar ticket</a>';
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
              $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
              $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
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
              $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Cancelar" onclick="cancel_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban"></i></a>';
              $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban mr-1"></i>Cancelar</a>';
            } else {
              $cancel = '';
              $cancel_2 = '';
            }
          } else {
            if ($data[$i]['state'] == 2 || $data[$i]['state'] == 4 || $data[$i]['state'] == 5) {
              if ($data[$i]['technical'] == 0) {
                $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Cancelar" onclick="cancel_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban"></i></a>';
                $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel_ticket(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban mr-1"></i>Cancelar</a>';
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
          $answer = array('status' => 'success', 'data' => $data);
        }
      } else {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      }
      echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function select_ticket(string $idticket)
  {
    if ($_SESSION['permits_module']['v']) {
      $idticket = decrypt($idticket);
      $idticket = intval($idticket);
      if ($idticket > 0) {
        $data = $this->model->select_ticket($idticket);
        if (empty($data)) {
          $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
        } else {
          $data['encrypt'] = encrypt($data['id']);
          $data['encrypt_client'] = encrypt($data['clientid']);
          $data['encrypt_incident'] = encrypt($data['incidentsid']);
          $data['encrypt_technical'] = ($data['technical'] == 0) ? 0 : encrypt($data['technical']);
          $data['code'] = $data['id'];
          $data['country_code'] = $_SESSION['businessData']['country_code'];
          $data['business'] = $_SESSION['businessData']['business_name'];
          $data['mobile'] = $data['mobile'];
          $data['client'] = $data['names'] . " " . $data['surnames'];
          $answer = array('status' => 'success', 'data' => $data);
        }
      } else {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      }
      echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function view_pdf(string $idticket)
  {
    if ($_SESSION['permits_module']['v']) {
      if (empty($idticket)) {
        header("Location:" . base_url() . "/customers");
      } else {
        $idticket = decrypt($idticket);
        $idticket = intval($idticket);
        if (is_numeric($idticket)) {
          $data = $this->model->select_ticket($idticket);
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
  public function finalize(string $idticket)
  {
    if ($_SESSION['permits_module']['a']) {
      $idticket = decrypt($idticket);
      $idticket = intval($idticket);
      if ($idticket > 0) {
        $information = $this->model->select_ticket($idticket);
        if (empty($information)) {
          $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
        } else {
          if ($information['state'] == 1) {
            $answer = array('status' => 'info', 'msg' => 'El ticket ya esta resuelto.');
          }
          if ($information['state'] == 6) {
            $answer = array('status' => 'info', 'msg' => 'El ticket esta cancelado.');
          } else if ($information['state'] == 2 || $information['state'] == 3 || $information['state'] == 4 || $information['state'] == 5) {
            $information['code'] = str_pad($information['id'], 7, "0", STR_PAD_LEFT);
            $information['encrypt_ticket'] = encrypt($information['id']);
            if ($information['state'] == 2 || $information['state'] == 4 || $information['state'] == 5) {
              $datetime = date("Y-m-d H:i:s");
              $this->model->open_ticket($idticket, $datetime, 3);
              $this->model->reassign_technical($idticket, $_SESSION['idUser']);
            }
            $answer = array('status' => 'success', 'data' => $information);
          }
        }
      } else {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      }
      echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    }
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
  public function cancel_ticket()
  {
    if ($_SESSION['permits_module']['e']) {
      if ($_POST) {
        $idticket = decrypt($_POST['idticket']);
        $idticket = intval($idticket);
        $request = $this->model->cancel_ticket($idticket);
        if ($request) {
          $arrResponse = array('status' => 'success', 'msg' => 'El ticket ha sido cancelado.');
        } else {
          $arrResponse = array('status' => 'error', 'msg' => 'Error no se pudo publicar cuestinario.');
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
      }
    }
    die();
  }
  /* MODULO DETALLE DE CONTRATOS Y SERVICIOS */
  public function list_internet(string $idcontract)
  {
    if ($_SESSION['permits_module']['v']) {
      $idcontract = decrypt($idcontract);
      $idcontract = intval($idcontract);
      $data = $this->model->list_internet($idcontract);
      for ($i = 0; $i < count($data); $i++) {
        $data[$i]['price'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['price']);
        $data[$i]['max_rise'] = "<strong class='mr-1'><i class='fa fa-arrow-up text-green mr-1'></i>" . $data[$i]['rise'] . "</strong>" . $data[$i]['rise_type'];
        $data[$i]['max_descent'] = "<strong class='mr-1'><i class='fa fa-arrow-down text-danger mr-1'></i>" . $data[$i]['descent'] . "</strong>" . $data[$i]['descent_type'];
        if ($_SESSION['permits_module']['a']) {
          $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update_service(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
          $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update_service(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
        } else {
          $update = '';
          $update_2 = '';
        }
        if ($_SESSION['permits_module']['e']) {
          $delete = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Eliminar" onclick="remove_detail(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-trash-alt"></i></a>';
          $delete_2 = '<a href="javascript:;" class="dropdown-item" onclick="remove_detail(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-trash-alt mr-1"></i>Eliminar</a>';
        } else {
          $delete = '';
          $delete_2 = '';
        }
        $options = '<div class="hidden-sm hidden-xs action-buttons">' . $update . $delete . '</div>';
        $options .= '<div class="hidden-md hidden-lg"><div class="dropdown">
            <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-ellipsis-v"></i>
            </button>
            <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
              ' . $update_2 . $delete_2 . '
            </div>
            </div></div>';
        $data[$i]['options'] = $options;
      }
      echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function list_personalized(string $idcontract)
  {
    if ($_SESSION['permits_module']['v']) {
      $idcontract = decrypt($idcontract);
      $idcontract = intval($idcontract);
      $data = $this->model->list_personalized($idcontract);
      for ($i = 0; $i < count($data); $i++) {
        $data[$i]['price'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['price']);
        if ($_SESSION['permits_module']['a']) {
          $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update_service(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
          $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update_service(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
        } else {
          $update = '';
          $update_2 = '';
        }
        if ($_SESSION['permits_module']['e']) {
          $delete = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Eliminar" onclick="remove_detail(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-trash-alt"></i></a>';
          $delete_2 = '<a href="javascript:;" class="dropdown-item" onclick="remove_detail(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-trash-alt mr-1"></i>Eliminar</a>';
        } else {
          $delete = '';
          $delete_2 = '';
        }
        $options = '<div class="hidden-sm hidden-xs action-buttons">' . $update . $delete . '</div>';
        $options .= '<div class="hidden-md hidden-lg"><div class="dropdown">
            <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-ellipsis-v"></i>
            </button>
            <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
              ' . $update_2 . $delete_2 . '
            </div>
            </div></div>';
        $data[$i]['options'] = $options;
      }
      echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function search_service()
  {
    if ($_POST) {
      $html = "";
      $search = strClean($_POST['search']);
      $arrData = $this->model->search_service($search);
      if (empty($arrData)) {
        $html .= '<li>No se encontro "' . $search . '"</li>';
      } else {
        foreach ($arrData as $row) {
          if ($row['type'] == 1) {
            $html .= '<li onclick="select_service(\'' . encrypt($row['id']) . '\')">' . $row['service'] . ' - ' . $_SESSION['businessData']['symbol'] . $row['price'] . ' - INTERNET DE BAJADA: ' . $row['descent'] . ' - SUBIDA: ' . $row['rise'] . '</li>';
          } else {
            $html .= '<li onclick="select_service(\'' . encrypt($row['id']) . '\')">' . $row['service'] . ' - ' . $_SESSION['businessData']['symbol'] . $row['price'] . '</li>';
          }
        }
      }
      echo $html;
    }
    die();
  }
  public function select_service(string $idservices)
  {
    $idservices = decrypt($idservices);
    $idservices = intval($idservices);
    if ($idservices > 0) {
      $data = $this->model->select_service($idservices);
      if (empty($data)) {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      } else {
        $data['encrypt_id'] = encrypt($data['id']);
        $answer = array('status' => 'success', 'data' => $data);
      }
    } else {
      $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
    }
    echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    die();
  }
  public function select_detail(string $iddetail)
  {
    if ($_SESSION['permits_module']['v']) {
      $iddetail = decrypt($iddetail);
      $iddetail = intval($iddetail);
      if ($iddetail > 0) {
        $data = $this->model->select_detail($iddetail);
        if (empty($data)) {
          $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
        } else {
          $data['encrypt_service'] = encrypt($data['serviceid']);
          $data['encrypt_id'] = encrypt($data['id']);
          $data['encrypt_contract'] = encrypt($data['contractid']);
          $answer = array('status' => 'success', 'data' => $data);
        }
      } else {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      }
      echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function action_service()
  {
    if ($_POST) {
      if (empty($_POST['price']) || empty($_POST['idcontract']) || empty($_POST['listService'])) {
        $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios.');
      } else {
        $id = decrypt($_POST['idservice']);
        $id = intval($id);
        $idservice = decrypt($_POST['listService']);
        $idservice = intval($idservice);
        $idcontract = decrypt($_POST['idcontract']);
        $idcontract = intval($idcontract);
        $price = strClean($_POST['price']);
        $datetime = date("Y-m-d H:i:s");
        if ($id == 0) {
          $option = 1;
          if ($_SESSION['permits_module']['r']) {
            $request = $this->model->create_detail($idcontract, $idservice, $price, $datetime);
          }
        } else {
          $option = 2;
          //
          $plan = sqlObject("SELECT s.* FROM `services` s WHERE s.id = $idservice");
          $contract = sqlObject("SELECT s.* FROM `contracts` s WHERE s.id = $idcontract");

          if (!is_null($plan->id)) {

            // Formatear el ancho de banda con "M" para Mbps
            $maxlimit = $plan->rise . "M/" . $plan->descent . "M";

            $result = sql("SELECT cl.* FROM `clients` cl WHERE cl.id = $contract->clientid;");

            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
              $r = sqlObject("SELECT * FROM network_routers WHERE id = " . $row['net_router']);
              if (isset($r->id)) {
                $router = new Router($r->ip, $r->port, $r->username, decrypt_aes($r->password, SECRET_IV));

                if ($router->connected != true) {
                  $response = array('status' => 'error', 'msg' => 'No se pudo conectar al mikrotik');
                  echo json_encode($response, JSON_UNESCAPED_UNICODE);
                  return;
                }

                $sqr = $router->APIGetQueuesSimple($row['net_ip']);
                if ($sqr->success && count($sqr->data) > 0) {
                  $sq = $sqr->data[0];
                  $router->APIModifyQueuesSimple($sq->{".id"}, $row['net_name'], $row['net_ip'], $maxlimit);
                }
              }
            }
          }

          if ($_SESSION['permits_module']['a']) {
            $request = $this->model->modify_detail($id, $idservice, $price);
          }
        }
        if ($request == "success") {
          if ($option == 1) {
            $response = array('status' => 'success', 'msg' => 'Se ha registrado exitosamente.');
          } else {
            $response = array('status' => 'success', 'msg' => 'Se ha actualizado el registro exitosamente.');
          }
        } else if ($request == "exists") {
          $response = array('status' => 'exists', 'msg' => 'El servicio ya esta agregado a este cliente, agregue otro.');
        } else {
          $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operaciòn, intentelo nuevamente.');
        }
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function remove_detail()
  {
    if ($_POST) {
      if ($_SESSION['permits_module']['e']) {
        $idservices = decrypt($_POST['idservices']);
        $idservices = intval($idservices);
        $request = $this->model->remove_detail($idservices);
        if ($request == 'success') {
          $response = array('status' => 'success', 'msg' => 'El registro se ha eliminado.');
        } else {
          $response = array('status' => 'error', 'msg' => 'Error no se pudo eliminar.');
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
      }
    }
    die();
  }
  /* MODULO GALERIA */
  public function register_image()
  {
    if ($_POST) {
      if (empty($_POST['idclient'])) {
        $response = array('status' => 'error', 'msg' => 'Error de datos.');
      } else {
        /* VARIABLES PARA EL REGISTRO A LA BD*/
        $idclient = decrypt($_POST['idclient']);
        $idclient = intval($idclient);
        $iduser = intval($_SESSION['idUser']);
        $type_image = 3; //libre
        $idlibre = 0;
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
        $request = $this->model->register_image($idclient, $iduser, $type_image, $idlibre, $datetime, $image);
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
  public function open_gallery(string $idclient)
  {
    if ($_SESSION['permits_module']['v']) {
      $idclient = decrypt($idclient);
      $idclient = intval($idclient);
      if ($idclient > 0) {
        $data = $this->model->open_gallery($idclient);
        if (empty($data)) {
          $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
        } else {
          for ($i = 0; $i < count($data); $i++) {
            $data[$i]['url_image'] = base_style() . '/uploads/gallery/' . $data[$i]['image'];
          }
          $answer = array('status' => 'success', 'data' => $data);
        }
        echo json_encode($answer, JSON_UNESCAPED_UNICODE);
      } else {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      }
    }
    die();
  }
  public function remove_image()
  {
    if ($_POST) {
      if (empty($_POST['idclient']) || empty($_POST['file'])) {
        $response = array("status" => 'error', "msg" => 'Datos incorrectos.');
      } else {
        $idclient = decrypt($_POST['idclient']);
        $idclient = intval($idclient);
        $image = strClean($_POST['file']);
        $request = $this->model->remove_image($idclient, $image);
        if ($request) {
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

  private function settingInfo($business, $item)
  {
    $billIds = explode(",", $item['billIds']);
    $businessCode = $business['country_code'];
    $client = (object) $item;

    $messageWsp = new PlantillaWspInfoService($client, (object) $business);
    $messageWsp->setArrayBillId($billIds);
    $message = $messageWsp->execute("PAYMENT_PENDING");

    return [
      "phone" => "{$businessCode}{$item['mobile']}",
      "message" => $message,
      "item" => $item
    ];
  }

  public function list_info_message()
  {
    $data = $this->model->list_info_clients($_GET);
    $business = $_SESSION['businessData'];
    foreach ($data as $i => $item) {
      $item = $this->settingInfo($business, $item);
      $data[$i] = [
        "phone" => $item['phone'],
        "message" => $item['message']
      ];
    }
    $this->json($data);
    return $data;
  }

  public function select_info_message(string $id)
  {
    try {
      $clientId = decrypt($id);
      $item = $this->model->select_info_client($clientId);
      if (!$item) {
        throw new Exception("EL CLIENTE NO TIENE DEUDA PARA ENVIAR.");
      } else {
        $business = $_SESSION['businessData'];
        $item = $this->settingInfo($business, $item);
        $item["success"] = true;
        return $this->json($item);
      }
    } catch (\Throwable $th) {
      $this->json([
        "success" => false,
        "message" => $th->getMessage()
      ]);
    }
  }

  public function send_massive_whatsapp()
  {
    $bill = new BillSendMessageWhatsapp();
    $data = $bill->send([
      "phone:required" => true,
      "deuda" => $_GET['deuda']
    ]);
    $this->json($data);
  }

  /* MODULO NETWORK */

  public $addModifyNetAction = false;

  public function modify_network()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $res = (object) array();

    if (!empty($_POST['clientid']) && !empty($_POST['net_router']) && !empty($_POST['net_name']) && !empty($_POST['net_ip'])) {

      $network = sqlObject("SELECT r.*, z.mode mode FROM network_routers r JOIN network_zones z ON z.id = r.zoneid WHERE r.id = " . $_POST['net_router']);

      if (isset($network->id)) {

        $clientid = $this->addModifyNetAction ? $_POST['clientid'] : decrypt($_POST['clientid']);
        $clientd = sqlObject("SELECT * FROM clients WHERE id = $clientid");

        $rd = sqlObject("SELECT r.*, z.mode mode FROM network_routers r JOIN network_zones z ON z.id = r.zoneid WHERE r.id = " . $clientd->net_router);

        // WISP MANAGEMENT
        $plan = sqlObject("SELECT s.* FROM `services` s 
            JOIN contracts c ON c.clientid = $clientid 
            JOIN detail_contracts cd ON cd.serviceid = s.id 
            AND cd.contractid = c.id");

        $profile = isset($plan->profile) ? $plan->profile : null;

        $maxlimit = $plan->rise . "M/" . $plan->descent . "M";
        $router = new Router($network->ip, $network->port, $network->username, decrypt_aes($network->password, SECRET_IV));
        $router_old = new Router($rd->ip, $rd->port, $rd->username, decrypt_aes($rd->password, SECRET_IV));

        if ($rd->id != $network->id && $rd->id != 0 && $router_old->connected) {
          $router_old->APIDeleteQueuesSimple($clientd->net_ip);
          $router_old->APIDeletePPPSecret($clientd->net_ip);
          $router_old->APIRemoveFirewallAddress($clientd->net_ip, "moroso");
        }

        if ($router->connected) {
          sqlUpdate("clients", "net_router", $_POST['net_router'], $clientid);
          sqlUpdate("clients", "net_name", $_POST['net_name'], $clientid);
          sqlUpdate("clients", "net_password", encrypt_aes($_POST['net_password'], SECRET_IV), $clientid);
          sqlUpdate("clients", "net_localaddress", $_POST['net_localaddress'], $clientid);
          sqlUpdate("clients", "net_ip", $_POST['net_ip'], $clientid);
          sqlUpdate("clients", "nap_cliente_id", $_POST['nap_cliente_id'], $clientid);
          sqlUpdate("clients", "ap_cliente_id", $_POST['ap_cliente_id'], $clientid);

          $sqr = $router->APIGetQueuesSimple(isset($clientd->id) ? $clientd->net_ip : $_POST['net_ip']);

          if (empty($profile) && $sqr->success && count($sqr->data) > 0) {
            $sq = $sqr->data[0];
            $router->APIModifyQueuesSimple($sq->{".id"}, $_POST['net_name'], $_POST['net_ip'], $maxlimit);
          } elseif (empty($profile)) {
            $res = $router->APIAddQueuesSimple($_POST['net_name'], $_POST['net_ip'], $maxlimit);
          }

          $flr = $router->APIGetFirewallAddress(isset($clientd->id) ? $clientd->net_ip : $_POST['net_ip'], "moroso");
          if ($flr->success && count($flr->data) > 0) {
            $dres = $router->APIRemoveFirewallAddress($clientd->net_ip, "moroso");
            if ($dres->success) {
              $router->APIAddFirewallAddress($_POST['net_ip'], "moroso", "");
            }
          }

          if ($network->mode == 2) {
            $psr = $router->APIGetPPPSecret($_POST['net_ip']);

            if (!$psr->success || count($psr->data) == 0) {
              $psr = $router->APIGetPPPSecretByName($_POST['net_name']);
            }

            if ($psr->success && count($psr->data) > 0) {
              $ps = $psr->data[0];
              $router->APIModifyPPPSecret(
                $ps->{".id"},
                $_POST['net_name'],
                $_POST['net_ip'],
                $_POST['net_password'],
                $_POST['net_localaddress'],
                $profile
              );
            } else {
              // Si no se encontró, crearlo
              $router->APIAddPPPSecret(
                $_POST['net_name'],
                $_POST['net_ip'],
                $_POST['net_password'],
                $_POST['net_localaddress'],
                $profile
              );
            }
          }

          //

          $res->result = "success";
        } else {
          $res->result = "failed";
          $res->message = "Could not connect with router.";
        }
      } else {
        $res->result = "failed";
        $res->message = "Invalid request";
      }
    } else {
      $res->result = "failed";
      $res->message = "Invalid request";
    }

    if (!$this->addModifyNetAction) {
      echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
  }

  public function resumen()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['simple'] = true;
    $data['filters'] = true;
    $data['hideColumns'] = [];
    $data['page_name'] = "Clientes";
    $data['page_title'] = "Gestión de Clientes";
    $data['home_page'] = "Dashboard";
    $data['actual_page'] = "Clientes";
    $data['page_functions_js'] = "clients.js";
    $data['orderPayday'] = "ASC";
    $this->views->getView($this, "customers", $data);
  }

  public function unblock_network()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }

    $clientId = decrypt($_POST['clientid']);
    $router = new ClientRouterService();
    $response = $router->unlockNetwork($clientId);
    $this->json($response);
  }


  public function block_network()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $clientId = decrypt($_POST['clientid']);
    $router = new ClientRouterService();
    $response = $router->blockNetwork($clientId);
    $this->json($response);
  }

  public function blocked_network(string $id)
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $res = (object) array();
    $clientd = sqlObject("SELECT * FROM clients WHERE id = $id");

    if (!is_null($clientd->id)) {

      if (empty($clientd->net_router)) {
        return $this->json([
          "status" => "success"
        ]);
      }

      $r = sqlObject("SELECT r.*, z.mode mode FROM network_routers r JOIN network_zones z ON z.id = r.zoneid WHERE r.id = " . $clientd->net_router);
      if (!is_null($r->id)) {
        $router = new Router($r->ip, $r->port, $r->username, decrypt_aes($r->password, SECRET_IV));

        if (!$router->connected) {
          return $this->json([
            "status" => "disconnected"
          ]);
        }

        $flr = $router->APIGetFirewallAddress($clientd->net_ip, "moroso");
        $blocked = ($flr->success && count($flr->data) > 0);

        return $this->json([
          "status" => $blocked ? "blocked" : "success"
        ]);
      } else {
        return $this->json([
          "status" => "error"
        ]);
      }
    } else {
      return $this->json([
        "status" => "error"
      ]);
    }
  }

  public function delete(string $id)
  {
    try {
      $clientId = decrypt($id);
      $business = (object) $_SESSION['businessData'];
      $service = new ClientDeleteService($business);
      $service->execute($clientId);
      return $this->json([
        "success" => true,
        "message" => "cliente eliminado!!!"
      ]);
    } catch (\Throwable $th) {
      $this->json([
        "success" => false,
        "message" => $th->getMessage()
      ], 501);
    }
  }
}
