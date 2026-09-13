<?php
require_once 'includes/db.php';

$edit_mode = false;
$edit_service = null;

/* =========================
   ADD SERVICE
========================= */
if (isset($_POST['add_service'])) {

    $service_name = $_POST['service_name'];
    $description = $_POST['description'];

    $stmt = $conn->prepare(
        "INSERT INTO services (service_name, description)
         VALUES (?, ?)"
    );

    $stmt->bind_param("ss", $service_name, $description);
    $stmt->execute();
    $stmt->close();

    header("Location: services.php");
    exit();
}


/* =========================
   UPDATE SERVICE
========================= */
if (isset($_POST['update_service'])) {

    $service_id = $_POST['service_id'];
    $service_name = $_POST['service_name'];
    $description = $_POST['description'];

    $stmt = $conn->prepare(
        "UPDATE services
         SET service_name = ?, description = ?
         WHERE service_id = ?"
    );

    $stmt->bind_param(
        "ssi",
        $service_name,
        $description,
        $service_id
    );

    $stmt->execute();
    $stmt->close();

    header("Location: services.php");
    exit();
}


/* =========================
   DELETE SERVICE
========================= */
if (isset($_GET['delete'])) {

    $service_id = $_GET['delete'];

    $stmt = $conn->prepare(
        "DELETE FROM services WHERE service_id = ?"
    );

    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $stmt->close();

    header("Location: services.php");
    exit();
}


/* =========================
   EDIT SERVICE
========================= */
if (isset($_GET['edit'])) {

    $service_id = $_GET['edit'];

    $stmt = $conn->prepare(
        "SELECT * FROM services WHERE service_id = ?"
    );

    $stmt->bind_param("i", $service_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $edit_service = $result->fetch_assoc();
        $edit_mode = true;
    }

    $stmt->close();
}


/* =========================
   GET ALL SERVICES
========================= */
$services = $conn->query(
    "SELECT * FROM services ORDER BY service_id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Services - Vehicle Pro</title>

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

.form-group {
    display: flex;
    flex-direction: column;
    margin-bottom: 18px;
}

.form-group label {
    font-size: 13px;
    font-weight: bold;
    margin-bottom: 7px;
    color: #374151;
}

.form-group input,
.form-group textarea {
    padding: 11px 12px;
    border: 1px solid #d1d5db;
    border-radius: 7px;
    font-size: 14px;
    outline: none;
    font-family: Arial, Helvetica, sans-serif;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #2563eb;
}

.form-group textarea {
    min-height: 90px;
    resize: vertical;
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
   RESPONSIVE
========================= */

@media (max-width: 800px) {

    .sidebar {
        width: 210px;
    }

    .main {
        margin-left: 210px;
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

    <a href="services.php" class="active">
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

        <h1>Services 🔧</h1>

        <p>
            Manage the services provided by the workshop
        </p>

    </div>


    <!-- =========================
         ADD / EDIT SERVICE
    ========================= -->

    <div class="card">

        <h2>
            <?php
            echo $edit_mode
                ? "✏️ Edit Service"
                : "➕ Add New Service";
            ?>
        </h2>


        <form method="POST">

            <?php if ($edit_mode): ?>

                <input
                    type="hidden"
                    name="service_id"
                    value="<?php echo $edit_service['service_id']; ?>"
                >

            <?php endif; ?>


            <div class="form-group">

                <label>Service Name</label>

                <input
                    type="text"
                    name="service_name"
                    placeholder="Enter service name"
                    required
                    value="<?php
                    echo $edit_mode
                        ? htmlspecialchars(
                            $edit_service['service_name']
                        )
                        : '';
                    ?>"
                >

            </div>


            <div class="form-group">

                <label>Description</label>

                <textarea
                    name="description"
                    placeholder="Enter service description"
                ><?php
                echo $edit_mode
                    ? htmlspecialchars(
                        $edit_service['description']
                    )
                    : '';
                ?></textarea>

            </div>


            <div class="buttons">

                <?php if ($edit_mode): ?>

                    <button
                        type="submit"
                        name="update_service"
                        class="btn btn-update"
                    >
                        Update Service
                    </button>

                    <a
                        href="services.php"
                        class="btn btn-cancel"
                    >
                        Cancel
                    </a>

                <?php else: ?>

                    <button
                        type="submit"
                        name="add_service"
                        class="btn btn-add"
                    >
                        Add Service
                    </button>

                <?php endif; ?>

            </div>

        </form>

    </div>


    <!-- =========================
         SERVICE LIST
    ========================= -->

    <div class="table-card">

        <h2>Service List 🔧</h2>

        <table>

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Service Name</th>
                    <th>Description</th>
                    <th>Action</th>

                </tr>

            </thead>


            <tbody>

                <?php if ($services->num_rows > 0): ?>

                    <?php while ($row = $services->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php
                                echo $row['service_id'];
                                ?>
                            </td>

                            <td>
                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $row['service_name']
                                    );
                                    ?>
                                </strong>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['description']
                                );
                                ?>
                            </td>

                            <td>

                                <a
                                    href="services.php?edit=<?php echo $row['service_id']; ?>"
                                    class="edit-btn"
                                >
                                    Edit
                                </a>

                                <a
                                    href="services.php?delete=<?php echo $row['service_id']; ?>"
                                    class="delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this service?');"
                                >
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="4"
                            style="text-align:center; padding:30px;"
                        >
                            No services found. Add your first service above.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>