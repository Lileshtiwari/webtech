<?php
require_once __DIR__ . '/db.php';

$pdo = getFoodDbConnection();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$flashMsg = '';

// Handle Cart Actions
if ($action === 'add' || $action === 'add_ajax') {
    $foodId = filter_var($_GET['id'] ?? ($_POST['id'] ?? null), FILTER_VALIDATE_INT);
    $qty = filter_var($_GET['qty'] ?? ($_POST['qty'] ?? 1), FILTER_VALIDATE_INT) ?: 1;
    $itemTitle = 'Item';

    if ($foodId) {
        // Verify item exists and is available
        $check = $pdo->prepare('SELECT food_id, food_name, availability FROM food_items WHERE food_id = :id');
        $check->execute([':id' => $foodId]);
        $item = $check->fetch();

        if ($item && $item['availability']) {
            $itemTitle = $item['food_name'];
            $_SESSION['cart'][$foodId] = ($_SESSION['cart'][$foodId] ?? 0) + $qty;
            $_SESSION['flash_cart'] = 'Added ' . htmlspecialchars($itemTitle) . ' to your bag.';
        }
    }

    if ($action === 'add_ajax') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'count' => getCartCount(),
            'item' => $itemTitle,
            'qty' => $qty
        ]);
        exit;
    }

    $redirect = $_GET['redirect'] ?? 'cart.php';
    header('Location: ' . $redirect);
    exit;
}

if ($action === 'remove') {
    $foodId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if ($foodId && isset($_SESSION['cart'][$foodId])) {
        unset($_SESSION['cart'][$foodId]);
        $_SESSION['flash_cart'] = 'Item removed from your cart.';
    }
    header('Location: cart.php');
    exit;
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantities = $_POST['qty'] ?? [];
    foreach ($quantities as $id => $qty) {
        $id = (int)$id;
        $qty = (int)$qty;
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = min($qty, 99); // Safe upper bound
        }
    }
    $_SESSION['flash_cart'] = 'Cart quantities updated.';
    header('Location: cart.php');
    exit;
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    $_SESSION['flash_cart'] = 'Your cart has been cleared.';
    header('Location: cart.php');
    exit;
}

$flashCart = $_SESSION['flash_cart'] ?? null;
unset($_SESSION['flash_cart']);

