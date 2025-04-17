# library-coursework
A simple web application for a fictional library (PHP, MySQL, Bootstrap)

This is a web-based library management system that supports both admin and user roles. Admins can manage books, provide arrivals, and view analytics. Users can browse books and borrow them.

##  Features

- User authentication (admin and regular users)
- Book browsing with filtering and searching
- Borrowing and return tracking with expected return dates
- Admin panel for managing books and arrivals
- Analytics dashboard with charts (popular genres, top readers, top books)
- Responsive layout using Bootstrap

##  Tech Stack

- **Frontend**: HTML, CSS, JavaScript (Fetch API, Chart.js)
- **Backend**: PHP (with PDO and named parameters)
- **Database**: MySQL

##  Setup Instructions

1. Clone this repository:
   Create a MySQL database and import the SQL schema and data from
   https://github.com/GannaUK/library-coursework/tree/main/!Docs

3. Important: Create the file includes/config.php with your own database credentials.
The file should look like this:

## <?php
## $DB_HOST = 'your_host';
## $DB_NAME = 'your_database';
## $DB_USER = 'your_user';
## $DB_PASS = 'your_password';

Make sure your server (e.g., XAMPP, MAMP, LAMP) has PHP and MySQL enabled.
Open index.php in the browser to get started.


