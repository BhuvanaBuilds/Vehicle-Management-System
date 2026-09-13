<?php
require_once 'includes/db.php';

$edit_mode = false;
$edit_assignment = null;

/* =========================
   ADD ASSIGNMENT
========================= */
if (isset($_POST['add_assignment'])) {

    $vehicle_id = $_POST['vehicle_id'];
    $employee_id = $_POST['employee_id'];
    $service_id = $_POST['service_id'];
    $start_date = $_POST['start_date'];
    $expected_end_date = $_POST['expected_end_date'];
    $actual_end_date = !empty($_POST['actual_end_date'])
        ? $_POST['actual_end_date']
        : null;
    $status = $_POST['status'];

    $stmt = $conn->prepare(
        "INSERT INTO work_assignments
        (vehicle_id, employee_id, service_id, start_date,
         expected_end_date, actual_end_date, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "iiissss",
        $vehicle_id,
        $employee_id,
        $service_id,
        $start_date,
        $expected_end_date,
        $actual_end_date,
        $status
    );

    $stmt->execute();
    $stmt->close();

    header("Location: assignments.php");
    exit();
}


/* =========================
   UPDATE ASSIGNMENT
========================= */
if (isset($_POST['update_assignment'])) {

    $assignment_id = $_POST['assignment_id'];
    $vehicle_id = $_POST['vehicle_id'];
    $employee_id = $_POST['employee_id'];
    $service_id = $_POST['service_id'];
    $start_date = $_POST['start_date'];
    $expected_end_date = $_POST['expected_end_date'];
    $actual_end_date = !empty($_POST['actual_end_date'])
        ? $_POST['actual_end_date']
        : null;
    $status = $_POST['status'];

    $stmt = $conn->prepare(
        "UPDATE work_assignments SET
        vehicle_id = ?,
        employee_id = ?,
        service_id = ?,
        start_date = ?,
        expected_end_date = ?,
        actual_end_date = ?,
        status = ?
        WHERE assignment_id = ?"
    );

    $stmt->bind_param(
        "iiissssi",
        $vehicle_id,
        $employee_id,
        $service_id,
        $start_date,
        $expected_end_date,
        $actual_end_date,
        $status,
        $assignment_id
    );

    $stmt->execute();
    $stmt->close();

    header("Location: assignments.php");
    exit();
}


/* =========================
   DELETE ASSIGNMENT
========================= */
if (isset($_GET['delete'])) {

    $assignment_id = $_GET['delete'];

    $stmt = $conn->prepare(
        "DELETE FROM work_assignments
         WHERE assignment_id = ?"
    );

    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $stmt->close();

    header("Location: assignments.php");
    exit();
}


/* =========================
   EDIT ASSIGNMENT
========================= */
if (isset($_GET['edit'])) {

    $assignment_id = $_GET['edit'];

    $stmt = $conn->prepare(
        "SELECT * FROM work_assignments
         WHERE assignment_id = ?"
    );

    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $edit_assignment = $result->fetch_assoc();
        $edit_mode = true;
    }

    $stmt->close();
}


/* =========================
   GET VEHICLES
========================= */
$vehicles = $conn->query(
    "SELECT vehicle_id, vehicle_number
     FROM vehicles
     ORDER BY vehicle_number"
);


/* =========================
   GET EMPLOYEES
========================= */
$employees = $conn->query(
    "SELECT employee_id, employee_name
     FROM employees
     WHERE status = 'Active'
     ORDER BY employee_name"
);


/* =========================
   GET SERVICES
========================= */
$services = $conn->query(
    "SELECT service_id, service_name
     FROM services
     ORDER BY service_name"
);


