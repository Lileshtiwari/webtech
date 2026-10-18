# Web Technologies Laboratory (U21CS504)
## Product & Academic Experiment Documentation
**Institution:** KPR Institute of Engineering and Technology (KPRIET)  
**Department:** Computer Science and Engineering  
**Curriculum Scope:** Experiments 1 through 6  
**Author / Architect:** Lilesh Kumar Tiwari  

---

# Experiment 1: Static Web Page Design Using HTML and CSS

### 1. Aim
To design and develop a responsive, multi-section static web page using semantic HTML5 and modern CSS3, applying CSS box-model principles, element/class/ID/group/descendant selectors, flexbox and grid layouts, and responsive viewport media queries.

### 2. Step-by-Step Procedure
1. **Directory Setup:** Create a modular project structure under `experiment-1/` containing `index.html`, `style.css`, and an `images/` directory for SVG assets.
2. **Semantic Document Structure:** Construct the HTML5 skeleton using strictly semantic landmark elements:
   - `<header>`: Platform header with institutional branding and logo.
   - `<nav>`: Accessible navigation bar containing at least four internal anchor links.
   - `<main>`: Central content wrapper enclosing distinct thematic sections.
   - `<section>` & `<article>`: Content containers for academic programs, faculty profiles, research highlights, and lab infrastructure.
   - `<footer>`: Closing section with copyright details, quick links, and developer attribution.
3. **Design System & CSS Styling:**
   - Define CSS custom variables (`:root`) for color palettes, elevation shadows, and standard border radii.
   - Apply diverse CSS selectors:
     - *Element:* `header`, `nav`, `section`, `p`, `footer`
     - *Class:* `.dept-card`, `.dept-brand`, `.badge`
     - *ID:* `#deptHero`, `#deptAbout`, `#labShowcase`, `#deptFooter`
     - *Group:* `h1, h2, h3, h4`
     - *Descendant:* `.dept-nav ul li a`
4. **Box Model Implementation:** Apply explicit margin, border (`1px solid #E2E8F0`), padding, and border-radius (`14px`) properties to card components to maintain consistent optical rhythm.
5. **Responsive Grid Layout:** Implement CSS Grid (`grid-template-columns: repeat(3, 1fr)`) and Flexbox layouts. Add media queries (`@media (max-width: 768px)`) to collapse multi-column sections into single-column layouts for mobile viewports.
6. **Verification & Testing:** Validate HTML5 compliance using W3C standards and test responsiveness across desktop, tablet, and mobile breakpoints.

### 3. Result
A responsive static department showcase portal was designed and rendered. Semantic HTML5 tags and CSS box-model properties were verified, ensuring layout stability and aesthetic consistency across all screen resolutions.

---

# Experiment 2: Clinical BMI Calculator Using JavaScript

### 1. Aim
To design and implement an interactive client-side Body Mass Index (BMI) calculator web application using vanilla JavaScript, DOM event handling, mathematical formulas, conditional branching for WHO health categories, and dynamic UI updates without page reloading.

### 2. Step-by-Step Procedure
1. **User Interface Construction:** Build a clean medical interface (`experiment-2/index.html`) featuring input controls for Body Weight (kg/lbs), Height (cm/m/ft), and a calculation action button.
2. **Mathematical Formulation:** Implement the standard World Health Organization (WHO) metric Body Mass Index formula in JavaScript:
   $$\text{BMI} = \frac{\text{Weight in Kilograms}}{(\text{Height in Meters})^2}$$
3. **Input Sanitization & Validation:** Write input verification logic in `script.js` to reject empty, non-numeric, negative, or zero inputs, presenting contextual warning alerts when invalid data is entered.
4. **Calculation & Classification Engine:** In `calculateBMI()`:
   - Extract numerical values from `#weight` and `#height` DOM input elements.
   - Compute BMI value and format to two decimal places using `.toFixed(2)`.
   - Apply conditional branching to determine health status:
     - $\text{BMI} < 18.5$: **Underweight** (Risk of nutritional deficiency)
     - $18.5 \le \text{BMI} < 25.0$: **Normal Weight** (Optimal health range)
     - $25.0 \le \text{BMI} < 30.0$: **Overweight** (Increased cardiovascular risk)
     - $\text{BMI} \ge 30.0$: **Obesity** (High clinical health risk)
5. **Dynamic DOM Manipulation:** Update the document object model in real-time by:
   - Injecting the calculated numerical BMI score into the display badge.
   - Adjusting color-coded status badges (Blue, Green, Amber, Rose).
   - Animating an analog visual gauge needle based on the calculated BMI score.
   - Displaying contextual dietary and exercise recommendations.
6. **Verification & Testing:** Execute boundary test cases (e.g., Weight = 70 kg, Height = 1.75 m $\rightarrow$ BMI = 22.86 Normal Weight) and verify instant UI updates without page refreshes.

