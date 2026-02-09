-- enhanced_vehicle.sql
CREATE DATABASE IF NOT EXISTS vehicle;

USE vehicle;

-- Existing tables with additions for location tracking
CREATE TABLE `admins` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`email` varchar(255) NOT NULL,
`password` varchar(255) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `service_providers` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`email` varchar(255) NOT NULL,
`password` varchar(255) NOT NULL,
`business_name` varchar(255) NOT NULL,
`business_phone` varchar(20) NOT NULL,
`workshop_address` varchar(255) NOT NULL,
`workshop_latitude` decimal(10,8) DEFAULT NULL,
`workshop_longitude` decimal(11,8) DEFAULT NULL,
`current_latitude` decimal(10,8) DEFAULT NULL,
`current_longitude` decimal(11,8) DEFAULT NULL,
`experience` int(11) NOT NULL,
`service_radius` int(11) NOT NULL DEFAULT 10,
`services_offered` varchar(255) NOT NULL,
`availability` tinyint(1) NOT NULL DEFAULT 0,
`online_status` tinyint(1) NOT NULL DEFAULT 0,
`rating` decimal(3,2) DEFAULT 0.00,
`last_location_update` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `vehicle_owners` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`email` varchar(255) NOT NULL,
`password` varchar(255) NOT NULL,
`phone_number` varchar(20) NOT NULL,
`current_latitude` decimal(10,8) DEFAULT NULL,
`current_longitude` decimal(11,8) DEFAULT NULL,
`last_location_update` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `vehicles` (
`vehicle_id` int(11) NOT NULL AUTO_INCREMENT,
`owner_id` int(11) NOT NULL,
`vehicle_type` varchar(50) NOT NULL,
`vehicle_brand` varchar(50) NOT NULL,
`vehicle_model` varchar(50) NOT NULL,
`number_plate` varchar(20) NOT NULL,
`fuel_type` varchar(20) NOT NULL,
PRIMARY KEY (`vehicle_id`),
FOREIGN KEY (`owner_id`) REFERENCES `vehicle_owners`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `service_requests` (
`request_id` int(11) NOT NULL AUTO_INCREMENT,
`vehicle_id` int(11) NOT NULL,
`service_type` varchar(255) NOT NULL,
`location` varchar(255) NOT NULL,
`latitude` decimal(10,8) DEFAULT NULL,
`longitude` decimal(11,8) DEFAULT NULL,
`status` varchar(50) NOT NULL DEFAULT 'pending',
`job_status` varchar(50) NOT NULL DEFAULT 'pending',
`provider_id` int(11) DEFAULT NULL,
`request_time` timestamp NOT NULL DEFAULT current_timestamp(),
`accepted_time` timestamp NULL DEFAULT NULL,
`started_time` timestamp NULL DEFAULT NULL,
`arrived_time` timestamp NULL DEFAULT NULL,
`in_progress_time` timestamp NULL DEFAULT NULL,
`completed_time` timestamp NULL DEFAULT NULL,
`estimated_arrival` int(11) DEFAULT NULL,
PRIMARY KEY (`request_id`),
FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles`(`vehicle_id`) ON DELETE CASCADE,
FOREIGN KEY (`provider_id`) REFERENCES `service_providers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- New table for job status tracking
CREATE TABLE `job_status_updates` (
`update_id` int(11) NOT NULL AUTO_INCREMENT,
`request_id` int(11) NOT NULL,
`status` varchar(50) NOT NULL,
`message` text DEFAULT NULL,
`update_time` timestamp NOT NULL DEFAULT current_timestamp(),
PRIMARY KEY (`update_id`),
FOREIGN KEY (`request_id`) REFERENCES `service_requests`(`request_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `service_history` (
`history_id` int(11) NOT NULL AUTO_INCREMENT,
`request_id` int(11) NOT NULL,
`completion_time` timestamp NOT NULL DEFAULT current_timestamp(),
`cost` decimal(10,2) NOT NULL,
`feedback` text DEFAULT NULL,
`rating` int(1) DEFAULT NULL,
PRIMARY KEY (`history_id`),
FOREIGN KEY (`request_id`) REFERENCES `service_requests`(`request_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
