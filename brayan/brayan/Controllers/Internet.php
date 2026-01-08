<?php

class Internet extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        consent_permission(SERVICES);
    }
    public function list_records()
    {
        if ($_SESSION['permits_module']['v']) {
            $n = 1;
            $data = $this->model->list_records();
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['n'] = $n++;
                $data[$i]['clients'] = '<span class="badge label-warning f-s-12">' . $this->model->clients($data[$i]['id']) . '</span>';
                $data[$i]['price'] = $_SESSION['businessData']['symbol'] . format_money($data[$i]['price']);
                $data[$i]['max_rise'] = "<strong class='mr-1'><i class='fa fa-arrow-up text-green mr-1'></i>" . $data[$i]['rise'] . "</strong>" . $data[$i]['rise_type'];
                $data[$i]['max_descent'] = "<strong class='mr-1'><i class='fa fa-arrow-down text-danger mr-1'></i>" . $data[$i]['descent'] . "</strong>" . $data[$i]['descent_type'];
                if ($_SESSION['permits_module']['a']) {
                    $update = '<a href="javascript:;" class="blue" data-toggle="tooltip" data-original-title="Editar" onclick="update(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-pencil-alt"></i></a><a href="javascript:;" class="orange" data-toggle="tooltip" data-original-title="Actualizar Planes en Routers" onclick="update_routers(\'' . encrypt($data[$i]['id']) . '\')"><i class="fa fa-network-wired"></i></a>';
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
    public function select_record(string $idservices)
    {
        if ($_SESSION['permits_module']['v']) {
            $idservices = decrypt($idservices);
            $idservices = intval($idservices);
            if ($idservices > 0) {
                $data = $this->model->select_record($idservices);
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
    public function list_internet()
    {
        $html = "";
        $arrData = $this->model->list_internet();
        if (count($arrData) > 0) {
            for ($i = 0; $i < count($arrData); $i++) {
                if ($arrData[$i]['state'] == 1) {
                    $html .= '<option value="' . encrypt($arrData[$i]['id']) . '">' . $arrData[$i]['service'] . '</option>';
                }
            }
        }
        echo $html;
        die();
    }
    public function action()
    {
        if ($_POST) {
            if (empty($_POST['service'])) {
                $response = array("status" => 'error', "msg" => 'Campos señalados son obligatorios.');
            } else {

                $id = decrypt($_POST['idservices']);
                $id = intval($id);
                $service = strtoupper(strClean($_POST['service']));
                $type = intval(1);
                $rise = intval(strClean($_POST['rise']));
                $rise_select = strClean($_POST['rise_select']);
                $descent = intval(strClean($_POST['descent']));
                $descent_select = strClean($_POST['descent_select']);
                $price = empty($_POST['price']) ? 0 : strClean($_POST['price']);
                $details = strtoupper(strClean($_POST['details']));
                $state = intval(strClean($_POST['listStatus']));
                $profile = strClean($_POST['profile']);
                $datetime = date("Y-m-d H:i:s");
                $routers = !array_key_exists('routersList', $_POST) ? "" : (is_array($_POST['routersList']) ? implode(',', $_POST['routersList']) : $_POST['routersList']);

                if ($id == 0) {
                    $option = 1;
                    $total = $this->model->returnCode();
                    if ($total == 0) {
                        $code = "S00001";
                    } else {
                        $max = $this->model->generateCode();
                        $code = "S" . substr((substr($max, 1) + 100001), 1);
                    }
                    if ($_SESSION['permits_module']['r']) {
                        $request = $this->model->create($code, $service, $type, $rise, $rise_select, $descent, $descent_select, $price, $details, $datetime, $state, $routers, $profile);
                    }
                } else {
                    $option = 2;
                    if ($_SESSION['permits_module']['a']) {
                        $request = $this->model->modify($id, $service, $rise, $rise_select, $descent, $descent_select, $price, $details, $state, $routers, $profile);
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
                $idservices = decrypt($_POST['idservices']);
                $idservices = intval($idservices);
                $request = $this->model->remove($idservices);
                if ($request == 'success') {
                    $response = array('status' => 'success', 'msg' => 'El registro se ha eliminado.');
                } else if ($request == 'exists') {
                    $response = array('status' => 'exists', 'msg' => 'El servicio esta en uso, imposible eliminar');
                } else {
                    $response = array('status' => 'error', 'msg' => 'Error no se pudo eliminar.');
                }
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }
    public function update_router_plans()
    {
        if (empty($_POST['id'])) {
            return $this->json([
                "success" => false,
                "message" => "El id del plan es obligatorio"
            ]);
        }

        $id = decrypt($_POST['id']);
        $plan = sqlObject("SELECT s.* FROM `services` s WHERE s.id = $id");

        if (is_null($plan->id)) {
            return $this->json([
                "success" => false,
                "message" => "No se encontró el plan"
            ]);
        }

        try {
            $networks = $this->model->createQueryBuilder()
                ->from("network_routers nr")
                ->innerJoin("clients cl", "cl.net_router = nr.id")
                ->innerJoin("contracts c", "c.clientid = cl.id")
                ->innerJoin("detail_contracts dc", "dc.serviceid = $id AND dc.contractid = c.id")
                ->groupBy("nr.id, nr.name, nr.ip, nr.port, nr.username, nr.password")
                ->select("nr.id, nr.name, nr.ip, nr.port, nr.username, nr.password")
                ->getMany();

            // verificar si hay conexión con los routers
            foreach ($networks as $network) {
                $network = (object) $network;
                $router = new Router(
                    $network->ip,
                    $network->port,
                    $network->username,
                    decrypt_aes(
                        $network->password,
                        SECRET_IV
                    )
                );

                if ($router->connected != true) {
                    return $this->json([
                        "success" => false,
                        "message" => "No se pudo conectar al mikrotik: {$network->name}"
                    ]);
                }
            }

            // obtener todos los clientes conectados al router
            $clients = $this->model->createQueryBuilder()
                ->from("clients", "cl")
                ->select("cl.*")
                ->addSelect("nr.ip", "network_ip")
                ->addSelect("nr.port", "network_port")
                ->addSelect("nr.username", "network_username")
                ->addSelect("nr.password", "network_password")
                ->innerJoin("network_routers nr", "cl.net_router = nr.id")
                ->innerJoin("contracts c", "c.clientid = cl.id")
                ->innerJoin("detail_contracts dc", "dc.serviceid = $id AND dc.contractid = c.id")
                ->getMany();

            foreach ($clients as $client) {
                $client = (object) $client;
                $router = new Router(
                    $client->network_ip,
                    $client->network_port,
                    $client->network_username,
                    decrypt_aes(
                        $client->network_password,
                        SECRET_IV
                    )
                );

                $sqr = $router->APIGetQueuesSimple($client->net_ip);

                if ($sqr->success && count($sqr->data) > 0) {
                    $sq = $sqr->data[0];
                    $maxlimit = $plan->rise . "M/" . $plan->descent . "M";

                    $router->APIModifyQueuesSimple(
                        $sq->{".id"},
                        $client->net_name,
                        $client->net_ip,
                        $maxlimit
                    );
                }
            }

            return $this->json([
                "success" => true,
                "message" => "Los cambios se aplicarón correctamente"
            ]);
        } catch (\Throwable $th) {
            return $this->json([
                "success" => false,
                "message" => "No se pudo aplicar los cambios"
            ]);
        }
    }
}
