# WEB_4.1 - Full Stack Web Development Project

[![GitHub repo size](https://img.shields.io/github/repo-size/mir183/WEB_4.1)](https://github.com/mir183/WEB_4.1)
[![GitHub last commit](https://img.shields.io/github/last-commit/mir183/WEB_4.1)](https://github.com/mir183/WEB_4.1)
[![GitHub issues](https://img.shields.io/github/issues/mir183/WEB_4.1)](https://github.com/mir183/WEB_4.1/issues)

This repository contains a comprehensive full-stack web development project showcasing modern frontend design, secure backend authentication systems, and database management. The project demonstrates proficiency in HTML5, CSS3, Bootstrap, JavaScript, PHP with session management, MySQL database operations, and file upload handling.

## 📁 Project Overview

This project consists of four main components that demonstrate different aspects of web development:

### 🍽️ FoodieLand - Modern Restaurant Website
A fully responsive, modern food website built with cutting-edge frontend technologies.

**✨ Key Features:**
- 📱 **Fully Responsive Design** - Optimized for mobile, tablet, and desktop
- 🎨 **Modern UI/UX** - Clean, attractive food-themed interface
- ⚡ **Fast Loading** - Optimized images and efficient CSS
- 🔤 **Custom Typography** - Google Fonts integration (Handjet)
- 🎯 **Interactive Elements** - Smooth animations and hover effects
- 📧 **Contact Form** - Functional contact page with form validation
- 🖼️ **Image Gallery** - High-quality food photography showcase
- 🧭 **Intuitive Navigation** - Bootstrap-powered responsive navbar

**🛠️ Technologies & Frameworks:**
- **HTML5** - Semantic markup structure
- **CSS3** - Modern styling with Flexbox and Grid
- **Bootstrap 5.3.7** - Responsive framework
- **JavaScript** - Interactive functionality
- **Font Awesome 6.7.2** - Icon library
- **Google Fonts** - Custom typography

**📄 Project Structure:**
```
Mid Assignment/
├── index.html          # Main homepage with hero section and features
├── contact.html        # Contact page with form and location info
├── styles.css          # Custom CSS styles and responsive design
└── images/            # Optimized image assets
    ├── chef.jpg       # Chef profile images
    ├── burger.jpg     # Food item photos
    ├── salad.jpg      # Healthy food options
    ├── salmon.jpg     # Premium dishes
    └── ...           # Additional food photography
```

### 🔐 SecureAuth - PHP Session Management System
A robust authentication system demonstrating secure session handling and access control.

**🔒 Security Features:**
- 🛡️ **Session-based Authentication** - Secure PHP session management
- 🚪 **Login/Logout System** - Complete authentication flow
- 🔐 **Access Control** - Protected dashboard areas
- 🛑 **Route Protection** - Unauthorized access prevention
- 🔄 **Session Persistence** - Maintains user state across requests
- ⚡ **Automatic Redirects** - Smart navigation based on auth status

**🛠️ Backend Technologies:**
- **PHP** - Server-side scripting and logic
- **Session Management** - Native PHP session handling
- **HTML Forms** - User input and authentication
- **Security Headers** - Protection against common vulnerabilities

**📄 System Structure:**
```
Log In With Session/
├── index.php          # Login form with authentication logic
├── dashboard.php      # Protected user dashboard
└── logout.php         # Session termination and cleanup
```

### 🗄️ CRUD Database System - Perfume Management
A complete Create, Read, Update, Delete (CRUD) system with file upload functionality for managing perfume products.

**🔧 Database Features:**
- 📊 **Full CRUD Operations** - Complete database management
- 📁 **File Upload System** - Image upload with validation
- 🔍 **Data Validation** - Server-side input validation and sanitization
- 🖼️ **Image Management** - Organized file storage system
- 🗃️ **MySQL Integration** - Professional database connectivity
- 🛡️ **SQL Injection Protection** - Prepared statements for security

**🛠️ Technical Implementation:**
- **PHP** - Backend logic and database operations
- **MySQL** - Relational database management
- **File Handling** - Image upload and management
- **Form Processing** - Data validation and sanitization

**📄 System Structure:**
```
CRUD(DB)/
├── connect.php        # Database connection configuration
├── crud/             # CRUD operations directory
│   ├── create.php    # Add new perfume products
│   ├── update.php    # Edit existing products
│   └── delete.php    # Remove products from database
└── uploads/          # Image storage directory
    ├── asad.jpg      # Product images
    ├── precieux.jpg  # Sample perfume photos
    └── ...          # Additional product images
```

### 🌐 WebFinal - Advanced User Management System
A sophisticated user management system with authentication, profile management, and advanced features.

**🚀 Advanced Features:**
- 🔐 **Complete Authentication** - Registration, login, logout system
- 👤 **Profile Management** - User profile with image upload
- 📧 **Email Integration** - PHPMailer for email functionality
- 🔄 **Password Reset** - Secure password recovery system
- 📊 **Login History** - Track user login activities
- 🌙 **Dark Theme** - Toggle between light and dark modes
- 🗑️ **Account Management** - User account deletion functionality

**🛠️ Advanced Technologies:**
- **PHP** - Server-side scripting and logic
- **MySQL** - User data management
- **PHPMailer** - Email functionality
- **JavaScript** - Dynamic theme switching
- **CSS3** - Advanced styling and theme system
- **Bootstrap** - Responsive UI framework

**📄 WebFinal Structure:**
```
WebFinal/
├── connect.php       # Database connection
├── login.php         # User authentication
├── reg.php           # User registration
├── dashboard.php     # User dashboard
├── reset_pass.php    # Password reset functionality
├── login_history.php # User login tracking
├── del.php           # Account deletion
├── logout.php        # Session termination
├── dark-theme.css    # Dark mode styling
├── dark-theme.js     # Theme switching logic
├── phpmailer/        # Email library
└── uploads/          # User profile images
```

## 🚀 Quick Start Guide

### 📋 Prerequisites
Before running this project, make sure you have:
- 🌐 **Modern Web Browser** (Chrome, Firefox, Safari, Edge)
- 🖥️ **Local Web Server** with PHP support (XAMPP, WAMP, MAMP, or Apache/Nginx)
- �️ **MySQL Database** (for CRUD and WebFinal systems)
- �📁 **Git** (for cloning the repository)

### ⚡ Installation Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/mir183/WEB_4.1.git
   cd WEB_4.1
   ```

2. **For FoodieLand Website (Frontend)**
   ```bash
   # Simply open in browser - no server required
   open "Mid Assignment/index.html"
   # Or double-click the index.html file
   ```

3. **For SecureAuth System (PHP Backend)**
   ```bash
   # Copy to your web server directory
   cp -r "Log In With Session" /path/to/your/webserver/
   # Navigate to: http://localhost/Log In With Session/
   ```

4. **For CRUD Database System**
   ```bash
   # Copy to your web server directory
   cp -r "CRUD(DB)" /path/to/your/webserver/
   # Create MySQL database named 'perfume'
   # Navigate to: http://localhost/CRUD(DB)/
   ```

5. **For WebFinal User Management System**
   ```bash
   # Copy to your web server directory
   cp -r "WebFinal" /path/to/your/webserver/
   # Create MySQL database and configure tables
   # Navigate to: http://localhost/WebFinal/
   ```

### 🔑 Authentication Credentials
```
Username: MIR
Password: abcd
```

### 🎯 Testing the Applications

#### FoodieLand Website:
- ✅ Test responsive design by resizing browser window
- ✅ Navigate between Home and Contact pages
- ✅ Try the contact form functionality
- ✅ Check mobile responsiveness

#### SecureAuth System:
- ✅ Access login page: `http://localhost/Log In With Session/`
- ✅ Test authentication with provided credentials
- ✅ Verify dashboard access after login
- ✅ Test logout functionality
- ✅ Try accessing dashboard without authentication

#### CRUD Database System:
- ✅ Test database connection
- ✅ Create new perfume products with images
- ✅ Read and display product listings
- ✅ Update existing product information
- ✅ Delete products from database
- ✅ Verify file upload functionality

#### WebFinal User Management:
- ✅ Register new user accounts
- ✅ Test login functionality
- ✅ Upload and manage profile pictures
- ✅ Try password reset feature
- ✅ Check login history tracking
- ✅ Toggle dark/light theme
- ✅ Test account deletion

## 📱 Features & Demonstrations

### 🍽️ FoodieLand Website Highlights
- **🎨 Responsive Design**: Seamlessly adapts to any screen size
- **⚡ Performance Optimized**: Fast loading with optimized assets
- **🎯 User Experience**: Intuitive navigation and smooth interactions
- **📸 Visual Appeal**: High-quality food photography and modern design
- **♿ Accessibility**: Semantic HTML and ARIA compliance
- **📱 Mobile-First**: Designed primarily for mobile users

### 🔐 SecureAuth System Capabilities
- **🛡️ Security First**: Implements PHP security best practices
- **🔄 Session Management**: Robust session handling and timeout
- **🚪 Access Control**: Role-based access to protected areas
- **🔒 Data Protection**: Secure credential validation
- **⚡ Performance**: Efficient authentication flow
- **🎯 User Experience**: Smooth login/logout process

### 🗄️ CRUD Database System Features
- **📊 Complete CRUD Operations**: Full Create, Read, Update, Delete functionality
- **📁 File Upload Management**: Secure image upload with validation
- **🛡️ SQL Injection Protection**: Prepared statements for security
- **🔍 Data Validation**: Server-side input validation and sanitization
- **🖼️ Image Management**: Organized file storage and retrieval
- **📈 Scalable Architecture**: Professional database design patterns

### 🌐 WebFinal Advanced Features
- **👥 User Management**: Complete registration and authentication system
- **📧 Email Integration**: PHPMailer for password reset and notifications
- **🌙 Theme Switching**: Dynamic dark/light mode toggle
- **📊 Activity Tracking**: Login history and user activity monitoring
- **🖼️ Profile Management**: User profile pictures and data management
- **🔒 Advanced Security**: Password hashing, session security, and data protection

## 🛠️ Technical Implementation

### Frontend Architecture (FoodieLand)
- **Component-Based Design**: Modular CSS and HTML structure
- **Mobile-First Responsive**: Bootstrap grid system
- **Performance Optimization**: Minified CSS and optimized images
- **Cross-Browser Compatibility**: Tested on major browsers
- **SEO Friendly**: Semantic HTML and meta tags

### Backend Architecture (SecureAuth)
- **MVC Pattern**: Clean separation of concerns
- **Session Security**: Secure session configuration
- **Input Validation**: Server-side validation and sanitization
- **Error Handling**: Graceful error management
- **Code Organization**: Clean, readable PHP code

### Database Architecture (CRUD System)
- **MySQL Integration**: Professional database connectivity
- **Prepared Statements**: Protection against SQL injection
- **File Upload System**: Secure image handling and storage
- **Data Validation**: Comprehensive input validation
- **CRUD Operations**: Complete Create, Read, Update, Delete functionality

### Advanced System Architecture (WebFinal)
- **User Authentication**: Secure login/registration system
- **Email Integration**: PHPMailer for email functionality
- **Theme Management**: Dynamic CSS switching with JavaScript
- **Profile Management**: User data and image upload handling
- **Activity Tracking**: Login history and user analytics
- **Security Implementation**: Password hashing and session management

## 🎓 Learning Outcomes

This project demonstrates proficiency in:
- ✅ **Frontend Development**: HTML5, CSS3, JavaScript, Bootstrap
- ✅ **Backend Development**: PHP, Session Management, Security
- ✅ **Database Management**: MySQL, CRUD operations, data modeling
- ✅ **File Upload Systems**: Secure file handling and storage
- ✅ **Email Integration**: PHPMailer, SMTP configuration, email templates
- ✅ **Theme Management**: Dynamic CSS, JavaScript theme switching
- ✅ **User Authentication**: Registration, login, password reset systems
- ✅ **Responsive Design**: Mobile-first approach and cross-device compatibility
- ✅ **Web Security**: Authentication, session handling, input validation, SQL injection prevention
- ✅ **User Experience**: Intuitive interfaces and smooth interactions
- ✅ **Code Organization**: Clean, maintainable, and scalable code structure
- ✅ **Version Control**: Git workflow and repository management
- ✅ **Full-Stack Development**: Complete frontend to backend integration

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

### Development Setup
1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📧 Contact & Support

- **Developer**: MIR
- **GitHub**: [@mir183](https://github.com/mir183)
- **Project Link**: [https://github.com/mir183/WEB_4.1](https://github.com/mir183/WEB_4.1)

For any questions, suggestions, or feedback about this project, please feel free to:
- 🐛 Open an issue for bug reports
- 💡 Start a discussion for feature requests
- 📧 Contact directly for collaboration opportunities

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

```
MIT License

Copyright (c) 2025 MIR

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
```

## 📊 Project Stats

- **Total Projects**: 4 complete web applications
- **Total Files**: 50+ files across all projects
- **Languages**: HTML, CSS, JavaScript, PHP, SQL
- **Frameworks**: Bootstrap 5.3.7, PHPMailer
- **Libraries**: Font Awesome, Google Fonts, jQuery
- **Database**: MySQL with multiple tables
- **Image Assets**: 30+ optimized images and uploads
- **Responsive Breakpoints**: 5 (XS, SM, MD, LG, XL)
- **PHP Features**: Session management, file uploads, email integration

## 🏆 Achievements

- ✅ **Responsive Design**: Works perfectly on all devices
- ✅ **Cross-Browser Compatible**: Tested on Chrome, Firefox, Safari, Edge
- ✅ **Performance Optimized**: Fast loading times across all applications
- ✅ **Security Implemented**: Multiple authentication systems with best practices
- ✅ **Database Integration**: Complete CRUD operations with MySQL
- ✅ **File Upload Systems**: Secure image handling and storage
- ✅ **Email Functionality**: Working email system with PHPMailer
- ✅ **Theme Management**: Dark/Light mode switching
- ✅ **Clean Code**: Well-organized and documented codebase
- ✅ **Modern Standards**: Uses latest web technologies and best practices
- ✅ **Full-Stack Development**: Complete frontend to backend integration

---

<div align="center">

**⭐ If you find this project helpful, please consider giving it a star!**

*Developed as part of Web Development Course - Showcasing Full Stack Development Skills*

**Made with ❤️ by [MIR](https://github.com/mir183)**

</div>