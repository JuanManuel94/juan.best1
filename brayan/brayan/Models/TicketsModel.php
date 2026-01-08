<?php
class TicketsModel extends Mysql
{
  private $intId, $intBusiness, $intUser, $intClient, $intTechnical, $intIncidents, $intTypeId, $intType, $strDescription, $strPriority, $strDatetime, $strAttention, $strOpening, $strClosing, $strObservation, $strState, $intBill;
  public function __construct()
  {
    parent::__construct();
  }
  public function list_current(string $user, string $day)
  {
    if ($user == 0) {
      $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.technical,t.opening_date,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
        FROM tickets t
        JOIN incidents i ON t.incidentsid = i.id
        JOIN clients c ON t.clientid = c.id
        JOIN users u ON t.userid = u.id
        WHERE t.state IN(2,3) AND DATE(attention_date) = '$day' ORDER BY t.id DESC";
    } else {
      $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.technical,t.opening_date,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
        FROM tickets t
        JOIN incidents i ON t.incidentsid = i.id
        JOIN clients c ON t.clientid = c.id
        JOIN users u ON t.userid = u.id
        WHERE t.technical = $user AND t.state IN(2,3) AND DATE(attention_date) = '$day' ORDER BY t.id DESC";
    }
    $answer = $this->select_all($sql);
    return $answer;
  }
  public function list_expired(string $user, string $affair, string $state)
  {
    if ($user == 0) {
      if ($affair != 0 && $state != 0) {
        $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.technical,t.opening_date,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
          FROM tickets t
          JOIN incidents i ON t.incidentsid = i.id
          JOIN clients c ON t.clientid = c.id
          JOIN users u ON t.userid = u.id
          WHERE DATE(t.attention_date) < DATE(NOW()) AND t.incidentsid = $affair AND t.state = $state ORDER BY t.id DESC";
      } elseif ($affair != 0 && $state == 0) {
        $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.technical,t.opening_date,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
          FROM tickets t
          JOIN incidents i ON t.incidentsid = i.id
          JOIN clients c ON t.clientid = c.id
          JOIN users u ON t.userid = u.id
          WHERE DATE(t.attention_date) < DATE(NOW()) AND t.state NOT IN(1,2,6) AND t.incidentsid = $affair ORDER BY t.id DESC";
      } elseif ($affair == 0 && $state != 0) {
        $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.technical,t.opening_date,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
          FROM tickets t
          JOIN incidents i ON t.incidentsid = i.id
          JOIN clients c ON t.clientid = c.id
          JOIN users u ON t.userid = u.id
          WHERE DATE(t.attention_date) < DATE(NOW()) AND t.state = $state ORDER BY t.id DESC";
      } else {
        $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.technical,t.opening_date,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
          FROM tickets t
          JOIN incidents i ON t.incidentsid = i.id
          JOIN clients c ON t.clientid = c.id
          JOIN users u ON t.userid = u.id
          WHERE DATE(t.attention_date) < DATE(NOW()) AND t.state NOT IN(1,2,6) ORDER BY t.id DESC";
      }
    } else {
      if ($affair != 0 && $state != 0) {
        $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.technical,t.opening_date,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
          FROM tickets t
          JOIN incidents i ON t.incidentsid = i.id
          JOIN clients c ON t.clientid = c.id
          JOIN users u ON t.userid = u.id
          WHERE DATE(t.attention_date) < DATE(NOW()) AND t.technical = $user AND t.incidentsid = $affair AND t.state = $state ORDER BY t.id DESC";
      } elseif ($affair != 0 && $state == 0) {
        $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.technical,t.opening_date,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
          FROM tickets t
          JOIN incidents i ON t.incidentsid = i.id
          JOIN clients c ON t.clientid = c.id
          JOIN users u ON t.userid = u.id
          WHERE DATE(t.attention_date) < DATE(NOW()) AND t.state NOT IN(1,2,6) AND  t.technical = $user AND t.incidentsid = $affair ORDER BY t.id DESC";
      } elseif ($affair == 0 && $state != 0) {
        $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.technical,t.opening_date,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
          FROM tickets t
          JOIN incidents i ON t.incidentsid = i.id
          JOIN clients c ON t.clientid = c.id
          JOIN users u ON t.userid = u.id
          WHERE DATE(t.attention_date) < DATE(NOW()) AND t.technical = $user AND t.state != 0 AND t.state = $state ORDER BY t.id DESC";
      } else {
        $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.technical,t.opening_date,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
          FROM tickets t
          JOIN incidents i ON t.incidentsid = i.id
          JOIN clients c ON t.clientid = c.id
          JOIN users u ON t.userid = u.id
          WHERE DATE(t.attention_date) < DATE(NOW()) AND t.state NOT IN(1,2,6) AND t.technical = $user ORDER BY t.id DESC";
      }
    }
    $answer = $this->select_all($sql);
    return $answer;
  }
  public function list_resolved(string $closing)
  {
    $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.opening_date,t.closing_date,t.technical,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
      FROM tickets t
      JOIN incidents i ON t.incidentsid = i.id
      JOIN clients c ON t.clientid = c.id
      JOIN users u ON t.userid = u.id
      WHERE t.state = 1 AND DATE(t.closing_date) = '$closing' ORDER BY t.id DESC";
    $answer = $this->select_all($sql);
    return $answer;
  }
  public function list_records(string $state, string $user, string $affair)
  {
    if ($state != 0 && $user != 0 && $affair != 0) {
      $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.opening_date,t.closing_date,t.technical,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
          FROM tickets t
          JOIN incidents i ON t.incidentsid = i.id
          JOIN clients c ON t.clientid = c.id
          JOIN users u ON t.userid = u.id
          WHERE t.state = $state AND t.technical = $user AND t.incidentsid = $affair ORDER BY t.id DESC";
    } elseif ($state != 0 && $user == 0 && $affair == 0) {
      $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.opening_date,t.closing_date,t.technical,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
          FROM tickets t
          JOIN incidents i ON t.incidentsid = i.id
          JOIN clients c ON t.clientid = c.id
          JOIN users u ON t.userid = u.id
          WHERE t.state = $state ORDER BY t.id DESC";
    } elseif ($state == 0 && $user != 0 && $affair == 0) {
      $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.opening_date,t.closing_date,t.technical,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
          FROM tickets t
          JOIN incidents i ON t.incidentsid = i.id
          JOIN clients c ON t.clientid = c.id
          JOIN users u ON t.userid = u.id
          WHERE t.state != 0 AND t.technical = $user ORDER BY t.id DESC";
    } elseif ($state == 0 && $user == 0 && $affair != 0) {
      $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.opening_date,t.closing_date,t.technical,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
          FROM tickets t
          JOIN incidents i ON t.incidentsid = i.id
          JOIN clients c ON t.clientid = c.id
          JOIN users u ON t.userid = u.id
          WHERE t.state != 0 AND t.incidentsid = $affair ORDER BY t.id DESC";
    } elseif ($state != 0 && $user != 0 && $affair == 0) {
      $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.opening_date,t.closing_date,t.technical,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
          FROM tickets t
          JOIN incidents i ON t.incidentsid = i.id
          JOIN clients c ON t.clientid = c.id
          JOIN users u ON t.userid = u.id
          WHERE t.state = $state AND t.technical = $user ORDER BY t.id DESC";
    } elseif ($state != 0 && $user == 0 && $affair != 0) {
      $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.opening_date,t.closing_date,t.technical,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
          FROM tickets t
          JOIN incidents i ON t.incidentsid = i.id
          JOIN clients c ON t.clientid = c.id
          JOIN users u ON t.userid = u.id
          WHERE t.state = $state AND t.incidentsid = $affair ORDER BY t.id DESC";
    } elseif ($state == 0 && $user != 0 && $affair != 0) {
      $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.opening_date,t.closing_date,t.technical,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
          FROM tickets t
          JOIN incidents i ON t.incidentsid = i.id
          JOIN clients c ON t.clientid = c.id
          JOIN users u ON t.userid = u.id
          WHERE t.state != 0 AND t.technical = $user AND t.incidentsid = $affair ORDER BY t.id DESC";
    } else {
      $sql = "SELECT t.id,t.clientid,CONCAT_WS(' ', c.names, c.surnames) AS client,c.document,u.names AS user,t.attention_date,t.opening_date,t.closing_date,t.technical,t.registration_date,i.incident,t.priority,t.state,c.mobile,c.mobile_optional,c.address,c.reference,c.latitud,c.longitud
          FROM tickets t
          JOIN incidents i ON t.incidentsid = i.id
          JOIN clients c ON t.clientid = c.id
          JOIN users u ON t.userid = u.id
          WHERE t.state != 0 ORDER BY t.id DESC";
    }
    $answer = $this->select_all($sql);
    return $answer;
  }
  public function create(int $user, int $client, int $technical, int $incidents, string $description, int $priority, string $attention, string $datetime)
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
  public function modify(int $id, int $client, int $technical, int $incidents, string $description, int $priority, string $attention)
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
  public function modify_state(int $id, int $state)
  {
    $this->intId = $id;
    $this->intState = $state;
    $answer = "";
    $query = "UPDATE tickets SET state = ? WHERE id = $this->intId";
    $data = array($this->intState);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function reassign_technical(int $id, int $technical)
  {
    $this->intId = $id;
    $this->intTechnical = $technical;
    $answer = "";
    $query = "UPDATE tickets SET technical = ? WHERE id = $this->intId";
    $data = array($this->intTechnical);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function reschedule_date(int $id, string $datetime)
  {
    $this->intId = $id;
    $this->strDatetime = $datetime;
    $answer = "";
    $query = "UPDATE tickets SET attention_date = ? WHERE id = $this->intId";
    $data = array($this->strDatetime);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function open_ticket(int $id, string $datetime, int $state)
  {
    $this->intId = $id;
    $this->strDatetime = $datetime;
    $this->intState = $state;
    $answer = "";
    $query = "UPDATE tickets SET opening_date = ?,state = ? WHERE id = $this->intId";
    $data = array($this->strDatetime, $this->intState);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function close_ticket(int $id, string $datetime, int $state)
  {
    $this->intId = $id;
    $this->strDatetime = $datetime;
    $this->intState = $state;
    $answer = "";
    $query = "UPDATE tickets SET closing_date = ?,state = ? WHERE id = $this->intId";
    $data = array($this->strDatetime, $this->intState);
    $update = $this->update($query, $data);
    if ($update) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
  public function complete_ticket(int $id, int $user, string $opening_date, string $closing_date, string $observation, int $state)
  {
    $this->intId = $id;
    $this->intUser = $user;
    $this->strOpening = $opening_date;
    $this->strClosing = $closing_date;
    $this->strObservation = $observation;
    $this->strState = $state;
    $answer = "";
    $query = "INSERT INTO ticket_solution(ticketid,technicalid,opening_date,closing_date,comment,state) VALUES(?,?,?,?,?,?)";
    $data = array($this->intId, $this->intUser, $this->strOpening, $this->strClosing, $this->strObservation, $this->strState);
    $insert = $this->insert($query, $data);
    if ($insert) {
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
  public function open_gallery(int $client)
  {
    $this->intClient = $client;
    $sql = "SELECT g.type,g.registration_date,g.image,u.names FROM gallery_images g JOIN users u ON g.userid = u.id WHERE g.clientid = $this->intClient";
    $asnwer = $this->select_all($sql);
    return $asnwer;
  }
  public function show_images(int $id)
  {
    $this->intId = $id;
    $sql = "SELECT g.type,g.registration_date,g.image,u.names FROM gallery_images g JOIN users u ON g.userid = u.id WHERE g.typeid = $this->intId AND g.type = 2";
    $asnwer = $this->select_all($sql);
    return $asnwer;
  }
  public function number_images(int $id)
  {
    $this->intId = $id;
    $sql = "SELECT COUNT(*) AS total FROM gallery_images WHERE typeid = $this->intId AND type = 2";
    $answer = $this->select($sql);
    $total = $answer['total'];
    return $total;
  }
  public function select_contract(int $client)
  {
    $this->intClient = $client;
    $sql = "SELECT * FROM contracts WHERE clientid = $this->intClient";
    $answer = $this->select($sql);
    return $answer;
  }
  public function select_record(int $id)
  {
    $this->intId = $id;
    $sql = "SELECT t.id,t.userid,t.clientid,t.technical,t.incidentsid,t.description,t.priority,t.attention_date, t.opening_date,t.closing_date,t.registration_date,t.state,c.names,c.surnames,c.mobile,i.incident
        FROM tickets t
        JOIN clients c ON t.clientid = c.id
        JOIN incidents i ON t.incidentsid = i.id
        WHERE t.id = $this->intId";
    $asnwer = $this->select($sql);
    return $asnwer;
  }
  public function view_ticket(int $id)
  {
    $request = array();
    $sql_ticket = "SELECT t.id,t.userid,t.clientid,t.technical,t.incidentsid,t.description,t.priority, t.attention_date,t.opening_date,t.closing_date,t.registration_date,t.state,CONCAT_WS(' ', c.names, c.surnames) AS client,dt.document AS type_doc,c.document,c.mobile,c.mobile_optional,c.address,c.email,CONCAT_WS(' ', u.names, u.surnames) AS user,u.image AS user_image,i.incident FROM tickets t JOIN users u ON t.userid = u.id JOIN clients c ON t.clientid = c.id JOIN document_type dt ON c.documentid = dt.id JOIN incidents i ON t.incidentsid = i.id WHERE t.id = $id";
    $request_ticket = $this->select($sql_ticket);
    if (!empty($request_ticket)) {
      $sql_detail = "SELECT ts.id,ts.ticketid,ts.technicalid,ts.opening_date,ts.closing_date,ts.comment,ts.state,u.names,u.image FROM ticket_solution ts JOIN users u ON ts.technicalid = u.id WHERE ts.ticketid = $id ORDER BY ts.id ASC";
      $request_detail = $this->select_all($sql_detail);
      $sql_images = "SELECT g.type,g.registration_date,g.image,u.names FROM gallery_images g JOIN users u ON g.userid = u.id WHERE g.typeid = $id AND g.type = 2";
      $request_images = $this->select_all($sql_images);
      $request = array('ticket' => $request_ticket, 'detail' => $request_detail, 'images' => $request_images);
    }
    return $request;
  }
  public function select_client(int $client)
  {
    $this->intClient = $client;
    $sql = "SELECT * FROM clients WHERE id = $this->intClient";
    $answer = $this->select($sql);
    return $answer;
  }
  public function see_technical(int $user)
  {
    $this->intUser = $user;
    $sql = "SELECT names AS technical FROM users WHERE id = $this->intUser";
    $answer = $this->select($sql);
    $technical = $answer['technical'];
    return $technical;
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

  public function find_client(string $clientId)
  {
    return $this->createQueryBuilder()
      ->from("clients")
      ->where("id = {$clientId}")
      ->getOne();
  }

  public function list_clients()
  {
    $sql = "SELECT ct.clientid,c.names,c.surnames
      FROM contracts ct
      JOIN clients c ON ct.clientid = c.id
      WHERE ct.state != 0 AND ct.clientid IN (SELECT id FROM clients WHERE state != 0)";
    $answer = $this->select_all($sql);
    return $answer;
  }
  public function cancel(int $id)
  {
    $this->intId = $id;
    $sql = "UPDATE tickets SET state = ? WHERE id = $this->intId";
    $arrData = array(6);
    $request = $this->update($sql, $arrData);
    return $request;
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
  public function remove_image(int $id, string $imagen)
  {
    $this->intId = $id;
    $this->strImagen = $imagen;
    $answer = "";
    $query = "DELETE FROM gallery_images WHERE typeid = $this->intId AND image = '{$this->strImagen}' AND type = 2";
    $delete = $this->delete($query);
    if ($delete) {
      $answer = 'success';
    } else {
      $answer = 'error';
    }
    return $answer;
  }
}
