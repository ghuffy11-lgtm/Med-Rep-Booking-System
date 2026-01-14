# Changelog

All notable changes to the Medical Representative Booking System will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Planned
- Two-factor authentication (2FA) for admin users
- SMS notifications for booking confirmations
- Advanced reporting dashboard with charts
- Mobile native app (iOS/Android)
- API rate limiting improvements
- Automated backup to cloud storage

---

## [1.0.0] - 2026-01-14

### Added

#### Authentication & Authorization
- User registration with email verification
- Role-based access control (Super Admin, Pharmacy Admin, Representative)
- Admin approval workflow for new user registrations
- Password complexity requirements (OWASP/NIST/PCI DSS compliant)
  - Minimum 8 characters, maximum 64 characters
  - Requires uppercase, lowercase, numbers, and special characters
- Password reset functionality via email
- Session management with configurable timeout
- "Remember Me" functionality

#### User Management
- User profile management
- Civil ID validation (12 digits, unique)
- Mobile number validation (8 digits, Kuwait format, unique)
- Email uniqueness validation
- Account activation/deactivation by admins
- User activity audit logging

#### Pharmacy Management
- CRUD operations for pharmacies
- Pharmacy information (name, location, contact details)
- Pharmacy activation/deactivation
- Multi-pharmacy support

#### Department Management
- CRUD operations for departments
- Department association with pharmacies
- Department activation/deactivation
- Department descriptions

#### Schedule Management
- Weekly schedule configuration per department
- Customizable working hours per day
- Configurable time slot duration (15-120 minutes)
- Maximum bookings per slot configuration
- Special day configuration (holidays, closures)

#### Booking System
- Representative booking creation
- Real-time slot availability checking
- Past time slot blocking (prevents booking past times)
- Booking status workflow:
  - Pending (awaiting approval)
  - Confirmed (approved by admin)
  - Cancelled (by user or admin)
  - Completed (after appointment time)
- Booking notes field (up to 500 characters)
- Cancellation reason tracking
- Booking list with filtering (status, date, department)
- Booking detail view
- Representative can cancel own bookings (with conditions)
- Admin can approve/reject booking requests
- Admin can cancel confirmed bookings
- No-show tracking

#### Security Features
- CSRF protection on all forms
- XSS prevention (Blade auto-escaping)
- SQL injection prevention (Eloquent ORM)
- bcrypt password hashing
- Rate limiting on authentication endpoints:
  - Login: 5 attempts per minute
  - Registration: 3 attempts per hour
  - Password reset request: 3 attempts per minute
  - Password reset: 5 attempts per minute
- HTTP security headers middleware:
  - X-Frame-Options: SAMEORIGIN
  - X-Content-Type-Options: nosniff
  - X-XSS-Protection: 1; mode=block
  - Content-Security-Policy (with hCaptcha support)
  - Referrer-Policy: strict-origin-when-cross-origin
  - Permissions-Policy
- Email verification required
- Admin approval required for new users
- hCaptcha integration for bot protection
- Session regeneration on login
- Session invalidation on logout
- Audit logging via AuditLogService

#### UI/UX
- Responsive design (desktop and mobile)
- Mobile-specific views with optimized layouts
- Bootstrap 5.3.0 framework
- Bootstrap Icons integration
- Clean, modern interface
- Flash message system (success, error, warning, info)
- Form validation with real-time feedback
- Password visibility toggle
- Loading indicators
- Confirmation dialogs for destructive actions

#### Email System
- Email verification emails
- Booking confirmation emails
- Booking cancellation notifications
- Password reset emails
- Admin approval notifications
- Configurable SMTP settings

#### Audit & Logging
- Comprehensive audit log system
- Tracks user actions:
  - Login/logout
  - Booking creation/cancellation
  - User approval/rejection
  - Profile changes
  - Admin actions
- Stores user ID, action type, IP address, timestamp
- 90-day retention policy (configurable)

#### Infrastructure
- Docker containerization
  - Nginx web server (Alpine)
  - PHP 8.2-FPM
  - MySQL 8.0
- Docker Compose orchestration
- Timezone configuration (Asia/Kuwait, GMT+3)
- Volume persistence for database and uploads
- Environment-based configuration (.env)

#### Developer Features
- Laravel 10.x framework
- Eloquent ORM with relationships
- Blade templating engine
- Service layer architecture (AuditLogService, ValidationRuleService)
- Centralized validation rules
- Database migrations with rollback support
- Database seeders for initial data
- RESTful route structure
- Middleware-based authentication
- Custom middleware (RoleMiddleware, SecurityHeaders)

