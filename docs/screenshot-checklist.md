# WebTech Lab Hub — Submission Screenshot Checklist
**Course:** U21CS504 — Web Technologies Laboratory  
**Institution:** KPR Institute of Engineering and Technology  
**Department:** Computer Science and Engineering  

Use this standardized checklist to capture academic laboratory record screenshots for submission:

---

## 1. Central Laboratory Hub
- [ ] **SS-01: Main Hub Landing Page**
  - *URL:* `http://localhost:8080/Lab_Expeiments/index.html`
  - *Capture Area:* Hero section with custom SVG composition, course badges, and primary action buttons.
- [ ] **SS-02: Six Experiment Cards Grid**
  - *URL:* `http://localhost:8080/Lab_Expeiments/#experiments`
  - *Capture Area:* Full grid showing all 6 experiment cards with individual bespoke SVG artwork, badges, and tech tags.
- [ ] **SS-03: Curriculum Progression & Architecture**
  - *URL:* `http://localhost:8080/Lab_Expeiments/#progression`
  - *Capture Area:* Visual 6-step progression diagram from HTML/CSS to PHP/MySQL full-stack application.

---

## 2. Experiment 1: Static Web Page Design Using HTML and CSS
- [ ] **SS-04: Department Landing Page Desktop View**
  - *URL:* `http://localhost:8080/Lab_Expeiments/experiment-1/index.html`
  - *Capture Area:* Experiment meta header (Aim & Objectives accordion) and university department hero section.
- [ ] **SS-05: Information Cards & Box Model Showcase**
  - *URL:* `http://localhost:8080/Lab_Expeiments/experiment-1/index.html#labShowcase`
  - *Capture Area:* 3 information cards showing computing laboratories, research cell, and industry collaboration.
- [ ] **SS-06: Mobile / Tablet Responsive Layout**
  - *URL:* `http://localhost:8080/Lab_Expeiments/experiment-1/index.html` (Browser resized to &le; 768px)
  - *Capture Area:* Responsive single-column card stacking and mobile alignment.

---

## 3. Experiment 2: BMI Calculator Using JavaScript
- [ ] **SS-07: BMI Calculator with Valid Result (Normal Weight)**
  - *URL:* `http://localhost:8080/Lab_Expeiments/experiment-2/index.html`
  - *Input:* Weight = 70 kg, Height = 1.75 m
  - *Capture Area:* Input fields, calculated score (22.86), green "Normal Weight" badge, and spectrum scale bar.
- [ ] **SS-08: BMI Calculator with Obesity Category Result**
  - *URL:* `http://localhost:8080/Lab_Expeiments/experiment-2/index.html`
  - *Input:* Weight = 100 kg, Height = 1.65 m
  - *Capture Area:* Result panel showing 36.73 with red "Obesity" badge and health advice.
- [ ] **SS-09: BMI Calculator Validation Error State**
  - *URL:* `http://localhost:8080/Lab_Expeiments/experiment-2/index.html`
  - *Input:* Weight = -10, Height = empty
  - *Capture Area:* Red highlight border and inline validation error messages.

---

## 4. Experiment 3: User Registration Form Validation Using JavaScript
- [ ] **SS-10: Form Validation Error States**
  - *URL:* `http://localhost:8080/Lab_Expeiments/experiment-3/index.html`
  - *Action:* Press "Register Account" with empty fields or invalid mobile/email.
  - *Capture Area:* Inline red validation messages near Full Name, Email, Mobile, Password, and Terms checkbox.
- [ ] **SS-11: Successful Client Registration Submission**
  - *URL:* `http://localhost:8080/Lab_Expeiments/experiment-3/index.html`
  - *Action:* Press "Fill Demo Data" and submit.
  - *Capture Area:* Green success alert: *"Registration completed successfully."* with verified data summary breakdown.

---

## 5. Experiment 4: Implementing Web Applications with API Calls Using JavaScript
- [ ] **SS-12: Successful Country API Query (India / Japan)**
  - *URL:* `http://localhost:8080/Lab_Expeiments/experiment-4/index.html`
  - *Query:* "India"
  - *Capture Area:* Search bar and rendered result cards displaying national flag, capital, population, currency, and language.
- [ ] **SS-13: API Error / Nonexistent Search Handling**
  - *URL:* `http://localhost:8080/Lab_Expeiments/experiment-4/index.html`
  - *Query:* "AtlantisLand"
  - *Capture Area:* Alert banner showing graceful 404 error intercept without breaking the interface.

---

## 6. Experiment 5: Event Registration Form Using PHP and MySQL
- [ ] **SS-14: Event Registration Form Interface**
  - *URL:* `http://localhost:8080/Lab_Expeiments/experiment-5/index.php`
  - *Capture Area:* Event registration form with event selection dropdown and symposium statistics banner.
- [ ] **SS-15: Successful Event Registration & Participant Database Table**
  - *URL:* `http://localhost:8080/Lab_Expeiments/experiment-5/participants.php`
  - *Capture Area:* Success confirmation banner with registration ID and MySQL table showing stored participant records.

---

## 7. Experiment 6: Web Application for Food Delivery System Using PHP and MySQL
- [ ] **SS-16: Customer Food Menu Catalog**
  - *URL:* `http://localhost:8080/Lab_Expeiments/experiment-6/index.php`
  - *Capture Area:* Gourmet food cards with category filter pills, prices in INR, and "Add to Cart" buttons.
- [ ] **SS-17: Active Cart & Item Quantity Management**
  - *URL:* `http://localhost:8080/Lab_Expeiments/experiment-6/cart.php`
  - *Capture Area:* Cart table showing selected items, quantities, subtotal, and checkout action.
- [ ] **SS-18: Order Receipt & ACID Transaction Confirmation**
  - *URL:* `http://localhost:8080/Lab_Expeiments/experiment-6/order.php?id=1002&confirmed=1`
  - *Capture Area:* Success receipt with order reference number, locked line-item prices, delivery address, and status.
- [ ] **SS-19: Customer Order History**
  - *URL:* `http://localhost:8080/Lab_Expeiments/experiment-6/order-history.php`
  - *Capture Area:* Filtered user order history table.
- [ ] **SS-20: Admin Dashboard & Order Status Transition**
  - *URL:* `http://localhost:8080/Lab_Expeiments/experiment-6/admin/orders.php`
  - *Capture Area:* Administrative console showing all orders, revenue metrics, and status dropdown selector.
