# User Management System

Complete PHP/MySQL system with authentication, CRUD, role-based access, and profile picture upload.

## Features
- User registration (password hashed)
- Login/Logout with sessions
- Role-based: Admin & User
- Admin: Add/Edit/Delete users (with confirmation)
- Profile: View/Edit, upload profile picture (validation: JPG/PNG/GIF, max 2MB)
- Security: Prepared statements, XSS protection, password_hash

## Installation
1. Import `database.sql` into MySQL
2. Update `config/db.php` with your DB credentials
3. Create `uploads/` folder (permissions 755)
4. Place a default avatar as `assets/images/default.png`
5. Run on localhost

## Default Admin
- Username: admin
- Password: admin123

## Folder Structure
├── assets/ (css, js, images)
├── config/db.php
├── inc/ (header, footer, functions, auth)
├── admin/ (users, add_user, edit_user, delete_user)
├── uploads/
├── index.php, register.php, login.php, logout.php, dashboard.php, profile.php, edit_profile.php
└── database.sql