### 3. Result
The interactive BMI calculator computes Body Mass Index with mathematical precision. Dynamic DOM updates, color-coded WHO classifications, and animated gauge feedback were successfully demonstrated without full page reloads.

---

# Experiment 3: User Registration Form Validation Using JavaScript

### 1. Aim
To design and develop a comprehensive user registration web form implementing robust client-side validation using JavaScript Regular Expressions (RegEx), event listeners, dynamic DOM manipulation, and interactive visual feedback states.

### 2. Step-by-Step Procedure
1. **Form Layout Design:** Construct an accessible registration form in `experiment-3/index.html` with fields for:
   - Full Name
   - Email Address
   - 10-Digit Mobile Number
   - Password and Confirm Password
   - Gender Selection (Radio buttons)
   - Date of Birth (Past date picker)
   - Residential Address
   - Terms & Conditions acceptance checkbox
2. **Regular Expression Definition:** Formulate strict regular expression patterns in `validation.js`:
   - *Full Name:* `/^[a-zA-Z\s\.]{2,50}$/` (alphabets, spaces, dots; 2–50 characters)
   - *Email Address:* `/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/` (RFC compliance)
   - *Mobile Number:* `/^[6-9]\d{9}$/` (10-digit Indian telecommunications format starting with 6–9)
   - *Password Strength:* `/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/` (minimum 8 characters with at least one letter, one number, and one special character)
3. **Event Listener Attachment:** Attach `blur`, `input`, and `submit` event listeners to form controls for real-time validation.
4. **Password Equality Verification:** Compare the password and confirmation password values using strict string equality (`password === confirmPassword`).
5. **Visual Feedback & State Management:**
   - Display specific inline error messages underneath offending input fields.
   - Apply dynamic CSS classes: red border and icon on invalid input, green ring on valid input.
   - Implement an interactive eye-toggle icon to show/hide password text.
6. **Form Interception:** Prevent default submission (`e.preventDefault()`) when validation errors are present. When all fields satisfy the constraints, render a submission summary modal detailing registered information.

### 3. Result
The user registration validation suite successfully intercepts invalid input submissions, enforces RegEx constraints in real time, provides clear visual feedback, and only permits valid data to be submitted.

---

# Experiment 4: Web Applications with Live API Calls Using JavaScript

### 1. Aim
To design and implement a dynamic web application that communicates with an external REST API using JavaScript's native Fetch API, handling asynchronous JSON data streams, request debouncing, race-condition mitigation via `AbortController`, and regional filtering.

### 2. Step-by-Step Procedure
1. **Explorer Interface Design:** Create a global demographic explorer in `experiment-4/index.html` with:
   - Search text input with live query listening.
   - Region filter dropdown (All, Africa, Americas, Asia, Europe, Oceania).
   - Population sort dropdown (Ascending, Descending, Alphabetical).
   - Card grid container and modal dialog for detailed view.
2. **Asynchronous Fetch Pipeline:** Implement `fetchCountries(query)` using `async`/`await` and JavaScript Promises to query the REST Countries v3.1 API:
   `https://restcountries.com/v3.1/name/{name}` or `https://restcountries.com/v3.1/all`.
3. **AbortController Integration:** Instantiate `AbortController` before each fetch call. Abort pending in-flight requests when a user types rapidly to eliminate race conditions and prevent stale asynchronous responses from overriding newer ones.
4. **DOM Construction & XSS Prevention:** Parse the returned JSON response. Use `document.createElement()` and `textContent` rather than raw HTML injection to safely render country cards showing flags, capital cities, population, currencies, and languages.
5. **Interactive Modal Inspection:** Attach click event listeners to country cards to open a detailed modal with border countries, timezones, and regional maps.
6. **Offline Fallback Architecture:** Embed an offline local JSON fallback cache so the application remains fully operable in isolated, intranet-only laboratory examination environments.

### 3. Result
The global country explorer fetches, parses, and renders live REST API JSON data asynchronously. Fast typing search debouncing, HTTP request cancellation via `AbortController`, and offline fallback recovery functioned with zero application crashes.

---

# Experiment 5: Event Registration Form Using PHP and MySQL

### 1. Aim
To implement a full-stack event registration and attendee management system using PHP 8 and MySQL, demonstrating server-side validation, relational database storage, PDO prepared statements, pass tier selection, password-protected admin portal, dynamic record filtering, and CSV/PDF export.

### 2. Step-by-Step Procedure
1. **Database Schema Creation:** Create database `event_management` and relational table `event_registrations` in MySQL:
   - Fields: `registration_id` (PK, Auto-Increment), `participant_name`, `email`, `mobile`, `institution`, `event_name`, `category` (Pass Tier), `participants`, and `registration_date`.
   - Add constraints: `chk_participants` ($1 \le \text{count} \le 20$) and unique index on `(email, event_name)`.
