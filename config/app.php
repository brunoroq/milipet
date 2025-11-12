<?php
// Application-level environment flags
if (!defined('APP_ENV')) {
    define('APP_ENV', getenv('APP_ENV') ?: (getenv('MILIPET_APP_ENV') ?: 'dev'));
}

// Optional: place for other app-wide toggles in future
