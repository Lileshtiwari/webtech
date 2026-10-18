# Web Technologies Laboratory Hub — Implementation Architecture
**Institution:** KPR Institute of Engineering and Technology  
**Department:** Computer Science and Engineering  
**Course Code & Title:** U21CS504 — Web Technologies Laboratory (Nov / Dec 2026)  
**Scope:** Exercises 1 through 6  

---

## 1. System Overview & Aesthetic Philosophy
The **WebTech Lab Hub** is an integrated educational web platform designed to present, execute, and verify Exercises 1 through 6 of the U21CS504 curriculum. Rather than disconnected laboratory scripts, the platform adopts an editorial dashboard aesthetic inspired by modern design engineering:
- **Palette:** Soft neutral off-white surface (`#F8FAFC`), deep charcoal and navy typography (`#0F172A`, `#334155`), and refined indigo accents (`#4F46E5`, `#4338CA`).
- **Icon & Illustration Policy:** Strict prohibition of third-party icon libraries (Lucide, FontAwesome, Bootstrap Icons) and emoji characters. 100% of interface controls, logos, cards, and diagrams use bespoke scalable vector graphics (SVG).
- **Architecture:** Decoupled experiment folders sharing a master CSS design system (`assets/css/design-system.css`) with consistent breadcrumb navigation back to the root Hub.

---

## 2. Detailed Experiment Architectures

### Exercise 01: Static Web Page Design Using HTML and CSS
- **Application Concept:** Department of Computer Science and Engineering Academic Landing Page.
- **Semantic Structure:** Implemented using `<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, and `<footer>`.
- **CSS Selectors Demonstrated:**
  1. *Element Selectors:* `header`, `nav`, `section`, `p`, `footer`.
  2. *Class Selectors:* `.dept-card`, `.dept-brand`, `.dept-hero-badge`.
  3. *ID Selectors:* `#deptHero`, `#deptHeader`, `#deptAbout`, `#labShowcase`, `#deptFooter`.
  4. *Group Selectors:* `.dept-showcase h2, .dept-showcase h3, .dept-showcase h4`.
  5. *Descendant Selectors:* `.dept-nav ul li a`.
- **CSS Box Model:** Strict dimensioning across margin, border (`1px solid #E2E8F0`), padding, border-radius (`14px`), and elevation shadows (`box-shadow`).
- **Responsive Technique:** CSS Grid (`grid-template-columns: repeat(3, 1fr)`) with mobile media queries collapsing into single-column layouts below `768px`.

### Exercise 02: BMI Calculator Using JavaScript
- **Application Concept:** Interactive clinical Body Mass Index calculator.
- **Formula:** $\text{BMI} = \frac{\text{Weight (kg)}}{[\text{Height (m)}]^2}$
- **Logic & DOM Updating:** Implemented in `calculateBMI()` in `script.js`.
  - Values extracted from `#weight` and `#height` input nodes.
  - Formatted to two decimal places using `Number.prototype.toFixed(2)`.
  - Conditional health classification thresholds:
    - $\text{BMI} < 18.5$: **Underweight**
    - $18.5 \le \text{BMI} < 25.0$: **Normal Weight**
    - $25.0 \le \text{BMI} < 30.0$: **Overweight**
    - $\text{BMI} \ge 30.0$: **Obesity**
- **Safety & Disclaimers:** Prevents division by zero, rejects negative/zero inputs, and clearly provides an educational screening notice.

### Exercise 03: User Registration Form Validation Using JavaScript
- **Application Concept:** Student account registration portal with strict client-side evaluation.
- **Validation Rules & Regular Expressions:**
  - *Full Name:* `/^[a-zA-Z\s]{2,50}$/` (alphabets and spaces, min 2 chars).
  - *Email Address:* `/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/` (RFC compliance).
  - *Mobile Number:* `/^[6-9]\d{9}$/` (10-digit Indian telecom standard).
  - *Password Strength:* `/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/` (minimum 8 characters, alphanumeric).
  - *Confirm Password:* Strict string equality check (`===`).
  - *Gender, DOB, Address, Terms:* Mandatory completion and past date check.
- **UI States:** Inline error messages near each input control, interactive password toggle, and successful submission summary panel.

### Exercise 04: Implementing Web Applications with API Calls Using JavaScript
- **Application Concept:** Global Country Explorer.
- **Target API:** REST Countries API (`https://restcountries.com/v3.1/name/{name}`).
- **Asynchronous Pipeline:** 
  - Uses native JavaScript `fetch()` API with `async`/Promises.
  - Implements `AbortController` to cancel stale in-flight HTTP requests during rapid typing.
  - Safe DOM generation via `document.createElement()` and `textContent` to eliminate cross-site scripting (XSS) risks.
  - Embedded offline JSON fallback dictionary for isolated laboratory examination centers.

### Exercise 05: Event Registration Form Using PHP and MySQL
- **Application Concept:** Technical symposium and workshop registration platform.
- **Database:** `event_management` | **Table:** `event_registrations`.
- **Backend Flow (`register.php`):**
  - POST-only request acceptance.
  - Server-side field sanitization and regex validation.
  - Duplicate check via `SELECT registration_id FROM event_registrations WHERE email = :email AND event_name = :event`.
  - Parameterized `INSERT` query via PHP PDO prepared statements.
- **Record Retrieval (`participants.php`):**
  - Dynamic `SELECT` query with search filters across name, institution, and category.
  - Live participant counters and output escaping via `htmlspecialchars()`.

### Exercise 06: Web Application for Food Delivery System Using PHP and MySQL
- **Application Concept:** Full-stack campus food ordering e-commerce platform.
- **Database:** `food_delivery` with four relational tables:
  1. `users`: Stores customer and administrator profiles with passwords hashed using `password_hash($pass, PASSWORD_BCRYPT)`.
  2. `food_items`: Menu catalog with pricing and stock availability flags.
  3. `orders`: High-level order details with foreign key linkage to `users`.
  4. `order_items`: Line-item breakdown storing locked purchase price per item.
- **Security & Concurrency:**
  - Session regeneration via `session_regenerate_id(true)` upon login.
  - Server-side price calculation: Client totals are never trusted; subtotals and grand totals are computed strictly from verified database records.
  - Transactional commits: All order placements execute within `$pdo->beginTransaction()` and `$pdo->commit()`, rolling back completely if any menu item is invalid or out of stock.
  - Role-based authorization: Dedicated admin console (`admin/`) protected by session role inspection.
