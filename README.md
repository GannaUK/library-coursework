
# library-coursework  
A simple web application for a fictional library built with PHP, MySQL, and Bootstrap.

This is a web-based library management system that supports both admin and user roles. Admins can manage books, provide arrivals, and view analytics. Users can browse books and borrow them.

## Features

- User authentication (admin and regular users)
- Book browsing with filtering and searching
- Borrowing and return tracking with expected return dates
- Admin panel for managing books and arrivals
- Analytics dashboard with charts (popular genres, top readers, top books)
- Responsive layout using Bootstrap

## Tech Stack

- **Frontend**: HTML, CSS, JavaScript (Fetch API, Chart.js)
- **Backend**: PHP (with PDO and named parameters)
- **Database**: MySQL

## Setup Instructions

1. Clone this repository:
   ```
   git clone https://github.com/GannaUK/library-coursework.git
   cd library-coursework
   ```

2. Create a MySQL database and import the SQL schema and data from:  
   [`!Docs` folder](https://github.com/GannaUK/library-coursework/tree/main/!Docs)

3. Create the file `includes/config.php` with your database credentials.  
   The file should look like this:

   ```php
   <?php
   $DB_HOST = 'your_host';
   $DB_NAME = 'your_database';
   $DB_USER = 'your_user';
   $DB_PASS = 'your_password';
   ```

4. Make sure your local server (e.g., XAMPP, MAMP, LAMP) has PHP and MySQL enabled.

5. Open `index.php` in your browser to get started.

## Admin Credentials

You can create an admin user directly in the database or use the seed data provided.
