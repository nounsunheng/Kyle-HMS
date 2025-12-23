<?php
/**
 * Router - URL routing and dispatch
 * 
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Core;

class Router {
    
    private array $routes = [];
    private array $middlewares = [];
    
    /**
     * Register GET route
     */
    public function get(string $path, $handler, array $middleware = []): void {
        $this->addRoute('GET', $path, $handler, $middleware);
    }
    
    /**
     * Register POST route
     */
    public function post(string $path, $handler, array $middleware = []): void {
        $this->addRoute('POST', $path, $handler, $middleware);
    }
    
    /**
     * Add route to registry
     */
    private function addRoute(string $method, string $path, $handler, array $middleware): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }
    
    /**
     * Dispatch request to appropriate handler
     */
    public function dispatch(string $uri, string $method = 'GET'): void {
        // Clean URI
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';
        
        // Find matching route
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            $pattern = $this->convertPathToRegex($route['path']);
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match
                
                // Execute middleware
                foreach ($route['middleware'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    $middleware->handle();
                }
                
                // Execute handler
                $this->executeHandler($route['handler'], $matches);
                return;
            }
        }
        
        // No route found
        $this->handleNotFound();
    }
    
    /**
     * Convert route path to regex pattern
     */
    private function convertPathToRegex(string $path): string {
        // Convert {id} to (\d+), {slug} to ([a-z0-9-]+)
        $pattern = preg_replace('/\{id\}/', '(\d+)', $path);
        $pattern = preg_replace('/\{slug\}/', '([a-z0-9-]+)', $pattern);
        $pattern = preg_replace('/\{([a-z]+)\}/', '([^/]+)', $pattern);
        
        return '#^' . $pattern . '$#';
    }
    
    /**
     * Execute route handler
     */
    private function executeHandler($handler, array $params): void {
        if (is_callable($handler)) {
            // Handler is a closure
            call_user_func_array($handler, $params);
        } elseif (is_array($handler)) {
            // Handler is [ControllerClass, 'method']
            [$controllerClass, $method] = $handler;
            $controller = new $controllerClass();
            call_user_func_array([$controller, $method], $params);
        } elseif (is_string($handler)) {
            // Handler is 'ControllerClass@method'
            [$controllerClass, $method] = explode('@', $handler);
            $controller = new $controllerClass();
            call_user_func_array([$controller, $method], $params);
        }
    }
    
    /**
     * Handle 404 Not Found
     */
    private function handleNotFound(): void {
        http_response_code(404);
        echo "404 - Page Not Found";
        exit;
    }
}