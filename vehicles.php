<?php

require_once 'includes/db.php';

$message = "";
$edit_mode = false;
$edit_vehicle = null;


/* =====================================================
   UPDATE VEHICLE
===================================================== */

if (isset($_POST['update_vehicle'])) {

    $vehicle_id = intval($_POST['vehicle_id']);
    $company_id = intval($_POST['company_id']);
    $vehicle_number = $_POST['vehicle_number'];
    $vehicle_type = $_POST['vehicle_type'];
    $chassis_number = $_POST['chassis_number'];
    $date_received = $_POST['date_received'];
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];

    $sql = "UPDATE vehicles
            SET company_id = ?,
                vehicle_number = ?,
                vehicle_type = ?,
                chassis_number = ?,
                date_received = ?,
                deadline = ?,
                status = ?
            WHERE vehicle_id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "issssssi",
            $company_id,
            $vehicle_number,
            $vehicle_type,
            $chassis_number,
            $date_received,
            $deadline,
            $status,
            $vehicle_id
        );

        if ($stmt->execute()) {
            $message = "Vehicle updated successfully!";
        } else {
            $message = "Error updating vehicle: " . $stmt->error;
        }

        $stmt->close();

    } else {
        $message = "Database error: " . $conn->error;
    }
}


/* =====================================================
   DELETE VEHICLE
===================================================== */

if (isset($_GET['delete'])) {

    $vehicle_id = intval($_GET['delete']);

    $stmt = $conn->prepare(
        "DELETE FROM vehicles WHERE vehicle_id = ?"
    );

    if ($stmt) {

        $stmt->bind_param("i", $vehicle_id);

        if ($stmt->execute()) {
            $message = "Vehicle deleted successfully!";
        } else {
            $message = "Cannot delete vehicle. It may have work assignments or quotations linked to it.";
        }

        $stmt->close();
    }
}


/* =====================================================
   EDIT VEHICLE
===================================================== */

if (isset($_GET['edit'])) {

    $vehicle_id = intval($_GET['edit']);

    $stmt = $conn->prepare(
        "SELECT * FROM vehicles WHERE vehicle_id = ?"
    );

    if ($stmt) {

        $stmt->bind_param("i", $vehicle_id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $edit_vehicle = $result->fetch_assoc();
            $edit_mode = true;

        }

        $stmt->close();
    }
}


/* =====================================================
   ADD VEHICLE
===================================================== */

if (isset($_POST['add_vehicle'])) {

    $company_id = $_POST['company_id'];
    $vehicle_number = $_POST['vehicle_number'];
    $vehicle_type = $_POST['vehicle_type'];
    $chassis_number = $_POST['chassis_number'];
    $date_received = $_POST['date_received'];
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];

    $sql = "INSERT INTO vehicles
            (company_id, vehicle_number, vehicle_type, chassis_number,
             date_received, deadline, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "issssss",
            $company_id,
            $vehicle_number,
            $vehicle_type,
            $chassis_number,
            $date_received,
            $deadline,
            $status
        );

        if ($stmt->execute()) {
            $message = "Vehicle added successfully!";
        } else {
            $message = "Error adding vehicle: " . $stmt->error;
        }

        $stmt->close();

    } else {
        $message = "Database error: " . $conn->error;
    }
}


/* =====================================================
   GET COMPANIES
===================================================== */

$companies = $conn->query(
    "SELECT * FROM companies ORDER BY company_name ASC"
);


/* =====================================================
   GET VEHICLES
===================================================== */

