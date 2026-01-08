<?php
require_once("Config/Config.php");
require_once("Helpers/Helpers.php");
require_once("Helpers/SystemInfo.php");
require_once('Libraries/XEPanel/mysqli_functions.php');
require_once('Libraries/NetworkUtils/utils.php');
require_once('Libraries/MikroTik/Router.php');
require_once('Libraries/MikroTik/CronjobMethods.php');
require_once("Libraries/Emitter2/ObserverInterface.php");
require_once("Libraries/Emitter2/EventManager.php");

$route = isset($_GET['route']) ? $_GET['route'] : "login/login";
$routes = explode("/", $route);
$controller = $routes[0];
$methop = $routes[0];
$params = "";

if (isset($routes[1])) {
  if ($routes[1] != "") {
    $methop = $routes[1];
  }
}
if (isset($routes[2])) {
  if ($routes[2] != "") {
    for ($i = 2; $i < count($routes); $i++) {
      $params .= $routes[$i] . ',';
    }
    $params = trim($params, ',');
  }
}
require_once("Libraries/Core/Autoload.php");
require_once("Libraries/Core/Load.php");
