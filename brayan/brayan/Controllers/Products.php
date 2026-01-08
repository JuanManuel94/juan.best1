<?php
    require 'Libraries/resize/vendor/autoload.php';
    require 'Libraries/spreadsheet/vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use Verot\Upload\Upload;
    class Products extends Controllers{
        public function __construct(){
            parent::__construct();
			session_start();
			if(empty($_SESSION['login'])){
				header('Location: '.base_url().'/login');
				die();
			}
			consent_permission(PRODUCTS);
        }
        public function products(){
            if(empty($_SESSION['permits_module']['v'])){
                header("Location:".base_url().'/dashboard');
            }
            $data['page_name'] = "Productos";
            $data['page_title'] = "Gestión de Productos";
            $data['home_page'] = "Dashboard";
            $data['previous_page'] = "Almacen";
            $data['actual_page'] = "Productos";
            $data['page_functions_js'] = "products.js";
            $this->views->getView($this,"products",$data);
        }
        public function detail(string $idproduct){
            if(empty($_SESSION['permits_module']['v'])){
                header("Location:".base_url().'/dashboard');
            }
            $idproduct = decrypt($idproduct);
            $idproduct = intval($idproduct);
            if($idproduct > 0){
                $detail = $this->model->view_detail($idproduct);
                $data['page_name'] = "Detalle de producto";
                $data['page_title'] = "Información del producto";
                $data['home_page'] = "Dashboard";
                $data['previous_page'] = "Productos";
                $data['actual_page'] = "Información";
                if(empty($detail['product'])){
                  header("Location:".base_url().'/products');
                }else{
                    $data['detail'] = $detail;
                    $this->views->getView($this,"detail",$data);
                }
            }else{
                header("Location:".base_url().'/products');
            }
            die();
        }
        public function list_records(){
            if($_SESSION['permits_module']['v']){
                $data = $this->model->list_records();
                for($i=0; $i < count($data); $i++){
                    /* ID PRODUCTO ENCRYTADO */
                    $data[$i]['encrypt'] = encrypt($data[$i]['id']);
                    if($data[$i]['stock'] >= 1 && $data[$i]['stock'] < $data[$i]['stock_alert']){
                      $data[$i]['state'] = '<span class="label label-warning">POR AGOTARSE</span>';
                    }else if($data[$i]['stock'] == $data[$i]['stock_alert']){
                      if($data[$i]['stock'] == 0 && $data[$i]['stock_alert'] == 0){
                        $data[$i]['state'] = '<span class="label label-danger">AGOTADO</span>';
                      }else{
                        $data[$i]['state'] = '<span class="label label-indigo">EN MINIMO</span>';
                      }
                    }else if($data[$i]['stock'] > $data[$i]['stock_alert']){
                      $data[$i]['state'] = '<span class="label label-success">ABASTESIDO</span>';
                    }else{
                      $data[$i]['state'] = '<span class="label label-danger">AGOTADO</span>';
                    }
                    $data[$i]['price'] = $_SESSION['businessData']['symbol'].format_money($data[$i]['sale_price']);
                    $data[$i]['date'] = date("d/m/Y", strtotime($data[$i]['registration_date']));
                    if($_SESSION['permits_module']['v']){
                        $view = '<a href="javascript:;" class="black" data-toggle="tooltip" data-original-title="Información detallada" onclick="view(\''.encrypt($data[$i]['id']).'\')"><i class="fas fa-box-open"></i></a>';
                        $view_2 = '<a href="javascript:;" class="dropdown-item" onclick="view(\''.encrypt($data[$i]['id']).'\')"><i class="fas fa-box-open mr-1"></i>Información detallada</a>';
                    }else{
                        $view = '';
                        $view_2 = '';
                    }
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
                    $options = '<div class="hidden-sm hidden-xs action-buttons">'.$update.$delete.$view.'</div>';
                    $options .='<div class="hidden-md hidden-lg"><div class="dropdown">
                    <button class="btn btn-white btn-sm" data-toggle="dropdown" aria-expanded="false">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 29px, 0px);">
                      '.$update_2.$delete_2.$view_2.'
                    </div>
                    </div></div>';
                    $data[$i]['options'] = $options;
                }
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function list_products(){
            $html = "";
            $arrData = $this->model->list_products();
            if(count($arrData) > 0){
                for($i=0; $i < count($arrData); $i++) {
                    $html .= '<option value="'.encrypt($arrData[$i]['id']).'">'.$arrData[$i]['product'].'</option>';
                }
            }
            echo $html;
            die();
        }
        public function select_record(string $idproduct){
            if($_SESSION['permits_module']['v']){
                $idproduct = decrypt($idproduct);
                $idproduct = intval($idproduct);
                if($idproduct > 0){
                  $data = $this->model->select_record($idproduct);
                  if(empty($data)){
                    $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
                  }else{
                    $data['encrypt_id'] = encrypt($data['id']);
                    $data['encrypt_category'] = encrypt($data['categoryid']);
                    $data['encrypt_unit'] = encrypt($data['unitid']);
                    $data['encrypt_provider'] = encrypt($data['providerid']);
                    $data['url_image'] = base_style().'/uploads/products/'.$data['image'];
                    $answer = array('status' => 'success', 'data' => $data);
                  }
                }else{
                  $answer = array('status' => 'error', 'msg' => 'La información buscada, no ha sido encontrada.');
                }
                echo json_encode($answer,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function search_products(){
            if($_POST){
                $html = "";
                $search = strClean($_POST['search']);
                $arrData = $this->model->search_products($search);
                if(empty($arrData)){
                    $html .= '<li>No se encontro "'.$search.'"</li>';
                }else{
                    foreach ($arrData as $row){
                        $html .= '<li onclick="add_product('.$row['id'].',\''.$row['product'].'\',\''.$row['internal_code'].'\','.$row['sale_price'].','.$row['stock'].')">'.$row['product'].' - '.$_SESSION['businessData']['symbol'].$row['sale_price'].' - Stock:'.$row['stock'].'</li>';
                    }
                }
                echo $html;
            }
            die();
        }
        public function action(){
          if($_POST){
            if(empty($_POST['product']) || empty($_POST['listProviders']) || empty($_POST['listCategories'])){
              $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios.');
            }else{
              $id = decrypt($_POST['idproduct']);
              $id = intval($id);
              $barcode = strClean($_POST['barcode']);
              $product = strtoupper(strClean($_POST['product']));
              $model = strtoupper(strClean($_POST['model']));
              $brand = strtoupper(strClean($_POST['brand']));
              if(isset($_POST['extra'])){
                $extra = 1;
                $serie = strClean($_POST['serie']);
                $mac = strClean($_POST['mac']);
              }else{
                $extra = 0;
                $serie = "";
                $mac = "";
              }
              $description = strtoupper(strClean($_POST['description']));
              $sale_price = empty($_POST['sale_price']) ? 0 : strClean($_POST['sale_price']);
              $purchase_price = empty($_POST['purchase_price']) ? 0 : strClean($_POST['purchase_price']);
              $stock = empty($_POST['stock']) ? 0 : intval(strClean($_POST['stock']));
              $stock_alert = empty($_POST['stock_alert']) ? 0 : intval(strClean($_POST['stock_alert']));
              $categories = decrypt($_POST['listCategories']);
              $categories = intval($categories);
              $units = decrypt($_POST['listUnits']);
              $units = intval($units);
              $providers = decrypt($_POST['listProviders']);
              $providers = intval($providers);
              $datetime = date("Y-m-d H:i:s");
              $photo = $_FILES['image_product'];
              $name_photo = $photo['name'];
              $image = 'no_image.jpg';
              $save_path = 'Assets/uploads/products/';

              if($name_photo != ''){
                $ext = explode(".",$photo['name']);
                $image = 'producto_'.md5(round(microtime(true))).'.'.end($ext);
                $image_file = 'producto_'.md5(round(microtime(true)));
              }

              if($id == 0){
                $option = 1;
                $total = $this->model->returnCode();
                if($total == 0){
                  $code = "P00001";
                }else{
                  $max = $this->model->generateCode();
                  $code = "P".substr((substr($max,1)+100001),1);
                }
                if($_SESSION['permits_module']['r']){
                  $request = $this->model->create($code,$barcode,$product,$model,$brand,$extra,$serie,$mac,$description,$sale_price,$purchase_price,$stock,$stock_alert,$categories,$units,$providers,$image,$datetime);
                  if($request == "success"){
                    $idproduct = $this->model->returnProduct();
                    $desc = "COMPRA DE PRODUCTO (MEDIANTE REGISTRO)";
                    $cost_total = $stock * $purchase_price;
                    if($stock >= 1){
                      $request = $this->model->create_incomes($idproduct,$datetime,$desc,$stock,$purchase_price,$cost_total);
                    }
                  }
                }
              }else{
                $option = 2;
                if($_SESSION['permits_module']['a']){
                  $last_stock = $this->model->returnStock($id);
                  if($last_stock > $stock){
                    $idbill = 0;
                    $new_stock = $last_stock - $stock;
                    $new_cost_total = $new_stock * $purchase_price;
                    $desc = "DEVOLUCIÓN DE PRODUCTO A PROVEEDOR (MEDIANTE ACTUALIZACIÓN)";
                    $request = $this->model->create_departures($idbill,$id,$datetime,$desc,$new_stock,$purchase_price,$new_cost_total);
                  }else if($last_stock < $stock){
                    $new_stock = $stock - $last_stock;
                    $new_cost_total = $new_stock * $purchase_price;
                    $desc = "COMPRA DE PRODUCTO (MEDIANTE ACTUALIZACIÓN)";
                    $request = $this->model->create_incomes($id,$datetime,$desc,$new_stock,$purchase_price,$new_cost_total);
                  }
                  if($name_photo == ''){
                    if($_POST['current_photo'] != 'no_image.jpg'){
                      $image = $_POST['current_photo'];
                    }
                  }
                  $request = $this->model->modify($id,$barcode,$product,$model,$brand,$extra,$serie,$mac,$description,$sale_price,$purchase_price,$stock,$stock_alert,$categories,$units,$providers,$image);
                }
              }
              if($request == "success"){
                if($option == 1){
                  if(isset($photo)){
                    $up = new Upload($photo);
                    if($up->uploaded){
                      $up->file_new_name_body = $image_file;
                      $up->image_resize = true;
                      $up->image_x = 300;
                      $up->image_ratio_y = true;
                      $up->Process($save_path);
                      if($up->processed){
                        $up->clean();
                      }
                    }
                  }
                  $response = array('status' => 'success', 'msg' => 'Se ha registrado exitosamente.');
                }else{
                  if(isset($photo)){
                    $up = new Upload($photo);
                    if($up->uploaded){
                      $up->file_new_name_body = $image_file;
                      $up->image_resize = true;
                      $up->image_x = 300;
                      $up->image_ratio_y = true;
                      $up->Process($save_path);
                      if($up->processed){
                        $up->clean();
                      }
                    }
                  }
                  if($name_photo != '' && $_POST['current_photo'] != 'no_image.jpg'){
                    delete_image('products',$_POST['current_photo']);
                  }
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
              $idproduct = decrypt($_POST['idproduct']);
              $idproduct =  intval($idproduct);
              $consult = $this->model->select_record($idproduct);
              $request = $this->model->remove($idproduct);
              if($request == 'success'){
                $response = array('status' => 'success', 'msg' => 'El registro se ha eliminado.');
                if($consult['image'] != 'no_image.jpg'){
                  delete_image('products',$consult['image']);
                }
              }else if($request == 'exists'){
                  $response = array('status' => 'exists', 'msg' => 'El producto esta en uso, imposible eliminar');
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
            $file = $_FILES["import_products"]["tmp_name"];
            /* Cargamos el archivo */
            $document = IOFactory::load($file);
            /* Hoja Productos*/
            $sheet_products = $document->getSheetByName("Productos");
            $row_products = $sheet_products->getHighestDataRow();
            /* Ciclo registrar productos */
            $total_products = 0;
            for($i=2; $i <= $row_products; $i++){
                $barcode = strClean($sheet_products->getCell("A".$i));
                $product = strtoupper(strClean($sheet_products->getCell("B".$i)));
                $model = strtoupper(strClean($sheet_products->getCell("C".$i)));
                $brand = strtoupper(strClean($sheet_products->getCell("D".$i)));
                $extra_option = strtoupper(strClean($sheet_products->getCell("E".$i)));
                if($extra_option == "SI"){
                  $extra = 1;
                  $serie = strClean($sheet_products->getCell("F".$i));
                  $mac = strClean($sheet_products->getCell("G".$i));
                }else if($extra_option == "NO"){
                  $extra = 0;
                  $serie = "";
                  $mac = "";
                }else{
                  $extra = 0;
                  $serie = "";
                  $mac = "";
                }
                $desc_product = strtoupper(strClean($sheet_products->getCell("H".$i)));
                $sale_price = strClean($sheet_products->getCell("I".$i));
                $purchase_price = strClean($sheet_products->getCell("J".$i));
                $stock = strClean($sheet_products->getCell("K".$i));
                $cost_total = strClean($sheet_products->getCell("L".$i)->getCalculatedValue());
                $stock_alert = strClean($sheet_products->getCell("M".$i));
                $category = strtoupper($sheet_products->getCell("N".$i));
                $code_unit = strtoupper(strClean($sheet_products->getCell("O".$i)));
                $unit = strtoupper(strClean($sheet_products->getCell("P".$i)));
                $type_document = strClean($sheet_products->getCell("Q".$i));
                $document = strClean($sheet_products->getCell("R".$i));
                $provider = strtoupper(strClean($sheet_products->getCell("S".$i)));
                $imagen = "no_image.jpg";
                $datetime = date("Y-m-d H:i:s");
                // Obtener id de las categorias
                $existing_category = $this->model->existing_category($category);
                if(empty($existing_category)){
                   $desc_cate = "NINGUNA";
                   $request_category = $this->model->create_category($category,$desc_cate,$datetime);
                   if($request_category == "success"){
                      $idcategory = $this->model->returnCategory();
                   }
                }else{
                   $idcategory = $existing_category['id'];
                }
                // Obtener id de las unidades de medida
                $existing_unit = $this->model->existing_unit($unit);
                if(empty($existing_unit)){
                   $request_unit = $this->model->create_unit($code_unit,$unit,$datetime);
                   if($request_unit == "success"){
                      $idunit = $this->model->returnUnit();
                   }
                }else{
                   $idunit = $existing_unit['id'];
                }
                // Obtener id de los proveedores
                $existing_provider = $this->model->existing_provider($provider);
                if(empty($existing_provider)){
                   $request_provider = $this->model->create_provider($provider,$type_document,$document,$datetime);
                   if($request_provider == "success"){
                      $idprovider = $this->model->returnProvider();
                   }
                }else{
                    $idprovider = $existing_provider['id'];
                }
                // Generar codigo interno a productos
                $total = $this->model->returnCode();
                if($total == 0){
                    $code = "P00001";
                }else{
                    $max = $this->model->generateCode();
                    $code = "P".substr((substr($max,1)+100001),1);
                }
                $request = $this->model->import($code,$barcode,$product,$model,$brand,$extra,$serie,$mac,$desc_product,$sale_price,$purchase_price,$stock,$stock_alert,$idcategory,$idunit,$idprovider,$imagen,$datetime);
                if($request > 0){
                    $idproduct = $this->model->returnProduct();
                    $description = "COMPRA DE PRODUCTO (REGISTRO MASIVO)";
                    if($stock >= "1"){
                        // Ingreso de producto a almacen (entrada)
                        $this->model->create_incomes($idproduct,$datetime,$description,$stock,$purchase_price,$cost_total);
                    }
                }
                $total_products = $total_products + $request;
            }
            if($total_products >= 1){
      			$response = array('status' => 'success', 'msg' => 'La importación se realizo exitosamente.');
      		}else if($total_products == 0){
                $response = array('status' => 'warning', 'msg' => 'No se pudo importar, revise el excel en caso que realizaste mal rellenado.');
            }else{
      			$response = array("status" => 'error', "msg" => 'No se pudo realizar esta operaciòn, intentelo nuevamente.');
      		}
      		echo json_encode($response,JSON_UNESCAPED_UNICODE);
      		die();
        }
        public function export(){
            $spreadsheet = new SpreadSheet();
            $style_header = array(
                'font' => array(
                    'name'  => 'Calibri',
                    'bold'  => true,
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
            $spreadsheet->getActiveSheet()->getStyle('A1:N1')->applyFromArray($style_header);
            $center_cell = array(
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ),
            );
            $spreadsheet->getActiveSheet()->getStyle('A')->applyFromArray($center_cell);
            $spreadsheet->getActiveSheet()->getStyle('C:F')->applyFromArray($center_cell);

            $active_sheet = $spreadsheet->getActiveSheet();
            $active_sheet->setTitle("Productos");
            $active_sheet->getColumnDimension('A')->setAutoSize(true);
            $active_sheet->setCellValue('A1', 'CODIGO');
            $active_sheet->getColumnDimension('B')->setAutoSize(true);
            $active_sheet->setCellValue('B1', 'PRODUCTO');
            $active_sheet->getColumnDimension('C')->setAutoSize(true);
            $active_sheet->setCellValue('C1', 'PRECIO COMPRA');
            $active_sheet->getColumnDimension('D')->setAutoSize(true);
            $active_sheet->setCellValue('D1', 'PRECIO VENTA');
            $active_sheet->getColumnDimension('E')->setAutoSize(true);
            $active_sheet->setCellValue('E1', 'STOCK');
            $active_sheet->getColumnDimension('F')->setAutoSize(true);
            $active_sheet->setCellValue('F1', 'STOCK MIN.');
            $active_sheet->getColumnDimension('G')->setAutoSize(true);
            $active_sheet->setCellValue('G1', 'MODELO');
            $active_sheet->getColumnDimension('H')->setAutoSize(true);
            $active_sheet->setCellValue('H1', 'MARCA');
            $active_sheet->getColumnDimension('I')->setAutoSize(true);
            $active_sheet->setCellValue('I1', 'CATEGORIA');
            $active_sheet->getColumnDimension('J')->setAutoSize(true);
            $active_sheet->setCellValue('J1', 'Nº SERIE');
            $active_sheet->getColumnDimension('K')->setAutoSize(true);
            $active_sheet->setCellValue('K1', 'Nº MAC');
            $active_sheet->getColumnDimension('L')->setAutoSize(true);
            $active_sheet->setCellValue('L1', 'PRESENTACIÓN');
            $active_sheet->getColumnDimension('M')->setAutoSize(true);
            $active_sheet->setCellValue('M1', 'PROVEEDOR');
            $active_sheet->getColumnDimension('N')->setAutoSize(true);
            $active_sheet->setCellValue('N1', 'DESCRIPCIÓN');

            $data = $this->model->export();
            if (!empty($data)){
                $i = 2;
                foreach ($data as $key => $value) {
                    $active_sheet->setCellValue('A'.$i, $value['internal_code']);
                    $active_sheet->setCellValue('B'.$i, $value['product']);
                    $active_sheet->setCellValue('C'.$i, $value['purchase_price']);
                    $active_sheet->setCellValue('D'.$i, $value['sale_price']);
                    $active_sheet->setCellValue('E'.$i, $value['stock']);
                    $active_sheet->setCellValue('F'.$i, $value['stock_alert']);
                    $active_sheet->setCellValue('G'.$i, $value['model']);
                    $active_sheet->setCellValue('H'.$i, $value['brand']);
                    $active_sheet->setCellValue('I'.$i, $value['category']);
                    $active_sheet->setCellValue('J'.$i, $value['serial_number']);
                    $active_sheet->setCellValue('K'.$i, $value['mac']);
                    $active_sheet->setCellValue('L'.$i, $value['united']);
                    $active_sheet->setCellValue('M'.$i, $value['provider']);
                    $active_sheet->setCellValue('N'.$i, $value['description']);
                    $i++;
                }
            }

            $title = 'Lista de productos';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'. $title .'.xlsx"');
            header('Cache-Control: max-age=0');

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }
    }
