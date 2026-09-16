<?php if (!isset($page_title)) { $page_title = 'MMTC Events'; } ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($page_title); ?> | MMTC Student Event Registration</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,500;8..60,600;8..60,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<a class="visually-hidden-focusable skip-link" href="#main-content">Skip to main content</a>

<nav class="navbar navbar-expand-lg sticky-top mmtc-navbar">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <img src="images/mmtc-logo.jpeg" alt="MMTC logo" class="brand-logo">
      <span class="brand-text">MMTC <span class="brand-text-light">Events</span></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link <?php echo ($active_page ?? '') === 'home' ? 'active' : ''; ?>" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link <?php echo ($active_page ?? '') === 'events' ? 'active' : ''; ?>" href="events.php">Events</a></li>
        <li class="nav-item"><a class="nav-link <?php echo ($active_page ?? '') === 'records' ? 'active' : ''; ?>" href="records.php">Registration Records</a></li>
        <li class="nav-item ms-lg-2">
          <button id="themeToggle" class="btn btn-theme-toggle" type="button" aria-label="Toggle dark mode" title="Toggle dark / light mode">
            <i class="fa-solid fa-moon"></i>
          </button>
        </li>
      </ul>
    </div>
  </div>
</nav>

<main id="main-content">
