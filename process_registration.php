<?php
require_once 'includes/config.php';

// Only accept POST submissions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: events.php');
    exit;
}

$event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;

$student_name       = trim($_POST['student_name'] ?? '');
$admission_number   = trim($_POST['admission_number'] ?? '');
$email              = trim($_POST['email'] ?? '');
$phone              = trim($_POST['phone'] ?? '');
$course             = trim($_POST['course'] ?? '');
$special_requirements = trim($_POST['special_requirements'] ?? '');

// Keep the submitted values so the form can be re-filled if something fails
$_SESSION['old_input'] = [
    'student_name'     => $student_name,
    'admission_number' => $admission_number,
    'email'            => $email,
    'phone'            => $phone,
    'course'           => $course,
    'special_requirements' => $special_requirements,
];

$errors = [];

if ($event_id <= 0) {
    $errors[] = 'No event was specified.';
}
if ($student_name === '' || mb_strlen($student_name) < 3) {
    $errors[] = 'Please enter your full name (at least 3 characters).';
}
if ($admission_number === '') {
    $errors[] = 'Please enter your admission number.';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}
if ($phone === '' || !preg_match('/^(0|\+254)[0-9]{9}$/', $phone)) {
    $errors[] = 'Please enter a valid Kenyan phone number, e.g. 0712345678.';
}
if ($course === '') {
    $errors[] = 'Please select your course.';
}
if (mb_strlen($special_requirements) > 300) {
    $errors[] = 'Special requirements must be under 300 characters.';
}

// Confirm the event actually exists and still has seats
if ($event_id > 0 && empty($errors)) {
    $stmt = mysqli_prepare($conn, "SELECT capacity,
            (SELECT COUNT(*) FROM registrations WHERE event_id = ?) AS taken
            FROM events WHERE event_id = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $event_id, $event_id);
    mysqli_stmt_execute($stmt);
    $event_row = mysqli_stmt_get_result($stmt)->fetch_assoc();

    if (!$event_row) {
        $errors[] = 'That event no longer exists.';
    } elseif ((int)$event_row['taken'] >= (int)$event_row['capacity']) {
        $errors[] = 'Sorry, this event is now fully booked.';
    }
}

// Prevent the same admission number registering twice for the same event
if (empty($errors)) {
    $stmt = mysqli_prepare($conn, "SELECT registration_id FROM registrations WHERE event_id = ? AND admission_number = ?");
    mysqli_stmt_bind_param($stmt, 'is', $event_id, $admission_number);
    mysqli_stmt_execute($stmt);
    if (mysqli_stmt_get_result($stmt)->fetch_assoc()) {
        $errors[] = 'This admission number is already registered for this event.';
    }
}

if (!empty($errors)) {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => implode(' ', $errors),
    ];
    header('Location: register.php?event_id=' . $event_id);
    exit;
}

// All good — insert the record
$stmt = mysqli_prepare($conn, "INSERT INTO registrations
    (student_name, admission_number, email, phone, course, event_id, special_requirements)
    VALUES (?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param(
    $stmt,
    'sssssis',
    $student_name,
    $admission_number,
    $email,
    $phone,
    $course,
    $event_id,
    $special_requirements
);

if (mysqli_stmt_execute($stmt)) {
    unset($_SESSION['old_input']);
    $_SESSION['flash'] = [
        'type'    => 'success',
        'message' => 'You are registered! A confirmation has been recorded — see you at the event.',
    ];
} else {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Something went wrong while saving your registration. Please try again.',
    ];
}

header('Location: register.php?event_id=' . $event_id);
exit;
