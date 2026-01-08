<?php

require_once("Kernel/ServiceRegister.php");
require_once("Kernel/ListenerRegister.php");

foreach ($serviceRegister as $serviceName) {
    $servicePath = "Services/{$serviceName}.php";
    if (file_exists($servicePath)) {
        require_once($servicePath);
    }
}

foreach ($listenerRegister as $listenerName) {
    $listenerPath = "Listeners/{$listenerName}.php";
    if (file_exists($listenerPath)) {
        require_once($listenerPath);
    }
}

$controller = ucwords($controller);
$controllerFile = "Controllers/" . $controller . ".php";
if (file_exists($controllerFile)) {
    require_once($controllerFile);
    $controller = new $controller();
    if (method_exists($controller, $methop)) {
        $controller->{$methop}($params);
    } else {
        require_once("Controllers/Error.php");
    }
} else {
    require_once("Controllers/Error.php");
}