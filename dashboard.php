<?php
session_start();

/* =========================
   LOGIN PROTECTION
========================= */

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';


/* =========================
   DASHBOARD COUNTS
========================= */

$total_vehicles = 0;
$pending_vehicles = 0;
$progress_vehicles = 0;
$completed_vehicles = 0;
$total_companies = 0;
$active_employees = 0;


/* TOTAL VEHICLES */

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM vehicles"
);

if ($result) {
    $row = $result->fetch_assoc();
    $total_vehicles = $row['total'];
}


/* PENDING VEHICLES */

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM vehicles
     WHERE status='Pending'"
);

if ($result) {
    $row = $result->fetch_assoc();
    $pending_vehicles = $row['total'];
}


/* IN PROGRESS */

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM vehicles
     WHERE status='In Progress'"
);

if ($result) {
    $row = $result->fetch_assoc();
    $progress_vehicles = $row['total'];
}


/* COMPLETED */

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM vehicles
     WHERE status='Completed'"
);

if ($result) {
    $row = $result->fetch_assoc();
    $completed_vehicles = $row['total'];
}


/* TOTAL COMPANIES */

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM companies"
);

if ($result) {
    $row = $result->fetch_assoc();
    $total_companies = $row['total'];
}


/* TOTAL EMPLOYEES */

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM employees"
);

if ($result) {
    $row = $result->fetch_assoc();
    $active_employees = $row['total'];
}


/* =========================
   RECENT VEHICLES
========================= */

$recent_vehicles = $conn->query("
    SELECT
        v.vehicle_number,
        v.vehicle_type,
        v.status,
        c.company_name
    FROM vehicles v
    INNER JOIN companies c
        ON v.company_id = c.company_id
    ORDER BY v.vehicle_id DESC
    LIMIT 5
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Sri Ayyappa Body Builders - Dashboard</title>


<style>

/* =====================================================
   RESET
===================================================== */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}


/* =====================================================
   BODY
===================================================== */

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f4f6f9;
    color: #222;
}


/* =====================================================
   SIDEBAR
===================================================== */

.sidebar {

    position: fixed;

    left: 0;
    top: 0;

    width: 250px;
    height: 100vh;

    background: #182433;

    color: white;

    padding: 25px 15px;

    overflow-y: auto;

    z-index: 1000;
}


/* =====================================================
   SIDEBAR LOGO
===================================================== */

.sidebar .logo {

    width: 100%;

    text-align: center;

    margin-bottom: 30px;
}

.car-icon {

    font-size: 42px;

    margin-bottom: 8px;
}

.sidebar .logo h2 {

    color: white;

    font-size: 22px;

    margin-bottom: 5px;
}

.sidebar .logo p {

    color: #aeb8c5;

    font-size: 11px;

    letter-spacing: 0.5px;
}


/* =====================================================
   MENU TITLES
===================================================== */

.menu-title {

    margin: 22px 0 8px 10px;

    color: #8995a5;

    font-size: 11px;

    font-weight: bold;

    text-transform: uppercase;
}


/* =====================================================
   SIDEBAR LINKS
===================================================== */

.sidebar a {

    display: flex;

    align-items: center;

    width: 100%;

    margin: 4px 0;

    padding: 12px 15px;

    border-radius: 8px;

    color: #dce3eb;

    text-decoration: none;

    font-size: 14px;

    transition: 0.2s;
}

.sidebar a span {

    margin-right: 10px;

    font-size: 16px;
}

.sidebar a:hover {

    background: #263548;

    color: white;
}

.sidebar a.active {

    background: #e63946;

    color: white;
}


/* =====================================================
   MAIN CONTENT
===================================================== */

.main-content {

    margin-left: 250px;

    width: calc(100% - 250px);

    min-height: 100vh;

    padding: 35px;
}


/* =====================================================
   TOP HEADER
===================================================== */

.top-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 22px;
}

.page-title h1 {

    font-size: 30px;

    color: #182433;

    margin-bottom: 7px;
}

.page-title p {

    color: #718096;

    font-size: 14px;
}


/* =====================================================
   USER BOX
===================================================== */

