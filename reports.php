<?php
require_once 'includes/db.php';

/* =========================
   SUMMARY COUNTS
========================= */

$total_vehicles = $conn->query("SELECT COUNT(*) AS total FROM vehicles")->fetch_assoc()['total'];

$pending_vehicles = $conn->query(
    "SELECT COUNT(*) AS total FROM vehicles WHERE status='Pending'"
)->fetch_assoc()['total'];

$in_progress = $conn->query(
    "SELECT COUNT(*) AS total FROM vehicles WHERE status='In Progress'"
)->fetch_assoc()['total'];

$completed = $conn->query(
    "SELECT COUNT(*) AS total FROM vehicles WHERE status='Completed'"
)->fetch_assoc()['total'];

$total_companies = $conn->query(
    "SELECT COUNT(*) AS total FROM companies"
)->fetch_assoc()['total'];

$total_employees = $conn->query(
    "SELECT COUNT(*) AS total FROM employees WHERE status='Active'"
)->fetch_assoc()['total'];

$pending_quotations = $conn->query(
    "SELECT COUNT(*) AS total FROM quotations WHERE quotation_status='Pending'"
)->fetch_assoc()['total'];


/* =========================
   TOTAL QUOTATION VALUE
========================= */

$quotation_result = $conn->query(
    "SELECT COALESCE(SUM(estimated_amount),0) AS total
     FROM quotations
     WHERE quotation_status='Approved'"
);

$total_quotation_value = $quotation_result->fetch_assoc()['total'];


/* =========================
   OVERDUE VEHICLES
========================= */

$overdue_query = "
    SELECT 
        v.vehicle_id,
        v.vehicle_number,
        c.company_name,
        v.vehicle_type,
        v.deadline,
        v.status
    FROM vehicles v
    JOIN companies c ON v.company_id = c.company_id
    WHERE v.deadline < CURDATE()
    AND v.status != 'Completed'
    ORDER BY v.deadline ASC
";

$overdue_vehicles = $conn->query($overdue_query);


/* =========================
   RECENT VEHICLES
========================= */

$recent_query = "
    SELECT 
        v.vehicle_number,
        c.company_name,
        v.vehicle_type,
        v.date_received,
        v.deadline,
        v.status
    FROM vehicles v
    JOIN companies c ON v.company_id = c.company_id
    ORDER BY v.vehicle_id DESC
    LIMIT 10
";

$recent_vehicles = $conn->query($recent_query);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reports - Vehicle Pro</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #f3f5f8;
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
    padding: 25px 18px;
}

.logo {
    text-align: center;
    margin-bottom: 35px;
}

.car-icon {
    font-size: 38px;
}

.logo h2 {
    margin: 5px 0;
    font-size: 20px;
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
    margin: 20px 10px 8px;
    text-transform: uppercase;
}

.sidebar a {
    display: block;
    color: #d1d5db;
    text-decoration: none;
    padding: 12px 14px;
    border-radius: 8px;
    margin-bottom: 5px;
    font-size: 14px;
}

.sidebar a span {
    margin-right: 10px;
}

.sidebar a:hover,
.sidebar a.active {
    background: #2563eb;
    color: white;
}


/* =========================
   MAIN CONTENT
========================= */

.main {
    margin-left: 250px;
    padding: 30px;
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
    color: #6b7280;
    margin-top: 6px;
}


/* =========================
   STAT CARDS
========================= */

.stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 22px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.08);
}

.stat-card h4 {
    margin: 0;
    color: #6b7280;
    font-size: 13px;
}

.stat-card .number {
    font-size: 30px;
    font-weight: bold;
    margin-top: 10px;
}

.blue {
    border-left: 5px solid #2563eb;
}

.orange {
    border-left: 5px solid #f59e0b;
}

.green {
    border-left: 5px solid #16a34a;
}

.red {
    border-left: 5px solid #dc2626;
}


/* =========================
   SECONDARY STATS
========================= */

.small-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    margin-bottom: 25px;
}

.small-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.08);
}

.small-card h3 {
    margin: 0 0 8px;
    font-size: 14px;
    color: #6b7280;
}

.small-card strong {
    font-size: 24px;
}


/* =========================
   REPORT BOX
========================= */

.report-box {
    background: white;
    border-radius: 12px;
    padding: 22px;
    margin-bottom: 25px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.08);
}

.report-box h2 {
    margin-top: 0;
    font-size: 20px;
}


/* =========================
   TABLE
========================= */

.table-container {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #f1f5f9;
    padding: 13px;
    text-align: left;
    font-size: 13px;
}

td {
    padding: 13px;
    border-bottom: 1px solid #e5e7eb;
    font-size: 13px;
}

tr:hover {
    background: #f8fafc;
}


/* =========================
   STATUS
========================= */

.status {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: bold;
}

.status.pending {
    background: #fef3c7;
    color: #92400e;
}

.status.progress {
    background: #dbeafe;
    color: #1d4ed8;
}

