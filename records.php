<?php
require_once 'includes/config.php';
$page_title = 'Registration Records';
$active_page = 'records';

$search = trim($_GET['q'] ?? '');

$base_query = "SELECT r.registration_id, r.student_name, r.admission_number, r.email, r.phone,
                      r.course, r.registration_date, e.event_name
               FROM registrations r
               JOIN events e ON e.event_id = r.event_id";

if ($search !== '') {
    $like = '%' . $search . '%';
    $stmt = mysqli_prepare($conn, $base_query . "
        WHERE r.student_name LIKE ? OR r.admission_number LIKE ? OR r.email LIKE ?
           OR r.course LIKE ? OR e.event_name LIKE ?
        ORDER BY r.registration_date DESC");
    mysqli_stmt_bind_param($stmt, 'sssss', $like, $like, $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, $base_query . " ORDER BY r.registration_date DESC");
}

$records = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $records[] = $row;
    }
}

$total_count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM registrations");
$total_count = $total_count_result ? mysqli_fetch_assoc($total_count_result)['total'] : 0;

require_once 'includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <p class="hero-eyebrow">Student Affairs Office</p>
    <h1 class="page-header-title">Registration records</h1>
    <p class="page-header-lead">Every registration submitted through the system, pulled live from MySQL.</p>

    <form class="events-search" method="GET" action="records.php">
      <label for="recordsSearch" class="visually-hidden">Search registration records</label>
      <i class="fa-solid fa-magnifying-glass events-search-icon"></i>
      <input type="text" id="recordsSearch" name="q" class="form-control"
             placeholder="Search by name, admission no., email, course or event…"
             value="<?php echo htmlspecialchars($search); ?>" autocomplete="off">
    </form>
  </div>
</section>

<section class="section-records">
  <div class="container">

    <div class="records-summary">
      <div class="records-summary-count">
        <span class="records-summary-number"><?php echo (int)$total_count; ?></span>
        <span>students registered in total</span>
      </div>
      <div class="records-summary-filtered" id="visibleCountLabel">
        <?php if ($search !== ''): ?>
          Showing <?php echo count($records); ?> result<?php echo count($records) === 1 ? '' : 's'; ?> for
          &ldquo;<?php echo htmlspecialchars($search); ?>&rdquo;
          &mdash; <a href="records.php">clear search</a>
        <?php else: ?>
          Showing all <?php echo count($records); ?> record<?php echo count($records) === 1 ? '' : 's'; ?>
        <?php endif; ?>
      </div>
    </div>

    <?php if (count($records) === 0): ?>
      <div class="alert alert-mmtc-empty">
        <?php echo $search !== '' ? 'No registrations match your search.' : 'No students have registered yet.'; ?>
      </div>
    <?php else: ?>
      <div class="table-responsive records-table-wrap">
        <table class="table table-hover align-middle mmtc-table" id="recordsTable">
          <thead>
            <tr>
              <th scope="col">Reg. ID</th>
              <th scope="col">Student name</th>
              <th scope="col">Admission no.</th>
              <th scope="col">Email</th>
              <th scope="col">Phone</th>
              <th scope="col">Course</th>
              <th scope="col">Event</th>
              <th scope="col">Registered on</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($records as $row): ?>
              <tr>
                <td>#<?php echo (int)$row['registration_id']; ?></td>
                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                <td><?php echo htmlspecialchars($row['admission_number']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo htmlspecialchars($row['course']); ?></td>
                <td><span class="badge-event"><?php echo htmlspecialchars($row['event_name']); ?></span></td>
                <td><?php echo date('d M Y, g:i A', strtotime($row['registration_date'])); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
