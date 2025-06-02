<?php
echo "=== Inventory Control System Setup ===\n\n";

// Database configuration
$host = "localhost";
$username = "root";
$password = "";

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to MySQL successfully.\n";
    
    // Read and execute SQL file
    echo "Creating database and tables...\n";
    $sql = file_get_contents('database.sql');
    $pdo->exec($sql);
    
    echo "\nSetup completed successfully!\n\n";
    echo "Default login credentials:\n";
    echo "Username: admin\n";
    echo "Password: admin123\n\n";
    
    echo "Next steps:\n";
    echo "1. Update database connection settings in config/database.php\n";
    echo "2. Start the PHP development server: php -S localhost:8000\n";
    echo "3. Visit http://localhost:8000 in your browser\n";
    echo "4. Log in with the default credentials\n";
    echo "5. Change the admin password after first login\n\n";
    
    echo "Note: For production use, make sure to:\n";
    echo "- Update database credentials\n";
    echo "- Configure a proper web server (Apache/Nginx)\n";
    echo "- Set appropriate file permissions\n";
    echo "- Enable error reporting only in development\n";
    
} catch(PDOException $e) {
    echo "Setup failed: " . $e->getMessage() . "\n";
}
?>
