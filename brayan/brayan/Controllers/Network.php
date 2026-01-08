<?php

class Network extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        consent_permission(INSTALLATIONS);
    }
    /* ROUTERS */
    public function routers()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_name'] = "Routers";
        $data['page_title'] = "Gestión de Routers";
        $data['home_page'] = "Dashboard";
        $data['actual_page'] = "Routers";
        $data['page_functions_js'] = "routers.js";

        $data['records'] = array();
        $data['zones'] = array();


        $mode_label = [];
        $mode_label["1"] = "Simple Queues";
        $mode_label["2"] = "PPPoE";

        $status_id = [];
        $status_id["0"] = "0";
        $status_id["CONNECTED"] = "1";
        $status_id["DISCONNECTED"] = "2";
        //GET ROUTERS

        $result = sql("SELECT r.*, z.id zone_id, z.name zone_name, z.mode zone_mode, (SELECT COUNT(*) FROM clients WHERE net_router = r.id) customers FROM network_routers r JOIN network_zones z ON z.id = r.zoneid");

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

            $router = new Router($row["ip"], $row["port"], $row["username"], decrypt_aes($row["password"], SECRET_IV));
            $resources = $router->APIGetSystemResources();

            if ($resources->success) {

                $row["identity"] = $router->RequestBuilder("system/identity", "GET")->data->name;
                $row["board_name"] = $resources->data->{"board-name"};
                $row["version"] = $resources->data->{"version"};
                $row["status"] = "CONNECTED";
            } else {

                $row["identity"] = "-";
                $row["board_name"] = "-";
                $row["version"] = "-";
                $row["status"] = "DISCONNECTED";
            }

            $row["status_id"] = $status_id[$row["status"]];

            $row["zone"] = $row["zone_name"] . ' (' . $mode_label[$row["zone_mode"]] . ')';

            $data['records'][] = $row;
        }

        //GET ZONES

        $result = sql("SELECT * FROM network_zones");

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

            $data['zones'][] = $row;
        }

        ////

        $this->views->getView($this, "routers", $data);
    }
    public function add_router()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();

        if (!empty($_POST['name']) && !empty($_POST['ip']) && !empty($_POST['port']) && !empty($_POST['username']) && !empty($_POST['password'])) {

            $router = new Router($_POST["ip"], $_POST["port"], $_POST["username"], $_POST["password"]);

            if ($router->connected) {
                $item = (object) array();
                $item->name = $_POST['name'];
                $item->ip = $_POST['ip'];
                $item->port = $_POST['port'];
                $item->username = $_POST['username'];
                $item->password = encrypt_aes($_POST['password'], SECRET_IV);
                $item->ip_range = $_POST['ip_range'];
                $item->zoneid = $_POST['zoneid'];

                sqlInsert("network_routers", $item);

                $res->result = "success";
            } else {

                $res->result = "failed";
                $res->message = "Could not connect to router.";
            }
        } else {
            $res->result = "failed";
            $res->message = "Invalid request";
        }

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
    public function get_edit_router()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();

        if (!empty($_POST['id'])) {

            $r = sqlObject("SELECT * FROM network_routers WHERE id = " . $_POST['id']);

            if (!is_null($r->id)) {

                $item = (object) array();

                $r->password = base64_encode(decrypt_aes($r->password, SECRET_IV));

                $res->result = "success";
                $res->data = $r;
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
    public function edit_router()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();

        if (!empty($_POST['id'])) {

            $r = sqlObject("SELECT * FROM network_routers WHERE id = " . $_POST['id']);

            if (!is_null($r->id)) {

                $router = new Router($_POST["ip"], $_POST["port"], $_POST["username"], $_POST["password"]);

                if ($router->connected) {

                    $item = (object) array();

                    if (isset($_POST['name'])) {
                        sqlUpdate("network_routers", "name", $_POST['name'], $_POST['id']);
                    }
                    if (isset($_POST['ip'])) {
                        sqlUpdate("network_routers", "ip", $_POST['ip'], $_POST['id']);
                    }
                    if (isset($_POST['port'])) {
                        sqlUpdate("network_routers", "port", $_POST['port'], $_POST['id']);
                    }
                    if (isset($_POST['username'])) {
                        sqlUpdate("network_routers", "username", $_POST['username'], $_POST['id']);
                    }
                    if (isset($_POST['password'])) {
                        sqlUpdate("network_routers", "password", encrypt_aes($_POST['password'], SECRET_IV), $_POST['id']);
                    }
                    if (isset($_POST['ip_range'])) {
                        sqlUpdate("network_routers", "ip_range", $_POST['ip_range'], $_POST['id']);
                    }
                    if (isset($_POST['zoneid'])) {
                        sqlUpdate("network_routers", "zoneid", $_POST['zoneid'], $_POST['id']);
                    }

                    $res->result = "success";
                } else {

                    $res->result = "failed";
                    $res->message = "Could not connect to router.";
                }
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
    public function remove_router()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();

        if (!empty($_POST['id'])) {

            $r = sqlObject("SELECT * FROM network_routers WHERE id = " . $_POST['id']);

            if (!is_null($r->id)) {

                sqlDelete("network_routers", $_POST['id']);

                $res->result = "success";
            } else {
                $res->result = "failed";
                $res->message = "Invalid request";
            }

            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }
    }

    public function router_system_info()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();

        if (!empty($_POST['id'])) {

            $r = sqlObject("SELECT * FROM network_routers WHERE id = " . $_POST['id']);

            if (!is_null($r->id)) {
                $router = new Router($r->ip, $r->port, $r->username, decrypt_aes($r->password, SECRET_IV));
                $rres = $router->APIGetSystemResources();

                if ($rres->success) {

                    $res->result = "success";

                    $resources = $rres->data;

                    $resources->free_memory = humanReadableBytes($resources->{"free-memory"}) . " (" . round((($resources->{"free-memory"} / $resources->{"total-memory"}) * 100), 2) . "%)";
                    $resources->total_memory = humanReadableBytes($resources->{"total-memory"});
                    $resources->cpu_count = $resources->{"cpu-count"};
                    $resources->cpu_frequency = $resources->{"cpu-frequency"};
                    $resources->cpu_load = $resources->{"cpu-load"};
                    $resources->factory_software = $resources->{"factory-software"};
                    $resources->free_hdd_space = humanReadableBytes($resources->{"free-hdd-space"}) . " (" . round((($resources->{"free-hdd-space"} / $resources->{"total-hdd-space"}) * 100), 2) . "%)";
                    $resources->total_hdd_space = humanReadableBytes($resources->{"total-hdd-space"});
                    $resources->build_time = $resources->{"build-time"};
                    $resources->board_name = $resources->{"board-name"};
                    $resources->bad_blocks = $resources->{"bad-blocks"} ?? null;
                    $resources->architecture_name = $resources->{"architecture-name"};
                    $resources->write_sect_since_reboot = $resources->{"write-sect-since-reboot"};
                    $resources->write_sect_total = $resources->{"write-sect-total"};

                    $res->html = "
                    <tr><td>Uptime</td><td>$resources->uptime</td></tr>
                    <tr><td>Free Memory</td><td>$resources->free_memory</td></tr>
                    <tr><td>Total Memory</td><td>$resources->total_memory</td></tr>
                    <tr><td>CPU</td><td>$resources->cpu</td></tr>
                    <tr><td>CPU Count</td><td>$resources->cpu_count</td></tr>
                    <tr><td>CPU Frequency</td><td>$resources->cpu_frequency</td></tr>
                    <tr><td>CPU Load</td><td>$resources->cpu_load</td></tr>
                    <tr><td>Free HDD Space</td><td>$resources->free_hdd_space</td></tr>
                    <tr><td>Total HDD Size</td><td>$resources->total_hdd_space</td></tr>
                    <tr><td>Sector Writes Since Reboot</td><td>$resources->write_sect_since_reboot</td></tr>
                    <tr><td>Total Sector Writes</td><td>$resources->write_sect_total</td></tr>
                    <tr><td>Bad Blocks</td><td>$resources->bad_blocks</td></tr>
                    <tr><td>Architecture Name</td><td>$resources->architecture_name</td></tr>
                    <tr><td>Board Name</td><td>$resources->board_name</td></tr>
                    <tr><td>Version</td><td>$resources->version</td></tr>
                    <tr><td>Build Time</td><td>$resources->build_time</td></tr>
                    <tr><td>Factory Software</td><td>$resources->factory_software</td></tr>
                    ";
                } else {

                    $res->result = "failed";
                    $res->html = "
                    <tr><td>Could not connect with Router</td><td></td></tr>";
                }
            }
        }

        ////

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    public function regla_moroso()
    {
        $r = sqlObject("SELECT * FROM network_routers WHERE id = " . $_POST['id']);

        if ($r && !empty($r->id)) {
            $router_ip = $r->ip;
            $router_port = $r->port;
            $username = $r->username;
            $password = decrypt_aes($r->password, SECRET_IV);

            // Obtener reglas actuales del firewall
            $existing_rules = $this->getExistingRules("http://$router_ip:$router_port/rest/ip/firewall/filter", $username, $password);

            $firewall_rules = [
                ["action" => "accept", "chain" => "forward", "comment" => "REGLA CREADA DESDE EL SISTEMA - Permitir HTTPS a página de corte", "dst-address" => "46.202.183.172", "dst-port" => "443", "protocol" => "tcp", "src-address-list" => "moroso"],
                ["action" => "accept", "chain" => "forward", "comment" => "REGLA CREADA DESDE EL SISTEMA - Permitir acceso a página de corte", "dst-port" => "53", "protocol" => "udp", "src-address-list" => "moroso"],
                ["action" => "accept", "chain" => "forward", "comment" => "REGLA CREADA DESDE EL SISTEMA - Permitir acceso a página de corte", "protocol" => "udp", "src-address-list" => "moroso", "src-port" => "53"],
                ["action" => "drop", "chain" => "forward", "comment" => "REGLA CREADA DESDE EL SISTEMA - Bloqueo de Internet para morosos", "src-address-list" => "moroso"],
                ["action" => "drop", "chain" => "forward", "comment" => "REGLA CREADA DESDE EL SISTEMA - Bloqueo HTTPS para morosos", "dst-port" => "443", "protocol" => "tcp", "src-address-list" => "moroso"],
                ["action" => "drop", "chain" => "forward", "comment" => "REGLA CREADA DESDE EL SISTEMA - Bloqueo HTTP para morosos", "dst-port" => "80", "protocol" => "tcp", "src-address-list" => "moroso"]
            ];

            foreach ($firewall_rules as $rule) {
                if (!$this->ruleExists($rule['comment'], $existing_rules)) {
                    $url = "http://$router_ip:$router_port/rest/ip/firewall/filter";
                    $this->sendRequest($url, $username, $password, $rule);
                }
            }

            // Obtener reglas actuales del NAT
            $existing_nat = $this->getExistingRules("http://$router_ip:$router_port/rest/ip/firewall/nat", $username, $password);

            $nat_rule = [
                "action" => "redirect",
                "chain" => "dstnat",
                "comment" => "REGLA CREADA DESDE EL SISTEMA - Redirección Web Proxy para morosos",
                "dst-port" => "80",
                "protocol" => "tcp",
                "src-address-list" => "moroso",
                "to-ports" => "3128"
            ];

            if (!$this->ruleExists($nat_rule['comment'], $existing_nat)) {
                $url_nat = "http://$router_ip:$router_port/rest/ip/firewall/nat";
                $this->sendRequest($url_nat, $username, $password, $nat_rule);
            }

            // Obtener reglas actuales del proxy
            $existing_proxy = $this->getExistingRules("http://$router_ip:$router_port/rest/ip/proxy/access", $username, $password);

            $proxy_rules = [
                ["action" => "allow", "dst-address" => "46.202.183.20", "comment" => "REGLA CREADA DESDE EL SISTEMA - Permitir acceso"],
                ["action" => "redirect", "dst-port" => "80", "action-data" => "corte.globalsn.pe", "comment" => "REGLA CREADA DESDE EL SISTEMA - Redirección Web Proxy"]
            ];

            foreach ($proxy_rules as $rule) {
                if (!$this->ruleExists($rule['comment'], $existing_proxy)) {
                    $url_proxy = "http://$router_ip:$router_port/rest/ip/proxy/access";
                    $this->sendRequest($url_proxy, $username, $password, $rule);
                }
            }

            echo json_encode(["result" => "success", "message" => "Reglas aplicadas correctamente."]);
            exit;
        }

        echo json_encode(["result" => "failed", "message" => "Router no encontrado."]);
        exit;
    }

    private function sendRequest($url, $username, $password, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    private function getExistingRules($url, $username, $password)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true) ?: [];
    }

    private function ruleExists($comment, $existing_rules)
    {
        foreach ($existing_rules as $rule) {
            if (isset($rule['comment']) && $rule['comment'] === $comment) {
                return true;
            }
        }
        return false;
    }

    public function router_reboot()
    {
        $r = sqlObject("SELECT * FROM network_routers WHERE id = " . $_POST['id']);

        if ($r && !empty($r->id)) {
            $url = "http://{$r->ip}:{$r->port}/rest/system/reboot";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_USERPWD, "{$r->username}:" . decrypt_aes($r->password, SECRET_IV));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
            $response = curl_exec($ch);
            curl_close($ch);

            echo json_encode(["result" => "success", "message" => "El MikroTik se está reiniciando."]);
            exit;
        }

        echo json_encode(["result" => "failed", "message" => "Router no encontrado."]);
        exit;
    }


    public function router_system_interface()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();

        if (!empty($_POST['id'])) {

            $r = sqlObject("SELECT * FROM network_routers WHERE id = " . $_POST['id']);

            if (!is_null($r->id)) {
                $router = new Router($r->ip, $r->port, $r->username, decrypt_aes($r->password, SECRET_IV));

                $ireq = $router->RequestBuilder("interface", "GET");
                if ($ireq->success) {
                    $res->result = "success";
                    $res->interface = $ireq->data;
                } else {
                    $res->result = "failed";
                    $res->message = "Could not connect";
                }
            }
        }

        ////

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
    public function router_system_log()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();

        if (!empty($_POST['id'])) {

            $r = sqlObject("SELECT * FROM network_routers WHERE id = " . $_POST['id']);

            if (!is_null($r->id)) {
                $router = new Router($r->ip, $r->port, $r->username, decrypt_aes($r->password, SECRET_IV));

                $res->result = "success";
                $res->log = $router->RequestBuilder("log", "GET")->data;

                $res->html = "";

                foreach ($res->log as $k => $log) {
                    $error = (str_contains($log->topics, "error") ? 'class="log-error"' : '');
                    //$log->time = DateTime::createFromFormat('m-d H:i:s', $log->time)->format('Y-m-d H:i:s');
                    $res->html .= "<tr $error><td>$log->time</td><td>$log->topics</td><td>$log->message</td></tr>";
                }
            }
        }

        ////

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
    public function router_available_ips()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();
        $querySearch = isset($_POST['querySearch']) ? $_POST['querySearch'] : null;

        if (!empty($_POST['id'])) {

            $id = $_POST['id'];
            $r = sqlObject("SELECT * FROM network_routers WHERE id = " . $id);

            if (!is_null($r->id)) {
                if (!empty($r->ip_range)) {
                    $lines = explode("\n", $r->ip_range);

                    $res = [];
                    $exl = array();

                    $result = sql("SELECT c.net_ip ip FROM clients c WHERE net_router = $r->id");

                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                        $exl[] = $row["ip"];
                    }


                    foreach ($lines as $k => $ip) {
                        $res = array_merge($res, getAvailableIPs($ip, $exl));
                    }

                    if ($querySearch) {
                        $res = array_filter($res, function ($id) use ($querySearch) {
                            return strpos($id, $querySearch) !== false;
                        });
                    }
                }
            }
        }

        $this->json($res);
    }

    /* ZONES */
    public function zones()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_name'] = "Zonas";
        $data['page_title'] = "Gestión de Zonas";
        $data['home_page'] = "Dashboard";
        $data['actual_page'] = "Zonas";
        $data['page_functions_js'] = "zones.js";

        $data['records'] = array();

        //GET ROUTERS

        $result = sql("SELECT z.*, (SELECT COUNT(*) FROM network_routers r WHERE r.zoneid = z.id) routers FROM network_zones z;");

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

            $data['records'][] = $row;
        }

        ////

        $this->views->getView($this, "zones", $data);
    }
    public function add_zone()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();

        if (!empty($_POST['name']) && !empty($_POST['mode'])) {

            $item = (object) array();
            $item->name = $_POST['name'];
            $item->mode = $_POST['mode'];

            sqlInsert("network_zones", $item);

            $res->result = "success";
        } else {
            $res->result = "failed";
            $res->message = "Invalid request";
        }

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
    public function get_edit_zone()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();

        if (!empty($_POST['id'])) {

            $r = sqlObject("SELECT * FROM network_zones WHERE id = " . $_POST['id']);

            if (!is_null($r->id)) {

                $res->result = "success";
                $res->data = $r;
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
    public function edit_zone()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();

        if (!empty($_POST['id'])) {

            $r = sqlObject("SELECT * FROM network_zones WHERE id = " . $_POST['id']);

            if (!is_null($r->id)) {

                $item = (object) array();

                if (isset($_POST['name'])) {
                    sqlUpdate("network_zones", "name", $_POST['name'], $_POST['id']);
                }
                if (isset($_POST['mode'])) {
                    sqlUpdate("network_zones", "mode", $_POST['mode'], $_POST['id']);
                }

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
    public function remove_zone()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();

        if (!empty($_POST['id'])) {

            $r = sqlObject("SELECT * FROM network_zones WHERE id = " . $_POST['id']);

            if (!is_null($r->id)) {


                $c = sqlObject("SELECT COUNT(*) count FROM network_routers WHERE zoneid = " . $_POST['id'])->count;

                if ($c == 0) {
                    sqlDelete("network_zones", $_POST['id']);

                    $res->result = "success";
                } else {
                    $res->result = "failed";
                    $res->message = "This zone have routers assigned";
                }
            } else {
                $res->result = "failed";
                $res->message = "Invalid request";
            }

            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }
    }

    public function network_template()
    {
        return $this->views->getView($this, "template", []);
    }

    public function network_ip_template()
    {
        return $this->views->getView($this, "ip-template", []);
    }
}
