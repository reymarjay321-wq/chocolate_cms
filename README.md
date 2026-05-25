# 🍫 Chocolate Management System

A premium, fully-featured inventory management system for chocolate products.
Built with **Core PHP**, **MySQL**, **Bootstrap 5**, **JavaScript**, and **SweetAlert2**.

---

## ⚡ Quick Setup (XAMPP)

### 1. Copy Project
Place the `chocolate_cms` folder into your XAMPP `htdocs` directory:
```
C:\xampp\htdocs\chocolate_cms\
```

### 2. Start XAMPP
- Start **Apache** and **MySQL** from the XAMPP Control Panel.

### 3. Database Setup (Choose ONE method)

**Option A — Auto-Setup (Recommended)**
Simply open the site in your browser. The `db.php` file will automatically:
- Create the `chocolate_cms` database
- Create the `chocolates` table
- Insert 10 sample chocolate records

**Option B — Manual SQL Import**
1. Open `http://localhost/phpmyadmin`
2. Click **Import**
3. Select `chocolate_cms.sql` from the project folder
4. Click **Go**

### 4. Open the System
```
http://localhost/chocolate_cms/
```

---

## 🗂 File Structure

```
chocolate_cms/
├── index.php           ← Dashboard with stats & product table
├── add.php             ← Add new chocolate form
├── edit.php            ← Edit existing chocolate
├── delete.php          ← Delete single chocolate
├── delete_all.php      ← Delete all chocolates
├── about.php           ← About the System page
├── db.php              ← Database connection & auto-setup
├── chocolate_cms.sql   ← Manual SQL import file
│
├── components/
│   ├── header.php      ← Shared header, sidebar, navbar
│   └── footer.php      ← Shared footer with scripts
│
├── assets/
│   ├── css/
│   │   └── style.css   ← All styles (chocolate theme)
│   └── js/
│       └── main.js     ← All JavaScript interactions
│
└── uploads/            ← Uploaded chocolate images
    └── .htaccess       ← Prevents PHP execution in uploads
```

---

## 🎨 Design Features

- **Dark Chocolate Theme** — rich browns, amber, and gold palette
- **Glassmorphism Cards** — frosted glass effect throughout
- **Smooth Animations** — row reveals, hover effects, stat counters
- **Dark / Light Mode** — toggle in the topbar, persists via localStorage
- **Mobile Responsive** — collapsible sidebar, stacked layouts on mobile
- **SweetAlert2** — premium delete confirmations with safety input
- **Toast Notifications** — top-right success/error messages

---

## 🛡 Security

- All database queries use **MySQLi Prepared Statements**
- Image uploads are validated by extension and size (max 5 MB)
- Uploaded files are sanitized with unique names via `uniqid()`
- The `uploads/` folder has `.htaccess` blocking PHP execution

---

## 📦 Technologies

| Technology    | Version   | Purpose                    |
|---------------|-----------|----------------------------|
| PHP           | 8.x       | Backend logic              |
| MySQL         | 8.x       | Database                   |
| Bootstrap     | 5.3.3     | Responsive grid & utilities|
| Font Awesome  | 6.5.0     | Icons                      |
| SweetAlert2   | 11        | Delete confirmations       |
| Google Fonts  | —         | Playfair Display + DM Sans |
| Vanilla JS    | ES6+      | Search, sort, pagination   |

---

## 👤 Creator

**Created By:** Your Name Here
**Course:** Bachelor of Science in Information Technology
**Section:** BSIT-3A
**School:** Your University Name
**Year:** 2026

---

**Chocolate Management System © 2026**
