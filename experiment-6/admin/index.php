<?php
require_once __DIR__ . '/../db.php';
requireAdmin();

$pdo = getFoodDbConnection();

// Gather Metrics
$metrics = [
    'total_orders'   => 0,
    'total_revenue'  => 0.00,
    'pending_orders' => 0,
    'total_items'    => 0
];

if ($pdo) {
    try {
        $mStmt = $pdo->query('
            SELECT 
                COUNT(*) as total_orders, 
                COALESCE(SUM(total_amount), 0) as total_revenue,
                SUM(CASE WHEN order_status = "Pending" THEN 1 ELSE 0 END) as pending_orders
            FROM orders
        ');
        $mRow = $mStmt->fetch();
        $metrics['total_orders']   = (int)$mRow['total_orders'];
        $metrics['total_revenue']  = (float)$mRow['total_revenue'];
        $metrics['pending_orders'] = (int)$mRow['pending_orders'];

        $iStmt = $pdo->query('SELECT COUNT(*) FROM food_items');
        $metrics['total_items'] = (int)$iStmt->fetchColumn();

        // Recent 5 orders
        $recentOrders = $pdo->query('
            SELECT o.*, u.name as customer_name, u.email as customer_email 
            FROM orders o 
            JOIN users u ON o.user_id = u.user_id 
            ORDER BY o.order_id DESC 
            LIMIT 5
        ')->fetchAll();

    } catch (Exception $e) {
        error_log("Admin metrics error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | Campus Food Hub - Experiment 6</title>
  <link rel="stylesheet" href="../../assets/css/design-system.css">
  <link rel="stylesheet" href="../style.css">
</head>
<body style="background: var(--bg-app);">

  <!-- Admin Sub-Navigation -->
  <nav class="food-app-header" style="background: #0F172A; border-bottom: 1px solid #1E293B;">
    <div class="container food-header-inner">
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <span class="badge badge-warning">ADMINISTRATOR</span>
        <span style="font-weight: 800; color: #FFFFFF; font-size: 1.15rem;">Campus Food Hub Console</span>
      </div>

      <ul class="food-nav-menu">
        <li><a href="index.php" class="food-nav-link active" style="color: #FFFFFF;">Overview</a></li>
        <li><a href="orders.php" class="food-nav-link" style="color: #94A3B8;">Order Management</a></li>
        <li><a href="items.php" class="food-nav-link" style="color: #94A3B8;">Menu Item Catalog</a></li>
        <li><a href="../logout.php" class="btn btn-secondary btn-sm" style="background: #1E293B; color: #FFFFFF !important; border-color: #334155;">Log Out</a></li>
      </ul>
    </div>
  </nav>

  <main class="container" style="padding: 2.5rem 1.5rem 5rem;">
    <div style="margin-bottom: 2.5rem;">
      <h1 style="font-size: 2rem; font-weight: 800;">Administration Overview</h1>
      <p style="color: var(--text-muted); font-size: 0.95rem;">
        Role-based management center for viewing customer food orders, updating live order statuses, and maintaining menu items.
      </p>
    </div>

    <!-- Metric Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
      <div class="card" style="padding: 1.5rem;">
        <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total System Orders</span>
        <div style="font-size: 2.25rem; font-weight: 800; color: var(--primary); margin-top: 0.25rem;"><?= $metrics['total_orders'] ?></div>
      </div>

      <div class="card" style="padding: 1.5rem;">
        <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Cumulative Revenue</span>
        <div style="font-size: 2.25rem; font-weight: 800; color: var(--success); margin-top: 0.25rem;">₹<?= number_format($metrics['total_revenue'], 2) ?></div>
      </div>

      <div class="card" style="padding: 1.5rem;">
        <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Pending Orders</span>
        <div style="font-size: 2.25rem; font-weight: 800; color: var(--warning); margin-top: 0.25rem;"><?= $metrics['pending_orders'] ?></div>
      </div>

      <div class="card" style="padding: 1.5rem;">
        <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Food Items Listed</span>
        <div style="font-size: 2.25rem; font-weight: 800; color: var(--text-main); margin-top: 0.25rem;"><?= $metrics['total_items'] ?></div>
      </div>
    </div>

    <!-- Quick Navigation Actions -->
    <div style="display: flex; gap: 1rem; margin-bottom: 2.5rem; flex-wrap: wrap;">
      <a href="orders.php" class="btn btn-primary">
        <span>Manage All Orders &rarr;</span>
      </a>
      <a href="items.php" class="btn btn-secondary">
        <span>Add & Edit Food Items &rarr;</span>
      </a>
    </div>

    <!-- Recent Orders Section -->
    <div class="card" style="padding: 1.75rem;">
      <h3 style="font-size: 1.25rem; margin-bottom: 1.25rem;">Recent Customer Orders</h3>
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>Order Ref</th>
              <th>Customer Name</th>
              <th>Order Timestamp</th>
              <th>Total Amount</th>
              <th>Current Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($recentOrders)): ?>
              <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">No orders placed yet.</td></tr>
            <?php else: ?>
              <?php foreach ($recentOrders as $ro): ?>
                <tr>
                  <td style="font-family: var(--font-mono); font-weight: 700; color: var(--primary);">#<?= $ro['order_id'] ?></td>
                  <td style="font-weight: 600;"><?= htmlspecialchars($ro['customer_name']) ?></td>
                  <td style="font-size: 0.85rem; color: var(--text-muted);"><?= htmlspecialchars(date('d M, h:i A', strtotime($ro['order_date']))) ?></td>
                  <td style="font-family: var(--font-mono); font-weight: 700;">₹<?= number_format($ro['total_amount'], 2) ?></td>
                  <td>
                    <span class="status-badge <?= htmlspecialchars(str_replace(' ', '_', $ro['order_status'])) ?>">
                      <?= htmlspecialchars($ro['order_status']) ?>
                    </span>
                  </td>
                  <td>
                    <a href="orders.php?edit=<?= $ro['order_id'] ?>" class="btn btn-outline btn-sm">Update Status</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Centered Standard Product Footer -->
  <footer class="foco-centered-footer">
    <div>
      <a href="../index.php" class="foco-footer-logo">
        <span>foco</span>
        <span class="foco-brand-dot"></span>
      </a>
      <p class="foco-footer-desc">
        foco Admin Control Console &bull; Live MySQL Relational Catalog &amp; Order Pipeline
      </p>
      <div>
        <a href="../../index.html" class="foco-footer-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          <span>Return to Home Screen</span>
        </a>
      </div>
      <div class="foco-footer-copy">
        <div>&copy; 2026 foco Gourmet Delivery. All rights reserved.</div>
        <div style="margin-top: 0.25rem;" class="foco-designer-tag">Designed By Lilesh Kumar Tiwari</div>
      </div>
    </div>
  </footer>
</body>
</html>
