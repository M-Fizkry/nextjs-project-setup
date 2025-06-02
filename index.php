<?php
require_once 'config/config.php';

// Redirect to dashboard if already logged in
if (isLoggedIn()) {
    header('Location: views/dashboard/index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('app_name'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header img {
            max-height: 80px;
            margin-bottom: 20px;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h2><?php echo __('app_name'); ?></h2>
            </div>

            <?php
            $flash = getFlashMessage();
            if ($flash) {
                echo '<div class="alert alert-' . $flash['type'] . '">' . $flash['message'] . '</div>';
            }
            ?>

            <form action="controllers/AuthController.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label"><?php echo __('username'); ?></label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label"><?php echo __('password'); ?></label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="language" class="form-label"><?php echo __('language'); ?></label>
                    <select class="form-select" id="language" name="language" onchange="changeLanguage(this.value)">
                        <?php
                        global $languages;
                        foreach ($languages as $code => $name) {
                            $selected = $_SESSION['lang'] === $code ? 'selected' : '';
                            echo "<option value=\"$code\" $selected>$name</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="action" value="login" class="btn btn-primary w-100">
                    <?php echo __('login'); ?>
                </button>
            </form>
        </div>
    </div>

    <script>
    function changeLanguage(lang) {
        window.location.href = 'controllers/LanguageController.php?lang=' + lang;
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
