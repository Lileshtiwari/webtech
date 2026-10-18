-- =======================================================
-- WebTech Lab Hub — Full-Stack Web Applications
-- Experiment 6: Web Application for Food Delivery System Using PHP and MySQL
-- Database: food_delivery
-- =======================================================

CREATE DATABASE IF NOT EXISTS `food_delivery`
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `food_delivery`;

-- Drop tables in reverse foreign-key dependency order
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `food_items`;
DROP TABLE IF EXISTS `users`;

-- 1. users table
CREATE TABLE `users` (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `mobile` VARCHAR(20) NOT NULL,
    `address` TEXT NOT NULL,
    `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. food_items table
CREATE TABLE `food_items` (
    `food_id` INT AUTO_INCREMENT PRIMARY KEY,
    `food_name` VARCHAR(150) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `description` TEXT NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `availability` TINYINT(1) NOT NULL DEFAULT 1,
    `image_url` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `chk_price` CHECK (`price` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. orders table
CREATE TABLE `orders` (
    `order_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `order_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `delivery_name` VARCHAR(150) NOT NULL,
    `delivery_mobile` VARCHAR(20) NOT NULL,
    `delivery_address` TEXT NOT NULL,
    `order_status` ENUM('Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled') NOT NULL DEFAULT 'Pending',
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. order_items table
CREATE TABLE `order_items` (
    `order_item_id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `food_id` INT NOT NULL,
    `quantity` INT NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`order_id`) ON DELETE CASCADE,
    FOREIGN KEY (`food_id`) REFERENCES `food_items`(`food_id`) ON DELETE RESTRICT,
    CONSTRAINT `chk_qty` CHECK (`quantity` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Demonstration Seed Users:
-- Admin: email: admin@foodhub.com / password: Admin@123
-- Student: email: student@apex.edu.in / password: Student@123
INSERT INTO `users` (`name`, `email`, `password`, `mobile`, `address`, `role`) VALUES
('Hub Administrator', 'admin@foodhub.com', '$2y$10$Tf0EJfhLxQrWhNs3ip.KpudUiiT83f8xboYSFhPoEFMlt.sBtS6Ca', '9876543210', 'Admin Suite 402, Campus Towers', 'admin'),
('Praveen Kumar', 'student@apex.edu.in', '$2y$10$lvbHWzyelHC7XjFnK1lbg.YRrbZMFWPR9RB3dMwrjuE2DAppfm9Ly', '9123456789', 'Room 312, Boys Hostel Block B', 'user');

-- Demonstration Seed Food Items
INSERT INTO `food_items` (`food_name`, `category`, `description`, `price`, `availability`, `image_url`) VALUES
('Artisan Paneer Tikka Burger', 'Burgers', 'Spiced cottage cheese patty, mint mayo, crisp lettuce, heirloom tomato on a toasted brioche bun.', 189.00, 1, 'assets/images/food-burger.svg'),
('Woodfired Margherita Pizza', 'Pizza', 'San Marzano tomato base, fresh buffalo mozzarella, aromatic basil, and extra virgin olive oil.', 299.00, 1, 'assets/images/food-pizza.svg'),
('Hyderabadi Dum Biryani', 'Biryani', 'Aromatic aged basmati rice cooked with garden vegetables, saffron, whole spices, and served with mirchi ka salan.', 249.00, 1, 'assets/images/food-biryani.svg'),
('Creamy Truffle Mushroom Pasta', 'Pasta', 'Al dente fettuccine tossed in a silky roasted garlic and wild mushroom truffle cream sauce.', 279.00, 1, 'assets/images/food-pasta.svg'),
('Mediterranean Quinoa Salad', 'Salads', 'Organic quinoa, Persian cucumbers, Kalamata olives, cherry tomatoes, and crumbled Greek feta with lemon vinaigrette.', 199.00, 1, 'assets/images/food-salad.svg'),
('Belgian Dark Chocolate Lava Cake', 'Desserts', 'Warm molten chocolate center enveloped in rich sponge, dusted with Valrhona cocoa.', 149.00, 1, 'assets/images/food-dessert.svg'),
('Classic Veg Supreme Club Sandwich', 'Snacks', 'Triple-layered toasted whole wheat bread packed with cheddar, cucumber, spiced potato and cilantro chutney.', 159.00, 1, 'assets/images/food-sandwich.svg'),
('Signature Mango Berry Smoothie', 'Beverages', 'Alphonso mango pulp blended with Greek yogurt, wild blueberries, and organic chia seeds.', 129.00, 1, 'assets/images/food-smoothie.svg');

-- Sample Initial Completed Order for Demo Student (Praveen Kumar, user_id = 2)
INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `total_amount`, `delivery_name`, `delivery_mobile`, `delivery_address`, `order_status`) VALUES
(1001, 2, '2026-10-17 19:40:00', 438.00, 'Praveen Kumar', '9123456789', 'Room 312, Boys Hostel Block B', 'Delivered');

INSERT INTO `order_items` (`order_id`, `food_id`, `quantity`, `price`) VALUES
(1001, 1, 1, 189.00),
(1001, 3, 1, 249.00);