2. **Modular Database Connector:** Create `config.php` and `db.php` initializing a PHP Data Objects (PDO) instance with `ATTR_ERRMODE => ERRMODE_EXCEPTION` and `ATTR_DEFAULT_FETCH_MODE => FETCH_ASSOC`.
3. **Summit Pass Registration (`index.php`):** Design the public event portal showcasing four distinct pass tiers:
   - **Normal Pass ($99)**
   - **Gold Pass ($249)**
   - **Diamond Pass ($499)**
   - **Kohinoor Pass ($899)**
4. **Server-Side Validation & Insertion (`register.php`):**
   - Accept only `POST` requests; sanitize inputs using `trim()` and `htmlspecialchars()`.
   - Validate field lengths, RFC email formats, and 10-digit mobile numbers.
   - Execute a duplicate check query: `SELECT registration_id FROM event_registrations WHERE email = :email AND event_name = :event`.
   - Execute parameterized `INSERT INTO event_registrations` query using prepared statements to prevent SQL Injection.
5. **Password-Gated Admin Portal (`admin.php`):**
   - Implement session-based authentication protecting the attendee management dashboard.
   - Default credentials: Username `admin`, Password `eventpulse@2026`.
   - Implement dynamic SQL filtering by pass tier using exact and prefix matching.
   - Integrate instant CSV file download via `php://output` stream.
   - Integrate printer-friendly CSS media queries (`@media print`) for clean PDF generation.

### 3. Result
Delegates successfully registered for chosen pass tiers and were securely stored in the MySQL database. The password-protected admin portal filtered applicants across all four pass tiers, exported attendee lists to CSV, and generated formatted PDF directories.

---

# Experiment 6: Web Application for Food Delivery System Using PHP and MySQL

### 1. Aim
To architect and implement a multi-page, full-stack transactional online food delivery web application using PHP 8 and MySQL, featuring multi-role authentication (Bcrypt hashing), session cart state management, ACID transactional order placement, customer order history isolation, and an administrator order fulfillment dashboard.

### 2. Step-by-Step Procedure
1. **Relational Database Design:** Architect the `food_delivery` database across four relational tables:
   - `users`: User profiles with `id`, `name`, `email`, `password` (VARCHAR 255), `role` (`user` / `admin`).
   - `food_items`: Menu catalog with `item_id`, `name`, `category`, `price`, `description`, `image_url`, `is_available`.
   - `orders`: High-level order records with `order_id`, `user_id` (FK), `total_amount`, `order_status`, `created_at`.
   - `order_items`: Line-item details with `order_id` (FK), `item_id` (FK), `quantity`, `price_at_purchase`.
2. **Secure User Authentication (`login.php`, `register.php`, `logout.php`):**
   - Hash customer and administrator passwords using PHP's native `password_hash($pass, PASSWORD_BCRYPT)`.
   - Authenticate users using `password_verify()`.
   - Regenerate session identifiers on authentication (`session_regenerate_id(true)`) to mitigate session fixation attacks.
3. **Catalog & Interactive Session Cart (`index.php`, `cart.php`):**
   - Query available food items from `food_items` table and display category filter tabs.
   - Store cart items in `$_SESSION['cart']`.
   - Compute subtotal, taxes, delivery fee, and grand total strictly on the server by querying verified prices from MySQL (never trusting client-supplied prices).
4. **ACID Transactional Checkout (`checkout.php` / `order.php`):**
   - Wrap order placement within an ACID database transaction:
     ```php
     $pdo->beginTransaction();
     // Insert into `orders`
     // Insert items into `order_items` with snapshot unit pricing
     $pdo->commit();
     ```
   - Rollback completely (`$pdo->rollBack()`) if any database error occurs during the transaction.
   - Clear session cart upon successful commit.
5. **Customer Order Isolation (`order-history.php`):**
   - Query orders filtered strictly by the logged-in customer's user ID (`WHERE user_id = :uid`), preventing unauthorized horizontal privilege escalation.
6. **Administrator Dispatch Console (`admin/orders.php`, `admin/items.php`):**
   - Restrict access to users with `role === 'admin'`.
   - Allow administrators to update order fulfillment states (*Pending $\rightarrow$ Preparing $\rightarrow$ Dispatched $\rightarrow$ Delivered*).
   - Allow administrators to add, edit, or toggle the availability of menu items.

### 3. Result
The full-stack food delivery application provides end-to-end functionality: customer account registration with Bcrypt encryption, menu browsing, persistent session cart, atomic order placement via ACID database transactions, customer order isolation, and real-time administrative status transitions.
