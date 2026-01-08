<?php
require 'Libraries/spreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
class Providers extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        consent_permission(SUPPLIERS);
    }
    public function providers()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_name'] = "Proveedores";
        $data['page_title'] = "Gestión de Proveedores";
        $data['home_page'] = "Dashboard";
        $data['previous_page'] = "Inventario";
        $data['actual_page'] = "Proveedores";
        $data['page_functions_js'] = "providers.js";
        $this->views->getView($this, "providers", $data);
    }
    public function list_records()
    {
        if ($_SESSION['permits_module']['v']) {
            $n = 1;
            $data = $this->model->list_records();
            for ($i = 0; $i < count($data); $i++) {
                $update = '';
                $delete = '';
                $data[$i]['n'] = $n++;
                $data[$i]['associates'] = '<span class="badge label-warning f-s-12">' . $this->model->associates($data[$i]['id']) . '</span>';
                if ($_SESSION['permits_module']['a']) {
                    $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a>';
                    $update_2 = '<a href="javascript:;" class="dropdown-item" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt mr-1"></i>Editar</a>';
                } else {
                    $update = '';
                    $update_2 = '';
                }
                if ($_SESSION['permits_module']['e']) {
                    $delete = '<a href="javascript:;" class="red" data-toggle="tooltip" data-original-title="Eliminar" onclick="remove(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-trash-alt"></i></a>';
                    $delete_2 = '<a href="javascript:;" class="dropdown-item" onclick="remove(\'' . encrypt($data[$i]['id']) . '\')"><i class="far fa-trash-alt mr-1"></i>Eliminar</a>';
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
    public function select_record(string $idprovider)
    {
        if ($_SESSION['permits_module']['v']) {
            $idprovider = decrypt($idprovider);
            $idprovider = intval($idprovider);
            if ($idprovider > 0) {
                $data = $this->model->select_record($idprovider);
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
        }
        die();
    }
    public function list_documents()
    {
        $selected = $_GET['selected'];
        $isJson = $_GET['isjson'];
        $html = "";
        $data = $this->model->list_documents();
        $html = "";
        if (isset($isJson))
            return $this->json($data);
        foreach ($data as $item) {
            $isSelected = $selected == $item['id'] ? 'selected' : '';
            $id = $item['id'];
            $text = $item['document'];
            $dataEncode = json_encode($item);
            $html .= "<option value='{$id}' {$isSelected} data-document='{$dataEncode}'>";
            $html .= $text;
            $html .= "</option>";
        }
        $this->send($html);
    }
    public function list_providers()
    {
        $html = "";
        $arrData = $this->model->list_providers();
        if (count($arrData) > 0) {
            for ($i = 0; $i < count($arrData); $i++) {
                if ($arrData[$i]['state'] == 1) {
                    $html .= '<option value="' . encrypt($arrData[$i]['id']) . '">' . $arrData[$i]['provider'] . '</option>';
                }
            }
        }
        echo $html;
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
                    $arrResponse = array('status' => 'error', 'msg' => 'No se encontraton resultados.');
                } else {
                    $arrConsult = array(
                        "business_name" => $answer['data']['nombres'] . " " . $answer['data']['apellido_paterno'] . " " . $answer['data']['apellido_materno'],
                        "address" => ""
                    );
                    $arrResponse = array('status' => 'success', 'data' => $arrConsult);
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
                    $arrResponse = array('status' => 'error', 'msg' => 'No se encontraton resultados.');
                } else {
                    $arrConsult = array(
                        "business_name" => $answer['data']['nombre_o_razon_social'],
                        "address" => $answer['data']['direccion']
                    );
                    $arrResponse = array('status' => 'success', 'data' => $arrConsult);
                }
            }
        }
        if ($type == 4) {
            $validate = strlen($document);
            if ($validate < 6) {
                $arrResponse = array('status' => 'info', 'msg' => 'El carnet de extranjeria no debe tener menos de 6 digitos.');
            } else if ($validate > 20) {
                $arrResponse = array('status' => 'info', 'msg' => 'El dni debe no debe tener mas de 20 digitos.');
            } else {
                $answer = consult_document("dni", $document, $_SESSION['businessData']['reniec_apikey']);
                if (empty($answer['success'])) {
                    $arrResponse = array('status' => 'error', 'msg' => 'No se encontraton resultados.');
                } else {
                    $arrConsult = array(
                        "business_name" => $answer['data']['nombres'] . " " . $answer['data']['apellido_paterno'] . " " . $answer['data']['apellido_materno'],
                        "address" => ""
                    );
                    $arrResponse = array('status' => 'success', 'data' => $arrConsult);
                }
            }
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function action()
    {
        if ($_POST) {
            if (empty($_POST['provider']) || empty($_POST['document'])) {
                $response = array("status" => 'errro', "msg" => 'Campos señalados son obligatorios.');
            } else {
                $id = decrypt($_POST['idprovider']);
                $id = intval($id);
                $provider = strtoupper(strClean($_POST['provider']));
                $types = intval(strClean($_POST['listTypes']));
                $document = strClean($_POST['document']);
                $mobile = strClean($_POST['mobile']);
                $email = strtolower(strClean($_POST['email']));
                $address = strtoupper(strClean($_POST['address']));
                $state = intval(strClean($_POST['listStatus']));
                $datetime = date("Y-m-d H:i:s");
                if ($id == 0) {
                    $option = 1;
                    if ($_SESSION['permits_module']['r']) {
                        $request = $this->model->create($provider, $types, $document, $mobile, $email, $address, $datetime, $state);
                    }
                } else {
                    $option = 2;
                    if ($_SESSION['permits_module']['a']) {
                        $request = $this->model->modify($id, $provider, $types, $document, $mobile, $email, $address, $state);
                    }
                }
                if ($request == "success") {
                    if ($option == 1) {
                        $response = array('status' => 'success', 'msg' => 'Se ha registrado exitosamente.');
                    } else {
                        $response = array('status' => 'success', 'msg' => 'Se ha actualizado el registro exitosamente.');
                    }
                } else if ($request == 'exists') {
                    $response = array('status' => 'error', 'msg' => '¡Atención! El registro ya existe, ingrese otro.');
                } else {
                    $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operaciòn, intentelo nuevamente.');
                }
            }
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function remove()
    {
        if ($_POST) {
            if ($_SESSION['permits_module']['e']) {
                $idprovider = decrypt($_POST['idprovider']);
                $idprovider = intval($idprovider);
                $request = $this->model->remove($idprovider);
                if ($request == 'success') {
                    $response = array('status' => 'success', 'msg' => 'El registro se ha eliminado.');
                } else if ($request == 'exists') {
                    $response = array('status' => 'exists', 'msg' => 'El proveedor esta en uso, imposible eliminar');
                } else {
                    $response = array('status' => 'error', 'msg' => 'Error no se pudo eliminar.');
                }
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }
    public function import()
    {
        /* Variable Post */
        $file = $_FILES["import_providers"]["tmp_name"];
        /* Cargamos el archivo */
        $document = IOFactory::load($file);
        /* Hoja Productos*/
        $sheet_suppliers = $document->getSheetByName("Proveedores");
        $row_suppliers = $sheet_suppliers->getHighestDataRow();
        /* Ciclo registrar productos */
        $total_suppliers = 0;
        for ($i = 2; $i <= $row_suppliers; $i++) {
            $provider = strtoupper(strClean($sheet_suppliers->getCell("A" . $i)));
            $type_document = strClean($sheet_suppliers->getCell("B" . $i));
            $document = strClean($sheet_suppliers->getCell("C" . $i));
            $mobile = strClean($sheet_suppliers->getCell("D" . $i));
            $email = strtolower(strClean($sheet_suppliers->getCell("E" . $i)));
            $address = strtoupper(strClean($sheet_suppliers->getCell("F" . $i)));
            $datetime = date("Y-m-d H:i:s");

            $request = $this->model->import($provider, $type_document, $document, $mobile, $email, $address, $datetime);

            $total_suppliers = $total_suppliers + $request;
        }
        if ($total_suppliers >= 1) {
            $response = array('status' => 'success', 'msg' => 'La importación se realizo exitosamente.');
        } else if ($total_suppliers == 0) {
            $response = array('status' => 'warning', 'msg' => 'No se pudo importar, revise el excel en caso que realizaste mal rellenado.');
        } else {
            $response = array("status" => 'error', "msg" => 'No se pudo realizar esta operaciòn, intentelo nuevamente');
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
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->applyFromArray($style_header);
        $center_cell = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A')->applyFromArray($center_cell);
        $spreadsheet->getActiveSheet()->getStyle('C:E')->applyFromArray($center_cell);

        $active_sheet = $spreadsheet->getActiveSheet();
        $active_sheet->setTitle("Proveedores");
        $active_sheet->getColumnDimension('A')->setAutoSize(true);
        $active_sheet->setCellValue('A1', 'COD');
        $active_sheet->getColumnDimension('B')->setAutoSize(true);
        $active_sheet->setCellValue('B1', 'RAZON SOCIAL');
        $active_sheet->getColumnDimension('C')->setAutoSize(true);
        $active_sheet->setCellValue('C1', 'TIPO DOCUMENTO');
        $active_sheet->getColumnDimension('D')->setAutoSize(true);
        $active_sheet->setCellValue('D1', 'DOCUMENTO');
        $active_sheet->getColumnDimension('E')->setAutoSize(true);
        $active_sheet->setCellValue('E1', 'CELULAR');
        $active_sheet->getColumnDimension('F')->setAutoSize(true);
        $active_sheet->setCellValue('F1', 'CORREO');
        $active_sheet->getColumnDimension('G')->setAutoSize(true);
        $active_sheet->setCellValue('G1', 'DIRECCÓN');

        $data = $this->model->export();
        if (!empty($data)) {
            $i = 2;
            foreach ($data as $key => $value) {
                $active_sheet->setCellValue('A' . $i, $value['id']);
                $active_sheet->setCellValue('B' . $i, $value['provider']);
                $active_sheet->setCellValue('C' . $i, $value['name_doc']);
                $active_sheet->setCellValue('D' . $i, $value['document']);
                $active_sheet->setCellValue('E' . $i, $value['mobile']);
                $active_sheet->setCellValue('F' . $i, $value['email']);
                $active_sheet->setCellValue('G' . $i, $value['address']);
                $i++;
            }
        }

        $title = 'Lista de proveedores';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
}