/* =========================
   GET ASSIGNMENTS
========================= */
$assignments = $conn->query(
    "SELECT
        wa.assignment_id,
        wa.vehicle_id,
        wa.employee_id,
        wa.service_id,
        wa.start_date,
        wa.expected_end_date,
        wa.actual_end_date,
        wa.status,
        v.vehicle_number,
        e.employee_name,
        s.service_name
     FROM work_assignments wa
     INNER JOIN vehicles v
        ON wa.vehicle_id = v.vehicle_id
     INNER JOIN employees e
        ON wa.employee_id = e.employee_id
     INNER JOIN services s
        ON wa.service_id = s.service_id
     ORDER BY wa.assignment_id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Work Assignment - Vehicle Pro</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #f4f6f9;
    color: #222;
}


/* =========================
   SIDEBAR
========================= */

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 250px;
    height: 100vh;
    background: #111827;
    color: white;
    padding: 25px 15px;
}

.logo {
    text-align: center;
    margin-bottom: 35px;
}

.car-icon {
    font-size: 42px;
}

.logo h2 {
    margin: 5px 0;
    font-size: 22px;
    letter-spacing: 1px;
}

.logo p {
    margin: 0;
    font-size: 10px;
    color: #9ca3af;
    letter-spacing: 1px;
}

.menu-title {
    color: #6b7280;
    font-size: 11px;
    font-weight: bold;
    margin: 20px 12px 8px;
    text-transform: uppercase;
}

.sidebar a {
    display: block;
    color: #d1d5db;
    text-decoration: none;
    padding: 12px 15px;
    margin: 5px 0;
    border-radius: 8px;
    font-size: 14px;
}

.sidebar a:hover {
    background: #1f2937;
    color: white;
}

.sidebar a.active {
    background: #2563eb;
    color: white;
}

.sidebar a span {
    margin-right: 10px;
}


/* =========================
   MAIN
========================= */

.main {
    margin-left: 250px;
    padding: 35px;
}

.header {
    margin-bottom: 25px;
}

.header h1 {
    margin: 0;
    font-size: 28px;
}

.header p {
    margin-top: 6px;
    color: #6b7280;
}


/* =========================
   FORM CARD
========================= */

.card {
    background: white;
    border-radius: 14px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06);
}

.card h2 {
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 20px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-size: 13px;
    font-weight: bold;
    margin-bottom: 7px;
    color: #374151;
}

.form-group input,
.form-group select {
    padding: 11px 12px;
    border: 1px solid #d1d5db;
    border-radius: 7px;
    font-size: 14px;
    outline: none;
    background: white;
}

.form-group input:focus,
.form-group select:focus {
    border-color: #2563eb;
}


/* =========================
   BUTTONS
========================= */

.buttons {
    margin-top: 20px;
}

.btn {
    border: none;
    padding: 11px 20px;
    border-radius: 7px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
}

.btn-add {
    background: #2563eb;
    color: white;
}

.btn-add:hover {
    background: #1d4ed8;
}

.btn-update {
    background: #16a34a;
    color: white;
}

.btn-cancel {
    background: #6b7280;
    color: white;
    text-decoration: none;
    display: inline-block;
    margin-left: 8px;
}


/* =========================
   TABLE
========================= */

.table-card {
    background: white;
    border-radius: 14px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06);
    overflow-x: auto;
}

