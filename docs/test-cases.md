# Web Technologies Laboratory Hub — Test Cases & QA Matrix
**Course:** U21CS504 — Web Technologies Laboratory  
**Target Environment:** Windows 11 · Apache 2.4 (Port 8080) · MySQL 8.0 (Port 3306) · PHP 8.2  

---

## QA Execution Matrix

| Test ID | Module | Scenario / Input | Expected Result | Actual Result | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-01** | Hub Home | Direct load of `http://localhost:8080/Lab_Expeiments/` | Editorial dashboard renders; all 6 experiment cards display with custom SVG illustrations; no console errors. | HTTP 200 OK; cards rendered; responsive menu functional. | **PASSED** |
| **TC-02** | Hub Nav | Click "Open Experiment" on any card | Directly opens the target experiment; breadcrumbs allow returning to Hub. | Seamless bidirectional routing across all 6 experiments. | **PASSED** |
| **TC-03** | Exp 1 (Static) | View Department Landing Page on desktop & mobile | Semantic tags (`<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<footer>`) render; box model margins and borders align. | Layout passes responsive viewport checks; all 4 links anchor correctly. | **PASSED** |
| **TC-04** | Exp 2 (BMI) | Normal Weight: Weight = 70 kg, Height = 1.75 m | Computes $\text{BMI} = 22.86$; displays green "Normal Weight" badge. | Correct formula; display updates dynamically without reload. | **PASSED** |
| **TC-05** | Exp 2 (BMI) | Underweight: Weight = 45 kg, Height = 1.70 m | Computes $\text{BMI} = 15.57$; displays blue "Underweight" badge. | Correct category boundary evaluated. | **PASSED** |
| **TC-06** | Exp 2 (BMI) | Overweight: Weight = 80 kg, Height = 1.70 m | Computes $\text{BMI} = 27.68$; displays amber "Overweight" badge. | Correct category boundary evaluated. | **PASSED** |
| **TC-07** | Exp 2 (BMI) | Obesity: Weight = 100 kg, Height = 1.65 m | Computes $\text{BMI} = 36.73$; displays red "Obesity" badge. | Correct category boundary evaluated. | **PASSED** |
| **TC-08** | Exp 2 (BMI) | Invalid inputs (empty, 0, negative, non-numeric) | Form submission blocked; inline error messages highlight offending inputs. | Blocked; error messages displayed cleanly. | **PASSED** |
| **TC-09** | Exp 3 (RegEx) | Blank submission | All required fields display inline error messages; form submission prevented. | All 9 required field errors rendered with red focus rings. | **PASSED** |
| **TC-10** | Exp 3 (RegEx) | Invalid email format (`student@domain`) | Flags email error: "Please enter a valid email format". | Blocked; specific RFC syntax message shown. | **PASSED** |
| **TC-11** | Exp 3 (RegEx) | Mobile number starting with 3 or &lt; 10 digits | Rejects entry; requires 10-digit number starting with 6, 7, 8, or 9. | Pattern matched against `/^[6-9]\d{9}$/`. | **PASSED** |
| **TC-12** | Exp 3 (RegEx) | Password mismatch (`pass123` vs `pass124`) | Flags confirm password mismatch inline error. | Blocked; password mismatch error shown. | **PASSED** |
| **TC-13** | Exp 3 (RegEx) | Valid submission | Displays green success banner: "Registration completed successfully." with payload breakdown. | Success banner displayed smoothly. | **PASSED** |
| **TC-14** | Exp 4 (API) | Search "India" | Asynchronously fetches live REST Countries JSON; renders capital, flag, currencies, population. | Retrieved 200 OK; cards rendered with flags and currency data. | **PASSED** |
| **TC-15** | Exp 4 (API) | Search nonexistent country "Atlantis123" | Intercepts 404 response; displays clean error: "No country found matching 'Atlantis123'". | Displayed user-friendly error without app crash. | **PASSED** |
| **TC-16** | Exp 4 (API) | Rapid successive searches | Prior in-flight HTTP requests aborted using `AbortController`; newer results never overwritten. | Race condition test passed; no stale responses. | **PASSED** |
| **TC-17** | Exp 5 (PHP/DB) | Submit valid event registration form | Data inserted into `event_management.event_registrations`; redirects to `participants.php` with highlight. | Inserted record with auto-increment ID; record verified in MySQL. | **PASSED** |
| **TC-18** | Exp 5 (PHP/DB) | Duplicate registration (same email & event) | Server blocks duplicate and displays error notification. | Caught by unique check; clean error message rendered. | **PASSED** |
| **TC-19** | Exp 5 (PHP/DB) | Server-side validation failure (empty name/email) | Form repopulates valid fields and presents field-specific errors. | Form preserved via session; errors highlighted. | **PASSED** |
| **TC-20** | Exp 6 (FullStack) | Customer login with valid credentials | Verifies bcrypt hash; creates session; regenerates session ID. | Signed in as `student@kpriet.ac.in`; user name in navbar. | **PASSED** |
| **TC-21** | Exp 6 (FullStack) | Invalid login credentials | Rejects attempt; displays "Invalid email address or password". | Blocked; error rendered cleanly. | **PASSED** |
| **TC-22** | Exp 6 (FullStack) | Add items to cart & update quantities | Session cart updates; server queries database prices; calculates subtotal and grand total. | Accurately computes totals based on MySQL prices. | **PASSED** |
| **TC-23** | Exp 6 (FullStack) | Checkout & Order Placement | Executes within `$pdo->beginTransaction()`; inserts `orders` & `order_items`; commits and clears cart. | Order #1002 generated; items saved with locked price; cart cleared. | **PASSED** |
| **TC-24** | Exp 6 (FullStack) | Customer order history isolation | Student Praveen can only view orders with `user_id = 2`. | Verified: `WHERE user_id = :uid` strictly prevents horizontal escalation. | **PASSED** |
| **TC-25** | Exp 6 (FullStack) | Unauthorized admin access | Direct unauthenticated request to `admin/index.php`. | 302 Redirect to `login.php` with authorization warning. | **PASSED** |
| **TC-26** | Exp 6 (FullStack) | Admin order status transition | Admin updates order status from Pending to Confivered / Delivered. | Database updated via prepared statement; status pill changes color. | **PASSED** |
| **TC-27** | Exp 6 (FullStack) | Admin food item creation & toggle | Admin adds item or toggles availability. | Reflected immediately in customer menu. | **PASSED** |