#### Documentation
- Comprehensive documentation suite:
  - README.md (Project overview)
  - INSTALLATION.md (Setup guide)
  - SECURITY.md (Security documentation)
  - ARCHITECTURE.md (System architecture)
  - DATABASE.md (Database schema)
  - USER_MANUAL.md (End-user guide)
  - ADMIN_GUIDE.md (Administrator guide)
  - DEVELOPMENT.md (Developer guide)
  - TROUBLESHOOTING.md (Common issues)
  - CHANGELOG.md (Version history)
  - CONFIGURATION.md (Configuration reference)

### Changed
- Mobile number field changed from 20 characters to 8 digits (Kuwait format)
- Login verification flow improved to flash messages before logout
- Timezone configuration moved to environment variable (APP_TIMEZONE)
- Session flash messages now persist across logout redirects

### Fixed
- **Critical**: Login verification messages not showing for unverified users
  - Issue: `Auth::logout()` was called before `->with()`, destroying session
  - Fix: Changed to `$request->session()->flash()` before logout
- **Critical**: Syntax error in AuthController (`$user->id()` changed to `$user->id`)
- Removed docker-compose.yml from git tracking (already in .gitignore)
- Password hints now show character count and special character requirements
- Time slot blocking now prevents booking past times on current day
- Timezone synchronization with Docker host

### Security
- Added rate limiting to all authentication routes
- Implemented HTTP security headers middleware
- Password complexity requirements enforced (international compliance)
- Email verification required before login
- Admin approval required for new accounts
- Session security improvements
- Audit logging for all critical actions
- CSRF protection on all forms
- XSS and SQL injection prevention

---

## Version History Summary

### [1.0.0] - 2026-01-14
- **Initial Release**: Full booking system with authentication, authorization, booking management, security features, and comprehensive documentation

---

## Semantic Versioning

This project follows [Semantic Versioning](https://semver.org/):

- **MAJOR** version (X.0.0): Incompatible API changes or major functionality changes
- **MINOR** version (0.X.0): New features in a backward-compatible manner
- **PATCH** version (0.0.X): Backward-compatible bug fixes

### Version Number Format: MAJOR.MINOR.PATCH

Example:
- `1.0.0` → `1.0.1`: Bug fix (patch)
- `1.0.1` → `1.1.0`: New feature (minor)
- `1.1.0` → `2.0.0`: Breaking change (major)

---

## Change Categories

Changes are grouped into the following categories:

- **Added**: New features
- **Changed**: Changes to existing functionality
- **Deprecated**: Soon-to-be removed features
- **Removed**: Removed features
- **Fixed**: Bug fixes
- **Security**: Security improvements or vulnerability fixes

---

## How to Update This Changelog

When making changes:

1. **Add entry under [Unreleased]** section
2. **Use appropriate category**: Added, Changed, Fixed, etc.
3. **Be descriptive**: Explain what changed and why
4. **Reference issues**: Link to GitHub issues if applicable
5. **On release**: Move [Unreleased] changes to new version section

### Example Entry Format

```markdown
### Added
- User profile photo upload feature (#123)
- Export bookings to PDF (#124)

### Fixed
- Booking cancellation not sending email notification (#125)
- Date picker showing wrong timezone (#126)

### Security
- Updated Laravel to 10.x.x to fix CVE-XXXX-XXXX (#127)
```

---

## Migration Guides

### Upgrading from Beta to 1.0.0

No migration needed (first release).

Future major version upgrades will include migration guides here.

---

## Deprecation Notices

### Deprecated in 1.0.0
- None

Future deprecations will be listed here with removal timeline.

---

## Known Issues

### Version 1.0.0
- None reported yet

Future known issues will be documented here.

---

## Contributors

### Version 1.0.0
- Development Team
- Security Review Team
- Documentation Team
- QA Team

---

## Release Schedule

- **Major releases**: Annually (January)
- **Minor releases**: Quarterly (April, July, October, January)
- **Patch releases**: As needed (bug fixes, security patches)

---

## Support

- **Version 1.0.x**: Full support until 2027-01-14
- **Security updates**: Provided for 2 years after release

For older versions, please upgrade to the latest version.

---

**Last Updated**: 2026-01-14
**Document Version**: 1.0
**Document ID**: CHANGELOG-001

For questions about changes or releases: dev-team@example.com
