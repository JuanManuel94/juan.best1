<?php

class Dashboard extends Controllers
{
  public function __construct()
  {
    parent::__construct();
    session_start();
    if (empty($_SESSION['login'])) {
      header('Location: ' . base_url() . '/login');
      die();
    }
    consent_permission(DASHBOARD);
  }
  public function dashboard()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      $data['page_name'] = "Página en blanco";
      $data['page_title'] = "Página en blanco";
      $data['home_page'] = "Dashboard";
      $data['actual_page'] = "Página en blanco";
      $this->views->getView($this, "blank", $data);
    } else {
      $data['page_name'] = "Dashboard";
      $data['page_title'] = "Panel de Control";
      $data['home_page'] = "Dashboard";
      $data['actual_page'] = "Inicio";
      if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) {
        $data['clients'] = $this->model->number_customers();
        $data['canceled_clients'] = $this->model->canceled_customers();
        $data['suspended_clients'] = $this->model->suspended_customers();
        $data['gratis_clients'] = $this->model->gratis_customers();
        $data['internet'] = $this->model->number_internet();
        $data['plans'] = $this->model->number_plans();
        $data['products'] = $this->model->number_products();
        $data['stock_products'] = $this->model->stock_products();
        $data['users'] = $this->model->number_users();
        $data['inactive_users'] = $this->model->inactive_users();
        $current = date('Y-m-d');
        $data['payments_day'] = $this->model->total_transactions_day($current);
        $data['payments_month'] = $this->model->total_transactions_month();
        $data['unpaid_bills'] = $this->model->unpaid_bills();
        $data['expired_bills'] = $this->model->expired_bills();
        $data['installations'] = $this->model->number_installations();
        $data['pending_installations'] = $this->model->pending_installations();
        $data['tickets'] = $this->model->number_tickets();
        $data['pending_tickets'] = $this->model->pending_tickets();
        $data['products_sellout'] = $this->model->products_sellout();
        $year = date('Y');
        $month = date('m');
        $data['monthly_payments'] = $this->model->transactions_month($month, $year);
        $data['payments_type'] = $this->model->payments_type($month, $year);
        $data['top_products'] = $this->model->top_products();
        $data['last_payments'] = $this->model->last_payments();
        $data['page_functions_js'] = "dashboard.js";
        $this->views->getView($this, "dashboard", $data);
      } else {
        $data['pending_installations'] = $this->model->pending_installations();
        $data['pending_tickets'] = $this->model->pending_tickets();
        $data['page_functions_js'] = "predetermined.js";
        $this->views->getView($this, "predetermined", $data);
      }
    }
  }
  public function count_widget()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      die();
    } else {

      $res = (object) array();

      $res->result = "success";
      $res->data = (object) array();
      $res->data->routers_connected = 0;
      $res->data->routers_disconnected = 0;
      $res->data->customers_active = 0;
      $res->data->customers_suspended = 0;
      $res->data->services_active = 0;
      $res->data->monitors_active = 0;
      $res->data->monitors_down = 0;

      //GET ROUTERS

      $result = sql("SELECT r.* FROM network_routers r");

      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

        $router = new Router($row["ip"], $row["port"], $row["username"], decrypt_aes($row["password"], SECRET_IV));

        if ($router->connected) {
          $res->data->routers_connected++;
        } else {
          $res->data->routers_disconnected++;
        }
      }

      // CUSTOMERS

      $res->data->customers_active = sqlObject("SELECT COUNT(cl.id) count FROM clients cl JOIN contracts c ON c.clientid = cl.id WHERE c.state = 2")->count;
      $res->data->customers_suspended = sqlObject("SELECT COUNT(cl.id) count FROM clients cl JOIN contracts c ON c.clientid = cl.id WHERE c.state = 3")->count;
      $res->data->services_active = sqlObject("SELECT COUNT(*) count FROM services WHERE state = 1")->count;

      echo json_encode($res);
    }
  }
  public function customers_connected_widget()
{
  if (empty($_SESSION['permits_module']['v'])) {
    die();
  } else {

    $res = (object) array();
    $res->result = "success";
    $res->html = "";

    // Obtener el límite de clientes (si no se pasa, por defecto es 8)
    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 8;

    //GET ROUTERS
    $result = sql("SELECT r.* FROM network_routers r JOIN network_zones z ON z.id = r.zoneid WHERE z.mode = 2");

    $connections = [];  // Array para almacenar las conexiones

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
      $router = new Router($row["ip"], $row["port"], $row["username"], decrypt_aes($row["password"], SECRET_IV));

      if ($router->connected) {
        $cons = $router->APIGetPPPActive()->data;
        foreach($cons as $con){
          // Almacenar la conexión en el array
          $customer = sqlObject("SELECT * FROM clients WHERE net_ip = '$con->address'");
          $connections[] = [
            'customer' => $customer,
            'uptime' => $con->uptime
          ];
        }
      }
    }

    // Ordenar las conexiones por uptime (menor a mayor)
    usort($connections, function($a, $b) {
      return $a['uptime'] <=> $b['uptime'];
    });

    // Limitar a los primeros 8 para la visualización
    $limitedConnections = array_slice($connections, 0, $limit);

    // Generar el HTML para las conexiones limitadas
    foreach ($limitedConnections as $connection) {
      $customer = $connection['customer'];
      $res->html .= '<li><a href="'.base_url().'/customers/view_client/'.encrypt($customer->id).'" class="text-center"><img src="https://ui-avatars.com/api/?name='.$customer->names.'+'.$customer->surnames.'&background=random"></a><h4 class="username text-ellipsis text-center">'.$customer->names.' '.$customer->surnames.'<small class="text-center">'.$connection['uptime'].'</small></h4></li>';
    }

    // Agregamos el total de conectados (sin límite)
    $res->total = count($connections);

    // CUSTOMERS
    echo json_encode($res);
  }
}
  public function transactions_month(string $params)
  {
    if ($_SESSION['permits_module']['v']) {
      if (!empty($params)) {
        $arrParams = explode("-", $params);
        $month = intval($arrParams[0]);
        $year = intval($arrParams[1]);
      } else {
        $month = date('m');
        $year = date('Y');
      }
      $data = $this->model->transactions_month($month, $year);
      if (empty($data)) {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      } else {
        $answer = array('status' => 'success', 'data' => $data);
      }
      echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function payments_type(string $params)
  {
    if ($_SESSION['permits_module']['v']) {
      if (!empty($params)) {
        $arrParams = explode("-", $params);
        $month = intval($arrParams[0]);
        $year = intval($arrParams[1]);
      } else {
        $month = date('m');
        $year = date('Y');
      }
      $data = $this->model->payments_type($month, $year);
      if (empty($data)) {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      } else {
        $answer = array('status' => 'success', 'data' => $data);
      }
      echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function libre_services(string $year)
  {
    if ($_SESSION['permits_module']['v']) {
      $year = !empty($year) ? intval($year) : date('Y');
      $data = $this->model->libre_services($year);
      if (empty($data)) {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      } else {
        $answer = array('status' => 'success', 'data' => $data);
      }
      echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function list_paymentes()
  {
    $user = intval($_SESSION['idUser']);
    $data = $this->model->list_paymentes($user);
    for ($i = 0; $i < count($data); $i++) {
      $data[$i]['count_total'] = $data[$i]['amount_paid'];
      /* COMPROBANTE */
      $correlative = str_pad($data[$i]['correlative'], 7, "0", STR_PAD_LEFT);
      $data[$i]['invoice'] = $correlative;
      $data[$i]['amount_paid'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['amount_paid']);
      $data[$i]['bill_total'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['bill_total']);
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    die();
  }
}
