<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'Libraries/phpmailer/Exception.php';
require 'Libraries/phpmailer/PHPMailer.php';
require 'Libraries/phpmailer/SMTP.php';
require 'Libraries/dompdf/vendor/autoload.php';
use Dompdf\Dompdf;
require_once("Countries.php");
function base_url()
{
    return BASE_URL;
}
function base_style()
{
    return BASE_URL . "/Assets";
}
function head($data = "")
{
    $view = "Views/Resources/includes/head.php";
    require_once($view);
}
function footer($data = "")
{
    $view = "Views/Resources/includes/footer.php";
    require_once($view);
}
function modal(string $name, $data = "")
{
    $route_modal = "Views/Resources/modals/{$name}.php";
    require_once $route_modal;
}
function form(string $name, $data = "")
{
    $route_form = "Views/Resources/forms/{$name}.php";
    require_once $route_form;
}
function views(string $name, $data = "")
{
    ob_start();
    $route = "Views/Resources/template/{$name}.php";
    require_once($route);
    $document = ob_get_clean();
    return $document;
}
function redirect_file(string $url, $data = "")
{
    require_once("Views/{$url}.php");
}
function redirect_pdf(string $url, $data = "")
{
    ob_start();
    require_once("Views/{$url}.php");
    $document = ob_get_clean();
    return $document;
}
function encrypt($string)
{
    $output = FALSE;
    $key = hash('sha256', SECRET_KEY);
    $iv = substr(hash('sha256', SECRET_IV), 0, 16);
    $output = openssl_encrypt($string, METHOD, $key, 0, $iv);
    $output = base64_encode($output);
    return $output;
}
function decrypt($string)
{
    $key = hash('sha256', SECRET_KEY);
    $iv = substr(hash('sha256', SECRET_IV), 0, 16);
    $output = openssl_decrypt(base64_decode($string), METHOD, $key, 0, $iv);
    return $output;
}
function format_money($amount)
{
    if (!$amount)
        return 0;
    $amount = number_format($amount, 2, SPD, SPM);
    return $amount;
}
function round_out($number, $decimals)
{
    $factor = pow(10, $decimals);
    $result = (round($number * $factor) / $factor);
    return $result;
}
function time_elapsed($date)
{
    $timestamp = strtotime($date);
    $strTime = array("segundo", "minuto", "hora", "dia", "mes", "año");
    $length = array("60", "60", "24", "30", "12", "10");
    $currentTime = time();
    if ($currentTime >= $timestamp) {
        $diff = time() - $timestamp;
        for ($i = 0; $diff >= $length[$i] && $i < count($length) - 1; $i++) {
            $diff = $diff / $length[$i];
        }
        $diff = round($diff);
        if ($diff == 1) {
            $result = "Hace " . $diff . " " . $strTime[$i];
        } else {
            $result = "Hace " . $diff . " " . $strTime[$i] . "s";
        }
        return $result;
    }
}
function ticket_duration($start_date, $end_date)
{
    $start = strtotime($start_date);
    $end = strtotime($end_date);
    $strTime = array("segundo", "minuto", "hora", "dia", "mes", "año");
    $length = array("60", "60", "24", "30", "12", "10");

    if ($end >= $start) {
        $diff = $end - $start;
        for ($i = 0; $diff >= $length[$i] && $i < count($length) - 1; $i++) {
            $diff = $diff / $length[$i];
        }
        $diff = round($diff);
        if ($diff == 1) {
            $result = $diff . " " . $strTime[$i];
        } else {
            $result = $diff . " " . $strTime[$i] . "s";
        }
        return $result;
    }
}
function months__2()
{
    $months = array(
        "1" => "Enero"
        ,
        "2" => "Febrero"
        ,
        "3" => "Marzo"
        ,
        "4" => "Abril"
        ,
        "5" => "Mayo"
        ,
        "6" => "Junio"
        ,
        "7" => "Julio"
        ,
        "8" => "Agosto"
        ,
        "9" => "Septiembre"
        ,
        "10" => "Octubre"
        ,
        "11" => "Noviembre"
        ,
        "12" => "Diciembre"
    );
    return $months;
}
function months()
{
    $months = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    return $months;
}
function day_name($date)
{
    $days = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
    $day = $days[date('w', strtotime($date))];
    $day = utf8_decode($day);
    return $day;
}
function date_letters($date)
{
    $day = day_name($date);
    $number_day = date("j", strtotime($date));
    $year = date("Y", strtotime($date));
    $months = months();
    $months = $months[(date('m', strtotime($date)) * 1) - 1];
    return $day . ', ' . $number_day . ' de ' . $months . ' del ' . $year;
}
function token()
{
    $r1 = bin2hex(random_bytes(10));
    $r2 = bin2hex(random_bytes(10));
    $r3 = bin2hex(random_bytes(10));
    $r4 = bin2hex(random_bytes(10));
    $token = $r1 . $r2 . $r3 . $r4;
    return $token;
}
function upload_image(string $file, array $data, string $name)
{
    $url_temp = $data['tmp_name'];
    $destiny = 'Assets/uploads/' . $file . '/' . $name;
    $move = move_uploaded_file($url_temp, $destiny);
    return $move;
}
function delete_image(string $file, string $name)
{
    $file_address = 'Assets/uploads/' . $file . '/' . $name;
    if (file_exists($file_address)) {
        unlink('Assets/uploads/' . $file . '/' . $name);
    }
}
function countrySelector($defaultCountry = "")
{
    global $countryArray;
    $output = "";
    foreach ($countryArray as $code => $country) {
        $code_c = $country["code"];
        $countryName = ucwords(strtolower($country["name"]));
        $output .= "<option value='" . $code_c . "' " . (($code_c == strtoupper($defaultCountry)) ? "selected" : "") . ">" . $countryName . " (+" . $country["code"] . ")</option>";
    }
    return $output;
}
function generate_password($length = 10)
{
    $password = "";
    $password_length = $length;
    $chain = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
    $chainlength = strlen($chain);
    for ($i = 1; $i <= $password_length; $i++) {
        $pos = rand(0, $chainlength - 1);
        $password .= substr($chain, $pos, 1);
    }
    return $password;
}
function debug($data)
{
    $format = print_r('<pre>');
    $format .= print_r($data);
    $format .= print_r('</pre>');
    return $format;
}
function strClean($strCadena)
{
    $string = preg_replace(['/\s+/', '/^\s|\s$/'], [' ', ''], $strCadena);
    $string = trim($string);
    $string = stripslashes($string);
    $string = str_ireplace("<script>", "", $string);
    $string = str_ireplace("</script>", "", $string);
    $string = str_ireplace("<script src>", "", $string);
    $string = str_ireplace("<script type=>", "", $string);
    $string = str_ireplace("SELECT * FROM", "", $string);
    $string = str_ireplace("DELETE FROM", "", $string);
    $string = str_ireplace("INSERT INTO", "", $string);
    $string = str_ireplace("SELECT COUNT(*) FROM", "", $string);
    $string = str_ireplace("DROP TABLE", "", $string);
    $string = str_ireplace("OR '1'='1", "", $string);
    $string = str_ireplace('OR "1"="1"', "", $string);
    $string = str_ireplace('OR ´1´=´1´', "", $string);
    $string = str_ireplace("is NULL; --", "", $string);
    $string = str_ireplace("is NULL; --", "", $string);
    $string = str_ireplace("LIKE '", "", $string);
    $string = str_ireplace('LIKE "', "", $string);
    $string = str_ireplace("LIKE ´", "", $string);
    $string = str_ireplace("OR 'a'='a", "", $string);
    $string = str_ireplace('OR "a"="a', "", $string);
    $string = str_ireplace("OR ´a´=´a", "", $string);
    $string = str_ireplace("OR ´a´=´a", "", $string);
    $string = str_ireplace("^", "", $string);
    $string = str_ireplace("[", "", $string);
    $string = str_ireplace("]", "", $string);
    $string = str_ireplace("==", "", $string);
    $string = str_ireplace('"', "", $string);
    $string = str_ireplace("''", "", $string);
    return $string;
}
function clear_cadena(string $cadena)
{
    //Reemplazamos la A y a
    $cadena = str_replace(
        array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
        array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
        $cadena
    );
    //Reemplazamos la E y e
    $cadena = str_replace(
        array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
        array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
        $cadena
    );
    //Reemplazamos la I y i
    $cadena = str_replace(
        array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
        array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
        $cadena
    );
    //Reemplazamos la O y o
    $cadena = str_replace(
        array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
        array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
        $cadena
    );
    //Reemplazamos la U y u
    $cadena = str_replace(
        array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
        array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
        $cadena
    );
    //Reemplazamos la N, n, C y c
    $cadena = str_replace(
        array('Ñ', 'ñ', 'Ç', 'ç'),
        array('N', 'n', 'C', 'c'),
        $cadena
    );
    return $cadena;
}
function filesize_formatted($path)
{
    $size = filesize($path);
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}
function numbers_letters($xcifra, $money = '', $money_plural = '')
{
    $xarray = array(
        0 => "Cero",
        1 => "UN",
        "DOS",
        "TRES",
        "CUATRO",
        "CINCO",
        "SEIS",
        "SIETE",
        "OCHO",
        "NUEVE",
        "DIEZ",
        "ONCE",
        "DOCE",
        "TRECE",
        "CATORCE",
        "QUINCE",
        "DIECISEIS",
        "DIECISIETE",
        "DIECIOCHO",
        "DIECINUEVE",
        "VEINTI",
        30 => "TREINTA",
        40 => "CUARENTA",
        50 => "CINCUENTA",
        60 => "SESENTA",
        70 => "SETENTA",
        80 => "OCHENTA",
        90 => "NOVENTA",
        100 => "CIENTO",
        200 => "DOSCIENTOS",
        300 => "TRESCIENTOS",
        400 => "CUATROCIENTOS",
        500 => "QUINIENTOS",
        600 => "SEISCIENTOS",
        700 => "SETECIENTOS",
        800 => "OCHOCIENTOS",
        900 => "NOVECIENTOS"
    );
    $xcifra = trim($xcifra);
    $xlength = strlen($xcifra);
    $xpos_punto = strpos($xcifra, ".");
    $xaux_int = $xcifra;
    $xdecimales = "00";
    if (!($xpos_punto === false)) {
        if ($xpos_punto == 0) {
            $xcifra = "0" . $xcifra;
            $xpos_punto = strpos($xcifra, ".");
        }
        $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a convertir
        $xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
    }
    $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
    $xcadena = "";
    for ($xz = 0; $xz < 3; $xz++) {
        $xaux = substr($XAUX, $xz * 6, 6);
        $xi = 0;
        $xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
        $xexit = true; // bandera para controlar el ciclo del While
        while ($xexit) {
            if ($xi == $xlimite) { // si ya ha llegado al límite máximo de enteros
                break; // termina el ciclo
            }

            $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
            $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
            for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
                switch ($xy) {
                    case 1: // checa las centenas
                        if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
                        } else {
                            $key = (int) substr($xaux, 0, 3);
                            if (TRUE === array_key_exists($key, $xarray)) {  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
                                $xseek = $xarray[$key];
                                $xsub = subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                                if (substr($xaux, 0, 3) == 100)
                                    $xcadena = " " . $xcadena . " CIEN " . $xsub;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                            } else { // entra aquí si la centena no es numero redondo (101, 253, 120, 980, etc.)
                                $key = (int) substr($xaux, 0, 1) * 100;
                                $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                $xcadena = " " . $xcadena . " " . $xseek;
                            } // ENDIF ($xseek)
                        } // ENDIF (substr($xaux, 0, 3) < 100)
                        break;
                    case 2: // Chequear las decenas (con la misma lógica que las centenas)
                        if (substr($xaux, 1, 2) < 10) {

                        } else {
                            $key = (int) substr($xaux, 1, 2);
                            if (TRUE === array_key_exists($key, $xarray)) {
                                $xseek = $xarray[$key];
                                $xsub = subfijo($xaux);
                                if (substr($xaux, 1, 2) == 20)
                                    $xcadena = " " . $xcadena . " VEINTE " . $xsub;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                $xy = 3;
                            } else {
                                $key = (int) substr($xaux, 1, 1) * 10;
                                $xseek = $xarray[$key];
                                if (20 == substr($xaux, 1, 1) * 10)
                                    $xcadena = " " . $xcadena . " " . $xseek;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " Y ";
                            } // ENDIF ($xseek)
                        } // ENDIF (substr($xaux, 1, 2) < 10)
                        break;
                    case 3: // Chequear las unidades
                        if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada
                        } else {
                            $key = (int) substr($xaux, 2, 1);
                            $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
                            $xsub = subfijo($xaux);
                            $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                        } // ENDIF (substr($xaux, 2, 1) < 1)
                        break;
                } // END SWITCH
            } // END FOR
            $xi = $xi + 3;
        } // ENDDO

        if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
            $xcadena .= " DE";

        if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
            $xcadena .= " DE";

        // ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
        if (trim($xaux) != "") {
            switch ($xz) {
                case 0:
                    if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                        $xcadena .= "UN BILLON ";
                    else
                        $xcadena .= " BILLONES ";
                    break;
                case 1:
                    if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                        $xcadena .= "UN MILLON ";
                    else
                        $xcadena .= " MILLONES ";
                    break;
                case 2:
                    if ($xcifra < 1) {
                        $xcadena = "CERO Y $xdecimales/100 " . strtoupper($money_plural);  // borrar en caso no se desee decimales /100
                    }
                    if ($xcifra >= 1 && $xcifra < 2) {
                        $xcadena = "UNO Y $xdecimales/100 " . strtoupper($money);   // borrar en caso no se desee decimales /100
                    }
                    if ($xcifra >= 2) {
                        $xcadena .= " Y $xdecimales/100 " . strtoupper($money_plural);  // borrar en caso no se desee decimales /100
                    }
                    break;
            } // endswitch ($xz)
        } // ENDIF (trim($xaux) != "")

        $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
        $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
        $xcadena = str_replace("UN UN", "UN ", $xcadena); // quito la duplicidad
        $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
        $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
        $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
        $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
    } // ENDFOR ($xz)
    $xcadena = str_replace("UN MIL ", "MIL ", $xcadena); // quito el BUG de UN MIL
    return trim($xcadena);
}
function subfijo($xx)
{
    $xx = trim($xx);
    $xstrlen = strlen($xx);
    if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
        $xsub = "";
    if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
        $xsub = "MIL";
    return $xsub;
}
function generate_qr($name_qr, $size, $type, $framework)
{
    require_once('Libraries/phpqrcode/qrlib.php');
    if (!empty($name_qr)) {
        ob_start();
        QRcode::png($name_qr, false, $type, $size, $framework);
        $imageString = base64_encode(ob_get_contents());
        ob_end_clean();
        $qr = 'data:image/jpeg;base64,' . $imageString;
        return $qr;
    }
}
function generate_qr_image($name_qr, $route_qr, $size, $type, $framework)
{
    require_once('Libraries/phpqrcode/qrlib.php');
    if (!empty($name_qr)) {
        $route = "Assets/uploads/qr/";
        if (file_exists($route)) {
            QRcode::png($name_qr, $route_qr, $type, $size, $framework);
            $qr = base_url() . "/" . $route_qr;
            echo $qr;
        }
    }
}
function user_session(int $user)
{
    require_once("Models/LoginModel.php");
    $object = new LoginModel();
    $request = $object->login_session($user);
    return $request;
}
function business_session()
{
    require_once("Models/BusinessModel.php");
    $object = new BusinessModel();
    $request = $object->show_business();
    return $request;
}
function consent_permission(int $module)
{
    require_once("Models/PermissionsModel.php");
    $object = new PermissionsModel();
    $profile = $_SESSION['userData']['profileid'];
    $arrPermits = $object->module_permissions($profile);
    $permits = '';
    $permits_module = '';
    if (count($arrPermits) > 0) {
        $permits = $arrPermits;
        $permits_module = isset($arrPermits[$module]) ? $arrPermits[$module] : "";
    }
    $_SESSION['permits'] = $permits;
    $_SESSION['permits_module'] = $permits_module;
}
function business_options()
{
    require_once("Libraries/Core/Mysql.php");
    $con = new Mysql();
    $request = array();
    $sql_currencys = "SELECT * FROM currency WHERE state != 0";
    $request_currencys = $con->select_all($sql_currencys);
    $request = array('currencys' => $request_currencys);
    return $request;
}
function contract_information(int $contract)
{
    require_once("Libraries/Core/Mysql.php");
    $con = new Mysql();
    $request = array();
    $sql_contract = "SELECT * FROM contracts WHERE id = $contract";
    $request_contract = $con->select($sql_contract);
    if (!empty($request_contract)) {
        $contract_date = date("Y-m-d", strtotime($request_contract['contract_date']));
        $contract_id = $request_contract['id'];
        $client = $request_contract['clientid'];
        $sql_client = "SELECT c.*, ap.nombre ap_cliente_nombre, CONCAT(nap.nombre, CONCAT('-', cnc.puerto)) nap_cliente_nombre
            FROM clients c
            LEFT JOIN ap_clientes ap ON ap.id = c.ap_cliente_id 
            LEFT JOIN caja_nap_clientes cnc ON cnc.id = c.nap_cliente_id
            LEFT JOIN caja_nap nap ON nap.id = cnc.nap_id
            WHERE c.id = $client";
        $request_client = $con->select($sql_client);
        $sql_pending = "SELECT COUNT(id) AS pending FROM bills WHERE clientid = $client AND state != 4 AND type = 2 ";
        $request_pending = $con->select($sql_pending);
        $pending = $request_pending['pending'];
        $sql_bill = "SELECT * FROM bills WHERE clientid = $client AND state != 4 AND type = 2 ORDER BY id DESC LIMIT 1";
        $request_bill = $con->select($sql_bill);
        $sql_debt = "SELECT COALESCE(SUM(remaining_amount),0) AS debt FROM bills WHERE clientid = $client AND state NOT IN(1,4)";
        $request_debt = $con->select($sql_debt);
        $debt = $request_debt['debt'];
        $request = array('contract' => $request_contract, 'client' => $request_client, 'current_debt' => $debt, 'bill' => $request_bill, 'pending' => $pending);
    }
    return $request;
}
function kardex_detail(int $product)
{
    require_once("Libraries/Core/Mysql.php");
    $con = new Mysql();
    $request = array();
    $sql_product = "SELECT *FROM products WHERE id = $product";
    $request_product = $con->select($sql_product);
    if (!empty($request_product)) {
        $sql_income = "SELECT COALESCE(SUM(quantity_income),0) AS input_amount,COALESCE(SUM(unit_price),0) AS total_input FROM income WHERE productid = $product";
        $request_income = $con->select($sql_income);
        $sql_departure = "SELECT COALESCE(SUM(quantity_departures),0) AS output_quantity,COALESCE(SUM(unit_price),0) AS total_output FROM departures WHERE productid = $product";
        $request_departure = $con->select($sql_departure);
        $sql_inventary = "SELECT COALESCE(MIN(quantity_income),0) AS initial_inventory FROM income WHERE productid = $product";
        $request_inventary = $con->select($sql_inventary);
        $inventary = $request_inventary['initial_inventory'];
        $sql_detail = "SELECT date,type,price,quantity,description,total FROM
    				((SELECT e.income_date AS date ,'ENTRADA' AS type,e.description AS description,e.unit_price AS price,e.quantity_income AS quantity,e.total_cost AS total
    				FROM income e WHERE e.productid = $product)
    				UNION ALL
    				(SELECT s.departure_date AS date,'SALIDA' AS type,s.description AS description,s.unit_price AS price,s.quantity_departures AS quantity,s.total_cost AS total
    				FROM departures s WHERE s.productid = $product))t2 ORDER BY date DESC;
            ";
        $request_detail = $con->select_all($sql_detail);
        $request = array('product' => $request_product, 'income' => $request_income, 'departure' => $request_departure, 'inventary' => $inventary, 'detail' => $request_detail);
    }
    return $request;
}
function sendMail($information, $template)
{
    $mail = new PHPMailer(true);
    ob_start();
    require_once("Views/Resources/emails/" . $template . ".php");
    $data_template = ob_get_clean();

    /* Valores del remitente */
    $name_sender = $information['name_sender'];
    $sender = $information['sender'];
    $password = $information['password'];
    /* Valores del servidor */
    $host = $information['host'];
    $port = $information['port'];
    if ($port == 465) {
        $serverhost = PHPMailer::ENCRYPTION_SMTPS;
    }
    if ($port == 587) {
        $serverhost = PHPMailer::ENCRYPTION_STARTTLS;
    }
    /* Asunto */
    $affair = $information['affair'];
    /* Valores del destinatario */
    $addressee = $information['addressee'];
    $name_addressee = $information['name_addressee'];
    if (!empty($information['add_pdf'])) {
        if ($information['add_pdf'] == true) {
            /* Data del pdf */
            $data = $information['data'];
            /* Valores del pdf */
            $state = $information['state'];
            $invoice = $information['invoice'];
            $total_invoice = $information['total_invoice'];
            $issue = $information['issue'];

            /* Nombre del pdf */
            $pdf_name = $invoice . '.pdf';
            /* fichero */
            $file_name = 'Assets/uploads/pdf/' . $pdf_name;

            /* tipo de pdf formato ticket o A4 */
            if ($information['type_pdf'] == 'ticket') {
                $orientation = 'portrait';
                $customPaper = array(0, 0, 204, 700);
                $pdf_template = 'invoice_ticket';
            } else {
                $orientation = 'portrait';
                $customPaper = 'A4';
                $pdf_template = 'invoice_a4';
            }

            /* plantilla pdf */
            $pdf = redirect_pdf("Resources/reports/pdf/" . $pdf_template, $data);

            /* Instacion a la libreria dompdf */
            $dompdf = new Dompdf();

            /* Para que se muestren las imagenes */
            $options = $dompdf->getOptions();
            $options->set(array('isRemoteEnabled' => true));
            $dompdf->setOptions($options);

            /* creo el pdf */
            $dompdf->loadHtml($pdf);
            $dompdf->setPaper($customPaper, $orientation);
            $dompdf->render();
            $file = $dompdf->output();

            /* lo guardo en assets para ser enviado */
            file_put_contents($file_name, $file);
        }
    }
    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $sender;
        $mail->Password = $password;
        $mail->SMTPSecure = $serverhost;
        $mail->Port = $port;

        $mail->setFrom($sender, $name_sender);
        $mail->addAddress($addressee);

        $mail->CharSet = 'UTF-8';

        $mail->isHTML(true);

        if (!empty($information['add_pdf'])) {
            if ($information['add_pdf'] == true) {
                $mail->AddAttachment($file_name);
            }
        }

        $mail->Subject = $affair;
        $mail->Body = $data_template;

        if ($mail->send()) {
            return true;
            if (!empty($information['add_pdf'])) {
                if ($information['add_pdf'] == true) {
                    if (file_exists($file_name)) {
                        unlink($file_name);
                    }
                }
            }
        }
    } catch (Exception $e) {
        return false;
    }
}

function consult_document($type, $document, $token)
{
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.factiliza.com/pe/v1/{$type}/info/{$document}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer {$token}"
        ],
    ));

    $response = curl_exec($curl);


    if (curl_errno($curl)) {

        $error_msg = curl_error($curl);
        curl_close($curl);
        return ['success' => false, 'error' => $error_msg];
    }

    curl_close($curl);
    $answer = json_decode($response, TRUE);
    return $answer;
}

function clearCookie()
{
    if (isset($_COOKIE["username"])) {
        setcookie('username', '', time() - 3600, '/');
    }
    if (isset($_COOKIE["password"])) {
        setcookie('password', '', time() - 3600, '/');
    }
}

function array_find_object(array $array, callable $callback)
{
    foreach ($array as $key => $value) {
        if ($callback((Object) $value, $key)) {
            return (Object) $value;
        }
    }

    return null;
}