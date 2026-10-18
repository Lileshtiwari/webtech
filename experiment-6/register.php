<?php
require_once __DIR__ . '/db.php';

$errors = [];
$name = '';
$email = '';
$mobile = '';
$address = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $mobile = trim($_POST['mobile'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Validation
    if (empty($name) || strlen($name) < 2) {
        $errors['name'] = 'Full name is required (min 2 characters).';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Valid email address is required.';
    }

    if (empty($password) || strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }

    if (empty($mobile) || !preg_match('/^[6-9]\d{9}$/', $mobile)) {
        $errors['mobile'] = 'Valid 10-digit mobile number starting with 6-9 is required.';
    }

    if (empty($address) || strlen($address) < 5) {
        $errors['address'] = 'Detailed campus delivery address is required.';
    }

    if (empty($errors)) {
        $pdo = getFoodDbConnection();
        if (!$pdo) {
            $errors['db'] = 'Database connection failure. Please verify MySQL service.';
        } else {
            // Check existing user
            $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $errors['email'] = 'An account with this email address already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $ins = $pdo->prepare('INSERT INTO users (name, email, password, mobile, address, role, created_at) VALUES (:name, :email, :pass, :mobile, :address, "user", NOW())');
                $ins->execute([
                    ':name' => $name,
                    ':email' => $email,
                    ':pass' => $hash,
                    ':mobile' => $mobile,
                    ':address' => $address
                ]);

                $_SESSION['flash_success'] = 'Account registered successfully! You may now sign in.';
                header('Location: login.php');
                exit;
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
  <title>Create Account · foco Gourmet Food Delivery</title>
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
      <a href="login.php" class="btn-foco-login" style="border: 1px solid var(--foco-border);">Sign In</a>
    </div>
  </header>

  <!-- Stylish Centered Auth Card -->
  <main class="foco-auth-container">
    <div class="foco-auth-card" style="max-width: 540px;">
      <div class="foco-auth-header">
        <a href="index.php" class="foco-brand" style="justify-content: center; font-size: 2.2rem; margin-bottom: 0.75rem;">
          <span>foco</span>
          <span class="foco-brand-dot"></span>
        </a>
        <h1>Create Your Account</h1>
        <p>Join foco to browse curated gourmet menus, order express campus meals, and track your orders in real time.</p>
      </div>

      <?php if (!empty($errors['db'])): ?>
        <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.85rem; margin-bottom: 1.25rem;">
          <?= htmlspecialchars($errors['db']) ?>
        </div>
      <?php endif; ?>

      <form action="register.php" method="POST">
        <div class="foco-input-group">
          <label for="name" class="foco-input-label">Full Name</label>
          <input type="text" id="name" name="name" class="foco-input-control" placeholder="e.g. Ramesh Kumar" value="<?= htmlspecialchars($name) ?>" required autofocus>
          <?php if (isset($errors['name'])): ?><div style="color: #DC2626; font-size: 0.75rem; margin-top: 0.25rem; font-weight: 600;"><?= htmlspecialchars($errors['name']) ?></div><?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div class="foco-input-group">
            <label for="email" class="foco-input-label">Email Address</label>
            <input type="email" id="email" name="email" class="foco-input-control" placeholder="name@domain.com" value="<?= htmlspecialchars($email) ?>" required>
            <?php if (isset($errors['email'])): ?><div style="color: #DC2626; font-size: 0.75rem; margin-top: 0.25rem; font-weight: 600;"><?= htmlspecialchars($errors['email']) ?></div><?php endif; ?>
          </div>

          <div class="foco-input-group">
            <label for="mobile" class="foco-input-label">Mobile Number</label>
            <input type="tel" id="mobile" name="mobile" maxlength="10" class="foco-input-control" placeholder="10 digits (6-9)" value="<?= htmlspecialchars($mobile) ?>" required>
            <?php if (isset($errors['mobile'])): ?><div style="color: #DC2626; font-size: 0.75rem; margin-top: 0.25rem; font-weight: 600;"><?= htmlspecialchars($errors['mobile']) ?></div><?php endif; ?>
          </div>
        </div>

        <div class="foco-input-group">
          <label for="password" class="foco-input-label">Password</label>
          <input type="password" id="password" name="password" class="foco-input-control" placeholder="Minimum 6 characters" required>
          <?php if (isset($errors['password'])): ?><div style="color: #DC2626; font-size: 0.75rem; margin-top: 0.25rem; font-weight: 600;"><?= htmlspecialchars($errors['password']) ?></div><?php endif; ?>
        </div>

        <div class="foco-input-group">
          <label for="address" class="foco-input-label">Delivery Address</label>
          <textarea id="address" name="address" class="foco-input-control" rows="2" placeholder="Hostel Block, Room No, or Street Address" required><?= htmlspecialchars($address) ?></textarea>
          <?php if (isset($errors['address'])): ?><div style="color: #DC2626; font-size: 0.75rem; margin-top: 0.25rem; font-weight: 600;"><?= htmlspecialchars($errors['address']) ?></div><?php endif; ?>
        </div>

        <button type="submit" class="foco-btn-primary">
          <span>Create My Account</span>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 5"></polyline>
          </svg>
        </button>
      </form>

      <div style="text-align: center; margin-top: 1.75rem; font-size: 0.875rem; color: var(--foco-text-muted); padding-top: 1.25rem; border-top: 1px solid #F1F5F9;">
        Already registered? <a href="login.php" style="color: var(--foco-orange); font-weight: 700; text-decoration: none;">Sign in here</a>
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
