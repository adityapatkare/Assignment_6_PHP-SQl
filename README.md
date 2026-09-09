# Multi-Vendor E-Commerce Platform

## Project Overview

This project is a full-stack dynamic Multi-Vendor E-Commerce Platform developed using PHP, MySQL, Bootstrap, HTML, CSS, and JavaScript.

The application allows Customers, Vendors, and Administrators to interact through dedicated panels and role-based access control. All products, categories, banners, coupons, orders, and featured sections are dynamically managed through the database.

The project is inspired by modern e-commerce platforms and follows a database-driven architecture with authentication, vendor management, analytics, product variations, multi-image product galleries, API endpoints, and security best practices.

Test Link: https://paleturquoise-shrew-583501.hostingersite.com/

## Demo Credentials

### Admin

URL:
https://paleturquoise-shrew-583501.hostingersite.com/admin/index.php

Username:
admin@test.com

Password:
admin123

---

### Vendor

URL:
https://paleturquoise-shrew-583501.hostingersite.com/vendor/index.php

Email:
jane@gmail.com

Password:
P@ssword

---

### Customer

URL:
https://paleturquoise-shrew-583501.hostingersite.com/login.php

Email:
admin@test.com or adipatkare@gmail.com

Password:
admin123 or P@ssword

---

## Features

### Customer Features

- User Registration & Login
- Secure Authentication
- Dynamic Homepage
- Product Search & Filters
- Product Detail Page
- Multiple Product Images (Gallery)
- Product Variations (Size & Color)
- Color Swatch Selection
- Shopping Cart
- Checkout & Order Placement
- Order History
- Product Reviews & Ratings
- Wishlist
- User Profile Management
- Contact & Feedback Form
- Responsive Design

---

### Vendor Features

- Vendor Login
- Vendor Dashboard
- Vendor Analytics
- Product Management (CRUD)
- Multiple Product Image Uploads
- Inventory Management
- Order Management
- Sales Tracking

---

### Admin Features

- Admin Login
- Dashboard Analytics
- Product Management (CRUD, multi-image gallery)
- Category Management (CRUD with image upload)
- Vendor Management
- User Management
- Order Management
- Coupon & Discount Management
- Homepage Banner Management
- Featured Product Management
- Reports & Analytics
- Site Settings Management

---

### API Features

- Products API
- Orders API
- Authentication API
- JSON Responses
- CORS Enabled
- Ready for Mobile App Integration

---

## Database

The application uses a MySQL database named:

ecommerce_db

### Database Tables

| Table               | Description                              |
| ------------------- | ----------------------------------------- |
| users               | Customer and vendor user accounts         |
| vendors             | Vendor information                        |
| products            | Product catalog                           |
| product_images      | Additional gallery images per product     |
| product_variations  | Product size/color variations             |
| categories          | Product categories (with image support)   |
| orders              | Customer orders                           |
| order_items         | Order details                             |
| coupons             | Discount coupons                          |
| reviews             | Product ratings and reviews               |
| feedback            | Contact form submissions                  |
| banners             | Homepage banners                          |
| admin               | Administrator accounts                    |

### Database Features

- Product Variations
- Multi-Image Product Galleries
- Category Images
- Order Tracking
- Vendor Mapping
- Reviews & Ratings
- Coupons & Discounts
- Customer Feedback
- Dynamic Homepage Content

### Database Import

The repository includes:

ecommerce_db.sql

Import this SQL file into phpMyAdmin before running the project.

---

## Security Features

The project implements the following security practices:

- Password Hashing using `password_hash()`
- Password Verification using `password_verify()`
- Session-Based Authentication
- Role-Based Access Control
- Input Validation
- Data Sanitization
- Prepared Statements for Insert/Update Operations
- API Security Headers
- CORS Configuration
- Protected Admin Routes
- Protected Vendor Routes

---

## API Endpoints

### Products API

/api/products.php

### Orders API

/api/orders.php

### Authentication API

/api/auth.php

### Login API

/api/login.php

---

## Technology Stack

### Frontend

- HTML5
- CSS3
- Bootstrap 5
- JavaScript

### Backend

