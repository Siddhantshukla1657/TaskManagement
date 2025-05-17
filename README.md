# ✅ Task Management App

A powerful **Task Management System** built using **HTML, CSS, PHP, and MySQL**, designed with a three-level authentication system: **Super Admin**, **Admin**, and **Member**. The app is tailored to handle task creation, assignment, status tracking, and performance reporting within teams.

---

## 🔐 User Roles

### 🟣 Super Admin
- Full access to all modules and data
- Can:
  - Create admins and members
  - Assign/reassign tasks to any user
  - View all tasks (pending, ongoing, completed)
  - View performance reports across the system

### 🔵 Admin
- Team-level management rights
- Can:
  - Create teams and manage their members
  - Add and assign tasks to members within their team
  - Track task progress and completion status
  - View team-specific reports

### 🟢 Member
- Task execution and tracking interface
- Can:
  - View assigned tasks with details
  - See who assigned the task
  - Update task status:
    - Not Yet Started
    - Started
    - Midway
    - Completed
    - Issue

---

## 🧰 Features

- 🔐 Multi-level authentication system
- 📝 Task creation and assignment by Admin/Super Admin
- 📊 Dashboard-based status views for each role
- ✅ Real-time status updates from members
- 📈 Completion tracking and team/user reports
- 👥 Role-based access to data and actions
- 📁 Clean, organized user interface

---

## 🚀 Getting Started

### 🔧 Prerequisites

- PHP >= 7.x
- MySQL or MariaDB
- Apache or any compatible server
- Git (optional)

### ⚙️ Setup

1. Clone the repository:
    ```bash
    git clone https://github.com/Siddhantshukla1657/TaskManagement.git
    cd task-management-app
    ```

2. Import the SQL database:
    - Use `phpMyAdmin` or MySQL CLI to import `database.sql` file.

3. Configure the database:
    - Edit your `db.php` or equivalent:
      ```php
      $host = "localhost";
      $username = "root";
      $password = "";
      $database = "task_management";
      ```

4. Launch the app in your browser:
    ```
    http://localhost/task-management-app/
    ```

---

## 🔐 Login Credentials (Sample)

| Role         | Username   | Password  |
|--------------|------------|-----------|
| Super Admin  | superadmin | super123  |
| Admin        | admin1     | admin123  |
| Member       | member1    | member123 |

> Update or remove before production.

---