$vehicles = $conn->query("
    SELECT
        v.vehicle_id,
        v.vehicle_number,
        v.vehicle_type,
        v.chassis_number,
        v.date_received,
        v.deadline,
        v.status,
        c.company_name
    FROM vehicles v
    INNER JOIN companies c
        ON v.company_id = c.company_id
    ORDER BY v.vehicle_id DESC
");

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Vehicles - Vehicle Pro</title>


    <!-- =================================================
         ALL CSS IS HERE
    ================================================== -->

    <style>

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            padding: 0;

            font-family: Arial, Helvetica, sans-serif;

            background: #f4f6f9;

            color: #222;
        }


        /* =============================================
           SIDEBAR
        ============================================= */

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


        .sidebar .logo {
            display: block;

            width: 100%;

            margin: 0 0 30px 0;
            padding: 0;

            background: transparent;

            text-align: center;

            box-shadow: none;

            border-radius: 0;
        }


        .sidebar .car-icon {
            display: block;

            width: 100%;

            margin: 0 0 8px 0;
            padding: 0;

            background: transparent;

            font-size: 40px;

            box-shadow: none;
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

            font-weight: normal;

            box-shadow: none;
        }


        .sidebar .menu-title {
            display: block;

            width: 100%;

            margin: 20px 0 8px 0;
            padding: 0 10px;

            background: transparent;

            color: #8995a5;

            font-size: 11px;

            font-weight: bold;

            text-transform: uppercase;

            box-shadow: none;
        }


        .sidebar a {
            display: block;

            width: 100%;

            margin: 4px 0;
            padding: 12px 15px;

            background: transparent;

            color: #dce3eb;

            text-decoration: none;

            text-align: left;

            border-radius: 8px;

            box-shadow: none;

            font-size: 14px;
        }


        .sidebar a:hover {
            background: #263548;

            color: white;
        }


        .sidebar a.active {
            background: #e63946;

            color: white;
        }


        /* =============================================
           MAIN CONTENT
        ============================================= */

        .main-content {
            margin-left: 250px;

            width: calc(100% - 250px);

            min-height: 100vh;

            padding: 35px;
        }


        /* =============================================
           PAGE HEADER
        ============================================= */

        .page-header {
            display: flex;

            width: 100%;

            margin: 0 0 25px 0;
            padding: 0;

            background: transparent;

            justify-content: space-between;

            align-items: center;

            box-shadow: none;
        }


        .page-header h1 {
            margin: 0;

            padding: 0;

            background: transparent;

            color: #222;

            font-size: 30px;
        }


        .page-header p {
            margin: 7px 0 0 0;

            padding: 0;

            background: transparent;

            color: #777;

            font-size: 14px;

            font-weight: normal;
        }


        /* =============================================
           FORM CARD
        ============================================= */

        .form-card {
            display: block;

            width: 100%;

            margin: 0 0 30px 0;
            padding: 28px;

            background: white;

            border-radius: 14px;

            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }


        .form-card h2 {
            margin: 0 0 25px 0;

            padding: 0;

            background: transparent;

            color: #2c3e50;

            font-size: 22px;
        }


        /* =============================================
           FORM GRID
        ============================================= */

        .form-grid {
            display: grid;

            width: 100%;

            margin: 0;
            padding: 0;

            background: transparent;

            grid-template-columns: 1fr 1fr;

            gap: 20px;

            box-shadow: none;
        }


        .form-group {
            display: flex;

            width: 100%;

            margin: 0;
            padding: 0;

            background: transparent;

            flex-direction: column;

            box-shadow: none;
        }


        .form-group label {
            display: block;

            width: 100%;

            margin: 0 0 8px 0;
            padding: 0;

            background: transparent;

            color: #444;

            font-size: 14px;

            font-weight: bold;

            box-shadow: none;
        }


        .form-group input,
        .form-group select {

            display: block;

            width: 100%;

            margin: 0;
            padding: 12px;

            background: white;

            border: 1px solid #d6d9dd;

            border-radius: 7px;

            font-size: 14px;

            outline: none;
        }


        .form-group input:focus,
        .form-group select:focus {

            border-color: #e63946;
        }


        /* =============================================
           BUTTON
        ============================================= */

        .submit-btn {

            display: inline-block;

            margin-top: 22px;

            padding: 13px 28px;

            background: #222;

            color: white;

            border: none;

            border-radius: 7px;

            font-size: 14px;

            font-weight: bold;

            cursor: pointer;
        }


        .submit-btn:hover {

            background: #e63946;
        }


        /* =============================================
           SUCCESS MESSAGE
        ============================================= */

        .message {

            display: block;

            width: 100%;

            margin: 0 0 20px 0;

            padding: 13px 15px;

            background: #dff6e4;

            color: #18743c;

            border-radius: 7px;

            font-size: 14px;
        }


        /* =============================================
           TABLE CARD
        ============================================= */

        .table-card {

            display: block;

            width: 100%;

            margin: 0;

            padding: 28px;

            background: white;

            border-radius: 14px;

            box-shadow: 0 4px 15px rgba(0,0,0,0.08);

            overflow-x: auto;
        }


        .table-card h2 {

            margin: 0 0 20px 0;

            padding: 0;

            background: transparent;

            color: #2c3e50;

            font-size: 22px;
        }


        /* =============================================
           TABLE
        ============================================= */

        .table-card table {

            width: 100%;

            margin: 0;

            padding: 0;

            border-collapse: collapse;

            background: white;
        }


        .table-card th {

            padding: 14px;

            background: #222;

            color: white;

            text-align: left;

            font-size: 13px;
        }


        .table-card td {

            padding: 14px;

            border-bottom: 1px solid #eee;

            color: #444;

            font-size: 13px;
        }


        .table-card tr:hover {

            background: #f8f9fa;
        }


        /* =============================================
           STATUS
        ============================================= */

        .status {

            display: inline-block;

            padding: 6px 11px;

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


        /* =============================================
           ACTION BUTTONS
        ============================================= */

        .edit-btn {
            display: inline-block;
            padding: 7px 12px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            margin-right: 5px;
        }

        .edit-btn:hover {
            background: #217dbb;
        }

        .delete-btn {
            display: inline-block;
            padding: 7px 12px;
            background: #e63946;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }

        .delete-btn:hover {
            background: #c62828;
        }

        .cancel-btn {
            display: inline-block;
            margin-top: 22px;
            padding: 13px 28px;
            background: #ddd;
            color: #333;
            text-decoration: none;
            border-radius: 7px;
            font-size: 14px;
            font-weight: bold;
        }

        .cancel-btn:hover {
            background: #bbb;
        }


        /* =============================================
           MOBILE
        ============================================= */

        @media (max-width: 850px) {

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


            .form-grid {

                grid-template-columns: 1fr;
            }

        }

    </style>

</head>


<body>


<?php include 'includes/sidebar.php'; ?>


<!-- =====================================================
     MAIN CONTENT
===================================================== -->

<div class="main-content">


    <!-- PAGE HEADER -->

    <div class="page-header">

        <div>

            <h1>🚗 Vehicle Management</h1>

            <p>
                Manage all vehicles received in the workshop.
            </p>

        </div>

    </div>


    <!-- SUCCESS MESSAGE -->

    <?php if ($message != ""): ?>

        <div class="message">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <!-- =================================================
         ADD VEHICLE
    ================================================== -->

    <div class="form-card">

        <?php if ($edit_mode): ?>
            <h2>✏️ Edit Vehicle</h2>
        <?php else: ?>
            <h2>➕ Add New Vehicle</h2>
        <?php endif; ?>


        <form method="POST">

            <?php if ($edit_mode): ?>

                <input
                    type="hidden"
                    name="vehicle_id"
                    value="<?php echo $edit_vehicle['vehicle_id']; ?>"
                >

            <?php endif; ?>


            <div class="form-grid">


                <!-- COMPANY -->

                <div class="form-group">

                    <label>Company</label>

                    <select
                        name="company_id"
                        required
                    >

                        <option value="">
                            Select Company
                        </option>


                        <?php if ($companies && $companies->num_rows > 0): ?>

                            <?php while ($company = $companies->fetch_assoc()): ?>

                                <option
                                    value="<?php echo $company['company_id']; ?>"
                                    <?php
                                    if ($edit_mode &&
                                        $edit_vehicle['company_id'] == $company['company_id']) {
                                        echo 'selected';
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

                        <?php endif; ?>

                    </select>

                </div>


                <!-- VEHICLE NUMBER -->

                <div class="form-group">

                    <label>Vehicle Number</label>

                    <input
                        type="text"
                        name="vehicle_number"
                        placeholder="Example: TN 01 AB 1234"
                        value="<?php echo $edit_mode ? htmlspecialchars($edit_vehicle['vehicle_number']) : ''; ?>"
                        required
                    >

                </div>


                <!-- VEHICLE TYPE -->

                <div class="form-group">

                    <label>Vehicle Type</label>

                    <select
                        name="vehicle_type"
                        required
                    >

                        <option value="">
                            Select Type
                        </option>

                        <option value="Bus" <?php echo ($edit_mode && $edit_vehicle['vehicle_type'] == 'Bus') ? 'selected' : ''; ?>>
                            Bus
                        </option>

                        <option value="Van" <?php echo ($edit_mode && $edit_vehicle['vehicle_type'] == 'Van') ? 'selected' : ''; ?>>
                            Van
                        </option>

                        <option value="Ambulance" <?php echo ($edit_mode && $edit_vehicle['vehicle_type'] == 'Ambulance') ? 'selected' : ''; ?>>
                            Ambulance
                        </option>

                        <option value="Tempo" <?php echo ($edit_mode && $edit_vehicle['vehicle_type'] == 'Tempo') ? 'selected' : ''; ?>>
                            Tempo
                        </option>

                        <option value="Other" <?php echo ($edit_mode && $edit_vehicle['vehicle_type'] == 'Other') ? 'selected' : ''; ?>>
                            Other
                        </option>

                    </select>

                </div>


                <!-- CHASSIS -->

                <div class="form-group">

                    <label>Chassis Number</label>

                    <input
                        type="text"
                        name="chassis_number"
                        placeholder="Enter chassis number"
                        value="<?php echo $edit_mode ? htmlspecialchars($edit_vehicle['chassis_number']) : ''; ?>"
                    >

                </div>


                <!-- DATE RECEIVED -->

                <div class="form-group">

                    <label>Date Received</label>

                    <input
                        type="date"
                        name="date_received"
                        value="<?php echo $edit_mode ? htmlspecialchars($edit_vehicle['date_received']) : ''; ?>"
                        required
                    >

                </div>


                <!-- DEADLINE -->

                <div class="form-group">

                    <label>Deadline</label>

                    <input
                        type="date"
                        name="deadline"
                        value="<?php echo $edit_mode ? htmlspecialchars($edit_vehicle['deadline']) : ''; ?>"
                        required
                    >

                </div>


                <!-- STATUS -->

                <div class="form-group">

                    <label>Status</label>

                    <select name="status">

                        <option value="Pending" <?php echo ($edit_mode && $edit_vehicle['status'] == 'Pending') ? 'selected' : ''; ?>>
                            Pending
                        </option>

                        <option value="In Progress" <?php echo ($edit_mode && $edit_vehicle['status'] == 'In Progress') ? 'selected' : ''; ?>>
                            In Progress
                        </option>

                        <option value="Completed" <?php echo ($edit_mode && $edit_vehicle['status'] == 'Completed') ? 'selected' : ''; ?>>
                            Completed
                        </option>

                    </select>

                </div>


            </div>


            <?php if ($edit_mode): ?>

                <button
                    type="submit"
                    name="update_vehicle"
                    class="submit-btn"
                >
                    Update Vehicle
                </button>

                <a
                    href="vehicles.php"
                    class="cancel-btn"
                >
                    Cancel
                </a>

            <?php else: ?>

                <button
                    type="submit"
                    name="add_vehicle"
                    class="submit-btn"
                >
                    Add Vehicle
                </button>

            <?php endif; ?>


        </form>

    </div>


    <!-- =================================================
         VEHICLE LIST
    ================================================== -->

    <div class="table-card">

        <h2>📋 Vehicle List</h2>


        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Vehicle Number</th>

                    <th>Company</th>

                    <th>Type</th>

                    <th>Date Received</th>

                    <th>Deadline</th>

                    <th>Status</th>
                    <th>Action</th>

                </tr>

            </thead>


            <tbody>


                <?php if ($vehicles && $vehicles->num_rows > 0): ?>


                    <?php while ($vehicle = $vehicles->fetch_assoc()): ?>


                        <tr>


                            <td>

                                <?php
                                echo $vehicle['vehicle_id'];
                                ?>

                            </td>


                            <td>

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $vehicle['vehicle_number']
                                    );
                                    ?>

                                </strong>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $vehicle['company_name']
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $vehicle['vehicle_type']
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $vehicle['date_received']
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $vehicle['deadline']
                                );
                                ?>

                            </td>


                            <td>


                                <?php

                                $status_class = "pending";


                                if ($vehicle['status'] == "In Progress") {

                                    $status_class = "progress";

                                } elseif ($vehicle['status'] == "Completed") {

                                    $status_class = "completed";

                                }

                                ?>


                                <span
                                    class="status <?php echo $status_class; ?>"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $vehicle['status']
                                    );
                                    ?>

                                </span>


                            </td>


                            <td>

                                <a
                                    href="vehicles.php?edit=<?php echo $vehicle['vehicle_id']; ?>"
                                    class="edit-btn"
                                >
                                    Edit
                                </a>

                                <a
                                    href="vehicles.php?delete=<?php echo $vehicle['vehicle_id']; ?>"
                                    class="delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this vehicle?');"
                                >
                                    Delete
                                </a>

                            </td>


                        </tr>


                    <?php endwhile; ?>


                <?php else: ?>


                    <tr>

                        <td
                            colspan="8"
                            style="text-align:center; padding:30px;"
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