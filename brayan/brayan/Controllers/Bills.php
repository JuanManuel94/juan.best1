<?php
require 'Libraries/dompdf/vendor/autoload.php';
require 'Libraries/spreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Dompdf\Dompdf;

class Bills extends Controllers
{
  public function __construct()
  {
    parent::__construct();
    session_start();
    if (empty($_SESSION['login'])) {
      header('Location: ' . base_url() . '/login');
      die();
    }
    consent_permission(BILLS);
  }
  public function bills()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['page_name'] = "Facturas";
    $data['page_title'] = "Gestión de Facturas";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Finanzas";
    $data['actual_page'] = "Facturas";
    $data['page_functions_js'] = "bills.js?v3";
    $this->views->getView($this, "bills", $data);
  }
  public function pendings()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['page_name'] = "Facturas pendientes";
    $data['page_title'] = "Facturas Pendientes";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Finanzas";
    $data['actual_page'] = "Facturas";
    $data['page_functions_js'] = "bills_pendings.js";
    $this->views->getView($this, "pendings", $data);
  }
  public function list_records()
  {
    if ($_SESSION['permits_module']['v']) {
      if (empty($_GET['start']) && empty($_GET['end'])) {
        $start = date("Y-m-01");
        $end = date("Y-m-t");
      } else {
        $dateStart = DateTime::createFromFormat('d/m/Y', $_GET['start']);
        $start = $dateStart->format('Y-m-d');
        $dateEnd = DateTime::createFromFormat('d/m/Y', $_GET['end']);
        $end = $dateEnd->format('Y-m-d');
      }
      $state = intVal($_GET['state']);
      $data = $this->model->list_records($start, $end, $state);
      for ($i = 0; $i < count($data); $i++) {
        /* VARIABLES */
        $type = 'ticket';
        /* ID FACTURA ENCRYTADO */
        $data[$i]['encrypt'] = encrypt($data[$i]['id']);
        /* CLIENTE TIENE CONTRATO */
        $contract = $this->model->select_contract($data[$i]['clientid']);
        if (empty($contract)) {
          $data[$i]['encrypt_contract'] = "";
        } else {
          $data[$i]['encrypt_contract'] = encrypt($contract['id']);
        }
        /* CODIGO DE FACTURAS */
        $payments = $this->model->invoice_paid($data[$i]['id']);
        /* FACTURA DE SERVIOS O PRODUCTOS / OBTENER MES DE LA FACTURA */
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
        $invoice = '#' . $correlative;
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
          if ($data[$i]['promise_enabled'] == "1") {
            sqlUpdate("bills", "promise_enabled", 0, $data[$i]['id']);
          }
        } else if ($data[$i]['state'] == 2) {
          /* VALIDAR SI LA FACTURA ESTA VENCIDA */
          $state = "PENDIENTE";
          $data[$i]['count_state'] = "PENDIENTE";
        } else if ($data[$i]['state'] == 3) {
          $state = "VENCIDO";
          $data[$i]['count_state'] = "VENCIDO";
          //$data[$i]['state'] = 5;
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
            $edit = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
            $edit_2 = '<a href="javascript:;" class="dropdown-item" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
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
              $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Anular" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\',\'' . $invoice . '\')"><i class="fa fa-ban"></i></a>';
              $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\',\'' . $invoice . '\')"><i class="fa fa-ban mr-1"></i>Anular</a>';
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
  public function list_pendings()
  {
    if ($_SESSION['permits_module']['v']) {
      $data = $this->model->list_pendings();
      for ($i = 0; $i < count($data); $i++) {
        /* ID FACTURA ENCRYTADO */
        $data[$i]['encrypt'] = encrypt($data[$i]['id']);
        /* CLIENTE TIENE CONTRATO */
        $contract = $this->model->select_contract($data[$i]['clientid']);
        if (empty($contract)) {
          $data[$i]['encrypt_contract'] = "";
        } else {
          $data[$i]['encrypt_contract'] = encrypt($contract['id']);
        }
        /* CODIGO DE FACTURAS */
        $payments = $this->model->invoice_paid($data[$i]['id']);
        /* FACTURA DE SERVIOS O PRODUCTOS / OBTENER MES DE LA FACTURA */
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
        $invoice = '#' . $correlative;
        $data[$i]['invoice'] = $correlative;
        $data[$i]['billing'] = $month_letter;
        /* TOTALES PRO FACTURA */
        $data[$i]['count_total'] = format_money($data[$i]['total']);
        $data[$i]['count_subtotal'] = empty($payments['remaining_amount']) ? format_money(0) : format_money($payments['remaining_amount']);
        /* TOTAL PAGADO */
        $data[$i]['total'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['total']);
        $data[$i]['balance'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['remaining_amount']);
        $data[$i]['subtotal'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['subtotal']);
        $data[$i]['discount'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['discount']);
        /* ESTADO DE FACTURAS */
        if ($data[$i]['state'] == 2) {
          /* VALIDAR SI LA FACTURA ESTA VENCIDA */
          $currentDate = date("Y-m-d");
          $expirationDate = $data[$i]['expiration_date'];
          if ($currentDate !== $expirationDate) {
            $current_date = new DateTime("now");
            $expiration = new DateTime($data[$i]['expiration_date']);
            $diff = $expiration->diff($current_date);
            $days = $diff->invert;
            if ($days <= 0) {
              if ($data[$i]['promise_enabled'] == "1" && (new DateTime($data[$i]['promise_date']))->diff($current_date)->invert > 0) {
              } else {
                sqlUpdate("bills", "promise_enabled", "0", $data[$i]['id']);

                $this->model->modify_state($data[$i]['id'], 3);

              }
              //
            }
          }
          $state = "PENDIENTE";
        } else if ($data[$i]['state'] == 3) {
          $state = "VENCIDO";
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
            $edit = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
            $edit_2 = '<a href="javascript:;" class="dropdown-item" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
          }
        } else {
          $edit = '';
          $edit_2 = '';
        }
        if ($_SESSION['permits_module']['r']) {
          if ($data[$i]['state'] == 2 || $data[$i]['state'] == 3) {
            $payment = '<a href="javascript:;" class="green-light" data-toggle="tooltip" data-original-title="Agregar pago" onclick="make_payment(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-dollar-sign"></i></a><a href="javascript:;" class="orange ' . ($data[$i]['promise_enabled'] == 1 ? 'promise-on' : '') . '" data-toggle="tooltip" data-original-title="Agregar promesa de pago" onclick="make_promise(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-calendar"></i></a>';
            $payment_2 = '<a href="javascript:;" class="dropdown-item" onclick="make_payment(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-dollar-sign mr-1"></i>Agregar pago</a><a href="javascript:;" class="dropdown-item ' . ($data[$i]['promise_enabled'] == 1 ? 'promise-on' : '') . '" onclick="make_promise(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-calendar"></i> Promesa de pago</a>';
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
              $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Anular" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\',\'' . $invoice . '\')"><i class="fa fa-ban"></i></a>';
              $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\',\'' . $invoice . '\')"><i class="fa fa-ban mr-1"></i>Anular</a>';
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
  public function promises()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    if ($_SESSION['permits_module']['v']) {
      $datav['page_name'] = "Promesas";
      $datav['page_title'] = "Promesas de pago";
      $datav['home_page'] = "Dashboard";
      $datav['actual_page'] = "Promesas";
      $datav['page_functions_js'] = "promises.js";

      $datav['records'] = array();

      //GET PROMISES

      if (empty($_GET['start']) && empty($_GET['end'])) {
        $start = date("Y-m-01");
        $end = date("Y-m-t");
      } else {
        $dateStart = DateTime::createFromFormat('d/m/Y', $_GET['start']);
        $start = $dateStart->format('Y-m-d');
        $dateEnd = DateTime::createFromFormat('d/m/Y', $_GET['end']);
        $end = $dateEnd->format('Y-m-d');
      }
      $state = isset($_GET['state']) ? intval($_GET['state']) : 0;
      $data = $this->model->list_records($start, $end, $state);
      for ($i = 0; $i < count($data); $i++) {
        if ($data[$i]['promise_enabled'] == 1) {
          /* VARIABLES */
          $type = 'ticket';
          /* ID FACTURA ENCRYTADO */
          $data[$i]['encrypt'] = encrypt($data[$i]['id']);
          /* CLIENTE TIENE CONTRATO */
          $contract = $this->model->select_contract($data[$i]['clientid']);
          if (empty($contract)) {
            $data[$i]['encrypt_contract'] = "";
          } else {
            $data[$i]['encrypt_contract'] = encrypt($contract['id']);
          }
          /* CODIGO DE FACTURAS */
          $payments = $this->model->invoice_paid($data[$i]['id']);
          /* FACTURA DE SERVIOS O PRODUCTOS / OBTENER MES DE LA FACTURA */
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
          $invoice = '#' . $correlative;
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
                if ($data[$i]['promise_enabled'] == "1" && (new DateTime($data[$i]['promise_date']))->diff($current_date)->invert > 0) {
                } else {
                  sqlUpdate("bills", "promise_enabled", "0", $data[$i]['id']);

                  $this->model->modify_state($data[$i]['id'], 3);

                  // SEND BLOCK REQUEST TO ROUTER
                  $clientid = $data[$i]["clientid"];
                  $clientd = sqlObject("SELECT * FROM clients WHERE id = $clientid");
                  $r = sqlObject("SELECT r.*, z.mode mode FROM network_routers r JOIN network_zones z ON z.id = r.zoneid WHERE r.id = " . $clientd->net_router);

                  if (!is_null($r->id)) {

                    $router = new Router($r->ip, $r->port, $r->username, decrypt_aes($r->password, SECRET_IV));
                    $flr = $router->APIGetFirewallAddress($clientd->net_ip, "moroso");
                    if ($flr->success && count($flr->data) == 0) {
                      $ares = $router->APIAddFirewallAddress($clientd->net_ip, "moroso", "");
                    }
                  }
                }
                //
              }
            }
            $state = "PENDIENTE";
            $data[$i]['count_state'] = "PENDIENTE";
          } else if ($data[$i]['state'] == 3) {
            $state = "VENCIDO";
            $data[$i]['count_state'] = "VENCIDO";
            //$data[$i]['state'] = 5;
          } else if ($data[$i]['state'] == 4) {
            $state = 'ANULADO';
            $data[$i]['count_state'] = "ANULADO";
          }
        } else {
          unset($data[$i]);
        }
      }
      $datav['records'] = $data;


      ////

      $this->views->getView($this, "promises", $datav);
    }
    die();
  }
  public function select_record(string $idbill)
  {
    if ($_SESSION['permits_module']['v']) {
      $idbill = decrypt($idbill);
      $idbill = intval($idbill);
      if ($idbill > 0) {
        $data = $this->model->select_record($idbill);
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
  public function view_bill(string $idbill)
  {
    if ($_SESSION['permits_module']['v']) {
      $idbill = decrypt($idbill);
      $idbill = intval($idbill);
      if ($idbill > 0) {
        $data = $this->model->view_bill($idbill);
        if (empty($data)) {
          $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
        } else {
          $client = $this->model->find_client($data['bill']['clientid']);

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
        header("Location:" . base_url() . "/bills");
      } else {
        $arrParams = explode(",", $params);
        $idbill = decrypt($arrParams[0]);
        $idbill = intval($idbill);
        $type = empty($_SESSION['businessData']['print_format']) ? 'ticket' : $_SESSION['businessData']['print_format'];
        if (is_numeric($idbill)) {
          $data = $this->model->view_bill($idbill);
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
        header("Location:" . base_url() . "/bills");
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
          $data = $this->model->view_bill($idbill);
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
              $dateMonth = DateTime::createFromFormat('d/m/Y', $_POST['billed_month']);
              $billed_month = $dateMonth->format('Y-m-d');
            }
            $total = $this->model->returnCode();
            if ($total == 0) {
              $code = "V00001";
            } else {
              $max = $this->model->generateCode();
              $code = "V" . substr((substr($max, 1) + 100001), 1);
            }
            $num_corre = $this->model->returnCorrelative($voucher, $serie);
            if (empty($num_corre)) {
              $correlative = 1;
            } else {
              $correlative = $this->model->returnUsed($voucher, $serie);
            }
            $request = $this->model->create($user, $client, $voucher, $serie, $code, $correlative, $issue, $expiration, $billed_month, $subtotal, $discount, $total_bill, $type, $method, $observation);
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
                    $this->model->create_datail($idbill, $typepro[$i], $idserpro[$i], strtoupper($description[$i]), $quanty[$i], $price[$i], $totald[$i]);
                    if ($idserpro[$i] != 0) {
                      $this->model->create_departures($idbill, $idserpro[$i], $datetime, $description_departures, $quanty[$i], $price[$i], $totald[$i]);
                      $this->model->subtract_stock($idserpro[$i], $quanty[$i]);
                    }
                  }
                }
              }
            } else {
              if ($request == "success") {
                $idbill = $this->model->returnBill();
                $this->model->modify_available($voucher, $serie);
                $idserpro = empty($_POST['idproducto']) ? 0 : $_POST['idproducto'];
                $typepro = $_POST['tipo'];
                $description = $_POST['descripcion'];
                $quanty = $_POST['unidad'];
                $price = $_POST['costo'];
                $totald = $_POST['totales'];
                if ($idserpro > 1) {
                  for ($i = 0; $i < count($idserpro); $i++) {
                    $this->model->create_datail($idbill, $typepro[$i], $idserpro[$i], strtoupper($description[$i]), $quanty[$i], $price[$i], $totald[$i]);
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
                $request = $this->model->modify($id, $issue, $expiration, $billed_month, $subtotal, $discount, $total_bill, $observation, $state);
                if ($request == "success") {
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
                            $last_payment = $this->model->last_payment($id);
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
                            $last_payment = $this->model->last_payment($id);
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
                      $this->model->create_datail($id, $typepro[$i], $idserpro[$i], strtoupper($description[$i]), $quanty[$i], $price[$i], $totald[$i]);
                    }
                  }
                }
              }
            }
          }
        }
        if ($request == "success") {
          if ($option == 1) {
            $consult_bill = $this->model->select_record($idbill);
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
  public function create_payment()
  {
    if ($_POST) {
      if (empty($_POST['idclient']) || empty($_POST['idbill']) || empty($_POST['total_payment'])) {
        $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios');
      } else {

        $ticket_number = $_POST['ticket_number'];
        $reference_number = $_POST['reference_number'];

        $exists = $this->model->checkTicketNumber($ticket_number);

        if ($exists) {
          $response = array("status" => 'error', "msg" => 'El número de boleta ya se encuentra registrado.');
        } else {

          $billId = decrypt($_POST['idbill']);
          $userId = intval(strClean($_SESSION['idUser']));
          $clientId = decrypt($_POST['idclient']);

          $client = $this->model->find_client($clientId);

          if ($_SESSION['userData']['profileid'] == 1) {
            $date = DateTime::createFromFormat('d/m/Y H:i', $_POST['date_time']);
            $datetime = $date->format('Y-m-d H:i:s');
          } else {
            $datetime = date('Y-m-d H:i:s');
          }

          $typepay = strClean($_POST['listTypePay']);
          $comment = strtoupper(strClean($_POST['comment']));
          $total_payment = (float) strClean($_POST['total_payment']);

          if ($_SESSION['permits_module']['r']) {
            try {
              $this->model->createQueryRunner();
              $business = (Object) $_SESSION['businessData'];
              $paymentBill = new PaymentBillService($business, $client, $userId, $typepay);
              $paymentBill->setMysql($this->model);
              $paymentBill->setDatetime($datetime);
              $paymentBill->setComment($comment);
              $result = $paymentBill->execute($billId, $total_payment);
              $this->model->commit();
              // settings
              $bill = $result['bill'];
              $paymentId = $result['paymentId'];

              $messageWsp = new PlantillaWspInfoService($client, $business);
              $messageWsp->setArrayPaymentId([$paymentId]);

              // response
              return $this->json([
                "status" => "success",
                'msg' => strtolower($bill->voucher) . ' Nº ' . str_pad($bill->correlative, 7, "0", STR_PAD_LEFT) . ' ha sido aceptada.',
                'modal' => true,
                'idbill' => encrypt($bill->id),
                'voucher' => $bill->voucher,
                'serie' => $bill->serie . "-" . str_pad($bill->correlative, 7, "0", STR_PAD_LEFT),
                'correlative' => str_pad($bill->correlative, 7, "0", STR_PAD_LEFT),
                'billed_month' => $bill->billed_month,
                'type' => $bill->type,
                'remaining_amount' => $bill->remaining_amount,
                'amount_paid' => $bill->amount_paid,
                'business_name' => $_SESSION['businessData']['business_name'],
                'symbol' => $_SESSION['businessData']['symbol'],
                'client' => $bill->client_name,
                'mobile' => $bill->client_mobile,
                'country' => $_SESSION['businessData']['country_code'],
                "message_wsp" => $messageWsp->execute("PAYMENT_CONFIRMED")
              ]);
            } catch (\Throwable $th) {
              $this->model->rollback();
              return $this->json([
                "status" => "error",
                "msg" => $th->getMessage()
              ]);
            }
          }
        }
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }
  public function cancel()
  {
    if ($_SESSION['permits_module']['e']) {
      if ($_POST) {
        $idbill = decrypt($_POST['idbill']);
        $idbill = intval($idbill);
        $consult = $this->model->select_record($idbill);
        $type = $consult['type'];
        $invoice = str_pad($consult['correlative'], 7, "0", STR_PAD_LEFT);
        $request = $this->model->cancel($idbill);
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
        $affair = strtoupper("Constancia de pago");
      }
      if ($state == "PENDIENTE") {
        $affair = strtoupper("Factura pendiente de pago");
      }
      if ($state == "VENCIDO") {
        $affair = strtoupper("Factura vencida con pendiente de pago");
      }
      $datetime = date("Y-m-d H:i:s");
      if ($idbill > 0) {
        $data = $this->model->view_bill($idbill);
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
  public function import()
  {
    /* Variable Post */
    $file = $_FILES["import_bills"]["tmp_name"];
    //$file = "Assets/facturas.xlsx";
    /* Cargamos el archivo */
    $document = IOFactory::load($file);
    /* Hoja Productos*/
    $bills_sheet = $document->getSheetByName("Facturas");
    $row_bills = $bills_sheet->getHighestDataRow();
    /* Ciclo registrar productos */
    $total_bill = 0;
    for ($i = 2; $i <= $row_bills; $i++) {
      $business = intval($_SESSION['businessData']['id']);
      $user = intval($_SESSION['idUser']);
      $voucher = 1;
      $serie = 1;
      $names = strtoupper(strClean($bills_sheet->getCell("A" . $i)));
      $surnames = strtoupper(strClean($bills_sheet->getCell("B" . $i)));
      $date_issue = DateTime::createFromFormat('d/m/Y', $bills_sheet->getCell("C" . $i));
      $issue = $date_issue->format('Y-m-d');
      $date_expiration = DateTime::createFromFormat('d/m/Y', $bills_sheet->getCell("D" . $i));
      $expiration = $date_expiration->format('Y-m-d');
      $subtotal = strClean($bills_sheet->getCell("E" . $i));
      $discount = strClean($bills_sheet->getCell("F" . $i));
      $total = strClean($bills_sheet->getCell("G" . $i));
      $type_bill = 2;
      $method = 2;
      $observation = strtoupper(strClean($bills_sheet->getCell("H" . $i)));
      $state = strClean($bills_sheet->getCell("I" . $i));
      $method_payment = strClean($bills_sheet->getCell("J" . $i));
      if ($method_payment == "EFECTIVO") {
        $idmethodpay = 1;
      } else {
        $idmethodpay = 1;
      }
      $payment_date = DateTime::createFromFormat('d/m/Y H:i:s', $bills_sheet->getCell("K" . $i));
      $paidout = $payment_date->format('Y-m-d H:i:s');
      $payment_amount = strClean($bills_sheet->getCell("L" . $i));
      $comment = strClean($bills_sheet->getCell("M" . $i));

      // Validar si el cliente existe
      $existing_client = $this->model->existing_client($names, $surnames);
      if (!empty($existing_client)) {
        $idclient = $existing_client['id'];
        // Obtener información del contrato
        $data_contract = $this->model->select_contract($idclient);
        $idcontract = $data_contract['id'];
        $payday = str_pad($data_contract['payday'], 2, "0", STR_PAD_LEFT);
        $days_grace = $data_contract['days_grace'];
        // Obtener fecha de vencimiento y corte
        $arrExpiration = explode("-", $expiration);
        $year_expiration = $arrExpiration[0];
        $month_expiration = $arrExpiration[1];
        $day_expiration = $arrExpiration[2];
        $new_expiration = $year_expiration . "-" . $month_expiration . "-" . $payday;
        $new_expiration = date("Y-m-d", strtotime($new_expiration));
        $current = date("Y-m-d", strtotime($new_expiration . " - 1 month"));
        $arrCurrent = explode("-", $current);
        $yearVal = $arrCurrent[0];
        $monthVal = $arrCurrent[1];
        $months = months();
        $month = $months[date('n', strtotime($current)) - 1];
        // obtener detalle de contrato - servicios
        $data_detail = $this->model->select_detail_contract($idcontract);
        // Registrar factura
        $row = $this->model->returnCode();
        if ($row == 0) {
          $code = "V00001";
        } else {
          $max = $this->model->generateCode();
          $code = "V" . substr((substr($max, 1) + 100001), 1);
        }
        $num_corre = $this->model->returnCorrelative($voucher, $serie);
        if (empty($num_corre)) {
          $correlative = 1;
        } else {
          $correlative = $this->model->returnUsed($voucher, $serie);
        }
        $request = $this->model->import($business, $user, $idclient, $voucher, $serie, $code, $correlative, $issue, $new_expiration, $current, $subtotal, $discount, $total, $type_bill, $method, $observation, $state, $yearVal, $monthVal);
        if ($request > 0) {
          $idbill = $this->model->returnBill();
          if ($state == 1) {
            $row_payment = $this->model->returnCodePayment();
            if ($row_payment == 0) {
              $code_payment = "T00001";
            } else {
              $max_payment = $this->model->generateCodePayment();
              $code_payment = "T" . substr((substr($max_payment, 1) + 100001), 1);
            }
            $request_payment = $this->model->create_payment($business, $idbill, $user, $idclient, $code_payment, $idmethodpay, "", $comment, $payment_amount, $payment_amount, 0, 1);
            if ($request_payment == "success") {
              $this->model->modify_amounts($idbill, $payment_amount, 0, 1);
            }
          }
          $this->model->modify_available($voucher, $serie);
          for ($d = 0; $d < count($data_detail); $d++) {
            $description_service = "SERVICIO DE " . $data_detail[$d]['service'] . ",MES DE " . strtoupper($month);
            $this->model->create_datail($idbill, $type_bill, $data_detail[$d]['serviceid'], $description_service, 1, $data_detail[$d]['price'], $data_detail[$d]['price']);
          }
        }
      }
      $total_bill = $total_bill + $request;
    }
    if ($total_bill >= 1) {
      $response = array('status' => 'success', 'msg' => 'La importación se realizo exitosamente.');
    } else if ($total_bill == 0) {
      $response = array('status' => 'warning', 'msg' => 'No se pudo importar, revise el excel en caso que realizaste mal rellenado.');
    } else {
      $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operación, intentelo nuevamente.');
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
    $spreadsheet->getActiveSheet()->getStyle('A1:O1')->applyFromArray($style_header);
    $center_cell = array(
      'alignment' => array(
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
      ),
    );
    $spreadsheet->getActiveSheet()->getStyle('A')->applyFromArray($center_cell);
    $spreadsheet->getActiveSheet()->getStyle('C:L')->applyFromArray($center_cell);
    $spreadsheet->getActiveSheet()->getStyle('O')->applyFromArray($center_cell);

    $active_sheet = $spreadsheet->getActiveSheet();
    $active_sheet->setTitle("Facturas");
    $active_sheet->getColumnDimension('A')->setAutoSize(true);
    $active_sheet->setCellValue('A1', 'COD');
    $active_sheet->getColumnDimension('B')->setAutoSize(true);
    $active_sheet->setCellValue('B1', 'CLIENTE');
    $active_sheet->getColumnDimension('C')->setAutoSize(true);
    $active_sheet->setCellValue('C1', 'COMPROBANTE');
    $active_sheet->getColumnDimension('D')->setWidth(20);
    $active_sheet->setCellValue('D1', 'MES FACTURADO');
    $active_sheet->getColumnDimension('E')->setWidth(20);
    $active_sheet->setCellValue('E1', 'TIPO');
    $active_sheet->getColumnDimension('F')->setAutoSize(true);
    $active_sheet->setCellValue('F1', 'F. EMISIÓN');
    $active_sheet->getColumnDimension('G')->setAutoSize(true);
    $active_sheet->setCellValue('G1', 'F. VENCIMIENTO');
    $active_sheet->getColumnDimension('H')->setAutoSize(true);
    $active_sheet->setCellValue('H1', 'F. PAGO');
    $active_sheet->getColumnDimension('I')->setAutoSize(true);
    $active_sheet->setCellValue('I1', 'SUBTOTAL');
    $active_sheet->getColumnDimension('J')->setAutoSize(true);
    $active_sheet->setCellValue('J1', 'DESCUENTO');
    $active_sheet->getColumnDimension('K')->setAutoSize(true);
    $active_sheet->setCellValue('K1', 'TOTAL');
    $active_sheet->getColumnDimension('L')->setAutoSize(true);
    $active_sheet->setCellValue('L1', 'PAGADO');
    $active_sheet->getColumnDimension('M')->setAutoSize(true);
    $active_sheet->setCellValue('M1', 'FORMA DE PAGO');
    $active_sheet->getColumnDimension('N')->setAutoSize(true);
    $active_sheet->setCellValue('N1', 'OBSERVACION');
    $active_sheet->getColumnDimension('O')->setAutoSize(true);
    $active_sheet->setCellValue('O1', 'ESTADO');

    $data = $this->model->export();
    if (!empty($data)) {
      $i = 2;
      foreach ($data as $key => $value) {
        if ($value['type'] == 1) {
          $type_bill = 'LIBRE';
          $month_letter = "";
        } else if ($value['type'] == 2) {
          $type_bill = 'SERVICIOS';
          $months = months();
          $month = date('n', strtotime($value['billed_month']));
          $month_letter = strtoupper($months[intval($month) - 1]);
        }
        if ($value['state'] == 1) {
          $state = 'PAGADO';
        } else if ($value['state'] == 2) {
          $state = 'PENDIENTE';
        } else if ($value['state'] == 3) {
          $state = 'VENCIDO';
        } else if ($value['state'] == 4) {
          $state = 'ANULADO';
        }
        $correlative = str_pad($value['correlative'], 5, "0", STR_PAD_LEFT);
        $invoice = $value['serie'] . '-' . $correlative;
        $payments = $this->model->invoice_paid($value['id']);

        $active_sheet->setCellValue('A' . $i, $value['internal_code']);
        $active_sheet->setCellValue('B' . $i, $value['client']);
        $active_sheet->setCellValue('C' . $i, $value['voucher'] . " Nº " . $invoice);
        $active_sheet->setCellValue('D' . $i, $month_letter);
        $active_sheet->setCellValue('E' . $i, $type_bill);
        $active_sheet->setCellValue('F' . $i, date("d/m/Y", strtotime($value['date_issue'])));
        $active_sheet->setCellValue('G' . $i, date("d/m/Y", strtotime($value['expiration_date'])));
        $active_sheet->setCellValue('H' . $i, empty($payments['payment_date']) ? "00/00/0000" : date("d/m/Y", strtotime($payments['payment_date'])));
        $active_sheet->setCellValue('I' . $i, format_money($value['subtotal']));
        $active_sheet->setCellValue('J' . $i, format_money($value['discount']));
        $active_sheet->setCellValue('K' . $i, format_money($value['total']));
        $active_sheet->setCellValue('L' . $i, empty($payments['amount']) ? format_money(0) : format_money($payments['amount']));
        $active_sheet->setCellValue('M' . $i, empty($payments['payment_type']) ? "" : $payments['payment_type']);
        $active_sheet->setCellValue('N' . $i, $value['observation']);
        $active_sheet->setCellValue('O' . $i, $state);
        $i++;
      }
    }

    $title = 'Lista de facturas';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
  }
  public function export_pendings()
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
    $spreadsheet->getActiveSheet()->getStyle('A1:L1')->applyFromArray($style_header);
    $center_cell = array(
      'alignment' => array(
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
      ),
    );
    $spreadsheet->getActiveSheet()->getStyle('A')->applyFromArray($center_cell);
    $spreadsheet->getActiveSheet()->getStyle('C:J')->applyFromArray($center_cell);
    $spreadsheet->getActiveSheet()->getStyle('L')->applyFromArray($center_cell);

    $active_sheet = $spreadsheet->getActiveSheet();
    $active_sheet->setTitle("Facturas pendientes");
    $active_sheet->getColumnDimension('A')->setAutoSize(true);
    $active_sheet->setCellValue('A1', 'COD');
    $active_sheet->getColumnDimension('B')->setAutoSize(true);
    $active_sheet->setCellValue('B1', 'CLIENTE');
    $active_sheet->getColumnDimension('C')->setAutoSize(true);
    $active_sheet->setCellValue('C1', 'COMPROBANTE');
    $active_sheet->getColumnDimension('D')->setWidth(20);
    $active_sheet->setCellValue('D1', 'MES FACTURADO');
    $active_sheet->getColumnDimension('E')->setWidth(20);
    $active_sheet->setCellValue('E1', 'TIPO');
    $active_sheet->getColumnDimension('F')->setAutoSize(true);
    $active_sheet->setCellValue('F1', 'F. EMISIÓN');
    $active_sheet->getColumnDimension('G')->setAutoSize(true);
    $active_sheet->setCellValue('G1', 'F. VENCIMIENTO');
    $active_sheet->getColumnDimension('H')->setAutoSize(true);
    $active_sheet->setCellValue('H1', 'SUBTOTAL');
    $active_sheet->getColumnDimension('I')->setAutoSize(true);
    $active_sheet->setCellValue('I1', 'DESCUENTO');
    $active_sheet->getColumnDimension('J')->setAutoSize(true);
    $active_sheet->setCellValue('J1', 'TOTAL');
    $active_sheet->getColumnDimension('K')->setAutoSize(true);
    $active_sheet->setCellValue('K1', 'OBSERVACION');
    $active_sheet->getColumnDimension('L')->setAutoSize(true);
    $active_sheet->setCellValue('L1', 'ESTADO');

    $data = $this->model->export_pendings();
    if (!empty($data)) {
      $i = 2;
      foreach ($data as $key => $value) {
        if ($value['type'] == 1) {
          $type_bill = 'LIBRE';
          $month_letter = "";
        } else if ($value['type'] == 2) {
          $type_bill = 'SERVICIOS';
          $months = months();
          $month = date('n', strtotime($value['billed_month']));
          $month_letter = strtoupper($months[intval($month) - 1]);
        }
        if ($value['state'] == 2) {
          $state = 'PENDIENTE';
        } else if ($value['state'] == 3) {
          $state = 'VENCIDO';
        }
        $correlative = str_pad($value['correlative'], 5, "0", STR_PAD_LEFT);
        $invoice = $value['serie'] . '-' . $correlative;

        $active_sheet->setCellValue('A' . $i, $value['internal_code']);
        $active_sheet->setCellValue('B' . $i, $value['client']);
        $active_sheet->setCellValue('C' . $i, $value['voucher'] . " Nº " . $invoice);
        $active_sheet->setCellValue('D' . $i, $month_letter);
        $active_sheet->setCellValue('E' . $i, $type_bill);
        $active_sheet->setCellValue('F' . $i, date("d/m/Y", strtotime($value['date_issue'])));
        $active_sheet->setCellValue('G' . $i, date("d/m/Y", strtotime($value['expiration_date'])));
        $active_sheet->setCellValue('H' . $i, format_money($value['subtotal']));
        $active_sheet->setCellValue('I' . $i, format_money($value['discount']));
        $active_sheet->setCellValue('J' . $i, format_money($value['total']));
        $active_sheet->setCellValue('K' . $i, $value['observation']);
        $active_sheet->setCellValue('L' . $i, $state);
        $i++;
      }
    }

    $title = 'Facturas pendientes';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
  }
  public function debt_opening(string $params)
  {
    if (!empty($params)) {
      $arrParams = explode("-", $params);
      $month = intval($arrParams[0]);
      $year = intval($arrParams[1]);
    } else {
      $month = date('m');
      $year = date('Y');
    }
    $data = $this->model->debt_opening($month, $year);
    if (empty($data)) {
      $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
    } else {
      $answer = array('status' => 'success', 'data' => $data);
    }
    echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    die();
  }
  public function detail_opening(string $params)
  {
    if (!empty($params)) {
      $arrParams = explode("-", $params);
      $month = intval($arrParams[0]);
      $year = intval($arrParams[1]);
    } else {
      $month = date('m');
      $year = date('Y');
    }
    $n = 1;
    $data = $this->model->detail_opening($month, $year);
    for ($i = 0; $i < count($data); $i++) {
      $list_services = '';
      $data[$i]['n'] = $n++;
      $data[$i]['client'] = $data[$i]['names'] . " " . $data[$i]['surnames'];
      $data[$i]['total'] = $_SESSION['businessData']['symbol'] . format_money($this->model->service_amount($data[$i]['id']));
      /* LISTA DE SERVICOS */
      $services = $this->model->select_detail_contract($data[$i]['id']);
      for ($p = 0; $p < count($services); $p++) {
        $list_services .= $services[$p]['service'] . "<br>";
      }
      $data[$i]['services'] = $list_services;
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    die();
  }
  public function mass_registration()
  {
    try {
      if (!$_POST)
        throw new Exception("El metodo no está habilitado!!!");
      // validar periodo
      if (empty($_POST['period'])) {
        throw new Exception("Campos señalados son obligatorios");
      }
      // generar dates
      $dateIssue = DateTime::createFromFormat('m/Y', $_POST['period']);
      $period = $dateIssue->format('m-Y');
      $period_currect = date("m-Y");
      // validar period
      if ($period_currect !== $period) {
        throw new Exception("El periodo no es valido!");
      }
      // configurar variables
      $period = explode("-", $period);
      $month = intval($period[0]);
      $year = intval($period[1]);
      // generar bills
      $service = new BillGenerate();
      $result = $service->generate([
        "year" => $year,
        "month" => $month
      ]);
      if (!$result) {
        return $this->json([
          "status" => "success",
          "msg" => "Las facturas de ese mes ya fueron emitidas"
        ]);
      }
      // facturas listas
      return $this->json([
        "status" => "success",
        "msg" => "se facturó los servicios de este mes"
      ]);
    } catch (\Throwable $th) {
      $this->json([
        "status" => "error",
        "msg" => $th->getMessage()
      ]);
    }
  }
  public function list_clients_free()
  {
    $html = "";
    $data = $this->model->list_clients_free();
    if (count($data) > 0) {
      $html .= '<option value="">SELECCIONAR</option>';
      for ($i = 0; $i < count($data); $i++) {
        if ($data[$i]['state'] == 1) {
          $html .= '<option value="' . encrypt($data[$i]['id']) . '">' . $data[$i]['names'] . ' ' . $data[$i]['surnames'] . '</option>';
        }
      }
    }
    echo $html;
    die();
  }
  public function list_clients_contract()
  {
    $html = "";
    $data = $this->model->list_clients_contract();
    if (count($data) > 0) {
      $html .= '<option value="">SELECCIONAR</option>';
      for ($i = 0; $i < count($data); $i++) {
        if ($data[$i]['state'] == 1) {
          $html .= '<option value="' . encrypt($data[$i]['id']) . '">' . $data[$i]['names'] . ' ' . $data[$i]['surnames'] . '</option>';
        }
      }
    }
    echo $html;
    die();
  }
  public function select_client_contract(string $idclient)
  {
    $idclient = decrypt($idclient);
    $idclient = intval($idclient);
    if ($idclient > 0) {
      $data = $this->model->select_invoice($idclient);
      if (empty($data)) {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      } else {
        $data['invoice']['encrypt_client'] = encrypt($data['invoice']['idclient']);
        $answer = array('status' => 'success', 'data' => $data);
      }
    } else {
      $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
    }
    echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    die();
  }

  public function set_promise()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }

    if (empty($_POST['billid'])) {
      return $this->json([
        "result" => "failed",
        "message" => "Invalid request"
      ]);
    }

    $payload = [
      "promise_enabled" => 1,
      "promise_date" => $_POST['promise_date'],
      "promise_set_date" => date('Y-m-d')
    ];

    if (isset($_POST['promise_comment'])) {
      $payload["promise_comment"] = $_POST['promise_comment'];
    }

    $billid = decrypt($_POST['billid']);
    $service = new BillAddPromiseService();
    $response = $service->execute($billid, $payload);

    if (!$response) {
      return $this->json([
        "result" => "failed",
        "message" => "No se pudo generar la promesa"
      ]);
    }

    return $this->json([
      "result" => "success",
      "message" => "Promesa registrada correctamente."
    ]);
  }

  public function unset_promise()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $res = (object) array();

    if (!empty($_POST['billid'])) {

      $billid = decrypt($_POST['billid']);

      $bill = sqlObject("SELECT * FROM bills WHERE id = " . $billid);

      if (!is_null($bill->id)) {

        $item = (object) array();

        sqlUpdate("bills", "promise_enabled", 0, $billid);

        $res->result = "success";
      } else {
        $res->result = "failed";
        $res->message = "Invalid request";
      }
    } else {
      $res->result = "failed";
      $res->message = "Invalid request";
    }

    echo json_encode($res, JSON_UNESCAPED_UNICODE);
  }
  public function get_promise()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $res = (object) array();

    if (!empty($_POST['billid'])) {

      $billid = decrypt($_POST['billid']);

      $bill = sqlObject("SELECT * FROM bills WHERE id = " . $billid);

      if (!is_null($bill->id)) {

        $promise = (object) array();

        $promise->promise_enabled = $bill->promise_enabled;
        $promise->promise_date = $bill->promise_date;
        $promise->promise_comment = $bill->promise_comment;

        $res->result = "success";
        $res->data = $promise;
      } else {
        $res->result = "failed";
        $res->message = "Invalid request";
      }
    } else {
      $res->result = "failed";
      $res->message = "Invalid request";
    }

    echo json_encode($res, JSON_UNESCAPED_UNICODE);
  }
}
