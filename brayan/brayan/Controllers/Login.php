<?php
class Login extends Controllers
{
    public function __construct()
    {
        session_start();
        if (!empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/dashboard');
        }
        parent::__construct();
    }
    public function login()
    {
        $data['page_name'] = "Login";
        $data['page_functions_js'] = "login.js";
        $data['business'] = business_session();
        $this->views->getView($this, "login", $data);
    }
    public function validation()
    {
        if ($_POST) {
            if (empty($_POST['username']) || empty($_POST['password'])) {
                $arrResponse = array('status' => "warning", 'msg' => 'El usuario y contraseña son campos obligatorios.');
            } else {
                $username = strtolower(strClean($_POST['username']));
                $password = encrypt($_POST['password']);
                $request = $this->model->validation($username, $password);
                if (empty($request)) {
                    $arrResponse = array('status' => "warning", 'msg' => 'Usuario o contraseña es incorrecta.');
                } else {
                    $arrData = $request;
                    if ($arrData['state'] == 1) {
                        $current_time = time();
                        $cookie_expiration_time = $current_time + (365 * 24 * 60 * 60);
                        if (!empty($_POST["remember"])) {
                            setcookie("username", $username, $cookie_expiration_time);
                            setcookie("password", decrypt($password), $cookie_expiration_time);
                        } else {
                            clearCookie();
                        }
                        $_SESSION['idUser'] = $arrData['id'];
                        $_SESSION['login'] = true;
                        $arrData = $this->model->login_session($_SESSION['idUser']);
                        user_session($_SESSION['idUser']);
                        business_session();
                        $arrResponse = array('status' => "success", 'msg' => 'ok');
                    } else {
                        $arrResponse = array('status' => "error", 'msg' => 'El usuario se encuentra desactivado, comuniquese con su administrador.');
                    }
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function reset()
    {
        if ($_POST) {
            if (empty($_POST['email'])) {
                $answer = array('status' => 'error', 'msg' => 'Ingrese un correo electrónico valido.');
            } else {
                $token = token();
                $email = strtolower(strClean($_POST['email']));
                $request = $this->model->validation_email($email);
                if (empty($request)) {
                    $answer = array('status' => 'not_exist', 'msg' => 'No existe ningún operador con este correo.');
                } else {
                    $iduser = $request['id'];
                    $fullnames = $request['names'] . ' ' . $request['surnames'];
                    $url_recovery = base_url() . '/login/restore/' . encrypt($email) . '/' . $token;
                    $businness = business_session();

                    $data = array(
                        'logo' => $businness['logo_email'],//logo empresa
                        'name_sender' => $businness['business_name'],//nombre remitente
                        'sender' => $businness['email'],//remitente
                        'password' => $businness['password'],//contraseña
                        'mobile' => $businness['mobile'],//celular
                        'address' => $businness['address'],//celular
                        'host' => $businness['server_host'],//host
                        'port' => $businness['port'],//puerto
                        'addressee' => $email,//destinatario
                        'name_addressee' => $fullnames,//nombre destinatario
                        'affair' => 'Restablecer su contraseña',//asunto
                        'url_recovery' => $url_recovery,//link para restablecer contraseña
                    );

                    $result = sendMail($data, "reset");
                    if ($result === true) {
                        $modify_token = $this->model->update_token($iduser, $token);
                        if ($modify_token == 'success') {
                            $answer = array('status' => 'success', 'msg' => "Se le envio un correo ,revise su bandeja de entrada de su cuenta de correo.");
                        } else {
                            $answer = array('status' => false, 'msg' => 'No es posible realizar el proceso, intenta más tarde.');
                        }
                    } else {
                        $answer = array('status' => 'error', 'msg' => "No es posible realizar el proceso, intenta más tarde.");
                    }
                }
            }
            echo json_encode($answer, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function restore(string $params)
    {
        if (empty($params)) {
            header('Location: ' . base_url());
        } else {
            $arrParams = explode(',', $params);
            $email = decrypt($arrParams[0]);
            $email = strClean($email);
            $token = strClean($arrParams[1]);
            $query = $this->model->user_information($email, $token);
            if (empty($query)) {
                header("Location: " . base_url());
            } else {
                $data['email'] = $email;
                $data['token'] = $token;
                $data['id'] = encrypt($query['id']);
                $data['page_name'] = "Restaurar contraseña";
                $data['page_functions_js'] = "restore.js";
                $data['business'] = business_session();
                $this->views->getView($this, "restore_password", $data);
            }
        }
        die();
    }
    public function update_password()
    {
        if (empty($_POST['id']) || empty($_POST['email']) || empty($_POST['token']) || empty($_POST['password']) || empty($_POST['passwordConfirm'])) {
            $answer = array('status' => false, 'msg' => 'Los campos son obligatorios.');
        } else {
            $id = decrypt($_POST['id']);
            $id = intval($id);
            $password = $_POST['password'];
            $passwordConfirm = $_POST['passwordConfirm'];
            $email = strClean($_POST['email']);
            $token = strClean($_POST['token']);

            if ($password != $passwordConfirm) {
                $answer = array('status' => 'error', 'msg' => 'Las contraseñas no coinciden.');
            } else {
                $request = $this->model->user_information($email, $token);
                if (empty($request)) {
                    $answer = array('status' => 'error', 'msg' => 'No se encontro información del usuario.');
                } else {
                    $password = encrypt($_POST['password']);
                    $modify = $this->model->update_password($id, $password);
                    if ($modify == 'success') {
                        $businness = business_session();
                        $data = array(
                            'logo' => $businness['logo_email'],//logo empresa
                            'name_sender' => $businness['business_name'],//nombre remitente
                            'sender' => $businness['email'],//remitente
                            'password' => $businness['password'],//contraseña
                            'mobile' => $businness['mobile'],//celular
                            'address' => $businness['address'],//celular
                            'host' => $businness['server_host'],//host
                            'port' => $businness['port'],//puerto
                            'addressee' => $email,//destinatario
                            'name_addressee' => $request['names'] . " " . $request['surnames'],//nombre destinatario
                            'affair' => 'Tu contraseña ha sido restablecida',//asunto
                        );

                        $result = sendMail($data, "change_password");
                        if ($result === true) {
                            $answer = array('status' => 'success', 'msg' => 'Tu contraseña ha sido restablecida.');
                        } else {
                            $answer = array('status' => 'error', 'msg' => "No es posible realizar el proceso, intenta más tarde.");
                        }
                    } else {
                        $answer = array('status' => 'error', 'msg' => 'No es posible realizar el proceso, intente más tarde.');
                    }
                }
            }
        }
        echo json_encode($answer, JSON_UNESCAPED_UNICODE);
        die();
    }
}
