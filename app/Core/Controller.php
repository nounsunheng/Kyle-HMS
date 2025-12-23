<?php
/**
 * Base Controller
 * Parent class for all controllers
 * 
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Core;

use App\Config\Security;

abstract class Controller {
    
    /**
     * Render view
     */
    protected function view(string $view, array $data = []): void {
        // Extract data to variables
        extract($data);
        
        // Construct view path
        $viewPath = __DIR__ . '/../../views/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewPath)) {
            die("View not found: $view");
        }
        
        require $viewPath;
    }
    
    /**
     * Render view with layout
     */
    protected function viewWithLayout(string $layout, string $view, array $data = []): void {
        $data['contentView'] = $view;
        $this->view("layouts.$layout", $data);
    }
    
    /**
     * Redirect to URL
     */
    protected function redirect(string $path): void {
        redirect($path);
    }
    
    /**
     * Return JSON response
     */
    protected function json(array $data, int $status = 200): void {
        jsonResponse($data, $status);
    }
    
    /**
     * Set flash message and redirect
     */
    protected function flashAndRedirect(string $message, string $type, string $path): void {
        flash($message, $type);
        redirect($path);
    }
    
    /**
     * Validate request data
     */
    protected function validate(array $rules, array $data): array {
        $errors = [];
        
        foreach ($rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $data[$field] ?? null;
            
            foreach ($rules as $rule) {
                if ($rule === 'required' && empty($value)) {
                    $errors[$field] = ucfirst($field) . ' is required';
                    break;
                }
                
                if (strpos($rule, 'min:') === 0 && strlen($value) < (int)substr($rule, 4)) {
                    $errors[$field] = ucfirst($field) . ' must be at least ' . substr($rule, 4) . ' characters';
                    break;
                }
                
                if (strpos($rule, 'max:') === 0 && strlen($value) > (int)substr($rule, 4)) {
                    $errors[$field] = ucfirst($field) . ' must not exceed ' . substr($rule, 4) . ' characters';
                    break;
                }
                
                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = 'Invalid email format';
                    break;
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Get request input
     */
    protected function input(string $key, $default = null) {
        return input($key, $default);
    }
    
    /**
     * Check if request is POST
     */
    protected function isPost(): bool {
        return isPost();
    }
}