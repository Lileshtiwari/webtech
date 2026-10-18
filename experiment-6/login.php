<?php
require_once __DIR__ . '/db.php';

$error = '';
$flashSuccess = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $pdo = getFoodDbConnection();
        if (!$pdo) {
            $error = 'Database connection failure. Please ensure MySQL is running.';
        } else {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int)$user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_mobile'] = $user['mobile'];
                $_SESSION['user_address'] = $user['address'];
                $_SESSION['user_role'] = $user['role'];

                $redirect = $_SESSION['redirect_after_login'] ?? ($user['role'] === 'admin' ? 'admin/index.php' : 'index.php');
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;
            } else {
                $error = 'Invalid email address or password. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In · foco Gourmet Food Delivery</title>
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
      </ul>
    </nav>

    <div class="foco-user-actions">
      <a href="register.php" class="btn-foco-login" style="border: 1px solid var(--foco-border);">Create Account</a>
    </div>
  </header>

  <!-- Stylish Centered Auth Card -->
  <main class="foco-auth-container">
    <div class="foco-auth-card">
      <div class="foco-auth-header">
        <a href="index.php" class="foco-brand" style="justify-content: center; font-size: 2.2rem; margin-bottom: 0.75rem;">
          <span>foco</span>
          <span class="foco-brand-dot"></span>
        </a>
        <h1>Welcome Back</h1>
        <p>Sign in to your account to order gourmet food, customize ingredients, and track campus deliveries in real time.</p>
      </div>

      <?php if ($flashSuccess): ?>
        <div style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.85rem; margin-bottom: 1.25rem;">
          <?= htmlspecialchars($flashSuccess) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.85rem; margin-bottom: 1.25rem;">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form action="login.php" method="POST">
        <div class="foco-input-group">
          <label for="email" class="foco-input-label">Email Address</label>
          <input type="email" id="email" name="email" class="foco-input-control" placeholder="name@domain.com" required autofocus>
        </div>

        <div class="foco-input-group">
          <div style="display: flex; justify-content: space-between; align-items: baseline;">
            <label for="password" class="foco-input-label">Password</label>
          </div>
          <input type="password" id="password" name="password" class="foco-input-control" placeholder="Enter your secure password" required>
        </div>

        <button type="submit" class="foco-btn-primary">
          <span>Sign In to Account</span>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </button>
      </form>

      <div style="text-align: center; margin-top: 1.75rem; font-size: 0.875rem; color: var(--foco-text-muted); padding-top: 1.25rem; border-top: 1px solid #F1F5F9;">
        Don't have an account yet? <a href="register.php" style="color: var(--foco-orange); font-weight: 700; text-decoration: none;">Create an account</a>
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
