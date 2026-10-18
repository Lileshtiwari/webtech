<?php
require_once __DIR__ . '/db.php';
requireAuth();

$pdo = getFoodDbConnection();
$userId = (int)$_SESSION['user_id'];

// Retrieve only this user's orders (Security isolation)
$stmt = $pdo->prepare('
    SELECT * FROM orders 
    WHERE user_id = :uid 
    ORDER BY order_id DESC
');
$stmt->execute([':uid' => $userId]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Order History | Campus Food Hub - Experiment 6</title>
  <link rel="stylesheet" href="../assets/css/design-system.css">
  <link rel="stylesheet" href="style.css">
</head>
<body class="foco-theme">

  <!-- Main Foco Brand Header -->
  <header class="foco-navbar" style="max-width: 1320px; margin: 0 auto; padding: 1.5rem 2rem;">
    <a href="index.php" class="foco-brand">
      <span>foco</span>
      <span class="foco-brand-dot"></span>
    </a>

    <nav aria-label="Foco Navigation">
      <ul class="foco-nav-links">
        <li><a href="index.php" class="foco-nav-link">Home</a></li>
        <li><a href="index.php#menuSection" class="foco-nav-link">Menu</a></li>
        <li><a href="cart.php" class="foco-nav-link">Cart (<?= getCartCount() ?>)</a></li>
        <li><a href="order-history.php" class="foco-nav-link active">My Orders</a></li>
      </ul>
    </nav>

    <div class="foco-user-actions">
      <span style="font-size: 0.85rem; color: #11142D; font-weight: 600;">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
      <a href="logout.php" class="btn-foco-login" style="border: 1px solid var(--foco-border); padding: 0.45rem 1rem;">Sign Out</a>
    </div>
  </header>

  <main class="container" style="padding-bottom: 5rem;">
    <div style="margin-bottom: 2rem;">
      <h1 style="font-size: 2rem; font-weight: 800;">Your Order History</h1>
      <p style="color: var(--text-muted); font-size: 0.95rem;">
        Secure record of all campus food deliveries associated with user account <strong><?= htmlspecialchars($_SESSION['user_email']) ?></strong>.
      </p>
    </div>

    <?php if (empty($orders)): ?>
      <div class="card" style="text-align: center; padding: 4rem 1.5rem;">
        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.5" style="margin: 0 auto 1rem;">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/>
        </svg>
        <h3 style="font-size: 1.35rem; margin-bottom: 0.5rem;">No Previous Orders Found</h3>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">You haven't placed any food orders yet.</p>
        <a href="index.php" class="btn btn-primary">Browse Food Menu</a>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>Order Ref</th>
              <th>Date & Time</th>
              <th>Recipient</th>
              <th>Destination Address</th>
              <th>Total Amount</th>
              <th>Status</th>
              <th>Receipt</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $ord): ?>
              <tr>
                <td style="font-family: var(--font-mono); font-weight: 700; color: var(--primary);">
                  #<?= htmlspecialchars((string)$ord['order_id']) ?>
                </td>
                <td style="font-size: 0.85rem; color: var(--text-muted); white-space: nowrap;">
                  <?= htmlspecialchars(date('d M Y, h:i A', strtotime($ord['order_date']))) ?>
                </td>
                <td style="font-weight: 600;">
                  <?= htmlspecialchars($ord['delivery_name']) ?>
                </td>
                <td style="font-size: 0.85rem; max-width: 250px;">
                  <?= htmlspecialchars($ord['delivery_address']) ?>
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
                  <a href="order.php?id=<?= $ord['order_id'] ?>" class="btn btn-outline btn-sm">
                    View Details &rarr;
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </main>

  <!-- Centered Standard Product Footer -->
  <footer class="foco-centered-footer">
    <div>
      <a href="index.php" class="foco-footer-logo">
        <span>foco</span>
        <span class="foco-brand-dot"></span>
      </a>
      <p class="foco-footer-desc">
        foco delivers chef-prepared gourmet meals directly across campus with ultra-fast delivery.<br>
        Crafted with precision full-stack engineering, live inventory, and transactional safety.
      </p>
      <div>
        <a href="../index.html" class="foco-footer-btn">
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
