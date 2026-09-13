<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>

/* ================================
   VEHICLE PRO SIDEBAR
================================ */

.sidebar {
    position: fixed;
    top: 0;
    left: 0;

    width: 250px;
    height: 100vh;

    background: #182433;
    color: white;

    padding: 25px 15px;

    overflow-y: auto;

    z-index: 9999;

    box-sizing: border-box;
}


/* LOGO */

.sidebar .logo {
    width: 100%;
    margin: 0 0 30px 0;
    padding: 0;

    background: transparent;

    text-align: center;

    box-shadow: none;
}

.sidebar .car-icon {
    width: 100%;

    margin: 0 0 8px 0;
    padding: 0;

    background: transparent;

    font-size: 38px;

    text-align: center;
}

.sidebar .logo h2 {
    margin: 0;
    padding: 0;

    background: transparent;

    color: white;

    font-size: 22px;
}

.sidebar .logo p {
    margin: 5px 0 0 0;
    padding: 0;

    background: transparent;

    color: #aeb8c5;

    font-size: 11px;
}


/* MENU TITLE */

.sidebar .menu-title {
    display: block;

    width: 100%;

    margin: 22px 0 8px 0;
    padding: 0 10px;

    background: transparent;

    color: #8995a5;

    font-size: 11px;

    font-weight: bold;

    text-transform: uppercase;

    box-sizing: border-box;
}


/* LINKS */

.sidebar a {
    display: flex;

    align-items: center;

    width: 100%;

    margin: 4px 0;
    padding: 12px 15px;

    background: transparent;

    color: #dce3eb;

    text-decoration: none;

    border-radius: 8px;

    font-size: 14px;

    box-sizing: border-box;

    transition: all 0.2s ease;
}


/* ICON */

.sidebar a span {
    display: inline-block;

    width: 25px;

    margin-right: 8px;

    font-size: 16px;

    text-align: center;
}


/* HOVER = RED */

.sidebar a:hover {
    background: #e63946;

    color: white;

    transform: translateX(3px);
}


/* CURRENT PAGE = RED */

.sidebar a.active {
    background: #e63946;

    color: white;

    font-weight: bold;

    box-shadow: 0 4px 10px rgba(230, 57, 70, 0.25);
}


/* LOGOUT */

.sidebar a.logout {
    margin-top: 10px;

    color: #ffb3b3;
}

.sidebar a.logout:hover {
    background: #e63946;

    color: white;
}


/* MOBILE */

@media (max-width: 700px) {

    .sidebar {
        position: relative;

        width: 100%;

        height: auto;
    }

}

</style>


<div class="sidebar">

    <!-- LOGO -->

    <div class="logo">

        <div class="car-icon">🚘</div>

        <h2>VEHICLE PRO</h2>

        <p>WORKSHOP MANAGEMENT</p>

    </div>


    <!-- MAIN MENU -->

    <div class="menu-title">
        Main Menu
    </div>


    <a href="dashboard.php"
       class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">

        <span>📊</span>
        Dashboard

    </a>


    <a href="companies.php"
       class="<?php echo ($current_page == 'companies.php') ? 'active' : ''; ?>">

        <span>🏢</span>
        Companies

    </a>


    <a href="vehicles.php"
       class="<?php echo ($current_page == 'vehicles.php') ? 'active' : ''; ?>">

        <span>🚗</span>
        Vehicles

    </a>


    <a href="employees.php"
       class="<?php echo ($current_page == 'employees.php') ? 'active' : ''; ?>">

        <span>👨‍🔧</span>
        Employees

    </a>


    <!-- WORKSHOP -->

    <div class="menu-title">
        Workshop
    </div>


    <a href="services.php"
       class="<?php echo ($current_page == 'services.php') ? 'active' : ''; ?>">

        <span>🔧</span>
        Services

    </a>


    <a href="assignments.php"
       class="<?php echo ($current_page == 'assignments.php') ? 'active' : ''; ?>">

        <span>📋</span>
        Work Assignment

    </a>


    <a href="quotations.php"
       class="<?php echo ($current_page == 'quotations.php') ? 'active' : ''; ?>">

        <span>🧾</span>
        Quotations

    </a>


    <!-- MANAGEMENT -->

    <div class="menu-title">
        Management
    </div>
    
    <a href="company_profile.php" class="<?php echo ($current_page == 'company_profile.php') ? 'active' : ''; ?>">
    <span>🏢</span>
    Company Profile
    </a>


    <a href="salary.php"
       class="<?php echo ($current_page == 'salary.php') ? 'active' : ''; ?>">

        <span>💰</span>
        Salary

    </a>


    <a href="reports.php"
       class="<?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">

        <span>📈</span>
        Reports

    </a>


    <!-- ACCOUNT -->

    <div class="menu-title">
        Account
    </div>


    <a href="logout.php" class="logout">

        <span>🚪</span>
        Logout

    </a>

</div>