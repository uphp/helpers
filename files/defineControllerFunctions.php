<?php
namespace controllers;

use src\Inflection;

if (! function_exists('view')) {
    function view($viewObject, $options = [])
    {
        $classController = "controllers\\" . Inflection::classify(ucwords($viewObject->controllerName)) . "Controller";
        $actionController = new $classController();
        $actionController->render($viewObject, $options);
    }
}

if (! function_exists('json')) {
    function json($arrayEncode, $options = [])
    {
        if (isset($arrayEncode->validate)) unset($arrayEncode->validate);
        echo json_encode($arrayEncode);
    }
}