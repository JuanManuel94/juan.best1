<?php

class Settings extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        consent_permission(BUSINESS);
    }
    public function settings()
    {
        $data['page_name'] = "Ajustes";
        $data['page_title'] = "Ajustes del Sistema";
        $data['home_page'] = "Dashboard";
        $data['actual_page'] = "Ajustes";

        $this->views->getView($this, "settings", $data);
    }
    public function general()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_name'] = "Ajustes generales";
        $data['page_title'] = "Ajustes Generales";
        $data['home_page'] = "Dashboard";
        $data['previous_page'] = "Ajustes";
        $data['actual_page'] = "General";
        $data['options'] = business_options();
        $data['page_functions_js'] = "general.js";
        $this->views->getView($this, "general", $data);
    }
    public function database()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_name'] = "Backup";
        $data['page_title'] = "Copias de Seguridad";
        $data['home_page'] = "Dashboard";
        $data['previous_page'] = "Ajustes";
        $data['actual_page'] = "Backup";
        $data['page_functions_js'] = "database.js";
        $this->views->getView($this, "database", $data);
    }
    public function cronjobs()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_name'] = "Cronjobs";
        $data['page_title'] = "Tareas programadas";
        $data['home_page'] = "Dashboard";
        $data['previous_page'] = "Ajustes";
        $data['actual_page'] = "Cronjobs";
        $data['page_functions_js'] = "cronjobs.js";

        $data['records'] = array();

        $result = sql("SELECT * FROM cronjobs");

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

            $data['records'][] = (object) $row;
        }

        $data['frequencies'] = array();
        $data['frequencies']["1"] = "1 minuto";
        $data['frequencies']["5"] = "5 minutos";
        $data['frequencies']["10"] = "10 minutos";
        $data['frequencies']["30"] = "30 minutos";
        $data['frequencies']["60"] = "1 hora";
        $data['frequencies']["120"] = "2 hora";
        $data['frequencies']["360"] = "6 hora";
        $data['frequencies']["720"] = "12 hora";
        $data['frequencies']["1440"] = "1 día";
        $data['frequencies']["2880"] = "2 días";
        $data['frequencies']["4320"] = "3 días";
        $data['frequencies']["7200"] = "5 días";
        $data['frequencies']["10080"] = "7 días";
        $data['frequencies']["14400"] = "10 días";
        $data['frequencies']["21600"] = "15 días";
        $data['frequencies']["43200"] = "30 días";

        $data['core'] = sqlObject("SELECT * FROM cronjobs_core WHERE id = 1");

        $this->views->getView($this, "cronjobs", $data);
    }
    public function cronjob_control()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();

        if (!empty($_POST['id']) && isset($_POST['status'])) {
            sqlUpdate("cronjobs", "status", $_POST['status'], $_POST['id']);
            $res->result = "success";
        } else {
            $res->result = "failed";
            $res->message = "Invalid request";
        }

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
    public function cronjob_save_parm()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();

        if (!empty($_POST['id']) && isset($_POST['parm'])) {
            sqlUpdate("cronjobs", "parm", $_POST['parm'], $_POST['id']);
            $res->result = "success";
        } else {
            $res->result = "failed";
            $res->message = "Invalid request";
        }

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
    public function cronjob_save_frequency()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();

        if (!empty($_POST['id']) && isset($_POST['frequency'])) {
            sqlUpdate("cronjobs", "frequency", $_POST['frequency'], $_POST['id']);
            $res->result = "success";
        } else {
            $res->result = "failed";
            $res->message = "Invalid request";
        }

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
    public function cronjob_get()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();

        if (!empty($_POST['id'])) {
            $o = sqlObject("SELECT * FROM cronjobs WHERE id = " . $_POST['id']);
            $res->result = "success";
            $res->data = $o;
        } else {
            $res->result = "failed";
            $res->message = "Invalid request";
        }

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
    public function cronjob_testrun()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();

        if (!empty($_POST['id'])) {

            $t = sqlObject("SELECT * FROM cronjobs WHERE id = " . $_POST['id']);

            switch ($t->code) {
                case 'IN001':
                    send_soon_due_invoices($t);
                    break;
                case 'IN002':
                    send_expired_invoices($t);
                    break;
                case 'IN003':
                    cut_service_expired_invoices($t);
                    break;
                case 'IN004':
                    cut_service_backup($t);
                    break;
                case 'CI001':
                    reg_customer_traffic($t);
                    break;
                default:
                    break;
            }

            $his = (object) array();
            $his->cronjobid = $_POST['id'];
            $his->result = "Prueba exitosa";
            $his->date = time();
            $res->result = "success";
        } else {
            $res->result = "failed";
            $res->message = "Invalid request";
        }

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
    public function cronjob_history()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $res = (object) array();

        if (!empty($_POST['id'])) {
            $res->result = "success";
            $res->html = "";
            $res->count = 0;

            $result = sql("SELECT * FROM cronjobs_history WHERE cronjobid = " . $_POST['id'] . " ORDER BY date DESC LIMIT 20");

            setlocale(LC_TIME, 'es_ES.UTF-8', 'Spanish_Spain.UTF-8', 'Spanish');
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $res->count++;
                $res->html .= '<tr><td>' . $row["result"] . '</td><td>' . (new IntlDateFormatter('es_ES', IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'UTC', IntlDateFormatter::GREGORIAN, 'd \'de\' MMMM \'de\' yyyy, h:mm a'))->format(new DateTime('@' . $row["date"])) . '</td></tr>';
            }

            if ($res->count == 0) {
                $res->html = '<tr><td>Sin historial.</td></tr>';
            }
        } else {
            $res->result = "failed";
            $res->message = "Invalid request";
        }

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }


    public function zones()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_name'] = "Zonas";
        $data['page_title'] = "Gestión de Zonas";
        $data['home_page'] = "Dashboard";
        $data['previous_page'] = "Ajustes";
        $data['actual_page'] = "Zonas";
        $data['page_functions_js'] = "zones.js";
        $this->views->getView($this, "zones", $data);
    }
    public function client_portfolio()
    {
        if (empty($_SESSION['permits_module']['v'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_name'] = "Caetera de clientes";
        $data['page_title'] = "Gestión de Cartera";
        $data['home_page'] = "Dashboard";
        $data['previous_page'] = "Ajustes";
        $data['actual_page'] = "Cartera";
        $data['page_functions_js'] = "wallet.js";
        $this->views->getView($this, "wallet", $data);
    }
}
