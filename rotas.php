<?php

use system\Controller\SiteController;
use system\Core\Helpers;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $url = SITE_URL;

    $r->addRoute('GET', $url, 'system\Controller\SiteController@index');
    $r->addRoute('GET', "{$url}deletar/{id:\d+}", 'system\Controller\SiteController@deleteRegister');
    $r->addRoute('POST', "{$url}cadastrar", 'system\Controller\SiteController@saveRegister');
    $r->addRoute(['GET', 'POST'], "{$url}editar/{id:\d+}", 'system\Controller\SiteController@selectRegisterToUpdate');
    $r->addRoute(['GET', 'POST'], "{$url}salvar-edicao/{id:\d+}", 'system\Controller\SiteController@updateRegister');

    $r->addRoute('GET', "{$url}404", 'system\Controller\SiteControlador@error404');

});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (($pos = strpos($uri, '?')) !== false) {
    $uri = substr($uri, 0, $pos);
}

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        Helpers::redirecionar('404');
        break;
    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        Helpers::redirecionar('404');
        break;
    case \FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        $id = isset($vars['id']) ? $vars['id'] : null;
        $slug = isset($vars['slug']) ? $vars['slug'] : null;

        if ($id !== null) {
            $variavel = $id;
        } else {
            $variavel = $slug;
        }

        $handlerParts = explode('@', $handler);
        $controllerClass = $handlerParts[0];
        $controllerMethod = $handlerParts[1];

        $siteController = new siteController();
        $siteController->$controllerMethod($variavel, (isset($vars['pagina']) ? $vars['pagina'] : null)); // Passa o ID do post como parâmetro

        break;
}