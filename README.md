# Supervised Driving Experience Management (PHP & MySQL)

## Description

This web application manages driving experiences, developed with **PHP** and **MySQL**. It allows users to log driving data (date, time, kilometers, weather conditions) and view statistics in tables and graphs. The app is mobile-friendly and uses responsive design. You can try out the live version of the app here: [Live Demo](https://asmar.alwaysdata.net/backend-project/)

## Features

- **Experience Entry Form**: A user-friendly form to input driving details with selectable weather conditions and other variables.
- **Summary Table**: Displays all records with sorting and filtering options.
- **Graphs**: Visualizes statistics like kilometers traveled and weather conditions using **ChartJS**.
- **Database**: Uses **MySQL** to store data with many-to-many relationships.
- **Security**: Employs prepared SQL queries and PHP sessions for secure data handling.

## Technologies

- **Back-End**: PHP, MySQL
- **Front-End**: HTML5, CSS3, JS (using **Bootstrap** and **Flexbox**)
- **Graphs**: **ChartJS**
- **Security**: Prepared queries, PHP sessions

## Installation

1. Clone this repository to your local machine:

    ```bash
    https://github.com/AsmarAlv/driving-experience-tracker.git
    ```

2. Navigate to the project directory:

    ```bash
    cd driving-experience-tracker
    ```

3. Set up the database by importing the provided SQL schema into MySQL.

4. Configure your database connection in the PHP scripts.

5. Run the application on a local or remote web server.

## Deployment

The app is hosted on a remote server with full access via a public URL.