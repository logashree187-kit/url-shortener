# 🔗 Lightweight PHP URL Shortening Service

A simple, production-ready URL Shortener web application built using **PHP 8**, **MySQL**, and **Bootstrap 5**. Designed without any frameworks to demonstrate core backend fundamentals, prepared statements, database CRUD operations, and REST principles.

---

## 📋 Features

- ✂️ **Create Short Links**: Converts long web addresses into 6-character unique alphanumeric short codes.
- 🔄 **Safe Redirections**: Performs fast HTTP 302 redirections while dynamically incrementing click metrics.
- ✏️ **Update Destinations**: Modify the target URL of any existing short code at any time.
- 🗑️ **Delete Short Codes**: Remove links that are no longer needed.
- 📊 **Track Click Analytics**: View total hits and timestamps for every generated link.
- 🛡️ **SQL Injection Safe**: Uses MySQLi parameterized prepared statements for all database operations.
- 📱 **Responsive UI**: Clean Bootstrap 5 dashboard matching desktop and mobile viewports.

---

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, Bootstrap 5.3
- **Backend**: Native PHP 8+
- **Database**: MySQL 8.x
- **Server Environment**: Apache via XAMPP
- **Testing Tools**: Chrome Browser, Postman

---

## ⚙️ System Requirements

- Windows 10/11, macOS, or Linux
- XAMPP (Apache + PHP 8.0 or higher + MySQL)
- Web Browser (Chrome, Firefox, Edge)
- Postman (for API testing)
- Git (for version control)

---

## 🚀 Installation & XAMPP Setup

### 1. Download and Place Project
1. Install [XAMPP](https://www.apachefriends.org/).
2. Open the directory: `C:\xampp\htdocs\`.
3. Create a folder named `url-shortener`.
4. Copy all project files into `C:\xampp\htdocs\url-shortener\`.

### 2. Start Servers
1. Open **XAMPP Control Panel**.
2. Click **Start** next to **Apache**.
3. Click **Start** next to **MySQL**.

### 3. Database Setup
1. In your browser, open `http://localhost/phpmyadmin/`.
2. Click the **SQL** tab at the top.
3. Paste and run the following query:

```sql
CREATE DATABASE IF NOT EXISTS url_shortener;

USE url_shortener;

CREATE TABLE IF NOT EXISTS urls (
    id INT PRIMARY KEY AUTO_INCREMENT,
    original_url TEXT NOT NULL,
    short_code VARCHAR(20) UNIQUE NOT NULL,
    clicks INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;