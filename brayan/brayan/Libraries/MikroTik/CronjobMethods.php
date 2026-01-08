<?php

// Notificación de deuda, mismo dia de pago(API-WhatsApp)
function send_soon_due_invoices($t)
{
    // Usar la constante BASE_URL
    $url = BASE_URL . "/tasks/invoice_send_payday_whatsapp";

    // Hacer la solicitud a la URL usando cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Si hay problemas con SSL
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Guardar el resultado en la base de datos
    $result_msg = ($http_code == 200) ? "Ejecución automática exitosa" : "Error en la ejecución ($http_code)";

    sqlUpdate("cronjobs", "lastrun", time(), $t->id);
    sqlUpdate("cronjobs", "lastresult", $result_msg, $t->id);

    // Registrar en el historial
    $his = (object) array();
    $his->cronjobid = $t->id;
    $his->result = $result_msg;
    $his->date = time();
    sqlInsert("cronjobs_history", $his);
}


// Notificación de deudas a todos los clientes(API-WhatsApp)
function send_expired_invoices($t)
{
    // Usar la constante BASE_URL
    $url = BASE_URL . "/tasks/invoice_send_whatsapp";

    // Hacer la solicitud a la URL usando cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Si hay problemas con SSL
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Guardar el resultado en la base de datos
    $result_msg = ($http_code == 200) ? "Ejecución automática exitosa" : "Error en la ejecución ($http_code)";

    sqlUpdate("cronjobs", "lastrun", time(), $t->id);
    sqlUpdate("cronjobs", "lastresult", $result_msg, $t->id);

    // Registrar en el historial
    $his = (object) array();
    $his->cronjobid = $t->id;
    $his->result = $result_msg;
    $his->date = time();
    sqlInsert("cronjobs_history", $his);
}


// Corta servicios a clientes 
function cut_service_expired_invoices($t)
{
    // Definir la URL para ejecutar la tarea
    $url = BASE_URL . "/tasks/client_deuda_suspend";

    // Inicializar cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Desactivar verificación SSL si es necesario
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Determinar el resultado de la ejecución
    $result_msg = ($http_code == 200) ? "Ejecución automática exitosa" : "Error en la ejecución ($http_code)";

    // Actualizar la tabla cronjobs
    sqlUpdate("cronjobs", "lastrun", time(), $t->id);
    sqlUpdate("cronjobs", "lastresult", $result_msg, $t->id);

    // Registrar en el historial
    $his = (object) array();
    $his->cronjobid = $t->id;
    $his->result = $result_msg;
    $his->date = time();
    sqlInsert("cronjobs_history", $his);
}


// Envio masivo de deudas por correo electronico
function reg_customer_traffic($t)
{
    // Hacer la solicitud a la URL usando cURL
    $url = BASE_URL . "/tasks/invoice_send_email";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Opción para evitar problemas con SSL
    $response = curl_exec($ch);
    curl_close($ch);

    // Registrar la ejecución en la base de datos
    sqlUpdate("cronjobs", "lastrun", time(), $t->id);
    sqlUpdate("cronjobs", "lastresult", "Ejecución automática exitosa", $t->id);

    $his = (object) array();
    $his->cronjobid = $t->id;
    $his->result = "Ejecución automática exitosa";
    $his->date = time();

    sqlInsert("cronjobs_history", $his);
}

// Generar backup
function cut_service_backup($t)
{
    $mysql = new Mysql();
    $service = new BackupDBService();
    $service->setMysql($mysql);
    $response = $service->execute();

    // Determinar el resultado de la ejecución
    $result_msg = $response['message'];

    // Actualizar la tabla cronjobs
    $mysql->createQueryBuilder()
        ->update()
        ->from("cronjobs")
        ->where("id = {$t->id}")
        ->set([
            "lastrun" => time(),
            "lastresult" => $result_msg
        ])->execute();

    // Registrar en el historial
    $mysql->setTableName("cronjobs_history");
    $mysql->insertObject(["cronjobid", "result", "date"], [
        "cronjobid" => $t->id,
        "result" => $result_msg,
        "date" => time()
    ]);
}
