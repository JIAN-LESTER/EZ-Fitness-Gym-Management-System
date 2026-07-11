# EZ Fitness Gym Management System

A modernized, cloud-hosted Gym Management Platform designed to replace the outdated local XAMPP system previously used by EZ Fitness Gym. Built with Laravel, Blade, TailwindCSS, and PostgreSQL, this system streamlines daily operations, enhances member experience, and introduces secure, role-based workflow automation.

---

## 🚀 Project Overview
The EZ Fitness Gym Management System is a full-featured, web-based platform engineered to address the operational issues of the old system. It modernizes workflows such as member registration, attendance tracking, payments, inventory, POS, and staff management — all within a centralized and secure web environment.

This system removes the limitations of the old single-computer setup and provides remote accessibility, improved UI/UX, accurate membership handling, and a complete administrative suite.

---

## 🧩 Key Features
1. **Role-Based Authentication** (Admin, Staff, Member)
2. **Secure Login & Logout** with proper session handling
3. **Integrated Member Registration** within the main dashboard
4. **Server-Based Membership Tracking** (accurate start/end dates)
5. **QR-Based Attendance System** with check-in & check-out logs
6. **Full Inventory Management & Stock Monitoring**
7. **Point of Sale (POS) Module** with Cash / GCash options
8. **Sales & Transaction Records** with detailed logs
9. **Comprehensive Dashboards** for Admin and Members
10. **Automated Activity Logs** for accountability and auditing

---

## 🏗️ System Architecture
- **Frontend:** Laravel Blade + TailwindCSS
- **Backend:** Laravel Framework (MVC)
- **Database:** PostgreSQL
- **Deployment:** Hostinger Cloud Hosting
- **Version Control:** Git + GitHub
- **Design Tools:** Figma for UI/UX prototyping

The application follows a **monolithic architecture**, suitable for streamlined development and straightforward deployment.

---

## 📡 Distributed Network (Simulation)
The system supports distributed hosting behavior via DNS failover.
- **Primary Server:** Handles all main traffic
- **Backup Servers:** Triggered automatically if the primary fails
- **DNS Load Handling:** Ensures continuous uptime

This design improves resiliency and minimizes service downtime.

---

## 📸 Core Modules (Screenshots in Documentation)
- Login & Registration
- Email Verification
- Member QR Code Generator
- Admin Dashboard
- User & Membership Management
- POS Interface
- Inventory Control
- Attendance Scanner
- Activity Logs
- Member Dashboard & History

Refer to the full documentation for visual references.

---

## 🔧 Installation Guide
### **1. Clone the Repository**
```bash
git clone <repository-link>
cd gym-management-system
```

### **2. Install Dependencies**
```bash
composer install
npm install
npm run build
```

### **3. Configure Environment**
```bash
cp .env.example .env
php artisan key:generate
```
Update the following in `.env`:
- `DB_CONNECTION=pgsql`
- PostgreSQL database name, user, and password
- Mail Configuration (for verification & password resets)

### **4. Run Migrations**
```bash
php artisan migrate --seed
```

### **5. Start Local Server**
```bash
php artisan serve
```

---

## 📦 Deployment
The system is deployed via **Hostinger Cloud Hosting** using:
- PHP 8+ with the `pdo_pgsql` extension
- PostgreSQL Database
- File Storage for QR Codes & Product Images

Upload the project files, configure `.env`, set storage permissions, and run migrations via SSH or PHPMyAdmin.

---

## 🧪 Testing
The system underwent:
- **Alpha Testing** (internal module validation)
- **Beta Testing** (stakeholder evaluation)
- **Network Simulation Tests** (failover & uptime validation)

---

## 💰 Cost-Benefit Snapshot
- **Initial Project Cost:** ₱225,000
- **Annual Net Benefit:** ₱430,000
- **ROI:** Recovered within Year 1
- **5-Year NPV:** ₱1.4 Million

---

## 🧑‍💻 Contributors
- EZ Fitness Gym System Development Team (IT67 — CMU)
- Development, Design, and Documentation members listed in the main report

---

## 📜 License
This project is for **academic and developmental use** under IT67 – Integrative Programming Technologies at **Central Mindanao University**.

---

## 📬 Contact
For inquiries, updates, or deployment concerns, contact the development group or the system administrator.

---

**EZ Fitness Gym Management System — Modern, Secure, and Built for Real Operations.**

---

# EZ Fitness Gym Management System — README v2 (Slightly Chaotic Edition 😎💪)

Welcome to **Version 2** of the README — the same professional system overview, but now enhanced with *exactly 5% quirkiness* for flavor. Just enough to keep readers awake at 2 AM during deployment, but not enough to get flagged by the panel. Perfect.

---

## 🚀 Project Overview
The EZ Fitness Gym Management System is a web-based solution built to replace the *ancient*, fossil-level XAMPP setup previously used by the gym. If the old system was a Nokia 3310, this one is basically a Samsung S24 Ultra with gym gains.

We modernized everything: roles, sessions, QR attendance, sales, inventory, and UI/UX — all now smooth, scalable, and blessed by Laravel.

---

## 🧩 Key Features (Now With a Hint of Personality)
1. **Role-Based Authentication** – No more shared accounts like some group project logins.
2. **Proper Logout** – You can finally switch accounts without rebooting Planet Earth.
3. **In-System Member Registration** – No more “registration outside the login void.”
4. **Accurate Membership Dates** – Now pulled from the server, not the computer that’s 7 hours behind.
5. **QR Attendance** – Because typing your name manually is so 2010.
6. **Inventory System** – Tracks products better than a tita tracks the neighborhood gossip.
7. **POS Module** – Cash? GCash? All good.
8. **Sales & Transaction Logs** – For when the owner wants receipts.
9. **Dashboards** – Beautiful analytics you can show off to investors.
10. **Activity Logs** – Someone deleted something? We know. We ALWAYS know.

---

## 🏗️ Architecture Summary
- **Laravel** – The backbone.
- **Blade + TailwindCSS** – The face.
- **MySQL** – The memory.
- **Hostinger** – The house.
- **GitHub** – The time machine.

Monolithic by design. Simple. Clean. Not microservices (yet). But definitely not a spaghetti monster.

---

## 📡 Distributed System Simulation
Yes, we simulated a multi-server failover.

If the main server dies, DNS quietly reroutes to a backup server.

Your users won’t even notice — unlike when Facebook went down for 6 hours.

---

## 🧪 Testing
- **Alpha Testing** – Dev team breaks it.
- **Beta Testing** – Stakeholders break it.
- **Simulation** – Servers break themselves.

All tests passed.

---

## 🛠️ Installation Guide (Now Human-Friendly)
Clone the repo:
```bash
git clone <repo-link>
```
Dependencies:
```bash
composer install
npm install
```
Generate magic keys:
```bash
php artisan key:generate
```
Database setup:
```bash
php artisan migrate --seed
```
Run the system:
```bash
php artisan serve
```
Deploy on Hostinger. Pray a little. Works like a charm.

---

## 💰 Cost–Benefit Highlights
- ROI in under 1 year.
- ₱430,000 annual net benefit.
- ₱1.4M projected 5-year NPV.

Basically, the system pays for itself faster than a gym newbie buys whey protein.

---

## 🧑‍💻 Contributors
A squad of CMU IT students who took a broken system and said:

> “Not on our watch.”

---

## 📜 License
For academic + development use.

If you find bugs, please report.
If you find features, congratulations — those were intentional.

---

**EZ Fitness Gym Management System — Now with 5% more personality.**
