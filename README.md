# Task 4: Food Ordering System 🍕

A full-stack PHP-based food ordering and delivery management system with user authentication, shopping cart functionality, order tracking, and admin dashboard.

## 🌟 Features

### Customer Features
- **User Authentication**: Register, login, and secure logout
- **Forgot Password**: Professional password reset via email with secure token-based links
- **Shopping Cart**: Add/remove items, update quantities, real-time cart updates
- **Checkout**: Easy checkout process with order placement
- **Order Tracking**: View order history and current order status
- **User Profile**: Manage account information
- **AI Insights**: AI-powered food recommendations based on user preferences

### Admin Features
- **Admin Dashboard**: Overview of all orders and users
- **User Management**: View and manage registered users
- **Order Management**: Track, process, and manage all orders
- **Menu Management**: Add, update, and manage food items
- **Admin Authentication**: Secure admin login with role-based access

## 🔧 Tech Stack

- **Backend**: PHP 7+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Email Service**: Mailtrap SMTP for password reset emails
- **Session Management**: PHP Sessions for user authentication

## 📋 Project Structure

```
task4/
├── config/
│   └── db.php                 # Database configuration
├── inc/
│   ├── auth.php              # Authentication utilities
│   ├── functions.php         # Helper functions
│   ├── header.php            # Common header component
│   └── footer.php            # Common footer component
├── api/
│   ├── add_to_cart.php      # Cart operations API
│   ├── remove_from_cart.php
│   ├── update_cart.php
│   └── add_to_log.php       # Logging API
├── admin/
│   ├── dashboard.php         # Admin dashboard
│   ├── login.php            # Admin login
│   ├── menu.php             # Menu management
│   ├── orders.php           # Order management
│   └── users.php            # User management
├── customer/
│   ├── index.php            # Customer home
│   ├── cart.php             # Shopping cart
│   ├── checkout.php         # Checkout page
│   ├── orders.php           # Order history
│   ├── profile.php          # User profile
│   └── ai_insights.php      # AI recommendations
├── assets/
│   ├── css/
│   │   └── style.css        # Global styles
│   ├── js/
│   │   └── main.js          # JavaScript functions
│   └── uploads/             # User uploads directory
├── index.php                 # Home page
├── register.php             # User registration
├── login.php                # User login
├── logout.php               # User logout
├── forgot_password.php      # ✨ Password reset (FIXED)
├── demo.php                 # Demo page
└── SETUP_GUIDE.md           # Setup instructions
```

## 🚀 Installation & Setup

### Prerequisites
- XAMPP or similar local server (Apache, MySQL, PHP)
- PHP 7.0+
- MySQL 5.7+

### Steps

1. **Clone/Download the project**
   ```bash
   cd C:\xampp\htdocs\task4
   ```

2. **Create Database**
   ```bash
   mysql -u root
   CREATE DATABASE food_ordering;
   ```

3. **Import Database Schema**
   - Run the setup script in the application or manually import tables

4. **Configure Database Connection**
   - Edit `config/db.php` with your database credentials:
   ```php
   $host = 'localhost';
   $dbname = 'food_ordering';
   $username = 'root';
   $password = '';
   ```

5. **Configure Email Service (Mailtrap)**
   - Update SMTP credentials in `forgot_password.php`:
   ```php
   define('SMTP_HOST', 'sandbox.smtp.mailtrap.io');
   define('SMTP_USER', 'your_mailtrap_user');
   define('SMTP_PASS', 'your_mailtrap_password');
   ```

6. **Start Apache & MySQL**
   - Open XAMPP Control Panel and start Apache and MySQL

7. **Access the Application**
   - Open browser: `http://localhost/task4`

## 🔐 Key Features Explained

### Forgot Password System (✨ Fixed)
The forgot password feature provides:
- **Email Validation**: Validates email format before processing
- **Secure Token Generation**: Uses `random_bytes()` for cryptographically secure tokens
- **Time-Limited Links**: Reset links expire after 1 hour
- **Professional Email Template**: HTML-formatted emails with branded design
- **Absolute URL Generation**: Works correctly with HTTP and HTTPS
- **Security Best Practices**: Doesn't reveal if email exists in system

**Flow:**
1. User enters email on forgot password page
2. System generates unique reset token
3. Professional HTML email sent to registered email
4. User clicks reset link in email
5. User enters new password
6. Password is updated and link invalidated

### User Authentication
- Passwords hashed with PHP's `PASSWORD_DEFAULT` (bcrypt)
- Session-based authentication
- Role-based access control (Customer/Admin)
- Secure logout functionality

### Shopping Cart
- Real-time cart updates via AJAX
- Session-based cart storage
- Add/remove/update item quantities
- Cart persistence during session

## 🗄️ Database Schema

### users table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    reset_token VARCHAR(255) NULL,
    reset_expires DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### orders table
```sql
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    status ENUM('pending', 'confirmed', 'delivered', 'cancelled') DEFAULT 'pending',
    total DECIMAL(10, 2) NOT NULL,
    items JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## 🧪 Testing

### Test Account (Default)
- **Admin Account**: 
  - Email: `admin@example.com`
  - Password: `admin123` (varies by installation)

- **Customer Account**: 
  - Register a new account or use existing test accounts

### Test Forgot Password
1. Navigate to `http://localhost/task4/forgot_password.php`
2. Enter registered email
3. Check Mailtrap inbox for reset email
4. Click reset link in email
5. Enter new password and confirm
6. Login with new password

## 📁 Important Files

| File | Purpose |
|------|---------|
| `config/db.php` | Database connection settings |
| `forgot_password.php` | ✨ Password reset functionality |
| `inc/auth.php` | Authentication helper functions |
| `inc/functions.php` | Utility functions |
| `admin/dashboard.php` | Admin dashboard |
| `customer/cart.php` | Shopping cart interface |

## 🐛 Troubleshooting

### Email Not Sending
- Check Mailtrap credentials in `forgot_password.php`
- Ensure SMTP_HOST and SMTP_PORT are correct
- Verify firewall/network allows port 2525
- Check error logs: `error_log()` output

### Database Connection Error
- Verify MySQL is running
- Check credentials in `config/db.php`
- Ensure database `food_ordering` exists
- Check database user permissions

### Session Issues
- Ensure `session_start()` is called at top of files
- Check PHP session settings in `php.ini`
- Clear browser cookies if needed

## 🔄 Recent Updates

### Version 1.0 - Task 4 Release
✨ **Fixed Forgot Password Feature**
- Professional HTML email template
- Absolute URL generation (HTTP/HTTPS)
- Email validation
- Better error handling
- Enhanced user interface
- Security improvements (no email enumeration)

## 📝 API Endpoints

### Cart API
- `POST /api/add_to_cart.php` - Add item to cart
- `POST /api/remove_from_cart.php` - Remove item from cart
- `POST /api/update_cart.php` - Update item quantity
- `POST /api/add_to_log.php` - Log user actions

## 👤 Author
**Madhu Sarvani**
- GitHub: [@madhusarvani](https://github.com/madhusarvani)
- Internship: Apex Planet Full Stack Internship

## 📄 License
This project is part of the Apex Planet Full Stack Internship program.

## 📞 Support
For issues or questions, please contact the development team or refer to `SETUP_GUIDE.md` for detailed setup instructions.

---

**Last Updated**: May 16, 2026
**Status**: ✅ Ready for Deployment
