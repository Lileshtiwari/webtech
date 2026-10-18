<?php
require_once __DIR__ . '/../db.php';
requireAdmin();

$pdo = getFoodDbConnection();
$flashSuccess = '';
$flashError = '';

// Handle Status Update Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $orderId = filter_var($_POST['order_id'] ?? null, FILTER_VALIDATE_INT);
    $newStatus = trim($_POST['order_status'] ?? '');
    $validStatuses = ['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'];

    if ($orderId && in_array($newStatus, $validStatuses)) {
        try {
            $stmt = $pdo->prepare('UPDATE orders SET order_status = :status WHERE order_id = :id');
            $stmt->execute([':status' => $newStatus, ':id' => $orderId]);
            $flashSuccess = "Order #$orderId status updated to '$newStatus'.";
        } catch (PDOException $e) {
            $flashError = 'Failed to update order status: ' . $e->getMessage();
        }
    }
}

// Filter orders
$statusFilter = trim($_GET['status'] ?? '');
$ordersList = [];

if ($pdo) {
    try {
        $sql = "
            SELECT o.*, u.name as customer_name, u.email as customer_email, u.mobile as customer_mobile 
            FROM orders o 
            JOIN users u ON o.user_id = u.user_id 
            WHERE 1=1
        ";
        $params = [];
        if (!empty($statusFilter) && $statusFilter !== 'All') {
            $sql .= " AND o.order_status = :status";
            $params[':status'] = $statusFilter;
        }
        $sql .= " ORDER BY o.order_id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $ordersList = $stmt->fetchAll();
    } catch (PDOException $e) {
        $flashError = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Customer Orders | Admin Console - Experiment 6</title>
  <link rel="stylesheet" href="../../assets/css/design-system.css">
  <link rel="stylesheet" href="../style.css">
</head>
<body style="background: var(--bg-app);">

  <!-- Admin Sub Navigation -->
  <nav class="food-app-header" style="background: #0F172A; border-bottom: 1px solid #1E293B;">
    <div class="container food-header-inner">
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <span class="badge badge-warning">ADMINISTRATOR</span>
        <span style="font-weight: 800; color: #FFFFFF; font-size: 1.15rem;">Campus Food Hub Console</span>
      </div>

      <ul class="food-nav-menu">
        <li><a href="index.php" class="food-nav-link" style="color: #94A3B8;">Overview</a></li>
        <li><a href="orders.php" class="food-nav-link active" style="color: #FFFFFF;">Order Management</a></li>
        <li><a href="items.php" class="food-nav-link" style="color: #94A3B8;">Menu Item Catalog</a></li>
        <li><a href="../logout.php" class="btn btn-secondary btn-sm" style="background: #1E293B; color: #FFFFFF !important; border-color: #334155;">Log Out</a></li>
      </ul>
    </div>
  </nav>

  <main class="container" style="padding: 2.5rem 1.5rem 5rem;">
    <div style="margin-bottom: 2rem;">
      <h1 style="font-size: 2rem; font-weight: 800;">Order Management & Fulfillment</h1>
      <p style="color: var(--text-muted); font-size: 0.95rem;">
        Monitor real-time student orders and transition delivery states.
      </p>
    </div>

    <?php if ($flashSuccess): ?>
      <div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div>
    <?php endif; ?>
    <?php if ($flashError): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div>
    <?php endif; ?>

    <!-- Status Filter Strip -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 2rem; overflow-x: auto; padding-bottom: 0.5rem;">
      <?php 
        $statuses = ['All', 'Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'];
        $curStat = empty($statusFilter) ? 'All' : $statusFilter;
        foreach ($statuses as $st):
          $active = ($curStat === $st);
      ?>
        <a href="orders.php?status=<?= urlencode($st) ?>" class="cat-pill <?= $active ? 'active' : '' ?>">
          <?= htmlspecialchars($st) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Orders Table -->
    <div class="table-responsive">
      <table class="data-table">
        <thead>
          <tr>
            <th>Order Ref</th>
            <th>Customer</th>
            <th>Address</th>
            <th>Date & Time</th>
            <th>Amount</th>
            <th>Current Status</th>
            <th>Update Fulfillment</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($ordersList)): ?>
            <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 3rem;">No orders match this status filter.</td></tr>
          <?php else: ?>
            <?php foreach ($ordersList as $ord): ?>
              <tr>
                <td style="font-family: var(--font-mono); font-weight: 700; color: var(--primary);">
                  #<?= $ord['order_id'] ?>
                </td>
                <td>
                  <div style="font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($ord['customer_name']) ?></div>
                  <div style="font-size: 0.775rem; color: var(--text-muted);"><?= htmlspecialchars($ord['delivery_mobile']) ?></div>
                </td>
                <td style="font-size: 0.825rem; max-width: 200px;">
                  <?= htmlspecialchars($ord['delivery_address']) ?>
                </td>
                <td style="font-size: 0.8rem; color: var(--text-muted); white-space: nowrap;">
                  <?= htmlspecialchars(date('d M, h:i A', strtotime($ord['order_date']))) ?>
                </td>
                <td style="font-family: var(--font-mono); font-weight: 800; color: var(--primary);">
                  ₹<?= number_format($ord['total_amount'], 2) ?>
                </td>
                <td>
                  <span class="status-badge <?= htmlspecialchars(str_replace(' ', '_', $ord['order_status'])) ?>">
                    <?= htmlspecialchars($ord['order_status']) ?>
                  </span>
                </td>
                <td>
                  <form action="orders.php" method="POST" style="display: flex; gap: 0.4rem; align-items: center;">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="order_id" value="<?= $ord['order_id'] ?>">
                    <select name="order_status" class="form-control" style="font-size: 0.8rem; padding: 0.35rem 0.6rem; width: 140px;">
                      <?php foreach (['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'] as $opt): ?>
                        <option value="<?= $opt ?>" <?= ($ord['order_status'] === $opt) ? 'selected' : '' ?>><?= $opt ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.65rem;">Save</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
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
