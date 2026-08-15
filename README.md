# Avalon Solutions Ltd - Caregiver & Patient Management System

A comprehensive management system for healthcare facilities, built with Laravel 13, AdminLTE 3, and Spatie Permissions.

## Features

### Authentication & Authorization
- Role-based access control (RBAC)
- Three roles: Super Admin, Admin, Accountant
- User status management
- Profile management with password change

### Modules

#### Caregivers Management
- Full CRUD operations
- Photo upload support
- Next of kin information
- Status tracking

#### Patients Management
- Full CRUD operations
- Patient status tracking (On Ward, Transferred, Discharged)
- Caregiver assignment
- Admission date tracking

#### Attendance Tracking
- Daily checkups
- Complaint reporting
- Follow-up notes
- Caregiver & patient linking

#### Finance - Payments
- Auto-calculated balance
- Partial and full payments
- Payment history
- Payment date tracking

#### Finance - Expenses
- Expense categories (Salaries, Supplies, Utilities, Maintenance, Transport, Food, Medical, Other)
- Expense tracking
- Notes support

### Dashboard
- Role-specific statistics
- 6-month Payments & Expenses trend chart
- User role distribution pie chart
- Recent patients and attendance tables
- Today/Week/Month summaries

### Notifications
- Internal notification system
- Notification bell in navbar
- Auto-refresh notifications
- Alerts for complaints and payments

## Tech Stack

- **Framework:** Laravel 13
- **Admin Panel:** AdminLTE 3
- **Permissions:** Spatie Permissions
- **Charts:** Chart.js
- **Database:** MySQL

## Installation

1. Clone the repository
```bash
git clone https://github.com/mycosoft/avalon-solutions-ltd.git
```

2. Install dependencies
```bash
composer install
npm install
```

3. Create environment file
```bash
cp .env.example .env
```

4. Generate application key
```bash
php artisan key:generate
```

5. Configure database in `.env`
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=avalon_solutions
DB_USERNAME=root
DB_PASSWORD=
```

6. Run migrations
```bash
php artisan migrate
```

7. Seed initial data (optional)
```bash
php artisan db:seed
```

8. Start the development server
```bash
php artisan serve
```

## Default Credentials

After seeding:
- **Super Admin:** superadmin@avalon.com / password
- **Admin:** admin@avalon.com / password
- **Accountant:** accountant@avalon.com / password

## Roles & Permissions

| Role | Permissions |
|------|------------|
| Super Admin | Full system access, manage users, roles, permissions |
| Admin | Manage caregivers, patients, attendance, view reports |
| Accountant | Manage payments, expenses, view financial reports |

## Project Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── Admin/           # User, Role, Permission controllers
│       ├── Attendance/      # Attendance management
│       ├── Caregiver/       # Caregiver CRUD
│       ├── Dashboard/       # Dashboard stats
│       ├── Finance/
│       │   ├── Expense/    # Expense management
│       │   └── Payment/    # Payment management
│       ├── Patient/        # Patient CRUD
│       └── Profile/        # Profile management
├── Models/
│   ├── Attendance.php
│   ├── Caregiver.php
│   ├── Expense.php
│   ├── Patient.php
│   ├── Payment.php
│   ├── User.php
│   └── UserNotification.php
└── Providers/
    └── AppServiceProvider.php
```

## Dashboard Preview

The dashboard includes:
- Financial statistics cards (Collections, Expenses)
- Key metrics info boxes (Caregivers, Patients, Attendance)
- 6-month trend line chart
- User role distribution pie chart
- Summary cards with Today/Week/Month breakdowns
- Recent records tables

## License

This project is proprietary software for Avalon Solutions Ltd.
