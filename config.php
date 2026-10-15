<?php
// ---- secure session cookie settings (must come before session_start) ----
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,       // JavaScript can't read the session cookie
    'samesite' => 'Lax',      // cookie is not sent on cross-site POSTs
]);
session_start();

date_default_timezone_set('Africa/Nairobi');

// ---- database ----
$host = "localhost";
$db   = "climate_research";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed.");
}

// ---- helpers ----

// Escape output to prevent XSS
function e($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

// Create (or reuse) the CSRF token for this session
function csrf_token(): string {
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf_token"];
}

// Hidden input to drop inside every POST form
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

// Check the token that came back with the form
function csrf_verify(): bool {
    $sent = $_POST["csrf_token"] ?? "";
    return !empty($_SESSION["csrf_token"])
        && is_string($sent)
        && hash_equals($_SESSION["csrf_token"], $sent);
}