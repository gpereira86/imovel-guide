<?php

use system\Controller\SiteController;
use system\Core\Helpers;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

/**
 * Configures the routing for the application using FastRoute.
 *
 * Defines the routes, methods, and handlers to be used in the application,
 * then dispatches the current HTTP request to the appropriate handler.
 */
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $url = SITE_URL;

    /**
     * Route definitions.
     *
     * - GET: Retrieves pages and lists.
     * - POST: Handles form submissions.
     * - Dynamic routes with parameters: e.g., `deletar/{id:\d+}` accepts numeric `id`.
     */
    $r->addRoute('GET', $url, 'system\Controller\SiteController@index');
    $r->addRoute('GET', "{$url}deletar/{id:\d+}", 'system\Controller\SiteController@deleteRegister');
    $r->addRoute('POST', "{$url}cadastrar", 'system\Controller\SiteController@saveRegister');
    $r->addRoute(['GET', 'POST'], "{$url}editar/{id:\d+}", 'system\Controller\SiteController@selectRegisterToUpdate');
    $r->addRoute(['GET', 'POST'], "{$url}salvar-edicao/{id:\d+}", 'system\Controller\SiteController@updateRegister');
    $r->addRoute('GET', "{$url}404", 'system\Controller\SiteControlador@error404');
});

// Capture the HTTP method and requested URI.
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Remove query string from the URI, if present.
if (($pos = strpos($uri, '?')) !== false) {
    $uri = substr($uri, 0, $pos);
}

// Dispatch the request and determine the route information.
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

/**
 * Handle the route based on the dispatch result.
 *
 * - NOT_FOUND: Redirects to the 404 page.
 * - METHOD_NOT_ALLOWED: Redirects to the 404 page.
 * - FOUND: Executes the handler for the matched route.
 */
switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
    case \FastRoute\Dispatcher::NOT_FOUND:
        // Redirect to a 404 error page.
        Helpers::redirect('404');
        break;

    case \FastRoute\Dispatcher::FOUND:
        // Retrieve the handler and route variables.
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        // Extract `id` or `slug` from the route variables.
        $id = $vars['id'] ?? null;
        $slug = $vars['slug'] ?? null;
        $variavel = $id ?? $slug;

        // Parse the handler to extract the class and method.
        $handlerParts = explode('@', $handler);
        $controllerClass = $handlerParts[0];
        $controllerMethod = $handlerParts[1];

        // Instantiate the controller and call the appropriate method.
        $siteController = new SiteController();
        $siteController->$controllerMethod($variavel, $vars['pagina'] ?? null);

        break;
}
