<?php
require_once '../config/config.php';

// Validate and set language
if (isset($_GET['lang'])) {
    $lang = cleanInput($_GET['lang']);
    
    // Check if language is supported
    global $languages;
    if (array_key_exists($lang, $languages)) {
        $_SESSION['lang'] = $lang;
    }
}

// Redirect back to previous page or index
$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';
header('Location: ' . $redirect);
exit();
?>
