<?php
require_once 'includes/db.php';

$edit_mode = false;
$edit_salary = null;

/* =========================
   ADD SALARY
========================= */
if (isset($_POST['add_salary'])) {

    $employee_id = $_POST['employee_id'];
    $salary_month = $_POST['salary_month'];
    $basic_salary = $_POST['basic_salary'];
    $bonus = $_POST['bonus'];
    $deduction = $_POST['deduction'];
    $net_salary = $basic_salary + $bonus - $deduction;
    $payment_status = $_POST['payment_status'];

    $stmt = $conn->prepare(
        "INSERT INTO salaries
        (employee_id, salary_month, basic_salary, bonus,
         deduction, net_salary, payment_status)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "isdddds",
        $employee_id,
        $salary_month,
        $basic_salary,
        $bonus,
        $deduction,
        $net_salary,
        $payment_status
    );

    $stmt->execute();
    $stmt->close();

    header("Location: salary.php");
    exit();
}


/* =========================
   UPDATE SALARY
========================= */
if (isset($_POST['update_salary'])) {

    $salary_id = $_POST['salary_id'];
    $employee_id = $_POST['employee_id'];
    $salary_month = $_POST['salary_month'];
    $basic_salary = $_POST['basic_salary'];
    $bonus = $_POST['bonus'];
    $deduction = $_POST['deduction'];
    $net_salary = $basic_salary + $bonus - $deduction;
    $payment_status = $_POST['payment_status'];

    $stmt = $conn->prepare(
        "UPDATE salaries SET
        employee_id = ?,
        salary_month = ?,
        basic_salary = ?,
        bonus = ?,
        deduction = ?,
        net_salary = ?,
        payment_status = ?
        WHERE salary_id = ?"
    );

    $stmt->bind_param(
        "isddddsi",
        $employee_id,
        $salary_month,
        $basic_salary,
        $bonus,
        $deduction,
        $net_salary,
        $payment_status,
        $salary_id
    );

    $stmt->execute();
    $stmt->close();

    header("Location: salary.php");
    exit();
}


/* =========================
   DELETE SALARY
========================= */
if (isset($_GET['delete'])) {

    $salary_id = $_GET['delete'];

    $stmt = $conn->prepare(
        "DELETE FROM salaries WHERE salary_id = ?"
    );

    $stmt->bind_param("i", $salary_id);
    $stmt->execute();
    $stmt->close();

    header("Location: salary.php");
    exit();
}


/* =========================
   EDIT SALARY
========================= */
if (isset($_GET['edit'])) {

    $salary_id = $_GET['edit'];

    $stmt = $conn->prepare(
        "SELECT * FROM salaries WHERE salary_id = ?"
    );

    $stmt->bind_param("i", $salary_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $edit_salary = $result->fetch_assoc();
        $edit_mode = true;
    }

    $stmt->close();
}


/* =========================
   GET EMPLOYEES
========================= */
$employees = $conn->query(
    "SELECT employee_id, employee_name
     FROM employees
     ORDER BY employee_name"
);


