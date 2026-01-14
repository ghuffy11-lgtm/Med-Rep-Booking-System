# Administrator Guide

## Table of Contents
1. [Introduction](#introduction)
2. [Admin Roles](#admin-roles)
3. [Getting Started](#getting-started)
4. [Super Admin Functions](#super-admin-functions)
5. [Pharmacy Admin Functions](#pharmacy-admin-functions)
6. [User Management](#user-management)
7. [Pharmacy Management](#pharmacy-management)
8. [Department Management](#department-management)
9. [Schedule Management](#schedule-management)
10. [Booking Management](#booking-management)
11. [Reports and Analytics](#reports-and-analytics)
12. [System Settings](#system-settings)
13. [Security Management](#security-management)
14. [Troubleshooting](#troubleshooting)
15. [Best Practices](#best-practices)

---

## Introduction

This guide is for administrators who manage the Medical Representative Booking System. It covers all administrative functions, from user approval to system configuration.

**Administrator Responsibilities**:
- Approve/reject user registrations
- Manage pharmacies and departments
- Configure schedules and availability
- Monitor bookings and appointments
- Generate reports
- Maintain system security

**Support Resources**:
- Technical Support: tech-support@example.com
- Emergency Contact: +965 XXXX XXXX
- Documentation: https://your-domain.com/docs

---

## Admin Roles

### Super Admin

**Access Level**: Full system access

**Responsibilities**:
- Manage all pharmacies across the system
- Create and manage pharmacy administrators
- View system-wide reports and analytics
- Configure system settings
- Monitor security and audit logs
- Approve critical changes
- Backup and restore data

**Limitations**: None (full access)

### Pharmacy Admin

**Access Level**: Limited to assigned pharmacy

**Responsibilities**:
- Manage own pharmacy information
- Manage departments within own pharmacy
- Configure department schedules
- Approve/reject booking requests
- Manage representatives for own pharmacy
- View pharmacy-specific reports
- Handle day-to-day operations

**Limitations**:
- Cannot access other pharmacies' data
- Cannot create other admins
- Cannot modify system-wide settings
- Cannot view system-wide reports

---

## Getting Started

### First Login

**Super Admin**:
1. Super Admin account is created during system installation
2. Default credentials (if seeded):
   - Email: admin@example.com
   - Password: See installation documentation
3. **IMPORTANT**: Change default password immediately

**Pharmacy Admin**:
1. Account created by Super Admin
2. Receive credentials via email
3. First login requires password change
4. Complete profile setup

### Admin Dashboard Overview

**Super Admin Dashboard**:
- System-wide statistics
- Total pharmacies, departments, users
- Recent activity across all pharmacies
- Pending approvals (all)
- System health indicators
- Quick actions (create pharmacy, create admin, view reports)

**Pharmacy Admin Dashboard**:
- Pharmacy-specific statistics
- Department status
- Today's bookings
- Pending booking approvals
- Representative activity
- Quick actions (manage bookings, manage schedules)

### Navigation Menu

**Super Admin Menu**:
- Dashboard
- Pharmacies (manage all)
- Administrators (manage all admins)
- Users (manage all users)
- Bookings (view all)
- Reports (system-wide)
- Settings (system configuration)
- Audit Logs
- Profile
- Logout

**Pharmacy Admin Menu**:
- Dashboard
- My Pharmacy
- Departments
- Schedules
- Bookings
- Representatives
- Reports (pharmacy-specific)
- Profile
- Logout

---

## Super Admin Functions

### Managing System

**Access**: Super Admin only

#### Creating a New Pharmacy

1. Navigate to **Pharmacies** → **Add New Pharmacy**
2. Fill in required information:
   - **Pharmacy Name**: Full name (e.g., "Hadi Clinic Pharmacy - Salmiya")
   - **Location**: Full address or area
   - **Contact Number**: 8-digit phone number
   - **Email**: Contact email (optional but recommended)
   - **Status**: Active/Inactive
3. Click **"Create Pharmacy"**
4. Success message will appear
5. Pharmacy ID is generated automatically

**Validation Rules**:
- Name must be unique
- Contact number must be 8 digits
- Email must be valid format (if provided)

#### Editing Pharmacy Information

1. Navigate to **Pharmacies**
2. Find the pharmacy to edit
3. Click **"Edit"** button
4. Modify information as needed
5. Click **"Save Changes"**

#### Deactivating/Activating Pharmacy

**To Deactivate**:
1. Go to **Pharmacies**
2. Find the pharmacy
3. Click **"Deactivate"**
4. Confirm action
5. **Effect**:
   - Pharmacy hidden from representatives
   - Existing bookings remain
   - Pharmacy admin can still login (read-only)
   - No new bookings accepted

**To Activate**:
1. Go to **Pharmacies**
2. Filter to show inactive pharmacies
3. Find the pharmacy
4. Click **"Activate"**
5. Pharmacy becomes available again

#### Deleting Pharmacy

**WARNING**: This is irreversible!

1. Go to **Pharmacies**
2. Find the pharmacy
3. Click **"Delete"**
4. Type pharmacy name to confirm
5. Click **"Permanently Delete"**

**Effect of Deletion**:
- Pharmacy permanently removed
- All departments deleted (CASCADE)
- All schedules deleted (CASCADE)
- All bookings deleted (CASCADE)
- Associated pharmacy admin account remains (pharmacy_id set to NULL)
- Audit logs preserved

**Best Practice**: Use "Deactivate" instead of delete in most cases.

### Managing Administrators

#### Creating Pharmacy Admin

1. Navigate to **Administrators** → **Add New Admin**
2. Fill in required information:
   - **Name**: Full name
   - **Email**: Must be unique
   - **Civil ID**: 12-digit unique ID
   - **Mobile Number**: 8-digit unique number
   - **Pharmacy Assignment**: Select pharmacy
   - **Status**: Active/Inactive
3. Click **"Create Administrator"**
4. Admin receives email with temporary password
5. Admin must verify email and change password on first login

**Initial Password**:
- Randomly generated secure password
- Sent via email
- Must be changed on first login
- Cannot be recovered (only reset)

#### Viewing Admin Activity

1. Go to **Administrators**
2. Click on admin name
3. View:
   - Login history
   - Last login date/time
   - Actions performed
   - Audit log entries

#### Resetting Admin Password

**For security, admins cannot see passwords**

1. Go to **Administrators**
2. Find the admin
3. Click **"Reset Password"**
4. New temporary password is generated
5. Password sent to admin's email
6. Admin must change password on next login

#### Removing Admin Access

**To Deactivate** (Soft removal):
1. Go to **Administrators**
2. Find the admin
3. Click **"Deactivate"**
4. Admin cannot login but data preserved

**To Delete** (Hard removal):
1. Go to **Administrators**
2. Find the admin
3. Click **"Delete Account"**
4. Confirm deletion
5. Admin account and data permanently removed

### System-Wide Reports

**Access**: Super Admin only

#### Available Reports

1. **User Registration Report**
   - Total registrations over time
   - Pending approvals
   - Active vs inactive users
   - Registration sources

2. **Booking Analytics**
   - Total bookings system-wide
   - Bookings by pharmacy
   - Bookings by department
   - Status distribution
   - Peak booking times
   - Cancellation rates

3. **Pharmacy Performance**
   - Bookings per pharmacy
   - Average response time
   - Confirmation rates
   - User satisfaction (if implemented)

4. **System Usage**
   - Active users
   - Peak usage times
   - Geographic distribution
   - Device types (mobile vs desktop)

5. **Security Report**
   - Failed login attempts
   - Suspicious activities
   - Rate limit violations
   - Audit log summary

#### Generating Reports

1. Navigate to **Reports**
2. Select report type
3. Choose date range
4. Apply filters (if applicable)
5. Click **"Generate Report"**
6. View online or download (PDF/Excel)

#### Scheduling Automated Reports

1. Go to **Reports** → **Scheduled Reports**
2. Click **"Add Schedule"**
3. Configure:
   - Report type
   - Frequency (daily, weekly, monthly)
   - Recipients (email addresses)
   - Format (PDF/Excel)
4. Save schedule
5. Reports sent automatically

---

## Pharmacy Admin Functions

### Managing Your Pharmacy

**Access**: Pharmacy Admin for assigned pharmacy

#### Updating Pharmacy Information

1. Navigate to **My Pharmacy**
2. Click **"Edit Information"**
3. Update fields:
   - Pharmacy name
   - Location
   - Contact number
   - Email
4. Click **"Save Changes"**

**Note**: Some fields may be restricted by Super Admin

#### Viewing Pharmacy Statistics

**My Pharmacy Dashboard shows**:
- Total departments
- Active departments
- Total bookings (all-time)
- Bookings this month
- Bookings today
- Pending approvals
- Confirmed appointments today
- Average response time

---

## User Management

### Approving User Registrations

**When a new user registers**:
1. User completes registration
2. User verifies email
3. Account created with `is_active = 0`
4. Admin sees notification: "Pending Approval"

**To approve user**:
1. Navigate to **Users** → **Pending Approvals**
2. Review user information:
   - Name
   - Email
   - Civil ID
   - Mobile number
   - Assigned pharmacy
   - Registration date
3. Click **"Approve"**
4. User receives approval email
5. User can now login

**To reject user**:
1. Navigate to **Users** → **Pending Approvals**
2. Find the user
3. Click **"Reject"**
4. Enter rejection reason
5. Click **"Confirm Rejection"**
6. User receives rejection email

**Approval Checklist**:
- ✓ Verify civil ID looks valid (12 digits)
- ✓ Verify mobile number looks valid (8 digits)
- ✓ Check if representative is known to pharmacy
- ✓ Verify email domain (company email preferred)
- ✓ Check for duplicate registrations

### Managing Active Users

#### Viewing User List

1. Navigate to **Users** → **Active Users**
2. View list with:
   - Name
   - Email
   - Role
   - Pharmacy
   - Last login
   - Status

#### Searching Users

**Search options**:
- By name
- By email
- By civil ID
- By mobile number
- By pharmacy
- By role
- By registration date

#### Viewing User Details

1. Click on user name
2. View complete profile:
   - Personal information
   - Booking history
   - Login history
   - Activity log
   - Account status

#### Deactivating User Account

**Reasons to deactivate**:
- User no longer working
- Suspicious activity
- User request
- Policy violation

**To deactivate**:
1. Go to **Users**
2. Find the user
3. Click **"Deactivate"**
4. Enter reason
5. Confirm action
6. User receives notification
7. User cannot login
8. Existing bookings remain

**To reactivate**:
1. Go to **Users** → filter **"Inactive"**
2. Find the user
3. Click **"Activate"**
4. User can login again

### Resetting User Password

**Users should use "Forgot Password" themselves, but admin can reset if needed**:

1. Go to **Users**
2. Find the user
3. Click **"Reset Password"**
4. Temporary password generated and sent to user's email
5. User must change password on next login

---

## Pharmacy Management

### Pharmacy Information

**Super Admin**: Can manage all pharmacies
**Pharmacy Admin**: Can only view/edit own pharmacy

#### Pharmacy Profile Fields

- **Name**: Official pharmacy name
- **Location**: Full address or area
- **Contact Number**: 8-digit phone number
- **Email**: Contact email
- **Status**: Active/Inactive
- **Created Date**: When pharmacy was added to system
- **Total Departments**: Count of departments
- **Total Bookings**: All-time booking count

#### Pharmacy Settings

1. **Booking Settings**:
   - Allow same-day bookings (Yes/No)
   - Booking advance limit (how many days ahead)
   - Auto-confirm bookings (Yes/No)
   - Require admin approval (Yes/No)

2. **Notification Settings**:
   - Email notifications for new bookings
   - SMS notifications (if configured)
   - Notification recipients (admin emails)

3. **Working Hours**:
   - Default for new departments
   - Configurable per department

---

## Department Management

### Creating a Department

**Access**: Pharmacy Admin (for own pharmacy), Super Admin (for any pharmacy)

1. Navigate to **Departments** → **Add New Department**
2. Fill in required information:
   - **Department Name**: e.g., "Dermatology", "Cardiology"
   - **Description**: Detailed description (optional)
   - **Pharmacy**: Auto-selected for Pharmacy Admin, selectable for Super Admin
   - **Status**: Active/Inactive
3. Click **"Create Department"**
4. Department created successfully
5. Next step: Configure schedule

**Department Naming Best Practices**:
- Use clear, specific names
- Avoid abbreviations
- Use title case
- Examples:
  - ✓ "Dermatology Department"
  - ✓ "Cardiology & Heart Center"
  - ✗ "Dept 1"
  - ✗ "derm"

### Editing Department

1. Navigate to **Departments**
2. Find the department
3. Click **"Edit"**
4. Modify information
5. Click **"Save Changes"**

### Deactivating/Activating Department

**To Deactivate**:
1. Go to **Departments**
2. Find the department
3. Click **"Deactivate"**
4. Confirm action
5. **Effect**:
   - Hidden from representatives
   - No new bookings accepted
   - Existing bookings remain
   - Schedules preserved

**To Activate**:
1. Go to **Departments**
2. Filter inactive departments
3. Find the department
4. Click **"Activate"**
5. Department becomes available

### Deleting Department

**WARNING**: Deletes all schedules and bookings!

1. Go to **Departments**
2. Find the department
3. Click **"Delete"**
4. Type department name to confirm
5. Click **"Permanently Delete"**

**Cascade Effects**:
- All schedules deleted
- All bookings deleted
- Cannot be recovered

**Recommendation**: Use "Deactivate" instead

---

## Schedule Management

### Understanding Schedules

**Schedule defines**:
- Which days department is open
- Opening and closing times
- Time slot duration
- Maximum bookings per slot

**Example Schedule**:
- Monday: 9:00 AM - 5:00 PM, 30-minute slots, 1 booking per slot
- Tuesday: 9:00 AM - 5:00 PM, 30-minute slots, 1 booking per slot
- Wednesday: Closed
- Thursday: 9:00 AM - 5:00 PM, 30-minute slots, 1 booking per slot
- Friday: Closed
- Saturday: Closed
- Sunday: 10:00 AM - 2:00 PM, 30-minute slots, 1 booking per slot

### Creating a Schedule

1. Navigate to **Departments** → Select Department → **Schedules**
2. Click **"Add Schedule"**
3. Fill in information:
   - **Day of Week**: Select day (Sunday = 0, Monday = 1, ..., Saturday = 6)
   - **Start Time**: Opening time (e.g., 09:00)
   - **End Time**: Closing time (e.g., 17:00)
   - **Slot Duration**: Minutes per appointment (default: 30)
   - **Max Bookings Per Slot**: Usually 1 (can be higher if multiple reps allowed)
   - **Status**: Active/Inactive
4. Click **"Create Schedule"**

**Validation Rules**:
- End time must be after start time
- Slot duration: 15-120 minutes
- Max bookings per slot: 1-10
- Cannot overlap with existing schedules for same day

### Quick Schedule Setup (Copy Week)

**To set up same schedule for all weekdays**:
1. Create schedule for Monday
2. Click **"Copy to Other Days"**
3. Select days to copy to
4. Click **"Apply"**
5. Schedules created for selected days

### Editing Schedule

1. Go to **Departments** → Select Department → **Schedules**
2. Find the schedule
3. Click **"Edit"**
4. Modify times or settings
5. Click **"Save Changes"**

**Note**: Changes affect future bookings only, not existing ones

### Temporary Schedule Changes

**For holidays or special closures**:

1. Go to **Departments** → Select Department → **Special Days**
2. Click **"Add Special Day"**
3. Configure:
   - Date
   - Status: Closed / Custom Hours
   - If custom: Start and end time
   - Reason: "Public Holiday", "Maintenance", etc.
4. Click **"Save"**
5. No bookings accepted for that date

### Deleting Schedule

1. Go to **Schedules**
2. Find the schedule
3. Click **"Delete"**
4. Confirm deletion
5. **Effect**:
   - No bookings accepted for that day
   - Existing bookings for that day remain
   - Can recreate schedule anytime

---

## Booking Management

### Viewing Bookings

**Access**:
- Super Admin: All bookings system-wide
- Pharmacy Admin: Bookings for own pharmacy only

#### Booking Dashboard

**Shows**:
- Today's bookings
- Pending approvals
- Confirmed appointments
- Recent cancellations

#### Booking List View

1. Navigate to **Bookings**
2. View list with:
   - Representative name
   - Department
   - Date and time
   - Status
   - Created date
   - Actions

#### Filtering Bookings

**Filter options**:
- Status: Pending, Confirmed, Cancelled, Completed
- Date range
- Department
- Representative
- Pharmacy (Super Admin only)

#### Searching Bookings

**Search by**:
- Representative name
- Department name
- Booking ID
- Date

### Managing Booking Requests

#### Approving Booking

1. Go to **Bookings** → **Pending Approvals**
2. Review booking details:
   - Representative information
   - Department requested
   - Date and time
   - Notes from representative
3. Check for conflicts
4. Click **"Approve"**
5. Optionally add confirmation note
6. Click **"Confirm Approval"**
7. Representative receives confirmation email
8. Booking status changes to "Confirmed"

**Approval Checklist**:
- ✓ Verify time slot is still available
- ✓ Check if department is staffed at that time
- ✓ Confirm no conflicting appointments
- ✓ Review representative's history (no-shows)
- ✓ Read representative's notes

#### Rejecting Booking

1. Go to **Bookings** → **Pending Approvals**
2. Find the booking
3. Click **"Reject"**
4. Enter rejection reason:
   - "Time slot no longer available"
   - "Department closed on that day"
   - "Please reschedule for a different time"
5. Click **"Confirm Rejection"**
6. Representative receives rejection email with reason
7. Booking status changes to "Cancelled"

**Best Practice**: Provide clear reason so representative can book correctly next time

#### Bulk Approval

**To approve multiple bookings at once**:
1. Go to **Bookings** → **Pending Approvals**
2. Check boxes next to bookings to approve
3. Click **"Bulk Approve"**
4. Confirm action
5. All selected bookings approved

### Managing Confirmed Bookings

#### Viewing Booking Details

1. Click on any booking
2. View complete information:
   - Representative details (name, email, mobile)
   - Department and pharmacy
   - Date and time
   - Status and timestamps
   - Notes
   - History (who approved, when)

#### Cancelling Confirmed Booking (Admin-initiated)

**Reasons**:
- Department emergency closure
- Staff unavailability
- Representative request
- Duplicate booking

**To cancel**:
1. Go to **Bookings**
2. Find the booking
3. Click **"Cancel"**
4. Select reason from dropdown
5. Add detailed explanation
6. Click **"Confirm Cancellation"**
7. Representative receives cancellation email
8. Booking status changes to "Cancelled"

**Cancellation Reasons**:
- Department closed unexpectedly
- Staff emergency
- Representative requested cancellation
- Duplicate booking
- Other (specify)

#### Rescheduling Booking

**Process**:
1. Cancel existing booking (with reason)
2. Help representative create new booking
3. Or representative creates new booking themselves

**Note**: Direct rescheduling feature may be added in future updates

#### Marking Booking as Completed

**Auto-completion**:
- Bookings automatically marked as "Completed" after appointment time passes
- Runs daily at midnight

**Manual completion**:
1. Go to **Bookings**
2. Find the booking
3. Click **"Mark as Completed"**
4. Optionally add completion notes
5. Click **"Confirm"**

#### Marking as No-Show

**If representative doesn't show up**:
1. Go to **Bookings**
2. Find the booking
3. Click **"Mark as No-Show"**
4. Add notes
5. Click **"Confirm"**
6. Representative receives notification
7. No-show recorded in representative's history

**No-Show Threshold**: 3 no-shows may result in account restrictions

### Booking Reports

**Pharmacy Admin Reports**:
1. **Daily Report**: Today's appointments
2. **Weekly Report**: Upcoming week
3. **Monthly Report**: Current month statistics
4. **Department Report**: Bookings by department
5. **Representative Report**: Bookings by representative

**How to generate**:
1. Go to **Reports** → **Booking Reports**
2. Select report type
3. Choose date range
4. Apply filters
5. Click **"Generate"**
6. View online or download

---

## Reports and Analytics

### Available Reports

#### For Super Admin

1. **System Overview Report**
   - Total users, pharmacies, departments, bookings
   - Growth trends
   - System health

2. **Multi-Pharmacy Comparison**
   - Bookings by pharmacy
   - Performance metrics
   - Response times

3. **User Analytics**
   - Active vs inactive users
   - Registration trends
   - User engagement

4. **Security Report**
   - Login attempts
   - Failed authentications
   - Suspicious activities

#### For Pharmacy Admin

1. **Pharmacy Performance Report**
   - Total bookings
   - Confirmation rate
   - Cancellation rate
   - Average response time

2. **Department Utilization Report**
   - Bookings by department
   - Most/least busy departments
   - Peak times

3. **Representative Activity Report**
   - Most active representatives
   - No-show rates
   - Booking patterns

4. **Operational Report**
   - Daily/weekly/monthly bookings
   - Status distribution
   - Trends over time

### Exporting Reports

**Export formats**:
- PDF (for printing/sharing)
- Excel (for further analysis)
- CSV (for data import)

**How to export**:
1. Generate report
2. Click **"Export"** button
3. Select format
4. File downloads to your device

### Scheduling Reports

**Auto-generated reports**:
1. Go to **Reports** → **Scheduled Reports**
2. Click **"Create Schedule"**
3. Configure:
   - Report type
   - Frequency: Daily, Weekly, Monthly
   - Day/time to run
   - Recipients (email addresses)
   - Format
4. Save schedule

**Example scheduled reports**:
- Daily: Today's appointments (sent at 8 AM)
- Weekly: Week summary (sent Monday 8 AM)
- Monthly: Month-end report (sent 1st day of month)

---

## System Settings

**Access**: Super Admin only

### General Settings

1. **Application Settings**:
   - System name
   - Support email
   - Support phone
   - Timezone (default: Asia/Kuwait)
   - Date format
   - Time format

2. **Booking Settings**:
   - Default slot duration (minutes)
   - Maximum advance booking (days)
   - Minimum cancellation notice (hours)
   - Auto-confirm bookings (Yes/No)
   - Allow same-day bookings (Yes/No)

3. **User Settings**:
   - Auto-approve new users (Yes/No)
   - Require email verification (Yes/No)
   - Password expiry (days, 0 = never)
   - Session timeout (minutes)
   - Max login attempts before lockout

### Email Settings

1. **SMTP Configuration**:
   - Mail host
   - Mail port
   - Username
   - Password
   - Encryption (TLS/SSL)
   - From address
   - From name

2. **Email Templates**:
   - Welcome email
   - Email verification
   - Booking confirmation
   - Booking cancellation
   - Password reset
   - Admin notifications

**To edit email template**:
1. Go to **Settings** → **Email Templates**
2. Select template
3. Click **"Edit"**
4. Modify content (supports variables)
5. Preview template
6. Click **"Save"**

### Security Settings

1. **Password Policy**:
   - Minimum length (default: 8)
   - Require uppercase (Yes/No)
   - Require lowercase (Yes/No)
   - Require numbers (Yes/No)
   - Require special characters (Yes/No)
   - Password history (prevent reuse of last N passwords)

2. **Rate Limiting**:
   - Login attempts (default: 5 per minute)
   - Registration attempts (default: 3 per hour)
   - API requests (if applicable)

3. **Session Management**:
   - Session lifetime (minutes)
   - Concurrent sessions allowed
   - Remember me duration (days)

4. **Two-Factor Authentication** (if enabled):
   - Require for admins (Yes/No)
   - Require for all users (Yes/No)
   - Allowed methods (Email, SMS, App)

### Backup Settings

1. **Automated Backups**:
   - Enable/disable
   - Frequency (Daily, Weekly)
   - Time to run
   - Retention period (days)
   - Backup location

2. **Manual Backup**:
   - Go to **Settings** → **Backup**
   - Click **"Create Backup Now"**
   - Wait for completion
   - Download backup file

### Maintenance Mode

**To enable maintenance mode**:
1. Go to **Settings** → **Maintenance**
2. Click **"Enable Maintenance Mode"**
3. Add message for users (e.g., "System maintenance in progress")
4. Add estimated downtime
5. Click **"Enable"**
6. **Effect**: All users see maintenance page (except admins)

**To disable**:
1. Go to **Settings** → **Maintenance**
2. Click **"Disable Maintenance Mode"**
3. System becomes accessible to all users

---

## Security Management

### Monitoring Security

#### Audit Logs

**Access**:
- Super Admin: All audit logs
- Pharmacy Admin: Pharmacy-related logs only

**View audit logs**:
1. Navigate to **Security** → **Audit Logs**
2. View list of all actions:
   - User who performed action
   - Action type
   - Affected resource
   - Timestamp
   - IP address
   - Details

**Filter audit logs**:
- By user
- By action type
- By date range
- By IP address

**Audit log retention**: 90 days (configurable)

#### Failed Login Attempts

1. Go to **Security** → **Failed Logins**
2. View list of failed attempts:
   - Email/username attempted
   - IP address
   - Timestamp
   - Reason (wrong password, account not found, etc.)

**Red flags**:
- Multiple failed attempts from same IP
- Failed attempts for admin accounts
- Unusual geographic locations
- Brute force patterns

#### Active Sessions

1. Go to **Security** → **Active Sessions**
2. View all logged-in users:
   - User name and email
   - Login time
   - Last activity time
   - IP address
   - Device/browser

**Force logout**:
- Select user session
- Click **"Terminate Session"**
- User is logged out immediately

### Security Alerts

**Automatic alerts for**:
- Multiple failed login attempts
- Admin password changes
- User role changes
- Suspicious activity patterns
- System errors

**Alert delivery**:
- Email to designated admins
- Dashboard notification
- SMS (if configured)

### IP Whitelisting (Optional)

**To restrict admin access to specific IPs**:
1. Go to **Security** → **IP Whitelist**
2. Click **"Add IP Address"**
3. Enter IP address or range
4. Add description
5. Click **"Save"**
6. **Effect**: Only whitelisted IPs can access admin area

**WARNING**: Don't lock yourself out! Add your current IP first.

### Security Best Practices

1. **Use strong passwords**: Mix of uppercase, lowercase, numbers, symbols
2. **Enable 2FA**: If available
3. **Regular password changes**: Every 3-6 months
4. **Monitor audit logs**: Weekly review
5. **Review user permissions**: Quarterly audit
6. **Keep software updated**: Apply updates promptly
7. **Backup regularly**: Daily automated backups
8. **Limit admin accounts**: Only create as needed
9. **Use HTTPS**: Always use secure connection
10. **Train staff**: Security awareness

---

## Troubleshooting

### Common Issues

#### "User cannot login after approval"

**Solutions**:
1. Verify email is verified (email_verified_at not NULL)
2. Verify is_active = 1
3. Check if user is using correct email
4. Check if password was set correctly
5. Try password reset

#### "Bookings not showing available slots"

**Solutions**:
1. Check if department schedule exists
2. Verify schedule is active
3. Check if slots are already booked
4. Verify department is active
5. Check if date is within booking advance limit

#### "Emails not being sent"

**Solutions**:
1. Check SMTP settings in .env
2. Verify email credentials
3. Check spam folder
4. Test email configuration:
   ```bash
   php artisan tinker
   Mail::raw('Test', function($msg) {
       $msg->to('test@example.com')->subject('Test');
   });
   ```
5. Check Laravel logs: `storage/logs/laravel.log`

#### "Cannot upload files"

**Solutions**:
1. Check file upload permissions
2. Verify storage folder writable
3. Check max file size in php.ini
4. Check Laravel upload limits in .env

#### "Slow page load times"

**Solutions**:
1. Enable caching:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
2. Optimize database queries
3. Add indexes to frequently queried columns
4. Consider Redis for session storage
5. Enable OPcache in PHP

### Error Messages

#### "SQLSTATE[HY000] [2002] Connection refused"

**Meaning**: Cannot connect to database

**Solutions**:
1. Verify MySQL is running:
   ```bash
   docker ps
   ```
2. Check database credentials in .env
3. Verify database host and port
4. Check firewall rules

#### "419 Page Expired"

**Meaning**: CSRF token mismatch

**Solutions**:
1. Clear browser cache
2. Try incognito/private mode
3. Verify session is working
4. Check if cookies are enabled

#### "500 Internal Server Error"

**Meaning**: Server-side error

**Solutions**:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable debug mode temporarily (.env: `APP_DEBUG=true`)
3. Check file permissions
4. Check PHP error logs
5. Verify all dependencies installed

### Getting Help

1. **Check documentation**: Review relevant sections first
2. **Search error message**: Google the exact error
3. **Check logs**: Laravel, Nginx, PHP logs
4. **Contact technical support**: Provide:
   - Exact error message
   - Steps to reproduce
   - Screenshots
   - Log files
   - System information

---

## Best Practices

### Daily Tasks

- [ ] Review pending user approvals
- [ ] Review pending booking approvals
- [ ] Check today's appointments
- [ ] Monitor dashboard for alerts
- [ ] Review recent cancellations

### Weekly Tasks

- [ ] Review audit logs
- [ ] Check failed login attempts
- [ ] Review no-show reports
- [ ] Verify department schedules
- [ ] Review user feedback

### Monthly Tasks

- [ ] Generate monthly reports
- [ ] Review system performance
- [ ] Audit user accounts (remove inactive)
- [ ] Update department schedules if needed
- [ ] Review security settings
- [ ] Check backup success

### Quarterly Tasks

- [ ] Full user permission audit
- [ ] Review and update documentation
- [ ] Staff training on new features
- [ ] System health check
- [ ] Plan for upcoming changes

### Annually

- [ ] Password policy review
- [ ] Security audit
- [ ] Disaster recovery test
- [ ] User satisfaction survey
- [ ] System upgrade planning

---

**Version**: 1.0
**Last Updated**: January 2026
**Document ID**: ADMIN-GUIDE-001

For technical support: tech-support@example.com
For emergency issues: +965 XXXX XXXX