.user-box {

    background: white;

    padding: 12px 20px;

    border-radius: 12px;

    box-shadow: 0 4px 15px rgba(0,0,0,0.06);

    color: #182433;

    font-size: 14px;
}


/* =====================================================
   COMPANY INFORMATION
===================================================== */

.company-banner {

    background: white;

    border-radius: 16px;

    padding: 22px 25px;

    margin-bottom: 25px;

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 25px;

    box-shadow: 0 4px 18px rgba(0,0,0,0.07);

    border-left: 6px solid #e63946;
}


/* COMPANY NAME */

.company-details {

    flex: 1;
}

.company-details h2 {

    color: #182433;

    font-size: 23px;

    margin-bottom: 7px;
}

.company-details p {

    color: #718096;

    font-size: 13px;
}


/* CONTACT DETAILS */

.company-contact {

    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 18px;

    flex-wrap: wrap;
}

.company-contact div {

    background: #f8fafc;

    padding: 10px 13px;

    border-radius: 8px;

    color: #4a5568;

    font-size: 13px;
}

.company-contact strong {

    color: #182433;
}


/* =====================================================
   HERO
===================================================== */

.hero {

    position: relative;

    background: #182433;

    color: white;

    padding: 35px;

    border-radius: 18px;

    margin-bottom: 25px;

    overflow: hidden;

    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.hero h2 {

    font-size: 28px;

    margin-bottom: 12px;
}

.hero p {

    font-size: 14px;

    color: #d7dee7;

    margin-bottom: 25px;

    max-width: 650px;
}

.hero-car {

    position: absolute;

    right: 45px;

    top: 50%;

    transform: translateY(-50%);

    font-size: 80px;
}


/* ADD VEHICLE BUTTON */

.add-btn {

    display: inline-block;

    background: #f5a400;

    color: white;

    padding: 13px 22px;

    border-radius: 9px;

    text-decoration: none;

    font-weight: bold;

    font-size: 14px;

    transition: 0.2s;
}

.add-btn:hover {

    background: #d98f00;
}


/* =====================================================
   STATISTICS
===================================================== */

.stats-grid {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 20px;

    margin-bottom: 25px;
}

.stat-card {

    background: white;

    border-radius: 15px;

    padding: 25px;

    min-height: 145px;

    box-shadow: 0 4px 15px rgba(0,0,0,0.07);

    border-left: 5px solid #e63946;
}

.stat-card.blue {

    border-left-color: #3182ce;
}

.stat-card.orange {

    border-left-color: #f5a400;
}

.stat-card.purple {

    border-left-color: #805ad5;
}

.stat-card.green {

    border-left-color: #10b981;
}

.stat-icon {

    font-size: 28px;

    margin-bottom: 12px;
}

.stat-number {

    font-size: 30px;

    font-weight: bold;

    color: #182433;

    margin-bottom: 5px;
}

.stat-label {

    color: #718096;

    font-size: 14px;
}


/* =====================================================
   SECONDARY CARDS
===================================================== */

.secondary-grid {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 20px;

    margin-bottom: 25px;
}

.info-card {

    background: white;

    border-radius: 15px;

    padding: 25px;

    box-shadow: 0 4px 15px rgba(0,0,0,0.07);
}

.info-card h3 {

    color: #182433;

    font-size: 18px;

    margin-bottom: 10px;
}

.info-number {

    font-size: 30px;

    font-weight: bold;

    color: #e63946;
}


/* =====================================================
   RECENT VEHICLES
===================================================== */

.table-card {

    background: white;

    border-radius: 15px;

    padding: 25px;

    box-shadow: 0 4px 15px rgba(0,0,0,0.07);

    overflow-x: auto;
}

.table-card h2 {

    color: #182433;

    font-size: 20px;

    margin-bottom: 20px;
}

table {

    width: 100%;

    border-collapse: collapse;
}

th {

    background: #182433;

    color: white;

    padding: 13px;

    text-align: left;

    font-size: 13px;
}

td {

    padding: 13px;

    border-bottom: 1px solid #edf0f3;

    font-size: 13px;

    color: #444;
}

tr:hover {

    background: #f8fafc;
}


/* =====================================================
   STATUS
===================================================== */

.status {

    display: inline-block;

    padding: 6px 12px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: bold;
}

.status.pending {

    background: #fff3cd;

    color: #856404;
}

.status.progress {

    background: #cfe2ff;

    color: #084298;
}

.status.completed {

    background: #d1e7dd;

    color: #0f5132;
}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 1100px) {

    .stats-grid {

        grid-template-columns:
            repeat(2, 1fr);
    }

    .company-banner {

        flex-direction: column;

        align-items: flex-start;
    }

    .company-contact {

        justify-content: flex-start;
    }
}


