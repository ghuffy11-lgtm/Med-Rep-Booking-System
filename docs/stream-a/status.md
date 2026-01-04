Stream A – Foundation & Core Engine

Status Document (Living File)

📌 Purpose

This document records what is already completed for Stream A and what remains, so other streams (B & C) and future contributors have a single source of truth.

This file does not contain code.
All source code lives in Git.

🧱 Project Context (Confirmed)

Project: Pharmacy Representative Booking System

Framework: Laravel 10.50.0
PHP: 8.2
Database: MySQL 8.0
Web Server: Nginx
Environment: Docker (Development)
Timezone: Asia/Kuwait
Ports:
App: 8085
phpMyAdmin: 8086


📂 Final Project Structure (Confirmed)


/mnt/repo/pharmacy-booking-system/
├── docker/                  # Docker configs only
│   ├── nginx/
│   │   └── default.conf
│   └── php/
│       └── Dockerfile
├── docker-compose.yml
├── src/                     # Laravel application root
│   ├── app/
│   ├── database/
│   ├── public/
│   ├── routes/
│   ├── artisan
│   └── .env
└── docs/
    └── stream-a/


🔒 Rule:

Docker files stay in project root
Laravel lives only inside /src

✅ Completed Work (As of Today)
🐳 Docker & Environment

Docker Compose running successfully
PHP-FPM, Nginx, MySQL, phpMyAdmin containers operational
Volume mapping corrected (./src → /var/www/html)
Nginx correctly serving /public
Laravel accessible at http://<host>:8085

🚀 Laravel Installation
Laravel 10.50.0 installed
artisan accessible inside container
.env exists and configured
APP_KEY generated
Base routes confirmed working

🗃️ Database

MySQL container running
Database pharmacy_db created
Laravel default migrations present
Migration system functional

🧩 Models

Core model files created under:
src/app/Models

Models are ready to be wired with relationships and logic
No models deleted from default Laravel install

🧠 Business Rules (Locked)

Two independent slot pools:

Pharmacy: 10 slots/day
Non-pharmacy: 20 slots/day

Cooldown: 14 days after APPROVED booking
New booking allowed on 15th day
Cooldown applies only to approved bookings
Pending booking blocks all new bookings
Representatives can cancel pending only
Admins can cancel pending or approved (with reason)
Default booking days: Tuesday & Thursday
Slot times configurable per department type


🔧 Command Convention (Mandatory)

All commands must be written as:

docker exec pharmacy_php <command>


Paths inside container:

/var/www/html/   → Laravel root

⏭️ Next Work (Stream A)
To Be Implemented

Core services:
BookingService
SlotGeneratorService
CooldownCalculatorService
ValidationRuleService
AuditLogService
Middleware:
Role checks
Audit logging
Policies:
BookingPolicy
DepartmentPolicy
Seeders
Tests (unit + feature)
API & Service documentation

🚫 Stream Boundaries

Stream A: Infrastructure, models, services, rules

Stream B: Controllers, routing, request validation

Stream C: Views, UI, Blade templates

Stream A must not modify:

Controllers
Blade views
Frontend logic

🔄 Change Management

Any Stream A change that affects Streams B or C must create:

docs/changes/CHANGE-XXX.md

🟢 Status Summary

Stream A foundation is stable and ready for core logic implementation.




🧠 Stream A – Core Engine & Security Layer (Completed)
Services (Core Business Logic)

✅ BookingService

Central booking orchestration service

Enforces critical business rules:

Rule 1: Separate pharmacy / non-pharmacy slot pools

Rule 2: Global 14-day cooldown (15th day allowed, approved bookings only)

Rule 3: Single pending booking per representative

Rule 4: Admin vs representative cancellation rules

Rule 5: Allowed booking days (default Tue/Thu)

Rule 6: Department schedule overrides

Handles:

Booking creation

Approval, rejection, cancellation

Slot availability & daily limits

Transaction safety & race condition protection

✅ CooldownCalculatorService

Calculates cooldown based on appointment date

Ignores rejected/cancelled bookings

Supports cooldown removal on admin cancellation

✅ SlotGeneratorService

Generates time slots based on:

Global configuration

Pharmacy vs non-pharmacy pools

Time ranges are fully configurable

✅ ValidationRuleService

Centralized reusable validation rules

Used by services to ensure consistency

✅ AuditLogService

Writes structured audit records

Tracks:

Actor

Action

Old vs new values

Contextual metadata

Middleware

✅ CheckRole

Role-based route protection

Supported roles:

super_admin

pharmacy_admin

representative

✅ LogAudit

Automatically logs request-level activity

Integrates with audit_logs table

Policies

✅ BookingPolicy

Authorization for:

Create

Approve

Reject

Cancel

✅ DepartmentPolicy

Department management permissions

✅ SchedulePolicy

Schedule and closure permissions

Validation Status

✔ All service, middleware, and policy files:

Pass php -l

Load correctly in Laravel

Instantiate without errors

✔ No parse errors or missing dependencies



Next Step

➡️ Seeders

Roles & permissions
Users (admin + representatives)
Departments (pharmacy & non-pharmacy)
Global slot configuration
Sample bookings for functional testing
