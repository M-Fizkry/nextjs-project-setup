<?php
require_once '../../config/config.php';
requireLogin();

// Get BOM list
try {
    $query = "SELECT 
        b.id,
        b.code,
        b.name,
        COUNT(bi.id) as items_count
    FROM bom b
    LEFT JOIN bom_items bi ON b.id = bi.bom_id
    GROUP BY b.id, b.code, b.name
    ORDER BY b.code";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $boms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching BOM data: " . $e->getMessage());
    $boms = [];
}
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('bom') . ' - ' . __('app_name'); ?></title>
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
        .bom-card {
            border-radius: 8px;
            margin-bottom: 20px;
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?php echo __('bom'); ?></h2>
                    <a href="form.php" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> <?php echo __('add_bom'); ?>
                    </a>
                </div>

                <?php
                $flash = getFlashMessage();
                if ($flash) {
                    echo '<div class="alert alert-' . $flash['type'] . '">' . $flash['message'] . '</div>';
                }
                ?>

                <!-- BOM List -->
                <div class="card bom-card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo __('bom_code'); ?></th>
                                        <th><?php echo __('bom_name'); ?></th>
                                        <th><?php echo __('materials'); ?></th>
                                        <th><?php echo __('actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($boms as $bom): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($bom['code']); ?></td>
                                        <td><?php echo htmlspecialchars($bom['name']); ?></td>
                                        <td><?php echo $bom['items_count']; ?> items</td>
                                        <td>
                                            <a href="form.php?id=<?php echo $bom['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete('<?php echo $bom['id']; ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo __('confirm_delete'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php echo __('confirm_delete'); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <?php echo __('cancel'); ?>
                    </button>
                    <form id="deleteForm" action="../../controllers/BomController.php" method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="btn btn-danger">
                            <?php echo __('delete'); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function confirmDelete(id) {
        document.getElementById('deleteId').value = id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
