<?php

declare (strict_types = 1);

namespace App;

use App\listing\Search;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

class Routing
{
    private $routes = null;

    public function __construct()
    {}

    // add all routes
    public function routes()
    {
        $this->routes = simpleDispatcher(function (RouteCollector $r) {
            $r->get('/api/v1/items[/{criteria_name}/{criteria_value}]', Search::class);
            $r->get('/api/v1/item/{id:\d+}', [Search::class, 'get_item']);
            $r->post('/api/v1/item', [Search::class, 'create']);
            $r->patch('/api/v1/item', [Search::class, 'update']);
            $r->delete('/api/v1/item', [Search::class, 'delete']);
            $r->post('/api/v1/book', [Search::class, 'book']);
        });

        // Fetch method and URI from somewhere
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $this->routes->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                header('location: /404.html');
                exit;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];

                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                $_REQUEST['vars'] = $vars;

                break;
        }

        return $this->routes;
    }
}
