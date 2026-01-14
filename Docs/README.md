# Med Rep Appointment Booking System

## 📋 Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Technical Stack](#technical-stack)
4. [System Requirements](#system-requirements)
5. [Documentation Index](#documentation-index)
6. [Quick Links](#quick-links)

---

## Overview

The **Med Rep Appointment Booking System** is a comprehensive web application designed to manage appointments between medical representatives and pharmacy departments. The system provides role-based access control, email verification, booking management, and administrative tools.

### Purpose
- Streamline appointment scheduling for medical representatives
- Enable pharmacy administrators to manage and approve bookings
- Provide super admins with complete system control and configuration
- Ensure secure, verified, and organized appointment management

### Key Objectives
- ✅ Reduce appointment scheduling conflicts
- ✅ Provide transparent booking approval workflow
- ✅ Maintain audit trail of all system activities
- ✅ Ensure data security and user privacy
- ✅ Support mobile and desktop users

---

## Features

### User Management
- ✅ **Multi-Role System**: Super Admin, Pharmacy Admin, Medical Representative
- ✅ **Email Verification**: Required for all new registrations
- ✅ **Account Activation**: Admin approval required for new representatives
- ✅ **Secure Authentication**: Password complexity requirements, rate limiting
- ✅ **Password Recovery**: Secure password reset via email

### Booking System
- ✅ **Time Slot Management**: Configurable time slots for different departments
- ✅ **Cooldown Period**: Prevents excessive booking frequency
- ✅ **Booking Approval Workflow**: Admin review and approval process
- ✅ **Rejection with Reasons**: Clear communication when bookings are rejected
- ✅ **My Bookings**: Users can view their booking history and status

### Admin Features
- ✅ **Pending Queue**: Quick access to pending approvals
- ✅ **Department Management**: Create and manage pharmacy departments
- ✅ **Schedule Management**: Configure availability and closures
- ✅ **Reports**: Generate appointment reports (web, PDF, print)
- ✅ **User Management**: Activate/deactivate users, manage permissions

### Super Admin Features
- ✅ **User Administration**: Complete CRUD operations for all users
- ✅ **Global Configuration**: System-wide settings management
- ✅ **Time Slot Configuration**: Configure booking hours and durations
- ✅ **Booking Limits**: Set daily booking limits per department
- ✅ **Audit Logs**: Complete activity tracking

### Security Features
- ✅ **CSRF Protection**: All forms protected
- ✅ **XSS Prevention**: Blade template auto-escaping
- ✅ **SQL Injection Prevention**: Eloquent ORM with parameterized queries
- ✅ **Rate Limiting**: Login, registration, and password reset throttling
- ✅ **HTTP Security Headers**: X-Frame-Options, CSP, X-Content-Type-Options
- ✅ **Password Hashing**: Bcrypt encryption
- ✅ **hCaptcha**: Bot protection on registration

---

## Technical Stack

### Backend
- **Framework**: Laravel 10.x
- **PHP Version**: 8.2+
- **Database**: MySQL 8.0
- **Authentication**: Laravel Sanctum
- **Email**: Laravel Mail with SMTP

### Frontend
- **UI Framework**: Bootstrap 5.3.0
- **Icons**: Bootstrap Icons 1.11.x
- **JavaScript**: Vanilla JS (no framework dependencies)
- **Mobile Support**: Responsive design with dedicated mobile views

### Infrastructure
- **Containerization**: Docker & Docker Compose
- **Web Server**: Nginx (Alpine)
- **Application Server**: PHP-FPM
- **Database Admin**: phpMyAdmin
- **Timezone**: Asia/Kuwait (GMT+3)

### Development Tools
- **Version Control**: Git
- **Package Manager**: Composer
- **Environment**: Docker-based development

---

## System Requirements

### Production Server
- **OS**: Linux (Ubuntu 20.04+ recommended)
- **Docker**: 20.10+ with Docker Compose
- **RAM**: Minimum 2GB, Recommended 4GB
- **Storage**: Minimum 10GB free space
- **Network**: HTTPS-capable (SSL certificate)

### Development Environment
- **OS**: Windows, macOS, or Linux
- **Docker Desktop**: Latest version
- **Git**: 2.x+
- **Code Editor**: VS Code, PHPStorm, or similar

### Browser Support
- **Desktop**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- **Mobile**: iOS Safari 14+, Chrome Mobile 90+

---

## Documentation Index

Comprehensive documentation is organized into the following files:

### 📚 Core Documentation
1. **[Installation Guide](./INSTALLATION.md)** - Setup instructions and deployment
2. **[Architecture](./ARCHITECTURE.md)** - System design and structure
3. **[API Documentation](./API.md)** - Endpoints and usage
4. **[Database Schema](./DATABASE.md)** - Tables, relationships, and migrations

### 🔧 Configuration & Setup
5. **[Configuration Guide](./CONFIGURATION.md)** - Environment and settings
6. **[Security Guide](./SECURITY.md)** - Security features and best practices
7. **[Deployment Guide](./DEPLOYMENT.md)** - Production deployment steps

### 👥 User Guides
8. **[User Manual](./USER_MANUAL.md)** - End-user documentation
9. **[Admin Guide](./ADMIN_GUIDE.md)** - Administrator documentation

### 🛠️ Development
10. **[Development Guide](./DEVELOPMENT.md)** - Developer guidelines
11. **[Troubleshooting](./TROUBLESHOOTING.md)** - Common issues and solutions
12. **[Changelog](./CHANGELOG.md)** - Version history and updates

---

## Quick Links

### For New Users
- [How to Register](./USER_MANUAL.md#registration)
- [How to Book an Appointment](./USER_MANUAL.md#creating-bookings)
- [How to View My Bookings](./USER_MANUAL.md#viewing-bookings)

### For Administrators
- [Approving Bookings](./ADMIN_GUIDE.md#approving-bookings)
- [Managing Departments](./ADMIN_GUIDE.md#department-management)
- [Generating Reports](./ADMIN_GUIDE.md#reports)

### For Developers
- [Local Setup](./INSTALLATION.md#local-development)
- [Database Migrations](./DATABASE.md#migrations)
- [Code Structure](./ARCHITECTURE.md#project-structure)

### Support & Contact
- **Issues**: Report bugs on GitHub Issues
- **Security**: Report security vulnerabilities privately
- **Documentation**: Contributions welcome via pull requests

---

## Project Status

**Current Version**: 1.6
**Status**: Production Ready
**Last Updated**: January 2026
**Maintained By**: Development Team

---

## License

Copyright © 2026 Med Rep Appointment Booking System. All rights reserved.

---

*For detailed information on any topic, please refer to the specific documentation files listed above.*
