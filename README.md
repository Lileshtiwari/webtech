# WebTech Lab Hub

<div align="center">

![WebTech Innovation Hero Banner](assets/images/top_hero_section.png)

# Innovate. Create. Lead the Future.
### Web Technologies Laboratory Engineering Suite — U21CS504
**Department of Computer Science and Engineering · KPR Institute of Engineering and Technology**  
*Curriculum Scope: Experiments 1 through 6 · Designed & Developed by Lilesh Kumar Tiwari*

---

[![PHP 8.2](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL 8.0](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![JavaScript ES6+](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![HTML5 & CSS3](https://img.shields.io/badge/HTML5_%26_CSS3-Semantic-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/HTML)
[![Apache HTTP Server](https://img.shields.io/badge/Apache-2.4-D22128?style=for-the-badge&logo=apache&logoColor=white)](https://httpd.apache.org/)

</div>

---

## 🛠️ Technology Stacks & Architectural Layers

| Layer | Technologies & Standards | Implementation Highlights |
| :--- | :--- | :--- |
| **Frontend Core** | HTML5 Semantic Architecture, CSS3 (Custom Properties, Grid & Flexbox) | High-contrast accessibility, responsive viewport design, zero third-party UI packages |
| **Client Scripting** | Vanilla JavaScript (ES6+), DOM Level 3 API, Regular Expressions | Real-time event delegation, client-side schema validation, mathematical computation |
| **Asynchronous I/O** | Fetch API, RESTful JSON interop, `AbortController` | Live asynchronous external API integration, race-condition mitigation, cached fallback |
| **Server Engine** | PHP 8.2 (Procedural & Object-Oriented MVC Modules) | Parameterized PDO statements, CSRF protection, secure Bcrypt hashing, server session control |
| **Relational Database** | MySQL 8.0 Database Engine (InnoDB) | Strict foreign keys, referential integrity, ACID transactional rollbacks |
| **Hosting Environment** | Apache 2.4 under XAMPP Suite (Port `8080` / `3306`) | Virtual directory routing, multi-platform localhost deployment |

---

## 🔐 Master Access Credentials & Default Logins

> [!IMPORTANT]
> Administrative credentials have been intentionally moved from front-facing portal cards into this documentation for clean production security standards. Use the pre-configured credentials below to evaluate administrative dashboards.

### 1. Experiment 5: EventPulse Summit — Admin Attendee Portal
- **Dashboard URL:** `http://localhost:8080/Lab_Expeiments/experiment-5/admin.php`
- **Default Username:** `admin` *(or `pulseadmin`)*
- **Default Password:** `eventpulse@2026` *(or `admin123`)*
- **Privileges:** Manage registered attendees, filter by Pass Tiers (*Normal*, *Gold*, *Diamond*, *Kohinoor*), export live records to CSV, and print clean printer-friendly PDF summaries.

### 2. Experiment 6: foco Campus Food Delivery — Multi-Role Accounts
- **Portal URL:** `http://localhost:8080/Lab_Expeiments/experiment-6/login.php`

| Role | Email Address | Password | Capabilities & Access |
| :--- | :--- | :--- | :--- |
| **Store Administrator** | `admin@foodhub.com` | `Admin@123` | Full admin dashboard, manage incoming orders, transition delivery states (*Pending &rarr; Preparing &rarr; Dispatched &rarr; Delivered*), and manage food menu items. |
| **Student / Customer** | `student@kpriet.ac.in`<br>*(alt: `student@apex.edu.in`)* | `Student@123` | Browse gourmet meals, dynamic live cart with instant tallying, single-click order placement, personal order history, and downloadable receipts. |

### 3. Database Connection Parameters
- **Host:** `127.0.0.1` *(or `localhost`)*
- **Port:** `3306`
- **Username:** `root`
- **Password:** `""` *(empty string by default in XAMPP, fallback to `root` if configured)*
- **Databases:** `event_management` (Exp 5), `food_delivery` (Exp 6)

---

## 🔬 Curriculum Lab Experiments (1–6)

```
========================================================================================
EXPERIMENT 1 · Static Web Page Design Using HTML and CSS
Route: /experiment-1/index.html
========================================================================================
- Modern showcase page built strictly with semantic HTML5 elements and vanilla CSS3.
- Features multi-column responsive grid, custom typography, feature cards, and contact section.
- Demonstrates box-model mastery, CSS custom variables, hover micro-interactions, and media queries.

========================================================================================
EXPERIMENT 2 · Clinical BMI Calculator Using JavaScript
Route: /experiment-2/index.html
========================================================================================
- Real-time Body Mass Index computation using the standard metric formula: BMI = weight / height^2.
- Instant health category classification: Underweight, Normal Weight, Overweight, Obese.
- Features dual-mode unit inputs (Metric & Imperial), dynamic visual gauge needle, and health recommendations.

========================================================================================
EXPERIMENT 3 · User Registration Form Validation Using JavaScript
Route: /experiment-3/index.html
========================================================================================
- Complete client-side validation suite enforcing strict business rules before submission.
- Real-time RegEx validation for Full Names, RFC-compliant Emails, 10-digit Indian Mobile Numbers,
  and 8+ character Alphanumeric Passwords with special symbol requirements.
- Dynamic visual feedback states with contextual error messages and password visibility toggle.

========================================================================================
EXPERIMENT 4 · Web Applications with Live API Calls Using JavaScript
Route: /experiment-4/index.html
========================================================================================
- Asynchronous data fetching integrating the public REST Countries v3.1 API.
- Live search debouncing, regional filtering (Asia, Europe, Americas, Africa, Oceania), and sorting.
- Features AbortController to cancel stale in-flight requests and an embedded offline fallback cache.

========================================================================================
EXPERIMENT 5 · EventPulse Summit: Event Registration Using PHP & MySQL
Route: /experiment-5/index.php
========================================================================================
- High-throughput summit delegate registration supporting 4 distinct pass tiers:
  * Normal Pass ($99)
  * Gold Pass ($249)
  * Diamond Pass ($499)
  * Kohinoor Pass ($899)
- Parameterized PDO prepared statements preventing SQL Injection, duplicate registration blocking.
- Password-protected Admin Management Portal (`admin.php`) with pass tier filtering, CSV export, and PDF printing.

========================================================================================
EXPERIMENT 6 · foco Campus Food Delivery: Full-Stack PHP & MySQL Web App
Route: /experiment-6/index.php
========================================================================================
- Production-grade online food ordering platform with customer registration and Bcrypt password hashing.
- Persistent session cart with live quantity adjustment, item deletion, and subtotal calculation.
- ACID database transactions saving orders and order_items with snapshot pricing.
- Complete Order History with status tracking and Admin Dispatch Console (`/admin/orders.php`).
```

---

## 📂 Project Architecture & Directory Layout

```
c:/xampp/htdocs/Lab_Expeiments/
│
├── index.html                   # Central WebTech Platform Landing Hub
├── index.php                    # Root PHP Router & Environment Checker
├── about.html                   # Platform Vision, Standards & Contact Page
├── README.md                    # System Documentation & Reference Manual
│
├── assets/                      # Shared Static Assets
│   ├── css/
│   │   ├── design-system.css    # Design tokens, typography, and button utility classes
│   │   └── main.css             # Main Hub landing layout & hero banner styles
│   ├── images/
│   │   ├── top_hero_section.png # Top hero campus illustration banner
│   │   └── platform_logo.png    # WebTech Lab Hub vector logo
│   └── svg/                     # Dedicated branding SVGs for all experiments
│
├── experiment-1/                # Exp 1: Static Web Page Design
│   ├── index.html               # Semantic landing page
│   └── style.css                # Pure CSS3 styles
│
├── experiment-2/                # Exp 2: BMI Calculator
│   ├── index.html               # Calculator interface
│   ├── script.js                # Calculation & gauge logic
│   └── style.css                # Medical dashboard styling
│
├── experiment-3/                # Exp 3: User Registration Validation
│   ├── index.html               # Registration form
│   ├── validation.js            # RegEx rules & error feedback
│   └── style.css                # Form styling & state indicators
│
├── experiment-4/                # Exp 4: REST Countries API Explorer
│   ├── index.html               # Explorer dashboard
│   ├── script.js                # Fetch API & AbortController integration
│   └── style.css                # Card grid & modal styling
│
├── experiment-5/                # Exp 5: EventPulse Platform (PHP + MySQL)
│   ├── config.php               # Database configuration
│   ├── db.php                   # PDO database connector
│   ├── index.php                # Summit overview & pass booking form
│   ├── register.php             # Server-side validation & record insertion
│   ├── admin.php                # Password-gated admin portal & pass filter
│   ├── database.sql             # Schema definition & initial seed records
│   └── style.css                # Dark luxury summit aesthetic
│
└── experiment-6/                # Exp 6: foco Food Delivery Platform (PHP + MySQL)
    ├── config.php               # Database configuration
    ├── db.php                   # Transactional PDO connector
    ├── index.php                # Restaurant storefront & menu catalog
    ├── login.php                # Multi-role authentication (Bcrypt)
    ├── register.php             # Customer registration
    ├── cart.php                 # Interactive session cart
    ├── checkout.php             # ACID transactional order placement
    ├── order-history.php        # Customer past orders & receipt viewer
    ├── logout.php               # Session destruction
    ├── database.sql             # Relational schema (Users, Items, Orders, Items)
    ├── style.css                # Modern gourmet food styling
    └── admin/
        ├── index.php            # Admin metrics & quick navigation
        ├── orders.php           # Order fulfillment dispatch console
        └── items.php            # Menu catalog management
```

---

## 🚀 Setup & Local Execution Guide

### Step 1: Start XAMPP Services
Open the **XAMPP Control Panel** and start:
1. **Apache HTTP Server** (Configured on Port `8080`)
2. **MySQL Database Engine** (Configured on Port `3306`)

### Step 2: Database Initialization (Idempotent)
Both databases are pre-populated with academic sample data. If resetting or initializing on a new machine:

```powershell
# Import Experiment 5 Schema & Seed Records
mysql -u root -p -h 127.0.0.1 -P 3306 < "c:\xampp\htdocs\Lab_Expeiments\experiment-5\database.sql"

# Import Experiment 6 Schema & Seed Records
mysql -u root -p -h 127.0.0.1 -P 3306 < "c:\xampp\htdocs\Lab_Expeiments\experiment-6\database.sql"
```

### Step 3: Launch in Web Browser
Open your browser and navigate to:
```
http://localhost:8080/Lab_Expeiments/
```

#### Direct Route Index:
- **Central Landing Hub:** [`http://localhost:8080/Lab_Expeiments/index.html`](http://localhost:8080/Lab_Expeiments/index.html)
- **About Platform & Contact:** [`http://localhost:8080/Lab_Expeiments/about.html`](http://localhost:8080/Lab_Expeiments/about.html)
- **Experiment 1 (Static):** [`http://localhost:8080/Lab_Expeiments/experiment-1/index.html`](http://localhost:8080/Lab_Expeiments/experiment-1/index.html)
- **Experiment 2 (BMI):** [`http://localhost:8080/Lab_Expeiments/experiment-2/index.html`](http://localhost:8080/Lab_Expeiments/experiment-2/index.html)
- **Experiment 3 (Validation):** [`http://localhost:8080/Lab_Expeiments/experiment-3/index.html`](http://localhost:8080/Lab_Expeiments/experiment-3/index.html)
- **Experiment 4 (API):** [`http://localhost:8080/Lab_Expeiments/experiment-4/index.html`](http://localhost:8080/Lab_Expeiments/experiment-4/index.html)
- **Experiment 5 (EventPulse):** [`http://localhost:8080/Lab_Expeiments/experiment-5/index.php`](http://localhost:8080/Lab_Expeiments/experiment-5/index.php)
- **Exp 5 Admin Dashboard:** [`http://localhost:8080/Lab_Expeiments/experiment-5/admin.php`](http://localhost:8080/Lab_Expeiments/experiment-5/admin.php)
- **Experiment 6 (foco Food):** [`http://localhost:8080/Lab_Expeiments/experiment-6/index.php`](http://localhost:8080/Lab_Expeiments/experiment-6/index.php)
- **Exp 6 Admin Console:** [`http://localhost:8080/Lab_Expeiments/experiment-6/admin/orders.php`](http://localhost:8080/Lab_Expeiments/experiment-6/admin/orders.php)

---

## 👨‍💻 Author & Academic Credit

- **Institution:** KPR Institute of Engineering and Technology (KPRIET)
- **Course Code:** U21CS504 — Web Technologies Laboratory
- **Designer & Architect:** Lilesh Kumar Tiwari
- **Academic Year:** 2026
- **License:** MIT License — Open for academic inspection and demonstration.
