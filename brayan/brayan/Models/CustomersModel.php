<?php
class CustomersModel extends Mysql
{
  /* VARIABLES GENERALES*/
  private $strDescription, $strDate, $strDatetime, $strTotal, $intIdDetail, $strPayment, $strPriceSer, $intStateDetail, $intState;
  /* VARIABLES NEGOCIO */
  private $intBusiness;
  /* VARIABLES CONTRATOS */
  private $intId, $strCode, $intPayDay, $intInvoice, $intDayGrace, $intDiscount, $strPrice, $intMonth, $strSuspension, $strReconnection, $strFinish, $intZone, $intWallet, $intVisit;
  /* VARIABLES FACTURAS */
  private $intBill, $strCodeBill, $intProduct, $strQuantity, $intVoucher, $intSerie, $strType, $strCorrelative, $strIssue, $strExpiration, $strMonth, $strSubtotal, $strDiscount, $intMethod, $strObservation, $strSubscriber, $strTotalPaid, $strRemaining;
  /* VARIABLES PAGOS */
  private $intPayment, $strCodePayment, $strTypePayment, $strAccount, $strComment, $strBalance, $strBank;
  /* VARIABLES TICKETS */
  private $intTicket, $intTypeId, $intIncidents, $strPriority, $strAttention, $strOpening, $strClosing;
  /* VARIABLES SERVICIOS*/
  private $intService;
  /* VARIABLES CLIENTES */
  private $intClient, $strNames, $strSurnames, $intType, $strDocument, $strMobile, $strMobileOp, $strEmail, $strAddess, $strReference, $strNote, $strLatitud, $strLongitud;
  /* VARIABLS USUARIOS */
  private $intUser;
  /* VARIABLS INSTALACION */
  private $intTechnical, $strInsDate, $strInsHour, $strInsPrice, $strDetail, $strService, $strImagen, $strUtil, $strCodeServ, $strTypeServ, $strRise, $strPriceServ, $intFacility, $strRedType, $strIp;
  public function __construct()
  {
    parent::__construct("clients");
  }

  function checkTicketNumber($ticket_number)
  {
    $sql = "SELECT * FROM payments WHERE ticket_number = '$ticket_number'";
    $exists = $this->select($sql);
    return $exists;
  }

  /* MODULO CLIENTES Y CONTRATOS */
  public function list_records($filters = [])
  {
    // numero de duda
    $orderDeuda = isset($filters['orderDeuda']) ? $filters['orderDeuda'] : "";
    $orderPayday = isset($filters['orderPayday']) ? $filters['orderPayday'] : "";
    $deudaCounter = $this->createQueryBuilder()
      ->select("COUNT(*) AS total")
      ->from("bills b")
      ->where("b.clientid = cl.id")
      ->andWhere("state NOT IN(1,4)")
      ->andWhere("id NOT IN(SELECT billid FROM payments WHERE state = 1)")
      ->getSql();
    // query principal
    $queryBuilder = $this->createQueryBuilder()
      ->select(implode(",", [
        "c.id, c.internal_code, c.clientid, c.payday, c.create_invoice",
        "c.days_grace, c.discount, c.discount_price, c.months_discount",
        "c.contract_date, c.suspension_date, c.finish_date, c.state",
        "CONCAT_WS(' ', cl.names, cl.surnames) AS client, cl.document",
        "d.document AS name_doc, cl.latitud, cl.longitud, cl.email",
        "cl.mobile, cl.mobile_optional, cl.address, cl.reference",
        "cl.net_name, cl.net_password, cl.net_ip"
      ]))
      ->from("contracts c")
      ->innerJoin("clients cl", "c.clientid = cl.id")
      ->innerJoin("document_type d", "cl.documentid = d.id")
      ->leftJoin("zonas z", "z.id = cl.zonaid");
    // filter state
    if (!$filters['state']) {
      $queryBuilder->andWhere("c.state != 0");
    } else {
      $condition = $filters['state'];
      $queryBuilder->andWhere("c.state = {$condition}");
    }
    // filter paydays
    if (isset($filters['paydayStart']) && $filters['paydayStart']) {
      $condition = $filters['paydayStart'];
      $queryBuilder->andWhere("c.payday >= {$condition}");
    }

    if (isset($filters['paydayOver']) && $filters['paydayOver']) {
      $condition = $filters['paydayOver'];
      $queryBuilder->andWhere("c.payday <= {$condition}");
    }

    // order payday
    if (in_array($orderPayday, ["ASC", "DESC"])) {
      $queryBuilder->orderBy("c.payday", $orderDeuda);
    }

    // order deuda
    if (in_array($orderDeuda, ["ASC", "DESC"])) {
      $queryBuilder
        ->andWhere("({$deudaCounter}) > 0")
        ->orderBy("({$deudaCounter})", $orderDeuda);
    } else {
      $queryBuilder->addOrderBy("c.id", "DESC");
    }

    // response
    return $queryBuilder->getMany();
  }

  public function saveClient($data = [])
  {
    try {
      $columns = [
        "names",
        "surnames",
        "documentid",
        "document",
        "mobile",
        "mobile_optional",
        "zonaid",
        "email",
        "address",
        "reference",
        "note",
        "nap_cliente_id",
        "ap_cliente_id"
      ];
      $clientId = $this->insertObject($columns, $data);
      if ($clientId) {
        return "success";
      } else {
        return "error";
      }
    } catch (\Throwable $th) {
      echo $th->getMessage();
      return "error";
    }
  }

  public function editClient(int $id, $data = [])
  {
    $client = $this->createQueryBuilder()
      ->where("id = {$id}")
      ->getOne();
    if (!$client)
      return null;
    $isUpdated = $this->createQueryBuilder()
      ->update()
      ->where("id = {$id}")
      ->set($data)
      ->execute();
    return $isUpdated ? "success" : "error";
  }

  private function query_info()
  {
    return $this->createQueryBuilder()
      ->from("clients cl")
      ->innerJoin("contracts c", "c.clientid = cl.id")
      ->innerJoin("document_type d", "cl.documentid = d.id")
      ->innerJoin("bills b", "b.clientid = cl.id AND b.state IN (2, 3)")
      ->where("c.state IN (2, 3, 4)")
      ->groupBy("c.id,c.internal_code,c.clientid,c.payday,c.days_grace,c.contract_date,c.suspension_date,c.finish_date,c.state,cl.names,cl.surnames,cl.document,d.document,cl.latitud,cl.longitud,cl.email,cl.mobile,cl.mobile_optional,cl.address,cl.reference, cl.note, cl.net_ip")
      ->select("c.id,c.internal_code,c.clientid,c.payday,c.days_grace,c.contract_date,c.suspension_date,c.finish_date,c.state, cl.names, cl.surnames, cl.note, CONCAT_WS(' ', cl.names, cl.surnames) AS client,cl.document,d.document AS name_doc,cl.latitud,cl.longitud,cl.email,cl.net_ip,cl.mobile,cl.mobile_optional,cl.address,cl.reference")
      ->addSelect("SUM(b.remaining_amount)", "remaining_amount")
      ->addSelect("GROUP_CONCAT(b.id)", "billIds")
      ->addSelect("GROUP_CONCAT(b.billed_month)", "months")
      ->addSelect("COUNT(b.id)", "counter");
  }

  public function list_info_clients($filters = [])
  {
    $query = $this->query_info();
    if ($filters['deuda']) {
      $condicion = $filters['deuda'];
      $query->andHaving("counter > {$condicion}");
    }

    if ($filters['phone:required']) {
      $condicion = $filters['phone:required'];
      $query->andWhere("(cl.mobile is not null OR cl.mobile <> '')");
    }

    return $query->getMany();
  }

  public function select_info_client(string $id)
  {
    return $this->query_info()
      ->where("cl.id = '{$id}'")
      ->getOne();
  }

  public function select_info_client_by_contract(string $id)
  {
    return $this->createQueryBuilder()
      ->from("clients cl")
      ->innerJoin("contracts c", "c.clientid = cl.id")
      ->where("c.id = {$id}")
      ->select("cl.*")
      ->getOne();
  }


  public function select_contract_by_id(string $id)
  {
    return $this->createQueryBuilder()
      ->from("clients cl")
      ->innerJoin("contracts c", "c.clientid = cl.id")
      ->where("c.id = {$id}")
      ->select("c.*")
      ->getOne();
  }


