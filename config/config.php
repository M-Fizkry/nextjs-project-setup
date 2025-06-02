<?php
session_start();

// Base URL configuration
define('BASE_URL', 'http://localhost/inventory');

// Default language
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'id';
}

// Available languages
$languages = [
    'id' => 'Indonesia',
    'en' => 'English',
    'ja' => '日本語'
];

// Load language file
function loadLanguage($lang) {
    $langFile = "../languages/{$lang}.php";
    if (file_exists($langFile)) {
        include_once $langFile;
        return $translations;
    }
    // Fallback to Indonesian if language file not found
    include_once "../languages/id.php";
    return $translations;
}

// Translation function
function __($key) {
    global $translations;
    return isset($translations[$key]) ? $translations[$key] : $key;
}

// Database connection
require_once 'database.php';
$database = new Database();
$db = $database->getConnection();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Authentication check
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/views/auth/login.php');
        exit();
    }
}

// Check user role
function hasRole($requiredRole) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $requiredRole;
}

// Flash messages
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Clean input
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
