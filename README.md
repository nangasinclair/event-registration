# MMTC Student Event Registration Management System

A mini project for **Web Application Development** — Macmillan Medical Training
College (MMTC), Nairobi. Students can browse college events and register for
them online; the Student Affairs Office can view every submitted registration
on a live records page.

Built with **HTML5, CSS3, Bootstrap 5, JavaScript, PHP (mysqli) and MySQL.**

## Features

- Home page with featured/upcoming events pulled live from MySQL
- Events page listing every event, with a JavaScript search box and a
  live per-event countdown
- Registration form with:
  - Client-side validation (Bootstrap validation states)
  - A confirmation modal shown before the form is actually submitted
  - A live character counter on the "special requirements" field
  - Server-side validation and duplicate-registration checking in PHP
  - Success / error messages after submitting
- Registration Records (admin) page:
  - Reads registrations straight from MySQL, joined with the event name
  - Server-side search (works even with JavaScript disabled) **and** an
    instant JavaScript live-filter as you type
  - Shows the total number of registered students
  - Responsive Bootstrap table
- Dark / light mode toggle (saved in the browser between visits)
- Fully responsive layout (desktop, tablet, mobile) using Bootstrap's grid
  and utility classes

## Project structure

```
student-event-registration/
├── index.php                 Home page
├── events.php                Browse all events
├── register.php              Registration form for a chosen event
├── process_registration.php  Validates and inserts a registration (PHP backend)
├── records.php                Admin / records page (search + table)
├── includes/
│   ├── config.php            Database connection (edit your credentials here)
│   ├── header.php             Shared navbar + <head>
│   └── footer.php             Shared footer + scripts
├── css/
│   └── style.css              All custom styling
├── js/
│   └── main.js                All JavaScript features
├── images/
│   └── mmtc-logo.jpeg
└── sql/
    └── student_event_registration.sql   Full database export (schema + sample data)
```

## How to run it locally (XAMPP / WAMP / MAMP)

1. **Install a local PHP + MySQL stack** such as XAMPP, WAMP, or MAMP, and
   start Apache and MySQL.

2. **Copy the project folder** into your server's web root, e.g.
   `C:\xampp\htdocs\student-event-registration` (Windows/XAMPP) or
   `/Applications/MAMP/htdocs/student-event-registration` (Mac/MAMP).

3. **Import the database.**
   - Open phpMyAdmin (`http://localhost/phpmyadmin`).
   - Click **Import**, choose `sql/student_event_registration.sql`, and run
     it. This creates the `student_event_registration` database with two
     tables (`events`, `registrations`) and some sample data.

4. **Check the database credentials** in `includes/config.php`. The
   defaults (`root` / no password) match a fresh XAMPP/MAMP install — change
   them if your setup is different.

5. **Open the site** in your browser:
   ```
   http://localhost/student-event-registration/index.php
   ```

6. Browse to **Events**, choose one, fill in the registration form and
   submit — the new record will immediately appear on the
   **Registration Records** page.

## Notes for markers / reviewers

- All database queries use prepared statements (`mysqli_prepare` /
  `mysqli_stmt_bind_param`) to prevent SQL injection.
- Registration data is both validated in the browser (JavaScript /
  Bootstrap) and re-validated on the server (`process_registration.php`)
  before it is written to MySQL, since client-side checks can always be
  bypassed.
- A student cannot register twice for the same event with the same
  admission number (enforced both in PHP and with a unique key in MySQL).
- Seats remaining and the "fully booked" state are calculated live from
  the `registrations` table, not hard-coded.