  public function export()
  {
    $sql = "
      
      SELECT c.id,c.internal_code,c.clientid,c.payday,c.create_invoice,c.days_grace,
      c.discount,c.discount_price,c.months_discount,c.contract_date,c.suspension_date,c.finish_date,
      c.state,cl.names,cl.surnames,cl.document,d.document AS name_doc,cl.latitud,cl.longitud,
      cl.email,cl.mobile,cl.mobile_optional,cl.address,cl.reference,cl.note,cl.net_ip,df.red_type
      FROM contracts c
      JOIN clients cl ON c.clientid = cl.id
      JOIN document_type d ON cl.documentid = d.id
      LEFT JOIN facility f ON cl.id = f.clientid
      LEFT JOIN detail_facility df ON f.id = df.facilityid 
      WHERE c.state != 0
      ORDER BY c.id ASC
      
      ";

    $answer = $this->select_all($sql);
    return $answer;
  }
  public function locations()
  {
    $sql = "SELECT c.id,c.internal_code,c.clientid,c.payday,c.create_invoice,c.days_grace,c.discount,c.discount_price,c.months_discount,c.contract_date,c.suspension_date,c.finish_date,c.state,cl.names,cl.surnames,cl.document,d.document AS name_doc,cl.latitud,cl.longitud,cl.email,cl.mobile,cl.mobile_optional,cl.address,cl.reference FROM contracts c JOIN clients cl ON c.clientid = cl.id JOIN document_type d ON cl.documentid = d.id WHERE c.state != 0 ORDER BY c.id ASC";
    $answer = $this->select_all($sql);
    return $answer;
  }
  public function create(int $user, int $client, string $code, int $payday, int $invoice, int $daygrace, int $discount, string $price, string $month, string $datetime, int $state, string $modalidad)
  {
    $this->intUser = $user;
    $this->intClient = $client;
    $this->strCode = $code;
    $this->intPayDay = $payday;
    $this->intInvoice = $invoice;
    $this->intDayGrace = $daygrace;
    $this->intDiscount = $discount;
    $this->strPrice = $price;
    $this->intMonth = $month;
    $this->strDatetime = $datetime;
    $this->intState = $state;
    $answer = "";
    $sql = "SELECT *FROM contracts WHERE clientid = $this->intClient";
    $request = $this->select_all($sql);
    if (empty($request)) {
      $query = "INSERT INTO contracts (userid, clientid, internal_code, payday, create_invoice, days_grace, discount, discount_price, months_discount, remaining_discount, contract_date, state, modalidad) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $data = array($this->intUser, $this->intClient, $this->strCode, $this->intPayDay, $this->intInvoice, $this->intDayGrace, $this->intDiscount, $this->strPrice, $this->intMonth, $this->intMonth, $this->strDatetime, $this->intState, $modalidad);
      $insert = $this->insert($query, $data);
      if ($insert) {
        $answer = 'success';
      } else {
        $answer = 'error';
      }
    } else {
      $answer = "exists";
    }
    return $answer;
  }
  public function import(string $business, string $user, string $client, string $code, string $payday, string $invoice, string $daygrace, string $discount, string $price, string $month, string $datetime, string $state)
  {
    $this->intUser = $user;
    $this->intClient = $client;
    $this->strCode = $code;
    $this->intPayDay = $payday;
    $this->intInvoice = $invoice;
    $this->intDayGrace = $daygrace;
    $this->intDiscount = $discount;
    $this->strPrice = $price;
    $this->intMonth = $month;
    $this->strDatetime = $datetime;
    $this->intState = $state;
    $answer = 0;
    $sql = "SELECT *FROM contracts WHERE clientid = $this->intClient";
    $request = $this->select_all($sql);
    if (empty($request)) {
      $query = "INSERT INTO contracts(userid,clientid,internal_code,payday,create_invoice,days_grace,discount,discount_price,months_discount,remaining_discount,contract_date,state) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
      $data = array($this->intUser, $this->intClient, $this->strCode, $this->intPayDay, $this->intInvoice, $this->intDayGrace, $this->intDiscount, $this->strPrice, $this->intMonth, $this->intMonth, $this->strDatetime, $this->intState);
      $insert = $this->insert($query, $data);
      if ($insert) {
        $answer = $insert;
      } else {
        $answer = 0;
      }
    } else {
      $answer = 0;
    }
    return $answer;
  }
  public function modify(int $id, int $payday, int $daygrace, int $discount, string $price, string $month)
  {
    $this->intId = $id;
    $this->intPayDay = $payday;
    $this->intDayGrace = $daygrace;
    $this->intDiscount = $discount;
    $this->strPrice = $price;
    $this->intMonth = $month;
    $answer = "";
    $query = "UPDATE contracts SET payday=?,days_grace=?,discount=?,discount_price=?,months_discount=?,remaining_discount=? WHERE id = $this->intId";
    $data = array($this->intPayDay, $this->intDayGrace, $this->intDiscount, $this->strPrice, $this->intMonth, $this->intMonth);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }

  public function editContract(int $contractId, array $payload)
  {
    return $this->createQueryBuilder()
      ->update()
      ->from("contracts")
      ->set($payload)
      ->where("id = {$contractId}")
      ->execute();
  }

