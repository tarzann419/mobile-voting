# Voting System Testing Guide

## Overview

This Laravel voting system has been seeded with comprehensive test data for all user roles. All test accounts use the password `password`.

## Test Accounts

### System Administrators

-   **admin@voteapp.com** - System Administrator
-   **admin2@voteapp.com** - Sarah Johnson

### Organization Administrators

-   **john.anderson@university.edu** - University Student Union
-   **maria.rodriguez@techpro.org** - Tech Professionals Association
-   **david.kim@sportscomm.org** - Community Sports League
-   **emily.chen@healthworkers.org** - Healthcare Workers Union
-   **robert.thompson@localbiz.com** - Local Business Chamber

### Sample Voters

-   **betty.allen.1@university.edu** - University Student Union
-   **jason.thomas.2@university.edu** - University Student Union
-   **deborah.hernandez.3@university.edu** - University Student Union

## Current Test Data Stats

-   **Organizations:** 5
-   **Total Users:** 112
-   **Elections:** 11 (various states: draft, published, active, completed)
-   **Positions:** 22
-   **Candidates:** 103
-   **Voter Accreditations:** 129
-   **Votes Cast:** 82

## Testing Scenarios

### 1. System Admin Dashboard

Login with `admin@voteapp.com` to:

-   View system-wide statistics
-   Manage organizations
-   Monitor system activities
-   Access admin-only features

### 2. Organization Admin Dashboard

Login with any organization admin (e.g., `john.anderson@university.edu`) to:

-   Manage elections for their organization
-   View election statistics
-   Handle voter accreditations
-   Monitor voting progress

### 3. Voter Dashboard

Login with any voter account to:

-   View available elections
-   See voting history
-   Participate in active elections
-   Check accreditation status

## Election Types Created

1. **University Student Union:** Student Council, Faculty Senate elections
2. **Tech Professionals:** Board elections, leadership voting
3. **Sports League:** Committee selections, board positions
4. **Healthcare Union:** Representative elections, policy voting
5. **Business Chamber:** Board positions, leadership roles

## Features to Test

### Registration & Authentication

-   Organization registration
-   User login/logout
-   Role-based access control
-   Dashboard routing based on user roles

### Election Management

-   Election creation and status management
-   Position and candidate management
-   Voter accreditation workflow
-   Real-time voting statistics

### Voting Process

-   Voter registration for elections
-   Voting interface
-   Vote verification and security
-   Results calculation

## Server Commands

### Start Development Server

```bash
php artisan serve
```

### Reset Database with Fresh Test Data

```bash
php artisan migrate:fresh --seed
```

### Build Frontend Assets

```bash
npm run build
```

### Access Application

-   **URL:** http://127.0.0.1:8000
-   **Default Password:** `password` (for all test accounts)

## Next Steps for Development

1. Implement real-time voting updates
2. Add email verification system
3. Integrate payment gateway for organization subscriptions
4. Add candidate photo upload functionality
5. Implement advanced voting methods (ranked choice, etc.)
6. Add comprehensive audit logging
7. Set up production deployment configuration

## Database Models Covered

-   ✅ Organizations (with subscription tiers)
-   ✅ Users (multi-role: admin, org_admin, voter)
-   ✅ Elections (with realistic timelines)
-   ✅ Positions (election-specific roles)
-   ✅ Candidates (with bios and manifestos)
-   ✅ Voter Accreditations (registration workflow)
-   ✅ Votes (with security hashing)

## Architecture Features Implemented

-   ✅ Clean role-based architecture
-   ✅ Multitenant organization structure
-   ✅ Comprehensive database seeding
-   ✅ Middleware-based access control
-   ✅ Modern Laravel best practices
-   ✅ Responsive UI with Tailwind CSS
-   ✅ Component-based view structure

## Troubleshooting

### Environment Variable Issues

If Laravel keeps trying to use SQLite despite `.env` being set to MySQL:

1. **Check for system-level environment variables:**

    ```bash
    env | grep DB_CONNECTION
    ```

2. **If a system variable exists, unset it:**

    ```bash
    unset DB_CONNECTION
    ```

3. **Clear all Laravel caches:**

    ```bash
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    php artisan view:clear
    ```

4. **Verify Laravel is reading the correct configuration:**
    ```bash
    php artisan tinker --execute="echo 'DB Connection: ' . env('DB_CONNECTION') . PHP_EOL;"
    ```

### Migration Order Issues

If you get foreign key constraint errors during migration:

-   Ensure the `organizations` table migration runs before the `users` table migration
-   The current setup has the correct order with organizations as `0001_01_01_000000`

### Cache Configuration

The system uses database-based caching by default. If you encounter cache-related issues:

-   Temporarily set `CACHE_STORE=file` in `.env`
-   Clear caches and test
-   Switch back to `CACHE_STORE=database` once database issues are resolved
