<?php
require_once 'includes/config.php';
$page_title = 'Register';
$active_page = 'events';

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

$stmt = mysqli_prepare($conn, "SELECT * FROM events WHERE event_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $event_id);
mysqli_stmt_execute($stmt);
$event = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$event) {
    $page_title = 'Event not found';
    require_once 'includes/header.php';
    echo '<section class="section-form"><div class="container"><div class="alert alert-mmtc-error">That event could not be found. <a href="events.php">Return to the events list</a>.</div></div></section>';
    require_once 'includes/footer.php';
    exit;
}

// Flash message set by process_registration.php after a submit attempt
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Keep whatever the student typed if the previous submission failed validation
$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);

require_once 'includes/header.php';
?>

<section class="section-form">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">

        <a href="events.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to events</a>

        <div class="form-event-summary">
          <span class="event-card-category"><?php echo htmlspecialchars($event['category']); ?></span>
          <h1 class="form-event-title"><?php echo htmlspecialchars($event['event_name']); ?></h1>
          <ul class="event-card-meta">
            <li><i class="fa-regular fa-calendar"></i> <?php echo date('l, d F Y', strtotime($event['event_date'])); ?></li>
            <li><i class="fa-regular fa-clock"></i> <?php echo date('g:i A', strtotime($event['event_time'])); ?></li>
            <li><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($event['venue']); ?></li>
          </ul>
        </div>

        <?php if ($flash): ?>
          <div class="alert alert-mmtc-<?php echo $flash['type']; ?>" role="alert">
            <i class="fa-solid <?php echo $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
            <?php echo htmlspecialchars($flash['message']); ?>
          </div>
        <?php endif; ?>

        <?php if (!($flash && $flash['type'] === 'success')): ?>
        <form action="process_registration.php" method="POST" id="registrationForm" class="needs-validation" novalidate>
          <input type="hidden" name="event_id" value="<?php echo (int)$event['event_id']; ?>">
          <input type="hidden" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>">

          <div class="row g-3">
            <div class="col-md-6">
              <label for="student_name" class="form-label">Full name</label>
              <input type="text" class="form-control" id="student_name" name="student_name" required minlength="3"
                     value="<?php echo htmlspecialchars($old['student_name'] ?? ''); ?>">
              <div class="invalid-feedback">Enter your full name (at least 3 characters).</div>
            </div>

            <div class="col-md-6">
              <label for="admission_number" class="form-label">Admission number</label>
              <input type="text" class="form-control" id="admission_number" name="admission_number" required
                     placeholder="e.g. MMTC/2024/0113"
                     value="<?php echo htmlspecialchars($old['admission_number'] ?? ''); ?>">
              <div class="invalid-feedback">Enter your MMTC admission number.</div>
            </div>

            <div class="col-md-6">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="form-control" id="email" name="email" required
                     value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
              <div class="invalid-feedback">Enter a valid email address.</div>
            </div>

            <div class="col-md-6">
              <label for="phone" class="form-label">Phone number</label>
              <input type="tel" class="form-control" id="phone" name="phone" required
                     placeholder="e.g. 0712345678" pattern="^(0|\+254)[0-9]{9}$"
                     value="<?php echo htmlspecialchars($old['phone'] ?? ''); ?>">
              <div class="invalid-feedback">Enter a valid Kenyan phone number (e.g. 0712345678).</div>
            </div>

            <div class="col-md-12">
              <label for="course" class="form-label">Course</label>
              <select class="form-select" id="course" name="course" required>
                <option value="" disabled <?php echo empty($old['course']) ? 'selected' : ''; ?>>Select your course&hellip;</option>
                <?php
                $courses = [
                  'Diploma in Nursing',
                  'Diploma in Clinical Medicine',
                  'Diploma in Pharmaceutical Technology',
                  'Certificate in Community Health',
                  'Diploma in Medical Laboratory Sciences',
                  'Diploma in Public Health',
                ];
                foreach ($courses as $c) {
                  $sel = (($old['course'] ?? '') === $c) ? 'selected' : '';
                  echo '<option value="' . htmlspecialchars($c) . '" ' . $sel . '>' . htmlspecialchars($c) . '</option>';
                }
                ?>
              </select>
              <div class="invalid-feedback">Select the course you are enrolled in.</div>
            </div>

            <div class="col-md-12">
              <label for="special_requirements" class="form-label">
                Special requirements <span class="form-label-optional">(optional)</span>
              </label>
              <textarea class="form-control" id="special_requirements" name="special_requirements" rows="3" maxlength="300"
                        placeholder="Accessibility needs, dietary requirements, etc."><?php echo htmlspecialchars($old['special_requirements'] ?? ''); ?></textarea>
              <div class="char-counter"><span id="charCount">0</span>/300</div>
            </div>
          </div>

          <button type="submit" class="btn btn-mmtc-primary btn-lg w-100 mt-4">Submit registration</button>
        </form>
        <?php endif; ?>

      </div>
    </div>
  </div>
</section>

<!-- Confirmation modal shown by JS before the form is actually submitted -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Confirm registration</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Register <strong id="confirmStudentName"></strong> for
        <strong id="confirmEventName"><?php echo htmlspecialchars($event['event_name']); ?></strong>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-mmtc-outline" data-bs-dismiss="modal">Go back</button>
        <button type="button" class="btn btn-mmtc-primary" id="confirmSubmitBtn">Yes, register me</button>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
