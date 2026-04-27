#AcadIQ - Student Management System

Project Overview

AcadIQ is a web-based Student Management System developed using PHP and MySQL.
It allows users to manage student records, track academic performance, and monitor attendance efficiently.

---

Features

* User Authentication (Login/Logout)
* Add, View, and Manage Students
* Add Student Marks
* Add and Track Attendance
* Performance Dashboard with Charts (Chart.js)
* Edit and Delete Student Records
* Input Validation and Error Handling

---

Technologies Used

* **Frontend:** HTML, CSS, JavaScript
* **Backend:** PHP
* **Database:** MySQL
* **Libraries:** Chart.js

---

Project Structure

* `config.php` – Database connection
* `login.php` – User login
* `logout.php` – Logout functionality
* `dashboard.php` – Main dashboard with analytics
* `index.php` – Student listing
* `add_student.php` – Add new students
* `add_marks.php` – Add student marks
* `add_attendance.php` – Add attendance records
* `style.css` – Styling

---

Security Features

* Prepared Statements (Prevents SQL Injection)
* Password Hashing (`password_hash`, `password_verify`)
* Session-based Authentication
* Input Validation (Client & Server Side)

---

Database Tables

1. **students** (id, name, email, password)
2. **marks** (student_id, subject_id, marks)
3. **attendance** (student_id, percentage)

---

How to Run the Project

1. Install XAMPP/WAMP
2. Place project folder in `htdocs`
3. Start Apache and MySQL
4. Create database: `acadiq_db`
5. Import required tables
6. Open browser and go to:
   `http://localhost/acadiq/login.php`

---

Future Enhancements

* Role-based login (Admin/Student)
* Improved UI/UX design
* Search and filter functionality
* Export reports (PDF/Excel)

---

Author

Developed as part of BCA Mini Project (II year)

---

Conclusion

AcadIQ simplifies student data management by providing an organized and user-friendly system to track academic performance and attendance.

---
