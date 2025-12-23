<?php
/**
 * Kyle-HMS Bootstrap File
 * Initializes the application
 * 
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

// Load Composer's autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Load environment configuration
// (In production, use .env file)

// Initialize application
use App\Config\App;

App::init();

// Register error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno]: $errstr in $errfile on line $errline");
    
    if (App::ENVIRONMENT === 'development') {
        echo "Error [$errno]: $errstr in $errfile on line $errline";
    } else {
        // In production, show generic error
        if (!headers_sent()) {
            http_response_code(500);
        }
        echo "An error occurred. Please try again later.";
    }
    return true;
});

// Register exception handler
set_exception_handler(function($exception) {
    error_log("Exception: " . $exception->getMessage());
    error_log("Stack trace: " . $exception->getTraceAsString());
    
    if (App::ENVIRONMENT === 'development') {
        echo "";
        echo "Exception: " . $exception->getMessage() . "\n\n";
        echo "File: " . $exception->getFile() . "\n";
        echo "Line: " . $exception->getLine() . "\n\n";
        echo "Stack trace:\n" . $exception->getTraceAsString();
        echo "";
    } else {
        if (!headers_sent()) {
            http_response_code(500);
        }
        echo "An unexpected error occurred. Please try again later.";
    }
});