<?php
require_once '../../config/config.php';
requireLogin();

$id = isset($_GET['id']) ? cleanInput($_GET['id']) : null;
$bom = null;
$bomItems = [];
$materials = [];

try {
    // Get all materials for dropdown
    $materialQuery = "SELECT id, code, name, unit FROM materials ORDER BY name";
    $stmt = $db->prepare($materialQuery);
    $stmt->execute();
    $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($id) {
        // Get BOM details
        $bomQuery = "SELECT * FROM bom WHERE id = ?";
        $stmt = $db->prepare($bomQuery);
        $stmt->execute([$id]);
        $bom = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($bom) {
            // Get BOM items
            $itemsQuery = "SELECT bi.*, m.name as material_name, m.code as material_code, m.unit 
                          FROM bom_items bi 
                          JOIN materials m ON bi.material_id = m.id 
                          WHERE bi.bom_id = ?";
            $stmt = $db->prepare($itemsQuery);
            $stmt->execute([$id]);
            $bomItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (PDOException $e) {
    error_log("Error fetching BOM data: " . $e->getMessage());
    setFlashMessage('danger', __('error_occurred'));
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($id ? __('edit_bom') : __('add_bom')) . ' - ' . __('app_name'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .sidebar a.active {
            background-color: #0d6efd;
        }
        .main-content {
            padding: 20px;
        }
        .bom-form {
            max-width: 800px;
            margin: 0 auto;
        }
        #materialsList {
            margin-top: 20px;
        }
        .material-item {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 px-0 sidebar">
                <div class="p-3 text-white">
                    <h5><?php echo __('app_name'); ?></h5>
                </div>
                <a href="../dashboard/index.php">
                    <i class="bi bi-speedometer2"></i> <?php echo __('dashboard'); ?>
                </a>
                <a href="index.php" class="active">
                    <i class="bi bi-diagram-3"></i> <?php echo __('bom'); ?>
                </a>
                <a href="../production/index.php">
                    <i class="bi bi-gear"></i> <?php echo __('production'); ?>
                </a>
                <?php if (hasRole('ADMIN')): ?>
                <a href="../users/index.php">
                    <i class="bi bi-people"></i> <?php echo __('users'); ?>
                </a>
                <?php endif; ?>
                <a href="../settings/index.php">
                    <i class="bi bi-sliders"></i> <?php echo __('settings'); ?>
                </a>
                <a href="../../controllers/AuthController.php?action=logout">
                    <i class="bi bi-box-arrow-right"></i> <?php echo __('logout'); ?>
                </a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="bom-form">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><?php echo $id ? __('edit_bom') : __('add_bom'); ?></h2>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> <?php echo __('back'); ?>
                        </a>
                    </div>

                    <?php
                    $flash = getFlashMessage();
                    if ($flash) {
                        echo '<div class="alert alert-' . $flash['type'] . '">' . $flash['message'] . '</div>';
                    }
                    ?>

                    <form action="../../controllers/BomController.php" method="POST" id="bomForm">
                        <input type="hidden" name="action" value="<?php echo $id ? 'update' : 'create'; ?>">
                        <?php if ($id): ?>
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <?php endif; ?>

                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="code" class="form-label"><?php echo __('bom_code'); ?></label>
                                    <input type="text" class="form-control" id="code" name="code" 
                                           value="<?php echo $bom ? htmlspecialchars($bom['code']) : ''; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label"><?php echo __('bom_name'); ?></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo $bom ? htmlspecialchars($bom['name']) : ''; ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Materials List -->
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3"><?php echo __('materials'); ?></h5>
                                <div id="materialsList">
                                    <?php foreach ($bomItems as $index => $item): ?>
                                    <div class="material-item">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <label class="form-label"><?php echo __('materials'); ?></label>
                                                <select name="materials[]" class="form-select" required>
                                                    <?php foreach ($materials as $material): ?>
                                                        <option value="<?php echo $material['id']; ?>" 
                                                                <?php echo $material['id'] === $item['material_id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($material['code'] . ' - ' . $material['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-5 mb-2">
                                                <label class="form-label"><?php echo __('quantity'); ?></label>
                                                <input type="number" name="quantities[]" class="form-control" 
                                                       value="<?php echo $item['quantity']; ?>" step="0.01" required>
                                            </div>
                                            <div class="col-md-1 mb-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger" onclick="removeMaterial(this)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="btn btn-success mt-3" onclick="addMaterial()">
                                    <i class="bi bi-plus-lg"></i> <?php echo __('add'); ?> <?php echo __('materials'); ?>
                                </button>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <?php echo __('save'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Material Item Template -->
    <template id="materialItemTemplate">
        <div class="material-item">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label"><?php echo __('materials'); ?></label>
                    <select name="materials[]" class="form-select" required>
                        <?php foreach ($materials as $material): ?>
                            <option value="<?php echo $material['id']; ?>">
                                <?php echo htmlspecialchars($material['code'] . ' - ' . $material['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5 mb-2">
                    <label class="form-label"><?php echo __('quantity'); ?></label>
                    <input type="number" name="quantities[]" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-1 mb-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger" onclick="removeMaterial(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <script>
    function addMaterial() {
        const template = document.getElementById('materialItemTemplate');
        const materialsList = document.getElementById('materialsList');
        const clone = template.content.cloneNode(true);
        materialsList.appendChild(clone);
    }

    function removeMaterial(button) {
        button.closest('.material-item').remove();
    }

    // Add at least one material item if none exists
    if (document.querySelectorAll('.material-item').length === 0) {
        addMaterial();
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
