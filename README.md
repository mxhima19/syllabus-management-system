# Syllabus Management System

A web-based system for managing course syllabi, allowing staff to create and edit syllabi, and students to view them.

## Prerequisites

- XAMPP (Apache + MySQL)
- Web Browser (Chrome, Firefox, or Edge recommended)

## Installation Steps

1. **Install XAMPP**
   - Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Install XAMPP on your system
   - Make sure to install Apache and MySQL components

2. **Set up Project Files**
   - Navigate to `C:\xampp\htdocs\`
   - Create a new folder named `syllabus_management`
   - Copy all project files into this folder maintaining the following structure:
   ```
   C:\xampp\htdocs\syllabus_management\
   ├── backend\
   │   ├── api\
   │   │   ├── courses.php
   │   │   ├── auth.php
   │   │   └── departments.php
   │   ├── config\
   │   │   └── database.php
   │   └── database.sql
   ├── index.html
   ├── staff-dashboard.html
   └── student-dashboard.html
   ```

3. **Database Setup**
   - Start XAMPP Control Panel
   - Start Apache and MySQL services
   - Open your web browser
   - Go to `http://localhost/phpmyadmin`
   - Create a new database named `syllabus_management`
   - Import the `database.sql` file from the backend folder

## Running the Application

1. **Start Services**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services
   - Both services should show green status

2. **Access the Website**
   - Open your web browser
   - Go to `http://localhost/syllabus_management/`
   - This will open the login page

## Login Credentials

### Staff Login
- Email: staff@bmsit.in
- Password: staff123

### Student Login
- Email: student@bmsit.in
- Password: student123

## Features

### Staff Dashboard
- Add new syllabus
- Edit existing syllabus
- View all syllabi
- Manage course topics
- Filter by branch and semester

### Student Dashboard
- View available courses
- Filter by branch and semester
- View course details and topics
- Access recommended NPTEL courses

## Troubleshooting

1. **Database Connection Issues**
   - Ensure MySQL service is running
   - Check database credentials in `backend/config/database.php`
   - Verify database name is `syllabus_management`

2. **Page Not Loading**
   - Ensure Apache service is running
   - Check if files are in correct location
   - Clear browser cache and reload

3. **Login Issues**
   - Verify correct login credentials
   - Check if database tables are properly imported
   - Ensure backend API is accessible

## Support

For any issues or questions, please contact:
- Email: principal@bmsit.in
- Phone: 080-68730444

## Quick Start Script

To quickly start the application, you can create a batch file:

1. Create a new file named `start_syllabus.bat`
2. Add the following content:
```batch
@echo off
echo Starting Syllabus Management System...
start "" "C:\xampp\xampp-control.exe"
timeout /t 5
start "" "http://localhost/syllabus_management/"
echo System started! Please wait for XAMPP to load...
```

3. Save the file and double-click to run

## Security Notes

- Change default passwords after first login
- Keep XAMPP updated
- Regularly backup the database
- Do not expose the system to public networks without proper security measures 