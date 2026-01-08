<?php
require 'Libraries/dompdf/vendor/autoload.php';
require 'Libraries/spreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Dompdf\Dompdf;

class Payments extends Controllers
{
  public function __construct()
  {
    parent::__construct();
    session_start();
    if (empty($_SESSION['login'])) {
      header('Location: ' . base_url() . '/login');
      die();
    }
    consent_permission(PAYMENTS);
  }

  public function payments()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['page_name'] = "Cobranzas realizadas";
    $data['page_title'] = "Cobranzas Realizadas";
    $data['home_page'] = "Dashboard";
    $data['previous_page'] = "Finanzas";
    $data['actual_page'] = "Cobros";
    $data['page_functions_js'] = "payments.js";
    $data['zonas'] = $this->model->list_zonas();
    $this->views->getView($this, "payments", $data);
  }

  public function add_payment()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['page_name'] = "Registrar pagos";
    $data['page_functions_js'] = "add_payment.js";
    $this->views->getView($this, "add", $data);
  }

  public function statistics()
  {
    if (empty($_SESSION['permits_module']['v'])) {
      header("Location:" . base_url() . '/dashboard');
    }
    $data['page_name'] = "Resumen de transacciones";
    $data['page_functions_js'] = "statistics.js";
    $this->views->getView($this, "statistics", $data);
  }

  public function export_pdf()
  {
    if ($_SESSION['permits_module']['v']) {
      $filter = [...$_GET];
      if (empty($_GET['start']) && empty($_GET['end'])) {
        $filter['start'] = date("Y-m-01");
        $filter['end'] = date("Y-m-t");
      } else {
        $dateStart = DateTime::createFromFormat('d/m/Y', $_GET['start']);
        $filter['start'] = $dateStart->format('Y-m-d');
        $dateEnd = DateTime::createFromFormat('d/m/Y', $_GET['end']);
        $filter['end'] = $dateEnd->format('Y-m-d');
      }

      $data = array();
      $query = $this->model->list_records($filter);
      if (empty($query)) {
        header('Location: ' . base_url() . '/login');
      } else {
        $data = array('start' => $filter['start'], 'end' => $filter['end'], 'data' => $query);
        ob_end_clean();
        $html = redirect_pdf("Resources/reports/pdf/payment_report", $data);
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set(array('isRemoteEnabled' => true));
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);
        $orientation = 'landscape';
        $customPaper = 'A4';
        $dompdf->setPaper($customPaper, $orientation);
        $dompdf->render();
        $dompdf->stream('Lista de cobros.pdf', array("Attachment" => false));
      }
      die();
    } else {
      header('Location: ' . base_url() . '/login');
    }
  }

  public function export_excel()
  {
    if ($_SESSION['permits_module']['v']) {
      $filter = [...$_GET];
      if (empty($_GET['start']) && empty($_GET['end'])) {
        $filter['start'] = date("Y-m-01");
        $filter['end'] = date("Y-m-t");
      } else {
        $dateStart = DateTime::createFromFormat('d/m/Y', $_GET['start']);
        $filter['start'] = $dateStart->format('Y-m-d');
        $dateEnd = DateTime::createFromFormat('d/m/Y', $_GET['end']);
        $filter['end'] = $dateEnd->format('Y-m-d');
      }

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
      $spreadsheet->getActiveSheet()->getStyle('A1:J1')->applyFromArray($style_header);
      $center_cell = array(
        'alignment' => array(
          'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
          'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ),
      );
      $spreadsheet->getActiveSheet()->getStyle('A')->applyFromArray($center_cell);
      $spreadsheet->getActiveSheet()->getStyle('C:L')->applyFromArray($center_cell);
      $spreadsheet->getActiveSheet()->getStyle('J')->applyFromArray($center_cell);
      $active_sheet = $spreadsheet->getActiveSheet();
      $active_sheet->setTitle("Cobranzas");
      $active_sheet->getColumnDimension('A')->setAutoSize(true);
      $active_sheet->setCellValue('A1', 'COD');
      $active_sheet->getColumnDimension('B')->setAutoSize(true);
      $active_sheet->setCellValue('B1', 'CLIENTE');
      $active_sheet->getColumnDimension('C')->setAutoSize(true);
      $active_sheet->setCellValue('C1', 'Nº FACTURA');
      $active_sheet->getColumnDimension('D')->setAutoSize(true);
      $active_sheet->setCellValue('D1', 'FECHA');
      $active_sheet->getColumnDimension('E')->setAutoSize(true);
      $active_sheet->setCellValue('E1', 'TOTAL FACTURA');
      $active_sheet->getColumnDimension('F')->setAutoSize(true);
      $active_sheet->setCellValue('F1', 'PAGADO');
      $active_sheet->getColumnDimension('G')->setAutoSize(true);
      $active_sheet->setCellValue('G1', 'FORMA PAGO');
      $active_sheet->getColumnDimension('H')->setAutoSize(true);
      $active_sheet->setCellValue('H1', 'USUARIO');
      $active_sheet->getColumnDimension('I')->setAutoSize(true);
      $active_sheet->setCellValue('I1', 'COMENTARIO');
      $active_sheet->getColumnDimension('J')->setAutoSize(true);
      $active_sheet->setCellValue('J1', 'ESTADO');
      $data = $this->model->list_records($filter);
      if (!empty($data)) {
        $i = 2;
        foreach ($data as $key => $value) {
          if ($value['state'] == 1) {
            $state = 'RECIBIDO';
          } else if ($value['state'] == 2) {
            $state = 'ENTREGAR';
          } else if ($value['state'] == 3) {
            $state = 'ANULADO';
          }
          $active_sheet->setCellValue('A' . $i, $value['internal_code']);
          $active_sheet->setCellValue('B' . $i, $value['client']);
          $active_sheet->setCellValue('C' . $i, str_pad($value['correlative'], 7, "0", STR_PAD_LEFT));
          $active_sheet->setCellValue('D' . $i, date("d/m/Y H:i", strtotime($value['payment_date'])));
          $active_sheet->setCellValue('E' . $i, $_SESSION['businessData']['symbol'] . format_money($value['bill_total']));
          $active_sheet->setCellValue('F' . $i, $_SESSION['businessData']['symbol'] . format_money($value['amount_paid']));
          $active_sheet->setCellValue('G' . $i, $value['payment_type']);
          $active_sheet->setCellValue('H' . $i, $value['user']);
          $active_sheet->setCellValue('I' . $i, $value['comment']);
          $active_sheet->setCellValue('J' . $i, $state);
          $i++;
        }
      }
      $title = 'Lista de cobros';
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
      header('Cache-Control: max-age=0');
      $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
      $writer->save('php://output');
    }
  }

  public function select_record(string $idpayment)
  {
    if ($_SESSION['permits_module']['v']) {
      $idpayment = decrypt($idpayment);
      $idpayment = intval($idpayment);
      if ($idpayment > 0) {
        $data = $this->model->select_record($idpayment);
        if (empty($data)) {
          $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
        } else {
          $data['encrypt_id'] = encrypt($data['id']);
          $data['encrypt_bill'] = encrypt($data['billid']);
          $data['encrypt_client'] = encrypt($data['clientid']);
          $data['invoice'] = str_pad($data['correlative'], 7, "0", STR_PAD_LEFT);
          $answer = array('status' => 'success', 'data' => $data);
        }
      } else {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      }
      echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    }
    die();
  }

  public function list_records()
  {
    if ($_SESSION['permits_module']['v']) {
      $filter = [...$_GET];

      if (empty($_GET['start']) && empty($_GET['end'])) {
        $filter["start"] = date("Y-m-01");
        $filter["end"] = date("Y-m-t");
      } else {
        $dateStart = DateTime::createFromFormat('d/m/Y', $_GET['start']);
        $filter["start"] = $dateStart->format('Y-m-d');
        $dateEnd = DateTime::createFromFormat('d/m/Y', $_GET['end']);
        $filter["end"] = $dateEnd->format('Y-m-d');
      }

      $data = $this->model->list_records($filter);
      $n = 1;

      for ($i = 0; $i < count($data); $i++) {
        $data[$i]['n'] = $n++;
        $data[$i]['count_total'] = $data[$i]['amount_paid'];
        /* ID ENCRUPTADOS */
        $data[$i]['encrypt_client'] = encrypt($data[$i]['clientid']);
        $data[$i]['encrypt_bill'] = encrypt($data[$i]['billid']);
        /* COMPROBANTE */
        $correlative = str_pad($data[$i]['correlative'], 7, "0", STR_PAD_LEFT);
        $data[$i]['invoice'] = $correlative;
        /* CLIENTE TIENE CONTRATO */
        $contract = $this->model->contract_client($data[$i]['clientid']);
        if (empty($contract)) {
          $data[$i]['encrypt_contract'] = "";
        } else {
          $data[$i]['encrypt_contract'] = encrypt($contract['id']);
        }
        $data[$i]['amount_paid'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['amount_paid']);
        $data[$i]['bill_total'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['bill_total']);
        if ($_SESSION['permits_module']['a']) {
          if ($data[$i]['state'] == 1) {
            $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
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
            $cancel = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Anular" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban"></i></a>';
            $cancel_2 = '<a href="javascript:;" class="dropdown-item" onclick="cancel(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-ban mr-1"></i>Anular</a>';
          } else {
            $cancel = '';
            $cancel_2 = '';
          }
        } else {
          $cancel = '';
          $cancel_2 = '';
        }
        if ($data[$i]['state'] == 1) {
          $data[$i]['count_state'] = 'RECIBIDA';
        } else if ($data[$i]['state'] == 2) {
          $data[$i]['count_state'] = 'ANULADO';
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

  public function modify_payment()
  {
    if ($_POST) {
      if (empty($_POST['idpayment']) || empty($_POST['idbill']) || empty($_POST['total_payment'])) {
        $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios');
      } else {
        $id = decrypt($_POST['idpayment']);
        $id = intval($id);
        $bill = intval(strClean($_POST['idbill']));
        $user = intval(strClean($_SESSION['idUser']));
        $date = DateTime::createFromFormat('d/m/Y H:i', $_POST['date_time']);
        $datetime = $date->format('Y-m-d H:i:s');
        $typepay = intval(strClean($_POST['listTypePay']));
        $comment = strtoupper(strClean($_POST['comment']));
        $subscriber = strClean($_POST['total_payment']);
        if ($_SESSION['permits_module']['a']) {
          $request = $this->model->modify($id, $typepay, $datetime, $comment);
        }
        if ($request == "success") {
          $response = array('status' => 'success', 'msg' => 'Los cambios fueron guardados correctamente.');
        } else {
          $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operaciòn, intentelo nuevamente.');
        }
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }

  public function cancel()
  {
    if ($_POST) {
      if ($_SESSION['permits_module']['e']) {
        $idpayment = decrypt($_POST['idpayment']);
        $idpayment = intval($idpayment);
        $payment = $this->model->select_record($idpayment);
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
          $request = $this->model->cancel($idpayment);
          if ($request == 'success') {
            $response = array('status' => 'success', 'msg' => 'Pago anulado correctamente.');
          } else {
            $response = array('status' => 'error', 'msg' => 'Error no se pudo anular.');
          }
        } else {
          $response = array('status' => 'error', 'msg' => 'La transacción no exite.');
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
      }
    }
    die();
  }

  public function payment_summary(int $year)
  {
    $year = intval($year);
    if ($year > 0) {
      $data = $this->model->payment_summary($year);
      echo json_encode($data, JSON_UNESCAPED_UNICODE);
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
          $data['bill']['voucher'] = ucwords(strtolower($data['bill']['voucher']));
          $data['bill']['serie'] = str_pad($data['bill']['correlative'], 7, "0", STR_PAD_LEFT);
          $answer = array('status' => 'success', 'data' => $data);
        }
      } else {
        $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
      }
      echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    }
    die();
  }

  public function search_clients()
{
  if ($_POST) {
    $html = "";
    $search = strClean($_POST['search']);
    $arrData = $this->model->search_clients($search);

    if (empty($arrData)) {
      $html .= '<li>No se encontró "' . $search . '"</li>';
    } else {
      foreach ($arrData as $row) {
        // Texto de documento
        $docText = (!empty($row['document'])) 
          ? '(DOC ' . $row['document'] . ')' 
          : '(SIN DOCUMENTO)';

        // Generar item (siempre tiene deuda)
        $html .= '<li onclick="pending_invoices(\'' . encrypt($row['id']) . '\')" class="bg-debt">'
          . $row['names'] . ' ' . $row['surnames']
          . '<small class="ml-1 f-s-10 text-secundary">' . $docText . '</small>'
          . '</li>';
      }
    }

    echo $html;
  }
  die();
}

  public function pending_invoices(string $idclient)
  {
    $idclient = decrypt($idclient);
    $idclient = intval($idclient);
    if ($idclient > 0) {
      $data = $this->model->pending_invoices($idclient);
      if (empty($data)) {
        $answer = array('status' => 'error', 'msg' => 'El cliente no tiene ninguna factura pendiente de pago.');
      } else {
        ob_end_clean();
        $data['views'] = views("bulk_payments", $data);
        $answer = array('status' => 'success', 'data' => $data);
      }
    } else {
      $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
    }
    echo json_encode($answer, JSON_UNESCAPED_UNICODE);
    die();
  }

  public function list_pendings(string $idclient)
  {
    $idclient = intval($idclient);
    $data = $this->model->list_pendings($idclient, "DESC");
    for ($i = 0; $i < count($data); $i++) {
      if ($data[$i]['type'] == 1) {
        $month_letter = "";
      } else if ($data[$i]['type'] == 2) {
        $months = months();
        $month = date('n', strtotime($data[$i]['billed_month']));
        $year = date('Y', strtotime($data[$i]['billed_month']));
        $month_letter = strtoupper($months[intval($month) - 1]) . "," . $year;
      }
      $correlative = str_pad($data[$i]['correlative'], 7, "0", STR_PAD_LEFT);
      $data[$i]['invoice'] = $correlative;
      $data[$i]['billing'] = $month_letter;
      $data[$i]['balance'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['remaining_amount']);
      $data[$i]['total'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['total']);
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    die();
  }

  public function cancel_massive()
  {
    if ($_POST) {
      if (isset($_POST['ids'])) {
        $idpayments = explode(",", $_POST['ids']);
      } else {
        $idpayments = [];
      }
      if (count($idpayments) > 0) {
        $total = 0;
        for ($i = 0; $i < count($idpayments); $i++) {
          $payment = $this->model->pending_payments($idpayments[$i]);
          $idbill = $payment['billid'];
          $amount_paid = $payment['amount_paid'];
          $bill = $this->model->select_bill($idbill);
          $remaining_amount = $bill['remaining_amount'];
          if ($remaining_amount == 0) {
            $this->model->subtract_amounts($idbill, $amount_paid, 2);
          } else if ($remaining_amount > 0) {
            $this->model->subtract_amounts($idbill, $amount_paid, 0);
          }
          $request = $this->model->cancel_massive($idpayments[$i]);
          $total = $total + $request;
        }
        if ($total > 0) {
          $response = array('status' => 'success', 'msg' => 'Pagos cancelados exitosamente.');
        } else {
          $response = array('status' => 'info', 'msg' => 'No se pudo realizar esta operacion.');
        }
      } else {
        $response = array('status' => 'error', 'msg' => 'No se enviaron datos, imposible realizar esta operacion.');
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }

  public function mass_payments()
  {
    if ($_POST) {

      $total_client = (float) $_POST['total_pay'];
      $total_discount = (float) $_POST['total_discount'];

      if (empty($_POST['idclient']) || $total_client < 0) {
        return $this->json([
          "status" => "error",
          "msg" => "Campos señalados son obligatorios"
        ]);
      }
      // validar fecha/hora
      $datetime = date('Y-m-d H:i:s');
      if ($_SESSION['userData']['profileid'] == 1) {
        $date = DateTime::createFromFormat('d/m/Y H:i', $_POST['date_time']);
        $datetime = $date->format('Y-m-d H:i:s');
      }

      $comment = strtoupper(strClean($_POST['comment']));
      $userId = intval(strClean($_SESSION['idUser']));
      $payTypeId = $_POST['typepay'];
      $clientId = (string) decrypt($_POST['idclient']);

      try {
        $service = new PaymentBillMassiveService($userId, $payTypeId);
        $service->setDatetime($datetime);
        $service->setComment(comment: $comment);
        $response = $service->execute(
          $clientId,
          $total_client,
          $total_discount
        );

        $businnes = $_SESSION['businessData'];
        $client = $response['client'];
        $arrayPaymentId = $response["arrayPaymentId"];

        $listener = new PaymentMassiveListener($this->model, (Object) $businnes);
        $listener->setArrayPaymentId($arrayPaymentId);

        $eventManager = new EventManager();
        $eventManager->subscribe($listener);
        $eventManager->triggerEvent($client);

        return $this->json([
          "status" => "success",
          "msg" => "Se agregó el pago correctamente!!!",
          'bills' => $response['arrayBillId'],
          "arrayPaymentId" => $response["arrayPaymentId"],
          'current_paid' => $total_client,
          'business_name' => $_SESSION['businessData']['business_name'],
          'symbol' => $_SESSION['businessData']['symbol'],
          'client' => $client->names . " " . $client->surnames,
          'mobile' => $client->mobile,
          'country' => $_SESSION['businessData']['country_code']
        ]);
      } catch (\Throwable $th) {
        return $this->json([
          "status" => "error",
          "msg" => $th->getMessage()
        ]);
      }
    }
    die();
  }

  public function massive_pdfs()
  {
    if ($_POST) {
      if (isset($_POST['ids'])) {
        $idbills = $_POST['ids'];
      } else {
        $idbills = [];
      }
      if (count($idbills) > 0) {
        $data = $this->model->massive_bills($idbills);
        if (empty($data)) {
          echo "Información no ha sido encontrada.";
        } else {
          ob_end_clean();
          $html = redirect_pdf("Resources/reports/pdf/massive_bills", $data);
          $customPaper = array(0, 0, 204, 700);
          $dompdf = new Dompdf();
          $options = $dompdf->getOptions();
          $options->set(array('isRemoteEnabled' => true));
          $dompdf->setOptions($options);
          $dompdf->loadHtml($html);
          $orientation = 'portrait';
          $dompdf->setPaper($customPaper, $orientation);
          $dompdf->render();
          $voucher = "comprobante";
          $dompdf->stream($voucher . '.pdf', array("Attachment" => false));
        }
      } else {
        echo "No se enviaron datos, imposible realizar esta operacion.";
      }
    }
    die();
  }

  public function massive_impressions()
  {
    if ($_POST) {
      if (isset($_POST['ids'])) {
        $idbills = $_POST['ids'];
      } else {
        $idbills = [];
      }
      if (count($idbills) > 0) {
        $data = $this->model->massive_bills($idbills);
        if (empty($data)) {
          echo "Información no ha sido encontrada.";
        } else {
          redirect_file("Resources/reports/prints/massive_bills", $data);
        }
      } else {
        echo "No se enviaron datos, imposible realizar esta operacion.";
      }
    }
    die();
  }

  public function massive_msj()
  {
    if ($_POST) {
      if (isset($_POST['ids'])) {
        $arrayPaymentId = explode(",", $_POST['ids']);
      } else {
        $arrayPaymentId = [];
      }

      if (count($arrayPaymentId) > 0) {

        $client = $this->model->find_client_by_paymentIds($arrayPaymentId);

        if (!$client) {
          return $this->json([
            "status" => "error",
            "message" => "No se encontró el cliente!!!"
          ]);
        }

        $messageWsp = new PlantillaWspInfoService($client, (Object) $_SESSION['businessData']);
        $messageWsp->setArrayPaymentId($arrayPaymentId);
        $message = $messageWsp->execute("PAGO_MASSIVE");

        if (empty($message)) {
          $response = array('status' => 'error', 'msg' => 'Información no ha sido encontrada.');
        } else {
          return $this->json([
            "status" => "success",
            "message" => $message
          ]);
        }
      } else {
        $response = array('status' => 'error', 'msg' => 'No se enviaron datos, imposible realizar esta operacion.');
      }
      echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    die();
  }

  public function generate_bills()
  {
    $status = 501;

    try {
      $payload = (object) $_POST;

      if (!$payload->clientId || !$payload->months) {
        throw new Exception("Los datos son incorrectos");
      }

      $payload->months = (int) $payload->months;
      if ($payload->months <= 0) {
        throw new Exception("El número de meses debe ser mayor/igual a 1");
      }

      $service = new BillGenerate();
      $service->setMysql($this->model);
      $service->getMysql()->createQueryRunner();

      $dateStart = new DateTime($payload->date);
      $limit = (int) $payload->months;


      try {
        for ($i = 0; $i < $limit; $i++) {

          $service->setIssue($dateStart->format("Y-m-d"));
          $service->generate([
            "clientId" => $payload->clientId,
          ]);

          $dateStart->modify('+1 month');
        }

        $service->getMysql()->commit();

        return $this->json([
          "status" => true,
          "message" => "Las facturas se generaron correctamente!!!"
        ]);
      } catch (\Throwable $th) {
        $service->getMysql()->rollback();
        throw $th;
      }
    } catch (Exception $ex) {
      http_response_code($status);
      return $this->json([
        "status" => $status,
        "message" => $ex->getMessage()
      ]);
    }
  }
}
