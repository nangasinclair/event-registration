<?php
require_once 'includes/config.php';
$page_title = 'Home';
$active_page = 'home';

$total_events = 0;
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM events");
if ($result) {
    $total_events = mysqli_fetch_assoc($result)['total'];
}

$total_registrations = 0;
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM registrations");
if ($result) {
    $total_registrations = mysqli_fetch_assoc($result)['total'];
}

$featured_events = [];
$query = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $featured_events[] = $row;
    }
}

require_once 'includes/header.php';
?>

<section class="hero">
  <div class="container">
    <div class="row align-items-center gy-5">
      <div class="col-lg-7">
        <p class="hero-eyebrow">Student Affairs &middot; Macmillan Medical Training College</p>
        <h1 class="hero-title">Every college event, one register away.</h1>
        <p class="hero-lead">Browse workshops, symposiums, drives and fairs happening across campus, then reserve your seat in under a minute &mdash; no queuing at the notice board required.</p>
        <div class="hero-actions">
          <a href="events.php" class="btn btn-mmtc-primary btn-lg">Browse events <i class="fa-solid fa-arrow-right"></i></a>
          <a href="records.php" class="btn btn-mmtc-outline btn-lg">View registration records</a>
        </div>
        <div class="hero-stats">
          <div class="hero-stat">
            <span class="hero-stat-number"><?php echo (int)$total_events; ?></span>
            <span class="hero-stat-label">Upcoming &amp; recent events</span>
          </div>
          <div class="hero-stat">
            <span class="hero-stat-number"><?php echo (int)$total_registrations; ?></span>
            <span class="hero-stat-label">Students registered</span>
          </div>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="hero-card">
          <span class="hero-card-kicker">Next up</span>
          <?php if (count($featured_events) > 0): $next = $featured_events[0]; ?>
            <h3 class="hero-card-title"><?php echo htmlspecialchars($next['event_name']); ?></h3>
            <ul class="hero-card-meta">
              <li><i class="fa-regular fa-calendar"></i> <?php echo date('l, d F Y', strtotime($next['event_date'])); ?></li>
              <li><i class="fa-regular fa-clock"></i> <?php echo date('g:i A', strtotime($next['event_time'])); ?></li>
              <li><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($next['venue']); ?></li>
            </ul>
            <a href="register.php?event_id=<?php echo (int)$next['event_id']; ?>" class="btn btn-mmtc-primary w-100">Register now</a>
          <?php else: ?>
            <p>No upcoming events at the moment &mdash; check back soon.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-featured">
  <div class="container">
    <div class="section-heading">
      <h2>Featured events</h2>
      <a href="events.php" class="section-heading-link">See all events <i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <?php if (count($featured_events) === 0): ?>
      <div class="alert alert-mmtc-empty">There are no upcoming events published yet. Please check back later.</div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($featured_events as $event): ?>
          <div class="col-md-6 col-lg-4">
            <div class="event-card category-<?php echo category_slug($event['category']); ?>">
              <span class="event-card-category"><?php echo htmlspecialchars($event['category']); ?></span>
              <h3 class="event-card-title"><?php echo htmlspecialchars($event['event_name']); ?></h3>
              <p class="event-card-desc"><?php echo htmlspecialchars(mb_strimwidth($event['event_description'], 0, 110, '…')); ?></p>
              <ul class="event-card-meta">
                <li><i class="fa-regular fa-calendar"></i> <?php echo date('d M Y', strtotime($event['event_date'])); ?></li>
                <li><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($event['venue']); ?></li>
              </ul>
              <a href="register.php?event_id=<?php echo (int)$event['event_id']; ?>" class="btn btn-mmtc-primary w-100">Register</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="section-how">
  <div class="container">
    <div class="section-heading">
      <h2>How registration works</h2>
    </div>
    <div class="row g-4">
      <div class="col-md-3 col-6">
        <div class="how-step">
          <span class="how-step-index">1</span>
          <h4>Browse events</h4>
          <p>See what's happening across campus this term.</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="how-step">
          <span class="how-step-index">2</span>
          <h4>Pick one</h4>
          <p>Open an event to see the full details and venue.</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="how-step">
          <span class="how-step-index">3</span>
          <h4>Fill the form</h4>
          <p>Enter your details once &mdash; it's validated as you type.</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="how-step">
          <span class="how-step-index">4</span>
          <h4>You're in</h4>
          <p>Your seat is saved and appears on the records page.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
