<?php
require_once 'includes/db.php';

$edit_mode = false;
$edit_employee = null;

/* =========================
   ADD EMPLOYEE
========================= */
if (isset($_POST['add_employee'])) {

    $employee_name = $_POST['employee_name'];
    $phone = $_POST['phone'];
    $job_role = $_POST['job_role'];
    $joining_date = $_POST['joining_date'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO employees 
        (employee_name, phone, job_role, joining_date, status)
        VALUES (?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "sssss",
        $employee_name,
        $phone,
        $job_role,
        $joining_date,
        $status
    );

    $stmt->execute();
    $stmt->close();

    header("Location: employees.php");
    exit();
}


/* =========================
   UPDATE EMPLOYEE
========================= */
if (isset($_POST['update_employee'])) {

    $employee_id = $_POST['employee_id'];
    $employee_name = $_POST['employee_name'];
    $phone = $_POST['phone'];
    $job_role = $_POST['job_role'];
    $joining_date = $_POST['joining_date'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE employees SET
        employee_name = ?,
        phone = ?,
        job_role = ?,
        joining_date = ?,
        status = ?
        WHERE employee_id = ?");

    $stmt->bind_param(
        "sssssi",
        $employee_name,
        $phone,
        $job_role,
        $joining_date,
        $status,
        $employee_id
    );

    $stmt->execute();
    $stmt->close();

    header("Location: employees.php");
    exit();
}


/* =========================
   DELETE EMPLOYEE
========================= */
if (isset($_GET['delete'])) {

    $employee_id = $_GET['delete'];

    $stmt = $conn->prepare(
        "DELETE FROM employees WHERE employee_id = ?"
    );

    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $stmt->close();

    header("Location: employees.php");
    exit();
}


/* =========================
   EDIT EMPLOYEE
========================= */
if (isset($_GET['edit'])) {

    $employee_id = $_GET['edit'];

    $stmt = $conn->prepare(
        "SELECT * FROM employees WHERE employee_id = ?"
    );

    $stmt->bind_param("i", $employee_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $edit_employee = $result->fetch_assoc();
        $edit_mode = true;
    }

    $stmt->close();
}


/* =========================
   GET ALL EMPLOYEES
========================= */
$employees = $conn->query(
    "SELECT * FROM employees ORDER BY employee_id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Employees - Vehicle Pro</title>

<style>

/* =========================
   GENERAL
========================= */

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
    transition: 0.2s;
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
   MAIN CONTENT
========================= */

.main {
    margin-left: 250px;
    padding: 35px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
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
}

.form-group input:focus,
.form-group select:focus {
    border-color: #2563eb;
}

.full-width {
    grid-column: 1 / -1;
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

.btn-update:hover {
    background: #15803d;
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

.status-active {
    color: #15803d;
    font-weight: bold;
}

.status-inactive {
    color: #dc2626;
    font-weight: bold;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 900px) {

    .sidebar {
        width: 210px;
    }

    .main {
        margin-left: 210px;
    }

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

    <a href="employees.php" class="active">
        <span>👨‍🔧</span> Employees
    </a>


    <div class="menu-title">Workshop</div>

    <a href="services.php">
        <span>🔧</span> Services
    </a>

    <a href="assignments.php">
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

        <div>

            <h1>Employees 👨‍🔧</h1>

            <p>
                Manage workshop employees and their details
            </p>

        </div>

    </div>


    <!-- =========================
         ADD / EDIT FORM
    ========================= -->

    <div class="card">

        <h2>
            <?php echo $edit_mode ? "✏️ Edit Employee" : "➕ Add New Employee"; ?>
        </h2>


        <form method="POST">

            <?php if ($edit_mode): ?>

                <input
                    type="hidden"
                    name="employee_id"
                    value="<?php echo $edit_employee['employee_id']; ?>"
                >

            <?php endif; ?>


            <div class="form-grid">


                <!-- Employee Name -->

                <div class="form-group">

                    <label>Employee Name</label>

                    <input
                        type="text"
                        name="employee_name"
                        placeholder="Enter employee name"
                        required
                        value="<?php
                            echo $edit_mode
                            ? htmlspecialchars($edit_employee['employee_name'])
                            : '';
                        ?>"
                    >

                </div>


                <!-- Phone -->

                <div class="form-group">

                    <label>Phone Number</label>

                    <input
                        type="text"
                        name="phone"
                        placeholder="Enter phone number"
                        value="<?php
                            echo $edit_mode
                            ? htmlspecialchars($edit_employee['phone'])
                            : '';
                        ?>"
                    >

                </div>


                <!-- Job Role -->

                <div class="form-group">

                    <label>Job Role</label>

                    <select name="job_role" required>

                        <option value="">Select Job Role</option>

                        <?php

                        $roles = [
                            "Body Builder",
                            "Painter",
                            "Electrician",
                            "Welder",
                            "Mechanic",
                            "Seat Stitcher",
                            "Interior Worker",
                            "Tinkerer",
                            "Supervisor",
                            "Other"
                        ];

                        foreach ($roles as $role):

                            $selected = "";

                            if (
                                $edit_mode &&
                                $edit_employee['job_role'] == $role
                            ) {
                                $selected = "selected";
                            }

                        ?>

                            <option
                                value="<?php echo $role; ?>"
                                <?php echo $selected; ?>
                            >
                                <?php echo $role; ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- Joining Date -->

                <div class="form-group">

                    <label>Joining Date</label>

                    <input
                        type="date"
                        name="joining_date"
                        required
                        value="<?php
                            echo $edit_mode
                            ? $edit_employee['joining_date']
                            : '';
                        ?>"
                    >

                </div>


                <!-- Status -->

                <div class="form-group">

                    <label>Status</label>

                    <select name="status">

                        <option
                            value="Active"
                            <?php
                            if (
                                !$edit_mode ||
                                $edit_employee['status'] == 'Active'
                            ) echo 'selected';
                            ?>
                        >
                            Active
                        </option>

                        <option
                            value="Inactive"
                            <?php
                            if (
                                $edit_mode &&
                                $edit_employee['status'] == 'Inactive'
                            ) echo 'selected';
                            ?>
                        >
                            Inactive
                        </option>

                    </select>

                </div>

            </div>


            <div class="buttons">

                <?php if ($edit_mode): ?>

                    <button
                        type="submit"
                        name="update_employee"
                        class="btn btn-update"
                    >
                        Update Employee
                    </button>

                    <a
                        href="employees.php"
                        class="btn btn-cancel"
                    >
                        Cancel
                    </a>

                <?php else: ?>

                    <button
                        type="submit"
                        name="add_employee"
                        class="btn btn-add"
                    >
                        Add Employee
                    </button>

                <?php endif; ?>

            </div>

        </form>

    </div>


    <!-- =========================
         EMPLOYEE TABLE
    ========================= -->

    <div class="table-card">

        <h2>Employee List 👨‍🔧</h2>

        <table>

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Employee Name</th>
                    <th>Phone</th>
                    <th>Job Role</th>
                    <th>Joining Date</th>
                    <th>Status</th>
                    <th>Action</th>

                </tr>

            </thead>


            <tbody>

                <?php if ($employees->num_rows > 0): ?>

                    <?php while ($row = $employees->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo $row['employee_id']; ?>
                            </td>

                            <td>
                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $row['employee_name']
                                    );
                                    ?>
                                </strong>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['phone']
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['job_role']
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['joining_date']
                                );
                                ?>
                            </td>

                            <td>

                                <?php if ($row['status'] == 'Active'): ?>

                                    <span class="status-active">
                                        ● Active
                                    </span>

                                <?php else: ?>

                                    <span class="status-inactive">
                                        ● Inactive
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <a
                                    href="employees.php?edit=<?php echo $row['employee_id']; ?>"
                                    class="edit-btn"
                                >
                                    Edit
                                </a>

                                <a
                                    href="employees.php?delete=<?php echo $row['employee_id']; ?>"
                                    class="delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this employee?');"
                                >
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="7"
                            style="text-align:center; padding:30px;"
                        >
                            No employees found. Add your first employee above.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>