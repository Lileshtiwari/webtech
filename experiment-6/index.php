<?php
require_once __DIR__ . '/db.php';

$pdo = getFoodDbConnection();
$category = trim($_GET['cat'] ?? 'All');
$search = trim($_GET['search'] ?? '');
$sort = trim($_GET['sort'] ?? 'recommended');

$foodItems = [];
$dbError = null;

if ($pdo) {
    try {
        $sql = "SELECT * FROM food_items WHERE 1=1";
        $params = [];

        if (!empty($category) && $category !== 'All') {
            $sql .= " AND category = :cat";
            $params[':cat'] = $category;
        }

        if (!empty($search)) {
            $sql .= " AND (food_name LIKE :s OR description LIKE :s)";
            $params[':s'] = "%$search%";
        }

        // Sorting filters
        if ($sort === 'low_to_high') {
            $sql .= " ORDER BY price ASC";
        } elseif ($sort === 'high_to_low') {
            $sql .= " ORDER BY price DESC";
        } elseif ($sort === 'name') {
            $sql .= " ORDER BY food_name ASC";
        } else {
            // Recommended default
            $sql .= " ORDER BY availability DESC, food_id ASC";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $foodItems = $stmt->fetchAll();

    } catch (PDOException $e) {
        error_log("Food query error: " . $e->getMessage());
        $dbError = "Unable to load menu items from MySQL database.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Foco - Order your favourite gourmet foods with instant campus delivery.">
  <title>foco · Order your favourite Foods</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="foco-theme">

  <div class="foco-master-wrapper">
    <!-- Foco Navigation Bar (matching Reference Image 3) -->
    <header class="foco-navbar">
      <a href="index.php" class="foco-brand">
        <span>foco</span>
        <span class="foco-brand-dot"></span>
      </a>

      <nav aria-label="Foco Navigation">
        <ul class="foco-nav-links">
          <li><a href="index.php" class="foco-nav-link active">Home</a></li>
          <li><a href="#menuSection" class="foco-nav-link">Menu</a></li>
          <li><a href="#services" class="foco-nav-link">Service</a></li>
          <li><a href="cart.php" class="foco-nav-link">Shop</a></li>
        </ul>
      </nav>

      <div class="foco-user-actions">
        <!-- Search anchor -->
        <a href="#menuSection" class="foco-search-btn" aria-label="Search food catalog">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
        </a>

        <!-- Cart Button with Red Badge -->
        <a href="cart.php" class="foco-cart-btn" aria-label="View shopping cart">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <path d="M16 10a4 4 0 0 1-8 0"/>
          </svg>
          <span class="foco-cart-badge"><?= getCartCount() ?></span>
        </a>

        <!-- Auth buttons -->
        <?php if (isLoggedIn()): ?>
          <a href="order-history.php" class="btn-foco-signin">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
            <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
          </a>
          <?php if (isAdmin()): ?>
            <a href="admin/index.php" class="btn-foco-login" style="background: #F97316; color: #FFFFFF;">
              <span>Admin</span>
            </a>
          <?php endif; ?>
          <a href="logout.php" class="btn-foco-signin" style="padding: 0.55rem 0.85rem;">Logout</a>
        <?php else: ?>
          <div class="foco-auth-btns">
            <a href="register.php" class="btn-foco-signin">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="8.5" cy="7" r="4"/>
                <line x1="20" y1="8" x2="20" y2="14"/>
                <line x1="23" y1="11" x2="17" y2="11"/>
              </svg>
              <span>Sign Up</span>
            </a>
            <a href="login.php" class="btn-foco-login">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                <polyline points="10 17 15 12 10 7"/>
                <line x1="15" y1="12" x2="3" y2="12"/>
              </svg>
              <span>Login</span>
            </a>
          </div>
        <?php endif; ?>
      </div>
    </header>

    <!-- ===================================================================
         HERO SECTION (EXACTLY MATCHING REFERENCE IMAGE 3)
         =================================================================== -->
    <section class="foco-hero">
      <!-- Dark Curved Backdrop on Right -->
      <div class="foco-hero-dark-curve"></div>

      <!-- Left Hero Content -->
      <div class="foco-hero-left">
        <h1 class="foco-hero-title">
          Order your<br>
          favourite Foods
        </h1>
        <p class="foco-hero-sub">
          Fresh and tasty gourmet meals delivered fast. Curabitur accumsan auctor pulvinar proin sit amet, prepared with organic farm ingredients.
        </p>

        <!-- Total Order Price & Stepper Widget (matching Image 3) -->
        <div class="foco-price-counter-row">
          <div class="foco-total-order-label">
            <span>Total order :</span>
            <span class="price-val" id="featuredOrderPrice">₹199.00</span>
          </div>

          <div class="foco-order-actions-bar">
            <!-- Stepper Pill: [v] 1 [^] -->
            <div class="foco-qty-pill">
              <button type="button" class="foco-qty-btn" onclick="adjustHeroQty(-1)" aria-label="Decrease quantity">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>
              <span class="foco-qty-num" id="heroQtyDisplay">1</span>
              <button type="button" class="foco-qty-btn" onclick="adjustHeroQty(1)" aria-label="Increase quantity">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                  <polyline points="18 15 12 9 6 15"></polyline>
                </svg>
              </button>
            </div>

            <!-- Buy Now Button with Shopping Bag icon (matching Image 3) -->
            <button type="button" class="btn-foco-buy" onclick="addFeaturedToCart()">
              <div class="btn-foco-buy-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                  <line x1="3" y1="6" x2="21" y2="6"/>
                  <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
              </div>
              <span>Buy Now</span>
            </button>
          </div>
        </div>

        <!-- Mini Preview Carousel Pills (matching Image 3: Burger, Cake, Salad) -->
        <div class="foco-carousel-strip">
          <button type="button" class="foco-carousel-nav-btn" onclick="slideCarousel(-1)" aria-label="Previous preview">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
          </button>

          <!-- Item 1: Burger -->
          <div class="foco-carousel-card" onclick="selectFeatured(1, 'Artisan Tikka Burger', 189.00)">
            <div class="foco-carousel-img-circle" style="background: #FEF3C7;">
              <img src="../assets/images/food-burger.svg" alt="Burger" width="40" height="40">
            </div>
            <div class="foco-carousel-name">Burger</div>
            <div class="foco-carousel-price">₹189</div>
          </div>

          <!-- Item 2: Dessert / Cake -->
          <div class="foco-carousel-card" onclick="selectFeatured(6, 'Dark Chocolate Lava Cake', 149.00)">
            <div class="foco-carousel-img-circle" style="background: #FDF4FF;">
              <img src="../assets/images/food-dessert.svg" alt="Cake" width="40" height="40">
            </div>
            <div class="foco-carousel-name">Cake</div>
            <div class="foco-carousel-price">₹149</div>
          </div>

          <!-- Item 3: Salad (Active in Image 3) -->
          <div class="foco-carousel-card active" onclick="selectFeatured(5, 'Mediterranean Quinoa Salad', 199.00)">
            <div class="foco-carousel-img-circle" style="background: #ECFDF5;">
              <img src="../assets/images/food-salad.svg" alt="Salad" width="40" height="40">
            </div>
            <div class="foco-carousel-name">Salad</div>
            <div class="foco-carousel-price">₹199</div>
          </div>

          <button type="button" class="foco-carousel-nav-btn" onclick="slideCarousel(1)" aria-label="Next preview">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </button>
        </div>
      </div>

      <!-- Right Hero Visual: Gourmet Bowl & Floating Elements (matching Image 3) -->
      <div class="foco-hero-right">
        <div class="foco-bowl-wrapper">
          <!-- Giant Delicious Bowl SVG -->
          <img src="images/hero-bowl.svg" alt="Gourmet Bowl Presentation" class="foco-bowl-img">

          <!-- Floating Garnishes (matching Image 3) -->
          <!-- Parsley -->
          <div class="foco-floating-garnish garnish-parsley">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="#22C55E">
              <path d="M12 2C8 6 4 10 4 15C4 18 7 21 11 21C11.5 21 12 20.8 12.5 20.5C13 20.8 13.5 21 14 21C18 21 21 18 21 15C21 10 17 6 13 2H12Z"/>
            </svg>
          </div>
          <!-- Red Chili -->
          <div class="foco-floating-garnish garnish-chili">
            <svg width="60" height="30" viewBox="0 0 60 30" fill="none">
              <path d="M5 25C15 25 35 15 50 5C45 15 30 28 10 28C6 28 4 27 5 25Z" fill="#DC2626"/>
              <path d="M50 5C53 2 56 3 58 4" stroke="#16A34A" stroke-width="3" stroke-linecap="round"/>
            </svg>
          </div>
          <!-- Tomato slice -->
          <div class="foco-floating-garnish garnish-tomato">
            <svg width="34" height="34" viewBox="0 0 34 34" fill="none">
              <circle cx="17" cy="17" r="16" fill="#EF4444"/>
              <circle cx="17" cy="17" r="12" fill="#F87171"/>
              <circle cx="14" cy="14" r="2.5" fill="#FEF08A"/>
              <circle cx="20" cy="14" r="2.5" fill="#FEF08A"/>
            </svg>
          </div>
          <!-- Olive -->
          <div class="foco-floating-garnish garnish-olive">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
              <circle cx="11" cy="11" r="10" fill="#15803D" stroke="#14532D" stroke-width="2"/>
              <circle cx="11" cy="11" r="5" fill="#FFFFFF"/>
            </svg>
          </div>

          <!-- Floating Orange Rating & Time Badge (matching Image 3) -->
          <div class="foco-floating-badge">
            <div class="foco-badge-title-row">
              <span class="foco-badge-food-title" id="floatingBadgeName">Salad</span>
              <span class="foco-badge-rating">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#FFFFFF">
                  <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
                <span>4.7</span>
              </span>
            </div>
            <div class="foco-badge-time-pill">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
              </svg>
              <span>10-18 mins</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================================================================
         MENU CATALOG WITH PROPER RECOMMENDATIONS & PRICE FILTERS (LOW TO HIGH)
         =================================================================== -->
    <section id="menuSection" class="foco-menu-section">
      <div class="foco-section-heading-bar">
        <div>
          <span style="font-size: 0.8rem; font-weight: 800; color: var(--foco-orange); text-transform: uppercase; letter-spacing: 0.08em; display: block; margin-bottom: 0.25rem;">
            Chef's Culinary Selections
          </span>
          <h2 class="foco-section-title">Explore Our Full Menu</h2>
        </div>

        <!-- Filter Tools: Search and Sort Low-to-High / High-to-Low -->
        <div class="foco-filter-group">
          <!-- Search Form -->
          <form action="index.php" method="GET" style="display: flex; gap: 0.5rem;">
            <input type="text" name="search" placeholder="Search menu..." value="<?= htmlspecialchars($search) ?>" 
                   style="border: 1px solid var(--foco-border); border-radius: 9999px; padding: 0.55rem 1.15rem; font-size: 0.875rem; width: 180px;">
            <?php if ($category !== 'All'): ?>
              <input type="hidden" name="cat" value="<?= htmlspecialchars($category) ?>">
            <?php endif; ?>
            <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
            <button type="submit" class="btn-foco-signin" style="border: none; cursor: pointer;">Search</button>
          </form>

          <!-- Sorting Dropdown (Low to High, High to Low, Recommendations) -->
          <form action="index.php" method="GET" id="sortForm">
            <?php if ($category !== 'All'): ?>
              <input type="hidden" name="cat" value="<?= htmlspecialchars($category) ?>">
            <?php endif; ?>
            <?php if (!empty($search)): ?>
              <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
            <?php endif; ?>
            <select name="sort" onchange="document.getElementById('sortForm').submit()" 
                    style="border: 1px solid var(--foco-border); border-radius: 9999px; padding: 0.55rem 1.15rem; font-size: 0.875rem; font-weight: 700; color: #11142D; background: #FFFFFF; cursor: pointer;">
              <option value="recommended" <?= ($sort === 'recommended') ? 'selected' : '' ?>>Chef's Recommended</option>
              <option value="low_to_high" <?= ($sort === 'low_to_high') ? 'selected' : '' ?>>Price: Low to High</option>
              <option value="high_to_low" <?= ($sort === 'high_to_low') ? 'selected' : '' ?>>Price: High to Low</option>
              <option value="name" <?= ($sort === 'name') ? 'selected' : '' ?>>Alphabetical (A–Z)</option>
            </select>
          </form>
        </div>
      </div>

      <!-- Category Filter Tabs -->
      <div class="foco-cat-tabs">
        <?php
          $cats = ['All', 'Burgers', 'Pizza', 'Biryani', 'Pasta', 'Salads', 'Desserts', 'Snacks', 'Beverages'];
          foreach ($cats as $ct):
            $isActive = ($category === $ct);
            $queryUrl = 'index.php?' . http_build_query(['cat' => $ct, 'sort' => $sort, 'search' => $search]);
        ?>
          <a href="<?= $queryUrl ?>#menuSection" class="foco-cat-tab <?= $isActive ? 'active' : '' ?>">
            <?= htmlspecialchars($ct) ?>
          </a>
        <?php endforeach; ?>
      </div>

      <!-- Food Menu Grid -->
      <?php if (empty($foodItems)): ?>
        <div style="text-align: center; padding: 4rem 1rem; color: var(--foco-text-muted);">
          <h3>No delicacies match your filter criteria.</h3>
          <p style="font-size: 0.9rem;">Try selecting a different category or clearing the search query.</p>
          <a href="index.php#menuSection" class="btn-foco-login" style="margin-top: 1rem;">View All Items</a>
        </div>
      <?php else: ?>
        <div class="foco-food-grid">
          <?php foreach ($foodItems as $food): ?>
            <article class="foco-food-card">
              <?php if ($food['price'] >= 249.00): ?>
                <span class="foco-food-badge-recom">Top Rated</span>
              <?php endif; ?>

              <div class="foco-food-thumb-wrap">
                <img src="../<?= htmlspecialchars($food['image_url'] ?: 'assets/images/food-burger.svg') ?>" 
                     alt="<?= htmlspecialchars($food['food_name']) ?>" 
                     class="foco-food-thumb" loading="lazy">
              </div>

              <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.25rem;">
                <span style="font-size: 0.725rem; font-weight: 800; color: var(--foco-orange); text-transform: uppercase;">
                  <?= htmlspecialchars($food['category']) ?>
                </span>
                <span style="font-size: 0.75rem; color: #16A34A; font-weight: 700;">★ 4.8</span>
              </div>

              <h3 class="foco-card-name"><?= htmlspecialchars($food['food_name']) ?></h3>
              <p class="foco-card-desc"><?= htmlspecialchars($food['description']) ?></p>

              <div class="foco-card-footer">
                <div class="foco-card-price">₹<?= number_format($food['price'], 2) ?></div>
                <?php if ($food['availability']): ?>
                  <a href="cart.php?action=add&id=<?= $food['food_id'] ?>" 
                     class="btn-foco-add" 
                     onclick="addToCartAjax(<?= $food['food_id'] ?>, 1, '<?= htmlspecialchars(addslashes($food['food_name'])) ?>', event)"
                     aria-label="Add <?= htmlspecialchars($food['food_name']) ?> to Cart">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                      <line x1="12" y1="5" x2="12" y2="19"></line>
                      <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                  </a>
                <?php else: ?>
                  <span style="font-size: 0.75rem; color: #DC2626; font-weight: 700;">Sold Out</span>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <!-- Services Banner Strip -->
    <section id="services" style="background: #FAF7F2; padding: 3rem 3.5rem; border-top: 1px solid #ECEEF2; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 2rem;">
      <div>
        <div style="width: 44px; height: 44px; border-radius: 50%; background: #FF7A00; color: #FFFFFF; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
          </svg>
        </div>
        <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.35rem;">Express Campus Delivery</h4>
        <p style="font-size: 0.85rem; color: var(--foco-text-muted); line-height: 1.5;">Hot gourmet meals delivered straight to your hostel or lab room in 10-18 minutes.</p>
      </div>

      <div>
        <div style="width: 44px; height: 44px; border-radius: 50%; background: #11142D; color: #FFFFFF; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
          </svg>
        </div>
        <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.35rem;">ACID Safe Transactions</h4>
        <p style="font-size: 0.85rem; color: var(--foco-text-muted); line-height: 1.5;">Every order is committed atomically to MySQL. Prices are locked and verified by the server.</p>
      </div>

      <div>
        <div style="width: 44px; height: 44px; border-radius: 50%; background: #16A34A; color: #FFFFFF; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
        </div>
        <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.35rem;">100% Quality Guaranteed</h4>
        <p style="font-size: 0.85rem; color: var(--foco-text-muted); line-height: 1.5;">Fresh farm ingredients prepared by certified campus chefs. Zero compromise on flavor.</p>
      </div>
    </section>

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
  </div>

  <!-- Floating Toast Notification -->
  <div id="focoToast" class="foco-toast">
    <div class="foco-toast-badge">✓</div>
    <div id="focoToastMsg">Added item to your bag!</div>
  </div>

  <script>
    let currentHeroFoodId = 5;
    let currentHeroUnitPrice = 199.00;
    let currentHeroQty = 1;

    function adjustHeroQty(delta) {
      currentHeroQty = Math.max(1, Math.min(20, currentHeroQty + delta));
      document.getElementById('heroQtyDisplay').textContent = currentHeroQty;
      const total = (currentHeroUnitPrice * currentHeroQty).toFixed(2);
      document.getElementById('featuredOrderPrice').textContent = '₹' + total;
    }

    function selectFeatured(foodId, name, price) {
      currentHeroFoodId = foodId;
      currentHeroUnitPrice = price;
      document.getElementById('floatingBadgeName').textContent = name.split(' ').slice(-1)[0];
      adjustHeroQty(0);

      // Toggle active card
      document.querySelectorAll('.foco-carousel-card').forEach(card => card.classList.remove('active'));
      if (window.event && window.event.currentTarget) {
        window.event.currentTarget.classList.add('active');
      }
    }

    function addFeaturedToCart() {
      const name = document.getElementById('floatingBadgeName').textContent || 'Featured Delicacy';
      addToCartAjax(currentHeroFoodId, currentHeroQty, name, window.event);
    }

    function addToCartAjax(foodId, qty, foodName, event) {
      if (event && event.preventDefault) event.preventDefault();

      fetch('cart.php?action=add_ajax&id=' + foodId + '&qty=' + qty)
        .then(res => res.json())
        .then(data => {
          if (data && data.success) {
            const badges = document.querySelectorAll('.foco-cart-badge, .cart-pill-badge');
            badges.forEach(b => b.textContent = data.count);
            showToast('Added ' + (qty > 1 ? qty + 'x ' : '') + '<strong>' + foodName + '</strong> to your bag! <a href="cart.php" style="color: #FF7A00; text-decoration: underline; margin-left: 6px; font-weight: 700;">View Cart &rarr;</a>');
          } else {
            window.location.href = 'cart.php?action=add&id=' + foodId + '&qty=' + qty;
          }
        })
        .catch(() => {
          window.location.href = 'cart.php?action=add&id=' + foodId + '&qty=' + qty;
        });
    }

    let toastTimer = null;
    function showToast(html) {
      const toast = document.getElementById('focoToast');
      const msg = document.getElementById('focoToastMsg');
      if (!toast || !msg) return;
      msg.innerHTML = html;
      toast.classList.add('show');
      clearTimeout(toastTimer);
      toastTimer = setTimeout(() => {
        toast.classList.remove('show');
      }, 3500);
    }

    function slideCarousel(dir) {
      const cards = document.querySelectorAll('.foco-carousel-card');
      let activeIdx = 0;
      cards.forEach((c, idx) => {
        if (c.classList.contains('active')) activeIdx = idx;
      });
      let nextIdx = (activeIdx + dir + cards.length) % cards.length;
      cards[nextIdx].click();
    }
  </script>
</body>
</html>
