Hotel and Restaurant Review Management System (CRMS)
Table of Contents
Introduction
Features
Technologies Used
Installation
Database Setup
Usage
Project Structure
Important Files
Troubleshooting
Contributing
License
Introduction
This project is a Hotel and Restaurant Review Management System (CRMS) designed to manage customer feedback and reviews for hotels and restaurants. Admins can view customer reviews, manage review questions, and send public review links via email or WhatsApp. The system also provides options to display and manage the status of hotels or restaurants, such as active, suspended, or terminated.

Features
Admin login and management.
Hotels and restaurants can be categorized as active, suspended, or terminated.
View and manage customer reviews.
Send public review links via email and WhatsApp.
Copy the public review link for easy sharing.
Display average ratings for each question.
Modal display for suspended accounts.
User role management (useradmin).
Technologies Used
Backend: PHP
Frontend: HTML, CSS, Bootstrap, JavaScript (with jQuery for some interactions)
Database: MySQL
Mailer: PHP mail() function for sending emails
Icons: Font Awesome
Installation
Prerequisites
PHP: >= 7.4
MySQL: >= 5.7
Web Server: Apache/Nginx
Composer: Optional (for managing dependencies)
Steps
Clone this repository to your local machine:

bash
Copy code
git clone https://github.com/yourusername/crms.git
cd crms
Configure your database by editing the db_connection.php file:

php
Copy code
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crms";  // Update this to match your database name
Start your local server and MySQL. For example, if using PHP's built-in server, run:

bash
Copy code
php -S localhost:8000
Ensure your web server points to the public directory if using Apache or Nginx.

Database Setup
Create a new MySQL database:

sql
Copy code
CREATE DATABASE crms;
Import the database schema from the provided crms.sql file:

bash
Copy code
mysql -u root -p crms < crms.sql
Configure the database connection in db_connection.php:

php
Copy code
$conn = new mysqli("localhost", "root", "your_password", "crms");
Run migrations if necessary (optional if using a framework).

Usage
Login as Admin:

Navigate to http://localhost/crms/login.php.
Enter your admin credentials (default admin credentials can be set up via the database).
View Reviews:

Once logged in, the admin can view customer reviews and feedback left for the respective hotel/restaurant.
Send Public Review Link:

On the admin dashboard, you can enter an email address or a WhatsApp number to send a review link.
The system uses the mail() function for email and WhatsApp Web API for WhatsApp.
Copy Review Link:

The admin can copy the public review link directly using the "Copy" button next to the link.
Check Average Ratings:

View average ratings for specific review questions on the dashboard.
Account Status:

Admin can see the account status of the hotel/restaurant (active, suspended, terminated). Suspended accounts trigger a modal with options to log out.
Project Structure
php
Copy code
crms/
│
├── assets/
│   ├── css/
│   ├── js/
│   ├── images/
│
├── db/
│   ├── crms.sql                # SQL dump for the database
│
├── public/
│   ├── index.php               # Public landing page
│   ├── login.php               # Admin login page
│   ├── public_review.php       # Public review form for customers
│
├── includes/
│   ├── header.php              # Common header for the app
│   ├── footer.php              # Common footer for the app
│   ├── db_connection.php       # Database connection file
│
├── admin/
│   ├── dashboard.php           # Admin dashboard
│   ├── logout.php              # Logout functionality
│
└── README.md                   # Documentation
Important Files
db_connection.php: Configures the database connection.
dashboard.php: The main admin page where reviews and public review links are managed.
public_review.php: Page where customers submit their reviews.
login.php: Admin login functionality.
Troubleshooting
Common Issues
Database Connection Errors:

Ensure that your db_connection.php file is correctly configured with your MySQL credentials.
Check if MySQL is running.
Email Not Sending:

Ensure that your server supports the PHP mail() function.
Verify email server settings if sending from a remote server.
WhatsApp Links Not Working:

Ensure that the WhatsApp Web API link is correctly formatted with a valid country code and phone number.
Copy to Clipboard Not Working:

Make sure your browser supports the Clipboard API.
Test on HTTPS or localhost.
