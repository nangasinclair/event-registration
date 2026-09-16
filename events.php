<?php
require_once 'includes/config.php';
$page_title = 'Events';
$active_page = 'events';

$events = [];
$query = "SELECT e.*,
          (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.event_id) AS registered_count
          FROM events e ORDER BY event_date ASC";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
}

require_once 'includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <p class="hero-eyebrow">All events</p>
    <h1 class="page-header-title">Upcoming college events</h1>
    <p class="page-header-lead">Every event currently published by the Student Affairs Office. Use the search box to jump straight to one.</p>

    <div class="events-search">
      <label for="eventSearch" class="visually-hidden">Search events</label>
      <i class="fa-solid fa-magnifying-glass events-search-icon"></i>
      <input type="text" id="eventSearch" class="form-control" placeholder="Search by event name, category or venue…" autocomplete="off">
    </div>
  </div>
</section>

<section class="section-events-grid">
  <div class="container">
    <?php if (count($events) === 0): ?>
      <div class="alert alert-mmtc-empty">No events have been published yet.</div>
    <?php else: ?>
      <p class="events-count" id="eventsCount"><?php echo count($events); ?> event<?php echo count($events) === 1 ? '' : 's'; ?> found</p>
      <div class="row g-4" id="eventsGrid">
        <?php foreach ($events as $event):
          $seats_left = max(0, (int)$event['capacity'] - (int)$event['registered_count']);
          $is_past = strtotime($event['event_date']) < strtotime(date('Y-m-d'));
        ?>
          <div class="col-md-6 col-lg-4 event-grid-item"
               data-name="<?php echo htmlspecialchars(strtolower($event['event_name'])); ?>"
               data-category="<?php echo htmlspecialchars(strtolower($event['category'])); ?>"
               data-venue="<?php echo htmlspecialchars(strtolower($event['venue'])); ?>">
            <div class="event-card category-<?php echo category_slug($event['category']); ?>">
              <span class="event-card-category"><?php echo htmlspecialchars($event['category']); ?></span>
              <h3 class="event-card-title"><?php echo htmlspecialchars($event['event_name']); ?></h3>
              <p class="event-card-desc"><?php echo htmlspecialchars($event['event_description']); ?></p>
              <ul class="event-card-meta">
                <li><i class="fa-regular fa-calendar"></i> <?php echo date('d M Y', strtotime($event['event_date'])); ?></li>
                <li><i class="fa-regular fa-clock"></i> <?php echo date('g:i A', strtotime($event['event_time'])); ?></li>
                <li><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($event['venue']); ?></li>
              </ul>

              <?php if ($is_past): ?>
                <p class="event-countdown event-countdown-past">This event has taken place</p>
              <?php else: ?>
                <p class="event-countdown" data-event-date="<?php echo $event['event_date']; ?>T<?php echo $event['event_time']; ?>">&nbsp;</p>
              <?php endif; ?>

              <p class="event-card-seats"><?php echo $seats_left; ?> of <?php echo (int)$event['capacity']; ?> seats remaining</p>

              <?php if ($is_past): ?>
                <button class="btn btn-mmtc-outline w-100" disabled>Registration closed</button>
              <?php elseif ($seats_left <= 0): ?>
                <button class="btn btn-mmtc-outline w-100" disabled>Fully booked</button>
              <?php else: ?>
                <a href="register.php?event_id=<?php echo (int)$event['event_id']; ?>" class="btn btn-mmtc-primary w-100">Register</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <p class="events-no-results d-none" id="eventsNoResults">No events match your search. Try a different keyword.</p>
    <?php endif; ?>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
