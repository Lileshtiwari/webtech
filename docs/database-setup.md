# WebTech Lab Hub — Database Setup Guide
**Institution:** KPR Institute of Engineering and Technology  
**Department:** Computer Science and Engineering  
**Databases Required:** `event_management` (Exp 5) and `food_delivery` (Exp 6)  

---

## 1. Local Database Environment Prerequisites
- **RDBMS:** MySQL 8.0 Server (or MariaDB 10.4+)
- **Default Port:** `3306`
- **Default Host:** `127.0.0.1` (or `localhost`)
- **Default User:** `root`
- **Default Password:** `root` (or configured via environment variables / `config.local.php`)

---

## 2. Importing Experiment 5 Database (`event_management`)

### Method A: Using MySQL Command Line Client
Open PowerShell or Command Prompt and execute:
```bash
mysql -u root -p -h 127.0.0.1 -P 3306 < "C:\xampp\htdocs\Lab_Expeiments\experiment-5\database.sql"
```
*(Enter your MySQL root password when prompted)*

### Method B: Using MySQL Workbench
1. Launch **MySQL Workbench** and connect to your local MySQL instance on port `3306`.
2. Go to **File &rarr; Open SQL Script...**
3. Select `C:\xampp\htdocs\Lab_Expeiments\experiment-5\database.sql`.
4. Click the yellow **Execute (Lightning bolt)** icon to run the script.
5. In the Schemas panel, right-click and choose **Refresh All** to verify `event_management` and its table `event_registrations`.

### Schema Structure (`event_registrations`)
```sql
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
```

---

## 3. Importing Experiment 6 Database (`food_delivery`)

### Method A: Using MySQL Command Line Client
```bash
mysql -u root -p -h 127.0.0.1 -P 3306 < "C:\xampp\htdocs\Lab_Expeiments\experiment-6\database.sql"
```

### Method B: Using MySQL Workbench
1. Open MySQL Workbench and navigate to **File &rarr; Open SQL Script...**
2. Select `C:\xampp\htdocs\Lab_Expeiments\experiment-6\database.sql`.
3. Click the yellow **Execute** icon.
4. Verify the 4 created relational tables: `users`, `food_items`, `orders`, `order_items`.

### Schema Relationships
- `orders.user_id` &rarr; `users.user_id` (Foreign Key, `ON DELETE CASCADE`)
- `order_items.order_id` &rarr; `orders.order_id` (Foreign Key, `ON DELETE CASCADE`)
- `order_items.food_id` &rarr; `food_items.food_id` (Foreign Key, `ON DELETE RESTRICT`)

---

## 4. Pre-configured Academic Demonstration Accounts
The database seed script automatically populates verified demonstration credentials with secure bcrypt password hashes:

| Role | Account Name | Email Address | Plaintext Password | Primary Access Area |
| :--- | :--- | :--- | :--- | :--- |
| **Student / Customer** | Praveen Kumar | `student@kpriet.ac.in` | `Student@123` | Menu, Active Cart, Checkout, Order History |
| **Administrator** | Hub Administrator | `admin@foodhub.com` | `Admin@123` | Admin Console, Order Status, Item Catalog CRUD |

---

## 5. Custom Credentials Configuration
If your local MySQL root password differs from `root`, you can configure credentials in either of two secure ways without modifying core repository files:

### Option 1: Environment Variables
Set the standard environment variables in Windows or Apache:
- `DB_HOST` (e.g. `127.0.0.1`)
- `DB_PORT` (e.g. `3306`)
- `DB_USER` (e.g. `root`)
- `DB_PASS` (e.g. `your_custom_password`)

### Option 2: Local Override File
Create `experiment-5/config.local.php` or `experiment-6/config.local.php`:
```php
<?php
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASS', 'your_custom_password');
```
*(Both `config.local.php` files are already excluded in `.gitignore`)*
