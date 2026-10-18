<?php
/**
 * EventPulse Summit - Registration Controller
 * Server-Side Validation, Prepared Statements & Database Storage
 */

session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$errors = [];
$name        = trim($_POST['participant_name'] ?? '');
$email       = trim($_POST['email'] ?? '');
$mobile      = trim($_POST['mobile'] ?? '');
$institution = trim($_POST['institution'] ?? '');
$event_name  = trim($_POST['event_name'] ?? '');
$category    = trim($_POST['category'] ?? '');
$participants = filter_var($_POST['participants'] ?? 1, FILTER_VALIDATE_INT);

// 1. Server-side validations
if (empty($name) || strlen($name) < 2) {
    $errors['participant_name'] = 'Participant name is required (minimum 2 characters).';
} elseif (!preg_match('/^[a-zA-Z\s\.]+$/', $name)) {
    $errors['participant_name'] = 'Participant name must contain only letters, dots, and spaces.';
}

if (empty($email)) {
    $errors['email'] = 'Email address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email format (e.g., name@domain.com).';
}

if (empty($mobile)) {
    $errors['mobile'] = 'Mobile number is required.';
} elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {
    $errors['mobile'] = 'Mobile number must be a valid 10-digit number.';
}

if (empty($institution) || strlen($institution) < 2) {
    $errors['institution'] = 'Organization or institution name is required.';
}

if (empty($event_name)) {
    $errors['event_name'] = 'Please select an event track.';
}

if (empty($category)) {
    $errors['category'] = 'Please select a pass tier (Normal, Gold, Diamond, or Kohinoor).';
}

if ($participants === false || $participants < 1 || $participants > 20) {
    $errors['participants'] = 'Number of participants must be between 1 and 20.';
}

// Retain submitted values for repopulation if errors occur
$_SESSION['form_data'] = [
    'participant_name' => $name,
    'email'            => $email,
    'mobile'           => $mobile,
    'institution'      => $institution,
    'event_name'       => $event_name,
    'category'         => $category,
    'participants'     => $participants ?: 1
];

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    header('Location: index.php#registerSection');
    exit;
}

$pdo = getEventDbConnection();
if (!$pdo) {
    $_SESSION['flash_error'] = 'Database connection error. Please verify MySQL service is running on port 3306.';
    header('Location: index.php#registerSection');
    exit;
}

try {
    // 2. Prevent duplicate registrations for same email and event
    $dupCheck = $pdo->prepare('SELECT registration_id FROM event_registrations WHERE email = :email AND event_name = :event_name LIMIT 1');
    $dupCheck->execute([
        ':email'      => $email,
        ':event_name' => $event_name
    ]);

    if ($dupCheck->fetch()) {
        $_SESSION['flash_error'] = 'This email address is already registered for the selected event track.';
        header('Location: index.php#registerSection');
        exit;
    }

    // 3. Insert record using PDO Prepared Statement
    $insertStmt = $pdo->prepare('
        INSERT INTO event_registrations 
        (participant_name, email, mobile, institution, event_name, category, participants, registration_date)
        VALUES 
        (:participant_name, :email, :mobile, :institution, :event_name, :category, :participants, NOW())
    ');

    $insertStmt->execute([
        ':participant_name' => $name,
        ':email'            => $email,
        ':mobile'           => $mobile,
        ':institution'      => $institution,
        ':event_name'       => $event_name,
        ':category'         => $category,
        ':participants'     => $participants
    ]);

    $newId = $pdo->lastInsertId();

    // Clear saved form data on success
    unset($_SESSION['form_data']);
    unset($_SESSION['form_errors']);

    $_SESSION['flash_success'] = 'Registration completed successfully! Your pass has been issued (Registration ID #' . $newId . ').';
    header('Location: index.php?success=1&id=' . $newId . '#registerSection');
    exit;

} catch (PDOException $e) {
    error_log('Registration Error: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'A database error occurred while recording your registration. Please try again.';
    header('Location: index.php#registerSection');
    exit;
}
