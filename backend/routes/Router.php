<?php
namespace Backend\Routes;

class Router {
    private $routes = [];
    // Register a GET route
    public function get($uri, $action) {
        $this->routes['GET'][$uri] = $action;
    }
    // Register a POST route
    public function post($uri, $action) {
        $this->routes['POST'][$uri] = $action;
    }
    // Resolve the current request
    public function resolve($requestUri, $method) {
        // Parse the URI and remove query parameters
        $path = parse_url($requestUri, PHP_URL_PATH);
        // Handle subdirectories if you aren't using a virtual host (e.g., localhost/barbershop/)
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/' && $scriptName !== '\\') {
            $path = str_replace($scriptName, '', $path);
        }
        $path = '/' . ltrim($path, '/');
        // Check if the route exists
        if (isset($this->routes[$method][$path])) {
            $action = $this->routes[$method][$path];
            $controllerName = $action[0];
            $methodName = $action[1];
            // Instantiate the controller and call the method
            $controller = new $controllerName();
            return $controller->$methodName();
        }
        // 404 Fallback
        http_response_code(404);
        echo "<h1>404 Not Found</h1>";
    }
}
?>