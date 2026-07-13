<?php
/**
 * Site functions
 */

/**
 * HTML Escape
 */
function h($val): string {
    return htmlspecialchars($val, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * AJAX check alias
 */
function is_ajax_request(): bool {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Ensure directory exists
 */
function ensure_directory(string $path, int $mode = 0755): bool {
    if (is_dir($path)) return true;
    return mkdir($path, $mode, true);
}