.status.completed {
    background: #dcfce7;
    color: #166534;
}

.status.overdue {
    background: #fee2e2;
    color: #991b1b;
}


/* =========================
   PRINT BUTTON
========================= */

.print-btn {
    background: #2563eb;
    color: white;
    border: none;
    padding: 11px 18px;
    border-radius: 7px;
    cursor: pointer;
    font-size: 13px;
}

.print-btn:hover {
    background: #1d4ed8;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 1000px) {

    .stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .small-stats {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 700px) {

    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
    }

    .main {
        margin-left: 0;
    }

    .stats {
        grid-template-columns: 1fr;
    }

}


/* =========================
   PRINT
========================= */

@media print {

    .sidebar,
    .print-btn {
        display: none;
    }

    .main {
        margin-left: 0;
    }

    body {
        background: white;
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

    <a href="salary.php">
        <span>💰</span> Salary
    </a>

    <a href="reports.php" class="active">
        <span>📈</span> Reports
    </a>

</div>



<!-- =========================
     MAIN
========================= -->

<div class="main">


    <div class="header">

        <div>

            <h1>Workshop Reports 📈</h1>

            <p>
                Overview of vehicles, employees, quotations and workshop activities.
            </p>

        </div>

        <button class="print-btn" onclick="window.print()">
            🖨 Print Report
        </button>

    </div>



    <!-- =========================
         VEHICLE STATS
    ========================= -->

    <div class="stats">


        <div class="stat-card blue">

            <h4>Total Vehicles</h4>

            <div class="number">
                <?php echo $total_vehicles; ?>
            </div>

        </div>


        <div class="stat-card orange">

            <h4>Pending Vehicles</h4>

            <div class="number">
                <?php echo $pending_vehicles; ?>
            </div>

        </div>


        <div class="stat-card blue">

            <h4>In Progress</h4>

            <div class="number">
                <?php echo $in_progress; ?>
            </div>

        </div>


        <div class="stat-card green">

            <h4>Completed Vehicles</h4>

            <div class="number">
                <?php echo $completed; ?>
            </div>

        </div>


    </div>



    <!-- =========================
         OTHER STATS
    ========================= -->

    <div class="small-stats">


        <div class="small-card">

            <h3>🏢 Total Companies</h3>

            <strong>
                <?php echo $total_companies; ?>
            </strong>

        </div>


        <div class="small-card">

            <h3>👨‍🔧 Active Employees</h3>

            <strong>
                <?php echo $total_employees; ?>
            </strong>

        </div>


        <div class="small-card">

            <h3>🧾 Pending Quotations</h3>

            <strong>
                <?php echo $pending_quotations; ?>
            </strong>

        </div>


    </div>



    <!-- =========================
         QUOTATION VALUE
    ========================= -->

    <div class="report-box">

        <h2>💰 Approved Quotation Value</h2>

        <div style="font-size:30px;font-weight:bold;">

            ₹ <?php echo number_format($total_quotation_value, 2); ?>

        </div>

    </div>



    <!-- =========================
         OVERDUE VEHICLES
    ========================= -->

    <div class="report-box">

        <h2>⚠️ Overdue Vehicles</h2>


        <div class="table-container">

        <table>

            <tr>

                <th>Vehicle Number</th>
                <th>Company</th>
                <th>Vehicle Type</th>
                <th>Deadline</th>
                <th>Status</th>

            </tr>


            <?php if ($overdue_vehicles->num_rows > 0) { ?>

                <?php while ($row = $overdue_vehicles->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($row['vehicle_number']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['company_name']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['vehicle_type']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['deadline']); ?>
                        </td>

                        <td>

                            <span class="status overdue">
                                OVERDUE
                            </span>

                        </td>

                    </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>

                    <td colspan="5" style="text-align:center;">
                        🎉 No overdue vehicles
                    </td>

                </tr>

            <?php } ?>

        </table>

        </div>

    </div>



    <!-- =========================
         RECENT VEHICLES
    ========================= -->

    <div class="report-box">

        <h2>🚗 Recent Vehicles</h2>


        <div class="table-container">

        <table>

            <tr>

                <th>Vehicle Number</th>
                <th>Company</th>
                <th>Type</th>
                <th>Date Received</th>
                <th>Deadline</th>
                <th>Status</th>

            </tr>


            <?php while ($row = $recent_vehicles->fetch_assoc()) { ?>

                <tr>

                    <td>
                        <?php echo htmlspecialchars($row['vehicle_number']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['company_name']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['vehicle_type']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['date_received']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['deadline']); ?>
                    </td>

                    <td>

                        <?php

                        if ($row['status'] == 'Pending') {

                            echo '<span class="status pending">Pending</span>';

                        } elseif ($row['status'] == 'In Progress') {

                            echo '<span class="status progress">In Progress</span>';

                        } else {

                            echo '<span class="status completed">Completed</span>';

                        }

                        ?>

                    </td>

                </tr>

            <?php } ?>


        </table>

        </div>

    </div>


</div>

</body>

</html>
