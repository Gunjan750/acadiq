#AcadIQ - Student Management System

Project Overview

AcadIQ is a web-based Student Management System developed using PHP and MySQL.
It allows users to manage student records, track academic performance, and monitor attendance efficiently.
The system also integrates a Machine Learning model using Python to predict student academic performance based on attendance, marks, and study hours.

---

Features

* User registration (Register)
* User Authentication (Login/Logout)
* Add, View, and Manage Students
* Add Student Marks
* Add and Track Attendance
* Performance Dashboard with Charts (Chart.js)
* Edit and Delete Student Records
* Input Validation and Error Handling
* Performance prediction System

---

Technologies Used

* **Frontend:** HTML, CSS, JavaScript
* **Backend:** PHP
* **Database:** MySQL
* **Libraries:** Chart.js
* **Machine Learning** Python (Scikit-Learn)

---

Project Structure

* `config.php` – Database connection
* `test.php` – Error testing file
* `register.php` – User Registration
* `login.php` – User login
* `logout.php` – Logout functionality
* `dashboard.php` – Main dashboard with analytics
* `index.php` – Student listing
* `add_student.php` – Add new students
* `add_marks.php` – Add student marks
* `add_attendance.php` – Add attendance records
* `delete_student.php` – Delete functionality
* `edit_student.php` – Edit functionality
* `predict.php` – Predicts Students Performance
* `predict.py` – Handles the machine learning prediction logic for student performance analysis.
* `style.css` – Styling

---

Security Features

* Prepared Statements (Prevents SQL Injection)
* Password Hashing (`password_hash`, `password_verify`)
* Session-based Authentication
* Input Validation (Client & Server Side)

---

Database Tables

1. **students** (id, name, email, password, role)
2. **marks** (student_id, subject_id, marks)
3. **attendance** (student_id, percentage, subject_id)
4. **predictions** (student_id, prediction, attendance, marks, created_at, study_hours)
5. **subjects** (id, subject_name)

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

* Improved UI/UX design
* AI-based performance analytics
* Email notifications
* Export reports (PDF/Excel)
* Advanced search and filtering
* Mobile responsive dashboard

---

Author

Developed as part of BCA Mini Project (II year)

---

Conclusion

AcadIQ simplifies student data management by providing an organized and user-friendly system to track academic performance and attendance.

---
