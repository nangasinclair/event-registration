<?php
/**
 * Database connection settings.
 * Update these four values to match your local MySQL setup.
 */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'student_event_registration');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    // In production this would log the error instead of exposing it.
    die('Database connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');

session_start();

/**
 * Turn an event category like "Health & Wellness" into a CSS-safe slug
 * like "health-wellness", used for the coloured accent bar on event cards.
 */
function category_slug($category) {
    $slug = strtolower(trim($category));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug, '-');
}
?>
