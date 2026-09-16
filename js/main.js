document.addEventListener('DOMContentLoaded', function () {

  /* ============================================================
     1. DARK / LIGHT MODE TOGGLE
     ============================================================ */
  (function themeToggle() {
    var toggleBtn = document.getElementById('themeToggle');
    if (!toggleBtn) return;

    var savedTheme = localStorage.getItem('mmtc-theme') || 'light';
    applyTheme(savedTheme);

    toggleBtn.addEventListener('click', function () {
      var current = document.body.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
      var next = current === 'dark' ? 'light' : 'dark';
      applyTheme(next);
      localStorage.setItem('mmtc-theme', next);
    });

    function applyTheme(theme) {
      if (theme === 'dark') {
        document.body.setAttribute('data-theme', 'dark');
        toggleBtn.innerHTML = '<i class="fa-solid fa-sun"></i>';
      } else {
        document.body.removeAttribute('data-theme');
        toggleBtn.innerHTML = '<i class="fa-solid fa-moon"></i>';
      }
    }
  })();

  /* ============================================================
     2. EVENT COUNTDOWN (events.php cards)
     ============================================================ */
  (function eventCountdowns() {
    var countdownEls = document.querySelectorAll('.event-countdown[data-event-date]');
    if (countdownEls.length === 0) return;

    function updateCountdowns() {
      var now = new Date();
      countdownEls.forEach(function (el) {
        var target = new Date(el.getAttribute('data-event-date'));
        var diffMs = target - now;

        if (diffMs <= 0) {
          el.textContent = 'Happening now / today';
          return;
        }
        var days = Math.floor(diffMs / (1000 * 60 * 60 * 24));
        var hours = Math.floor((diffMs / (1000 * 60 * 60)) % 24);

        if (days > 0) {
          el.textContent = days + ' day' + (days === 1 ? '' : 's') + ' ' + hours + ' hr' + (hours === 1 ? '' : 's') + ' to go';
        } else {
          var minutes = Math.floor((diffMs / (1000 * 60)) % 60);
          el.textContent = hours + ' hr ' + minutes + ' min to go';
        }
      });
    }

    updateCountdowns();
    setInterval(updateCountdowns, 60000);
  })();

  /* ============================================================
     3. EVENT SEARCH / FILTER (events.php)
     ============================================================ */
  (function eventSearch() {
    var input = document.getElementById('eventSearch');
    var grid = document.getElementById('eventsGrid');
    if (!input || !grid) return;

    var cards = Array.prototype.slice.call(grid.querySelectorAll('.event-grid-item'));
    var countLabel = document.getElementById('eventsCount');
    var noResults = document.getElementById('eventsNoResults');

    input.addEventListener('input', function () {
      var term = input.value.trim().toLowerCase();
      var visibleCount = 0;

      cards.forEach(function (card) {
        var haystack = card.dataset.name + ' ' + card.dataset.category + ' ' + card.dataset.venue;
        var matches = haystack.indexOf(term) !== -1;
        card.classList.toggle('d-none', !matches);
        if (matches) visibleCount++;
      });

      if (countLabel) {
        countLabel.textContent = visibleCount + ' event' + (visibleCount === 1 ? '' : 's') + ' found';
      }
      if (noResults) {
        noResults.classList.toggle('d-none', visibleCount !== 0);
      }
    });
  })();

  /* ============================================================
     4. CHARACTER COUNTER (register.php — special requirements)
     ============================================================ */
  (function charCounter() {
    var textarea = document.getElementById('special_requirements');
    var counter = document.getElementById('charCount');
    if (!textarea || !counter) return;

    function update() { counter.textContent = textarea.value.length; }
    update();
    textarea.addEventListener('input', update);
  })();

  /* ============================================================
     5. CLIENT-SIDE VALIDATION + CONFIRMATION MODAL
        (register.php registration form)
     ============================================================ */
  (function registrationFormFlow() {
    var form = document.getElementById('registrationForm');
    if (!form) return;

    var confirmModalEl = document.getElementById('confirmModal');
    var confirmModal = confirmModalEl ? new bootstrap.Modal(confirmModalEl) : null;
    var confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
    var confirmStudentName = document.getElementById('confirmStudentName');

    form.addEventListener('submit', function (event) {
      // Always stop the default submit first — Bootstrap validity styling,
      // then either open the confirmation modal or block the submission.
      event.preventDefault();
      event.stopPropagation();

      if (!form.checkValidity()) {
        form.classList.add('was-validated');
        var firstInvalid = form.querySelector(':invalid');
        if (firstInvalid) firstInvalid.focus();
        return;
      }

      form.classList.add('was-validated');

      if (confirmModal) {
        var nameField = document.getElementById('student_name');
        if (confirmStudentName) confirmStudentName.textContent = nameField.value || 'you';
        confirmModal.show();
      } else {
        form.submit();
      }
    });

    if (confirmSubmitBtn) {
      confirmSubmitBtn.addEventListener('click', function () {
        confirmSubmitBtn.disabled = true;
        confirmSubmitBtn.textContent = 'Submitting…';
        form.submit();
      });
    }
  })();

  /* ============================================================
     6. SUCCESS / ERROR ALERT BEHAVIOUR
        (auto-dismiss success alerts after a few seconds)
     ============================================================ */
  (function alertBehaviour() {
    var successAlert = document.querySelector('.alert-mmtc-success');
    if (!successAlert) return;

    setTimeout(function () {
      successAlert.style.transition = 'opacity .4s ease';
      successAlert.style.opacity = '0';
      setTimeout(function () { successAlert.remove(); }, 400);
    }, 6000);
  })();

  /* ============================================================
     7. LIVE SEARCH / FILTER — Registration Records table
     ============================================================ */
  (function recordsLiveFilter() {
    var input = document.getElementById('recordsSearch');
    var table = document.getElementById('recordsTable');
    if (!input || !table) return;

    var tbody = table.querySelector('tbody');
    var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));

    input.addEventListener('input', function () {
      var term = input.value.trim().toLowerCase();
      var visibleCount = 0;

      rows.forEach(function (row) {
        var matches = row.textContent.toLowerCase().indexOf(term) !== -1;
        row.classList.toggle('d-none', !matches);
        if (matches) visibleCount++;
      });

      var visibleLabel = document.getElementById('visibleCountLabel');
      if (visibleLabel) {
        visibleLabel.textContent = term === ''
          ? 'Showing all ' + visibleCount + ' record' + (visibleCount === 1 ? '' : 's')
          : 'Showing ' + visibleCount + ' result' + (visibleCount === 1 ? '' : 's') + ' for “' + input.value.trim() + '”';
      }

      var existingNoResults = tbody.querySelector('.no-results-row');
      if (visibleCount === 0) {
        if (!existingNoResults) {
          var tr = document.createElement('tr');
          tr.className = 'no-results-row';
          var td = document.createElement('td');
          td.colSpan = 8;
          td.textContent = 'No registrations match your search.';
          tr.appendChild(td);
          tbody.appendChild(tr);
        }
      } else if (existingNoResults) {
        existingNoResults.remove();
      }
    });
  })();

});
