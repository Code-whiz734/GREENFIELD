# Greenfield

Greenfield is a PHP and MySQL course registration system for students and administrators. Students can create accounts, log in, browse courses, and register for available courses. Administrators use a shared credential to manage courses, view registrations, and access an admin dashboard.

## Features

- Student registration/login
- Shared admin login
- Role-based dashboard redirects
- Course browsing and course registration
- Student view for registered courses
- Admin dashboard for course and registration management
- MySQL database schema with starter course records

## Project Structure

```text
GREENFIELD/
├── Backend/
│   ├── Config/          # Backend PHP handlers and database config
│   └── Schema/          # MySQL database schema
├── Frontend/
│   └── public/          # Public PHP pages, CSS, JS, auth, admin, and student views
└── index.php            # Redirects to Frontend/public/index.php
```

## Requirements

- PHP 7.4 or newer
- MySQL or MariaDB
- A local server environment such as XAMPP, WAMP, MAMP, or PHP's built-in server

## Setup

1. Copy or move the project folder into your web server directory.

   For XAMPP, a common location is:

   ```text
   C:\xampp\htdocs\GREENFIELD
   ```

2. Start Apache and MySQL from your local server control panel.

3. Create the database by importing:

   ```text
   Backend/Schema/greenfield.sql
   ```

   The script creates the `greenfield` database and the required tables.

4. Confirm the database connection settings in:

   ```text
   Frontend/public/includes/db.php
   Backend/Config/DB.php
   ```

   Default local settings:

   ```php
   $host = "localhost";
   $user = "root";
   $pass = "";
   $db = "greenfield";
   ```

5. Open the app in your browser:

   ```text
   http://localhost/GREENFIELD/
   ```

   If your server points directly to this folder, the root `index.php` redirects to:

   ```text
   Frontend/public/index.php
   ```

## Admin Login

All administrators use the same shared credential. Admin account registration is disabled.

Default admin login:

```text
Email: admin@greenfield.edu
Password: admin@23
```

To change the shared admin credential, update:

```text
Frontend/public/includes/config.php
Backend/Config/includes/db.php
```

The `Frontend/public/setup_admin.php` page no longer creates database users.

## Main Pages

- Home: `Frontend/public/index.php`
- Login: `Frontend/public/auth/login.php`
- Register: `Frontend/public/auth/register.php`
- Student dashboard: `Frontend/public/student/Dashboard.php`
- Student courses: `Frontend/public/student/Courses.php`
- Student registered courses: `Frontend/public/student/My_courses.php`
- Admin dashboard: `Frontend/public/admin/dashboard.php`
- Manage courses: `Frontend/public/admin/manage_courses.php`
- View registrations: `Frontend/public/admin/registrations.php`

## Notes

- The app uses PHP sessions for login state.
- Passwords are hashed for new accounts.
- Course registrations are stored in the `registrations` table.
- A student cannot register for the same course more than once because of the unique database constraint on `student_id` and `course_id`.
