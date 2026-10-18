<?php
session_start();
require_once __DIR__ . '/db.php';

$pdo = getEventDbConnection();
$totalRegistrations = 0;
$totalAttendees = 0;

if ($pdo) {
    try {
        $statStmt = $pdo->query('SELECT COUNT(*) as reg_count, COALESCE(SUM(participants), 0) as attendee_count FROM event_registrations');
        $stats = $statStmt->fetch();
        $totalRegistrations = (int)$stats['reg_count'];
        $totalAttendees = (int)$stats['attendee_count'];
    } catch (Exception $e) {
        // Fallback gracefully
    }
}

$formData = $_SESSION['form_data'] ?? [];
$formErrors = $_SESSION['form_errors'] ?? [];
$flashError = $_SESSION['flash_error'] ?? null;
$flashSuccess = $_SESSION['flash_success'] ?? null;

unset($_SESSION['form_errors']);
unset($_SESSION['flash_error']);
unset($_SESSION['flash_success']);

$successId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="EventPulse Global Technology Summit 2026 - Registration Portal & Pass Booking. Powered by PHP and MySQL.">
  <title>EventPulse Summit 2026 | Delegate Registration & Ticketing</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- EventPulse Main Header -->
  <header class="event-header">
    <div class="event-container header-inner">
      <a href="index.php" class="event-brand">
        <img src="eventpulse-logo.svg" alt="EventPulse Logo" width="44" height="44">
        <div class="brand-titles">
          <span class="brand-name">Event<span class="brand-accent">Pulse</span></span>
          <span class="brand-sub">Global Technology Summit 2026</span>
        </div>
      </a>

      <nav class="event-nav">
        <ul class="nav-list">
          <li><a href="#overview" class="nav-link">Overview</a></li>
          <li><a href="#tiers" class="nav-link">Pass Tiers</a></li>
          <li><a href="#registerSection" class="nav-link">Registration</a></li>
          <li><a href="admin.php" class="nav-cta-admin">Admin Dashboard &rarr;</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Main Summit Body -->
  <main class="event-main" id="overview">

    <!-- Hero Section -->
    <section class="summit-hero">
      <div class="event-container">
        <div class="hero-content">
          <div class="hero-eyebrow">
            <span class="event-date-badge">November 12&ndash;14, 2026 &middot; San Francisco Conference Center</span>
          </div>

          <h1 class="hero-title">
            Global Technology <span class="gradient-text">Summit 2026</span>
          </h1>

          <p class="hero-sub">
            Join engineers, architects, and industry innovators for 3 days of keynote sessions, technical workshops, and collaboration. Choose your pass tier below and reserve your seat today.
          </p>

          <div class="hero-stats-row">
            <div class="h-stat-box">
              <span class="h-stat-num"><?= $totalRegistrations ?></span>
              <span class="h-stat-lbl">Registered Delegations</span>
            </div>
            <div class="h-stat-box">
              <span class="h-stat-num"><?= $totalAttendees ?></span>
              <span class="h-stat-lbl">Confirmed Attendees</span>
            </div>
            <div class="h-stat-box">
              <span class="h-stat-num"><?= max(0, 500 - $totalAttendees) ?></span>
              <span class="h-stat-lbl">Seats Remaining</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pass Tiers Section -->
    <section class="tiers-section" id="tiers">
      <div class="event-container">
        <div class="section-center-head">
          <span class="badge-sub">Registration Options</span>
          <h2>Select Your Summit Pass Tier</h2>
          <p>Choose from our four comprehensive pass tiers. Click any tier to auto-select it in the registration form.</p>
        </div>

        <div class="tiers-grid">
          <!-- 1. Normal Pass -->
          <div class="tier-card" onclick="selectTier('Normal Pass', 99)">
            <span class="tier-level">General Access</span>
            <h3>Normal Pass</h3>
            <div class="tier-price">$99 <span>/ attendee</span></div>
            <p>Access to all general stage keynotes and plenary speaker sessions.</p>
            <ul class="tier-perks">
              <li>&check; Main Stage Keynotes</li>
              <li>&check; Digital Conference Badge</li>
              <li>&check; Standard Networking Receptions</li>
            </ul>
            <button type="button" class="btn-tier-select">Select Normal Pass &rarr;</button>
          </div>

          <!-- 2. Gold Pass -->
          <div class="tier-card featured-gold" onclick="selectTier('Gold Pass', 249)">
            <span class="tier-level">Professional</span>
            <h3>Gold Pass</h3>
            <div class="tier-price">$249 <span>/ attendee</span></div>
            <p>Full access to all technical breakout workshops and daily catered lunch.</p>
            <ul class="tier-perks">
              <li>&check; All Keynotes &amp; Workshops</li>
              <li>&check; Catered Networking Lunch</li>
              <li>&check; Complete Session Recordings</li>
            </ul>
            <button type="button" class="btn-tier-select gold-btn">Select Gold Pass &rarr;</button>
          </div>

          <!-- 3. Diamond Pass -->
          <div class="tier-card featured-diamond" onclick="selectTier('Diamond Pass', 499)">
            <span class="tier-level">Priority Access</span>
            <h3>Diamond Pass</h3>
            <div class="tier-price">$499 <span>/ attendee</span></div>
            <p>Priority auditorium seating, VIP speaker lounge access, and exclusive breakout sessions.</p>
            <ul class="tier-perks">
              <li>&check; Priority Front-Row Seating</li>
              <li>&check; VIP Speaker Lounge Access</li>
              <li>&check; Premium Gift Bag &amp; Swag</li>
            </ul>
            <button type="button" class="btn-tier-select diamond-btn">Select Diamond Pass &rarr;</button>
          </div>

          <!-- 4. Kohinoor Pass -->
          <div class="tier-card featured-kohinoor" onclick="selectTier('Kohinoor Pass', 899)">
            <div class="featured-ribbon">Ultimate Pass</div>
            <span class="tier-level">All-Inclusive Executive</span>
            <h3>Kohinoor Pass</h3>
            <div class="tier-price">$899 <span>/ attendee</span></div>
            <p>The crown tier — private executive dinner, 1-on-1 speaker advisory, and premier gala access.</p>
            <ul class="tier-perks">
              <li>&check; Private Executive Dinner</li>
              <li>&check; 1-on-1 Speaker Consultations</li>
              <li>&check; Lifetime Archive Access</li>
              <li>&check; 5-Star Concierge Service</li>
            </ul>
            <button type="button" class="btn-tier-select kohinoor-btn">Select Kohinoor Pass &rarr;</button>
          </div>
        </div>
      </div>
    </section>

    <!-- Registration Form Section -->
    <section class="register-section" id="registerSection">
      <div class="event-container">
        
        <?php if (!empty($flashSuccess)): ?>
          <div class="badge-success-container">
            <div class="badge-icon-wrap">
              <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
              </svg>
            </div>
            <div class="badge-content">
              <h3>Registration Completed Successfully!</h3>
              <p><?= htmlspecialchars($flashSuccess) ?></p>
              <div class="badge-actions">
                <a href="admin.php" class="btn-view-registry">View in Admin Directory &rarr;</a>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if (!empty($flashError)): ?>
          <div class="alert-error-banner">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span><?= htmlspecialchars($flashError) ?></span>
          </div>
        <?php endif; ?>

        <div class="register-card">
          <div class="card-intro">
            <h2>Summit Delegate Registration</h2>
            <p>Fill out the required information below to reserve your pass for the 2026 Summit.</p>
          </div>

          <form action="register.php" method="POST" class="reg-form" novalidate>
            
            <div class="form-row-2">
              <!-- Full Name -->
              <div class="form-field <?= isset($formErrors['participant_name']) ? 'has-error' : '' ?>">
                <label for="participant_name">Delegate Full Name <span class="req">*</span></label>
                <input type="text" id="participant_name" name="participant_name" class="form-input" placeholder="e.g. Rachel Adams" value="<?= htmlspecialchars($formData['participant_name'] ?? '') ?>" required>
                <?php if (isset($formErrors['participant_name'])): ?>
                  <span class="err-text"><?= htmlspecialchars($formErrors['participant_name']) ?></span>
                <?php endif; ?>
              </div>

              <!-- Email -->
              <div class="form-field <?= isset($formErrors['email']) ? 'has-error' : '' ?>">
                <label for="email">Email Address <span class="req">*</span></label>
                <input type="email" id="email" name="email" class="form-input" placeholder="rachel@example.com" value="<?= htmlspecialchars($formData['email'] ?? '') ?>" required>
                <?php if (isset($formErrors['email'])): ?>
                  <span class="err-text"><?= htmlspecialchars($formErrors['email']) ?></span>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-row-2">
              <!-- Mobile -->
              <div class="form-field <?= isset($formErrors['mobile']) ? 'has-error' : '' ?>">
                <label for="mobile">Mobile Number (10 Digits) <span class="req">*</span></label>
                <input type="tel" id="mobile" name="mobile" class="form-input" placeholder="9876543210" maxlength="10" value="<?= htmlspecialchars($formData['mobile'] ?? '') ?>" required>
                <?php if (isset($formErrors['mobile'])): ?>
                  <span class="err-text"><?= htmlspecialchars($formErrors['mobile']) ?></span>
                <?php endif; ?>
              </div>

              <!-- Organization / Institution -->
              <div class="form-field <?= isset($formErrors['institution']) ? 'has-error' : '' ?>">
                <label for="institution">Organization / Company <span class="req">*</span></label>
                <input type="text" id="institution" name="institution" class="form-input" placeholder="e.g. Tech Systems Inc." value="<?= htmlspecialchars($formData['institution'] ?? '') ?>" required>
                <?php if (isset($formErrors['institution'])): ?>
                  <span class="err-text"><?= htmlspecialchars($formErrors['institution']) ?></span>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-row-3">
              <!-- Event Track -->
              <div class="form-field <?= isset($formErrors['event_name']) ? 'has-error' : '' ?>">
                <label for="event_name">Select Event Track <span class="req">*</span></label>
                <select id="event_name" name="event_name" class="form-input" required>
                  <option value="" disabled <?= empty($formData['event_name']) ? 'selected' : '' ?>>Choose Track</option>
                  <option value="Keynote & Plenary Sessions" <?= ($formData['event_name'] ?? '') === 'Keynote & Plenary Sessions' ? 'selected' : '' ?>>Keynote &amp; Plenary Sessions</option>
                  <option value="Technical Architecture Workshops" <?= ($formData['event_name'] ?? '') === 'Technical Architecture Workshops' ? 'selected' : '' ?>>Technical Architecture Workshops</option>
                  <option value="FullStack Coding Sprint" <?= ($formData['event_name'] ?? '') === 'FullStack Coding Sprint' ? 'selected' : '' ?>>FullStack Coding Sprint</option>
                  <option value="Design Systems Masterclass" <?= ($formData['event_name'] ?? '') === 'Design Systems Masterclass' ? 'selected' : '' ?>>Design Systems Masterclass</option>
                </select>
                <?php if (isset($formErrors['event_name'])): ?>
                  <span class="err-text"><?= htmlspecialchars($formErrors['event_name']) ?></span>
                <?php endif; ?>
              </div>

              <!-- Pass Tier (Normal, Gold, Diamond, Kohinoor) -->
              <div class="form-field <?= isset($formErrors['category']) ? 'has-error' : '' ?>">
                <label for="category">Pass Tier <span class="req">*</span></label>
                <select id="category" name="category" class="form-input" required>
                  <option value="" disabled <?= empty($formData['category']) ? 'selected' : '' ?>>Select Pass</option>
                  <option value="Normal Pass" <?= ($formData['category'] ?? '') === 'Normal Pass' ? 'selected' : '' ?>>Normal Pass ($99)</option>
                  <option value="Gold Pass" <?= ($formData['category'] ?? '') === 'Gold Pass' ? 'selected' : '' ?>>Gold Pass ($249)</option>
                  <option value="Diamond Pass" <?= ($formData['category'] ?? '') === 'Diamond Pass' ? 'selected' : '' ?>>Diamond Pass ($499)</option>
                  <option value="Kohinoor Pass" <?= ($formData['category'] ?? '') === 'Kohinoor Pass' ? 'selected' : '' ?>>Kohinoor Pass ($899)</option>
                </select>
                <?php if (isset($formErrors['category'])): ?>
                  <span class="err-text"><?= htmlspecialchars($formErrors['category']) ?></span>
                <?php endif; ?>
              </div>

              <!-- Number of Participants -->
              <div class="form-field <?= isset($formErrors['participants']) ? 'has-error' : '' ?>">
                <label for="participants">Attendees Count <span class="req">*</span></label>
                <input type="number" id="participants" name="participants" class="form-input" min="1" max="20" value="<?= htmlspecialchars($formData['participants'] ?? '1') ?>" required>
                <?php if (isset($formErrors['participants'])): ?>
                  <span class="err-text"><?= htmlspecialchars($formErrors['participants']) ?></span>
                <?php endif; ?>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions-row">
              <button type="submit" class="btn-submit-reg">
                <span>Confirm &amp; Register Pass &rarr;</span>
              </button>
              <button type="reset" class="btn-reset-reg">
                <span>Clear Fields</span>
              </button>
            </div>

          </form>
        </div>

      </div>
    </section>

  </main>

  <!-- Clean Unified Footer -->
  <footer class="event-footer" style="text-align: center; padding: 4.5rem 0 2rem;">
    <div class="event-container">
      <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 2.5rem;">
        <img src="eventpulse-logo.svg" alt="EventPulse Logo" width="56" height="56" style="margin-bottom: 1.25rem;">
        <span class="brand-name" style="color: #FFFFFF; font-size: 1.4rem; font-weight: 800; margin-bottom: 0.5rem;">Event<span style="color: #F43F5E;">Pulse</span></span>
        <p style="color: #94A3B8; font-size: 0.95rem; max-width: 540px; margin: 0 auto 1.75rem auto; line-height: 1.6;">
          Global Technology Summit 2026 — Registration, pass booking, and attendee database management system powered by PHP and MySQL.
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

  <script>
    function selectTier(tierName) {
      document.getElementById('category').value = tierName;
      document.getElementById('registerSection').scrollIntoView({ behavior: 'smooth' });
    }
  </script>
</body>
</html>