@media (max-width: 700px) {

    .sidebar {

        position: relative;

        width: 100%;

        height: auto;
    }

    .main-content {

        margin-left: 0;

        width: 100%;

        padding: 20px;
    }

    .stats-grid {

        grid-template-columns: 1fr;
    }

    .secondary-grid {

        grid-template-columns: 1fr;
    }

    .top-header {

        flex-direction: column;

        align-items: flex-start;

        gap: 15px;
    }

    .hero-car {

        display: none;
    }

    .company-contact {

        flex-direction: column;

        align-items: flex-start;

        width: 100%;
    }

    .company-contact div {

        width: 100%;
    }
}

</style>

</head>


<body>


<!-- =====================================================
     SIDEBAR
===================================================== -->

<div class="sidebar">

    <div class="logo">

        <div class="car-icon">
            🚘
        </div>

        <h2>
            VEHICLE PRO
        </h2>

        <p>
            WORKSHOP MANAGEMENT
        </p>

    </div>


    <div class="menu-title">
        Main Menu
    </div>


    <a href="dashboard.php" class="active">

        <span>📊</span>

        Dashboard

    </a>


    <a href="companies.php">

        <span>🏢</span>

        Companies

    </a>


    <a href="vehicles.php">

        <span>🚗</span>

        Vehicles

    </a>


    <a href="employees.php">

        <span>👨‍🔧</span>

        Employees

    </a>


    <div class="menu-title">
        Workshop
    </div>


    <a href="services.php">

        <span>🔧</span>

        Services

    </a>


    <a href="assignments.php">

        <span>📋</span>

        Work Assignment

    </a>


    <a href="quotations.php">

        <span>🧾</span>

        Quotations

    </a>


    <div class="menu-title">
        Management
    </div>


    <a href="salary.php">

        <span>💰</span>

        Salary

    </a>


    <a href="reports.php">

        <span>📈</span>

        Reports

    </a>


    <div class="menu-title">
        Account
    </div>


    <a href="logout.php">

        <span>🚪</span>

        Logout

    </a>

</div>



<!-- =====================================================
     MAIN CONTENT
===================================================== -->

