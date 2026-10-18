-- =======================================================
-- WebTech Lab Hub — Full-Stack Web Applications
-- Experiment 5: Event Registration Form Using PHP and MySQL
-- Database: event_management
-- =======================================================

CREATE DATABASE IF NOT EXISTS `event_management`
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `event_management`;

-- Drop table if already exists for clean idempotent setup
DROP TABLE IF EXISTS `event_registrations`;

CREATE TABLE `event_registrations` (
    `registration_id` INT AUTO_INCREMENT PRIMARY KEY,
    `participant_name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `mobile` VARCHAR(20) NOT NULL,
    `institution` VARCHAR(200) NOT NULL,
    `event_name` VARCHAR(150) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `participants` INT NOT NULL DEFAULT 1,
    `registration_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `chk_participants` CHECK (`participants` >= 1 AND `participants` <= 20),
    CONSTRAINT `uq_email_event` UNIQUE (`email`, `event_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional Demonstration Seed Data (Clearly marked as academic demo records)
INSERT INTO `event_registrations` 
(`participant_name`, `email`, `mobile`, `institution`, `event_name`, `category`, `participants`, `registration_date`) 
VALUES
('Aravindhan S', 'aravindhan.cse@apex.edu.in', '9876543210', 'Apex Institute of Technology', 'WebCraft 2026 Hackathon', 'Normal Pass', 4, '2026-10-15 10:30:00'),
('Divya Bharathi R', 'divya.b@cit.edu.in', '9845123456', 'Coimbatore Institute of Technology', 'AI in Web Design Symposium', 'Gold Pass', 1, '2026-10-16 11:15:00'),
('Karthikeyan M', 'karthik.m@psgtech.ac.in', '9789012345', 'PSG College of Technology', 'FullStack Code Sprint', 'Diamond Pass', 2, '2026-10-17 14:00:00'),
('Sowmya Lakshmi K', 'sowmya.k@gct.ac.in', '9655123987', 'Government College of Technology', 'UI/UX Design Masterclass', 'Kohinoor Pass', 1, '2026-10-18 09:20:00');
