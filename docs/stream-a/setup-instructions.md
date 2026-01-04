# Stream A – Infrastructure & Core Engine Setup Instructions

This document defines the **authoritative setup and correction steps** for Stream A, based on the *current, already-completed Docker + Laravel installation*.

---

## 1. Project Structure (FINAL – CONFIRMED)

```
pharmacy-booking-system/
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   └── php/
│       └── Dockerfile
├── docker-compose.yml
├── src/                     # Laravel application root
│   ├── app/
│   ├── artisan
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── tests/
│   └── .env
└── README.md
```

📌 **Important**

* Docker config lives at project root
* Laravel lives entirely inside `/src`
* All future work MUST respect this structure

---

## 2. Docker Volume Mapping (CRITICAL)

### PHP Container

```yaml
volumes:
  - ./src:/var/www/html
```

### Nginx Container

```yaml
volumes:
  - ./src:/var/www/html
  - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
```

📌 Both containers must point to `/var/www/html` → `/src`

---

## 3. Laravel Status Verification (ALREADY DONE)

### Confirm Laravel Installed

```bash
docker exec -it pharmacy_php php /var/www/html/artisan --version
```

Expected:

```
Laravel Framework 10.x
```

### Confirm Routes Load

```bash
docker exec -it pharmacy_php php /var/www/html/artisan route:list
```

If this works, Laravel is correctly installed.

---

## 4. Environment Configuration

### Active .env Location

```
/src/.env
```

### Required DB Settings

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=pharmacy_db
DB_USERNAME=pharmacy_user
DB_PASSWORD=pharmacy_pass
```

📌 `DB_HOST` **must be `mysql`**, not `127.0.0.1`

---

## 5. Database Connectivity – VERIFIED METHOD

### Correct Test (Laravel-side)

```bash
docker exec -it pharmacy_php php /var/www/html/artisan migrate:status
```

If you see:

```
Migration table not found
```

✅ This means DB connection is WORKING

---

## 6. Initial Migration Execution

Run once on fresh install:

```bash
docker exec -it pharmacy_php php /var/www/html/artisan migrate
```

This creates:

* `migrations` table
* Laravel base schema

---

## 7. Permissions (Host-side ONLY)

Run on host (not container):

```bash
sudo chown -R 1000:1000 src
```

Container permissions:

```bash
docker exec -it pharmacy_php chmod -R 775 storage bootstrap/cache
```

---

## 8. What NOT To Do ❌

* ❌ Do NOT run `composer create-project` again
* ❌ Do NOT install Laravel in `/var/www/html` root
* ❌ Do NOT change volume paths
* ❌ Do NOT test MySQL using `localhost`

---

## 9. Stream A Development Scope (NEXT WORK)

Stream A now proceeds with:

1. Database migrations (7 tables)
2. Eloquent models + relationships
3. Core services (BookingService, SlotGeneratorService, etc.)
4. Policies, middleware, and guards
5. Seeders and factories
6. API contract documentation for Streams B & C

---

## 10. Change Management Rule

Any Stream A change impacting other streams must include:

```
docs/changes/CHANGE-XXX.md
```

---

## 11. Handover Confirmation Checklist

Before notifying Streams B & C:

* [ ] `docker-compose up -d` works
* [ ] `php artisan migrate` works
* [ ] Laravel accessible at [http://localhost:8085](http://localhost:8085)
* [ ] All configs documented

---

✅ **This document is now the single source of truth for Stream A setup.**
# Stream A – Infrastructure & Core Engine Setup Instructions

This document defines the **authoritative setup and correction steps** for Stream A, based on the *current, already-completed Docker + Laravel installation*.

---

## 1. Project Structure (FINAL – CONFIRMED)

```
pharmacy-booking-system/
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   └── php/
│       └── Dockerfile
├── docker-compose.yml
├── src/                     # Laravel application root
│   ├── app/
│   ├── artisan
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── tests/
│   └── .env
└── README.md
```

📌 **Important**

* Docker config lives at project root
* Laravel lives entirely inside `/src`
* All future work MUST respect this structure

---

## 2. Docker Volume Mapping (CRITICAL)

### PHP Container

```yaml
volumes:
  - ./src:/var/www/html
```

### Nginx Container

```yaml
volumes:
  - ./src:/var/www/html
  - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
```

📌 Both containers must point to `/var/www/html` → `/src`

---

## 3. Laravel Status Verification (ALREADY DONE)

### Confirm Laravel Installed

```bash
docker exec -it pharmacy_php php /var/www/html/artisan --version
```

Expected:

```
Laravel Framework 10.x
```

### Confirm Routes Load

```bash
docker exec -it pharmacy_php php /var/www/html/artisan route:list
```

If this works, Laravel is correctly installed.

---

## 4. Environment Configuration

### Active .env Location

```
/src/.env
```

### Required DB Settings

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=pharmacy_db
DB_USERNAME=pharmacy_user
DB_PASSWORD=pharmacy_pass
```

📌 `DB_HOST` **must be `mysql`**, not `127.0.0.1`

---

## 5. Database Connectivity – VERIFIED METHOD

### Correct Test (Laravel-side)

```bash
docker exec -it pharmacy_php php /var/www/html/artisan migrate:status
```

If you see:

```
Migration table not found
```

✅ This means DB connection is WORKING

---

## 6. Initial Migration Execution

Run once on fresh install:

```bash
docker exec -it pharmacy_php php /var/www/html/artisan migrate
```

This creates:

* `migrations` table
* Laravel base schema

---

## 7. Permissions (Host-side ONLY)

Run on host (not container):

```bash
sudo chown -R 1000:1000 src
```

Container permissions:

```bash
docker exec -it pharmacy_php chmod -R 775 storage bootstrap/cache
```

---

## 8. What NOT To Do ❌

* ❌ Do NOT run `composer create-project` again
* ❌ Do NOT install Laravel in `/var/www/html` root
* ❌ Do NOT change volume paths
* ❌ Do NOT test MySQL using `localhost`

---

## 9. Stream A Development Scope (NEXT WORK)

Stream A now proceeds with:

1. Database migrations (7 tables)
2. Eloquent models + relationships
3. Core services (BookingService, SlotGeneratorService, etc.)
4. Policies, middleware, and guards
5. Seeders and factories
6. API contract documentation for Streams B & C

---

## 10. Change Management Rule

Any Stream A change impacting other streams must include:

```
docs/changes/CHANGE-XXX.md
```

---

## 11. Handover Confirmation Checklist

Before notifying Streams B & C:

* [ ] `docker-compose up -d` works
* [ ] `php artisan migrate` works
* [ ] Laravel accessible at [http://localhost:8085](http://localhost:8085)
* [ ] All configs documented

---

✅ **This document is now the single source of truth for Stream A setup.**