// Retrieve item details from MySQL using trusted database prices
$cartItems = [];
$totalAmount = 0.00;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $inClause = implode(',', array_fill(0, count($ids), '?'));
    
    $stmt = $pdo->prepare("SELECT food_id, food_name, category, price, availability, image_url FROM food_items WHERE food_id IN ($inClause)");
    $stmt->execute($ids);
    $itemsFromDb = $stmt->fetchAll();

    foreach ($itemsFromDb as $row) {
        $id = $row['food_id'];
        $qty = $_SESSION['cart'][$id] ?? 0;
        if ($qty > 0) {
            $subtotal = $row['price'] * $qty;
            $totalAmount += $subtotal;
            $cartItems[] = [
                'food_id'    => $id,
                'name'       => $row['food_name'],
                'category'   => $row['category'],
                'price'      => (float)$row['price'],
                'quantity'   => $qty,
                'subtotal'   => $subtotal,
                'image_url'  => $row['image_url'],
                'available'  => (bool)$row['availability']
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Order Cart | Campus Food Hub - Experiment 6</title>
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
        <li><a href="cart.php" class="foco-nav-link active">Cart (<?= getCartCount() ?>)</a></li>
        <?php if (isLoggedIn()): ?>
          <li><a href="order-history.php" class="foco-nav-link">My Orders</a></li>
        <?php endif; ?>
      </ul>
    </nav>

    <div class="foco-user-actions">
      <?php if (isLoggedIn()): ?>
        <span style="font-size: 0.85rem; color: #11142D; font-weight: 600;">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <a href="logout.php" class="btn-foco-login" style="border: 1px solid var(--foco-border); padding: 0.45rem 1rem;">Sign Out</a>
      <?php else: ?>
        <a href="login.php" class="btn-foco-login" style="border: 1px solid var(--foco-border); padding: 0.45rem 1rem;">Sign In</a>
      <?php endif; ?>
    </div>
  </header>

  <main class="container" style="padding-bottom: 5rem;">
    <div style="margin-bottom: 2rem;">
      <h1 style="font-size: 2rem; font-weight: 800;">Shopping Cart</h1>
      <p style="color: var(--text-muted); font-size: 0.95rem;">Review your selected gourmet delicacies, adjust quantities, or proceed to checkout.</p>
    </div>

    <?php if ($flashCart): ?>
      <div class="alert alert-info"><?= htmlspecialchars($flashCart) ?></div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
      <div class="card" style="text-align: center; padding: 4rem 1.5rem;">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.5" style="margin: 0 auto 1rem;">
          <circle cx="9" cy="21" r="1"/>
          <circle cx="20" cy="21" r="1"/>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
        </svg>
        <h3 style="font-size: 1.35rem; margin-bottom: 0.5rem;">Your Cart is Currently Empty</h3>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem; max-width: 400px; margin-left: auto; margin-right: auto;">
          Looks like you haven't added any food items yet. Browse our menu to select fresh, delicious campus meals.
        </p>
        <a href="index.php" class="btn btn-primary">
          <span>Explore Food Menu &rarr;</span>
        </a>
      </div>
    <?php else: ?>
      <div class="cart-layout">
        <!-- Left: Items Table with Update Form -->
        <div class="card" style="padding: 1.75rem;">
          <form action="cart.php" method="POST">
            <input type="hidden" name="action" value="update">
            
            <div class="table-responsive">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Item Details</th>
                    <th>Price</th>
                    <th style="width: 130px;">Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($cartItems as $item): ?>
                    <tr>
                      <td>
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                          <div style="width: 50px; height: 50px; border-radius: 8px; overflow: hidden; background: var(--bg-subtle); flex-shrink: 0;">
                            <img src="../<?= htmlspecialchars($item['image_url'] ?: 'assets/images/food-burger.svg') ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                          </div>
                          <div>
                            <div style="font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($item['name']) ?></div>
                            <span class="badge badge-muted" style="font-size: 0.65rem;"><?= htmlspecialchars($item['category']) ?></span>
                          </div>
                        </div>
                      </td>
                      <td style="font-family: var(--font-mono); font-weight: 600;">
                        ₹<?= number_format($item['price'], 2) ?>
                      </td>
                      <td>
                        <input type="number" name="qty[<?= $item['food_id'] ?>]" value="<?= $item['quantity'] ?>" min="1" max="99" class="form-control" style="width: 80px; padding: 0.4rem; text-align: center;">
                      </td>
                      <td style="font-family: var(--font-mono); font-weight: 700; color: var(--primary);">
                        ₹<?= number_format($item['subtotal'], 2) ?>
                      </td>
                      <td>
                        <a href="cart.php?action=remove&id=<?= $item['food_id'] ?>" style="color: var(--danger); font-size: 0.85rem; font-weight: 600;">Remove</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; flex-wrap: wrap; gap: 1rem;">
              <a href="index.php" class="btn btn-secondary btn-sm">&larr; Continue Shopping</a>
              <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary btn-sm">Update Quantities</button>
                <a href="cart.php?action=clear" class="btn btn-outline btn-sm" style="color: var(--danger) !important; border-color: #FECACA;">Clear Cart</a>
              </div>
            </div>
          </form>
        </div>

        <!-- Right: Order Summary & Checkout -->
        <div class="order-summary-card">
          <h3>Order Summary</h3>
          <div class="summary-row">
            <span style="color: var(--text-muted);">Items Subtotal (<?= count($cartItems) ?> items):</span>
            <span style="font-family: var(--font-mono); font-weight: 600;">₹<?= number_format($totalAmount, 2) ?></span>
          </div>
          <div class="summary-row">
            <span style="color: var(--text-muted);">Campus Delivery Fee:</span>
            <span style="color: var(--success); font-weight: 600;">FREE</span>
          </div>
          <div class="summary-row">
            <span style="color: var(--text-muted);">Campus Taxes:</span>
            <span style="font-family: var(--font-mono); font-weight: 600;">₹0.00</span>
          </div>
          <div class="summary-row total">
            <span>Total Payable:</span>
            <span style="color: var(--primary);">₹<?= number_format($totalAmount, 2) ?></span>
          </div>

          <div style="margin-top: 1.75rem;">
            <?php if (isLoggedIn()): ?>
              <a href="order.php" class="btn btn-primary" style="width: 100%; padding: 0.85rem;">
                <span>Proceed to Checkout &rarr;</span>
              </a>
            <?php else: ?>
              <a href="login.php" class="btn btn-primary" style="width: 100%; padding: 0.85rem;">
                <span>Sign In to Checkout &rarr;</span>
              </a>
              <span style="font-size: 0.775rem; color: var(--text-muted); display: block; text-align: center; margin-top: 0.5rem;">
                Account required for delivery verification
              </span>
            <?php endif; ?>
          </div>
        </div>
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
