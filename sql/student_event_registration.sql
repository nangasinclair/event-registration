-- ============================================================
-- Macmillan Medical Training College (MMTC)
-- Student Event Registration Management System
-- Database export
-- ============================================================

CREATE DATABASE IF NOT EXISTS student_event_registration
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE student_event_registration;

-- ------------------------------------------------------------
-- Table: events
-- Holds every event students can register for.
-- ------------------------------------------------------------
DROP TABLE IF EXISTS registrations;
DROP TABLE IF EXISTS events;

CREATE TABLE events (
    event_id          INT AUTO_INCREMENT PRIMARY KEY,
    event_name        VARCHAR(150)  NOT NULL,
    category          VARCHAR(50)   NOT NULL,
    event_description TEXT          NOT NULL,
    event_date        DATE          NOT NULL,
    event_time        TIME          NOT NULL,
    venue             VARCHAR(150)  NOT NULL,
    capacity          INT           NOT NULL DEFAULT 100,
    created_at        TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: registrations
-- Holds every student registration submitted through the form.
-- ------------------------------------------------------------
CREATE TABLE registrations (
    registration_id   INT AUTO_INCREMENT PRIMARY KEY,
    student_name       VARCHAR(100)  NOT NULL,
    admission_number   VARCHAR(50)   NOT NULL,
    email              VARCHAR(150)  NOT NULL,
    phone              VARCHAR(20)   NOT NULL,
    course             VARCHAR(100)  NOT NULL,
    event_id           INT           NOT NULL,
    special_requirements VARCHAR(300) DEFAULT NULL,
    registration_date  DATETIME      DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_registrations_event
        FOREIGN KEY (event_id) REFERENCES events(event_id)
        ON DELETE CASCADE,
    CONSTRAINT uq_admission_per_event
        UNIQUE (event_id, admission_number)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Seed data: events
-- ------------------------------------------------------------
INSERT INTO events (event_name, category, event_description, event_date, event_time, venue, capacity) VALUES
('Clinical Skills Workshop', 'Academic', 'Hands-on practice in suturing, injections, and patient assessment led by senior clinical instructors.', '2026-10-14', '09:00:00', 'Skills Lab A', 40),
('Inter-College Blood Donation Drive', 'Health & Wellness', 'MMTC partners with the Kenya Red Cross to host a campus-wide blood donation exercise.', '2026-10-21', '08:30:00', 'Main Quadrangle', 200),
('Nursing Week Symposium', 'Academic', 'A two-day symposium featuring guest lecturers on emerging trends in community health nursing.', '2026-11-05', '10:00:00', 'Auditorium Hall', 150),
('MMTC Sports & Wellness Day', 'Sports', 'Inter-department football, netball, and athletics, capped off with a wellness fun run.', '2026-11-18', '08:00:00', 'College Sports Ground', 300),
('Healthcare Careers Fair', 'Career', 'Meet recruiters from hospitals and NGOs, and explore internship and attachment opportunities.', '2026-12-02', '09:00:00', 'Multipurpose Hall', 250),
('Mental Health Awareness Talk', 'Health & Wellness', 'An open conversation on student wellbeing, stress management, and available campus support services.', '2026-12-09', '14:00:00', 'Lecture Theatre 2', 120);

-- ------------------------------------------------------------
-- Seed data: registrations (sample records for demo purposes)
-- ------------------------------------------------------------
INSERT INTO registrations (student_name, admission_number, email, phone, course, event_id, special_requirements) VALUES
('Amina Hassan', 'MMTC/2024/0113', 'amina.hassan@student.mmtc.ac.ke', '0712345678', 'Diploma in Nursing', 1, NULL),
('Brian Otieno', 'MMTC/2023/0087', 'brian.otieno@student.mmtc.ac.ke', '0723456789', 'Diploma in Clinical Medicine', 2, 'Requires wheelchair access'),
('Cynthia Wanjiru', 'MMTC/2024/0056', 'cynthia.wanjiru@student.mmtc.ac.ke', '0734567890', 'Certificate in Community Health', 3, NULL),
('David Kiptoo', 'MMTC/2022/0210', 'david.kiptoo@student.mmtc.ac.ke', '0745678901', 'Diploma in Pharmaceutical Technology', 4, NULL);
