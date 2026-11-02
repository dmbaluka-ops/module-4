

<?php
require_once "db.php";

// Get dashboard statistics
$patientResult = $conn->query("SELECT COUNT(*) AS total FROM patients");
$doctorResult = $conn->query("SELECT COUNT(*) AS total FROM doctors");
$appointmentResult = $conn->query("SELECT COUNT(*) AS total FROM appointments");

$totalPatients = $patientResult
    ? (int) $patientResult->fetch_assoc()["total"]
    : 0;

$totalDoctors = $doctorResult
    ? (int) $doctorResult->fetch_assoc()["total"]
    : 0;

$totalAppointments = $appointmentResult
    ? (int) $appointmentResult->fetch_assoc()["total"]
    : 0;

// Get recent appointments
$sql = "SELECT
            a.id,
            a.appointment_date,
            a.reason,
            p.fname,
            p.lname,
            d.fullname AS doctor_name
        FROM appointments a
        INNER JOIN patients p ON a.patient_id = p.id
        INNER JOIN doctors d ON a.doctor_id = d.id
        ORDER BY a.appointment_date DESC
        LIMIT 5";

$recentAppointments = $conn->query($sql);

// Safely display database values
function safe($value) {
    return htmlspecialchars(
        (string) ($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tibabu Hospital Dashboard</title>

<style>

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: "Segoe UI", Arial, sans-serif;
        background: #f4f7fb;
        color: #263238;
    }

    a {
        text-decoration: none;
    }

    /* Main layout */
    .layout {
        display: flex;
        min-height: 100vh;
    }

    /* Left sidebar */
    .sidebar {
        width: 250px;
        flex-shrink: 0;
        background: #172554;
        color: white;
        padding: 28px 18px;
    }

    .brand {
        padding: 0 12px 30px;
        font-size: 21px;
        font-weight: bold;
        line-height: 1.5;
    }

    .brand span {
        display: block;
        color: #bfdbfe;
        font-size: 12px;
        font-weight: normal;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .nav-heading {
        padding: 10px 12px;
        color: #a5b4fc;
        font-size: 11px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    .sidebar nav a {
        display: block;
        margin: 5px 0;
        padding: 13px 14px;
        border-radius: 8px;
        color: #dbeafe;
        font-size: 14px;
        transition: background 0.2s;
    }

    .sidebar nav a:hover,
    .sidebar nav a.active {
        background: #2e4380;
        color: white;
    }

    .sidebar-footer {
        margin: 35px 12px 0;
        padding-top: 20px;
        border-top: 1px solid #334477;
        color: #bfdbfe;
        font-size: 12px;
        line-height: 1.7;
    }

    /* Main content */
    .main {
        flex: 1;
        min-width: 0;
        padding: 32px;
    }

    /* Main dashboard header */
    .topbar {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 20px;
        margin-bottom: 32px;
    }

    /* Hospital name */
    .topbar .hospital-name {
        width: 100%;
        margin: 0 0 5px;
        color: red;
        font-size: 32px;
        font-weight: bold;
        text-align: center;
    }

    /* Dashboard title and date */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }

    .topbar h1 {
        margin: 0 0 8px;
        color: #172554;
        font-size: 27px;
    }

    .subtitle {
        color: #718096;
        font-size: 14px;
        line-height: 1.6;
    }

    .date-label {
        flex-shrink: 0;
        padding: 11px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        background: white;
        font-size: 13px;
    }

    /* Statistics cards */
    .stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        padding: 23px;
        border: 1px solid #e7edf5;
        border-radius: 14px;
        background: white;
        box-shadow: 0 4px 15px rgba(23, 37, 84, 0.03);
    }

    .stat-label {
        margin: 0 0 15px;
        color: #718096;
        font-size: 14px;
    }

    .stat-number {
        margin-bottom: 8px;
        color: #172554;
        font-size: 34px;
        font-weight: 700;
    }

    .stat-link {
        display: inline-block;
        margin-top: 10px;
        color: #2563eb;
        font-size: 13px;
        font-weight: 600;
    }

    .stat-link:hover {
        text-decoration: underline;
    }

    /* Section headings */
    .section {
        margin-bottom: 32px;
    }

    .section-header {
        margin-bottom: 17px;
    }

    .section-header h2 {
        margin: 0 0 6px;
        color: #172554;
        font-size: 19px;
    }

    .section-header p {
        margin: 0;
        color: #718096;
        font-size: 13px;
    }

    /* Quick action cards */
    .actions {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 17px;
    }

    .action-card {
        display: block;
        padding: 22px;
        border: 1px solid #e7edf5;
        border-radius: 12px;
        background: white;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 22px rgba(23, 37, 84, 0.08);
    }

    .action-icon {
        margin-bottom: 14px;
        font-size: 26px;
    }

    .action-card h3 {
        margin: 0 0 8px;
        color: #172554;
        font-size: 15px;
    }

    .action-card p {
        margin: 0 0 16px;
        color: #718096;
        font-size: 13px;
        line-height: 1.6;
    }

    .action-link {
        color: #2563eb;
        font-size: 13px;
        font-weight: 600;
    }

    /* Search records */
    .search-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .search-card {
        padding: 22px;
        border: 1px solid #e7edf5;
        border-radius: 12px;
        background: white;
        box-shadow: 0 4px 15px rgba(23, 37, 84, 0.03);
    }

    .search-card h3 {
        margin: 0 0 8px;
        color: #172554;
        font-size: 16px;
    }

    .search-card p {
        margin: 0 0 16px;
        color: #718096;
        font-size: 13px;
        line-height: 1.6;
    }

    .search-form {
        display: flex;
        gap: 10px;
    }

    .search-form input {
        flex: 1;
        min-width: 0;
        padding: 12px;
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        font: inherit;
        font-size: 13px;
    }

    .search-form input:focus {
        outline: 2px solid #bfdbfe;
        border-color: #2563eb;
    }

    .search-form button {
        padding: 12px 17px;
        border: none;
        border-radius: 7px;
        background: #b22222;
        color: white;
        font-weight: 600;
        cursor: pointer;
    }

    .search-form button:hover {
        background: #8b0000;
    }

    /* Recent appointments table */
    .table-card {
        overflow: hidden;
        border: 1px solid #e7edf5;
        border-radius: 14px;
        background: white;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    table {
        width: 100%;
        min-width: 600px;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 15px 18px;
        border-bottom: 1px solid #edf1f7;
        text-align: left;
        font-size: 13px;
    }

    th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 600;
    }

    td {
        color: #334155;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    .empty-state {
        padding: 28px;
        color: #718096;
        text-align: center;
    }

    /* Footer */
    .footer {
        padding: 25px 0 5px;
        color: #94a3b8;
        font-size: 12px;
        line-height: 1.8;
        text-align: center;
    }

    /* Responsive design */
    @media (max-width: 1100px) {
        .stats,
        .actions {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 850px) {
        .search-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 750px) {
        .layout {
            display: block;
        }

        .sidebar {
            width: 100%;
            padding: 18px;
        }

        .brand {
            padding-bottom: 15px;
        }

        .nav-heading,
        .sidebar-footer {
            display: none;
        }

        .sidebar nav a {
            display: inline-block;
            margin: 3px;
            padding: 10px;
            font-size: 12px;
        }

        .main {
            padding: 22px 16px;
        }

        .topbar .hospital-name {
            font-size: 27px;
        }

        .dashboard-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .topbar h1 {
            font-size: 23px;
        }
    }

    @media (max-width: 500px) {
        .stats,
        .actions {
            grid-template-columns: 1fr;
        }
    }

</style>

</head>

<body>

<div class="layout">

    <!-- Sidebar navigation -->
    <aside class="sidebar">

        <div class="brand">
            HospitalCare
            <span>Management System</span>
        </div>

        <div class="nav-heading">Main Menu</div>

        <nav aria-label="Main navigation">
            <a class="active" href="index.php">⌂ &nbsp; Dashboard</a>
            <a href="patients.php">♙ &nbsp; View Patients</a>
            <a href="register_patient.php">＋ &nbsp; Register Patient</a>
            <a href="doctors.php">⚕ &nbsp; View Doctors</a>
            <a href="register_doctor.php">＋ &nbsp; Register Doctor</a>
            <a href="appointments.php">▦ &nbsp; Appointments</a>
            <a href="book_appointment.php">＋ &nbsp; Book Appointment</a>
        </nav>

        <div class="sidebar-footer">
            Hospital Management System<br>
            Local Administration Panel
        </div>

    </aside>

    <!-- Main dashboard -->
    <main class="main">

        <header class="topbar">

            <h1 class="hospital-name">TIBABU HOSPITAL</h1>

            <div>
                <h1>Dashboard Overview</h1>

                <p class="subtitle">
                    Welcome to your Hospital Management System.
                    Manage patients, doctors and appointments in one place.
                </p>
            </div>

            <div class="date-label">
                <?= date("l, d M Y") ?>
            </div>

        </header>

        <!-- Dashboard statistics -->
        <section class="stats">

            <div class="stat-card">
                <p class="stat-label">Total Patients</p>
                <div class="stat-number"><?= $totalPatients ?></div>
                <p class="subtitle">Registered patients</p>
                <a class="stat-link" href="patients.php">View patients →</a>
            </div>

            <div class="stat-card">
                <p class="stat-label">Total Doctors</p>
                <div class="stat-number"><?= $totalDoctors ?></div>
                <p class="subtitle">Registered doctors</p>
                <a class="stat-link" href="doctors.php">View doctors →</a>
            </div>

            <div class="stat-card">
                <p class="stat-label">Total Appointments</p>
                <div class="stat-number"><?= $totalAppointments ?></div>
                <p class="subtitle">Appointments recorded</p>
                <a class="stat-link" href="appointments.php">View appointments →</a>
            </div>

        </section>

        <!-- Quick actions -->
        <section class="section">

            <div class="section-header">
                <h2>Quick Actions</h2>
                <p>Choose a task to get started.</p>
            </div>

            <div class="actions">

                <a class="action-card" href="register_patient.php">
                    <div class="action-icon">♙</div>
                    <h3>Register Patient</h3>
                    <p>Add a new patient and record their details.</p>
                    <span class="action-link">Get started →</span>
                </a>

                <a class="action-card" href="register_doctor.php">
                    <div class="action-icon">⚕</div>
                    <h3>Register Doctor</h3>
                    <p>Add a doctor and their area of specialisation.</p>
                    <span class="action-link">Get started →</span>
                </a>

                <a class="action-card" href="book_appointment.php">
                    <div class="action-icon">▦</div>
                    <h3>Book Appointment</h3>
                    <p>Schedule an appointment with a doctor.</p>
                    <span class="action-link">Get started →</span>
                </a>

            </div>

        </section>

        <!-- Search records -->
        <section class="section">

            <div class="section-header">
                <h2>Search Records</h2>
                <p>Find a patient or doctor using their ID or name.</p>
            </div>

            <div class="search-grid">

                <!-- Patient search -->
                <div class="search-card">

                    <h3>Search for a Patient</h3>

                    <p>
                        Enter a patient's ID, first name or last name.
                    </p>

                    <form
                        class="search-form"
                        action="search_patient.php"
                        method="GET"
                    >

                        <input
                            type="text"
                            name="q"
                            placeholder="Patient ID or name"
                            aria-label="Patient ID or name"
                            required
                        >

                        <button type="submit">Search</button>

                    </form>

                </div>

                <!-- Doctor search -->
                <div class="search-card">

                    <h3>Search for a Doctor</h3>

                    <p>
                        Enter a doctor's ID or full name.
                    </p>

                    <form
                        class="search-form"
                        action="search_doctor.php"
                        method="GET"
                    >

                        <input
                            type="text"
                            name="q"
                            placeholder="Doctor ID or name"
                            aria-label="Doctor ID or name"
                            required
                        >

                        <button type="submit">Search</button>

                    </form>

                </div>

            </div>

        </section>

        <!-- Recent appointments -->
        <section class="section">

            <div class="section-header">
                <h2>Recent Appointments</h2>
                <p>The five most recently scheduled appointments.</p>
            </div>

            <div class="table-card">

                <div class="table-wrapper">

                    <table>

                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date &amp; Time</th>
                                <th>Reason</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if ($recentAppointments && $recentAppointments->num_rows > 0): ?>

                            <?php while ($row = $recentAppointments->fetch_assoc()): ?>

                                <tr>

                                    <td>
                                        <?= safe($row["fname"] . " " . $row["lname"]) ?>
                                    </td>

                                    <td>
                                        <?= safe($row["doctor_name"]) ?>
                                    </td>

                                    <td>
                                        <?php
                                        $timestamp = strtotime($row["appointment_date"]);

                                        echo $timestamp !== false
                                            ? safe(date("d M Y, H:i", $timestamp))
                                            : "Invalid date";
                                        ?>
                                    </td>

                                    <td>
                                        <?= safe($row["reason"]) ?>
                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="4" class="empty-state">
                                    No appointments found. Book your first appointment to see it here.
                                </td>
                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

            <p style="margin-top: 14px;">
                <a class="stat-link" href="appointments.php">
                    View all appointments →
                </a>
            </p>

        </section>

        <!-- Footer -->
        <footer class="footer">

            Hospital Management System &copy; <?= date("Y") ?><br>

            Served by container: <?= safe(gethostname()) ?>

        </footer>

    </main>

</div>

<?php
$conn->close();
?>

</body>
</html>