.table-card h2 {
    margin-top: 0;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th {
    background: #f3f4f6;
    color: #374151;
    padding: 13px;
    text-align: left;
    font-size: 13px;
    white-space: nowrap;
}

td {
    padding: 13px;
    border-bottom: 1px solid #e5e7eb;
    font-size: 14px;
}

tr:hover {
    background: #f9fafb;
}


/* =========================
   ACTION BUTTONS
========================= */

.edit-btn {
    background: #f59e0b;
    color: white;
    padding: 7px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    margin-right: 5px;
}

.delete-btn {
    background: #dc2626;
    color: white;
    padding: 7px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
}

.edit-btn:hover {
    background: #d97706;
}

.delete-btn:hover {
    background: #b91c1c;
}


/* =========================
   STATUS
========================= */

.status {
    font-weight: bold;
}

.status-not-started {
    color: #d97706;
}

.status-progress {
    color: #2563eb;
}

.status-completed {
    color: #15803d;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 1000px) {

    .form-grid {
        grid-template-columns: 1fr;
    }

}

</style>

</head>

<body>


<!-- =========================
     SIDEBAR
========================= -->

<div class="sidebar">

    <div class="logo">

        <div class="car-icon">🚘</div>

        <h2>VEHICLE PRO</h2>

        <p>WORKSHOP MANAGEMENT</p>

    </div>


    <div class="menu-title">Main Menu</div>

    <a href="dashboard.php">
        <span>📊</span> Dashboard
    </a>

    <a href="companies.php">
        <span>🏢</span> Companies
    </a>

    <a href="vehicles.php">
        <span>🚗</span> Vehicles
    </a>

    <a href="employees.php">
        <span>👨‍🔧</span> Employees
    </a>


    <div class="menu-title">Workshop</div>

    <a href="services.php">
        <span>🔧</span> Services
    </a>

    <a href="assignments.php" class="active">
        <span>📋</span> Work Assignment
    </a>

    <a href="quotations.php">
        <span>🧾</span> Quotations
    </a>


    <div class="menu-title">Management</div>

    <a href="salary.php">
        <span>💰</span> Salary
    </a>

    <a href="reports.php">
        <span>📈</span> Reports
    </a>

</div>


<!-- =========================
     MAIN CONTENT
========================= -->

<div class="main">

    <div class="header">

        <h1>Work Assignment 📋</h1>

        <p>
            Assign workshop employees to vehicle services
        </p>

    </div>


    <!-- =========================
         ADD / EDIT ASSIGNMENT
    ========================= -->

    <div class="card">

        <h2>
            <?php
            echo $edit_mode
                ? "✏️ Edit Work Assignment"
                : "➕ Create Work Assignment";
            ?>
        </h2>


        <form method="POST">

            <?php if ($edit_mode): ?>

                <input
                    type="hidden"
                    name="assignment_id"
                    value="<?php
                    echo $edit_assignment['assignment_id'];
                    ?>"
                >

            <?php endif; ?>


            <div class="form-grid">


                <!-- Vehicle -->

                <div class="form-group">

                    <label>Vehicle</label>

                    <select name="vehicle_id" required>

                        <option value="">
                            Select Vehicle
                        </option>

                        <?php while ($vehicle = $vehicles->fetch_assoc()): ?>

                            <option
                                value="<?php
                                echo $vehicle['vehicle_id'];
                                ?>"
                                <?php
                                if (
                                    $edit_mode &&
                                    $edit_assignment['vehicle_id']
                                    == $vehicle['vehicle_id']
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >
                                <?php
                                echo htmlspecialchars(
                                    $vehicle['vehicle_number']
                                );
                                ?>
                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <!-- Employee -->

                <div class="form-group">

                    <label>Employee</label>

                    <select name="employee_id" required>

                        <option value="">
                            Select Employee
                        </option>

                        <?php while ($employee = $employees->fetch_assoc()): ?>

                            <option
                                value="<?php
                                echo $employee['employee_id'];
                                ?>"
                                <?php
                                if (
                                    $edit_mode &&
                                    $edit_assignment['employee_id']
                                    == $employee['employee_id']
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >
                                <?php
                                echo htmlspecialchars(
                                    $employee['employee_name']
                                );
                                ?>
                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <!-- Service -->

                <div class="form-group">

                    <label>Service</label>

                    <select name="service_id" required>

                        <option value="">
                            Select Service
                        </option>

                        <?php while ($service = $services->fetch_assoc()): ?>

                            <option
                                value="<?php
                                echo $service['service_id'];
                                ?>"
                                <?php
                                if (
                                    $edit_mode &&
                                    $edit_assignment['service_id']
                                    == $service['service_id']
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >
                                <?php
                                echo htmlspecialchars(
                                    $service['service_name']
                                );
                                ?>
                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <!-- Start Date -->

                <div class="form-group">

                    <label>Start Date</label>

                    <input
                        type="date"
                        name="start_date"
                        required
                        value="<?php
                        echo $edit_mode
                            ? $edit_assignment['start_date']
                            : '';
                        ?>"
                    >

                </div>


                <!-- Expected End Date -->

                <div class="form-group">

                    <label>Expected End Date</label>

                    <input
                        type="date"
                        name="expected_end_date"
                        required
                        value="<?php
                        echo $edit_mode
                            ? $edit_assignment['expected_end_date']
                            : '';
                        ?>"
                    >

                </div>


                <!-- Actual End Date -->

                <div class="form-group">

                    <label>Actual End Date</label>

                    <input
                        type="date"
                        name="actual_end_date"
                        value="<?php
                        echo $edit_mode
                            ? $edit_assignment['actual_end_date']
                            : '';
                        ?>"
                    >

                </div>


                <!-- Status -->

                <div class="form-group">

                    <label>Status</label>

                    <select name="status">

                        <option
                            value="Not Started"
                            <?php
                            if (
                                !$edit_mode ||
                                $edit_assignment['status']
                                == 'Not Started'
                            ) echo 'selected';
                            ?>
                        >
                            Not Started
                        </option>

                        <option
                            value="In Progress"
                            <?php
                            if (
                                $edit_mode &&
                                $edit_assignment['status']
                                == 'In Progress'
                            ) echo 'selected';
                            ?>
                        >
                            In Progress
                        </option>

                        <option
                            value="Completed"
                            <?php
                            if (
                                $edit_mode &&
                                $edit_assignment['status']
                                == 'Completed'
                            ) echo 'selected';
                            ?>
                        >
                            Completed
                        </option>

                    </select>

                </div>

            </div>


            <div class="buttons">

                <?php if ($edit_mode): ?>

                    <button
                        type="submit"
                        name="update_assignment"
                        class="btn btn-update"
                    >
                        Update Assignment
                    </button>

                    <a
                        href="assignments.php"
                        class="btn btn-cancel"
                    >
                        Cancel
                    </a>

                <?php else: ?>

                    <button
                        type="submit"
                        name="add_assignment"
                        class="btn btn-add"
                    >
                        Create Assignment
                    </button>

                <?php endif; ?>

            </div>

        </form>

    </div>


    <!-- =========================
         ASSIGNMENT LIST
    ========================= -->

    <div class="table-card">

        <h2>Work Assignment List 📋</h2>

        <table>

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Vehicle</th>
                    <th>Employee</th>
                    <th>Service</th>
                    <th>Start Date</th>
                    <th>Expected End</th>
                    <th>Actual End</th>
                    <th>Status</th>
                    <th>Action</th>

                </tr>

            </thead>


            <tbody>

                <?php if ($assignments->num_rows > 0): ?>

                    <?php while ($row = $assignments->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php
                                echo $row['assignment_id'];
                                ?>
                            </td>

                            <td>
                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $row['vehicle_number']
                                    );
                                    ?>
                                </strong>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['employee_name']
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['service_name']
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['start_date']
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['expected_end_date']
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo $row['actual_end_date']
                                    ? htmlspecialchars(
                                        $row['actual_end_date']
                                    )
                                    : '-';
                                ?>
                            </td>

                            <td>

                                <?php if (
                                    $row['status']
                                    == 'Not Started'
                                ): ?>

                                    <span class="status status-not-started">
                                        ● Not Started
                                    </span>

                                <?php elseif (
                                    $row['status']
                                    == 'In Progress'
                                ): ?>

                                    <span class="status status-progress">
                                        ● In Progress
                                    </span>

                                <?php else: ?>

                                    <span class="status status-completed">
                                        ● Completed
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <a
                                    href="assignments.php?edit=<?php
                                    echo $row['assignment_id'];
                                    ?>"
                                    class="edit-btn"
                                >
                                    Edit
                                </a>

                                <a
                                    href="assignments.php?delete=<?php
                                    echo $row['assignment_id'];
                                    ?>"
                                    class="delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this assignment?');"
                                >
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="9"
                            style="text-align:center; padding:30px;"
                        >
                            No work assignments found.
                            Create your first assignment above.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>