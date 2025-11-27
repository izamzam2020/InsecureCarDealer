# InsecureCarDealer — Intentionally Vulnerable Car Dealership Web App

**InsecureCarDealer** is a deliberately insecure web application built to support web application security practice: vulnerability scanning, bug discovery, remediation, and secure coding exercises. The app simulates a small car dealership website and contains a range of intentionally implemented weaknesses for learning and testing in safe, isolated environments.

---

## ⚠️ Important Legal & Safety Notice

This project is intentionally insecure. **Do not** deploy it on public networks without proper isolation (use a local VM, private network, or an isolated Docker environment). Only test this application on systems and environments you own or have explicit permission to test. The author is not responsible for misuse.

---

## Purpose

- Provide a realistic, small-scale web application that contains common security weaknesses.  
- Provide a concise demo environment to show practical pentesting findings, proof-of-concepts, and remediation steps.  
- Serve as a teaching tool for developers and security trainees to practice scanning, exploitation, and code-level fixes.

---

## Key Features

- Simple car marketplace UI (listings, search, add/edit listing, contact form)  
- User authentication (registration, login, role-based pages)  
- Admin panel for inventory and user management  
- File upload for car images  
- REST-like endpoints for AJAX functionality

---

## Intentionally Included Vulnerabilities

The following weaknesses are implemented intentionally for learning purposes. Each item is included to support hands-on discovery and remediation exercises.

- **SQL Injection (SQLi)** 
- **Cross-Site Scripting (XSS)**
- **Cross-Site Request Forgery (CSRF)**
- **Insecure Direct Object References (IDOR)**
- **Broken Authentication / Weak Password Storage**
- **Insecure File Upload**
- **Open Redirects**
- **Verbose Error Handling / Sensitive Data Exposure**
- **Business Logic Flaws**

---
## Setup
On your local machine, clone the repository
```git clone https://github.com/izamzam2020/InsecureCarDealer.git```

For my local environment, I'm using XAMPP, which may mean the following steps aren't identical to yours, BUT the principles are the same.

If you are using XAMPP, open the XAMPP program and click "Start" on **Apache** and **MySQL**. We will need both of these services running.

Using **phpMyAdmin** or any other tool you wish to use to connect to your local database, create a new database called **car_shop**. You may call the database anything you like, but you must remember the name of the database when setting up the connection.

Now that the database has been created, open the project directory where you cloned the repository and navigate to **includes/config.php**. This is where you will enter your MySQL credentials.

Between lines 8-11, you will see the connection string details. Populate the credentials and save the file.

```
$DB_HOST = '';
$DB_USER = '';
$DB_PASS = '';
$DB_NAME = '';
```

## Set The Base URL
Inside the config.php file, you must also set the base URL of your application. For example, if the project folder is named InsecureCarDealer and you're using XAMPP, your base URL should be: http://localhost/InsecureCarDealer/ **(don't forget the trailing slash!)** - Save the file.

## Create Database Tables
We are now ready to create the database tables, ready for data seeding.

Open up a web browser and navigate to your local instance of the application and access the setup.php page. As an example, my URL for the application is **http://localhost/InsecureCarDealer/setup.php**. Depending on your setup up this URL will be different.

If the connection is successful, you will see a **database connected message** with a list of the tables that have been created. If your connection was unsuccessful, you will need to double-check the credentials in the config file and ensure that the database has been created and you are using the same database name in the config.php file.

## Seed The Database
Now that we have established a database connection and the database tables have been created, we can now seed the database. This will add dummy data to the database.

You can seed the database by clicking the link to the **seed.php** file from the setup page, or you can navigate directly to **http://localhost/InsecureCarDealer/seed.php**

## Resetting The Database
One or more of the vulnerabilities may allow you to corrupt the database information or inject malicious code. You can reset the database at any time by navigating to **http://localhost/InsecureCarDealer/reset.php**. Once you have reset the database, you will need to reseed the database.
