<?php
require_once '../config/config.php';

class AuthController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function login($username, $password) {
        try {
            $query = "SELECT id, username, password, role FROM users WHERE username = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$username]);
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verify password (using password_verify if passwords are hashed)
                if (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    return true;
                }
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    public function logout() {
        // Destroy all session data
        session_destroy();
        // Clear session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController($db);
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'login':
                $username = cleanInput($_POST['username']);
                $password = cleanInput($_POST['password']);
                
                if ($auth->login($username, $password)) {
                    header('Location: ../views/dashboard/index.php');
                    exit();
                } else {
                    setFlashMessage('danger', __('invalid_credentials'));
                    header('Location: ../index.php');
                    exit();
                }
                break;
                
            case 'logout':
                $auth->logout();
                header('Location: ../index.php');
                exit();
                break;
        }
    }
}

// Handle direct script access
if (!isset($db)) {
    header('Location: ../index.php');
    exit();
}
?>
