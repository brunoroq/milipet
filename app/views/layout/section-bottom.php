<?php
/**
 * Bottom Section Router
 * Determines which bottom section to display based on current route
 */

// Get current route from URL parameter
$currentRoute = $_GET['r'] ?? 'home';

// Normalize route (remove controller/action if present)
$routeParts = explode('/', $currentRoute);
$mainRoute = strtolower($routeParts[0]);

// Define which pages show newsletter vs map
$newsletterPages = ['home', '', 'index'];
$showNewsletter = in_array($mainRoute, $newsletterPages);

// Include appropriate section
if ($showNewsletter) {
    require_once __DIR__ . '/bottom-newsletter.php';
} else {
    require_once __DIR__ . '/bottom-map.php';
}
?>
