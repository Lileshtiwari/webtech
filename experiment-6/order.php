<?php
require_once __DIR__ . '/db.php';
requireAuth();

$pdo = getFoodDbConnection();
$orderId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$isConfirmed = !empty($_GET['confirmed']);
$error = '';

// ===================================================================
// Case 1: Displaying an Existing / Newly Confirmed Order Summary
// ===================================================================
if ($orderId) {
    // Ensure ordinary users cannot view other people's orders!
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE order_id = :id');
    $stmt->execute([':id' => $orderId]);
    $order = $stmt->fetch();

    if (!$order || (!isAdmin() && (int)$order['user_id'] !== (int)$_SESSION['user_id'])) {
        $_SESSION['flash_error'] = 'Order not found or access unauthorized.';
        header('Location: order-history.php');
        exit;
    }

    // Retrieve order items with food names
    $itemStmt = $pdo->prepare('
        SELECT oi.*, fi.food_name, fi.category, fi.image_url 
        FROM order_items oi 
        JOIN food_items fi ON oi.food_id = fi.food_id 
        WHERE oi.order_id = :oid
    ');
    $itemStmt->execute([':oid' => $orderId]);
    $orderItems = $itemStmt->fetchAll();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Order Confirmation #<?= htmlspecialchars((string)$orderId) ?> | Campus Food Hub</title>
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
            <li><a href="cart.php" class="foco-nav-link">Cart</a></li>
            <li><a href="order-history.php" class="foco-nav-link active">My Orders</a></li>
          </ul>
        </nav>

        <div class="foco-user-actions">
          <a href="index.php" class="btn-foco-login" style="border: 1px solid var(--foco-border); padding: 0.45rem 1rem;">Back to Menu</a>
        </div>
      </header>

      <main class="container" style="padding: 3rem 1.5rem 5rem; max-width: 820px;">
        <?php if ($isConfirmed): ?>
          <div class="alert alert-success" style="margin-bottom: 2rem;">
            <svg class="alert-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            <div>
              <strong style="font-size: 1.05rem;">Order Placed Successfully!</strong><br>
              Your gourmet order has been saved via ACID database transaction and sent to the campus kitchen.
            </div>
          </div>
        <?php endif; ?>

        <div class="card" style="padding: 2.5rem;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-light); flex-wrap: wrap; gap: 1rem;">
            <div>
              <span style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">
                Order Reference Receipt
              </span>
              <h1 style="font-size: 2rem; font-weight: 800; color: var(--text-main);">
                Order #<?= htmlspecialchars((string)$order['order_id']) ?>
              </h1>
              <div style="font-size: 0.875rem; color: var(--text-muted);">
                Placed on: <?= htmlspecialchars(date('d M Y, h:i A', strtotime($order['order_date']))) ?>
              </div>
            </div>

            <div>
              <span class="status-badge <?= htmlspecialchars(str_replace(' ', '_', $order['order_status'])) ?>">
                Status: <?= htmlspecialchars($order['order_status']) ?>
              </span>
            </div>
          </div>

          <!-- Items Table -->
          <div style="margin: 2rem 0;">
            <h3 style="font-size: 1.15rem; margin-bottom: 1rem;">Ordered Delicacies</h3>
            <div class="table-responsive">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Unit Price</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($orderItems as $oi): ?>
                    <tr>
                      <td style="font-weight: 700; color: var(--text-main);">
                        <?= htmlspecialchars($oi['food_name']) ?>
                      </td>
                      <td>
                        <span class="badge badge-muted"><?= htmlspecialchars($oi['category']) ?></span>
                      </td>
                      <td style="font-family: var(--font-mono);">
                        ₹<?= number_format($oi['price'], 2) ?>
                      </td>
                      <td style="text-align: center; font-weight: 700;">
                        <?= htmlspecialchars((string)$oi['quantity']) ?>
                      </td>
                      <td style="text-align: right; font-family: var(--font-mono); font-weight: 700; color: var(--primary);">
                        ₹<?= number_format($oi['price'] * $oi['quantity'], 2) ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
              <div style="min-width: 260px;">
                <div class="summary-row">
                  <span style="color: var(--text-muted);">Delivery Fee:</span>
                  <span style="color: var(--success); font-weight: 600;">FREE</span>
                </div>
                <div class="summary-row total">
                  <span>Grand Total:</span>
                  <span style="color: var(--primary);">₹<?= number_format($order['total_amount'], 2) ?></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Delivery Destination Info -->
          <div style="background: var(--bg-subtle); border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 1.5rem; margin-top: 2rem;">
            <h4 style="font-size: 1rem; color: var(--text-main); margin-bottom: 0.75rem;">Delivery Destination Details</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.9rem;">
              <div>
                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; display: block;">Recipient Name:</span>
                <strong><?= htmlspecialchars($order['delivery_name']) ?></strong>
              </div>
              <div>
                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; display: block;">Contact Mobile:</span>
                <strong><?= htmlspecialchars($order['delivery_mobile']) ?></strong>
              </div>
              <div style="grid-column: span 2;">
                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; display: block;">Campus Address:</span>
                <div><?= htmlspecialchars($order['delivery_address']) ?></div>
              </div>
            </div>
          </div>

          <div style="display: flex; justify-content: space-between; margin-top: 2rem; flex-wrap: wrap; gap: 1rem;">
            <a href="order-history.php" class="btn btn-secondary">
              <span>View Past Order History</span>
            </a>
            <a href="index.php" class="btn btn-primary">
              <span>Browse Menu for More</span>
            </a>
          </div>
        </div>
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
    <?php
    exit;
}

// ===================================================================
// Case 2: Placing Order via POST Transaction
// ===================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['cart'])) {
        header('Location: cart.php');
        exit;
    }

    $deliveryName    = trim($_POST['delivery_name'] ?? '');
    $deliveryMobile  = trim($_POST['delivery_mobile'] ?? '');
    $deliveryAddress = trim($_POST['delivery_address'] ?? '');

    if (empty($deliveryName) || empty($deliveryMobile) || empty($deliveryAddress)) {
        $error = 'Please fill out all delivery recipient details.';
    } elseif (!preg_match('/^[6-9]\d{9}$/', $deliveryMobile)) {
        $error = 'Delivery contact mobile must be a valid 10-digit number.';
    } else {
        // Run ACID Transaction
        try {
            $pdo->beginTransaction();

            $cart = $_SESSION['cart'];
            $ids = array_keys($cart);
            $inClause = implode(',', array_fill(0, count($ids), '?'));

            // Query items with lock to re-verify availability and prices
            $itemQuery = $pdo->prepare("SELECT food_id, food_name, price, availability FROM food_items WHERE food_id IN ($inClause) FOR UPDATE");
            $itemQuery->execute($ids);
            $verifiedItems = $itemQuery->fetchAll();

            if (count($verifiedItems) !== count($ids)) {
                throw new Exception('One or more selected menu items are no longer listed.');
            }

            $serverTotal = 0.00;
            $orderItemsToInsert = [];

            foreach ($verifiedItems as $vi) {
                if (!$vi['availability']) {
                    throw new Exception("Item '{$vi['food_name']}' is currently out of stock.");
                }
                $qty = (int)$cart[$vi['food_id']];
                if ($qty <= 0) continue;

                $price = (float)$vi['price'];
                $sub = $price * $qty;
                $serverTotal += $sub;

                $orderItemsToInsert[] = [
                    'food_id'  => $vi['food_id'],
                    'quantity' => $qty,
                    'price'    => $price
                ];
            }

            if (empty($orderItemsToInsert) || $serverTotal <= 0) {
                throw new Exception('Cart is empty or invalid.');
            }

            // 1. Insert into orders table
            $insOrder = $pdo->prepare('
                INSERT INTO orders 
                (user_id, order_date, total_amount, delivery_name, delivery_mobile, delivery_address, order_status)
                VALUES 
                (:uid, NOW(), :total, :dname, :dmobile, :daddr, "Pending")
            ');
            $insOrder->execute([
                ':uid'     => $_SESSION['user_id'],
                ':total'   => $serverTotal,
                ':dname'   => $deliveryName,
                ':dmobile' => $deliveryMobile,
                ':daddr'   => $deliveryAddress
            ]);

            $newOrderId = (int)$pdo->lastInsertId();

            // 2. Insert order items
            $insItem = $pdo->prepare('
                INSERT INTO order_items (order_id, food_id, quantity, price)
                VALUES (:oid, :fid, :qty, :price)
            ');

            foreach ($orderItemsToInsert as $oItem) {
                $insItem->execute([
                    ':oid'   => $newOrderId,
                    ':fid'   => $oItem['food_id'],
                    ':qty'   => $oItem['quantity'],
                    ':price' => $oItem['price']
                ]);
            }

            // Commit transaction
            $pdo->commit();

            // Clear Cart only after successful commit
            $_SESSION['cart'] = [];

            header('Location: order.php?id=' . $newOrderId . '&confirmed=1');
            exit;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Order placement failed: ' . $e->getMessage();
        }
    }
}

// ===================================================================
// Case 3: Checkout Form Presentation (Enter Delivery Details)
// ===================================================================
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$ids = array_keys($_SESSION['cart']);
$inClause = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT food_id, food_name, price FROM food_items WHERE food_id IN ($inClause)");
$stmt->execute($ids);
$checkoutItems = $stmt->fetchAll();

$checkoutTotal = 0.00;
foreach ($checkoutItems as $ci) {
    $checkoutTotal += $ci['price'] * ($_SESSION['cart'][$ci['food_id']] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout Delivery Details | Campus Food Hub</title>
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
        <li><a href="order-history.php" class="foco-nav-link">My Orders</a></li>
      </ul>
    </nav>

    <div class="foco-user-actions">
      <a href="cart.php" class="btn-foco-login" style="border: 1px solid var(--foco-border); padding: 0.45rem 1rem;">&larr; Return to Cart</a>
    </div>
  </header>

  <main class="container" style="padding: 3rem 1.5rem 5rem; max-width: 780px;">
    <div class="card" style="padding: 2.5rem;">
      <div style="margin-bottom: 2rem;">
        <span class="badge badge-warning" style="margin-bottom: 0.5rem;">Secure Checkout</span>
        <h1 style="font-size: 1.85rem; margin-bottom: 0.5rem;">Campus Delivery Details</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">
          Verify your delivery coordinates. Your order will be processed inside a MySQL ACID transaction block.
        </p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <!-- Pre-Order Mini Cart Breakdown -->
      <div style="background: var(--bg-subtle); border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 1.25rem 1.5rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
          <span style="font-weight: 700; color: var(--text-main);">Order Total (<?= count($checkoutItems) ?> items):</span>
          <span style="font-family: var(--font-mono); font-size: 1.25rem; font-weight: 800; color: var(--primary);">₹<?= number_format($checkoutTotal, 2) ?></span>
        </div>
        <div style="font-size: 0.8rem; color: var(--text-muted);">
          Prices verified against active MySQL catalog. Free campus delivery included.
        </div>
      </div>

      <form action="order.php" method="POST">
        <div class="form-group">
          <label for="delivery_name" class="form-label">Customer / Recipient Name <span class="req">*</span></label>
          <input type="text" id="delivery_name" name="delivery_name" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label for="delivery_mobile" class="form-label">Contact Mobile Number <span class="req">*</span></label>
          <input type="tel" id="delivery_mobile" name="delivery_mobile" class="form-control" maxlength="10" value="<?= htmlspecialchars($_SESSION['user_mobile'] ?? '') ?>" required>
          <span class="form-hint">Delivery rider will contact this phone number upon arrival.</span>
        </div>

        <div class="form-group">
          <label for="delivery_address" class="form-label">Hostel Block / Department Delivery Address <span class="req">*</span></label>
          <textarea id="delivery_address" name="delivery_address" class="form-control" rows="3" required><?= htmlspecialchars($_SESSION['user_address'] ?? '') ?></textarea>
          <span class="form-hint">Specify room number, hostel name, or department lab room.</span>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
          <button type="submit" class="btn btn-primary" style="flex: 2; padding: 0.85rem;">
            <span>Confirm & Place Order (₹<?= number_format($checkoutTotal, 2) ?>)</span>
          </button>
          <a href="cart.php" class="btn btn-secondary" style="flex: 1;">Cancel</a>
        </div>
      </form>
    </div>
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
