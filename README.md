# Tibabu Hospital Management System

## 1. Project Overview

Tibabu Hospital Management System is a web-based client-server application developed to manage basic hospital operations. It supports patient registration, doctor management, appointment booking, and record management.

## 2. Technologies Used

- HTML and CSS for the user interface.
- PHP for server-side processing and application logic.
- MariaDB for data storage.
- Docker and Docker Compose for running the web application and database in separate containers.
- Apache as the web server.

## 3. System Architecture

The application follows a layered architecture.

### Presentation Layer
The browser displays the pages, forms, tables, and dashboard. Users interact with the system through this layer.

### Application Layer
PHP processes requests from the browser, performs validation, executes application logic, and communicates with the database.

### Data Layer
MariaDB stores patient, doctor, and appointment records in the `hospital_db` database.

## 4. Client-Server Communication

When a user submits a form, the browser sends an HTTP request to the PHP application. PHP processes the request and communicates with MariaDB to retrieve or save information. The application then returns a response to the browser.

## 5. Docker Architecture

Docker Compose runs the application in two services:

- `web`: PHP and Apache.
- `db`: MariaDB.

The services communicate through the Docker network. The web application is available locally at http://localhost:8080.

## 6. Main Features

- Patient registration and record management.
- Doctor registration and record management.
- Appointment booking and management.
- Dashboard displaying hospital information.
- Database-backed CRUD operations.

## 7. Running the Application

1. Install Docker Desktop and start it.
2. Open a terminal in the project folder.
3. Run `docker compose up --build -d`.
4. Open http://localhost:8080 in a browser.
5. To stop the application, run `docker compose down`.

The database uses a Docker volume to preserve its data when the containers are stopped and restarted. Do not remove the volume if you want to retain the stored records.

## 8. Testing

The system should be tested by registering a patient, adding a doctor, booking an appointment, viewing records, editing a record, and deleting a test record. Database connectivity and recovery after a database restart should also be tested.

## 9. Limitations

The current implementation runs on a single computer. It separates application and database components but does not provide automatic database failover or high availability. The system is an academic demonstration and should not be used with real patient information without additional security and privacy controls.