- PHP

### Database

- MySQL

### Version Control

- Git
- GitHub

---

## Project Structure

```
ecommerce/
├── admin/
│   ├── index.php
│   ├── dashboard.php
│   ├── products.php
│   ├── categories.php
│   ├── vendors.php
│   ├── orders.php
│   ├── coupons.php
│   ├── reports.php
│   ├── settings.php
│   └── includes/
├── vendor/
│   ├── index.php
│   ├── dashboard.php
│   ├── products.php
│   ├── orders.php
│   └── includes/
├── api/
│   ├── auth.php
│   ├── login.php
│   ├── products.php
│   └── orders.php
├── includes/
│   ├── header.php
│   └── footer.php
├── uploads/
│   ├── categories/
│   ├── screenshots/
│   └── includes/
├── db.php
├── index.php
├── product.php
├── cart.php
├── checkout.php
├── login.php
├── signup.php
├── logout.php
├── profile.php
├── contact.php
├── search.php
├── update_cart.php
└── README.md
```

---

## Screenshots

The repository contains a dedicated screenshots folder demonstrating the major functionalities of the application.

### Screenshots Included

```
screenshots/
├── homepage.png
├── product-page.png
├── cart.png
├── checkout.png
├── customer-profile.png
├── admin-dashboard.png
├── admin-products.png
├── vendor-dashboard.png
├── vendor-products.png
├── orders-management.png
```

### Demonstrated Features

- Homepage
- Dynamic Products
- Product Variations
- Multi-Image Product Gallery
- Shopping Cart
- Checkout Process
- User Profile
- Admin Dashboard
- Product Management
- Category Management
- Vendor Dashboard
- Vendor Product Management
- Order Management
- Reports & Analytics

---

## Installation Guide

### Step 1

Clone the repository

```bash
git clone YOUR_GITHUB_REPOSITORY_URL
```

### Step 2

Import:

ecommerce_db.sql

into MySQL using phpMyAdmin.

### Step 3

Configure database credentials inside:

```php
db.php
```

### Step 4

Place the project folder inside:

```
xampp/htdocs/
```

### Step 5

Start:

- Apache
- MySQL

from XAMPP Control Panel.

### Step 6

Open the project:

```
http://localhost/project-folder-name
```

---

## User Roles

### Admin

Access:

/admin

Capabilities:

- Manage Products (with multi-image galleries)
- Manage Categories (with image upload)
- Manage Vendors
- Manage Orders
- Manage Coupons
- Manage Banners
- Generate Reports

---

### Vendor

Access:

/vendor

Capabilities:

- Manage Own Products
- View Orders
- View Analytics
- Manage Inventory

---

### Customer

Access:

/login.php

Capabilities:

- Browse Products
- View Product Image Galleries
- Place Orders
- Manage Profile
- Submit Reviews
- Contact Support

---

## Assignment Requirements Covered

### Frontend

- Dynamic Homepage
- Product Details
- Multi-Image Product Gallery
- Product Variations (Size & Color Swatches)
- Search & Filters
- Cart System
- Checkout System
- User Authentication
- User Profile
- Reviews & Ratings
- Contact Form

### Admin Panel

- Dashboard
- Product CRUD (multi-image support)
- Category CRUD (image support)
- Vendor Management
- Order Management
- Coupon Management
- Banner Management
- Reports

### Vendor Panel

- Vendor Login
- Vendor Dashboard
- Product CRUD
- Vendor Orders
- Vendor Analytics

### Security

- Authentication
- Authorization
- Session Management
- Password Hashing
- Input Validation
- Prepared Statements
- CORS Support

### Database

- Fully Dynamic Content
- Product Variations
- Multi-Image Galleries
- Reviews
- Feedback
- Coupons
- Orders

---

## Future Improvements

- Razorpay Integration
- Stripe Integration
- Recently Viewed Products
- Newsletter Integration
- SEO-Friendly URLs
- Sitemap Generation
- Live Chat Support
- Product Import/Export

---

## Author

Aditya Patkare

Full Stack Developer Evaluation Project

Submitted as part of the Full Stack Developer Assessment.