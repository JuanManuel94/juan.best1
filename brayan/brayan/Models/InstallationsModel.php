<?php
	class InstallationsModel extends Mysql{
		private $intId,$intContract,$intClient,$intFacility,$intUser,$intState,$intTechnical,$strObservation;
		private $intProduct,$intQuantity,$strPricePro,$strTotalPro,$strConditions,$strDeail,$strPrice;
		private $strDate,$strDescription,$strImagen,$strLatitude,$strLongitude,$strAddress,$strState;
		private $intBusiness,$intVoucher,$intSerie,$strCode,$intBill,$strTotal,$strType,$strTypeD,$strDatetime,$intTypeId,$intType,$intIdTool,$strOpening,$strClosing,$strSubtotal,$strDiscount,$strIssue,$strExpiration,$strMonth;
		public function __construct(){
			parent::__construct();
		}
		public function list_records(int $state){
			if($state == 0){
				$sql = "SELECT i.id,i.clientid,i.attention_date,i.opening_date, i.closing_date,i.cost,i.technical,i.detail,i.state,CONCAT_WS(' ', c.names, c.surnames) AS client,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud,c.document,d.document AS name_doc,u.names AS user,i.clientid
	      FROM facility i
	      JOIN clients c ON i.clientid = c.id
		  JOIN document_type d ON c.documentid = d.id
	      JOIN users u ON i.userid = u.id
	      WHERE i.state != 0 ORDER BY i.id DESC";
			}else{
				$sql = "SELECT i.id,i.clientid,i.attention_date,i.opening_date, i.closing_date,i.cost,i.technical,i.detail,i.state,CONCAT_WS(' ', c.names, c.surnames) AS client,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud,c.document,d.document AS name_doc,u.names AS user,i.clientid
	      FROM facility i
	      JOIN clients c ON i.clientid = c.id
		  JOIN document_type d ON c.documentid = d.id
	      JOIN users u ON i.userid = u.id
	      WHERE i.state = $state ORDER BY i.id DESC";
			}
			$answer = $this->select_all($sql);
			return $answer;
		}
		public function select_record(int $id){
			$this->intId = $id;
			$sql = "SELECT f.id,f.clientid,f.technical,f.attention_date,f.opening_date,f.closing_date,f.cost,f.detail,f.state,c.names,c.surnames,c.document,c.mobile,c.mobile_optional,c.email,c.address,c.reference
			FROM facility f
			JOIN clients c ON f.clientid = c.id
			WHERE f.id = $this->intId";
			$asnwer = $this->select($sql);
			return $asnwer;
		}
		public function create(int $client,int $user,int $technical,string $attention,string $price,string $detail,string $datetime){
			$this->intClient = $client;
			$this->intUser = $user;
			$this->intTechnical = $technical;
			$this->strDate = $attention;
			$this->strPrice = $price;
			$this->strDeail = $detail;
			$this->strDatetime = $datetime;
			$answer = "";
			$sql = "SELECT *FROM facility WHERE clientid = '{$this->intClient}' AND state != 5";
			$request = $this->select_all($sql);
			if(empty($request)){
				$query = "INSERT INTO facility(clientid,userid,technical,attention_date,cost,detail,registration_date) VALUES(?,?,?,?,?,?,?)";
				$data = array($this->intClient,$this->intUser,$this->intTechnical,$this->strDate,$this->strPrice,$this->strDeail,$this->strDatetime);
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
		public function modify(int $id,int $technical,string $attention,string $price,string $detail){
			$this->intId = $id;
			$this->intTechnical = $technical;
			$this->strDate = $attention;
			$this->strPrice = $price;
			$this->strDeail = $detail;
			$answer = "";
			$query = "UPDATE facility SET technical=?,attention_date=?,cost=?,detail=? WHERE id = $this->intId";
			$data = array($this->intTechnical,$this->strDate,$this->strPrice,$this->strDeail);
			$update = $this->update($query,$data);
			if($update){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function open_facility(int $id,string $datetime,int $state){
			$this->intId = $id;
			$this->strDatetime = $datetime;
			$this->strState = $state;
			$answer = "";
			$query = "UPDATE facility SET opening_date = ?,state = ? WHERE id = $this->intId";
			$data = array($this->strDatetime,$this->strState);
			$update = $this->update($query,$data);
			if($update){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function close_facility(int $id,string $datetime,int $state){
      $this->intId = $id;
      $this->strDatetime = $datetime;
      $this->intState = $state;
      $answer = "";
      $query = "UPDATE facility SET closing_date = ?,state = ? WHERE id = $this->intId";
      $data = array($this->strDatetime,$this->intState);
      $update = $this->update($query,$data);
      if($update){
        $answer = 'success';
      }else{
        $answer = 'error';
      }
      return $answer;
    }
		public function complete_installation(int $id,int $user,string $opening_date,string $closing_date,string $observation,int $state, string $red_type){
			$this->intId = $id;
			$this->intUser = $user;
			$this->strOpening = $opening_date;
      $this->strClosing = $closing_date;
      $this->strObservation = $observation;
			$this->strState = $state;
			$this->strRedType = $red_type;
			$answer = "";
      $query = "INSERT INTO detail_facility(facilityid,technicalid,opening_date,closing_date,comment,state,red_type) VALUES(?,?,?,?,?,?,?)";
      $data = array($this->intId,$this->intUser,$this->strOpening,$this->strClosing,$this->strObservation,$this->strState,$this->strRedType);
      $insert = $this->insert($query,$data);
      if($insert){
        $answer = 'success';
      }else{
        $answer = 'error';
      }
			return $answer;
		}
		public function view_installation(int $facility){
			$request = array();
			$sql_facility = "SELECT f.id,CONCAT_WS(' ', c.names, c.surnames) AS client,dt.document AS type_doc,c.document,c.mobile,c.mobile_optional,c.address,c.reference,c.email,f.clientid,f.attention_date,f.registration_date,f.technical,f.cost,f.detail,f.state,CONCAT_WS(' ', u.names, u.surnames) AS user,u.image AS user_image
			FROM facility f
			JOIN clients c ON f.clientid = c.id
			JOIN users u ON f.userid = u.id
			JOIN document_type dt ON c.documentid = dt.id
			WHERE f.id = $facility";
			$request_facility = $this->select($sql_facility);
			if(!empty($request_facility)){
				$idtechnical = $request_facility['technical'];
				$idclient = $request_facility['clientid'];

				$sql_contract = "SELECT *FROM contracts WHERE clientid = $idclient";
				$request_contract = $this->select($sql_contract);
				$idcontract = $request_contract['id'];

				$sql_services = "SELECT dc.id,s.service
				FROM detail_contracts dc
				JOIN services s ON dc.serviceid = s.id
				WHERE dc.contractid = $idcontract";
				$request_services = $this->select_all($sql_services);

				$sql_technical = "SELECT *FROM users WHERE id = $idtechnical";
				$request_technical = $this->select($sql_technical);

				$sql_tools = "SELECT t.id,t.serie,t.mac,t.facilityid,t.productid,t.quantity,t.price,t.total,t.product_condition,p.product
				FROM tools t
				JOIN products p ON t.productid = p.id
				WHERE t.facilityid = $facility";
				$request_tools = $this->select_all($sql_tools);

				$sql_detail = "SELECT ts.id,ts.facilityid,ts.technicalid,ts.opening_date,ts.closing_date,ts.comment,ts.state,u.names,u.image FROM detail_facility ts JOIN users u ON ts.technicalid = u.id WHERE ts.facilityid = $facility ORDER BY ts.id ASC";
				$request_detail = $this->select_all($sql_detail);

				$sql_images = "SELECT g.type,g.registration_date,g.image,u.names FROM gallery_images g JOIN users u ON g.userid = u.id WHERE g.typeid = $facility AND g.type = 1";
				$request_images = $this->select_all($sql_images);

				$request = array('facility' => $request_facility,'services' => $request_services,'tools' => $request_tools,'technical' => $request_technical,'detail' => $request_detail,'images' => $request_images);
			}
			return $request;
		}
		public function cancel(int $id){
      $this->intId = $id;
      $sql = "UPDATE facility SET state = ? WHERE id = $this->intId";
      $arrData = array(5);
      $request = $this->update($sql,$arrData);
      return $request;
    }
		public function select_tools(int $tool){
			$this->intIdTool = $tool;
			$sql = "SELECT *FROM tools WHERE id = $this->intIdTool";
			$asnwer = $this->select($sql);
			return $asnwer;
		}
		public function select_product(int $product){
			$this->intProduct = $product;
			$sql = "SELECT *FROM products WHERE id = $this->intProduct";
			$asnwer = $this->select($sql);
			return $asnwer;
		}
		public function list_materials(int $id){
			$this->intId = $id;
			$sql = "SELECT t.id,t.facilityid,t.productid,t.serie,t.mac,t.quantity,t.price,t.total,t.product_condition,p.product,pc.category
			FROM tools t
			JOIN products p ON t.productid = p.id
			JOIN product_category pc ON p.categoryid = pc.id
			WHERE t.facilityid = $this->intId ORDER BY t.id DESC";
			$answer = $this->select_all($sql);
			return $answer;
		}
		public function contract_services(int $contract){
			$this->intContract = $contract;
			$sql = "SELECT dc.serviceid,dc.price,i.service FROM detail_contracts dc
			JOIN services i ON dc.serviceid = i.id
			WHERE contractid = $this->intContract";
			$answer = $this->select_all($sql);
			return $answer;
    }
		public function consult_loan_facility(int $facility,int $product){
			$this->intId = $facility;
			$this->intProduct = $product;
			$sql = "SELECT *FROM tools WHERE facilityid = $this->intId AND productid = $this->intProduct AND product_condition = 'PRESTAMO'";
			$answer = $this->select_all($sql);
			return $answer;
		}
		public function create_material(int $facility,int $product,int $quantity,string $price,string $total,string $conditions, string $serie, string $mac){
			$this->intId = $facility;
			$this->intProduct = $product;
			$this->intQuantity = $quantity;
			$this->strPricePro = $price;
			$this->strTotalPro = $total;
			$this->strConditions = $conditions;
			$answer = "";
			$sql = "SELECT *FROM tools WHERE facilityid = $this->intId AND productid = $this->intProduct AND mac = '{$mac}' AND serie = '{$serie}'";
			$request = $this->select_all($sql);
			if(empty($request)){
				$query = "INSERT INTO tools(facilityid,productid,quantity,price,total,product_condition,serie,mac) VALUES (?,?,?,?,?,?,?,?)";
				$data = array($this->intId,$this->intProduct,$this->intQuantity,$this->strPricePro,$this->strTotalPro,$this->strConditions, $serie, $mac);
				$insert = $this->insert($query,$data);
				if($insert){
					$answer = 'success';
				}else{
					$answer = 'error';
				}
			}else{
				if($this->strConditions == "PRESTAMO"){
					$query = "UPDATE tools SET quantity = quantity + ? WHERE facilityid = $this->intId AND productid = $this->intProduct";
					$data = array($this->intQuantity);
				}else{
					$sql_lendlease = "SELECT *FROM tools WHERE facilityid = $this->intId AND productid = $this->intProduct AND product_condition = 'PRESTAMO'";
					$lendlease = $this->select_all($sql_lendlease);
					if(!empty($lendlease)){
						$query = "UPDATE tools SET quantity = quantity + ? WHERE facilityid = $this->intId AND productid = $this->intProduct";
						$data = array($this->intQuantity);
					}else {
						$query = "UPDATE tools SET quantity = quantity + ?,total = total + ? WHERE facilityid = $this->intId AND productid = $this->intProduct";
						$data = array($this->intQuantity,$this->strTotalPro);
					}
				}
				$update = $this->update($query,$data);
				if($update){
					$answer = 'success';
				}else{
					$answer = 'error';
				}
			}
			return $answer;
		}
		public function create_departures(int $product,string $date,string $description,string $quantity,string $price,string $total){
			$this->intProduct = $product;
			$this->strDate = $date;
			$this->strDescription = $description;
			$this->intQuantity = $quantity;
			$this->strPricePro = $price;
			$this->strTotalPro = $total;
			$answer = "";
			$query = "INSERT INTO departures(billid,productid,departure_date,description,quantity_departures,unit_price,total_cost) VALUES(?,?,?,?,?,?,?)";
			$data = array(0,$this->intProduct,$this->strDate,$this->strDescription,$this->intQuantity,$this->strPricePro,$this->strTotalPro);
			$insert = $this->insert($query,$data);
			if($insert){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function create_incomes(int $product,string $date,string $description,int $quantity,string $price,string $total){
			$this->intProduct = $product;
			$this->strDate = $date;
			$this->strDescription = $description;
			$this->intQuantity = $quantity;
			$this->strPricePro = $price;
			$this->strTotalPro = $total;
			$answer = "";
			$query = "INSERT INTO income(productid,income_date,description,quantity_income,unit_price,total_cost) VALUES(?,?,?,?,?,?)";
			$data = array($this->intProduct,$this->strDate,$this->strDescription,$this->intQuantity,$this->strPricePro,$this->strTotalPro);
			$insert = $this->insert($query,$data);
			if($insert){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function subtract_stock(int $product,int $quantity){
			$this->intProduct = $product;
			$this->intQuantity = $quantity;
			$answer = "";
			$query = "UPDATE products SET stock = stock - ? WHERE id = $this->intProduct";
			$data = array($this->intQuantity);
			$update = $this->update($query,$data);
			if($update){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function increase_stock(int $product,int $quantity){
			$this->intProduct = $product;
			$this->intQuantity = $quantity;
			$answer = "";
			$query = "UPDATE products SET stock = stock + ? WHERE id = $this->intProduct";
			$data = array($this->intQuantity);
			$update = $this->update($query,$data);
			if($update){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function remove_material(int $tool){
			$this->intIdTool = $tool;
			$answer = "";
			$sql = "DELETE FROM tools WHERE id = $this->intIdTool";
			$delete = $this->delete($sql);
			if($delete){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function select_contract(int $client){
				$this->intClient = $client;
				$sql = "SELECT * FROM contracts WHERE clientid = $this->intClient";
				$answer = $this->select($sql);
				return $answer;
		}
		public function see_technical(int $user){
      $this->intUser = $user;
      $sql = "SELECT names AS technical FROM users WHERE id = $this->intUser";
      $answer = $this->select($sql);
      $technical = $answer['technical'];
      return $technical;
    }
		public function list_technical(){
        $where = "";
        if($_SESSION['userData']['profileid'] == ADMINISTRATOR){
            $where = " AND profileid IN(1,2)";
        }else{
            $where = " AND profileid = 2";
        }
        $sql = "SELECT *FROM users WHERE state = 1".$where;
        $request = $this->select_all($sql);
        return $request;
    }
		public function list_clients(){
			$sql = "SELECT ct.clientid,c.names,c.surnames
			FROM contracts ct
			JOIN clients c ON ct.clientid = c.id
			WHERE ct.state != 0 AND ct.clientid IN (SELECT id FROM clients WHERE state != 0)";
			$answer = $this->select_all($sql);
			return $answer;
	  }
		public function reassign_technical(int $id,int $technical){
      $this->intId = $id;
      $this->intTechnical = $technical;
      $answer = "";
      $query = "UPDATE facility SET technical = ? WHERE id = $this->intId";
      $data = array($this->intTechnical);
      $update = $this->update($query,$data);
      if($update){
        $answer = 'success';
      }else{
        $answer = 'error';
      }
      return $answer;
    }
		public function select_client(int $client){
			$this->intClient = $client;
			$sql = "SELECT * FROM clients WHERE id = $this->intClient";
			$answer = $this->select($sql);
			return $answer;
		}
		public function modify_client(int $client,string $latitude,string $longitude){
			$this->intClient = $client;
			$this->strLatitude = $latitude;
			$this->strLongitude = $longitude;
			$answer = "";
			$query = "UPDATE clients SET latitud = ?,longitud = ? WHERE id = $this->intClient";
			$data = array($this->strLatitude,$this->strLongitude);
			$update = $this->update($query,$data);
			if($update){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function modify_contract(int $contract){
			$this->intContract = $contract;
			$answer = "";
			$query = "UPDATE contracts SET state = ? WHERE id = $this->intContract";
			$data = array(2);
			$update = $this->update($query,$data);
			if($update){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function returnSerie(int $voucher){
			$this->intVoucher = $voucher;
			$sql = "SELECT MAX(id) AS serie FROM voucher_series WHERE voucherid = $this->intVoucher";
			$answer = $this->select($sql);
			$serie = $answer['serie'];
			return $serie;
		}
		public function returnCodeBill(){
						$sql = "SELECT COUNT(internal_code) AS code FROM bills";
			$answer = $this->select($sql);
			$code = $answer['code'];
						return $code;
				}
				public function generateCodeBill(){
						$sql = "SELECT MAX(internal_code) AS code FROM bills";
						$answer = $this->select($sql);
			$code = $answer['code'];
						return $code;
				}
		public function returnCorrelative(int $voucher,int $serie){
			$this->intVoucher = $voucher;
			$this->intSerie = $serie;
			$sql = "SELECT MAX(correlative) as correlative FROM bills WHERE serieid = $this->intSerie AND voucherid = $this->intVoucher";
			$answer = $this->select($sql);
			$correlative = $answer['correlative'];
						return $correlative;
		}
		public function returnUsed(int $voucher,int $serie){
			$this->intVoucher = $voucher;
			$this->intSerie = $serie;
			$sql = "SELECT until - available + 1 AS used FROM voucher_series WHERE id = $this->intSerie AND voucherid = $this->intVoucher";
			$answer = $this->select($sql);
			$used = $answer['used'];
			return $used;
		}
		public function returnBill(){
			$sql = "SELECT MAX(id) AS id FROM bills";
			$answer = $this->select($sql);
			$contract = $answer['id'];
			return $contract;
		}
		public function create_bill(int $user,int $client,int $voucher,int $serie,string $code,string $correlative,string $issue,string $expiration,string $billed_month,string $subtotal,string $discount,string $total,int $type){
			$this->intUser = $user;
			$this->intClient = $client;
			$this->intVoucher = $voucher;
			$this->intSerie = $serie;
			$this->strCode = $code;
			$this->strCorrelative = $correlative;
			$this->strIssue = $issue;
			$this->strExpiration = $expiration;
			$this->strMonth = $billed_month;
			$this->strSubtotal = $subtotal;
			$this->strDiscount = $discount;
			$this->strTotal = $total;
			$this->strType = $type;
			$answer = "";
			$query = "INSERT INTO bills(userid,clientid,voucherid,serieid,internal_code,correlative,date_issue,expiration_date,billed_month,subtotal,discount,total,remaining_amount,type,sales_method) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$data = array($this->intUser,$this->intClient,$this->intVoucher,$this->intSerie,$this->strCode,$this->strCorrelative,$this->strIssue,$this->strExpiration,$this->strMonth,$this->strSubtotal,$this->strDiscount,$this->strTotal,$this->strTotal,$this->strType,2);
			$insert = $this->insert($query,$data);
			if($insert){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function create_datailBill(int $bill,int $type,int $product,string $description,string $quantity,string $price,string $total){
			$this->intBill = $bill;
			$this->strTypeD = $type;
			$this->intProduct = $product;
			$this->strDescription = $description;
			$this->intQuantity = $quantity;
			$this->strPricePro = $price;
			$this->strTotalPro = $total;
			$answer = "";
			$query = "INSERT INTO detail_bills(billid,type,serproid,description,quantity,price,total) VALUES(?,?,?,?,?,?,?)";
			$data = array($this->intBill,$this->strTypeD,$this->intProduct,$this->strDescription,$this->intQuantity,$this->strPricePro,$this->strTotalPro);
			$insert = $this->insert($query,$data);
			if($insert){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function modify_available(int $voucher,int $serie){
			$this->intVoucher = $voucher;
			$this->intSerie = $serie;
			$answer = "";
			$query = "UPDATE voucher_series SET available = available - ? WHERE id = $this->intSerie AND voucherid = $this->intVoucher";
			$data = array(1);
			$update = $this->update($query,$data);
			if($update){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function number_images(int $facility){
				$this->intId = $facility;
				$sql = "SELECT COUNT(*) AS total FROM gallery_images WHERE typeid = $this->intId AND type = 1";
				$answer = $this->select($sql);
				$total = $answer['total'];
				return $total;
		}
		public function register_image(int $client,int $user,int $type,int $typeid,string $datetime,string $imagen){
			$this->intClient = $client;
			$this->intUser = $user;
			$this->intType = $type;
			$this->intTypeId = $typeid;
			$this->strDatetime = $datetime;
			$this->strImagen = $imagen;
			$answer = "";
			$query = "INSERT INTO gallery_images(clientid,userid,type,typeid,registration_date,image) VALUES(?,?,?,?,?,?)";
			$data = array($this->intClient,$this->intUser,$this->intType,$this->intTypeId,$this->strDatetime,$this->strImagen);
			$insert = $this->insert($query,$data);
			if($insert){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function remove_image(int $facility, string $imagen){
			$this->intFacility = $facility;
			$this->strImagen = $imagen;
			$answer = "";
			$query  = "DELETE FROM gallery_images WHERE typeid = $this->intFacility AND image = '{$this->strImagen}'";
			$delete = $this->delete($query);
			if($delete){
				$answer = 'success';
			}else{
				$answer = 'error';
			}
			return $answer;
		}
		public function show_images(int $facility){
			$this->intFacility = $facility;
			$sql = "SELECT g.type,g.registration_date,g.image,u.names FROM gallery_images g JOIN users u ON g.userid = u.id WHERE g.typeid = $this->intFacility AND g.type = 1";
			$asnwer = $this->select_all($sql);
			return $asnwer;
		}
	}
