<?php

use Slim\App;
use Slim\Http\Response;

require_once '../src/controllers/ctr_users.php';

return function (App $app) {
    $routesU = require_once __DIR__ . "/../src/routes/routes_users.php";
    $container = $app->getContainer();
    $routesU($app);
	$userController = new ctr_users();

    $app->get('/', function ($request, $response, $args) use ($container, $userController) {
        if(isset($_SESSION['systemSession'])){
            $responseFunction = $userController->validateCurrentSession();
            $args['versionerp'] = '?'.FECHA_ULTIMO_PUSH;
            if($responseFunction->result == 2 && $responseFunction->currentSession->permisos == "administrador"){
                return $response->withStatus(302)->withHeader('Location', 'home');
            } else if($responseFunction->result == 2 && $responseFunction->currentSession->permisos == "normal"){
                return $response->withStatus(302)->withHeader('Location', 'inicio');
            } else {
                return $response->withStatus(302)->withHeader('Location', 'iniciar-sesion');
            }
        } else {
            return $response->withStatus(302)->withHeader('Location', 'iniciar-sesion');
        }
        return $this->view->render($response, "index.twig", $args);
    })->setName("Start");
};