<div class="main-content">


    <!-- =================================================
         TOP HEADER
    ================================================== -->

    <div class="top-header">

        <div class="page-title">

            <h1>
                Dashboard
            </h1>

            <p>
                Welcome to Vehicle Pro Workshop Management System
            </p>

        </div>


        <div class="user-box">

            👤

            <?php
            echo htmlspecialchars(
                $_SESSION['username']
            );
            ?>

        </div>

    </div>



    <!-- =================================================
         COMPANY INFORMATION
    ================================================== -->

    <div class="company-banner">


        <div class="company-details">

            <h2>
                🏢 Sri Ayyappa Body Builders
            </h2>

            <p>
                Vehicle Body Building & Service
            </p>

        </div>


        <div class="company-contact">


            <div>

                📍

                <strong>
                    Location:
                </strong>

                Ariyalur, Chennai

            </div>


            <div>

                📞

                <strong>
                    Phone:
                </strong>

                9444454933

            </div>


            <div>

                ✉️

                <strong>
                    Email:
                </strong>

                sriayyappabodybuilders@gmail.com

            </div>


        </div>

    </div>



    <!-- =================================================
         HERO SECTION
    ================================================== -->

    <div class="hero">


        <h2>
            Keep Every Vehicle Moving 🚘
        </h2>


        <p>

            Manage vehicles, companies, employees,
            services, quotations and workshop operations
            in one place.

        </p>


        <a href="vehicles.php"
           class="add-btn">

            + Add New Vehicle

        </a>


        <div class="hero-car">

            🚙

        </div>


    </div>



    <!-- =================================================
         STATISTICS
    ================================================== -->

    <div class="stats-grid">


        <!-- TOTAL VEHICLES -->

        <div class="stat-card blue">

            <div class="stat-icon">
                🚗
            </div>

            <div class="stat-number">

                <?php
                echo $total_vehicles;
                ?>

            </div>

            <div class="stat-label">

                Total Vehicles

            </div>

        </div>



        <!-- PENDING -->

        <div class="stat-card orange">

            <div class="stat-icon">
                ⏳
            </div>

            <div class="stat-number">

                <?php
                echo $pending_vehicles;
                ?>

            </div>

            <div class="stat-label">

                Pending Vehicles

            </div>

        </div>



        <!-- IN PROGRESS -->

        <div class="stat-card purple">

            <div class="stat-icon">
                🔧
            </div>

            <div class="stat-number">

                <?php
                echo $progress_vehicles;
                ?>

            </div>

            <div class="stat-label">

                In Progress

            </div>

        </div>



        <!-- COMPLETED -->

        <div class="stat-card green">

            <div class="stat-icon">
                ✅
            </div>

            <div class="stat-number">

                <?php
                echo $completed_vehicles;
                ?>

            </div>

            <div class="stat-label">

                Completed

            </div>

        </div>


    </div>



    <!-- =================================================
         COMPANIES / EMPLOYEES
    ================================================== -->

    <div class="secondary-grid">


        <!-- COMPANIES -->

        <div class="info-card">

            <h3>
                🏢 Total Companies
            </h3>

            <div class="info-number">

                <?php
                echo $total_companies;
                ?>

            </div>

        </div>



        <!-- EMPLOYEES -->

        <div class="info-card">

            <h3>
                👨‍🔧 Active Employees
            </h3>

            <div class="info-number">

                <?php
                echo $active_employees;
                ?>

            </div>

        </div>


    </div>



    <!-- =================================================
         RECENT VEHICLES
    ================================================== -->

    <div class="table-card">


        <h2>
            📋 Recent Vehicles
        </h2>


        <table>


            <thead>

                <tr>

                    <th>
                        Vehicle Number
                    </th>

                    <th>
                        Company
                    </th>

                    <th>
                        Type
                    </th>

                    <th>
                        Status
                    </th>

                </tr>

            </thead>


            <tbody>


            <?php

            if (
                $recent_vehicles &&
                $recent_vehicles->num_rows > 0
            ):

            ?>


                <?php

                while (
                    $vehicle =
                    $recent_vehicles->fetch_assoc()
                ):

                ?>


                <tr>


                    <!-- VEHICLE NUMBER -->

                    <td>

                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $vehicle['vehicle_number']
                            );

                            ?>

                        </strong>

                    </td>


                    <!-- COMPANY -->

                    <td>

                        <?php

                        echo htmlspecialchars(
                            $vehicle['company_name']
                        );

                        ?>

                    </td>


                    <!-- TYPE -->

                    <td>

                        <?php

                        echo htmlspecialchars(
                            $vehicle['vehicle_type']
                        );

                        ?>

                    </td>


                    <!-- STATUS -->

                    <td>


                        <?php

                        $status_class = "pending";


                        if (
                            $vehicle['status']
                            == "In Progress"
                        ) {

                            $status_class = "progress";

                        }


                        if (
                            $vehicle['status']
                            == "Completed"
                        ) {

                            $status_class = "completed";

                        }

                        ?>


                        <span
                            class="status
                            <?php
                            echo $status_class;
                            ?>"
                        >

                            <?php

                            echo htmlspecialchars(
                                $vehicle['status']
                            );

                            ?>

                        </span>


                    </td>


                </tr>


                <?php

                endwhile;

                ?>


            <?php else: ?>


                <tr>

                    <td
                        colspan="4"
                        style="
                        text-align:center;
                        padding:25px;
                        "
                    >

                        No vehicles found.

                    </td>

                </tr>


            <?php endif; ?>


            </tbody>


        </table>


    </div>


</div>


</body>

</html>