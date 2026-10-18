<?php
require_once __DIR__ . '/../db.php';
requireAdmin();

$pdo = getFoodDbConnection();
$flashSuccess = '';
$flashError = '';

$editItem = null;
$editId = filter_var($_GET['edit'] ?? null, FILTER_VALIDATE_INT);

// 1. Handle POST Actions (Create, Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $name        = trim($_POST['food_name'] ?? '');
        $category    = trim($_POST['category'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price       = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
        $availability = isset($_POST['availability']) ? 1 : 0;
        $imageUrl    = trim($_POST['image_url'] ?? 'assets/images/food-burger.svg');

        if (empty($name) || empty($category) || empty($description) || $price === false || $price < 0) {
            $flashError = 'Please provide valid values for all required food item fields.';
        } else {
            try {
                $stmt = $pdo->prepare('
                    INSERT INTO food_items (food_name, category, description, price, availability, image_url, created_at)
                    VALUES (:name, :cat, :desc, :price, :avail, :img, NOW())
                ');
                $stmt->execute([
                    ':name'  => $name,
                    ':cat'   => $category,
                    ':desc'  => $description,
                    ':price' => $price,
                    ':avail' => $availability,
                    ':img'   => $imageUrl
                ]);
                $flashSuccess = "Food item '{$name}' created successfully.";
            } catch (PDOException $e) {
                $flashError = 'Failed to create item: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'update') {
        $foodId      = filter_var($_POST['food_id'] ?? null, FILTER_VALIDATE_INT);
        $name        = trim($_POST['food_name'] ?? '');
        $category    = trim($_POST['category'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price       = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
        $availability = isset($_POST['availability']) ? 1 : 0;
        $imageUrl    = trim($_POST['image_url'] ?? 'assets/images/food-burger.svg');

        if (!$foodId || empty($name) || empty($category) || empty($description) || $price === false || $price < 0) {
            $flashError = 'Please provide valid values for all required food item fields.';
        } else {
            try {
                $stmt = $pdo->prepare('
                    UPDATE food_items 
                    SET food_name = :name, category = :cat, description = :desc, price = :price, availability = :avail, image_url = :img
                    WHERE food_id = :id
                ');
                $stmt->execute([
                    ':name'  => $name,
                    ':cat'   => $category,
                    ':desc'  => $description,
                    ':price' => $price,
                    ':avail' => $availability,
                    ':img'   => $imageUrl,
                    ':id'    => $foodId
                ]);
                $flashSuccess = "Food item '{$name}' updated successfully.";
                $editId = null; // Clear edit mode
            } catch (PDOException $e) {
                $flashError = 'Failed to update item: ' . $e->getMessage();
            }
        }
    }
}

// 2. Handle GET actions (toggle, delete)
if (isset($_GET['toggle'])) {
    $tId = filter_var($_GET['toggle'], FILTER_VALIDATE_INT);
    if ($tId) {
        try {
            $stmt = $pdo->prepare('UPDATE food_items SET availability = 1 - availability WHERE food_id = :id');
            $stmt->execute([':id' => $tId]);
            $flashSuccess = 'Item availability toggled.';
        } catch (PDOException $e) {
            $flashError = $e->getMessage();
        }
    }
}

if (isset($_GET['delete'])) {
    $dId = filter_var($_GET['delete'], FILTER_VALIDATE_INT);
    if ($dId) {
        try {
            // Check if item has order references
            $chk = $pdo->prepare('SELECT COUNT(*) FROM order_items WHERE food_id = :id');
            $chk->execute([':id' => $dId]);
            if ($chk->fetchColumn() > 0) {
                // To preserve historical order referential integrity, toggle availability instead of deleting foreign key records
                $stmt = $pdo->prepare('UPDATE food_items SET availability = 0 WHERE food_id = :id');
                $stmt->execute([':id' => $dId]);
                $flashSuccess = 'Item is linked to historical customer orders. It has been marked Out of Stock instead of permanently dropped.';
            } else {
                $stmt = $pdo->prepare('DELETE FROM food_items WHERE food_id = :id');
                $stmt->execute([':id' => $dId]);
                $flashSuccess = 'Item deleted successfully.';
            }
        } catch (PDOException $e) {
            $flashError = 'Delete operation failed: ' . $e->getMessage();
        }
    }
}

// Fetch item if editing
if ($editId) {
    $stmt = $pdo->prepare('SELECT * FROM food_items WHERE food_id = :id');
    $stmt->execute([':id' => $editId]);
    $editItem = $stmt->fetch();
}

// Retrieve all items
$allItems = $pdo->query('SELECT * FROM food_items ORDER BY food_id ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Menu Items | Admin Console - Experiment 6</title>
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
        <li><a href="orders.php" class="food-nav-link" style="color: #94A3B8;">Order Management</a></li>
        <li><a href="items.php" class="food-nav-link active" style="color: #FFFFFF;">Menu Item Catalog</a></li>
        <li><a href="../logout.php" class="btn btn-secondary btn-sm" style="background: #1E293B; color: #FFFFFF !important; border-color: #334155;">Log Out</a></li>
      </ul>
    </div>
  </nav>

  <main class="container" style="padding: 2.5rem 1.5rem 5rem;">
    <div style="margin-bottom: 2rem;">
      <h1 style="font-size: 2rem; font-weight: 800;">Menu Catalog Management</h1>
      <p style="color: var(--text-muted); font-size: 0.95rem;">
        Add new culinary offerings, adjust pricing, update descriptions, and toggle stock availability.
      </p>
    </div>

    <?php if ($flashSuccess): ?>
      <div class="alert alert-success"><?= htmlspecialchars($flashSuccess) ?></div>
    <?php endif; ?>
    <?php if ($flashError): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($flashError) ?></div>
    <?php endif; ?>

    <!-- Add / Edit Item Form Card -->
    <div class="card" style="padding: 2rem; margin-bottom: 3rem;">
      <h3 style="font-size: 1.35rem; margin-bottom: 1.25rem;">
        <?= $editItem ? 'Edit Food Item: ' . htmlspecialchars($editItem['food_name']) : 'Add New Food Item to Menu' ?>
      </h3>

      <form action="items.php" method="POST">
        <input type="hidden" name="action" value="<?= $editItem ? 'update' : 'create' ?>">
        <?php if ($editItem): ?>
          <input type="hidden" name="food_id" value="<?= $editItem['food_id'] ?>">
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 1.25rem;">
          <div class="form-group">
            <label for="food_name" class="form-label">Food Name <span class="req">*</span></label>
            <input type="text" id="food_name" name="food_name" class="form-control" placeholder="e.g. Tandoori Paneer Roll" value="<?= htmlspecialchars($editItem['food_name'] ?? '') ?>" required>
          </div>

          <div class="form-group">
            <label for="category" class="form-label">Category <span class="req">*</span></label>
            <select id="category" name="category" class="form-control" required>
              <?php foreach (['Burgers', 'Pizza', 'Biryani', 'Pasta', 'Salads', 'Desserts', 'Snacks', 'Beverages'] as $c): ?>
                <option value="<?= $c ?>" <?= (($editItem['category'] ?? '') === $c) ? 'selected' : '' ?>><?= $c ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
          <div class="form-group">
            <label for="price" class="form-label">Price (INR) <span class="req">*</span></label>
            <input type="number" step="0.50" id="price" name="price" class="form-control" placeholder="189.00" value="<?= htmlspecialchars((string)($editItem['price'] ?? '')) ?>" required>
          </div>

          <div class="form-group">
            <label for="image_url" class="form-label">Image Asset Relative Path</label>
            <select id="image_url" name="image_url" class="form-control">
              <?php 
                $imgOptions = [
                  'assets/images/food-burger.svg'   => 'Burger Graphic',
                  'assets/images/food-pizza.svg'    => 'Pizza Graphic',
                  'assets/images/food-biryani.svg'  => 'Biryani Graphic',
                  'assets/images/food-pasta.svg'    => 'Pasta Graphic',
                  'assets/images/food-salad.svg'    => 'Salad Graphic',
                  'assets/images/food-dessert.svg'  => 'Dessert Graphic',
                  'assets/images/food-sandwich.svg' => 'Sandwich Graphic',
                  'assets/images/food-smoothie.svg' => 'Smoothie Beverage Graphic'
                ];
                $curImg = $editItem['image_url'] ?? 'assets/images/food-burger.svg';
                foreach ($imgOptions as $val => $lbl) {
                  $sel = ($curImg === $val) ? 'selected' : '';
                  echo "<option value=\"$val\" $sel>$lbl ($val)</option>";
                }
              ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="description" class="form-label">Description <span class="req">*</span></label>
          <textarea id="description" name="description" class="form-control" rows="2" placeholder="Describe the ingredients, taste, and serving size..." required><?= htmlspecialchars($editItem['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label class="checkbox-label">
            <input type="checkbox" name="availability" value="1" <?= (!isset($editItem['availability']) || $editItem['availability']) ? 'checked' : '' ?>>
            <span>Available for Instant Ordering (In Stock)</span>
          </label>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
          <button type="submit" class="btn btn-primary">
            <span><?= $editItem ? 'Save Item Changes' : 'Add Food Item to Menu' ?></span>
          </button>
          <?php if ($editItem): ?>
            <a href="items.php" class="btn btn-secondary">Cancel Edit</a>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <!-- Active Menu Items List Table -->
    <div class="card" style="padding: 1.75rem;">
      <h3 style="font-size: 1.25rem; margin-bottom: 1.25rem;">Active Menu Items (<?= count($allItems) ?>)</h3>
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Food Item</th>
              <th>Category</th>
              <th>Price</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($allItems as $item): ?>
              <tr>
                <td style="font-family: var(--font-mono); font-weight: 700;">#<?= $item['food_id'] ?></td>
                <td>
                  <div style="font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($item['food_name']) ?></div>
                  <div style="font-size: 0.8rem; color: var(--text-muted); max-width: 320px;"><?= htmlspecialchars($item['description']) ?></div>
                </td>
                <td><span class="badge badge-muted"><?= htmlspecialchars($item['category']) ?></span></td>
                <td style="font-family: var(--font-mono); font-weight: 700; color: var(--primary);">₹<?= number_format($item['price'], 2) ?></td>
                <td>
                  <span class="badge <?= $item['availability'] ? 'badge-success' : 'badge-danger' ?>">
                    <?= $item['availability'] ? 'In Stock' : 'Out of Stock' ?>
                  </span>
                </td>
                <td>
                  <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <a href="items.php?edit=<?= $item['food_id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                    <a href="items.php?toggle=<?= $item['food_id'] ?>" class="btn btn-secondary btn-sm" title="Toggle availability">
                      <?= $item['availability'] ? 'Mark Out' : 'Mark In' ?>
                    </a>
                    <a href="items.php?delete=<?= $item['food_id'] ?>" class="btn btn-outline btn-sm" style="color: var(--danger) !important; border-color: #FECACA;" onclick="return confirm('Are you sure you want to delete this menu item?');">
                      Delete
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
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
