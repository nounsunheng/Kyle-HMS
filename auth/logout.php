<?php
/**
 * Kyle-HMS Logout Handler
 * Destroys session and redirects to login
 */
require_once '../config/config.php';
require_once '../config/session.php';

// Destroy session
destroySession();

// Set flash message
setFlashMessage('You have been logged out successfully.', 'success');

// Redirect to login page
redirect('/auth/login.php');
?>