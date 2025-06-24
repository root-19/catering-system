<?php
namespace root_dev;

class Router {
    protected $routes = [];

    // Add route to the router
    public function addRoute($method, $path, $controllerAction) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controllerAction' => $controllerAction
        ];
    }

    // Handle incoming request
    public function handleRequest($uri, $method) {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                $this->callControllerAction($route['controllerAction']);
                return;
            }
        }
        echo "404 Not Found: Route [$uri]";
    }

    private function callControllerAction($controllerAction) {
        list($controller, $action) = explode('@', $controllerAction);
        $controller = 'root_dev\\Controller\\' . $controller;
        $controllerInstance = new $controller();
        $controllerInstance->$action();
    }
}
