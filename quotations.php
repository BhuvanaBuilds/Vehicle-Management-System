<?php
require_once 'includes/db.php';

$edit_mode = false;
$edit_quotation = null;

/* =========================
   ADD QUOTATION
========================= */
if (isset($_POST['add_quotation'])) {

    $company_id = $_POST['company_id'];
    $vehicle_id = !empty($_POST['vehicle_id'])
        ? $_POST['vehicle_id']
        : null;

    $quotation_date = $_POST['quotation_date'];
    $estimated_amount = $_POST['estimated_amount'];
    $quotation_status = $_POST['quotation_status'];

    $stmt = $conn->prepare(
        "INSERT INTO quotations
        (company_id, vehicle_id, quotation_date,
         estimated_amount, quotation_status)
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "iidss",
        $company_id,
        $vehicle_id,
        $quotation_date,
        $estimated_amount,
        $quotation_status
    );

    $stmt->execute();
    $stmt->close();

    header("Location: quotations.php");
    exit();
}


/* =========================
   UPDATE QUOTATION
========================= */
if (isset($_POST['update_quotation'])) {

    $quotation_id = $_POST['quotation_id'];
    $company_id = $_POST['company_id'];
    $vehicle_id = !empty($_POST['vehicle_id'])
        ? $_POST['vehicle_id']
        : null;

    $quotation_date = $_POST['quotation_date'];
    $estimated_amount = $_POST['estimated_amount'];
    $quotation_status = $_POST['quotation_status'];

    $stmt = $conn->prepare(
        "UPDATE quotations SET
        company_id = ?,
        vehicle_id = ?,
        quotation_date = ?,
        estimated_amount = ?,
        quotation_status = ?
        WHERE quotation_id = ?"
    );

    $stmt->bind_param(
        "iisdsi",
        $company_id,
        $vehicle_id,
        $quotation_date,
        $estimated_amount,
        $quotation_status,
        $quotation_id
    );

    $stmt->execute();
    $stmt->close();

    header("Location: quotations.php");
    exit();
}


/* =========================
   DELETE QUOTATION
========================= */
if (isset($_GET['delete'])) {

    $quotation_id = $_GET['delete'];

    $stmt = $conn->prepare(
        "DELETE FROM quotations
         WHERE quotation_id = ?"
    );

    $stmt->bind_param("i", $quotation_id);
    $stmt->execute();
    $stmt->close();

    header("Location: quotations.php");
    exit();
}


/* =========================
   EDIT QUOTATION
========================= */
if (isset($_GET['edit'])) {

    $quotation_id = $_GET['edit'];

    $stmt = $conn->prepare(
        "SELECT * FROM quotations
         WHERE quotation_id = ?"
    );

    $stmt->bind_param("i", $quotation_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $edit_quotation = $result->fetch_assoc();
        $edit_mode = true;
    }

    $stmt->close();
}


/* =========================
   GET COMPANIES
========================= */
$companies = $conn->query(
    "SELECT company_id, company_name
     FROM companies
     ORDER BY company_name"
);


/* =========================
   GET VEHICLES
========================= */
$vehicles = $conn->query(
    "SELECT vehicle_id, vehicle_number, company_id
     FROM vehicles
     ORDER BY vehicle_number"
);