  public function modify_date(int $id, string $suspension, string $finish)
  {
    $this->intId = $id;
    $this->strSuspension = $suspension;
    $this->strFinish = $finish;
    $answer = "";
    $query = "UPDATE contracts SET suspension_date=?,finish_date=? WHERE id = $this->intId";
    $data = array($this->strSuspension, $this->strFinish);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function discount_months(int $id)
  {
    $this->intId = $id;
    $answer = "";
    $query = "UPDATE contracts SET remaining_discount=remaining_discount-? WHERE id = $this->intId";
    $data = array(1);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function modify_discount(int $id)
  {
    $this->intId = $id;
    $answer = "";
    $query = "UPDATE contracts SET discount=?,discount_price=?,months_discount=? WHERE id = $this->intId";
    $data = array(0, 0, 0);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function import_client(string $names, string $surnames, int $type, string $document, string $mobile, string $mobileOp, string $email, string $address, string $reference, string $latitud, string $longitud, string $note)
  {
    $this->strNames = $names;
    $this->strSurnames = $surnames;
    $this->intType = $type;
    $this->strDocument = $document;
    $this->strMobile = $mobile;
    $this->strMobileOp = $mobileOp;
    $this->strEmail = $email;
    $this->strAddess = $address;
    $this->strReference = $reference;
    $this->strLatitud = $latitud;
    $this->strLongitud = $longitud;
    $this->strNote = $note;
    $answer = "";
    $query = "INSERT INTO clients(names,surnames,documentid,document,mobile,mobile_optional,email,address,reference,latitud,longitud,note) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
    $data = array($this->strNames, $this->strSurnames, $this->intType, $this->strDocument, $this->strMobile, $this->strMobileOp, $this->strEmail, $this->strAddess, $this->strReference, $this->strLatitud, $this->strLongitud, $this->strNote);
    $insert = $this->insert($query, $data);
    if ($insert) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function create_client(string $names, string $surnames, int $type_document, string $document, string $mobile, string $mobileOp, string $email, string $address, string $reference, string $note)
  {
    $this->strNames = $names;
    $this->strSurnames = $surnames;
    $this->intType = $type_document;
    $this->strDocument = $document;
    $this->strMobile = $mobile;
    $this->strMobileOp = $mobileOp;
    $this->strEmail = $email;
    $this->strAddess = $address;
    $this->strReference = $reference;
    $this->strNote = $note;
    $answer = "";
    $query = "INSERT INTO clients(names,surnames,documentid,document,mobile,mobile_optional,email,address,reference,note) VALUES(?,?,?,?,?,?,?,?,?,?)";
    $data = array($this->strNames, $this->strSurnames, $this->intType, $this->strDocument, $this->strMobile, $this->strMobileOp, $this->strEmail, $this->strAddess, $this->strReference, $this->strNote);
    $insert = $this->insert($query, $data);
    if ($insert) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function modify_client(int $id, string $names, string $surnames, int $type_document, string $document, string $mobile, string $mobileOp, string $email, string $address, string $reference, string $note, string $latitud, string $longitud)
  {
    $this->intClient = $id;
    $this->strNames = $names;
    $this->strSurnames = $surnames;
    $this->intType = $type_document;
    $this->strDocument = $document;
    $this->strMobile = $mobile;
    $this->strMobileOp = $mobileOp;
    $this->strEmail = $email;
    $this->strAddess = $address;
    $this->strReference = $reference;
    $this->strNote = $note;
    $this->strLatitud = $latitud;
    $this->strLongitud = $longitud;
    $answer = "";
    $sql = "SELECT *FROM clients WHERE names = '{$this->strNames}' AND surnames = '{$this->strSurnames}' AND id != $this->intClient";
    $request = $this->select_all($sql);
    if (empty($request)) {
      $query = "UPDATE clients SET names=?,surnames=?,documentid=?,document=?,mobile=?,mobile_optional=?,email=?,address=?,reference=?,note=?,latitud=?,longitud=? WHERE id = $this->intClient";
      $data = array($this->strNames, $this->strSurnames, $this->intType, $this->strDocument, $this->strMobile, $this->strMobileOp, $this->strEmail, $this->strAddess, $this->strReference, $this->strNote, $this->strLatitud, $this->strLongitud);
      $update = $this->update($query, $data);
      if ($update) {
        $answer = 'success';
      } else {
        $answer = 'error';
      }
    } else {
      $answer = 'exists';
    }
    return $answer;
  }


  public function select_client(int $client)
  {
    $this->intClient = $client;
    $sql = "SELECT c.id,c.names,c.surnames,c.documentid,c.document,c.mobile,c.mobile_optional,c.email,c.address,c.reference,c.latitud,c.longitud, c.state,d.document AS name_doc FROM clients c JOIN document_type d ON c.documentid = d.id WHERE c.id = $this->intClient";
    $answer = $this->select($sql);
    return $answer;
  }

  public function find_client($clientId)
  {
    return (Object) $this->createQueryBuilder()
      ->from("clients")
      ->select("*")
      ->where("id = {$clientId}")
      ->getOne();
  }

  public function find_client_by_id(string $contractId)
  {
    return (Object) $this->createQueryBuilder()
      ->from("clients cli")
      ->innerJoin("contracts c", "c.clientid = cli.id")
      ->select("cli.*")
      ->where("c.id = {$contractId}")
      ->getOne();
  }

  public function select_contract(int $client)
  {
    $this->intClient = $client;
    $sql = "SELECT * FROM contracts WHERE clientid = $this->intClient";
    $answer = $this->select($sql);
    return $answer;
  }

  public function existing_client(string $names, string $surnames)
  {
    $this->strNames = $names;
    $this->strSurnames = $surnames;
    $query = "SELECT * FROM contracts WHERE clientid IN(SELECT id FROM clients WHERE names = '$this->strNames' AND surnames = '$this->strSurnames' AND state = 1)";
    $answer = $this->select_all($query);
    return $answer;
  }
  public function list_documents()
  {
    //$sql = "SELECT *FROM document_type WHERE id NOT IN(1,4,5)";
    $sql = "SELECT *FROM document_type";
    $request = $this->select_all($sql);
    return $request;
  }
  public function returnCode()
  {
    $sql = "SELECT COUNT(internal_code) AS code FROM contracts";
    $answer = $this->select($sql);
    $code = $answer['code'];
    return $code;
  }
  public function generateCode()
  {
    $sql = "SELECT MAX(internal_code) AS code FROM contracts";
    $answer = $this->select($sql);
    $code = $answer['code'];
    return $code;
  }
  public function select_record(int $id)
  {
    $this->intId = $id;
    $sql = "SELECT *FROM contracts WHERE id = $this->intId";
    $asnwer = $this->select($sql);
    return $asnwer;
  }
  public function search_document(string $document)
  {
    $this->strDocument = $document;
    $sql = "SELECT * FROM clients WHERE document = '$this->strDocument'";
    $request = $this->select($sql);
    return $request;
  }
  public function returnContract()
  {
    $sql = "SELECT MAX(id) AS id FROM contracts";
    $answer = $this->select($sql);
    $contract = $answer['id'];
    return $contract;
  }
  public function returnClient()
  {
    $sql = "SELECT MAX(id) AS id FROM clients";
    $answer = $this->select($sql);
    $client = $answer['id'];
    return $client;
  }
  public function cancel(int $id, string $date)
  {
    $this->intId = $id;
    $this->strFinish = $date;
    $answer = '';
    $sql = "UPDATE contracts SET finish_date = ?,state = ? WHERE id = $this->intId";
    $data = array($this->strFinish, 4);
    $update = $this->update($sql, $data);
    if ($update) {
      $sql_detail = "UPDATE detail_contracts SET state = ? WHERE contractid = $this->intId";
      $data_detail = array(3);
      $update_detail = $this->update($sql_detail, $data_detail);
      if ($update_detail) {
        $answer = 'success';
      } else {
        $answer = 'error';
      }
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function state_service(int $id, int $state)
  {
    $this->intId = $id;
    $this->intState = $state;
    $answer = '';
    $sql = "UPDATE detail_contracts SET state = ? WHERE contractid = $this->intId";
    $data = array($this->intState);
    $update = $this->update($sql, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function layoff(int $id, string $date)
  {
    $this->intId = $id;
    $this->strSuspension = $date;
    $answer = '';
    $sql = "UPDATE contracts SET suspension_date = ?,state = ? WHERE id = $this->intId";
    $data = array($this->strSuspension, 3);
    $update = $this->update($sql, $data);
    if ($update) {
      $sql_detail = "UPDATE detail_contracts SET state = ? WHERE contractid = $this->intId";
      $data_detail = array(2);
      $update_detail = $this->update($sql_detail, $data_detail);
      if ($update_detail) {
        $answer = 'success';
      } else {
        $answer = 'error';
      }
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function activate(int $id, string $date)
  {
    $this->intId = $id;
    $this->strFinish = $date;
    $answer = '';
    $sql = "UPDATE contracts SET finish_date = ?,state = ? WHERE id = $this->intId";
    $data = array($this->strFinish, 2);
    $update = $this->update($sql, $data);
    if ($update) {
      $sql_detail = "UPDATE detail_contracts SET state = ? WHERE contractid = $this->intId";
      $data_detail = array(1);
      $update_detail = $this->update($sql_detail, $data_detail);
      if ($update_detail) {
        $answer = 'success';
      } else {
        $answer = 'error';
      }
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  /* MODULO FACTURAS */
  public function create_bill(int $user, int $client, int $voucher, int $serie, string $code, string $correlative, string $issue, string $expiration, string $billed_month, string $subtotal, string $discount, string $total, int $type, int $method, string $observation)
  {
    $this->intUser = $user;
    $this->intClient = $client;
    $this->intVoucher = $voucher;
    $this->intSerie = $serie;
    $this->strCodeBill = $code;
    $this->strCorrelative = $correlative;
    $this->strIssue = $issue;
    $this->strExpiration = $expiration;
    $this->strMonth = $billed_month;
    $this->strSubtotal = $subtotal;
    $this->strDiscount = $discount;
    $this->strTotal = $total;
    $this->strType = $type;
    $this->intMethod = $method;
    $this->strObservation = $observation;
    $answer = "";
    $query = "INSERT INTO bills(userid,clientid,voucherid,serieid,internal_code,correlative,date_issue,expiration_date,billed_month,subtotal,discount,total,remaining_amount,type,sales_method,observation) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $data = array($this->intUser, $this->intClient, $this->intVoucher, $this->intSerie, $this->strCodeBill, $this->strCorrelative, $this->strIssue, $this->strExpiration, $this->strMonth, $this->strSubtotal, $this->strDiscount, $this->strTotal, $this->strTotal, $this->strType, $this->intMethod, $this->strObservation);
    $insert = $this->insert($query, $data);
    if ($insert) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function modify_bill(int $bill, string $issue, string $expiration, string $billed_month, string $subtotal, string $discount, string $total, string $observation, int $state)
  {
    $this->intBill = $bill;
    $this->strIssue = $issue;
    $this->strExpiration = $expiration;
    $this->strMonth = $billed_month;
    $this->strSubtotal = $subtotal;
    $this->strDiscount = $discount;
    $this->strTotal = $total;
    $this->strObservation = $observation;
    $this->intState = $state;
    $answer = "";
    $query = "UPDATE bills SET date_issue=?,expiration_date=?,billed_month=?,subtotal=?,discount=?,total=?,observation=?,state=? WHERE id = $this->intBill";
    $data = array($this->strIssue, $this->strExpiration, $this->strMonth, $this->strSubtotal, $this->strDiscount, $this->strTotal, $this->strObservation, $this->intState);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function replacement_amounts(int $bill, string $amount_paid, string $remaining)
  {
    $this->intBill = $bill;
    $this->strTotalPaid = $amount_paid;
    $this->strRemaining = $remaining;
    $answer = "";
    $query = "UPDATE bills SET amount_paid = ?,remaining_amount = ? WHERE id = $this->intBill";
    $data = array($this->strTotalPaid, $this->strRemaining);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function detail_bill(int $bill, int $type, int $product, string $description, string $quantity, string $price, string $total)
  {
    $this->intBill = $bill;
    $this->strType = $type;
    $this->intProduct = $product;
    $this->strDescription = $description;
    $this->strQuantity = $quantity;
    $this->strPrice = $price;
    $this->strTotal = $total;
    $answer = "";
    $query = "INSERT INTO detail_bills(billid,type,serproid,description,quantity,price,total) VALUES(?,?,?,?,?,?,?)";
    $data = array($this->intBill, $this->strType, $this->intProduct, $this->strDescription, $this->strQuantity, $this->strPrice, $this->strTotal);
    $insert = $this->insert($query, $data);
    if ($insert) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function remove_datail(int $bill)
  {
    $this->intBill = $bill;
    $answer = "";
    $sql = "DELETE FROM detail_bills WHERE billid = $this->intBill";
    $delete = $this->delete($sql);
    if ($delete) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function create_departures(int $bill, int $product, string $date, string $description, string $quantity, string $price, string $total)
  {
    $this->intBill = $bill;
    $this->intProduct = $product;
    $this->strDate = $date;
    $this->strDescription = $description;
    $this->strQuantity = $quantity;
    $this->strPrice = $price;
    $this->strTotal = $total;
    $answer = "";
    $query = "INSERT INTO departures(billid,productid,departure_date,description,quantity_departures,unit_price,total_cost) VALUES(?,?,?,?,?,?,?)";
    $data = array($this->intBill, $this->intProduct, $this->strDate, $this->strDescription, $this->strQuantity, $this->strPrice, $this->strTotal);
    $insert = $this->insert($query, $data);
    if ($insert) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function create_incomes(int $product, string $date, string $description, int $quantity, string $price, string $total)
  {
    $this->intProduct = $product;
    $this->strDate = $date;
    $this->strDescription = $description;
    $this->strQuantity = $quantity;
    $this->strPrice = $price;
    $this->strTotal = $total;
    $answer = "";
    $query = "INSERT INTO income(productid,income_date,description,quantity_income,unit_price,total_cost) VALUES(?,?,?,?,?,?)";
    $data = array($this->intProduct, $this->strDate, $this->strDescription, $this->strQuantity, $this->strPrice, $this->strTotal);
    $insert = $this->insert($query, $data);
    if ($insert) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function register_email(int $client, int $bill, string $affair, string $sender, string $files, string $type_file, string $template_email, string $datetime, int $state_email)
  {
    $answer = '';
    $query = "INSERT INTO emails(clientid,billid,affair,sender,files,type_file,template_email,registration_date,state) VALUES(?,?,?,?,?,?,?,?,?)";
    $data = array($client, $bill, $affair, $sender, $files, $type_file, $template_email, $datetime, $state_email);
    $insert = $this->insert($query, $data);
    if ($insert) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function list_bills(int $client)
  {
    $this->intClient = $client;
    $sql = "SELECT b.id,b.clientid,b.internal_code,b.correlative,b.date_issue,b.expiration_date,b.billed_month,b.subtotal,b.discount,b.total,b.type,b.sales_method,b.remaining_amount,b.amount_paid,b.state,vs.serie,v.voucher,b.observation FROM bills b JOIN vouchers v ON b.voucherid = v.id JOIN voucher_series vs ON b.serieid = vs.id WHERE b.state != 0 AND b.clientid = $this->intClient ORDER BY b.id DESC";
    $asnwer = $this->select_all($sql);
    return $asnwer;
  }
  public function select_bill(int $bill)
  {
    $this->intBill = $bill;
    $sql = "SELECT b.id,CONCAT_WS(' ', c.names, c.surnames) AS client,b.clientid,b.internal_code,b.correlative,b.date_issue,b.expiration_date,b.subtotal,b.discount,b.total,b.amount_paid,b.remaining_amount,b.type,b.sales_method,b.billed_month,b.state,vs.serie,v.voucher,b.observation,b.voucherid,b.serieid
      FROM bills b JOIN clients c ON b.clientid = c.id JOIN vouchers v ON b.voucherid = v.id JOIN voucher_series vs ON b.serieid = vs.id WHERE b.id = $this->intBill";
    $asnwer = $this->select($sql);
    return $asnwer;
  }
  public function bill_voucher(int $id)
  {
    $request = array();
    $sql_bill = "SELECT b.id,b.clientid,b.voucherid,b.serieid,c.names,c.surnames,dt.document AS type_doc,c.document,c.mobile,c.address,c.email,v.voucher,vs.serie,b.internal_code,b.observation,b.date_issue,b.billed_month,b.correlative,b.expiration_date,b.promise_date,b.subtotal,b.discount,b.total,b.amount_paid,b.remaining_amount,b.type,b.sales_method,b.state FROM bills b JOIN clients c ON b.clientid = c.id JOIN document_type dt ON c.documentid = dt.id JOIN users u ON b.userid = u.id JOIN vouchers v ON b.voucherid = v.id JOIN voucher_series vs ON b.serieid = vs.id WHERE b.id = $id";
    $request_bill = $this->select($sql_bill);
    if (!empty($request_bill)) {
      $sql_business = "SELECT b.id,b.documentid,b.ruc,b.business_name,b.tradename,b.slogan,b.mobile,b.mobile_refrence,b.email,b.password,b.server_host,b.port,b.address,b.department,b.province,b.district,b.ubigeo,b.footer_text,b.currencyid,b.logo_login,b.logotyope,b.favicon,b.country_code,b.google_apikey,b.reniec_apikey,c.symbol,c.money,c.money_plural FROM business b JOIN currency c ON b.currencyid = c.id LIMIT 1";
      $request_business = $this->select($sql_business);
      $sql_atm = "SELECT u.names AS user FROM payments p JOIN users u ON p.userid = u.id WHERE p.billid = $id  ORDER BY p.id DESC LIMIT 1";
      $request_atm = $this->select($sql_atm);
      $sql_payment = "SELECT p.id, p.internal_code,fp.payment_type,p.payment_date,p.comment,p.amount_paid,p.amount_total,p.remaining_credit,u.names FROM payments p JOIN forms_payment fp ON p.paytypeid = fp.id JOIN users u ON p.userid = u.id WHERE p.billid = $id AND p.state = 1";
      $request_payment = $this->select_all($sql_payment);
      $sql_detail = "SELECT *FROM detail_bills WHERE billid = $id";
      $request_detail = $this->select_all($sql_detail);
      $request = array('bill' => $request_bill, 'detail' => $request_detail, 'business' => $request_business, 'payments' => $request_payment, 'atm' => $request_atm);
    }
    return $request;
  }
  public function remaining_amount(int $bill)
  {
    $this->intBill = $bill;
    $sql = "SELECT remaining_amount AS total FROM bills WHERE id = $this->intBill";
    $answer = $this->select($sql);
    $total = $answer['total'];
    return $total;
  }
  public function remaining_bill(int $bill, string $remaining)
  {
    $this->intBill = $bill;
    $this->strRemaining = $remaining;
    $answer = "";
    $query = "UPDATE bills SET remaining_amount = ? WHERE id = $this->intBill";
    $data = array($this->strRemaining);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function total_paid(int $bill)
  {
    $this->intBill = $bill;
    $sql = "SELECT COALESCE(SUM(amount_paid),0) AS amount_paid FROM payments WHERE billid = $this->intBill AND state = 1";
    $answer = $this->select($sql);
    $amount_paid = $answer['amount_paid'];
    return $amount_paid;
  }
  public function amount_paid(int $bill)
  {
    $this->intBill = $bill;
    $sql = "SELECT amount_paid AS total FROM bills WHERE id = $this->intBill";
    $answer = $this->select($sql);
    $total = $answer['total'];
    return $total;
  }
  public function select_invoice(int $client)
  {
    /* VARIABLE ARRAY  PARA ALMACENAR LA INF QUE SE MANDARA A LA VISTA */
    $request = array();
    /* CONSULTA PARA OBTENER LA INFO DEL CLIENTE */
    $sql_client = "SELECT * FROM clients WHERE id = $client";
    $request_client = $this->select($sql_client);
    if (!empty($request_client)) {
      /* TOTAL DE FACTURAS */
      $sql_validate = "SELECT COUNT(*) AS total FROM bills WHERE clientid = $client AND state != 4 AND type = 2";
      $request_validate = $this->select($sql_validate);
      /* NOMBRE DEL CLIENTE */
      $full_name = $request_client['names'] . " " . $request_client['surnames'];
      /* DATOS DEL CONTRATO */
      $sql_contract = "SELECT * FROM contracts WHERE clientid = $client";
      $request_contract = $this->select($sql_contract);
      /* OBTENEMOS EL ID DEL CONTRATO */
      $idcontract = $request_contract['id'];
      /* VALIDAMOS SI EL CLIENTE TIENE DESCUETO */
      if ($request_contract['discount'] == 1) {
        $discount = $request_contract['discount_price'];
      } else {
        $discount = 0;
      }
      /* CONSULTA PARA OBTENER SI TIENE SERVIOS EL CLIENTE */
      $sql_services = "SELECT COUNT(serviceid) AS serivices FROM detail_contracts WHERE contractid = $idcontract";
      $request_services = $this->select($sql_services);
      $services = $request_services['serivices'];
      if ($request_validate['total'] >= 1) {
        /* CONSULTA PARA OBTENER LA INFORMACION DE LA FACTURA */
        $sql_bill = "SELECT * FROM bills WHERE clientid = $client AND state != 4 AND type = 2 ORDER BY id DESC LIMIT 1";
        $request_bill = $this->select($sql_bill);
        /* CONSULTA PARA OBTENER LOS SERVICOS DEL CLIENTE */
        $sql_detail = "SELECT s.id,s.service,s.price
          FROM detail_contracts dc
          JOIN services s ON dc.serviceid = s.id
          WHERE dc.contractid = $idcontract";
        $request_detail = $this->select_all($sql_detail);
        /* FECHA DE VENCIMIENTO DE LA FACTURA */
        $expiration = date("d/m/Y", strtotime($request_bill['expiration_date'] . " + 1 month"));
        /* OBTENER MES EN LETRAS */
        $months = months();
        $month = $months[date('n', strtotime($request_bill['expiration_date'])) - 1];
        /* MES DE FACTURACION */
        $billed_month = date("d/m/Y", strtotime($request_bill['expiration_date']));
        /* MODIFICAR EL COSTO DEL SERVCIO POR LOS DIAS A UTILIZAR */
        if (count($request_detail) > 0) {
          for ($i = 0; $i < count($request_detail); $i++) {
            $request_detail[$i]['service'] = "SERVICIO DE " . $request_detail[$i]['service'] . ", MES DE " . strtoupper($month);
          }
        }
        /* ARRAY DE DATOS GENERALES */
        $invoice = array(
          "idclient" => $client,
          "client" => $full_name,
          "expiration" => $expiration,
          "billed_month" => $billed_month,
          "discount" => $discount,
          "status" => $request_contract['state']
        );
        /* ARRAY CONTENIENDO TODA LA INFO PARA LA VISTA */
        $request = array('invoice' => $invoice, 'detail' => $request_detail, 'service' => $services);
      } else {
        /* CONSULTA PARA OBTENER LOS SERVICOS DEL CLIENTE */
        $sql_detail = "SELECT s.id,s.service,s.price
          FROM detail_contracts dc
          JOIN services s ON dc.serviceid = s.id
          WHERE dc.contractid = $idcontract";
        $request_detail = $this->select_all($sql_detail);
        /* DESGLOSAR FECHA DE CONTRATO PARA OBTENER EL MES */
        $month_contract = date("m", strtotime($request_contract['contract_date']));
        /* DESGLOSAR FECHA DE CONTRATO PARA OBTENER EL AÑO */
        $year_contract = date("Y", strtotime($request_contract['contract_date']));
        /* CONSULTA PARA OBTENER LA INSTALACION DEL CLIENTE */
        $sql_facility = "SELECT MAX(attention_date) AS attention_date FROM facility WHERE clientid = $client AND state != 5";
        $request_facility = $this->select($sql_facility);
        //VALIDAR SI EXISTE INSTALACION
        if (!empty($request_facility['attention_date'])) {
          /* FECHA DE INSTALACION */
          $date_facility = new DateTime($request_facility['attention_date']);
          /* OBTENER TOTAL DE DIAS DEL MES */
          $total_day = cal_days_in_month(CAL_GREGORIAN, $month_contract, $year_contract);
          /* ULTIMO DIA DEL MES EN FORMATO FECHA */
          $date_lastday = $year_contract . "-" . $month_contract . "-" . $total_day;
          $last_day = new DateTime($date_lastday);
          /* OBETENR LOS DIAS DE DIERENCIA ENTRE EL CONTRATO Y ULTIMO DIA DEL MES */
          $diff = $last_day->diff($date_facility);
          $used_days = ($diff->invert == 1) ? $diff->days : $diff->days;
        }
        /* DIA DE PAGO */
        $payday = str_pad($request_contract['payday'], 2, "0", STR_PAD_LEFT);
        /* FECHA DE VENCIMIENTO DE LA FACTURA */
        $date_payday = date("Y-m-" . $payday);
        $billed_month = date("d/m/Y", strtotime($date_payday));
        $date_exp = date("Y-m-d", strtotime($date_payday . " + 1 months"));
        $expiration = date("d/m/Y", strtotime($date_payday . " + 1 months"));
        /* OBTENER MES EN LETRAS */
        $months = months();
        $month = $months[date('n', strtotime($date_payday)) - 1];
        /* MODIFICAR EL COSTO DEL SERVCIO POR LOS DIAS A UTILIZAR */
        if (count($request_detail) > 0) {
          for ($i = 0; $i < count($request_detail); $i++) {
            if (!empty($request_facility['attention_date'])) {
              $cost_day = $request_detail[$i]['price'] / $total_day;
              $price_prorrateado = $cost_day * $used_days;
              $request_detail[$i]['price'] = round($price_prorrateado);
              $request_detail[$i]['service'] = "SERVICIO DE " . $request_detail[$i]['service'] . ", MES DE " . strtoupper($month) . " PRORRATEADO";
            } else {
              $request_detail[$i]['service'] = "SERVICIO DE " . $request_detail[$i]['service'] . ", MES DE " . strtoupper($month);
            }
          }
        }
        /* ARRAY DE DATOS GENERALES */
        $invoice = array(
          "idclient" => $client,
          "client" => $full_name,
          "expiration" => $expiration,
          "billed_month" => $billed_month,
          "discount" => $discount,
          "status" => $request_contract['state']
        );
        /* ARRAY CONTENIENDO TODA LA INFO PARA LA VISTA */
        $request = array('invoice' => $invoice, 'detail' => $request_detail, 'service' => $services);
      }
    }
    return $request;
  }
  public function modify_state_bill(int $bill, int $state)
  {
    $this->intBill = $bill;
    $this->intState = $state;
    $answer = "";
    $query = "UPDATE bills SET state = ? WHERE id = $this->intBill";
    $data = array($this->intState);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function modify_amounts(int $bill, string $subscriber, string $remaining, int $state)
  {
    $this->intBill = $bill;
    $this->strSubscriber = $subscriber;
    $this->strRemaining = $remaining;
    $this->intState = $state;
    $answer = "";
    if ($this->intState == 0) {
      $query = "UPDATE bills SET amount_paid = amount_paid + ?,remaining_amount = ? WHERE id = $this->intBill";
      $data = array($this->strSubscriber, $this->strRemaining);
    } else {
      $query = "UPDATE bills SET amount_paid = amount_paid + ?,remaining_amount = ?,state = ? WHERE id = $this->intBill";
      $data = array($this->strSubscriber, $this->strRemaining, $this->intState);
    }
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function subtract_amounts(int $bill, string $subscriber, int $state)
  {
    $this->intBill = $bill;
    $this->strSubscriber = $subscriber;
    $this->intState = $state;
    $answer = "";
    if ($this->intState == 0) {
      $query = "UPDATE bills SET amount_paid = amount_paid - ?,remaining_amount = remaining_amount + ? WHERE id = $this->intBill";
      $data = array($this->strSubscriber, $this->strSubscriber);
    } else {
      $query = "UPDATE bills SET amount_paid = amount_paid - ?,remaining_amount = ?, state = ? WHERE id = $this->intBill";
      $data = array($this->strSubscriber, $this->strSubscriber, $this->intState);
    }
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function next_payment(int $client)
  {
    $this->intClient = $client;
    $sql = "SELECT MAX(expiration_date) AS paydate FROM bills WHERE clientid = $this->intClient AND state != 4 AND type = 2";
    $answer = $this->select($sql);
    $date = $answer['paydate'];
    return $date;
  }
  public function pending_payments(int $client)
  {
    $this->intClient = $client;
    $sql = "SELECT COUNT(*) AS total FROM bills WHERE clientid = $this->intClient AND state NOT IN(1,4) AND id NOT IN(SELECT billid FROM payments WHERE state = 1)";
    $answer = $this->select($sql);
    $total = $answer['total'];
    return $total;
  }
  public function pending_services(int $client)
  {
    $this->intClient = $client;
    $sql = "SELECT COUNT(id) AS total FROM bills WHERE clientid = $this->intClient AND state NOT IN(1,4) AND type = 2";
    $answer = $this->select($sql);
    $total = $answer['total'];
    return $total;
  }
  public function outstanding_balance(int $client)
  {
    $this->intClient = $client;
    $sql = "SELECT SUM(remaining_amount) AS total FROM bills WHERE clientid = $this->intClient AND state NOT IN(1,4)";
    $answer = $this->select($sql);
    $total = $answer['total'];
    return $total;
  }
  public function consult_departure(int $bill)
  {
    $this->intBill = $bill;
    $sql = "SELECT *FROM departures WHERE billid = $this->intBill";
    $asnwer = $this->select_all($sql);
    return $asnwer;
  }
  public function subtract_stock(int $product, int $quantity)
  {
    $this->intProduct = $product;
    $this->strQuantity = $quantity;
    $answer = "";
    $query = "UPDATE products SET stock = stock - ? WHERE id = $this->intProduct";
    $data = array($this->strQuantity);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function increase_stock(int $product, int $quantity)
  {
    $this->intProduct = $product;
    $this->strQuantity = $quantity;
    $answer = "";
    $query = "UPDATE products SET stock = stock + ? WHERE id = $this->intProduct";
    $data = array($this->strQuantity);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function increase_serie(int $voucher, int $serie)
  {
    $this->intVoucher = $voucher;
    $this->intSerie = $serie;
    $answer = "";
    $query = "UPDATE voucher_series SET available = available + ? WHERE id = $this->intSerie AND voucherid = $this->intVoucher";
    $data = array(1);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function modify_available(int $voucher, int $serie)
  {
    $this->intVoucher = $voucher;
    $this->intSerie = $serie;
    $answer = "";
    $query = "UPDATE voucher_series SET available = available - ? WHERE id = $this->intSerie AND voucherid = $this->intVoucher";
    $data = array(1);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function returnCodeBill()
  {
    $sql = "SELECT COUNT(internal_code) AS code FROM bills";
    $answer = $this->select($sql);
    $code = $answer['code'];
    return $code;
  }
  public function generateCodeBill()
  {
    $sql = "SELECT MAX(internal_code) AS code FROM bills";
    $answer = $this->select($sql);
    $code = $answer['code'];
    return $code;
  }
  public function returnUsed(int $voucher, int $serie)
  {
    $this->intVoucher = $voucher;
    $this->intSerie = $serie;
    $sql = "SELECT until - available + 1 AS used FROM voucher_series WHERE id = $this->intSerie AND voucherid = $this->intVoucher";
    $answer = $this->select($sql);
    $used = $answer['used'];
    return $used;
  }
  public function returnBill()
  {
    $sql = "SELECT MAX(id) AS id FROM bills";
    $answer = $this->select($sql);
    $contract = $answer['id'];
    return $contract;
  }
  public function returnVoucher(int $voucher)
  {
    $this->intVoucher = $voucher;
    $sql = "SELECT voucher FROM vouchers WHERE id = $this->intVoucher";
    $answer = $this->select($sql);
    $voucher = $answer['voucher'];
    return $voucher;
  }
  public function returnCorrelative(int $voucher, int $serie)
  {
    $this->intVoucher = $voucher;
    $this->intSerie = $serie;
    $sql = "SELECT MAX(correlative) as correlative FROM bills WHERE serieid = $this->intSerie AND voucherid = $this->intVoucher";
    $answer = $this->select($sql);
    $correlative = $answer['correlative'];
    return $correlative;
  }
  public function cancel_bill(int $bill)
  {
    $this->intBill = $bill;
    $answer = '';
    $sql = "UPDATE bills SET state = ? WHERE id = $this->intBill";
    $data = array(4);
    $update = $this->update($sql, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  /* MODULO PAGOS Y BALANCE */
  public function list_payments(int $client)
  {
    $this->intClient = $client;
    $sql = "SELECT p.id,p.billid,p.internal_code,fp.payment_type,p.payment_date,p.comment,p.amount_paid,p.state,u.names AS user,vs.serie,v.voucher,b.correlative
        FROM payments p
        JOIN forms_payment fp ON p.paytypeid = fp.id
        JOIN users u ON p.userid = u.id
        JOIN bills b ON p.billid = b.id
        JOIN vouchers v ON b.voucherid = v.id
				JOIN voucher_series vs ON b.serieid = vs.id
        WHERE p.clientid = $this->intClient ORDER BY p.id DESC";
    $asnwer = $this->select_all($sql);
    return $asnwer;
  }
  public function create_payment(int $bill, int $user, int $client, string $code, int $type, string $datetime, string $comment, string $subscriber, string $total_paid, string $remaining, int $state)
  {
    $this->intBill = $bill;
    $this->intUser = $user;
    $this->intClient = $client;
    $this->strCodePayment = $code;
    $this->strTypePayment = $type;
    $this->strDatetime = $datetime;
    $this->strComment = $comment;
    $this->strSubscriber = $subscriber;
    $this->strTotalPaid = $total_paid;
    $this->strRemaining = $remaining;
    $this->intState = $state;
    $answer = "";
    $query = "INSERT INTO payments(billid,userid,clientid,internal_code,paytypeid,payment_date,comment,amount_paid,amount_total,remaining_credit,state) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
    $data = array($this->intBill, $this->intUser, $this->intClient, $this->strCodePayment, $this->strTypePayment, $this->strDatetime, $this->strComment, $this->strSubscriber, $this->strTotalPaid, $this->strRemaining, $this->intState);
    $insert = $this->insert($query, $data);
    if ($insert) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function modify_payment(int $payment, int $type, string $datetime, string $comment)
  {
    $this->intPayment = $payment;
    $this->strTypePayment = $type;
    $this->strDatetime = $datetime;
    $this->strComment = $comment;
    $answer = "";
    $query = "UPDATE payments SET paytypeid=?,payment_date=?,comment=? WHERE id = $this->intPayment";
    $data = array($this->strTypePayment, $this->strDatetime, $this->strComment);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function state_payments(int $bill, int $state)
  {
    $this->intBill = $bill;
    $this->intState = $state;
    $answer = "";
    $query = "UPDATE payments SET state = ? WHERE billid = $this->intBill";
    $data = array($this->intState);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function remaining_payments(int $payment, string $remaining)
  {
    $this->intPayment = $payment;
    $this->strRemaining = $remaining;
    $answer = "";
    $query = "UPDATE payments SET remaining_credit = ? WHERE id = $this->intPayment";
    $data = array($this->strRemaining);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function voided_payments(int $bill)
  {
    $this->intBill = $bill;
    $sql = "SELECT COUNT(*) AS total FROM payments WHERE billid = $this->intBill AND state = 1";
    $answer = $this->select($sql);
    $total = $answer['total'];
    return $total;
  }
  public function invoice_paid(int $bill)
  {
    $this->intBill = $bill;
    $sql = "SELECT p.id,p.billid,p.userid,p.clientid,p.internal_code,p.paytypeid,fp.payment_type,p.payment_date,p.comment,p.amount_paid,p.amount_total,p.remaining_credit FROM payments p JOIN forms_payment fp ON p.paytypeid = fp.id WHERE p.billid = $this->intBill AND p.state = 1 ORDER BY p.id DESC LIMIT 1";
    $answer = $this->select($sql);
    return $answer;
  }
  public function last_payment(int $idclient)
  {
    $this->intClient = $idclient;
    $sql = "SELECT MAX(payment_date) AS payment_date FROM payments WHERE clientid = $this->intClient AND state = 1";
    $answer = $this->select($sql);
    $total = $answer['payment_date'];
    return $total;
  }
  public function last_paymentid(int $id)
  {
    $this->intBill = $id;
    $sql = "SELECT MAX(id) AS id FROM payments WHERE billid = $this->intBill AND state = 1";
    $answer = $this->select($sql);
    $id = $answer['id'];
    return $id;
  }
  public function returnCodePayment()
  {
    $sql = "SELECT COUNT(internal_code) AS code FROM payments";
    $answer = $this->select($sql);
    $code = $answer['code'];
    return $code;
  }
  public function generateCodePayment()
  {
    $sql = "SELECT MAX(internal_code) AS code FROM payments";
    $answer = $this->select($sql);
    $code = $answer['code'];
    return $code;
  }
  public function select_payment(int $payment)
  {
    $this->intPayment = $payment;
    $sql = "SELECT p.id,p.billid,p.userid,p.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,p.internal_code,p.paytypeid,p.payment_date,p.comment,p.amount_paid,p.amount_total,p.remaining_credit,vs.serie,v.voucher,b.correlative
      FROM payments p
      JOIN clients c ON p.clientid = c.id
      JOIN bills b ON p.billid = b.id
      JOIN vouchers v ON b.voucherid = v.id
      JOIN voucher_series vs ON b.serieid = vs.id
      WHERE p.id = $this->intPayment";
    $asnwer = $this->select($sql);
    return $asnwer;
  }
  public function cancel_payment(int $payment)
  {
    $this->intPayment = $payment;
    $answer = "";
    $query = "UPDATE payments SET state = ? WHERE id = $this->intPayment";
    $data = array(2);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  /* MODULO TICKETS */
  public function create_ticket(int $user, int $client, int $technical, int $incidents, string $description, int $priority, string $attention, string $datetime)
  {
    $this->intUser = $user;
    $this->intClient = $client;
    $this->intTechnical = $technical;
    $this->intIncidents = $incidents;
    $this->strDescription = $description;
    $this->strPriority = $priority;
    $this->strAttention = $attention;
    $this->strDatetime = $datetime;
    $answer = "";
    $sql = "SELECT *FROM tickets WHERE attention_date = '{$this->strAttention}' AND clientid  = $this->intClient AND state != 6";
    $request = $this->select_all($sql);
    if (empty($request)) {
      $query = "INSERT INTO tickets(userid,clientid,technical,incidentsid,description,priority,attention_date,registration_date) VALUES(?,?,?,?,?,?,?,?)";
      $data = array($this->intUser, $this->intClient, $this->intTechnical, $this->intIncidents, $this->strDescription, $this->strPriority, $this->strAttention, $this->strDatetime);
      $insert = $this->insert($query, $data);
      if ($insert) {
        $answer = 'success';
      } else {
        $answer = 'error';
      }
    } else {
      $answer = "exists";
    }
    return $answer;
  }
  public function modify_ticket(int $id, int $client, int $technical, int $incidents, string $description, int $priority, string $attention)
  {
    $this->intId = $id;
    $this->intClient = $client;
    $this->intTechnical = $technical;
    $this->intIncidents = $incidents;
    $this->strDescription = $description;
    $this->strPriority = $priority;
    $this->strAttention = $attention;
    $answer = "";
    $sql = "SELECT *FROM tickets WHERE attention_date = '{$this->strAttention}' AND clientid = $this->intClient AND id != $this->intId";
    $request = $this->select_all($sql);
    if (empty($request)) {
      $query = "UPDATE tickets SET clientid=?,technical=?,incidentsid=?,description=?,priority=?,attention_date=? WHERE id = $this->intId";
      $data = array($this->intClient, $this->intTechnical, $this->intIncidents, $this->strDescription, $this->strPriority, $this->strAttention);
      $update = $this->update($query, $data);
      if ($update) {
        $answer = 'success';
      } else {
        $answer = 'error';
      }
    } else {
      $answer = "exists";
    }
    return $answer;
  }
  public function list_ticket(int $client)
  {
    $this->intClient = $client;
    $sql = "SELECT t.id,t.clientid,u.names AS user,t.attention_date,t.opening_date,t.closing_date,t.technical,t.registration_date,i.incident,t.priority,t.state
      FROM tickets t
      JOIN incidents i ON t.incidentsid = i.id
      JOIN users u ON t.userid = u.id
      WHERE t.state != 0 AND t.clientid = $this->intClient  ORDER BY t.id DESC";
    $asnwer = $this->select_all($sql);
    return $asnwer;
  }
  public function modify_state_ticket(int $ticket, int $state)
  {
    $this->intTicket = $ticket;
    $this->intState = $state;
    $answer = "";
    $query = "UPDATE tickets SET state = ? WHERE id = $this->intTicket";
    $data = array($this->intState);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function returnTicket()
  {
    $sql = "SELECT MAX(id) AS id FROM tickets";
    $answer = $this->select($sql);
    $ticket = $answer['id'];
    return $ticket;
  }
  public function see_technical(int $user)
  {
    $this->intUser = $user;
    $sql = "SELECT names AS technical FROM users WHERE id = $this->intUser";
    $answer = $this->select($sql);
    $technical = $answer['technical'];
    return $technical;
  }
  public function select_ticket(int $ticket)
  {
    $this->intTicket = $ticket;
    $sql = "SELECT t.id,t.userid,t.clientid,t.technical,t.incidentsid,t.description,t.priority,t.attention_date,t.opening_date,t.closing_date,t.registration_date,t.state,c.names,c.surnames,c.mobile,i.incident
      FROM tickets t
      JOIN clients c ON t.clientid = c.id
      JOIN incidents i ON t.incidentsid = i.id
      WHERE t.id = $this->intTicket";
    $asnwer = $this->select($sql);
    return $asnwer;
  }
  public function view_ticket(int $id)
  {
    $request = array();
    $sql_bill = "SELECT t.id,t.userid,t.clientid,t.technical,t.incidentsid,t.description,t.priority, t.attention_date,t.opening_date,t.closing_date,t.registration_date,t.state,CONCAT_WS(' ', c.names, c.surnames) AS client,dt.document AS type_doc,c.document,c.mobile,c.mobile_optional,c.address,c.email,CONCAT_WS(' ', u.names, u.surnames) AS user,u.image AS user_image,i.incident FROM tickets t JOIN users u ON t.userid = u.id JOIN clients c ON t.clientid = c.id JOIN document_type dt ON c.documentid = dt.id JOIN incidents i ON t.incidentsid = i.id WHERE t.id = $id";
    $request_ticket = $this->select($sql_bill);
    if (!empty($request_ticket)) {
      $sql_detail = "SELECT ts.id,ts.ticketid,ts.technicalid,ts.opening_date,ts.closing_date,ts.comment,ts.state,u.names,u.image FROM ticket_solution ts JOIN users u ON ts.technicalid = u.id WHERE ts.ticketid = $id ORDER BY ts.id ASC";
      $request_detail = $this->select_all($sql_detail);
      $request = array('ticket' => $request_ticket, 'detail' => $request_detail);
    }
    return $request;
  }
  public function list_technical()
  {
    $where = "";
    if ($_SESSION['userData']['profileid'] == ADMINISTRATOR) {
      $where = " AND profileid IN(1,2)";
    } else {
      $where = " AND profileid = 2";
    }
    $sql = "SELECT *FROM users WHERE state = 1" . $where;
    $request = $this->select_all($sql);
    return $request;
  }
  public function reassign_technical(int $ticket, int $technical)
  {
    $this->intTicket = $ticket;
    $this->intTechnical = $technical;
    $answer = "";
    $query = "UPDATE tickets SET technical = ? WHERE id = $this->intTicket";
    $data = array($this->intTechnical);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function reschedule_date(int $ticket, string $datetime)
  {
    $this->intTicket = $ticket;
    $this->strDatetime = $datetime;
    $answer = "";
    $query = "UPDATE tickets SET attention_date = ? WHERE id = $this->intTicket";
    $data = array($this->strDatetime);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function open_ticket(int $ticket, string $datetime, int $state)
  {
    $this->intTicket = $ticket;
    $this->strDatetime = $datetime;
    $this->intState = $state;
    $answer = "";
    $query = "UPDATE tickets SET opening_date = ?,state = ? WHERE id = $this->intTicket";
    $data = array($this->strDatetime, $this->intState);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function close_ticket(int $ticket, string $datetime, int $state)
  {
    $this->intTicket = $ticket;
    $this->strDatetime = $datetime;
    $this->intState = $state;
    $answer = "";
    $query = "UPDATE tickets SET closing_date = ?,state = ? WHERE id = $this->intTicket";
    $data = array($this->strDatetime, $this->intState);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function complete_ticket(int $ticket, int $user, string $opening_date, string $closing_date, string $observation, int $state)
  {
    $this->intTicket = $ticket;
    $this->intUser = $user;
    $this->strOpening = $opening_date;
    $this->strClosing = $closing_date;
    $this->strObservation = $observation;
    $this->intState = $state;
    $answer = "";
    $query = "INSERT INTO ticket_solution(ticketid,technicalid,opening_date,closing_date,comment,state)VALUES(?,?,?,?,?,?)";
    $data = array($this->intTicket, $this->intUser, $this->strOpening, $this->strClosing, $this->strObservation, $this->intState);
    $insert = $this->insert($query, $data);
    if ($insert) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function cancel_ticket(int $ticket)
  {
    $this->intTicket = $ticket;
    $sql = "UPDATE tickets SET state = ? WHERE id = $this->intTicket";
    $arrData = array(6);
    $request = $this->update($sql, $arrData);
    return $request;
  }
  /* MODULO DETALLE DE CONTRATOS Y SERVICIOS */
  public function list_internet(int $contract)
  {
    $this->intId = $contract;
    $sql = "SELECT dc.id,dc.contractid,dc.serviceid,dc.price,dc.registration_date,dc.state,i.internal_code,i.service,i.rise,i.rise_type,i.descent,i.descent_type,i.details FROM detail_contracts dc JOIN services i ON dc.serviceid = i.id WHERE dc.state != 0 AND i.type = 1 AND dc.contractid = $this->intId ORDER BY dc.id DESC";
    $asnwer = $this->select_all($sql);
    return $asnwer;
  }
  public function list_personalized(int $contract)
  {
    $this->intId = $contract;
    $sql = "SELECT dc.id,dc.contractid,dc.serviceid,dc.price,dc.registration_date,dc.state,i.internal_code,i.service,i.rise,i.descent,i.details FROM detail_contracts dc JOIN services i ON dc.serviceid = i.id WHERE dc.state != 0 AND i.type = 2 AND dc.contractid = $this->intId ORDER BY dc.id DESC";
    $asnwer = $this->select_all($sql);
    return $asnwer;
  }
  public function import_service(string $code, string $service, string $type, string $rise, string $price, string $datetime)
  {
    $this->strCodeServ = $code;
    $this->strService = $service;
    $this->strTypeServ = $type;
    $this->strRise = $rise;
    $this->strPriceServ = $price;
    $this->strDatetime = $datetime;
    $answer = "";
    $query = "INSERT INTO services(internal_code,service,type,rise_type,descent_type,price,registration_date) VALUES(?,?,?,?,?,?,?)";
    $data = array($this->strCodeServ, $this->strService, $this->strTypeServ, $this->strRise, $this->strRise, $this->strPriceServ, $this->strDatetime);
    $insert = $this->insert($query, $data);
    if ($insert) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function create_detail(int $contract, int $service, string $price, string $datetime)
  {
    $this->intId = $contract;
    $this->intService = $service;
    $this->strPriceSer = $price;
    $this->strDatetime = $datetime;
    $answer = "";
    $sql = "SELECT *FROM detail_contracts WHERE contractid = $this->intId AND serviceid = $this->intService";
    $request = $this->select_all($sql);
    if (empty($request)) {
      $query = "INSERT INTO detail_contracts(contractid,serviceid,price,registration_date) VALUES(?,?,?,?)";
      $data = array($this->intId, $this->intService, $this->strPriceSer, $this->strDatetime);
      $insert = $this->insert($query, $data);
      if ($insert) {
        $answer = 'success';
      } else {
        $answer = 'error';
      }
    } else {
      $answer = "exists";
    }
    return $answer;
  }
  public function modify_detail(int $id, int $service, string $price)
  {
    $this->intId = $id;
    $this->intService = $service;
    $this->strPriceSer = $price;
    $answer = "";
    $query = "UPDATE detail_contracts SET serviceid=?,price=? WHERE id = $this->intId";
    $data = array($this->intService, $this->strPriceSer);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function select_detail(int $id)
  {
    $this->intId = $id;
    $sql = "SELECT dc.id,dc.contractid,dc.serviceid,dc.price,i.service,i.type,i.rise,i.descent,i.details FROM detail_contracts dc JOIN services i ON dc.serviceid = i.id WHERE dc.id = $this->intId";
    $asnwer = $this->select($sql);
    return $asnwer;
  }
  public function remove_detail(int $id)
  {
    $this->intId = $id;
    $answer = "";
    $sql = "DELETE FROM detail_contracts WHERE id = $this->intId";
    $delete = $this->delete($sql);
    if ($delete) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function contract_services(int $id)
  {
    $this->intId = $id;
    $sql = "SELECT i.service FROM detail_contracts dc JOIN services i ON dc.serviceid = i.id WHERE contractid = $this->intId LIMIT 2";
    $answer = $this->select_all($sql);
    return $answer;
  }
  public function search_service(string $service)
  {
    $search = explode(" ", $service);
    $sql = "SELECT * FROM services WHERE service LIKE '%$search[0]%' AND state = 1";
    for ($i = 0; $i < count($search); $i++) {
      $sql .= " AND service LIKE '%$search[$i]%'";
    }
    $request = $this->select_all($sql);
    return $request;
  }
  public function select_service(int $id)
  {
    $this->intService = $id;
    $sql = "SELECT *FROM services WHERE id = $this->intService";
    $asnwer = $this->select($sql);
    return $asnwer;
  }
  public function returnCodeService()
  {
    $sql = "SELECT COUNT(internal_code) AS code FROM services";
    $answer = $this->select($sql);
    $code = $answer['code'];
    return $code;
  }
  public function generateCodeService()
  {
    $sql = "SELECT MAX(internal_code) AS code FROM services";
    $answer = $this->select($sql);
    $code = $answer['code'];
    return $code;
  }
  public function existing_service(string $service)
  {
    $this->strService = $service;
    $query = "SELECT * FROM services WHERE service = '$this->strService'";
    $answer = $this->select($query);
    return $answer;
  }
  public function returnService()
  {
    $sql = "SELECT MAX(id) AS id FROM services";
    $answer = $this->select($sql);
    $service = $answer['id'];
    return $service;
  }
  public function returnPriceService(int $id)
  {
    $this->intService = $id;
    $sql = "SELECT price FROM services WHERE id = $this->intService";
    $answer = $this->select($sql);
    $price = $answer['price'];
    return $price;
  }
  /* MODULO GALERIA */
  public function returnFacility()
  {
    $sql = "SELECT MAX(id) AS id FROM facility";
    $answer = $this->select($sql);
    $facility = $answer['id'];
    return $facility;
  }
  public function create_facility(int $client, int $user, int $technical, string $insDate, string $price, string $detail, string $datetime)
  {
    $this->intClient = $client;
    $this->intUser = $user;
    $this->intTechnical = $technical;
    $this->strInsDate = $insDate;
    $this->strInsPrice = $price;
    $this->strDetail = $detail;
    $this->strDatetime = $datetime;
    $answer = "";
    $query = "INSERT INTO facility(clientid,userid,technical,attention_date,cost,detail,registration_date)VALUES(?,?,?,?,?,?,?)";
    $data = array($this->intClient, $this->intUser, $this->intTechnical, $this->strInsDate, $this->strInsPrice, $this->strDetail, $this->strDatetime);
    $insert = $this->insert($query, $data);
    if ($insert) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function import_facility(string $client, string $user, string $technical, string $insDate, string $price)
  {
    $this->intClient = $client;
    $this->intUser = $user;
    $this->intTechnical = $technical;
    $this->strInsDate = $insDate;
    $this->strInsPrice = $price;
    $answer = "";
    $query = "INSERT INTO facility(clientid,userid,technical,attention_date,opening_date,closing_date,cost,registration_date,state)VALUES(?,?,?,?,?,?,?,?,?)";
    $data = array($this->intClient, $this->intUser, $this->intTechnical, $this->strInsDate, $this->strInsDate, $this->strInsDate, $this->strInsPrice, $this->strInsDate, 1);
    $insert = $this->insert($query, $data);
    if ($insert) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function import_detail_facility(string $facility, string $technical, string $insDate, string $red_type, string $ip)
  {
    $this->intFacility = $facility;
    $this->intTechnical = $technical;
    $this->strInsDate = $insDate;
    $this->strRedType = $red_type;
    $this->strIp = $ip;
    $answer = "";
    $query = "INSERT INTO detail_facility(facilityid,technicalid,opening_date,closing_date,state,red_type,ip)VALUES(?,?,?,?,?,?,?)";
    $data = array($this->intFacility, $this->intTechnical, $this->strInsDate, $this->strInsDate, 1, $this->strRedType, $this->strIp);
    $insert = $this->insert($query, $data);
    if ($insert) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function open_gallery(int $client)
  {
    $this->intClient = $client;
    $sql = "SELECT g.type,g.registration_date,g.image,u.names FROM gallery_images g JOIN users u ON g.userid = u.id WHERE g.clientid = $this->intClient";
    $asnwer = $this->select_all($sql);
    return $asnwer;
  }
  public function register_image(int $client, int $user, int $type, int $typeid, string $datetime, string $imagen)
  {
    $this->intClient = $client;
    $this->intUser = $user;
    $this->intType = $type;
    $this->intTypeId = $typeid;
    $this->strDatetime = $datetime;
    $this->strImagen = $imagen;
    $answer = "";
    $query = "INSERT INTO gallery_images(clientid,userid,type,typeid,registration_date,image) VALUES(?,?,?,?,?,?)";
    $data = array($this->intClient, $this->intUser, $this->intType, $this->intTypeId, $this->strDatetime, $this->strImagen);
    $insert = $this->insert($query, $data);
    if ($insert) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function remove_image(int $client, string $imagen)
  {
    $this->intClient = $client;
    $this->strImagen = $imagen;
    $query = "DELETE FROM gallery_images WHERE clientid = $this->intClient AND image = '{$this->strImagen}'";
    $request_delete = $this->delete($query);
    return $request_delete;
  }
}
