CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `role` ENUM('admin', 'driver') NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `cnic` VARCHAR(20),
  `joining_date` DATE,
  `grade` VARCHAR(10),
  `grade_comments` TEXT,
  `is_deleted` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `cars` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `model` VARCHAR(50),
  `plate_number` VARCHAR(20) UNIQUE NOT NULL,
  `color` VARCHAR(30),
  `status` ENUM('Available', 'Assigned', 'In Maintenance') DEFAULT 'Available',
  `private_notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `assignments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `driver_id` INT,
  `car_id` INT,
  `start_date` DATE,
  `end_date` DATE NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`driver_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`)
);

CREATE TABLE `wages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `driver_id` INT,
  `date` DATE NOT NULL,
  `amount` DECIMAL(10, 2) DEFAULT 3200.00,
  `is_paid` TINYINT(1) DEFAULT 0,
  `paid_date` DATE NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`driver_id`) REFERENCES `users`(`id`)
);

CREATE TABLE `maintenance` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `car_id` INT,
  `type` VARCHAR(50) NOT NULL,
  `reading_value` INT,
  `photo_path` VARCHAR(255),
  `maintenance_date` DATE,
  `next_due_days` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`)
);

CREATE TABLE `driver_calls` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `driver_id` INT,
  `call_date` DATE,
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`driver_id`) REFERENCES `users`(`id`)
);

-- Insert a default admin user
INSERT INTO `users` (`role`, `username`, `password`, `name`) VALUES
('admin', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator'); -- password is 'password'
