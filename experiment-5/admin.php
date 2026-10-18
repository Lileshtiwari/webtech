<?php
session_start();
require_once __DIR__ . '/db.php';

// 1. Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['eventpulse_admin']);
    unset($_SESSION['eventpulse_user']);
    header('Location: admin.php');
    exit;
}

// 2. Handle Login POST
$loginError = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Credentials: admin / eventpulse@2026 (or pulseadmin / admin123)
    if (($username === 'admin' || $username === 'pulseadmin') && ($password === 'eventpulse@2026' || $password === 'admin123')) {
        $_SESSION['eventpulse_admin'] = true;
        $_SESSION['eventpulse_user'] = $username;
        header('Location: admin.php');
        exit;
    } else {
        $loginError = 'Invalid admin username or password. Please verify your credentials and try again.';
    }
}

$isAuthenticated = !empty($_SESSION['eventpulse_admin']);

$pdo = getEventDbConnection();
$search = trim($_GET['search'] ?? '');
$categoryFilter = trim($_GET['category'] ?? '');
$highlightId = filter_var($_GET['highlight'] ?? null, FILTER_VALIDATE_INT);
$flashSuccess = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);

$participantsList = [];
$dbError = null;

if ($isAuthenticated) {
    if (!$pdo) {
        $dbError = "Unable to connect to MySQL database 'event_management'. Verify that MySQL service is running on port 3306.";
    } else {
        try {
            $sql = "SELECT registration_id, participant_name, email, mobile, institution, event_name, category, participants, registration_date 
                    FROM event_registrations 
                    WHERE 1=1";
            $params = [];

            if (!empty($search)) {
                $sql .= " AND (participant_name LIKE :s OR email LIKE :s OR institution LIKE :s OR event_name LIKE :s)";
                $params[':s'] = "%$search%";
            }

            if (!empty($categoryFilter)) {
                $primaryWord = explode(' ', trim($categoryFilter))[0];
                $sql .= " AND (category = :cat_exact OR category LIKE :cat_prefix OR category LIKE :cat_wild)";
                $params[':cat_exact'] = $categoryFilter;
                $params[':cat_prefix'] = $primaryWord . '%';
                $params[':cat_wild'] = '%' . $primaryWord . '%';
            }

            $sql .= " ORDER BY registration_id DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $participantsList = $stmt->fetchAll();

            // Handle CSV Export
            if (isset($_GET['export']) && $_GET['export'] === 'csv') {
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=eventpulse_attendees_' . date('Y-m-d') . '.csv');
                $output = fopen('php://output', 'w');
                fputcsv($output, ['ID', 'Delegate Name', 'Email', 'Mobile', 'Organization', 'Event Track', 'Pass Tier', 'Participants', 'Registration Date']);
                foreach ($participantsList as $p) {
                    fputcsv($output, [
                        $p['registration_id'],
                        $p['participant_name'],
                        $p['email'],
                        $p['mobile'],
                        $p['institution'],
                        $p['event_name'],
                        $p['category'],
                        $p['participants'],
                        $p['registration_date']
                    ]);
                }
                fclose($output);
                exit;
            }

        } catch (PDOException $e) {
            error_log("Participants Query Error: " . $e->getMessage());
            $dbError = "Failed to retrieve registered records from database.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="EventPulse Summit Admin Portal - Manage attendees, download CSV records, and print attendee directories.">
  <title><?= $isAuthenticated ? 'Admin Dashboard' : 'Admin Sign-In' ?> | EventPulse Summit 2026</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    @media print {
      .event-header, .filter-card, .registry-top-btns, .event-footer, .btn-return-home, .filter-match-count {
        display: none !important;
      }
      body, .registry-main {
        background: #FFFFFF !important;
        color: #000000 !important;
        padding: 0 !important;
      }
      .registry-table-card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
      }
      .registry-table th, .registry-table td {
        color: #000 !important;
        border-bottom: 1px solid #ccc !important;
      }
    }
  </style>
</head>
<body>

  <!-- EventPulse Main Header -->
  <header class="event-header">
    <div class="event-container header-inner">
      <a href="index.php" class="event-brand">
        <img src="eventpulse-logo.svg" alt="EventPulse Logo" width="44" height="44">
        <div class="brand-titles">
          <span class="brand-name">Event<span class="brand-accent">Pulse</span></span>
          <span class="brand-sub">Admin Attendee Management</span>
        </div>
      </a>

      <nav class="event-nav">
        <ul class="nav-list" style="display: flex; align-items: center; gap: 1.25rem;">
          <li><a href="index.php" class="nav-link">&larr; Back to Registration Form</a></li>
          <?php if ($isAuthenticated): ?>
            <li class="admin-nav-user">
              <span class="user-status-pill">
                <span class="status-dot-green"></span>
                <span><?= htmlspecialchars($_SESSION['eventpulse_user'] ?? 'admin') ?></span>
              </span>
              <a href="admin.php?action=logout" class="btn-logout-pill" title="Sign out of Admin Dashboard">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                  <polyline points="16 17 21 12 16 7"></polyline>
                  <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>Logout</span>
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </header>

  <?php if (!$isAuthenticated): ?>
    <!-- ==========================================
         Admin Login Gateway
         ========================================== -->
    <main class="admin-auth-wrapper">
      <div class="admin-login-card">
        <div class="admin-login-head">
          <span class="admin-login-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            Secure Admin Area
          </span>
          <h1 class="admin-login-title">Admin Sign-In</h1>
          <p class="admin-login-desc">Sign in with administrative credentials to access summit registrations, export attendee CSVs, and filter applicants.</p>
        </div>

        <?php if (!empty($loginError)): ?>
          <div class="alert-error-banner" style="margin-bottom: 1.5rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span><?= htmlspecialchars($loginError) ?></span>
          </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="admin.php" method="POST" class="reg-form" novalidate>
          <input type="hidden" name="action" value="login">

          <div class="form-field" style="margin-bottom: 1.25rem;">
            <label for="admin_user">Admin Username <span class="req">*</span></label>
            <input type="text" id="admin_user" name="username" class="form-input" placeholder="Enter admin username" required autocomplete="username">
          </div>

          <div class="form-field" style="margin-bottom: 1.75rem;">
            <label for="admin_pass">Password <span class="req">*</span></label>
            <input type="password" id="admin_pass" name="password" class="form-input" placeholder="Enter password" required autocomplete="current-password">
          </div>

          <button type="submit" class="btn-submit-reg" style="width: 100%; justify-content: center;">
            <span>Unlock Dashboard &rarr;</span>
          </button>
        </form>

        <div style="margin-top: 1.75rem; text-align: center;">
          <a href="index.php" style="color: var(--text-muted); font-size: 0.85rem; text-decoration: none;">&larr; Return to Delegate Registration</a>
        </div>
      </div>
    </main>

  <?php else: ?>
    <!-- ==========================================
         Authenticated Admin Dashboard
         ========================================== -->
    <main class="registry-main">
      <div class="event-container">

        <!-- Header & Actions -->
        <div class="registry-header-row">
          <div>
            <span class="badge-sub">Admin Records</span>
            <h1 class="registry-title">Registered Applicants &amp; Attendees</h1>
            <p class="registry-sub">Direct administration view of all registered candidates with pass filtering, CSV export, and PDF print functionality.</p>
          </div>

          <div class="registry-top-btns">
            <!-- Download CSV Button -->
            <a href="admin.php?export=csv<?= !empty($categoryFilter) ? '&category=' . urlencode($categoryFilter) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="btn-register-action" style="background: #10B981;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 4px; vertical-align: middle;">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
              </svg>
              <span>Download CSV</span>
            </a>

            <!-- Print PDF Button -->
            <button type="button" class="btn-print-action" onclick="window.print()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px; vertical-align: middle;">
                <polyline points="6 9 6 2 18 2 18 9"/>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
              </svg>
              <span>Print in PDF</span>
            </button>
          </div>
        </div>

        <?php if ($dbError): ?>
          <div class="alert-error-banner">
            <span><?= htmlspecialchars($dbError) ?></span>
          </div>
        <?php endif; ?>

        <!-- Filter Bar -->
        <div class="filter-card">
          <form action="admin.php" method="GET" class="filter-form">
            <div class="filter-search-box">
              <input type="text" name="search" class="filter-input" placeholder="Search by name, email, institution, or event..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="filter-select-box">
              <select name="category" class="filter-select" onchange="this.form.submit()">
                <option value="">All Pass Tiers</option>
                <option value="Normal Pass" <?= (stripos($categoryFilter, 'Normal') !== false) ? 'selected' : '' ?>>Normal Pass ($99)</option>
                <option value="Gold Pass" <?= (stripos($categoryFilter, 'Gold') !== false) ? 'selected' : '' ?>>Gold Pass ($249)</option>
                <option value="Diamond Pass" <?= (stripos($categoryFilter, 'Diamond') !== false) ? 'selected' : '' ?>>Diamond Pass ($499)</option>
                <option value="Kohinoor Pass" <?= (stripos($categoryFilter, 'Kohinoor') !== false) ? 'selected' : '' ?>>Kohinoor Pass ($899)</option>
              </select>
            </div>
            <button type="submit" class="btn-filter-apply">Filter Applicants</button>
            <?php if (!empty($search) || !empty($categoryFilter)): ?>
              <a href="admin.php" class="btn-filter-reset">Reset Filters</a>
            <?php endif; ?>
          </form>

          <?php if (!empty($categoryFilter) || !empty($search)): ?>
            <div class="filter-match-count">
              <span>Showing <strong><?= count($participantsList) ?></strong> applicant<?= count($participantsList) === 1 ? '' : 's' ?></span>
              <?php if (!empty($categoryFilter)): ?>
                <span class="filter-active-chip">Pass: <?= htmlspecialchars($categoryFilter) ?></span>
              <?php endif; ?>
              <?php if (!empty($search)): ?>
                <span class="filter-active-chip">Keyword: "<?= htmlspecialchars($search) ?>"</span>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Table Container -->
        <div class="registry-table-card">
          <table class="registry-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Delegate Name</th>
                <th>Email &amp; Mobile</th>
                <th>Organization</th>
                <th>Event Track</th>
                <th>Pass Tier</th>
                <th>Count</th>
                <th>Registered At</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($participantsList)): ?>
                <tr>
                  <td colspan="8" class="empty-table-cell">
                    No applicants registered yet matching the selected filter criteria.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($participantsList as $row): ?>
                  <?php
                    $catLower = strtolower($row['category']);
                    $badgeClass = 'badge-cat-gold';
                    if (strpos($catLower, 'diamond') !== false) {
                        $badgeClass = 'badge-cat-diamond';
                    } elseif (strpos($catLower, 'kohinoor') !== false) {
                        $badgeClass = 'badge-cat-kohinoor';
                    } elseif (strpos($catLower, 'normal') !== false) {
                        $badgeClass = 'badge-cat-normal';
                    }
                  ?>
                  <tr class="<?= ($highlightId === (int)$row['registration_id']) ? 'highlighted-row' : '' ?>">
                    <td class="cell-id">#<?= htmlspecialchars($row['registration_id']) ?></td>
                    <td class="cell-name">
                      <strong><?= htmlspecialchars($row['participant_name']) ?></strong>
                    </td>
                    <td class="cell-contact">
                      <span class="contact-email"><?= htmlspecialchars($row['email']) ?></span>
                      <span class="contact-phone"><?= htmlspecialchars($row['mobile']) ?></span>
                    </td>
                    <td class="cell-inst"><?= htmlspecialchars($row['institution']) ?></td>
                    <td class="cell-track">
                      <span class="track-title"><?= htmlspecialchars($row['event_name']) ?></span>
                    </td>
                    <td>
                      <span class="badge-cat <?= $badgeClass ?>">
                        <?= htmlspecialchars($row['category']) ?>
                      </span>
                    </td>
                    <td class="cell-count">
                      <span class="count-pill"><?= htmlspecialchars($row['participants']) ?></span>
                    </td>
                    <td class="cell-date">
                      <?= date('M d, Y · H:i', strtotime($row['registration_date'])) ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </main>
  <?php endif; ?>

  <!-- Clean Unified Footer -->
  <footer class="event-footer" style="text-align: center; padding: 4.5rem 0 2rem;">
    <div class="event-container">
      <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 2.5rem;">
        <img src="eventpulse-logo.svg" alt="EventPulse Logo" width="56" height="56" style="margin-bottom: 1.25rem;">
        <span class="brand-name" style="color: #FFFFFF; font-size: 1.4rem; font-weight: 800; margin-bottom: 0.5rem;">Event<span style="color: #F43F5E;">Pulse</span></span>
        <p style="color: #94A3B8; font-size: 0.95rem; max-width: 540px; margin: 0 auto 1.75rem auto; line-height: 1.6;">
          Admin Attendee Management — Database records queried from MySQL <code>event_management.event_registrations</code>.
        </p>

        <!-- Back to Main Button -->
        <a href="../index.html" class="btn btn-secondary" style="background: rgba(255,255,255,0.08); color: #FFFFFF; border: 1.5px solid rgba(255,255,255,0.2); border-radius: 999px; padding: 0.65rem 1.75rem; font-size: 0.875rem; text-decoration: none;">
          <span>&larr; Return to Home Screen</span>
        </a>
      </div>

      <div style="border-top: 1px solid rgba(255, 255, 255, 0.08); padding-top: 1.75rem; display: flex; align-items: center; justify-content: space-between; font-size: 0.85rem; color: #64748B; flex-wrap: wrap; gap: 1rem;">
        <p>&copy; 2026 EventPulse Summit. All rights reserved.</p>
        <p>Designed By Lilesh Kumar Tiwari</p>
      </div>
    </div>
  </footer>

</body>
</html>