/* =========================
   GET QUOTATIONS
========================= */
$quotations = $conn->query(
    "SELECT
        q.quotation_id,
        q.company_id,
        q.vehicle_id,
        q.quotation_date,
        q.estimated_amount,
        q.quotation_status,
        c.company_name,
        v.vehicle_number
     FROM quotations q
     INNER JOIN companies c
        ON q.company_id = c.company_id
     LEFT JOIN vehicles v
        ON q.vehicle_id = v.vehicle_id
     ORDER BY q.quotation_id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Quotations - Vehicle Pro</title>

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


/* SIDEBAR */

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


/* MAIN */

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


/* CARD */

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


/* BUTTONS */

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


/* TABLE */

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


/* ACTIONS */

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


/* STATUS */

.status {
    font-weight: bold;
}

.pending {
    color: #d97706;
}

.sent {
    color: #2563eb;
}

.approved {
    color: #15803d;
}

.rejected {
    color: #dc2626;
}


/* RESPONSIVE */

@media (max-width: 900px) {

    .form-grid {
        grid-template-columns: 1fr;
    }

}

</style>

</head>

<body>


<!-- SIDEBAR -->

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

    <a href="quotations.php" class="active">
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


<!-- MAIN -->

<div class="main">

    <div class="header">

        <h1>Quotations 🧾</h1>

        <p>
            Manage customer quotations and estimated costs
        </p>

    </div>


    <!-- FORM -->

    <div class="card">

        <h2>
            <?php
            echo $edit_mode
                ? "✏️ Edit Quotation"
                : "➕ Create New Quotation";
            ?>
        </h2>


        <form method="POST">

            <?php if ($edit_mode): ?>

                <input
                    type="hidden"
                    name="quotation_id"
                    value="<?php
                    echo $edit_quotation['quotation_id'];
                    ?>"
                >

            <?php endif; ?>


            <div class="form-grid">


                <!-- COMPANY -->

                <div class="form-group">

                    <label>Customer Company</label>

                    <select name="company_id" required>

                        <option value="">
                            Select Company
                        </option>

                        <?php while ($company = $companies->fetch_assoc()): ?>

                            <option
                                value="<?php
                                echo $company['company_id'];
                                ?>"
                                <?php
                                if (
                                    $edit_mode &&
                                    $edit_quotation['company_id']
                                    == $company['company_id']
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >
                                <?php
                                echo htmlspecialchars(
                                    $company['company_name']
                                );
                                ?>
                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <!-- VEHICLE -->

                <div class="form-group">

                    <label>Vehicle</label>

                    <select name="vehicle_id">

                        <option value="">
                            Select Vehicle (Optional)
                        </option>

                        <?php while ($vehicle = $vehicles->fetch_assoc()): ?>

                            <option
                                value="<?php
                                echo $vehicle['vehicle_id'];
                                ?>"
                                <?php
                                if (
                                    $edit_mode &&
                                    $edit_quotation['vehicle_id']
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


                <!-- DATE -->

                <div class="form-group">

                    <label>Quotation Date</label>

                    <input
                        type="date"
                        name="quotation_date"
                        required
                        value="<?php
                        echo $edit_mode
                            ? $edit_quotation['quotation_date']
                            : date('Y-m-d');
                        ?>"
                    >

                </div>


                <!-- AMOUNT -->

                <div class="form-group">

                    <label>Estimated Amount (₹)</label>

                    <input
                        type="number"
                        name="estimated_amount"
                        step="0.01"
                        min="0"
                        placeholder="Enter estimated amount"
                        required
                        value="<?php
                        echo $edit_mode
                            ? $edit_quotation['estimated_amount']
                            : '';
                        ?>"
                    >

                </div>


                <!-- STATUS -->

                <div class="form-group">

                    <label>Quotation Status</label>

                    <select name="quotation_status">

                        <option
                            value="Pending"
                            <?php
                            if (
                                !$edit_mode ||
                                $edit_quotation['quotation_status']
                                == 'Pending'
                            ) echo 'selected';
                            ?>
                        >
                            Pending
                        </option>

                        <option
                            value="Sent"
                            <?php
                            if (
                                $edit_mode &&
                                $edit_quotation['quotation_status']
                                == 'Sent'
                            ) echo 'selected';
                            ?>
                        >
                            Sent
                        </option>

                        <option
                            value="Approved"
                            <?php
                            if (
                                $edit_mode &&
                                $edit_quotation['quotation_status']
                                == 'Approved'
                            ) echo 'selected';
                            ?>
                        >
                            Approved
                        </option>

                        <option
                            value="Rejected"
                            <?php
                            if (
                                $edit_mode &&
                                $edit_quotation['quotation_status']
                                == 'Rejected'
                            ) echo 'selected';
                            ?>
                        >
                            Rejected
                        </option>

                    </select>

                </div>

            </div>


            <div class="buttons">

                <?php if ($edit_mode): ?>

                    <button
                        type="submit"
                        name="update_quotation"
                        class="btn btn-update"
                    >
                        Update Quotation
                    </button>

                    <a
                        href="quotations.php"
                        class="btn btn-cancel"
                    >
                        Cancel
                    </a>

                <?php else: ?>

                    <button
                        type="submit"
                        name="add_quotation"
                        class="btn btn-add"
                    >
                        Create Quotation
                    </button>

                <?php endif; ?>

            </div>

        </form>

    </div>


    <!-- QUOTATION LIST -->

    <div class="table-card">

        <h2>Quotation List 🧾</h2>

        <table>

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Company</th>
                    <th>Vehicle</th>
                    <th>Date</th>
                    <th>Estimated Amount</th>
                    <th>Status</th>
                    <th>Action</th>

                </tr>

            </thead>


            <tbody>

                <?php if ($quotations->num_rows > 0): ?>

                    <?php while ($row = $quotations->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php
                                echo $row['quotation_id'];
                                ?>
                            </td>

                            <td>
                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $row['company_name']
                                    );
                                    ?>
                                </strong>
                            </td>

                            <td>
                                <?php
                                echo $row['vehicle_number']
                                    ? htmlspecialchars(
                                        $row['vehicle_number']
                                    )
                                    : '-';
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['quotation_date']
                                );
                                ?>
                            </td>

                            <td>
                                ₹
                                <?php
                                echo number_format(
                                    $row['estimated_amount'],
                                    2
                                );
                                ?>
                            </td>

                            <td>

                                <?php
                                $status = $row['quotation_status'];
                                $class = strtolower($status);
                                ?>

                                <span class="status <?php echo $class; ?>">
                                    ● <?php echo htmlspecialchars($status); ?>
                                </span>

                            </td>

                            <td>

                                <a
                                    href="quotations.php?edit=<?php
                                    echo $row['quotation_id'];
                                    ?>"
                                    class="edit-btn"
                                >
                                    Edit
                                </a>

                                <a
                                    href="quotations.php?delete=<?php
                                    echo $row['quotation_id'];
                                    ?>"
                                    class="delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this quotation?');"
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
                            No quotations found.
                            Create your first quotation above.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>