<?php
require_once '../../config/config.php';
requireLogin();

// Get stock data
try {
    $query = "SELECT 
        m.name,
        m.current_stock as actual_stock,
        m.min_stock,
        m.max_stock,
        m.unit
    FROM materials m
    ORDER BY m.name";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching stock data: " . $e->getMessage());
    $stocks = [];
}
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('dashboard') . ' - ' . __('app_name'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .stock-card {
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .chart-container {
            margin-top: 20px;
            height: 400px;
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
                <a href="index.php" class="active">
                    <i class="bi bi-speedometer2"></i> <?php echo __('dashboard'); ?>
                </a>
                <a href="../bom/index.php">
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
                <h2 class="mb-4"><?php echo __('dashboard'); ?></h2>

                <!-- Stock Table -->
                <div class="card stock-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><?php echo __('stock_levels'); ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo __('materials'); ?></th>
                                        <th><?php echo __('actual_stock'); ?></th>
                                        <th><?php echo __('minimum_stock'); ?></th>
                                        <th><?php echo __('maximum_stock'); ?></th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stocks as $stock): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($stock['name']); ?></td>
                                        <td><?php echo number_format($stock['actual_stock'], 2); ?></td>
                                        <td><?php echo number_format($stock['min_stock'], 2); ?></td>
                                        <td><?php echo number_format($stock['max_stock'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($stock['unit']); ?></td>
                                        <td>
                                            <?php
                                            if ($stock['actual_stock'] < $stock['min_stock']) {
                                                echo '<span class="badge bg-danger">Low Stock</span>';
                                            } elseif ($stock['actual_stock'] > $stock['max_stock']) {
                                                echo '<span class="badge bg-warning">Over Stock</span>';
                                            } else {
                                                echo '<span class="badge bg-success">Normal</span>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Stock Chart -->
                <div class="card stock-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><?php echo __('stock_levels'); ?> - <?php echo __('chart'); ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="stockChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Prepare chart data
    const stocks = <?php echo json_encode($stocks); ?>;
    const labels = stocks.map(stock => stock.name);
    const actualStocks = stocks.map(stock => stock.actual_stock);
    const minStocks = stocks.map(stock => stock.min_stock);
    const maxStocks = stocks.map(stock => stock.max_stock);

    // Create chart
    const ctx = document.getElementById('stockChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: '<?php echo __('actual_stock'); ?>',
                    data: actualStocks,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: '<?php echo __('minimum_stock'); ?>',
                    data: minStocks,
                    type: 'line',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    fill: false
                },
                {
                    label: '<?php echo __('maximum_stock'); ?>',
                    data: maxStocks,
                    type: 'line',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