/* =========================
   GET SALARY RECORDS
========================= */
$salaries = $conn->query(
    "SELECT
        s.salary_id,
        s.employee_id,
        s.salary_month,
        s.basic_salary,
        s.bonus,
        s.deduction,
        s.net_salary,
        s.payment_status,
        e.employee_name
     FROM salaries s
     INNER JOIN employees e
        ON s.employee_id = e.employee_id
     ORDER BY s.salary_id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Salary - Vehicle Pro</title>

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
   CARD
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


/* =========================
   PAYMENT STATUS
========================= */

.paid {
    color: #15803d;
    font-weight: bold;
}

.pending {
    color: #d97706;
    font-weight: bold;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 900px) {

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

    <a href="assignments.php">
        <span>📋</span> Work Assignment
    </a>

    <a href="quotations.php">
        <span>🧾</span> Quotations
    </a>


    <div class="menu-title">Management</div>

    <a href="salary.php" class="active">
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

        <h1>Salary Management 💰</h1>

        <p>
            Manage employee salaries and payment records
        </p>

    </div>


    <!-- =========================
         ADD / EDIT SALARY
    ========================= -->

    <div class="card">

        <h2>
            <?php
            echo $edit_mode
                ? "✏️ Edit Salary Record"
                : "➕ Add Salary Record";
            ?>
        </h2>


        <form method="POST">

            <?php if ($edit_mode): ?>

                <input
                    type="hidden"
                    name="salary_id"
                    value="<?php
                    echo $edit_salary['salary_id'];
                    ?>"
                >

            <?php endif; ?>


            <div class="form-grid">


                <!-- EMPLOYEE -->

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
                                    $edit_salary['employee_id']
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


                <!-- MONTH -->

                <div class="form-group">

                    <label>Salary Month</label>

                    <input
                        type="date"
                        name="salary_month"
                        required
                        value="<?php
                        echo $edit_mode
                            ? $edit_salary['salary_month']
                            : date('Y-m-d');
                        ?>"
                    >

                </div>


                <!-- BASIC -->

                <div class="form-group">

                    <label>Basic Salary (₹)</label>

                    <input
                        type="number"
                        name="basic_salary"
                        step="0.01"
                        min="0"
                        placeholder="Enter basic salary"
                        required
                        value="<?php
                        echo $edit_mode
                            ? $edit_salary['basic_salary']
                            : '';
                        ?>"
                    >

                </div>


                <!-- BONUS -->

                <div class="form-group">

                    <label>Bonus (₹)</label>

                    <input
                        type="number"
                        name="bonus"
                        step="0.01"
                        min="0"
                        value="<?php
                        echo $edit_mode
                            ? $edit_salary['bonus']
                            : '0';
                        ?>"
                    >

                </div>


                <!-- DEDUCTION -->

                <div class="form-group">

                    <label>Deduction (₹)</label>

                    <input
                        type="number"
                        name="deduction"
                        step="0.01"
                        min="0"
                        value="<?php
                        echo $edit_mode
                            ? $edit_salary['deduction']
                            : '0';
                        ?>"
                    >

                </div>


                <!-- PAYMENT STATUS -->

                <div class="form-group">

                    <label>Payment Status</label>

                    <select name="payment_status">

                        <option
                            value="Pending"
                            <?php
                            if (
                                !$edit_mode ||
                                $edit_salary['payment_status']
                                == 'Pending'
                            ) echo 'selected';
                            ?>
                        >
                            Pending
                        </option>

                        <option
                            value="Paid"
                            <?php
                            if (
                                $edit_mode &&
                                $edit_salary['payment_status']
                                == 'Paid'
                            ) echo 'selected';
                            ?>
                        >
                            Paid
                        </option>

                    </select>

                </div>

            </div>


            <div class="buttons">

                <?php if ($edit_mode): ?>

                    <button
                        type="submit"
                        name="update_salary"
                        class="btn btn-update"
                    >
                        Update Salary
                    </button>

                    <a
                        href="salary.php"
                        class="btn btn-cancel"
                    >
                        Cancel
                    </a>

                <?php else: ?>

                    <button
                        type="submit"
                        name="add_salary"
                        class="btn btn-add"
                    >
                        Add Salary
                    </button>

                <?php endif; ?>

            </div>

        </form>

    </div>


    <!-- =========================
         SALARY LIST
    ========================= -->

    <div class="table-card">

        <h2>Salary Records 💰</h2>

        <table>

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Employee</th>
                    <th>Month</th>
                    <th>Basic Salary</th>
                    <th>Bonus</th>
                    <th>Deduction</th>
                    <th>Net Salary</th>
                    <th>Payment</th>
                    <th>Action</th>

                </tr>

            </thead>


            <tbody>

                <?php if ($salaries->num_rows > 0): ?>

                    <?php while ($row = $salaries->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php
                                echo $row['salary_id'];
                                ?>
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
                                    $row['salary_month']
                                );
                                ?>
                            </td>

                            <td>
                                ₹<?php
                                echo number_format(
                                    $row['basic_salary'],
                                    2
                                );
                                ?>
                            </td>

                            <td>
                                ₹<?php
                                echo number_format(
                                    $row['bonus'],
                                    2
                                );
                                ?>
                            </td>

                            <td>
                                ₹<?php
                                echo number_format(
                                    $row['deduction'],
                                    2
                                );
                                ?>
                            </td>

                            <td>
                                <strong>
                                    ₹<?php
                                    echo number_format(
                                        $row['net_salary'],
                                        2
                                    );
                                    ?>
                                </strong>
                            </td>

                            <td>

                                <?php if (
                                    $row['payment_status']
                                    == 'Paid'
                                ): ?>

                                    <span class="paid">
                                        ● Paid
                                    </span>

                                <?php else: ?>

                                    <span class="pending">
                                        ● Pending
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <a
                                    href="salary.php?edit=<?php
                                    echo $row['salary_id'];
                                    ?>"
                                    class="edit-btn"
                                >
                                    Edit
                                </a>

                                <a
                                    href="salary.php?delete=<?php
                                    echo $row['salary_id'];
                                    ?>"
                                    class="delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this salary record?');"
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
                            No salary records found.
                            Add your first salary record above.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>