# Garden Library

A web application for managing a community garden library system.

## Project Structure

```
gardenlibrary/
├─ assets/               # Global assets
│  ├─ css/
│  │  └─ style.css
│  ├─ img/
│  │  ├─ background.jpg
│  │  ├─ library.jpg
│  │  └─ logo.jpg
│  └─ js/
│     └─ script.js
├─ includes/            
│  ├─ db.php
│  ├─ db_connection.php
│  ├─ footer.php
│  └─ header.php
├─ pages/                
│  ├─ bookborrow/
│  │  ├─ assets/       
│  │  │  ├─ css/
│  │  │  ├─ img/
│  │  │  └─ js/
│  │  └─ index.php
│  ├─ login/
│  │  ├─ assets/         
│  │  │  ├─ css/
│  │  │  ├─ img/
│  │  │  └─ js/
│  │  └─ index.php
│  └─ signup/
│     ├─ assets/        
│     │  ├─ css/
│     │  ├─ img/
│     │  └─ js/
│     └─ index.php
├─ index.php             
├─ readme.md             
└─ sql_of_library_system.sql 
```

## File Description

- `index.php`: Main entry point of the application
- `assets/`: Contains global assets used across the site
  - `css/`: Style sheets
  - `img/`: Images used in the site
  - `js/`: JavaScript files
- `includes/`: Contains shared PHP components
  - `header.php`: Common header included in all pages
  - `footer.php`: Common footer included in all pages
  - `db.php` and `db_connection.php`: Database connection files
- `pages/`: Individual pages of the application, each with its own assets
  - Each page has its own separate assets folder for page-specific resources
- `sql_of_library_system.sql`: SQL file with database structure and initial data

## How to Use

1. Place the entire `gardenlibrary` folder in your web server`s document root
2. Import the `sql_of_library_system.sql` into your MySQL database
3. Configure the database connection in `includes/db_connection.php`
4. Access the application through your web browser at `http://localhost/gardenlibrary`
