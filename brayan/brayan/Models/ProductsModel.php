<?php
	class ProductsModel extends Mysql{
		private $intId,$strCode,$strBarcode,$strProduct,$strModel,$strBrand,$strExtra,$strSerie,$strMac,$strSale,$strPurchase,$strStock,$strStockMin,$intCategories,$intUnits,$intPorviders;
		private $strDate,$strDescription,$strCostTotal;
		private $strCategory,$strCodeUnit,$strUnit,$strProvider,$strType,$strDocument,$strImage,$strDatetime;
		public function __construct(){
			parent::__construct();
		}
		public function list_records(){
			$sql = "SELECT p.id,p.internal_code,p.barcode,p.product,p.model,p.brand,p.serial_number,p.mac,p.sale_price,p.purchase_price,p.stock,p.stock_alert,p.registration_date,pc.category,u.united,pro.provider
			FROM products p
			JOIN product_category pc ON p.categoryid = pc.id
			JOIN unit u ON p.unitid = u.id
			JOIN providers pro ON p.providerid = pro.id
			ORDER BY p.id DESC";
			$answer = $this->select_all($sql);
			return $answer;
		}
		public function export(){
			$sql = "SELECT p.id,p.internal_code,p.barcode,p.product,p.model,p.brand,p.serial_number,p.mac,p.description,p.sale_price,p.purchase_price,p.stock,p.stock_alert,p.registration_date,pc.category,u.united,pro.provider
			FROM products p
			JOIN product_category pc ON p.categoryid = pc.id
			JOIN unit u ON p.unitid = u.id
			JOIN providers pro ON p.providerid = pro.id
			ORDER BY p.id ASC";
			$answer = $this->select_all($sql);
			return $answer;
		}
		public function search_products(string $product){
			$search = explode(" ", $product);
			$sql = "SELECT * FROM products WHERE product LIKE '%$search[0]%'";
			for ($i=0; $i < count($search); $i++){
				$sql.= " AND product LIKE '%$search[$i]%'";
			}
			$request = $this->select_all($sql);
			return $request;
		}
		public function create(string $code,string $barcode,string $product,string $model,string $brand,int $extra,string $serie,string $mac,string $description,string $sale_price,string $purchase_price,string $stock,string $stock_alert,int $categories,int $units,int $providers,string $image,string $datetime){
      $this->strCode = $code;
			$this->strBarcode = $barcode;
      $this->strProduct = $product;
			$this->strModel = $model;
			$this->strBrand = $brand;
			$this->strExtra = $extra;
			$this->strSerie = $serie;
			$this->strMac = $mac;
			$this->strDescription = $description;
			$this->strSale = $sale_price;
			$this->strPurchase = $purchase_price;
      $this->strStock = $stock;
      $this->strStockMin = $stock_alert;
			$this->intCategories = $categories;
      $this->intUnits = $units;
      $this->intPorviders = $providers;
			$this->strImage = $image;
			$this->strDatetime = $datetime;
      $answer = "";
      $sql = "SELECT *FROM products WHERE product = '{$this->strProduct}'";
      $request = $this->select_all($sql);
      if(empty($request)){
        $query = "INSERT INTO products(internal_code,barcode,product,model,brand,extra_info,serial_number,mac,description,sale_price,purchase_price,stock,stock_alert,categoryid,unitid,providerid,image,registration_date) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $data = array($this->strCode,$this->strBarcode,$this->strProduct,$this->strModel,$this->strBrand,$this->strExtra,$this->strSerie,$this->strMac,$this->strDescription,$this->strSale,$this->strPurchase,$this->strStock,$this->strStockMin,$this->intCategories,$this->intUnits,$this->intPorviders,$this->strImage,$this->strDatetime);
        $insert = $this->insert($query,$data);
        if($insert){
          $answer = 'success';
        }else{
          $answer = 'error';
        }
      }else{
        $answer = "exists";
      }
      return $answer;
    }
    public function modify(int $id,string $barcode,string $product,string $model,string $brand,int $extra,string $serie,string $mac,string $description,string $sale_price,string $purchase_price,string $stock,string $stock_alert,int $categories,int $units,int $providers,string $image){
      $this->intId = $id;
			$this->strBarcode = $barcode;
      $this->strProduct = $product;
			$this->strModel = $model;
			$this->strBrand = $brand;
			$this->strExtra = $extra;
			$this->strSerie = $serie;
			$this->strMac = $mac;
			$this->strDescription = $description;
			$this->strSale = $sale_price;
			$this->strPurchase = $purchase_price;
      $this->strStock = $stock;
      $this->strStockMin = $stock_alert;
			$this->intCategories = $categories;
      $this->intUnits = $units;
      $this->intPorviders = $providers;
			$this->strImage = $image;
      $answer = "";
      $sql = "SELECT *FROM products WHERE product = '$this->strProduct' AND id != $this->intId";
      $request = $this->select_all($sql);
      if(empty($request)){
				$query = "UPDATE products SET barcode=?,product=?,model=?,brand=?,extra_info=?,serial_number=?,mac=?,description=?,sale_price=?,purchase_price=?,stock=?,stock_alert=?,categoryid=?,unitid=?,providerid=?,image=? WHERE id = $this->intId";
				$data = array($this->strBarcode,$this->strProduct,$this->strModel,$this->strBrand,$this->strExtra,$this->strSerie,$this->strMac,$this->strDescription,$this->strSale,$this->strPurchase,$this->strStock,$this->strStockMin,$this->intCategories,$this->intUnits,$this->intPorviders,$this->strImage);
				$update = $this->update($query,$data);
				if($update){
					$answer = 'success';
				}else{
					$answer = 'error';
				}
      }else{
        $answer = "exists";
      }
      return $answer;
    }
    public function select_record(int $id){
      $this->intId = $id;
      $sql = "SELECT *FROM products WHERE id = $this->intId";
      $asnwer = $this->select($sql);
      return $asnwer;
    }
		public function view_detail(int $id){
			$request = array();
			$sql_product = "SELECT p.id,p.internal_code,p.barcode,p.product,p.model,p.brand,p.serial_number,p.mac,p.sale_price,p.purchase_price,p.stock,p.stock_alert,p.registration_date,p.image,p.description,pc.category,u.united,pro.provider
			FROM products p
			JOIN product_category pc ON p.categoryid = pc.id
			JOIN unit u ON p.unitid = u.id
			JOIN providers pro ON p.providerid = pro.id WHERE p.id = $id";
			$request_product = $this->select($sql_product);
			if(!empty($request_product)){
				$idproduct = $request_product['id'];
				$sql_detail = "SELECT date,type,price,quantity,description,total FROM
				((SELECT e.income_date AS date ,'ENTRADA' AS type,e.description AS description,e.unit_price AS price,e.quantity_income AS quantity,e.total_cost AS total
				FROM income e WHERE e.productid = $id)
				UNION ALL
				(SELECT s.departure_date AS date,'SALIDA' AS type,s.description AS description,s.unit_price AS price,s.quantity_departures AS quantity,s.total_cost AS total
				FROM departures s WHERE s.productid = $id))t2 ORDER BY date DESC";
				$request_detail = $this->select_all($sql_detail);

				$sql_bills = "SELECT CONCAT_WS(' ', c.names, c.surnames) AS client,b.correlative,b.date_issue,b.expiration_date,b.billed_month,b.discount,b.subtotal,b.total,b.type,b.sales_method,b.remaining_amount,b.amount_paid,b.state,vs.serie,v.voucher
				FROM bills b
				JOIN clients c ON b.clientid = c.id
				JOIN vouchers v ON b.voucherid = v.id
				JOIN voucher_series vs ON b.serieid = vs.id
				JOIN detail_bills db ON b.id = db.billid
				JOIN products p ON db.serproid = p.id
				WHERE db.serproid = $idproduct AND db.type = 1 ORDER BY b.id DESC";
				$request_bills = $this->select_all($sql_bills);
				$request = array('product' => $request_product,'bills' => $request_bills,'a' => $request_detail);
			}
			return $request;
		}
		public function list_products(){
			$sql = "SELECT *FROM products";
			$request = $this->select_all($sql);
			return $request;
		}
		public function returnProduct(){
			$sql = "SELECT MAX(id) AS id FROM products";
			$answer = $this->select($sql);
			$contract = $answer['id'];
			return $contract;
		}
		public function returnStock(int $id){
			$this->intId = $id;
			$sql = "SELECT stock FROM products WHERE id = $this->intId";
			$answer = $this->select($sql);
			$stock = $answer['stock'];
			return $stock;
		}
		public function returnCode(){
			$sql = "SELECT COUNT(internal_code) AS code FROM products";
			$answer = $this->select($sql);
			$code = $answer['code'];
			return $code;
		}
		public function generateCode(){
			$sql = "SELECT MAX(internal_code) AS code FROM products";
			$answer = $this->select($sql);
			$code = $answer['code'];
			return $code;
		}
		public function remove(int $id){
				$this->intId = $id;
				$answer = "";
				$sql = "SELECT *FROM detail_bills WHERE serproid = $this->intId AND type=1";
				$request = $this->select_all($sql);
				if(empty($request)){
						$sql = "DELETE FROM products WHERE id = $this->intId";
						$delete = $this->delete($sql);
						if($delete){
								$answer = 'success';
						}else{
								$answer = 'error';
						}
				}else{
						$answer = 'exists';
				}
				return $answer;
		}
		public function create_incomes(int $id,string $date,string $description,string $quantity,string $price,string $total){
			$this->intId = $id;
			$this->strDate = $date;
			$this->strDescription = $description;
			$this->strStock = $quantity;
			$this->strPurchase = $price;
			$this->strCostTotalEn = $total;
			$answer = "";
			$query = "INSERT INTO income(productid,income_date,description,quantity_income,unit_price,total_cost) VALUES(?,?,?,?,?,?)";
			$data = array($this->intId,$this->strDate,$this->strDescription,$this->strStock,$this->strPurchase,$this->strCostTotalEn);
			$insert = $this->insert($query,$data);
			if($insert){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function create_departures(int $idbill,int $id,string $date,string $description,string $quantity,string $precio,string $total){
			$this->intBill = $idbill;
			$this->intId = $id;
			$this->strDate = $date;
			$this->strDescription = $description;
			$this->strStock = $quantity;
			$this->strSale = $precio;
			$this->strCostTotal = $total;
			$answer = "";
			$query = "INSERT INTO departures(billid,productid,departure_date,description,quantity_departures,unit_price,total_cost) VALUES(?,?,?,?,?,?,?)";
			$data = array($this->intBill,$this->intId,$this->strDate,$this->strDescription,$this->strStock,$this->strSale,$this->strCostTotal);
			$insert = $this->insert($query,$data);
			if($insert){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function existing_category(string $category){
			$this->strCategory = $category;
			$query = "SELECT * FROM product_category WHERE category = '$this->strCategory'";
			$answer = $this->select($query);
			return $answer;
		}
		public function create_category(string $category,string $description,string $datetime){
				$this->strCategory = $category;
				$this->strDescription = $description;
				$this->strDatetime = $datetime;
				$answer = "";
				$query = "INSERT INTO product_category(category,description,registration_date) VALUES(?,?,?)";
				$data = array($this->strCategory,$this->strDescription,$this->strDatetime);
				$insert = $this->insert($query,$data);
				if($insert){
						$answer = 'success';
				}else{
						$answer = 'error';
				}
				return $answer;
		}
		public function returnCategory(){
			$sql = "SELECT MAX(id) AS id FROM product_category";
			$answer = $this->select($sql);
			$id = $answer['id'];
			return $id;
		}
		public function existing_unit(string $unit){
			$this->strUnit = $unit;
			$query = "SELECT * FROM unit WHERE united = '$this->strUnit'";
			$answer = $this->select($query);
			return $answer;
		}
		public function create_unit(string $code,string $unit,string $datetime){
				$this->strCodeUnit = $code;
				$this->strUnit = $unit;
				$this->strDatetime = $datetime;
				$answer = "";
				$query = "INSERT INTO unit(code,united,registration_date) VALUES(?,?,?)";
				$data = array($this->strCodeUnit,$this->strUnit,$this->strDatetime);
				$insert = $this->insert($query,$data);
				if($insert){
						$answer = 'success';
				}else{
						$answer = 'error';
				}
				return $answer;
		}
		public function returnUnit(){
			$sql = "SELECT MAX(id) AS id FROM unit";
			$answer = $this->select($sql);
			$id = $answer['id'];
			return $id;
		}
		public function existing_provider(string $provider){
			$this->strProvider = $provider;
			$query = "SELECT * FROM providers WHERE provider = '$this->strProvider'";
			$answer = $this->select($query);
			return $answer;
		}
		public function create_provider(string $provider,string $type,string $document,string $datetime){
				$this->strProvider = $provider;
				$this->strType = $type;
				$this->strDocument = $document;
				$this->strDatetime = $datetime;
				$answer = "";
				$query = "INSERT INTO providers(provider,documentid,document,registration_date) VALUES(?,?,?,?)";
				$data = array($this->strProvider,$this->strType,$this->strDocument,$this->strDatetime);
				$insert = $this->insert($query,$data);
				if($insert){
						$answer = 'success';
				}else{
						$answer = 'error';
				}
				return $answer;
		}
		public function returnProvider(){
			$sql = "SELECT MAX(id) AS id FROM providers";
			$answer = $this->select($sql);
			$id = $answer['id'];
			return $id;
		}
		public function import(string $code,string $barcode,string $product,string $model,string $brand,int $extra,string $serie,string $mac,string $description,string $sale_price,string $purchase_price,string $stock,string $stock_alert,int $category,int $unit,int $provider,string $image,string $datetime){
			$this->strCode = $code;
			$this->strBarcode = $barcode;
			$this->strProduct = $product;
			$this->strModel = $model;
			$this->strBrand = $brand;
			$this->strExtra = $extra;
			$this->strSerie = $serie;
			$this->strMac = $mac;
			$this->strDescription = $description;
			$this->strSale = $sale_price;
			$this->strPurchase = $purchase_price;
			$this->strStock = $stock;
			$this->strStockMin = $stock_alert;
			$this->intCategories = $category;
			$this->intUnits = $unit;
			$this->intPorviders = $provider;
			$this->strImage = $image;
			$this->strDatetime = $datetime;
			$answer = 0;
			$sql = "SELECT *FROM products WHERE product = '{$this->strProduct}'";
		  $request = $this->select_all($sql);
			if(empty($request)){
				$query = "INSERT INTO products(internal_code,barcode,product,model,brand,extra_info,serial_number,mac,description,sale_price,purchase_price,stock,stock_alert,categoryid,unitid,providerid,image,registration_date) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				$data = array($this->strCode,$this->strBarcode,$this->strProduct,$this->strModel,$this->strBrand,$this->strExtra,$this->strSerie,$this->strMac,$this->strDescription,$this->strSale,$this->strPurchase,$this->strStock,$this->strStockMin,$this->intCategories,$this->intUnits,$this->intPorviders,$this->strImage,$this->strDatetime);
				$insert = $this->insert($query,$data);
				if($insert){
					$answer = $insert;
				}else{
					$answer = 0;
				}
			}else{
				$answer = 0;
			}
			return $answer;
		}
  }
